<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewTradeEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tradeData;

    /**
     * Create a new event instance.
     */
    public function __construct($tradeData)
    {
        $this->tradeData = $tradeData;
        Log::info("NewTradeEvent initialized", ['tradeData' => $tradeData]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info("NewTradeEvent is broadcasting on channel: trade-channel", [
            'tradeData' => $this->tradeData
        ]);

        return [
            new Channel('trade-channel'),
        ];
    }


    public function broadcastAs()
    {
        return 'new-trade';
    }
}
