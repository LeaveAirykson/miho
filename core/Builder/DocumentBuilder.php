<?php

namespace App\Core\Builder;

use App\Core\Template\Engine;
use App\Model\Document;

class DocumentBuilder
{
    public Engine $engine;
    public Document $document;

    function __construct()
    {
        $this->engine = new Engine();
    }

    function parse(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    function build()
    {
        $data = $this->document->getData();

        $rendered = $this->engine->render(
            $data['template'],
            $data,
            ['minify' => true]
        );

        file_put_contents(PUBLIC_PATH . '/' . $data['slug'] . '.html', $rendered);
    }
}
