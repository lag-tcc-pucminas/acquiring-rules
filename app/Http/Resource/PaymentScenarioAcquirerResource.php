<?php

namespace App\Http\Resource;

use App\Model\PaymentScenarioAcquirer;
use Hyperf\Resource\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class PaymentScenarioAcquirerResource extends JsonResource
{
    /** @var PaymentScenarioAcquirer $resource */
    public $resource;

    /**
     * @OA\Property(
     *      property="acquirer",
     *      type="object",
     *      @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *      ),
     *      @OA\Property(
     *          property="name",
     *          type="string",
     *          example="green"
     *      )
     * ),
     * @OA\Property(
     *      property="priority",
     *      type="integer",
     *      example="1"
     *)
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'acquirer' => [
                'id' => $this->resource->acquirer->id,
                'name' => $this->resource->acquirer->name
            ],
            'priority' => $this->resource->priority
        ];
    }
}
