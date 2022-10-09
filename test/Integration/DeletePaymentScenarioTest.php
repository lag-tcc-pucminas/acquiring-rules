<?php

namespace Test\Integration;

use App\Enum\BrandEnum;
use App\Model\PaymentScenario;
use Hyperf\Testing\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Test\Traits\RefreshDatabase;

class DeletePaymentScenarioTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->client = make(Client::class);
    }

    public function testDeletePaymentScenario(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::ELO,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        /** @var ResponseInterface $response */
        $response = $this->client->request('DELETE', sprintf('/scenarios/%d', $scenario->id));
        $this->assertEquals(204, $response->getStatusCode());

        $scenario->refresh();
        $this->assertTrue($scenario->trashed());
    }

    public function testShouldReturn404WhenScenarioNotFound(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('DELETE', '/scenarios/99');

        $this->assertEquals(404, $response->getStatusCode());
    }
}