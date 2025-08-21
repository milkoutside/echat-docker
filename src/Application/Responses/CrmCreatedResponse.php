<?php

namespace src\Application\Responses;

class CrmCreatedResponse
{
    public $id;
    public $status;

    public $module;

    public $error;

    public function __construct($id, $module, $error)
    {
        $this->module = $module;
        if (!empty($id)) {
            $this->id = $id;
            $this->status = "success";
        } else {
            $this->id = null;
            $this->status = "error";
            $this->error = $error;

        }
    }
}
