<?php

namespace App\Controller;

use App\Controller\Documentation\PaymentScenarioControllerDocumentation;
use App\Exception\ConflictScenarioException;
use App\Http\Request\UpdateScenarioRequest;
use App\Http\Resource\PaymentScenarioAcquirerResource;
use App\Http\Resource\PaymentScenarioPaginatorResource;
use App\Repository\PaymentScenarioRepository;
use App\Service\PaymentScenarioService;
use Psr\Http\Message\ResponseInterface;
use App\Http\Request\CreateScenarioRequest;
use App\Http\Resource\PaymentScenarioResource;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Hyperf\HttpServer\Contract\RequestInterface as Request;

class PaymentScenarioController implements PaymentScenarioControllerDocumentation
{
    public function __construct(
        private PaymentScenarioRepository $repository,
        private PaymentScenarioService $service
    ) {
    }

    /**
     * @throws ConflictScenarioException
     */
    public function store(CreateScenarioRequest $request, Response $response): ResponseInterface
    {
        $scenario = $this->service->create($request);
        return $response->json(new PaymentScenarioResource($scenario))->withStatus(201);
    }

    public function getById(Request $request, Response $response): ResponseInterface
    {
        $scenario = $this->repository->getById((int) $request->route('id'));

        if (!$scenario) {
            return $response->json([])->withStatus(404);
        }

        return $response->json(new PaymentScenarioResource($scenario))->withStatus(200);
    }

    public function search(Request $request, Response $response): ResponseInterface
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);

        $paginator = $this->repository->searchAndPaginate($request->getQueryParams(), $perPage, $page);

        return $response->json(PaymentScenarioPaginatorResource::make($paginator))->withStatus(200);
    }

    public function update(UpdateScenarioRequest $request, Response $response): ResponseInterface
    {
        $scenario = $this->service->update($request);

        if (!$scenario) {
            return $response->json([])->withStatus(404);
        }

        return $response->json(new PaymentScenarioResource($scenario))->withStatus(200);
    }

    public function delete(Request $request, Response $response): ResponseInterface
    {
        $scenario = $this->repository->getById((int) $request->route('id'));

        if (!$scenario) {
            return $response->json([])->withStatus(404);
        }

        $this->repository->delete($scenario);

        return $response->json([])->withStatus(204);
    }

    public function getAcquirerPrioritization(Request $request, Response $response): ResponseInterface
    {
        if (!$request->has(['brand', 'installment'])) {
            return $response->json(['message' => 'The brand and installment params are required.'])->withStatus(400);
        }

        list('brand' => $brand, 'installment' => $installment) = $request->getQueryParams();

        $acquirers = $this->service->getAcquirerPrioritization($brand, $installment);

        return $response->json(PaymentScenarioAcquirerResource::collection($acquirers))->withStatus(200);
    }
}
