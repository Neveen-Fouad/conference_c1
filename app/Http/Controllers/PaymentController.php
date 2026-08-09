<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(
        PaymentService $paymentService
    ) {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $result = $this->paymentService
            ->createBookingPayment(
                $data['client_id'],
                $data['booking_id']
            );

        return response()->json([
            'payment' => $result['payment'],
            'checkout_url' => $result['checkout_url'],
            'message' => 'Payment created successfully',
        ], 201);
    }

    public function show($paymentId)
    {
        $payment = $this->paymentService
            ->getPayment($paymentId);

        return response()->json([
            'data' => $payment,
        ]);
    }

    public function clientPayments($clientId)
    {
        $payments = $this->paymentService
            ->getClientPayments($clientId);

        return response()->json([
            'data' => $payments,
        ]);
    }
}
