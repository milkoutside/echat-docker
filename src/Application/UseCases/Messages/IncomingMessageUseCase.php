<?php

namespace src\Application\UseCases\Messages;

use App\Events\InboxMessageEvent;
use App\Events\SendMessageEvent;
use src\Application\DTO\Clients\UpsertClientDTO;
use src\Domain\Entities\Clients\Clients;
use src\Domain\Entities\Messages\Messages;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Domain\Interfaces\Messages\IMessageRepository;
use src\Infrastructure\Mappers\Messages\MessagesMapper;

class IncomingMessageUseCase
{
    public function __construct(
        private IMessageRepository $messageRepository,
        private IClientsRepository $clientsRepository
    ) {}

    public function execute(Messages $message, Clients $client): array
    {
        $dbMessage = $this->messageRepository->getMessagesById($message->messageId);

        // Получаем телефон из соответствующего канала
        $phone = $client->phones[$message->channel] ?? null;
        $username = $client->username;

        // Ищем клиента по номеру (во всех сервисах) или username
        $dbClient = $this->clientsRepository->getClientByPhoneOrUsername($phone, $username);

        if (empty($dbClient)) {
            // Создаем массив phones для нового клиента
            $phones = [];
            if ($phone) {
                $phones[$message->channel] = $phone;
            }

            $dbClient = $this->clientsRepository->createClient(
                new UpsertClientDTO(!empty($phones) ? $phones : null, $username)
            );
        } else {
            // Если клиент найден, обновляем phones при необходимости
            $currentPhones = is_array($dbClient->phones) ? $dbClient->phones : json_decode($dbClient->phones, true) ?? [];

            if ($phone && !isset($currentPhones[$message->channel])) {
                $updatedPhones = array_merge($currentPhones, [$message->channel => $phone]);
                $this->clientsRepository->updateClientById(
                    new UpsertClientDTO($updatedPhones, $username),
                    $dbClient->id
                );
            }
        }

        if (!empty($dbClient)) {
            $message->clientId = $dbClient->id;
            if (empty($dbMessage)) {
                $this->messageRepository->saveMessage(MessagesMapper::toEntity($message->toArray()));
                broadcast(new SendMessageEvent($message));
                broadcast(new InboxMessageEvent($message));
            }
        }

        return [];
    }
}
