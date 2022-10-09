<?php

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int id
 * @property string $name
 * @property boolean $default
 */

class Acquirer extends Model
{
    public const GREEN = 'green';
    public const RED = 'red';
    public const BLUE = 'blue';

    protected $table = 'acquirers';

    protected $fillable = [];

    protected $casts = ['default' => 'bool'];
}
