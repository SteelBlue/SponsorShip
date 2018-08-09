<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SponsorableSlot extends Model
{
    protected $guarded = [];

    public function scopeSponsorable($query)
    {
        return $query->whereNull('sponsorship_id')->where('publish_date', '>', now());
    }
}
