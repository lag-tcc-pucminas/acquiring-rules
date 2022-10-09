<?php

namespace App\Controller\Documentation;

use OpenApi\Annotations as OA;

interface HealthControllerDocumentation
{
    /**
     * @OA\Get(
     *      path="/health",
     *      operationId="health",
     *      summary="Health Check",
     *      description="Health Check",
     *      tags={"Health Check"},
     *      @OA\Response(
     *          response=200,
     *          description="Application Is Alive"
     *       )
     *     )
     */
    public function index();
}
