<?php

namespace src\Web\Controllers\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use src\Application\DTO\Messages\ReceiveStatusDTO;
use src\Application\UseCases\Messages\ReceiveMessageStatusUseCase;
use src\Application\UseCases\Messages\SendMessageUseCase;
use src\Domain\Interfaces\Messages\IMessageRepository;
use src\Infrastructure\Mappers\Messages\MessagesMapper;

class MessagesController extends Controller
{
    public function __construct(
        private IMessageRepository $messageRepository,

    )
    {
    }

    public function getMessages(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);
        $clientId = $validated['client_id'];
        $page = $validated['page'] ?? 1;
        $limit = $validated['limit'] ?? 50;
        $result = $this->messageRepository->getMessagesByClientId($clientId, $page, $limit);
        return response()->json($result);
    }
    public function readAllMessagesByClient(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:clients,id'
        ]);
        $clientId = $validated['id'];
        $this->messageRepository->readAllMessagesByClientId($clientId);
        return response()->json(['status' => 'ok']);
    }


}
