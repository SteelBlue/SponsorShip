<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Sponsorable;
use App\Sponsorship;
use App\PaymentGateway;
use App\SponsorableSlot;
use Tests\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PurchaseSponsorshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function purchasing_available_sponsorship_slots()
    {
        // Binding to IoC/Service Container for PaymentGateway
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);

        $slotA = factory(SponsorableSlot::class)->create(['price' => 50000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB = factory(SponsorableSlot::class)->create(['price' => 30000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]);
        $slotC = factory(SponsorableSlot::class)->create(['price' => 25000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3)]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'DigitalTechnoSoft, Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotC->getKey(),
            ],
        ]);

        // Assert return of Status 201: CREATED.
        $response->assertStatus(201);

        $this->assertEquals(1, Sponsorship::count());
        $sponsorship = Sponsorship::first();

        $this->assertEquals('john@example.com', $sponsorship->email);
        $this->assertEquals('DigitalTechnoSoft, Inc.', $sponsorship->company_name);
        $this->assertEquals(75000, $sponsorship->amount);

        $this->assertEquals($sponsorship->getKey(), $slotA->fresh()->sponsorship_id);
        $this->assertEquals($sponsorship->getKey(), $slotC->fresh()->sponsorship_id);

        $this->assertNull($slotB->fresh()->sponsorship_id);

        // Assert there was (1) charge.
        $this->assertCount(1, $paymentGateway->charges());

        // Assert the charge-amount equals $750.
        $charge = $paymentGateway->charges()->first();
        $this->assertEquals('john@example.com', $charge->email());
        $this->assertEquals(75000, $charge->amount());
        $this->assertEquals('Full Stack Radio sponsorship', $charge->description());
    }

    /** @test */
    public function sponsorship_is_not_created_if_payment_token_cannot_be_charged()
    {
        // Binding to IoC/Service Container for PaymentGateway
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slot = factory(SponsorableSlot::class)->create(['price' => 50000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'DigitalTechnoSoft, Inc.',
            'payment_token' => 'not-a-valid-token',
            'sponsorable_slots' => [
                $slot->getKey(),
            ],
        ]);

        // Assert return of Status 422: UNPROCESSABLE ENTITY.
        $response->assertStatus(422);

        $this->assertEquals(0, Sponsorship::count());

        $this->assertNull($slot->fresh()->sponsorship_id);

        // Assert there was no charges made.
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function company_name_is_required()
    {
        // Binding to IoC/Service Container for PaymentGateway
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slot = factory(SponsorableSlot::class)->create(['price' => 50000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => '',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ],
        ]);

        // Assert return of Status 422: UNPROCESSABLE ENTITY.
        $response->assertStatus(422);

        // Assert Json validation errors for the company_name.
        $response->assertJsonValidationErrors('company_name');

        $this->assertEquals(0, Sponsorship::count());

        $this->assertNull($slot->fresh()->sponsorship_id);

        // Assert there was no charges made.
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function email_is_required()
    {
        // Binding to IoC/Service Container for PaymentGateway
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slot = factory(SponsorableSlot::class)->create(['price' => 50000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => '',
            'company_name' => 'DigitalTechnoSoft, Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ],
        ]);

        // Assert return of Status 422: UNPROCESSABLE ENTITY.
        $response->assertStatus(422);

        // Assert Json validation errors for the email.
        $response->assertJsonValidationErrors('email');

        $this->assertEquals(0, Sponsorship::count());

        $this->assertNull($slot->fresh()->sponsorship_id);

        // Assert there was no charges made.
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function email_must_look_like_an_email()
    {
        // Binding to IoC/Service Container for PaymentGateway
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slot = factory(SponsorableSlot::class)->create(['price' => 50000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'not-a-valid-email',
            'company_name' => 'DigitalTechnoSoft, Inc.',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ],
        ]);

        // Assert return of Status 422: UNPROCESSABLE ENTITY.
        $response->assertStatus(422);

        // Assert Json validation errors for the email.
        $response->assertJsonValidationErrors('email');

        $this->assertEquals(0, Sponsorship::count());

        $this->assertNull($slot->fresh()->sponsorship_id);

        // Assert there was no charges made.
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function payment_token_is_required()
    {
        // Binding to IoC/Service Container for PaymentGateway
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway);

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slot = factory(SponsorableSlot::class)->create(['price' => 50000,'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'DigitalTechnoSoft, Inc.',
            'payment_token' => null,
            'sponsorable_slots' => [
                $slot->getKey(),
            ],
        ]);

        // Assert return of Status 422: UNPROCESSABLE ENTITY.
        $response->assertStatus(422);

        // Assert Json validation errors for the payment_token.
        $response->assertJsonValidationErrors('payment_token');

        $this->assertEquals(0, Sponsorship::count());

        $this->assertNull($slot->fresh()->sponsorship_id);

        // Assert there was no charges made.
        $this->assertCount(0, $paymentGateway->charges());
    }
}
