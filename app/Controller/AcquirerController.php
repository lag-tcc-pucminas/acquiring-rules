<?php

namespace App\Controller;

use App\Controller\Documentation\AcquirerControllerDocumentation;
use App\Http\Resource\AcquirerResource;
use App\Repository\AcquirerRepository;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface as Request;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;

class AcquirerController implements AcquirerControllerDocumentation
{
    public function __construct(
        private AcquirerRepository $acquirerRepository
    ) {
    }

    public function getAll(Request $request, Response $response): ResponseInterface
    {
        $acquirers = $this->acquirerRepository->getAll();
        return $response->json(AcquirerResource::collection($acquirers))->withStatus(200);
    }
}
