<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use App\Sponsorable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SponsorableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function finding_a_sponsorable_by_slug()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $foundSponsorable = Sponsorable::findOrFailBySlug('full-stack-radio');

        $this->assertTrue($foundSponsorable->is($sponsorable));
    }

    /** @test */
    public function an_exception_is_thrown_if_a_sponsorable_cannot_be_found_by_the_slug()
    {
        try {
            Sponsorable::findOrFailBySlug('slug-that-does-not-exist');
            $this->fail("Exception was not thrown even though Sponsorable could not be found by slug.");
        } catch (Exception $e) {
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }
    }
}
