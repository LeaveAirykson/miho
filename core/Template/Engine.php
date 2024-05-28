<?php

namespace App\Core\Template;

use App\Core\Utility\StringHelper;

class Engine
{
    public function render(string $template, array $data = [], array $options = []): string
    {
        $path = $this->resolveTemplatePath($template);

        if (!file_exists($path)) {
            throw new TemplateNotFoundException($template);
        }

        $output = (new Template($this, $path, $data))->render();

        if ($options['minify'] ?? null) {
            $output = StringHelper::minify($output);
        }

        return $output;
    }

    public function resolveTemplatePath($template)
    {
        return TEMPLATES_PATH . '/' . str_replace('.php', '', $template) . '.php';
    }

    public function renderSegments(array $segments = null)
    {
        if (!is_array($segments)) {
            return;
        }

        foreach ($segments as $segment) {
            dump($segment);
            die();
        }
    }
}
