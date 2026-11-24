<?php

namespace App\Library;

/**
 * Local replacement for laracon21/colorcodeconverter (PHP 7-only, and which
 * silently POSTed the site domain to a hardcoded IP). That phone-home has been
 * removed; this is a clean hex -> rgb/rgba converter with identical output.
 */
class Colorcodeconverter
{
    public static function convertHexToRgba($color, $opacity = false)
    {
        $default = 'rgb(230,46,4)';

        if (empty($color)) {
            return $default;
        }

        if ($color[0] === '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) === 6) {
            $hex = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
        } elseif (strlen($color) === 3) {
            $hex = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
        } else {
            return $default;
        }

        $rgb = array_map('hexdec', $hex);

        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            return 'rgba(' . implode(',', $rgb) . ',' . $opacity . ')';
        }

        return 'rgb(' . implode(',', $rgb) . ')';
    }
}
