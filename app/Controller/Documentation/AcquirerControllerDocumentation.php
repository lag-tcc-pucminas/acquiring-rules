<?php

namespace App\Controller\Documentation;

use Hyperf\HttpServer\Contract\RequestInterface as Request;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use OpenApi\Annotations as OA;

interface AcquirerControllerDocumentation
{
    /**
     * @OA\Get(
     *      path="/acquirers",
     *      operationId="listAcquirers",
     *      summary="List Acquirers",
     *      description="List All Acquirers",
     *      tags={"List Acquirers"},
     *      @OA\Response(
     *          response=200,
     *          description="Acquirer List",
     *          @OA\JsonContent(
     *              type="array",
     *              example={
     *                  {"id":1,"name":"green","default":true},
     *                  {"id":2,"name":"red","default":false},
     *                  {"id":3,"name":"blue","default":false}
     *              },
     *              @OA\Items(ref="#/components/schemas/AcquirerResource")
     *          )
     *       )
     *   )
     */
    public function getAll(Request $request, Response $response): ResponseInterface;
}
