<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class RegexConstraintConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        preg_match($constraintValue, $value, $matches);

        if (!$matches) {
            throw new InvalidFieldError("Input $value does not match Regex: $constraintValue");
        }
    }
}
