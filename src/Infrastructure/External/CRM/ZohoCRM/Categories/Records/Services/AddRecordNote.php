<?php

namespace src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Services;

use src\Infrastructure\External\CRM\ZohoCRM\Categories\Records\Interfaces\SingleRecord;
use src\Infrastructure\External\CRM\ZohoCRM\Request\ApiRequest;

class AddRecordNote extends ApiRequest implements SingleRecord
{
    private string $module;

    private $id;
    private $data;
    private $content;
    private $subject;

    function setModule(string $module)
    {
        $this->module = $module;
        return $this;
    }

    function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
    function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function apiRequest()
    {
        return $this->setHeaders()->setBody([
            "data" => [[
                "Note_Title" => $this->subject,
                "Note_Content" => $this->content,
                "Parent_Id" => $this->id,
                "se_module" => $this->module,
            ]],
        ])->request('POST',"{$this->module}/{$this->id}/Notes");
    }
}
