<?php

namespace Test\Integration;

use App\Enum\BrandEnum;
use App\Model\Acquirer;
use App\Model\PaymentScenario;
use App\Model\PaymentScenarioAcquirer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Test\HttpClient;
use Test\Traits\RefreshDatabase;

class GetAcquirerPrioritizationTest extends TestCase
{
    use RefreshDatabase;

    private HttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->client = make(HttpClient::class);
    }

    public function testGetAcquirerPrioritization(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::MASTERCARD,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->saveMany([
            new PaymentScenarioAcquirer(['acquirer_id' => 1, 'priority' => 2]),
            new PaymentScenarioAcquirer(['acquirer_id' => 2, 'priority' => 1]),
            new PaymentScenarioAcquirer(['acquirer_id' => 3, 'priority' => 3]),
        ]);


        $filters = [
            'brand' => BrandEnum::MASTERCARD,
            'installment' => 3
        ];

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', sprintf('/acquirer-prioritization?%s', http_build_query($filters)));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals([
            [
                'acquirer' => [
                    'id' => 2,
                    'name' => Acquirer::RED
                ],
                'priority' => 1
            ],
            [
                'acquirer' => [
                    'id' => 1,
                    'name' => Acquirer::GREEN
                ],
                'priority' => 2
            ],
            [
                'acquirer' => [
                    'id' => 3,
                    'name' => Acquirer::BLUE
                ],
                'priority' => 3
            ]
        ], json_decode($response->getBody(), true));
    }

    public function testShouldReturnDefaultAcquirerWhenScenarioNotExists(): void
    {
        $filters = [
            'brand' => BrandEnum::MASTERCARD,
            'installment' => 3
        ];

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', sprintf('/acquirer-prioritization?%s', http_build_query($filters)));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals([
            [
                'acquirer' => [
                    'id' => 1,
                    'name' => Acquirer::GREEN
                ],
                'priority' => 1
            ]
        ], json_decode($response->getBody(), true));
    }
}