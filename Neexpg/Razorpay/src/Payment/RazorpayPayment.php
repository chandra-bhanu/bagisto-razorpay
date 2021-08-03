<?php

namespace Neexpg\Razorpay\Payment;

use Webkul\Payment\Payment\Payment;

class RazorpayPayment extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'razorpay';

    public function getRedirectUrl()
    {
         return route('razorpay.payment.redirect');
    }
}