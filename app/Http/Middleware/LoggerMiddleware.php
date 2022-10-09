<?php

namespace App\Http\Middleware;

use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class LoggerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerFactory $factory)
    {
        $this->logger = $factory->get('http');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestAsArray = [
            'url' => $request->getUri()->getPath(),
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'body' => json_decode($request->getBody(), true),
        ];

        $this->logger->info('Request data', $requestAsArray);

        $response = $handler->handle($request);

        $responseAsArray = [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => json_decode($response->getBody(), true)
        ];

        $this->logger->info('Response data', $responseAsArray);

        $this->logger->info('Request and Response', [
            'request' => $requestAsArray,
            'response' => $responseAsArray,
        ]);

        return $response;
    }
}
