<?php

namespace App\Core\Utility;


class Operator
{
    public static function evaluate($value, $operator, $cond)
    {


        switch ($operator) {
            case '=':
                $eval = $value == $cond;
                break;

            case '===':
                $eval = $value === $cond;
                break;

            case '<>':
                $eval = $value <> $cond;
                break;

            case '!=':
                $eval = $value != $cond;
                break;

            case '!==':
                $eval = $value !== $cond;
                break;

            case '>':
                $eval = $value > $cond;
                break;

            case '<':
                $eval = $value < $cond;
                break;

            case '>=':
                $eval = $value >= $cond;
                break;

            case '<=':
                $eval = $value <= $cond;
                break;

            case '<=':
                $eval = $value <= $cond;
                break;

            case '<=>':
                $eval = $value <=> $cond;
                break;

            default:
                break;
        }

        Logger::log($value . " " . $operator . " " . $cond);
        Logger::log($eval);

        return $eval;
    }
}
