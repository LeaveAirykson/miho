<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class TypeConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        if (gettype($value) !== $constraintValue) {
            $t = gettype($value);
            throw new InvalidFieldError("Invalid data type $field. is: $t needs: $constraintValue!");
        }
    }
}
