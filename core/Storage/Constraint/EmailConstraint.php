<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class EmailConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidFieldError("Invalid email address: $value");
        }
    }
}
