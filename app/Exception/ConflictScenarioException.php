<?php

namespace App\Exception;

use App\Http\Resource\PaymentScenarioResource;
use App\Model\PaymentScenario;

class ConflictScenarioException extends HttpException
{
    public static function make(PaymentScenario $scenario): self
    {
        $resource = new PaymentScenarioResource($scenario);

        $self = new self('Conflict Scenario', 409);
        $self->status = 409;
        $self->response = $resource->toArray();

        return $self;
    }
}
