<?php

namespace App\Repository;

use App\Model\Acquirer;
use Hyperf\Database\Model\Collection;

class AcquirerRepository
{
    public function getAll(): Collection
    {
        return Acquirer::all();
    }

    public function getDefault(): Acquirer
    {
        return Acquirer::where('default', true)->first();
    }
}
