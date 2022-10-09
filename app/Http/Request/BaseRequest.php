<?php

namespace App\Http\Request;

use Hyperf\Validation\Request\FormRequest;

abstract class BaseRequest extends FormRequest
{
    protected function authorize(): bool
    {
        return true;
    }
}
