<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\ConstraintResolver;
use App\Core\Storage\InvalidFieldError;
use App\Core\Storage\Storable;

class ArrayConstraint implements ConstraintInterface
{
    public function test(string $field, $value, $constraintValue, Storable $model): void
    {

        if (gettype($value) !== 'array') {
            $t = gettype($value);
            throw new InvalidFieldError("$field must be of type array! is: $t");
        }

        if (is_array($constraintValue)) {
            foreach ($constraintValue as $constraint => $val) {
                $constraintClass = ConstraintResolver::resolve($constraint);

                if (!$constraintClass) {
                    continue;
                }

                foreach ($value as $vval) {
                    $constraintClass->test($field, $vval, $val, $model);
                }
            }
        }
    }
}
