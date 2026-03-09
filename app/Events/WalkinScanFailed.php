<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalkinScanFailed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public int $packagePurchaseId,
        public string $nonce,
        /** @var string One of: expired, used, not_found, no_remaining, rejected, error */
        public string $reason,
        public string $message,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'walkin.scan_failed';
    }

    public function broadcastWith(): array
    {
        return [
            'package_purchase_id' => $this->packagePurchaseId,
            'status' => 'failed',
            'reason' => $this->reason,
            'message' => $this->message,
        ];
    }
}

