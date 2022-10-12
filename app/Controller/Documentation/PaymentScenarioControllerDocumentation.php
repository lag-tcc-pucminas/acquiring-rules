<?php

namespace App\Controller\Documentation;

use App\Http\Request\CreateScenarioRequest;
use App\Http\Request\UpdateScenarioRequest;
use Hyperf\HttpServer\Contract\RequestInterface as Request;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;

interface PaymentScenarioControllerDocumentation
{
    /**
     * @OA\Get(
     *      path="/scenarios/{id}",
     *      operationId="getScenarioById",
     *      summary="Get Scenario By Id",
     *      description="Get Scenario By Id",
     *      tags={"Payment Scenario"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Found Scenario Data",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentScenarioResource")
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Can't find any scenario with the given id."
     *      )
     *   )
     */
    public function getById(Request $request, Response $response): ResponseInterface;

    /**
     * @OA\Delete (
     *      path="/scenarios/{id}",
     *      operationId="deleteScenario",
     *      summary="Delete Scenario",
     *      description="Delete Scenario",
     *      tags={"Payment Scenario"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Deleted with success."
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Can't find any scenario with the given id."
     *      )
     *   )
     */
    public function delete(Request $request, Response $response): ResponseInterface;

    /**
     * @OA\Get  (
     *      path="/acquirer-prioritization",
     *      operationId="Get Acquirer Prioritization",
     *      summary="Get Acquirer Prioritization",
     *      description="Get Acquirer Prioritization By Parameters",
     *      tags={"Acquirer Prioritization"},
     *      @OA\Parameter(
     *         name="installment",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=7
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="mastercard"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Acquirer Prioritization Data",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentScenarioAcquirerResource")
     *          )
     *      )
     *   )
     */
    public function getAcquirerPrioritization(Request $request, Response $response): ResponseInterface;

    /**
     * @OA\Get  (
     *      path="/scenarios",
     *      operationId="Search Scenarios",
     *      summary="Search Scenarios",
     *      description="Search Scenarios By Parameters",
     *      tags={"Payment Scenario"},
     *      @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="mastercard"
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="installment_start",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="installment_end",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example="12"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Search Result",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentScenarioPaginatorResource")
     *      )
     *   )
     */
    public function search(Request $request, Response $response): ResponseInterface;

    /**
     * @OA\Post(
     *     path="/scenarios",
     *     operationId="Create Payment Scenario",
     *     summary="Create Payment Scenario",
     *     description="Create Payment Scenario",
     *     tags={"Payment Scenario"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CreateScenarioRequest")
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Created Scenario Data",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentScenarioResource")
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Invalid Request"
     *     ),
     *     @OA\Response(
     *          response=409,
     *          description="Cannot create the scenario, because it conflicts with an existing one.",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentScenarioResource")
     *     )
     * )
     */
    public function store(CreateScenarioRequest $request, Response $response): ResponseInterface;

    /**
     * @OA\Put(
     *     path="/scenarios/{id}",
     *     operationId="Update Payment Scenario",
     *     summary="Update Payment Scenario",
     *     description="Update Payment Scenario",
     *     tags={"Payment Scenario"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateScenarioRequest")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Updated Scenario Data",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentScenarioResource")
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Invalid Request"
     *     ),
     *     @OA\Response(
     *          response=409,
     *          description="Cannot update the scenario, because it conflicts another else.",
     *          @OA\JsonContent(ref="#/components/schemas/PaymentScenarioResource")
     *     )
     * )
     */
    public function update(UpdateScenarioRequest $request, Response $response): ResponseInterface;
}
