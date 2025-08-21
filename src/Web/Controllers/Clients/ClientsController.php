<?php

namespace src\Web\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use src\Application\DTO\Clients\UpsertClientDTO;
use src\Domain\Interfaces\Clients\IClientsRepository;

class ClientsController extends Controller
{
    public function __construct(
        private IClientsRepository $clientsRepository
    ) {}

    public function getClients(Request $request)
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);
        $page = $validated['page'] ?? 1;
        $limit = $validated['limit'] ?? 10;
        return $this->clientsRepository->getClients($page, $limit);
    }

    public function getClientByPhoneOrUsername(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string',
            'username' => 'nullable|string'
        ]);
        return $this->clientsRepository->getClientByPhoneOrUsername($validated['phone'] ?? null, $validated['username'] ?? null);
    }
    public function getClientByPhonesOrUsername(Request $request)
    {
        $validated = $request->validate([
            'phones' => 'nullable|array',
            'username' => 'nullable|string'
        ]);
        return $this->clientsRepository->getClientByPhonesOrUsername($validated['phones'] ?? null, $validated['username'] ?? null);
    }
    public function getClientById(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:clients,id'
        ]);
        return $this->clientsRepository->getClientById($validated['id']);
    }

    public function createOrUpdateClient(Request $request)
    {
        $validated = $request->validate([
            'phones' => 'nullable|array',
            'username' => 'nullable|string'
        ]);
        $phones = $validated['phones'] ?? [];
        return $this->clientsRepository->createOrUpdateClient(
            new UpsertClientDTO(
                $phones,
                $validated['username'] ?? null
            )
        );
    }

    public function updateClientById(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:clients,id',
            'phones' => 'nullable|array',
            'username' => 'nullable|string'
        ]);
        $phones = $validated['phones'] ?? [];
        $phones = array_filter($phones, function($phone) { return !empty($phone); });
        return $this->clientsRepository->updateClientById(
            new UpsertClientDTO(
                !empty($phones) ? $phones : null,
                $validated['username'] ?? null
            ),
            $validated['id']
        );
    }

    public function mergeClientsByData(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string',
            'username' => 'nullable|string'
        ]);
        $phone = $validated['phone'] ?? null;
        $username = $validated['username'] ?? null;

        if (!$phone && !$username) {
            return response()->json(['error' => 'Phone or username required'], 400);
        }

        // Поиск клиентов по номеру телефона (в любом из сервисов) или username
        $clients = DB::table('clients')
            ->when($phone, function($query) use ($phone) {
                $query->whereJsonContains('phones', $phone)
                    ->orWhereJsonContains('phones->main', $phone)
                    ->orWhereJsonContains('phones->viber', $phone)
                    ->orWhereJsonContains('phones->telegram', $phone)
                    ->orWhereJsonContains('phones->whatsapp', $phone);
            })
            ->when($username, function($query) use ($username) {
                $query->orWhere('username', $username);
            })
            ->get();

        if ($clients->count() > 1) {
            $primaryClient = $clients->first();
            $primaryClientId = $primaryClient->id;

            // Собираем все phones из дубликатов
            $allPhones = $clients->pluck('phones')
                ->map(function($phonesJson) {
                    return json_decode($phonesJson, true) ?: [];
                })
                ->reduce(function($carry, $phones) {
                    return array_merge($carry, $phones);
                }, []);

            // Объединяем username
            $mergedUsername = $primaryClient->username ?:
                $clients->whereNotNull('username')->pluck('username')->first();

            // Обновляем главного клиента
            DB::table('clients')->where('id', $primaryClientId)->update([
                'phones' => json_encode($allPhones),
                'username' => $mergedUsername
            ]);

            // Переносим сообщения
            DB::table('messages')
                ->whereIn('client_id', $clients->pluck('id'))
                ->update(['client_id' => $primaryClientId]);

            // Удаляем дубликаты
            DB::table('clients')
                ->whereIn('id', $clients->pluck('id'))
                ->where('id', '!=', $primaryClientId)
                ->delete();

            return response()->json(['message' => 'Clients merged successfully']);
        }

        return response()->json(['message' => 'No duplicates found']);
    }
}
