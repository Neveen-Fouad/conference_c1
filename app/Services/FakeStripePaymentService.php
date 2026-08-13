<?php

namespace App\Services;

use Illuminate\Support\Str;

class FakeStripePaymentService
{
    public function createCheckoutSession()
    {
        return [
            'session_id' => 'cs_test_'.Str::random(24),
            'payment_intent_id' => 'pi_test_'.Str::random(24),
        ];

    }
}
