<?php

namespace App\Http\Resource;

use App\Model\PaymentScenario;
use Hyperf\Resource\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class PaymentScenarioResource extends JsonResource
{
    /** @var PaymentScenario $resource */
    public $resource;

    /**
     * @OA\Property(
     *      property="id",
     *      type="integer",
     *      example="1"
     *),
     * @OA\Property(
     *      property="brand",
     *      type="string",
     *      example="mastercard"
     *),
     * @OA\Property(
     *      property="installments",
     *      type="object",
     *      @OA\Property(
     *          property="start",
     *          type="integer",
     *          example="1"
     *      ),
     *      @OA\Property(
     *          property="end",
     *          type="integer",
     *          example="12"
     *      )
     * ),
     * @OA\Property(
     *      property="acquirers",
     *      type="array",
     *      @OA\Items(ref="#/components/schemas/PaymentScenarioAcquirerResource")
     * )
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'brand' => $this->resource->brand,
            'installments' => [
                'start' => $this->resource->installment_interval_start,
                'end' => $this->resource->installment_interval_end,
            ],
            'acquirers' => PaymentScenarioAcquirerResource::collection($this->resource->acquirers)
        ];
    }
}
