<?php

namespace src\Application\UseCases\Messages;


use src\Application\DTO\Messages\ReceiveStatusDTO;
use src\Domain\Entities\Messages\Messages;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Domain\Interfaces\Messages\IMessageRepository;

class ReceiveMessageStatusUseCase
{
    public function __construct(
        private IMessageRepository $messageRepository,
        private IClientsRepository $clientsRepository
    ) {}

    public function execute(ReceiveStatusDTO $receiveMessageDTO): array
    {

        $dbMessage = $this->messageRepository->getMessagesById($receiveMessageDTO->messageId);

        if (!empty($dbMessage)) {
            switch ($receiveMessageDTO->status) {
                case 'Message successfully delivered':
                    $dbMessage->status = "delivered";
                    $this->messageRepository->saveMessage($dbMessage);
                    break;
            }
        }

        return [];

    }

}
