<?php

namespace App\Repository;

use App\Job\SendWebhookNotificationJob;
use App\Model\PaymentScenario;
use App\Model\PaymentScenarioAcquirer;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Query\Builder as QueryBuilder;
use Hyperf\DbConnection\Db;

class PaymentScenarioRepository
{
    private PaymentScenario $model;
    private DriverInterface $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->model = new PaymentScenario();
        $this->driver = $driverFactory->get('default');
    }

    public function getById(int $id): ?PaymentScenario
    {
        return $this->model->find($id);
    }

    /**
     * @param PaymentScenario $scenario
     * @param PaymentScenarioAcquirer[] $acquirers
     * @return int
     */
    public function create(PaymentScenario $scenario, array $acquirers): int
    {
        Db::transaction(function () use ($scenario, $acquirers) {
            $scenario->save();
            $scenario->acquirers()->saveMany($acquirers);
        });

        $this->enqueueJob($scenario);

        return $scenario->id;
    }

    /**
     * @param PaymentScenario $scenario
     * @param PaymentScenarioAcquirer[] $acquirers
     * @return PaymentScenario
     */
    public function update(PaymentScenario $scenario, array $acquirers): PaymentScenario
    {
        return Db::transaction(function () use ($scenario, $acquirers) {
            $scenario->save();

            foreach ($acquirers as $acquirer) {
                $scenario->acquirers()
                    ->updateOrCreate(
                        ['acquirer_id' => $acquirer->acquirer_id],
                        ['priority' => $acquirer->priority]
                    );
            }

            $mappedAcquirers = array_column($acquirers, 'acquirer_id');
            $scenario->acquirers()
                ->whereNotIn('acquirer_id', $mappedAcquirers)
                ->delete();

            $this->enqueueJob($scenario);

            return $scenario;
        });
    }

    public function delete(PaymentScenario $scenario): void
    {
        $scenario->delete();
        $this->enqueueJob($scenario);
    }

    public function getExtensiveScenario(PaymentScenario $scenario): ?PaymentScenario
    {
        $query = $this->model->newQuery()
            ->where('brand', $scenario->brand)
            ->where('installment_interval_start', '<=', $scenario->installment_interval_start)
            ->where('installment_interval_end', '>=', $scenario->installment_interval_end);

        if ($scenario->exists) {
            $query->where('id', '!=', $scenario->id);
        }

        return $query->first();
    }

    public function getConflictScenarios(PaymentScenario $scenario): Collection
    {
        $query = $this->model->newQuery()
            ->where('brand', $scenario->brand)
            ->whereNested(function (QueryBuilder $builder) use ($scenario) {
                $builder
                    ->where('installment_interval_start', '<=', $scenario->installment_interval_start)
                    ->where('installment_interval_end', '>=', $scenario->installment_interval_start)
                    ->orWhere('installment_interval_start', '<=', $scenario->installment_interval_end)
                    ->where('installment_interval_end', '>=', $scenario->installment_interval_end);
            });

        if ($scenario->exists) {
            $query->where('id', '!=', $scenario->id);
        }

        return $query->get();
    }

    public function searchAndPaginate(array $filters, int $perPage, int $page): LengthAwarePaginatorInterface
    {
        $query = $this->model->newQuery();

        if (!empty($filters['brand'])) {
            $query->where('brand', $filters['brand']);
        }

        if (!empty($filters['installment_start'])) {
            $query->where('installment_interval_start', '>=', $filters['installment_start']);
        }

        if (!empty($filters['installment_end'])) {
            $query->where('installment_interval_end', '<=', $filters['installment_end']);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findScenarioByBrandAndInstallment(string $brand, int $installment): ?PaymentScenario
    {
        return $this->model->newQuery()
            ->where('brand', $brand)
            ->whereNested(function (QueryBuilder $builder) use ($installment) {
                $builder->where('installment_interval_start', '<=', $installment);
                $builder->where('installment_interval_end', '>=', $installment);
            })->first();
    }

    private function enqueueJob(PaymentScenario $scenario): void
    {
        $this->driver->push(new SendWebhookNotificationJob($scenario->toArray()));
    }
}
