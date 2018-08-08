<?php

namespace App\Http\Controllers;

use App\Sponsorable;
use Illuminate\Http\Request;

class SponsorableSponsorshipsController extends Controller
{
    public function new()
    {
        $sponsorable = Sponsorable::findOrFailBySlug($slug);
        $sponsorableSlots = $sponsorable->slots;

        return view('sponsorable-sponsorships.new', [
            'sponsorableSlots' => $sponsorableSlots,
        ]);
    }
}
