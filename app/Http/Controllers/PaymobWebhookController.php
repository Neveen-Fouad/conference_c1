<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Bookings;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymobWebhookController extends Controller
{
    protected $paymentRepository;
    protected $notificationService;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        NotificationService $notificationService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->notificationService = $notificationService;
    }

    public function handle(Request $request)
    {
        $data = $request->input('obj');

        if (!$data) {
            return response()->json([
                'message' => 'Invalid data',
            ], 400);
        }

        if (!$this->checkHmac(
            $data,
            $request->query('hmac')
        )) {
            return response()->json([
                'message' => 'Invalid HMAC',
            ], 401);
        }

        $reference = data_get(
            $data,
            'order.merchant_order_id'
        );

        $payment = $this->paymentRepository
            ->findByPaymentReference($reference);

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found',
            ], 404);
        }

        if ($payment->status === 'paid') {
            return response()->json([
                'message' => 'Payment already processed',
            ]);
        }

        $status = $this->getPaymentStatus($data);

        $payment = $this->paymentRepository->update(
            $payment->id,
            [
                'status' => $status,

                'gateway_transaction_id' =>
                    $data['id'] ?? null,

                'payment_method' => data_get(
                    $data,
                    'source_data.sub_type'
                ),

                'gateway_response' => $request->all(),

                'failure_reason' => $status === 'failed'
                    ? 'Payment failed'
                    : null,

                'paid_at' => $status === 'paid'
                    ? now()
                    : null,
            ]
        );

        if ($status === 'paid') {
            $this->handleSuccessfulPayment($payment);
        }

        if ($status === 'failed') {
            $this->notificationService
                ->sendPaymentFailedNotification(
                    $payment->client
                );
        }

        return response()->json([
            'message' => 'Webhook received successfully',
        ]);
    }

    private function getPaymentStatus($data)
    {
        if ($data['pending'] ?? false) {
            return 'pending';
        }

        if ($data['success'] ?? false) {
            return 'paid';
        }

        return 'failed';
    }

    private function handleSuccessfulPayment($payment)
    {
        if ($payment->booking_id) {
            Bookings::where(
                'id',
                $payment->booking_id
            )->update([
                'status' => 'confirmed',
            ]);
        }

        $this->notificationService
            ->sendPaymentSuccessNotification(
                $payment->client
            );
    }

    private function checkHmac(
        $data,
        $receivedHmac
    ) {
        if (!$receivedHmac) {
            return false;
        }

        $fields = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order.id',
            'owner',
            'pending',
            'source_data.pan',
            'source_data.sub_type',
            'source_data.type',
            'success',
        ];

        $text = '';

        foreach ($fields as $field) {
            $value = data_get($data, $field);

            if (is_bool($value)) {
                $value = $value
                    ? 'true'
                    : 'false';
            }

            $text .= $value;
        }

        $calculatedHmac = hash_hmac(
            'sha512',
            $text,
            config('services.paymob.hmac_secret')
        );

        return hash_equals(
            $calculatedHmac,
            $receivedHmac
        );
    }
}
