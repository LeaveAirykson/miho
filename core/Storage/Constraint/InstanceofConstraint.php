<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class InstanceofConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        if (!$value instanceof $constraintValue) {
            throw new InvalidFieldError("Invalid instance class used for: $value");
        }
    }
}
