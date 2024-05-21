<?php

namespace App\Core\Request;

use Iterator;

class HttpHeader implements Iterator
{
    private $headers;
    private $position = 0;

    function __construct($headers = [])
    {
        $this->position = 0;
        $this->headers = $headers;
        return $this;
    }

    function set($header)
    {
        array_push($this->headers, $header);
        $this->headers = array_unique($this->headers);
        return $this;
    }

    function rewind(): void
    {
        $this->position = 0;
    }

    function current()
    {
        return $this->headers[$this->position];
    }

    function key()
    {
        return $this->position;
    }

    function next(): void
    {
        ++$this->position;
    }

    function valid(): bool
    {
        return isset($this->headers[$this->position]);
    }
}
