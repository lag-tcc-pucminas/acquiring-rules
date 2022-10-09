<?php

namespace Test\Integration;

use Hyperf\Testing\Client;
use PHPUnit\Framework\TestCase;
use Test\Traits\RefreshDatabase;

class GetAcquirersTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->client = make(Client::class);
    }

    public function testGetAcquirers(): void
    {
        $response = $this->client->get('/acquirers');
        $this->assertIsArray($response);

        $collection = collect($response);

        $this->assertTrue($collection->every(function (array $acquirer) {
            $this->assertArrayHasKey('id', $acquirer);
            $this->assertIsInt($acquirer['id']);

            $this->assertArrayHasKey('name', $acquirer);
            $this->assertIsString($acquirer['name']);

            $this->assertArrayHasKey('default', $acquirer);
            $this->assertIsBool($acquirer['default']);

            return true;
        }));
    }


}