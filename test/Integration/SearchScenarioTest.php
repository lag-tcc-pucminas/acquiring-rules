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

class SearchScenarioTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    public function testSearchScenario(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', '/scenarios');

        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('total_count', $responseBody);
        $this->assertEquals(4, $responseBody['total_count']);

        $this->assertArrayHasKey('current_page', $responseBody);
        $this->assertEquals(1, $responseBody['current_page']);

        $this->assertArrayHasKey('page_size', $responseBody);
        $this->assertEquals(10, $responseBody['page_size']);

        $this->assertArrayHasKey('total_pages', $responseBody);
        $this->assertEquals(1, $responseBody['total_pages']);

        $this->assertArrayHasKey('items', $responseBody);
        $this->assertIsArray($responseBody['items']);

        $firstItem = current($responseBody['items']);
        $this->assertEquals([
            'id' => 1,
            'brand' => BrandEnum::VISA,
            'installments' => [
                'start' => 1,
                'end' => 6
            ],
            'acquirers' => [
                [
                    'acquirer' => [
                        'name' => Acquirer::GREEN,
                        'id' => 1
                    ],
                    'priority' => 1
                ]
            ]
        ], $firstItem);
    }

    public function testPageSize(): void
    {
        $queryParams = ['per_page' => 1];

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', sprintf('/scenarios?%s', http_build_query($queryParams)));
        $responseBody = json_decode($response->getBody(), true);

        $this->assertEquals(4, $responseBody['total_count']);
        $this->assertEquals(1, $responseBody['current_page']);
        $this->assertEquals(1, $responseBody['page_size']);
        $this->assertEquals(4, $responseBody['total_pages']);
    }

    public function testPageNavigation(): void
    {
        $queryParams = ['per_page' => 2, 'page' => 2];

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', sprintf('/scenarios?%s', http_build_query($queryParams)));
        $responseBody = json_decode($response->getBody(), true);

        $this->assertEquals(4, $responseBody['total_count']);
        $this->assertEquals(2, $responseBody['current_page']);
        $this->assertEquals(2, $responseBody['page_size']);
        $this->assertEquals(2, $responseBody['total_pages']);
        $this->assertCount(2, $responseBody['items']);
    }

    /**
     * @dataProvider filtersProvider
     */
    public function testSearchWithFilters(array $filters, int $expectedCount): void
    {
        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', sprintf('/scenarios?%s', http_build_query($filters)));
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);
        $this->assertCount($expectedCount, $responseBody['items']);
    }

    public function filtersProvider(): array
    {
        return [
            'Filter By Brand' => [
                ['brand' => BrandEnum::MASTERCARD],
                2
            ],
            'Filter By Installment Interval Start' => [
                ['installment_start' => 6],
                2
            ],
            'Filter By Installment Interval End' => [
                ['installment_end' => 7],
                2
            ],
            'Filter By Brand And Installment Interval Start' => [
                ['brand' => BrandEnum::MASTERCARD, 'installment_start' => 6],
                1
            ],
            'Filter By Brand And Installment Interval End' => [
                ['brand' => BrandEnum::MASTERCARD, 'installment_end' => 7],
                1
            ],
            'Filter By Non Existent Brand' => [
                ['brand' => 'fake'],
                0
            ]
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();
        $this->client = make(Client::class);

        $this->seed();
    }

    private function seed(): void
    {
        $scenarios = [
            [
                'brand' => BrandEnum::VISA,
                'installment_interval_start' => 1,
                'installment_interval_end' => 6
            ],
            [
                'brand' => BrandEnum::VISA,
                'installment_interval_start' => 7,
                'installment_interval_end' => 12
            ],
            [
                'brand' => BrandEnum::MASTERCARD,
                'installment_interval_start' => 1,
                'installment_interval_end' => 6
            ],
            [
                'brand' => BrandEnum::MASTERCARD,
                'installment_interval_start' => 7,
                'installment_interval_end' => 12
            ]
        ];

        foreach ($scenarios as $scenario) {
            PaymentScenario::create($scenario)->acquirers()->save(new PaymentScenarioAcquirer([
                'acquirer_id' => 1,
                'priority' => 1
            ]));
        }
    }
}