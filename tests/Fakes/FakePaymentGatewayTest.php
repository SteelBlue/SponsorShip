<?php

namespace Tests\Fakes;

use Tests\TestCase;
use Tests\FakePaymentGateway;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function retrieving_charges()
    {
        $paymentGateway = new FakePaymentGateway;

        // Process (3) separate charges via the paymentGateway.
        $paymentGateway->charge('john@example.com', 25000, $paymentGateway->validTestToken(), 'Example description A');
        $paymentGateway->charge('jane@example.org', 5000, $paymentGateway->validTestToken(), 'Example description B');
        $paymentGateway->charge('jeff@example.net', 7500, $paymentGateway->validTestToken(), 'Example description C');

        $charges = $paymentGateway->charges();

        // Assert there was a total of (3) charges.
        $this->assertCount(3, $charges);

        // Assert the first charge equals.
        $this->assertEquals('john@example.com', $charges[0]->email());
        $this->assertEquals(25000, $charges[0]->amount());
        $this->assertEquals('Example description A', $charges[0]->description());

        // Assert the second charge equals.
        $this->assertEquals('jane@example.org', $charges[1]->email());
        $this->assertEquals(5000, $charges[1]->amount());
        $this->assertEquals('Example description B', $charges[1]->description());

        // Assert the third charge equals.
        $this->assertEquals('jeff@example.net', $charges[2]->email());
        $this->assertEquals(7500, $charges[2]->amount());
        $this->assertEquals('Example description C', $charges[2]->description());
    }
}
