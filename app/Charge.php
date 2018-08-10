<?php

namespace App;

class Charge
{
    private $email;
    private $amount;
    private $description;

    function __construct($email, $amount, $description)
    {
        $this->email = $email;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function email()
    {
        return $this->email;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function description()
    {
        return $this->description;
    }
}
