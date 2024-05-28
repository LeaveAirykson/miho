<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class RequiredConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        if (empty($value)) {
            throw new InvalidFieldError("Field $field is mandatory!");
        }
    }
}
