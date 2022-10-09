<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Controller\Documentation\HealthControllerDocumentation;

class HealthController implements HealthControllerDocumentation
{
    public function index()
    {
        return [
            'message' => 'I\'m alive',
        ];
    }
}
