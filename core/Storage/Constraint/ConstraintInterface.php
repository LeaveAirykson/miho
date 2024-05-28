<?php

namespace App\Core\Storage\Constraint;

use App\Core\Storage\Storable;

interface ConstraintInterface
{
    function test(
        string $field,
        $value,
        $constraintValue,
        Storable $model
    ): void;
}
