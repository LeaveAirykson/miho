<?php

namespace App\Core\Template;

class Template
{
    public function __construct(
        protected Engine $engine,
        protected string $path,
        protected array $data = []
    ) {
    }

    public function escape(string $str)
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function include(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }

    public function render(): string
    {
        ob_start();
        extract($this->data);
        include($this->path);
        return ob_get_clean();
    }

    public function renderSegments()
    {
        return $this->engine->renderSegments($this->data['segments'] ?? null);
    }

    public function renderPartial(string $partial, array $data = []): string
    {
        return '';
    }

    public function renderHeadAssets(): string
    {
        return '';
    }

    public function renderBodyAssets(): string
    {
        return '';
    }
}
