<?php

namespace src\Domain\Interfaces\Clients;

use src\Application\DTO\Clients\UpsertClientDTO;

interface IClientsRepository
{
    public function getClients($page, $limit);
    public function createOrUpdateClient(UpsertClientDTO $clientDTO);
    public function createClient(UpsertClientDTO $clientDTO);

    public function getClientByPhoneOrUsername(?string $phone, ?string $username);
    public function getClientById($id);

    public function updateClientById(UpsertClientDTO $clientDTO, int $id);

}
