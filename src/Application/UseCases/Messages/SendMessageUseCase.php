<?php

namespace src\Application\UseCases\Messages;

use App\Events\InboxMessageEvent;
use App\Events\SendMessageEvent;
use Illuminate\Support\Facades\Log;
use src\Domain\Entities\Messages\Messages;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Domain\Interfaces\Messages\IMessageRepository;
use src\Domain\Services\Messages\MessageServiceContext;
use src\Infrastructure\Helpers\Logger\Logger;
use src\Infrastructure\Mappers\Messages\MessagesMapper;

class SendMessageUseCase
{
    use Logger;
    public function __construct(
        private MessageServiceContext $messageServiceContext,
        private IMessageRepository $messageRepository,
        private IClientsRepository $clientsRepository
    ) {}

    public function execute(Messages $message)
    {
        $client = $this->clientsRepository->getClientById($message->clientId);
        $sendMessageResponse = $this->messageServiceContext->sendMessage($message,$client);
        $sendMessageResponse->client = $client->id;
        if($sendMessageResponse->status == "success") {
            $message->status = "success";
            $message->messageId = $sendMessageResponse->id;
            $this->messageRepository->saveMessage(MessagesMapper::toEntity($message->toArray()));
        }else{
            $this->createLog("\nmessageId: {$sendMessageResponse->id}\n{$sendMessageResponse->error}",'Send Message Use Case','ERROR');
            $message->status = "error";
            $message->error = $sendMessageResponse->error ?? null;
            $message->messageId = $sendMessageResponse->id;
            $this->messageRepository->saveMessage(MessagesMapper::toEntity($message));
        }
        broadcast(new SendMessageEvent($message));
        broadcast(new InboxMessageEvent($message));
        return $sendMessageResponse;
    }

}
