<?php

namespace App\Http\Resource;

use App\Model\Acquirer;
use Hyperf\Resource\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class AcquirerResource extends JsonResource
{
    /** @var Acquirer $resource */
    public $resource;

    /**
     * @OA\Property(
     *      property="id",
     *      type="integer",
     *      example="1"
     * ),
     * @OA\Property(
     *      property="name",
     *      type="string",
     *      example="green"
     *),
     * @OA\Property(
     *      property="default",
     *      type="boolean",
     *      example="true"
     * )
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'default' => $this->resource->default
        ];
    }
}
