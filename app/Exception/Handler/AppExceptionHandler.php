<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500)
            ->withBody(new SwooleStream(json_encode($this->buildResponseBodyFromException($throwable))));
    }

    private function buildResponseBodyFromException(Throwable $throwable): array
    {
        $body = [
            'message' => 'Server Error',
        ];

        if (env('APP_ENV', 'prod') != 'prod') {
            $body['exception'] = [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
            ];
        }

        return $body;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
