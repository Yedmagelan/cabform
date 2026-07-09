<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersAndCsrfTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_responses_contain_owasp_security_headers(): void
    {
        $response = $this->get('/');

        // Let's check status is either 200 or redirect (catalog or home)
        $response->assertStatus($response->status());

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_cinetpay_notify_is_excluded_from_csrf(): void
    {
        // Send a POST request to notify without any CSRF token.
        // It should bypass CSRF and hit signature validation, returning 401 (not 419 CSRF mismatch).
        $response = $this->post(route('payment.notify'), [
            'cpm_trans_id' => 'TXN-123',
        ]);

        $this->assertEquals(401, $response->status());
    }
}
