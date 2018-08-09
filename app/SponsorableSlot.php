<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SponsorableSlot extends Model
{
    public function scopePurchasable($query)
    {
        return $query->whereNull('purchase_id')->where('publish_date', '>', now());
    }
}
