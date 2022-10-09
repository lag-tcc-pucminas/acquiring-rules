<?php

namespace App\Service;

use App\Exception\ConflictScenarioException;
use App\Http\Request\CreateScenarioRequest;
use App\Http\Request\UpdateScenarioRequest;
use App\Model\PaymentScenario;
use App\Model\PaymentScenarioAcquirer;
use App\Repository\AcquirerRepository;
use App\Repository\PaymentScenarioRepository;
use Hyperf\Database\Model\Collection;

class PaymentScenarioService
{
    public function __construct(
        private AcquirerRepository $acquirerRepository,
        private PaymentScenarioRepository $repository
    ) {
    }

    /**
     * @throws ConflictScenarioException
     */
    public function create(CreateScenarioRequest $request): PaymentScenario
    {
        $scenario = new PaymentScenario([
            'brand' => $request->input('brand'),
            'installment_interval_start' => $request->input('installment_interval.start'),
            'installment_interval_end' => $request->input('installment_interval.end')
        ]);

        $acquirers = $this->buildScenarioAcquirers($request->input('acquirers'));

        $this->validateInstallmentInterval($scenario);

        $this->repository->create($scenario, $acquirers);

        return $scenario;
    }

    /**
     * @param array $acquirers
     * @return PaymentScenarioAcquirer[]
     */
    private function buildScenarioAcquirers(array $acquirers): array
    {
        return array_map(
            function (array $acquirer) {
                return new PaymentScenarioAcquirer([
                    'acquirer_id' => $acquirer['id'],
                    'priority' => $acquirer['priority']
                ]);
            },
            $acquirers
        );
    }

    /**
     * @throws ConflictScenarioException
     */
    public function update(UpdateScenarioRequest $request): ?PaymentScenario
    {
        $scenario = $this->repository->getById((int) $request->route('id'));

        if (!$scenario) {
            return null;
        }

        $scenario->brand = $request->input('brand');
        $scenario->installment_interval_start = $request->input('installment_interval.start');
        $scenario->installment_interval_end = $request->input('installment_interval.end');

        $acquirers = $this->buildScenarioAcquirers($request->input('acquirers'));

        $this->validateInstallmentInterval($scenario);



        return $this->repository->update($scenario, $acquirers);
    }

    /**
     * @throws ConflictScenarioException
     */
    private function validateInstallmentInterval(PaymentScenario $scenario): void
    {
        $extensiveScenario = $this->repository->getExtensiveScenario($scenario);

        if ($extensiveScenario !== null) {
            throw ConflictScenarioException::make($extensiveScenario);
        }

        $conflictScenarios = $this->repository->getConflictScenarios($scenario);

        if ($conflictScenarios->count() > 0) {
            throw ConflictScenarioException::make($conflictScenarios->first());
        }
    }

    public function getAcquirerPrioritization(array $filters): Collection
    {
        $paymentScenario = $this->repository->findScenarioByAttributes($filters);
        if ($paymentScenario) {
            return $paymentScenario->acquirers()->orderBy('priority')->get();
        }

        $default = $this->acquirerRepository->getDefault();
        return new Collection([PaymentScenarioAcquirer::buildFromDefault($default)]);
    }
}
