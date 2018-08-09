<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Sponsorable;
use App\SponsorableSlot;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PurchaseSponsorshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function purchasing_available_sponsorship_slots()
    {
        // $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slotA = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable,]);
        $slotB = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable,]);
        $slotC = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable,]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();

        $this->assertEquals(1, Purchase::count());
        $purchase = Purchase::first();

        $this->assertEquals($purchase->getKey(), $slotA->fresh()->purchase_id);
        $this->assertEquals($purchase->getKey(), $slotC->fresh()->purchase_id);

        $this->assertNull($slotB->fresh()->purchase_id);
    }
}
