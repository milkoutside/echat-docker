<?php

namespace src\Web\Controllers\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use src\Application\DTO\Messages\ReceiveStatusDTO;
use src\Application\UseCases\Messages\IncomingMessageUseCase;
use src\Application\UseCases\Messages\ReceiveMessageStatusUseCase;
use src\Application\UseCases\Messages\SendMessageUseCase;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Infrastructure\External\CRM\ZohoCRM\ZohoCRMApi;
use src\Infrastructure\Mappers\Clients\EchatIncomingTelegramMessageClientMapper;
use src\Infrastructure\Mappers\Clients\EchatIncomingViberMessageClientMapper;
use src\Infrastructure\Mappers\Clients\EchatIncomingWhatsAppMessageClientMapper;
use src\Infrastructure\Mappers\Messages\EchatIncomingTelegramMessageMapper;
use src\Infrastructure\Mappers\Messages\EchatIncomingViberMessageMapper;
use src\Infrastructure\Mappers\Messages\EchatIncomingWhatsAppMessageMapper;
use src\Infrastructure\Mappers\Messages\MessagesMapper;

class MessagesEchatController extends Controller
{
    public function __construct(
        private SendMessageUseCase          $sendMessageUseCase,
        private ReceiveMessageStatusUseCase $receiveMessageStatusUseCase,
        private IncomingMessageUseCase      $incomingMessageUseCase,
        private IClientsRepository          $clientsRepository,
    )
    {
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'sender' => 'required|string',
            'text' => 'nullable|string',
            'channel' => 'required|string|in:telegram,viber,whatsapp',
            'fileUrl' => 'nullable|string',
            'client_id' => 'required|integer|exists:clients,id',
            'send_type' => 'required|string|in:text,file'
        ]);
        $result = $this->sendMessageUseCase->execute(MessagesMapper::toDomain($validated));
        return response()->json($result);
    }

    public function receiveStatusTelegram(Request $request)
    {
        $data = $request->all();
        Log::info(json_encode($request->all()));
        /*$result = $this->receiveMessageStatusUseCase->execute(new ReceiveStatusDTO(
            $data['message_id'],
            $data['description'],
            $data['status'] ?? null,
            $data['messenger'] ?? null,
            $data['event'] ?? null
        ));*/
    }

    public function receiveStatusWhatsApp(Request $request)
    {
        $data = $request->all();
        // Log::info(json_encode($request->all()));
        /*$result = $this->receiveMessageStatusUseCase->execute(new ReceiveStatusDTO(
            $data['message_id'],
            $data['description'],
            $data['status'] ?? null,
            $data['messenger'] ?? null,
            $data['event'] ?? null
        ));*/
    }

    public function receiveStatusViber(Request $request)
    {
        $data = $request->all();
        // Log::info(json_encode($request->all()));
        /*$result = $this->receiveMessageStatusUseCase->execute(new ReceiveStatusDTO(
            $data['message_id'],
            $data['description'],
            $data['status'] ?? null,
            $data['messenger'] ?? null,
            $data['event'] ?? null
        ));*/
    }

    public function incomingMessageTelegram(Request $request)
    {
        $data = $request->all();
        Log::info(json_encode($data));
        $domainMessage = EchatIncomingTelegramMessageMapper::toDomain($data);
        $domainClient = EchatIncomingTelegramMessageClientMapper::toDomain($data);

        // Получаем telegram номер из массива phones
        $telegramPhone = $domainClient->phones['telegram'] ?? null;

        // Ищем клиента по telegram номеру или username
        $dbClient = $this->clientsRepository->getClientByPhoneOrUsername($telegramPhone, $domainClient->username);

        if (empty($dbClient)) {
            $modulesToSearch = ['Contacts'];
            foreach ($modulesToSearch as $module) {
                $searchResults = [];

                // Ищем по telegram номеру
                /*if (!empty($telegramPhone)) {
                    $searchResults = ZohoCRMApi::getInstance()
                        ->api()
                        ->records()
                        ->searchRecords($module, 'phone', $telegramPhone);

                    // Если не нашли по основному номеру, проверяем другие поля
                    if (empty($searchResults)) {
                        $searchResults = ZohoCRMApi::getInstance()
                            ->api()
                            ->records()
                            ->searchRecords($module, 'Viber_phone', $telegramPhone);
                    }
                    if (empty($searchResults)) {
                        $searchResults = ZohoCRMApi::getInstance()
                            ->api()
                            ->records()
                            ->searchRecords($module, 'WhatsApp_phone', $telegramPhone);
                    }
                }
*/
                // Если не нашли по номеру, ищем по username
                if (empty($searchResults) && !empty($domainClient->username)) {
                    $searchResults = ZohoCRMApi::getInstance()
                        ->api()
                        ->records()
                        ->searchRecords(
                            $module,
                            'criteria',
                            "Telegram_nickname:equals:{$domainClient->username}"
                        );
                }

                if (!empty($searchResults[0])) {
                    $findClient = $searchResults[0];
                    break;
                }
            }

            if (empty($findClient['id'])) {
                // Создаем нового контакта в CRM
                $contactData = [
                    "Last_Name" => !empty($telegramPhone) ? $telegramPhone : $domainClient->username,
                    "Lead_Source" => "Telegram",
                    //"Phone" => $telegramPhone,
                    "Telegram_nickname" => $domainClient->username,
                ];


                $response = ZohoCRMApi::getInstance()->api()->records()->createRecord('Contacts', [
                    "data" => [
                        $contactData
                    ],
                    "trigger" => [
                        "workflow",
                        "blueprint",
                        "approval"
                    ]
                ]);
            }
        }
        Log::info(json_encode($dbClient));
        $this->incomingMessageUseCase->execute($domainMessage, $domainClient);
    }

    public function incomingMessageViber(Request $request)
    {
        $data = $request->all();
        $clientName = !empty($data['contact']['name']) ? $data['contact']['name'] : null;
        $domainMessage = EchatIncomingViberMessageMapper::toDomain($data);
        $domainClient = EchatIncomingViberMessageClientMapper::toDomain($data);
        $findClient = false;
        $dbClient = $this->clientsRepository->getClientByPhoneOrUsername($domainClient->phone, null);
        if (empty($dbClient)) {
            $modulesToSearch = ['Contacts'];
            foreach ($modulesToSearch as $module) {
                $phoneResults = ZohoCRMApi::getInstance()
                    ->api()
                    ->records()
                    ->searchRecords($module, 'phone', $domainClient->phone);
                if (!empty($phoneResults[0])) {
                    $findClient = $phoneResults[0];
                }
            }
            if (empty($findClient['id'])) {
                Log::info('Creating new client');
                $response = ZohoCRMApi::getInstance()->api()->records()->createRecord('Contacts', [
                    "data" => [
                        [
                            "Last_Name" => !empty($clientName) ? $clientName : $domainClient->phone,
                            "Lead_Source" => "Viber",
                            //"Source_details" => $domainMessage->text,
                            "Phone" => $domainClient->phone,
                        ]
                    ],
                    "trigger" => [
                        "workflow",
                        "blueprint",
                        "approval"
                    ]
                ]);
                Log::info(json_encode($response));
            }
        }
        $this->incomingMessageUseCase->execute($domainMessage, $domainClient);
    }

    public function incomingMessageWhatsApp(Request $request)
    {
        $data = $request->all();
        $clientName = !empty($data['contact']['name']) ? $data['contact']['name'] : null;
        $domainMessage = EchatIncomingWhatsAppMessageMapper::toDomain($data);
        $domainClient = EchatIncomingWhatsAppMessageClientMapper::toDomain($data);
        $whatsappPhone = $domainClient->phones['whatsapp'] ?? null;

        // Ищем клиента по telegram номеру или username
        $dbClient = $this->clientsRepository->getClientByPhoneOrUsername($whatsappPhone, $domainClient->username);

        $findClient = false;
        if (empty($dbClient)) {
            $modulesToSearch = ['Contacts'];
            foreach ($modulesToSearch as $module) {
                $searchResults = [];

                // Ищем по telegram номеру
                /*if (!empty($telegramPhone)) {
                    $searchResults = ZohoCRMApi::getInstance()
                        ->api()
                        ->records()
                        ->searchRecords($module, 'phone', $telegramPhone);

                    // Если не нашли по основному номеру, проверяем другие поля
                    if (empty($searchResults)) {
                        $searchResults = ZohoCRMApi::getInstance()
                            ->api()
                            ->records()
                            ->searchRecords($module, 'Viber_phone', $telegramPhone);
                    }
                    if (empty($searchResults)) {
                        $searchResults = ZohoCRMApi::getInstance()
                            ->api()
                            ->records()
                            ->searchRecords($module, 'WhatsApp', $telegramPhone);
                    }
                }
*/
                // Если не нашли по номеру, ищем по username
                if (!empty($whatsappPhone)) {
                    if (empty($searchResults)) {
                        $searchResults = ZohoCRMApi::getInstance()
                            ->api()
                            ->records()
                            ->searchRecords($module, 'WhatsApp', $whatsappPhone);
                    }
                }

                if (!empty($searchResults[0])) {
                    $findClient = $searchResults[0];
                    break;
                }
            }

            if (empty($findClient['id'])) {
                // Создаем нового контакта в CRM
                $contactData = [
                    "Last_Name" => !empty($whatsappPhone) ? $whatsappPhone : $clientName,
                    "Lead_Source" => "WhatsApp",
                    //"Phone" => $telegramPhone,
                    "WhatsApp" => $whatsappPhone,
                ];


                $response = ZohoCRMApi::getInstance()->api()->records()->createRecord('Contacts', [
                    "data" => [
                        $contactData
                    ],
                    "trigger" => [
                        "workflow",
                        "blueprint",
                        "approval"
                    ]
                ]);
            }
        }
        Log::info(json_encode($dbClient));
        $this->incomingMessageUseCase->execute($domainMessage, $domainClient);
    }
}
