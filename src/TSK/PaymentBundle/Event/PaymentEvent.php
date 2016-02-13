<?php

namespace TSK\PaymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use TSK\PaymentBundle\Entity\Payment;

class PaymentEvent extends Event {
    private $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

 
    /**
     * Get payment.
     *
     * @return payment.
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
