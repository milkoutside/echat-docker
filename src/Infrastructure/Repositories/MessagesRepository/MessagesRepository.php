<?php

namespace src\Infrastructure\Repositories\MessagesRepository;

use src\Domain\Entities\Messages\Messages;
use src\Domain\Interfaces\Messages\IMessageRepository;
use src\Infrastructure\DB\Messages\DBMessage;

class MessagesRepository implements IMessageRepository
{
    public function getMessagesByClientId($clientId, $page = 1, $limit = 50)
    {
        return DBMessage::where('client_id', $clientId)
            ->orderBy('send_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function saveMessage(DBMessage $message): bool
    {
        return $message->save();
    }

    public function updateMessage(Messages $message): bool
    {
        return DBMessage::where('id', $message->id)
                ->update($message->toArray()) > 0;
    }

    public function deleteMessage(Messages $message): bool
    {
        return DBMessage::where('id', $message->id)
                ->delete() > 0;
    }

    public function getMessagesById($messageId)
    {
        return DBMessage::where('message_id', $messageId)
            ->first();
    }

    public function getLastMessageByClientId($clientId)
    {
        return DBMessage::where('client_id', $clientId)
            ->orderBy('send_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }
    public function readAllMessagesByClientId($clientId)
    {
        DBMessage::where('client_id', $clientId)->update(['message_read' => true]);
    }
}

