<?php

namespace App\Job;

use App\Service\WebhookService;
use Hyperf\AsyncQueue\Job;

class SendWebhookNotificationJob extends Job
{
    public function __construct(private array $scenario)
    {
    }

    public function handle()
    {
        if (empty($this->scenario['brand'])) {
            return;
        }

        /** @var WebhookService $service */
        $service = make(WebhookService::class);
        $service->notifyChangesForBrand($this->scenario['brand']);
    }

    public function getMaxAttempts(): int
    {
        return 3;
    }
}
