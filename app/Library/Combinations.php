<?php

namespace App\Library;

/**
 * Local replacement for laracon21/combinations (PHP 7-only, dropped during the
 * Laravel 8->13 upgrade). Behaviour is identical to the original makeCombinations().
 */
class Combinations
{
    /**
     * Cartesian product of the given option arrays, keyed by property.
     *
     * @param  array  $arrays  e.g. ['color' => ['red','blue'], 'size' => ['s','m']]
     * @return array
     */
    public static function makeCombinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}
