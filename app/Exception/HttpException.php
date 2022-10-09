<?php

namespace App\Exception;

use Exception;

abstract class HttpException extends Exception
{
    protected int $status;

    protected array $response;

    public function getResponseBody(): string
    {
        return json_encode($this->response);
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
