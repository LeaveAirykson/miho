<?php

namespace App\Core\Template;

use Exception;

final class TemplateNotFoundException extends Exception
{
    public function __construct(string $template)
    {
        parent::__construct(sprintf('Unable to find template %s', $template));
    }
}
