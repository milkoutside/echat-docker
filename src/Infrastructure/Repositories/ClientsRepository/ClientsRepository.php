<?php

namespace src\Infrastructure\Repositories\ClientsRepository;

use Illuminate\Support\Facades\DB;
use src\Application\DTO\Clients\UpsertClientDTO;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Infrastructure\DB\Clients\DBClients;
use src\Infrastructure\Mappers\Clients\ClientsMapper;

class ClientsRepository implements IClientsRepository
{
    public function getClients($page, $limit)
    {
        return DBClients::paginate($limit, ['*'], 'page', $page);
    }
    public function getClientByPhonesOrUsername(?array $phones, ?string $username)
    {
        if (empty($phones) && empty($username)) {
            return null;
        }

        $query = DB::table('clients');

        $hasPhones = !empty($phones);
        $hasUsername = !empty($username);

        if ($hasPhones) {
            $query->where(function($q) use ($phones) {
                foreach ($phones as $service => $phone) {
                    if (!empty($phone)) {
                        $q->orWhereJsonContains("phones->{$service}", $phone);
                    }
                }
            });
        }

        if ($hasUsername) {
            if ($hasPhones) {
                $query->orWhere('username', $username);
            } else {
                $query->where('username', $username);
            }
        }

        return $query->first();
    }
    public function getClientByPhoneOrUsername(?string $phone, ?string $username)
    {
        if (empty($phone) && empty($username)) {
            return null;
        }

        if (!empty($phone)) {
            $clientByPhone = DB::table('clients')
                ->whereJsonContains('phones->main', $phone)
                ->orWhereJsonContains('phones->viber', $phone)
                ->orWhereJsonContains('phones->telegram', $phone)
                ->orWhereJsonContains('phones->whatsapp', $phone)
                ->first();

            if ($clientByPhone) {
                return $clientByPhone;
            }
        }

        if (!empty($username)) {
            return DB::table('clients')->where('username', $username)->first();
        }

        return null;
    }

    public function getClientById($id)
    {
        return DB::table('clients')->where('id',$id)->first();
    }

    public function createOrUpdateClient(UpsertClientDTO $clientDTO)
    {
        if (empty($clientDTO->phones) && empty($clientDTO->username)) {

            return null;
        }

        $query = DB::table('clients');

        if (!empty($clientDTO->phones)) {
            foreach ($clientDTO->phones as $key => $phone) {
                $query->orWhereJsonContains("phones->{$key}", $phone);
            }
        }

        if (!empty($clientDTO->username)) {
            $query->orWhere('username', $clientDTO->username);
        }

        $client = $query->first();

        $data = [
            'phones' => json_encode($clientDTO->phones ?? []),
            'username' => $clientDTO->username,
        ];

        if ($client) {
            DB::table('clients')
                ->where('id', $client->id)
                ->update($data);
            $clientId = $client->id;
        } else {
            $clientId = DB::table('clients')->insertGetId($data);
        }

// Вернём объект клиента (если нужно, можно привести к stdClass или сделать кастомную обёртку)
        return DB::table('clients')->where('id', $clientId)->first();
    }

    public function createClient(UpsertClientDTO $clientDTO)
    {
        // Убедитесь, что phones всегда сохраняется как JSON
        $client = new DBClients();
        $client->phones = $clientDTO->phones ? json_encode($clientDTO->phones) : null;
        $client->username = $clientDTO->username;
        $client->save();

        return ClientsMapper::toDomain($client);
    }

    public function updateClientById(UpsertClientDTO $clientDTO,  $id)
    {
        $client = DB::table('clients')->where('id', $id)->first();
        if (!$client) {
            return null;
        }

        if (empty($clientDTO->phones) && empty($clientDTO->username)) {
            return null;
        }

        DB::table('clients')
            ->where('id', $id)
            ->update([
                'phones' => isset($clientDTO->phones) ? json_encode($clientDTO->phones) : $client->phones,
                'username' => $clientDTO->username ?? $client->username,
            ]);

        return DB::table('clients')->where('id', $id)->first();
    }
}
