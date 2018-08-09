<?php

namespace App\Http\Controllers;

use App\Sponsorable;
use Illuminate\Http\Request;

class SponsorableSponsorshipsController extends Controller
{
    public function new($slug)
    {
        $sponsorable = Sponsorable::findOrFailBySlug($slug);
        $sponsorableSlots = $sponsorable->slots;

        return view('sponsorable-sponsorships.new', [
            'sponsorable' => $sponsorable,
            'sponsorableSlots' => $sponsorableSlots,
        ]);
    }
}
