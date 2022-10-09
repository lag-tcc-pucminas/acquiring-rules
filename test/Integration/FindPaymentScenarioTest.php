<?php

namespace Test\Integration;

use App\Enum\BrandEnum;
use App\Model\PaymentScenario;
use App\Model\PaymentScenarioAcquirer;
use Hyperf\Testing\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Test\Traits\RefreshDatabase;

class FindPaymentScenarioTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->client = make(Client::class);
    }

    public function testGetPaymentScenario(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::HIPERCARD,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->save($acquirer = new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', sprintf('/scenarios/%d', $scenario->id));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals([
            'id' => $scenario->id,
            'brand' => $scenario->brand,
            'installments' => [
                'start' => $scenario->installment_interval_start,
                'end' => $scenario->installment_interval_end,
            ],
            'acquirers' => [
                [
                    'acquirer' => [
                        'id' => $acquirer->acquirer->id,
                        'name' => $acquirer->acquirer->name
                    ],
                    'priority' => $acquirer->priority
                ]
            ]
        ], json_decode($response->getBody(), true));
    }

    public function testShouldReturn404WhenScenarioNotFound(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', '/scenarios/99');

        $this->assertEquals(404, $response->getStatusCode());
    }
}