<?php

namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Throwable;

class WebhookService
{
    public function __construct(private Client $client, private LoggerInterface $logger)
    {
    }

    public function notifyChangesForBrand(string $brand): void
    {
        $this->logger->info('Send webhook notification', ['brand' => $brand]);

        try {
            $this->client->request('DELETE', sprintf('/acquirer-prioritization/%s', $brand));
            $this->logger->info('Webhook notification was sent successfully');
        } catch (Throwable $exception) {
            $this->logger->info('An error occurred when trying to send the webhook notification.', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'class' => get_class($exception)
            ]);

            throw $exception;
        }
    }
}
