<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBackOfficeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        $adminRole = Role::create(['name' => 'administrateur']);
        $permission = Permission::create(['name' => 'reports.view']);
        $adminRole->givePermissionTo($permission);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrateur');
    }

    public function test_admin_can_access_reports_and_see_statistics(): void
    {
        $this->actingAs($this->admin);

        // Create dummy users, courses and orders
        User::factory()->count(3)->create();
        
        $course = Course::create([
            'instructor_id' => $this->admin->id,
            'title' => 'Admin Course',
            'slug' => 'admin-course',
            'price' => 50,
        ]);

        $order = Order::create([
            'user_id' => $this->admin->id,
            'order_number' => 'ORD-11111',
            'total' => 150.00,
            'status' => 'paid',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $this->admin->id,
            'amount' => 150.00,
            'method' => 'credit_card',
            'status' => 'completed',
            'transaction_id' => 'TXN-111',
        ]);

        $response = $this->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('150,00 €');
    }

    public function test_admin_can_export_users_csv(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.reports.export'), [
            'type' => 'users'
        ]);

        $response->assertStatus(200);
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment; filename=export_users_', $contentDisposition);
        $this->assertStringEndsWith('.csv', $contentDisposition);
        
        // Retrieve stream content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('ID', $content);
        $this->assertStringContainsString('Prénom', $content);
        $this->assertStringContainsString($this->admin->email, $content);
    }

    public function test_admin_can_export_financial_csv(): void
    {
        $this->actingAs($this->admin);

        $order = Order::create([
            'user_id' => $this->admin->id,
            'order_number' => 'ORD-22222',
            'total' => 200.00,
            'status' => 'paid',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $this->admin->id,
            'amount' => 200.00,
            'method' => 'mobile_money',
            'status' => 'completed',
            'transaction_id' => 'TXN-222',
        ]);

        $response = $this->post(route('admin.reports.export'), [
            'type' => 'financial'
        ]);

        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Utilisateur', $content);
        $this->assertStringContainsString('mobile_money', $content);
        $this->assertStringContainsString('200', $content);
    }

    public function test_admin_can_export_courses_csv(): void
    {
        $this->actingAs($this->admin);

        Course::create([
            'instructor_id' => $this->admin->id,
            'title' => 'Export Course Test',
            'slug' => 'export-course-test',
            'price' => 99.00,
        ]);

        $response = $this->post(route('admin.reports.export'), [
            'type' => 'courses'
        ]);

        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Titre', $content);
        $this->assertStringContainsString('Export Course Test', $content);
        $this->assertStringContainsString('99', $content);
    }
}
