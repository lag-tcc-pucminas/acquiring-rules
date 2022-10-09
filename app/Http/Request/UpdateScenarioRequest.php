<?php

namespace App\Http\Request;

use App\Enum\BrandEnum;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class UpdateScenarioRequest extends BaseRequest
{
    /**
     * @OA\Property(
     *      property="brand",
     *      type="string",
     *      example="mastercard"
     * ),
     * @OA\Property(
     *      property="installment_interval",
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
     *      example={{"acquirer_id":1,"priority":1},{"acquirer_id":2,"priority":2}},
     *      @OA\Items(
     *          @OA\Property(
     *              property="acquirer_id",
     *              type="integer",
     *              example="1"
     *          ),
     *          @OA\Property(
     *              property="priority",
     *              type="integer",
     *              example="1"
     *          )
     *      )
     * )
     *
     * @return array
     */
    protected function getRules(): array
    {
        return [
            'brand' => 'required|string|in:' . implode(',', BrandEnum::getValidValues()),

            'installment_interval' => 'required|filled',
            'installment_interval.start' => 'required|numeric|between:1,12|lte:installment_interval.end',
            'installment_interval.end' => 'required|numeric|between:1,12|gte:installment_interval.start',

            'acquirers' => 'required|filled',
            'acquirers.*.id' => 'required|numeric|distinct|exists:acquirers,id',
            'acquirers.*.priority' => 'required|numeric|distinct|gte:1'
        ];
    }
}
