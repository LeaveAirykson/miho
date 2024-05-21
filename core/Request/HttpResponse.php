<?php

namespace App\Core\Request;

class HttpResponse
{

    public $body = null;
    public $header;
    public $code = 200;

    public function __construct()
    {
        $this->header = new HttpHeader();
        return $this;
    }

    public function status($status)
    {
        $this->code = $status;
        return $this;
    }

    public function print()
    {
        echo $this->body;
    }

    public function json($body)
    {
        $res = [
            'status' => $this->code,
            'ok' => ($this->code === 200) ? true : false,
            'data' => $body
        ];

        $this->header->set('Content-Type: application/json');

        $this->body = json_encode($res);

        return $this->send();
    }

    public function send($body = null)
    {
        // save body
        $this->body = is_null($body) ? $this->body : $body;

        // Clear output buffer
        if (ob_get_contents()) {
            ob_clean();
        };

        // start output buffer
        ob_start();

        // set response code
        http_response_code($this->code);

        // set all additional headers
        foreach ($this->header as $header) {
            header($header, true);
        }

        // finally print body
        $this->print();
        exit();
    }
}
