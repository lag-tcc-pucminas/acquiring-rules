<?php

namespace App\Model;

use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int id
 * @property int priority
 * @property int payment_scenario_id
 * @property int acquirer_id
 * @property Acquirer $acquirer
 */
class PaymentScenarioAcquirer extends Model
{
    protected $table = 'payment_scenario_acquirers';

    protected $fillable = ['acquirer_id', 'payment_scenario_id', 'priority'];

    protected $casts = ['priority' => 'int'];

    public $timestamps = false;

    protected $with = ['acquirer'];

    public function acquirer(): BelongsTo
    {
        return $this->belongsTo(Acquirer::class, 'acquirer_id', 'id');
    }

    public static function buildFromDefault(Acquirer $acquirer): self
    {
        return new self([
            'acquirer_id' => $acquirer->id,
            'priority' => 1
        ]);
    }
}
