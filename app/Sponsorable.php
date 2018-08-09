<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sponsorable extends Model
{
    public static function findOrFailBySlug($slug)
    {
        return self::where('slug', $slug)->firstOrFail();
    }

    public function slots()
    {
        return $this->hasMany(SponsorableSlot::class);
    }
}
