<?php

namespace src\Infrastructure\Mappers\Messages;

use src\Domain\Entities\Messages\Messages;
use src\Infrastructure\DB\Messages\DBMessage;
use src\Infrastructure\Mappers\IMapper;

class MessagesMapper implements IMapper
{

    public static function toDomain(array|object $object)
    {
        // Если входной параметр — массив
        if (is_array($object) && isset($object[0])) {
            $clientsArray = [];
            foreach ($object as $message) {
                $clientsArray[] = new Messages(
                    $message['sender'] ?? null,
                        $message['text'] ?? null,
                        $message['channel'] ?? null,
                        $message['fileUrl'] ?? null,
                        $message['messageId'] ?? null,
                        $message['status'] ?? null,
                        $message['sentAt'] ?? null,
                        $message['client_id'] ?? null,
                        $message['send_type'] ?? null,
                        $message['messageRead'] ?? true,
                        $message['error'] ?? null

                );
            }
            return $clientsArray;
        }
        if (is_array($object)) {
            return new Messages(
                $object['sender'] ?? null,
                    $object['text'] ?? null,
                    $object['channel'] ?? null,
                    $object['fileUrl'] ?? null,
                    $object['messageId'] ?? null,
                    $object['status'] ?? null,
                    $object['sentAt'] ?? null,
                    $object['client_id'] ?? null,
                    $object['send_type'] ?? null,
                    $object['messageRead'] ?? true,
                    $object['error'] ?? null
            );
        }
        // Если входной параметр — объект
        if (is_object($object)) {
            return new Messages(
                $object->sender ?? null,
                    $object->text ?? null,
                    $object->channel ?? null,
                    $object->fileUrl ?? null,
                    $object->messageId ?? null,
                    $object->status ?? null,
                    $object->sentAt ?? null,
                    $object->client_id ?? null,
                    $object->send_type ?? null,
                    $object->messageRead ?? true,
                    $object->error ?? null
            );
        }

        // Если формат неподходящий, возвращаем null
        return null;
    }

    public static function toEntity(array|object $object)
    {
        // Если входной параметр — массив
        if (is_array($object) && isset($object[0])) {
            $clientsArray = [];
            foreach ($object as $message) {
                $clientsArray[] = new DBMessage([
                        'sender' => $message['sender'] ?? null,
                        'text' => $message['text'] ?? null,
                        'channel' => $message['channel'] ?? null,
                        'fileUrl' => $message['fileUrl'] ?? null,
                        'message_id' => $message['messageId'] ?? null,
                        'status' => $message['status'] ?? null,
                        'send_time' => $message['sentAt'] ?? null,
                        'client_id' => $message['clientId'] ?? null,
                        'send_type' => $message['sendType'] ?? null,
                        'message_read' => $message['messageRead'] ?? true,
                        'error' => $message['error'] ?? null,
                    ]

                );
            }
            return $clientsArray;
        }
        if (is_array($object)) {
            return new DBMessage([
                    'sender' => $object['sender'] ?? null,
                    'text' => $object['text'] ?? null,
                    'channel' => $object['channel'] ?? null,
                    'fileUrl' => $object['fileUrl'] ?? null,
                    'message_id' => $object['messageId'] ?? null,
                    'status' => $object['status'] ?? null,
                    'send_time' => $object['sentAt'] ?? null,
                    'client_id' => $object['clientId'] ?? null,
                    'send_type' => $object['sendType'] ?? null,
                    'message_read' => $object['messageRead'] ?? true,
                    'error' => $object['error'] ?? null,
                ]
            );
        }
        // Если входной параметр — объект
        if (is_object($object)) {
            return new DBMessage([
                    'sender' => $object->sender ?? null,
                    'text' => $object->text ?? null,
                    'channel' => $object->channel ?? null,
                    'fileUrl' => $object->fileUrl ?? null,
                    'message_id' => $object->messageId ?? null,
                    'status' => $object->status ?? null,
                    'send_time' => $object->sentAt ?? null,
                    'client_id' => $object->clientId ?? null,
                    'send_type' => $object->sendType ?? null,
                    'message_read' => $object->messageRead ?? true,
                    'error' => $object->error ?? null,
                ]
            );
        }

        // Если формат неподходящий, возвращаем null
        return null;
    }
}
