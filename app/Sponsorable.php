<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sponsorable extends Model
{
    public static function findOrFailBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }
}
