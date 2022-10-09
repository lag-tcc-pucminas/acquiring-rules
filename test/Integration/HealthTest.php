<?php

namespace Test\Integration;

use Hyperf\Testing\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HealthTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = make(Client::class);
    }

    public function testGetHealth(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', '/health');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'message' => 'I\'m alive'
        ], json_decode($response->getBody(), true));
    }
}