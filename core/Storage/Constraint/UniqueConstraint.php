<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class UniqueConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {
        $exists = $model::getByPropertyValue($field, $value, true);

        if ($exists) {
            throw new InvalidFieldError("Field $field with value: $value is not unique!");
        }
    }
}
