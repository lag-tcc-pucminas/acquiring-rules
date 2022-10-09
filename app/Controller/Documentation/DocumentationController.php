<?php

namespace App\Controller\Documentation;

use Hyperf\ViewEngine\View;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use OpenApi\Annotations as OA;

use function Hyperf\ViewEngine\view;

/**
 * @OA\OpenApi(
 *      @OA\Info(
 *          version="1.0.0",
 *          title="Acquiring Rules API",
 *          description="Acquiring Rules OpenAPI Documentation"
 *      )
 * )
 *
 * @codeCoverageIgnore
 */
class DocumentationController
{
    public function index(Response $response): ResponseInterface
    {
        return $response->redirect('/docs');
    }

    public function docs(): View
    {
        return view('documentation-swagger');
    }
}
