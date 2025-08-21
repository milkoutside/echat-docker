<?php

namespace src\Domain\Interfaces\Messages;

use src\Domain\Entities\Messages\Messages;
use src\Infrastructure\DB\Messages\DBMessage;

interface IMessageRepository
{

    public function getMessagesByClientId($clientId, $page, $limit);
    public function getMessagesById($messageId);
    public function saveMessage(DBMessage $message): bool;
    public function updateMessage(Messages $message): bool;
    public function deleteMessage(Messages $message): bool;
    public function getLastMessageByClientId($clientId);
    public function readAllMessagesByClientId($clientId);


}
