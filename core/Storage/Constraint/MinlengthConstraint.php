<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class MinlengthConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        if (strlen($value) < $constraintValue) {
            throw new InvalidFieldError("Invalid length for field $field (minlength: $constraintValue) !");
        }
    }
}
