<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalkinScanStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public int $packagePurchaseId,
        public string $nonce,
        public int $expiresAtUnix,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'walkin.scan_started';
    }

    public function broadcastWith(): array
    {
        return [
            'package_purchase_id' => $this->packagePurchaseId,
            'status' => 'started',
            'expires_at_unix' => $this->expiresAtUnix,
            'message' => 'QR scan started.',
        ];
    }
}

