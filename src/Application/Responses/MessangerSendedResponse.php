<?php

namespace src\Application\Responses;

class MessangerSendedResponse
{
    public $id;

    public $status;
    public $client;


    public $error;

    public function __construct($id,$client,$status, $error)
    {
        $this->client = $client;
        $this->id = $id;
        if (strtolower($status) == "success") {
            $this->status = "success";
        } else {
            $this->status = "error";
            $this->error = $error;
        }
    }
}
