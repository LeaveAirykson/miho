<?php

namespace App\Core\Template;

use ReflectionClass;

class Segment
{
    public array $data;
    public string $template;
    public Engine $engine;

    function __construct(array $data = [])
    {
        $this->engine = new Engine();
        $this->setTemplate($this->resolveTemplate());
        $this->setData($data);

        return $this;
    }

    function resolveTemplate()
    {
        $shortname = (new ReflectionClass($this))->getShortName();
        return 'segment/' . strtolower($shortname) . '.php';
    }

    function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    function getData()
    {
        $data = $this->data;
        $data['content'] = htmlspecialchars_decode($data['content']);

        return $data;
    }

    function setTemplate(string $template)
    {
        $this->template = $template;
        return $this;
    }

    function getTemplate()
    {
        return $this->template;
    }

    function render()
    {
        $this->preRender();
        return $this->engine->render($this->getTemplate(), $this->getData());
    }

    function preRender()
    {
    }
}
