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

class UpdatePaymentScenarioTest extends TestCase
{
    use RefreshDatabase;

    private HttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->client = make(HttpClient::class);
    }

    public function testUpdateBrandScenario(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::MASTERCARD,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->save(new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', sprintf('/scenarios/%d', $scenario->id), [
            'json' => $this->buildRequestBody($scenario, ['brand' => BrandEnum::VISA]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);
        $this->assertEquals(BrandEnum::VISA, $responseBody['brand']);

        $scenario->refresh();
        $this->assertEquals(BrandEnum::VISA, $scenario->brand);
    }

    public function testUpdateInstallmentInterval(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::VISA,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->save(new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', sprintf('/scenarios/%d', $scenario->id), [
            'json' => $this->buildRequestBody($scenario, ['installment_interval' => ['start' => 1, 'end' => 12]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);

        $this->assertEquals(1, $responseBody['installments']['start']);
        $this->assertEquals(12, $responseBody['installments']['end']);

        $scenario->refresh();

        $this->assertEquals(1, $scenario->installment_interval_start);
        $this->assertEquals(12, $scenario->installment_interval_end);
    }

    public function testAddAcquirerIntoScenario(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::ELO,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->save(new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', sprintf('/scenarios/%d', $scenario->id), [
            'json' => $this->buildRequestBody($scenario, ['acquirers' => [
                ['id' => 1, 'priority' => 1],
                ['id' => 2, 'priority' => 2]
            ]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);

        $this->assertEquals([
            [
                'acquirer' => [
                    'id' => 1,
                    'name' => Acquirer::GREEN
                ],
                'priority' => 1
            ],
            [
                'acquirer' => [
                    'id' => 2,
                    'name' => Acquirer::RED
                ],
                'priority' => 2
            ]
        ], $responseBody['acquirers']);

        $scenario->refresh();

        $this->assertCount(2, $scenario->acquirers);

        /** @var PaymentScenarioAcquirer $firstAcquirerScenario */
        $firstAcquirerScenario = $scenario->acquirers->offsetGet(0);

        $this->assertEquals(1, $firstAcquirerScenario->acquirer_id);
        $this->assertEquals(1, $firstAcquirerScenario->priority);

        /** @var PaymentScenarioAcquirer $secondAcquirerScenario */
        $secondAcquirerScenario = $scenario->acquirers->offsetGet(1);

        $this->assertEquals(2, $secondAcquirerScenario->acquirer_id);
        $this->assertEquals(2, $secondAcquirerScenario->priority);
    }

    public function testRemoveAcquirerFromScenario(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::AMEX,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->saveMany([
            new PaymentScenarioAcquirer(['acquirer_id' => 1, 'priority' => 1]),
            new PaymentScenarioAcquirer(['acquirer_id' => 2, 'priority' => 2])
        ]);

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', sprintf('/scenarios/%d', $scenario->id), [
            'json' => $this->buildRequestBody($scenario, ['acquirers' => [['id' => 2, 'priority' => 2]]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);
        $this->assertEquals([
            [
                'acquirer' => [
                    'id' => 2,
                    'name' => Acquirer::RED
                ],
                'priority' => 2
            ]
        ], $responseBody['acquirers']);

        $scenario->refresh();

        $this->assertCount(1, $scenario->acquirers);

        /** @var PaymentScenarioAcquirer $scenarioAcquirer */
        $scenarioAcquirer = $scenario->acquirers->first();

        $this->assertEquals(2, $scenarioAcquirer->acquirer_id);
        $this->assertEquals(2, $scenarioAcquirer->priority);
    }

    public function testChangeAcquirerPriority(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::HIPERCARD,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->saveMany([
            new PaymentScenarioAcquirer(['acquirer_id' => 1, 'priority' => 1]),
            new PaymentScenarioAcquirer(['acquirer_id' => 2, 'priority' => 2])
        ]);

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', sprintf('/scenarios/%d', $scenario->id), [
            'json' => $this->buildRequestBody($scenario, ['acquirers' => [
                ['id' => 1, 'priority' => 2],
                ['id' => 2, 'priority' => 1]
            ]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);
        $this->assertEquals([
            [
                'acquirer' => [
                    'id' => 1,
                    'name' => Acquirer::GREEN
                ],
                'priority' => 2
            ],
            [
                'acquirer' => [
                    'id' => 2,
                    'name' => Acquirer::RED
                ],
                'priority' => 1
            ]
        ], $responseBody['acquirers']);

        $scenario->refresh();

        /** @var PaymentScenarioAcquirer $firstAcquirerScenario */
        $firstAcquirerScenario = $scenario->acquirers->offsetGet(0);

        $this->assertEquals(1, $firstAcquirerScenario->acquirer_id);
        $this->assertEquals(2, $firstAcquirerScenario->priority);

        /** @var PaymentScenarioAcquirer $secondAcquirerScenario */
        $secondAcquirerScenario = $scenario->acquirers->offsetGet(1);

        $this->assertEquals(2, $secondAcquirerScenario->acquirer_id);
        $this->assertEquals(1, $secondAcquirerScenario->priority);
    }

    public function testShouldReturn409WhenExistsConflictScenario(): void
    {
        $conflictScenario = PaymentScenario::create([
            'brand' => BrandEnum::MASTERCARD,
            'installment_interval_start' => 7,
            'installment_interval_end' => 12,
        ]);

        $conflictScenario->acquirers()->save(new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::MASTERCARD,
            'installment_interval_start' => 1,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->save(new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', sprintf('/scenarios/%d', $scenario->id), [
            'json' => $this->buildRequestBody($scenario, ['installment_interval' => [
                'start' => 1,
                'end' => 8
            ]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(409, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);

        $this->assertEquals($conflictScenario->id, $responseBody['id']);
    }

    public function testShouldReturn404WhenScenarioNotFound(): void
    {
        $scenario = PaymentScenario::create([
            'brand' => BrandEnum::MASTERCARD,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $scenario->acquirers()->save(new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', '/scenarios/99', [
            'json' => $this->buildRequestBody($scenario),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    private function buildRequestBody(PaymentScenario $scenario, array $overwrite = []): array
    {
        return array_merge([
            'brand' => $scenario->brand,
            'installment_interval' => [
                'start' => $scenario->installment_interval_start,
                'end' => $scenario->installment_interval_end
            ],
            'acquirers' => $scenario->acquirers->map(fn(PaymentScenarioAcquirer $acquirer) => [
                'id' => $acquirer->acquirer_id,
                'priority' => $acquirer->priority,
            ]
            )->toArray()
        ], $overwrite);
    }

    public function testShouldReturn422WhenBrandIsInvalid(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', '/scenarios/1', [
            'json' => [
                'brand' => 'master',
                'installment_interval' => [
                    'start' => 2,
                    'end' => 3
                ],
                'acquirers' => [
                    [
                        'id' => 1,
                        'priority' => 1
                    ]
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertEquals([
            'brand' => [
                'The selected brand is invalid.'
            ]
        ], json_decode($response->getBody(), true));
    }

    public function testShouldReturn422WhenPrioritizationIsDuplicated(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', '/scenarios/1', [
            'json' => [
                'brand' => BrandEnum::ELO,
                'installment_interval' => [
                    'start' => 2,
                    'end' => 3
                ],
                'acquirers' => [
                    [
                        'id' => 1,
                        'priority' => 1
                    ],
                    [
                        'id' => 2,
                        'priority' => 1
                    ]
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertEquals([
            "acquirers.0.priority" => [
                0 => "The acquirers.0.priority field has a duplicate value."
            ],
            "acquirers.1.priority" => [
                0 => "The acquirers.1.priority field has a duplicate value."
            ]
        ], json_decode($response->getBody(), true));
    }

    public function testShouldReturn422WhenAcquirerIsDuplicated(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('PUT', '/scenarios/1', [
            'json' => [
                'brand' => BrandEnum::ELO,
                'installment_interval' => [
                    'start' => 2,
                    'end' => 3
                ],
                'acquirers' => [
                    [
                        'id' => 1,
                        'priority' => 1
                    ],
                    [
                        'id' => 1,
                        'priority' => 2
                    ]
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertEquals([
            "acquirers.0.id" => [
                0 => "The acquirers.0.id field has a duplicate value."
            ],
            "acquirers.1.id" => [
                0 => "The acquirers.1.id field has a duplicate value."
            ]
        ], json_decode($response->getBody(), true));
    }
}
