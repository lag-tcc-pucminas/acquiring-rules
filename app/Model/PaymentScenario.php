<?php

namespace App\Model;

use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Events\Deleting;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int id
 * @property string brand
 * @property int installment_interval_start
 * @property int installment_interval_end
 * @property Collection acquirers
 */
class PaymentScenario extends Model
{
    use SoftDeletes;

    protected $table = 'payment_scenarios';

    protected $fillable = ['brand', 'installment_interval_start', 'installment_interval_end'];

    protected $casts = ['installment_interval_start' => 'int', 'installment_interval_end' => 'int'];

    public function acquirers(): HasMany
    {
        return $this->hasMany(PaymentScenarioAcquirer::class, 'payment_scenario_id', 'id');
    }
}
