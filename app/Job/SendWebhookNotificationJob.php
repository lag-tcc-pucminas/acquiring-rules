<?php

namespace App\Job;

use App\Service\WebhookService;
use Hyperf\AsyncQueue\Job;
use Hyperf\Contract\CompressInterface;
use Hyperf\Contract\UnCompressInterface;

class SendWebhookNotificationJob extends Job
{
    private ?WebhookService $service = null;
    private array $scenario;

    public function __construct(array $scenario)
    {
        $this->scenario = $scenario;
    }

    public function handle()
    {
        if (empty($this->scenario['brand'])) {
            return;
        }

        $this->service->notifyChangesForBrand($this->scenario['brand']);
    }

    public function getMaxAttempts(): int
    {
        return 3;
    }

    public function compress(): UnCompressInterface
    {
        $this->service = null;
        return parent::compress();
    }

    public function uncompress(): CompressInterface
    {
        /** @var WebhookService $service */
        $this->service = make(WebhookService::class);

        return parent::uncompress();
    }
}
