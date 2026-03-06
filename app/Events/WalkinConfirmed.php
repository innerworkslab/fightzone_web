<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalkinConfirmed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public int $packagePurchaseId,
        public int $userWalkinId,
        public int $remainingDays,
        public bool $completed,
    ) {}

    /**
     * Private channel so only the authenticated user can receive walk-in status.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * Event name for the client to listen to.
     */
    public function broadcastAs(): string
    {
        return 'walkin.confirmed';
    }

    /**
     * Payload sent to the client.
     */
    public function broadcastWith(): array
    {
        return [
            'package_purchase_id' => $this->packagePurchaseId,
            'user_walkin_id' => $this->userWalkinId,
            'remaining_days' => $this->remainingDays,
            'completed' => $this->completed,
            'message' => $this->completed
                ? 'Walk-in confirmed. This package is now fully used.'
                : "Walk-in confirmed. {$this->remainingDays} day(s) remaining.",
        ];
    }
}
