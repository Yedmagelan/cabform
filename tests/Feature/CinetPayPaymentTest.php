<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Services\CinetPayService;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CinetPayPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Course $course;
    private string $secretKey = 'test_secret_key';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.cinetpay.secret_key' => $this->secretKey]);
        config(['services.cinetpay.api_key' => 'test_api_key']);
        config(['services.cinetpay.site_id' => '123456']);

        $instructor = User::factory()->create();
        $this->user = User::factory()->create();
        $this->course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'CinetPay Course',
            'slug' => 'cinetpay-course',
            'price' => 1000.00,
        ]);
    }

    public function test_payment_initiation_creates_order_and_payment_records(): void
    {
        $this->actingAs($this->user);

        // Mock CinetPay API call
        Http::fake([
            'https://api-checkout.cinetpay.com/v2/payment' => Http::response([
                'code' => '201',
                'message' => 'CREATED',
                'data' => [
                    'payment_url' => 'https://checkout.cinetpay.com/pay/token',
                    'payment_token' => 'token123',
                ]
            ], 200)
        ]);

        $response = $this->post(route('payment.initiate', $this->course->slug));

        $response->assertRedirect('https://checkout.cinetpay.com/pay/token');

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total' => 1000.00,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'amount' => 1000.00,
            'status' => 'pending',
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson(route('payment.notify'), [
            'cpm_trans_id' => 'TXN-123',
        ], [
            'x-token' => 'invalid_signature_token',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Invalid signature']);
    }

    public function test_webhook_processes_valid_signature_and_completes_payment(): void
    {
        // Create pending order and payment
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-123',
            'total' => 1000.00,
            'status' => 'pending',
        ]);

        $order->items()->create([
            'course_id' => $this->course->id,
            'quantity' => 1,
            'price' => 1000.00,
            'total' => 1000.00,
        ]);

        $transactionId = 'TXN-123';
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'amount' => 1000.00,
            'status' => 'pending',
        ]);

        // Mock verification response
        Http::fake([
            'https://api-checkout.cinetpay.com/v2/payment/check' => Http::response([
                'code' => '00',
                'data' => [
                    'status' => 'ACCEPTED',
                    'amount' => '1000',
                    'currency' => 'XOF',
                    'payment_method' => 'mobile_money',
                ]
            ], 200)
        ]);

        $bodyData = [
            'cpm_trans_id' => $transactionId,
        ];
        $rawBody = json_encode($bodyData);
        $signature = hash_hmac('sha256', $rawBody, $this->secretKey);

        $response = $this->postJson(route('payment.notify'), $bodyData, [
            'x-token' => $signature,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->status);

        // Verify learner is enrolled
        $this->assertTrue($this->user->enrolledIn($this->course));
    }

    public function test_webhook_idempotency(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-123',
            'total' => 1000.00,
            'status' => 'paid',
        ]);

        $transactionId = 'TXN-123';
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        $bodyData = [
            'cpm_trans_id' => $transactionId,
        ];
        $rawBody = json_encode($bodyData);
        $signature = hash_hmac('sha256', $rawBody, $this->secretKey);

        $response = $this->postJson(route('payment.notify'), $bodyData, [
            'x-token' => $signature,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'message' => 'Payment already processed']);
    }
}
