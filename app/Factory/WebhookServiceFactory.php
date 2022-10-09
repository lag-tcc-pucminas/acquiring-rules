<?php

namespace App\Factory;

use App\Service\WebhookService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\TransferStats;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Hyperf\Guzzle\CoroutineHandler;

class WebhookServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class)->get('guzzle.acquirer-gateway');

        $logger = $container->get(LoggerFactory::class)->get('webhook');

        $client = make(Client::class, [
            'config' => array_merge($config, [
                'handler' => HandlerStack::create(new CoroutineHandler()),
                'on_stats' => function (TransferStats $stats) use ($logger) {
                    $logger->info('Webhook Request', [
                        'url' => $stats->getRequest()->getUri()->getPath(),
                        'method' => $stats->getRequest()->getMethod(),
                        'headers' => $stats->getRequest()->getHeaders(),
                        'body' => json_decode($stats->getRequest()->getBody(), true),
                    ]);

                    if ($stats->getResponse()) {
                        $logger->info('Webhook Response', [
                            'status' => $stats->getResponse()->getStatusCode(),
                            'headers' => $stats->getResponse()->getHeaders(),
                            'body' => json_decode($stats->getResponse()->getBody(), true)
                        ]);
                    }
                }
            ]),
        ]);

        $logger = $container->get(LoggerFactory::class)->get('webhook');

        return new WebhookService($client, $logger);
    }
}
