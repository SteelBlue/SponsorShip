<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewSponsorshipTest extends TestCase
{
    /** @test */
    function viewing_the_new_sponsorship_page()
    {
        $response = $this->withoutExceptionHandling()
                         ->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertCount(3, $response->data('sponsorableSlots'));
        $sponsorableSlots->assertEquals($response->data('sponsorableSlots'));
    }
}
