<?php

namespace src\Infrastructure\Repositories\MessagesRepository;

use src\Application\Responses\MessangerSendedResponse;
use src\Domain\Entities\Messages\Messages;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Domain\Interfaces\Messages\IMessageRepository;
use src\Domain\Interfaces\Messages\IMessageServiceStrategy;
use src\Domain\Interfaces\Messages\Parts\SendMessageByUsername;
use src\Infrastructure\External\Echat\Auth\Auth;
use src\Infrastructure\External\Echat\EchatApi;

class MessagesEchatTelegramRepository implements IMessageServiceStrategy
{
    private IMessageRepository $messageRepository;
    private IClientsRepository $clientsRepository;
    public function __construct(

    ) {}

    public function send($message, $client): MessangerSendedResponse
    {
        Auth::setApiKeyBySender($message->sender, 'telegram');
        $response =  EchatApi::getInstance()
            ->api()
            ->telegram()
            ->sendMessage([
                "user" => [
                    "number" => $message->sender
                ],
                "message" => [
                    "id" => $message->messageId,
                    "text" => $message->text,
                    "file_path" => $message->fileUrl ?? null
                ],
                "receiver" => [
                    "username" => $client->username,
                    //"phone" => $client->phone,
                ]
            ]);
        $response->id = $message->messageId;
        $response->client = $client->id;
        return $response;
    }

    public function receive(Messages $message)
    {
        $dbMessage = $this->messageRepository->getMessagesById($message->messageId);

        if (!empty($dbMessage)) {
            // Если сообщение существует, обновляем его данные.
            $dbMessage->status = $message->status;
            $this->messageRepository->saveMessage($dbMessage);
        } else {
            // Если сообщение отсутствует
            if ($message->sendType == "incoming") {
                $clientPhone = $message->sender;
                $dbClient = $this->clientsRepository->getClientByPhone($clientPhone);

                if (!empty($dbClient)) {
                    // Если клиент существует, но сообщение отсутствует, создаем сообщение.
                    $newMessage = new Messages();
                    $newMessage->clientId = $dbClient->id;
                    $newMessage->content = $message->content;
                    $newMessage->status = $message->status;
                    $this->messageRepository->saveMessage($newMessage);
                } else {
                    // Если клиента нет, создаем клиента и сообщение.
                    $newClient = $this->clientsRepository->createOrUpdateClient($clientPhone);

                    $newMessage = new Messages();
                    $newMessage->clientId = $newClient->id;
                    $newMessage->content = $message->content;
                    $newMessage->status = $message->status;
                    $this->messageRepository->saveMessage($newMessage);
                }
            } else {
                // Логика для других типов сообщений, если необходимо.
            }
        }
    }

    public function sendMessageByUsername($messageData)
    {
        // TODO: Implement sendMessageByUsername() method.
    }
}
