<?php

namespace App\Http\Resource;

use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Resource\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class PaymentScenarioPaginatorResource extends JsonResource
{
    /** @var LengthAwarePaginatorInterface $resource */
    public $resource;

    /**
     * @OA\Property(
     *      property="total_count",
     *      type="integer",
     *      example="20"
     * ),
     * @OA\Property(
     *      property="current_page",
     *      type="integer",
     *      example="1"
     * ),
     * @OA\Property(
     *      property="page_size",
     *      type="integer",
     *      example="10"
     * ),
     * @OA\Property(
     *      property="total_pages",
     *      type="integer",
     *      example="2"
     * ),
     * @OA\Property(
     *      property="items",
     *      type="array",
     *      @OA\Items(ref="#/components/schemas/PaymentScenarioResource")
     * )
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'total_count' => $this->resource->total(),
            'current_page' => $this->resource->currentPage(),
            'page_size' => $this->resource->perPage(),
            'total_pages' => $this->resource->lastPage(),
            'items' => PaymentScenarioResource::collection($this->resource->items()),
        ];
    }
}
