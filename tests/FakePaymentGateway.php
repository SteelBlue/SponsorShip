<?php

namespace Tests;

use App\Charge;
use Illuminate\Support\Collection;

class FakePaymentGateway
{
    private $charges;

    function __construct()
    {
        $this->charges = new Collection;
    }

    public function validTestToken()
    {
        return 'valid_test_token';
    }

    public function charge($email, $amount, $token, $description)
    {
        $this->charges->push(new Charge($email, $amount, $description));
    }

    public function charges()
    {
        return $this->charges;
    }
}
