<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use src\Domain\Entities\Messages\Messages;

class SendMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    private $message;
    public function __construct(Messages $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Отправляем событие только на канал этого пользователя
        return new Channel('laravel_database_client_' . $this->message->clientId);
    }

    public function broadcastAs()
    {
        return 'new_message'; // Имя события для клиента
    }
    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
