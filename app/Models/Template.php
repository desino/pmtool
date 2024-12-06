<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function hexToRgb($hex)
    {
        // Remove "#" if present
        $hex = str_replace('#', '', $hex);

        // Handle shorthand hex codes like "#fff"
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // Parse the color values
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return [
            'r' => $r,
            'g' => $g,
            'b' => $b
        ];
    }
}
