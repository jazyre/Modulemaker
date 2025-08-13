<?php

namespace App\Interfaces;

interface PaymentInterface
{
    /**
     * Initiate a payment request.
     *
     * @param  array  $data
     * @return mixed
     */
    public function pay(array $data);

    /**
     * Verify a payment.
     *
     * @param  array  $data
     * @return mixed
     */
    public function verify(array $data);
}
