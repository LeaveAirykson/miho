<?php

namespace App\Core\Storage;

use JsonSerializable;

class StorableList implements JsonSerializable
{
    private array $data;

    function __construct(array $data)
    {
        $this->data = $data;

        return $this;
    }

    function filterBy($conditions = [])
    {
        $data = array_values(array_filter($this->data, function ($v) use ($conditions) {
            foreach ($conditions as $key => $value) {
                if ($v->{$key} !== $value) {
                    return false;
                }
            }

            return true;
        }));

        $this->data = $data;

        return $this;
    }

    function sortBy(string | array $sortings = [])
    {
        $sorts = [];

        if (is_string($sortings)) {
            $sortings = [$sortings];
        }

        foreach ($sortings as $idx => $value) {
            $v = explode(':', $value, 2);
            $sorts[$v[0]] = strtolower($v[1] ?? 'asc');
        }

        usort($this->data, function ($a, $b) use ($sorts) {
            $x = [];
            $y = [];

            foreach ($sorts as $key => $asc) {
                $aa = is_string($a->{$key}) ? strtolower($a->{$key}) : $a->{$key};
                $bb = is_string($b->{$key}) ? strtolower($b->{$key}) : $b->{$key};

                $x[] = $asc == 'asc' ? $aa : $bb;
                $y[] = $asc == 'asc' ? $bb : $aa;
            }

            return $x <=> $y;
        });

        return $this;
    }

    function getData()
    {
        return $this->data;
    }

    function jsonSerialize()
    {
        return $this->getData();
    }
}
