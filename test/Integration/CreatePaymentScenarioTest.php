<?php

namespace Test\Integration;

use App\Enum\BrandEnum;
use App\Model\Acquirer;
use App\Model\PaymentScenario;
use App\Model\PaymentScenarioAcquirer;
use Hyperf\Testing\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Test\Traits\RefreshDatabase;

class CreatePaymentScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshDatabase();
        $this->client = make(Client::class);
    }

    /**
     * @test
     */
    public function createPaymentScenario(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('POST', '/scenarios', [
            'json' => [
                'brand' => BrandEnum::VISA,
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

        $this->assertEquals(201, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);

        $this->assertIsArray($responseBody);
        $this->assertEquals(BrandEnum::VISA, $responseBody['brand']);

        $this->assertArrayHasKey('installments', $responseBody);
        $this->assertEquals(['start' => 2, 'end' => 3], $responseBody['installments']);

        $this->assertArrayHasKey('acquirers', $responseBody);
        $this->assertEquals([
            ['acquirer' => ['id' => 1, 'name' => Acquirer::GREEN], 'priority' => 1]
        ], $responseBody['acquirers']);
    }

    /**
     * @test
     */
    public function shouldReturnConflictWhenExistsExtensiveScenario(): void
    {
        $extensiveScenario = PaymentScenario::create([
            'brand' => BrandEnum::MASTERCARD,
            'installment_interval_start' => 2,
            'installment_interval_end' => 6,
        ]);

        $extensiveScenario->acquirers()->save($extensiveScenarioAcquirer = new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('POST', '/scenarios', [
            'json' => [
                'brand' => BrandEnum::MASTERCARD,
                'installment_interval' => [
                    'start' => 4,
                    'end' => 5
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

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals([
            'id' => $extensiveScenario->id,
            'brand' => $extensiveScenario->brand,
            'installments' => [
                'start' => $extensiveScenario->installment_interval_start,
                'end' => $extensiveScenario->installment_interval_end,
            ],
            'acquirers' => [
                [
                    'acquirer' => [
                        'id' => $extensiveScenarioAcquirer->acquirer->id,
                        'name' => $extensiveScenarioAcquirer->acquirer->name
                    ],
                    'priority' => $extensiveScenarioAcquirer->priority
                ]
            ]
        ], json_decode($response->getBody(), true));
    }

    /**
     * @test
     */
    public function shouldReturnConflictWhenExistsConflictScenario(): void
    {
        $conflictScenario = PaymentScenario::create([
            'brand' => BrandEnum::AMEX,
            'installment_interval_start' => 11,
            'installment_interval_end' => 12,
        ]);

        $conflictScenario->acquirers()->save($conflictScenarioAcquirer = new PaymentScenarioAcquirer([
            'acquirer_id' => 1,
            'priority' => 1
        ]));

        /** @var ResponseInterface $response */
        $response = $this->client->request('POST', '/scenarios', [
            'json' => [
                'brand' => BrandEnum::AMEX,
                'installment_interval' => [
                    'start' => 1,
                    'end' => 11
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

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals([
            'id' => $conflictScenario->id,
            'brand' => $conflictScenario->brand,
            'installments' => [
                'start' => $conflictScenario->installment_interval_start,
                'end' => $conflictScenario->installment_interval_end,
            ],
            'acquirers' => [
                [
                    'acquirer' => [
                        'id' => $conflictScenarioAcquirer->acquirer->id,
                        'name' => $conflictScenarioAcquirer->acquirer->name
                    ],
                    'priority' => $conflictScenarioAcquirer->priority
                ]
            ]
        ], json_decode($response->getBody(), true));
    }

    public function testShouldReturn422WhenBrandIsInvalid(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('POST', '/scenarios', [
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
        $response = $this->client->request('POST', '/scenarios', [
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
        $response = $this->client->request('POST', '/scenarios', [
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