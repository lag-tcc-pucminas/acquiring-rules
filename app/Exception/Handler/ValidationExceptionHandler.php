<?php

namespace App\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Hyperf\Utils\MessageBag;

class ValidationExceptionHandler extends ExceptionHandler
{
    /**
     * @param ValidationException $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        /** @var MessageBag $errors */
        $errors = $throwable->validator->errors();

        if (!$response->hasHeader('content-type')) {
            $response = $response->withAddedHeader('content-type', 'application/json; charset=utf-8');
        }

        return $response->withStatus($throwable->status)->withBody(new SwooleStream($errors->toJson()));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
