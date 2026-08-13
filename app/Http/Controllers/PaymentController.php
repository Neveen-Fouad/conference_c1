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
        ]);

        $result = $this->paymentService
            ->createBookingPayment(
                $this->clientId($request),
                $data['booking_id']
            );

        return response()->json([
            'payment' => $result['payment'],
            'checkout_url' => $result['checkout_url'],
            'message' => 'Payment created successfully',
        ], 201);
    }

    public function show(Request $request, int $paymentId)
    {
        $payment = $this->paymentService
            ->getClientPayment($paymentId, $this->clientId($request));

        return response()->json([
            'data' => $payment,
        ]);
    }

    public function clientPayments(Request $request, int $clientId)
    {
        abort_unless($clientId === $this->clientId($request), 403, 'Unauthorized action.');

        $payments = $this->paymentService
            ->getClientPayments($clientId);

        return response()->json([
            'data' => $payments,
        ]);
    }

    private function clientId(Request $request): int
    {
        $clientId = $request->user()?->client?->id;
        abort_if($clientId === null, 403, 'A client profile is required.');

        return $clientId;
    }
}
