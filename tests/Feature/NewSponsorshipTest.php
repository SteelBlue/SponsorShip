<?php

namespace Feature;

use Tests\TestCase;
use App\Sponsorable;
use App\SponsorableSlot;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class NewSponsorshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function viewing_the_new_sponsorship_page()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $sponsorableSlots = new EloquentCollection([
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
        ]);

        $response = $this->withoutExceptionHandling()->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertCount(3, $response->data('sponsorableSlots'));
        $sponsorableSlots->assertEquals($response->data('sponsorableSlots'));
    }
}
