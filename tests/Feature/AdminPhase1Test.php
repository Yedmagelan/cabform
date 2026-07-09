<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\SessionCohort;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPhase1Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $learner1;
    private User $learner2;
    private Course $course;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions if not handled automatically, or create admin role
        $this->setUpRolesAndPermissions();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrateur');

        $this->learner1 = User::factory()->create(['status' => 'active']);
        $this->learner1->assignRole('apprenant');

        $this->learner2 = User::factory()->create(['status' => 'inactive']);
        $this->learner2->assignRole('apprenant');

        $this->category = Category::create(['name' => 'Tech', 'slug' => 'tech', 'is_active' => true]);
        
        $instructor = User::factory()->create();
        $this->course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $this->category->id,
            'title' => 'DevOps Cloud',
            'slug' => 'devops-cloud',
            'price' => 15000,
            'status' => 'draft',
        ]);
    }

    private function setUpRolesAndPermissions()
    {
        // Simple setup for Spatie roles in sqlite memory
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'administrateur']);
        $learnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'apprenant']);
        
        $viewPermission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'users.view']);
        $coursesViewPermission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'courses.view']);
        $enrollmentsViewPermission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'enrollments.view']);
        $categoriesManagePermission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'categories.manage']);
        
        $adminRole->givePermissionTo([$viewPermission, $coursesViewPermission, $enrollmentsViewPermission, $categoriesManagePermission]);
    }

    public function test_admin_can_bulk_activate_users(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.users.bulk'), [
            'user_ids' => [$this->learner2->id],
            'action' => 'activate',
        ]);

        $response->assertRedirect();
        $this->assertEquals('active', $this->learner2->fresh()->status);
        
        // Assert AuditLog was created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_bulk_activate',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_detailed_user_profile(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users.show', $this->learner1->id));

        $response->assertStatus(200);
        $response->assertSee($this->learner1->email);
        $response->assertSee('Général');
        $response->assertSee('Formations');
    }

    public function test_admin_can_override_user_permissions(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.users.permissions', $this->learner1->id), [
            'permissions' => ['users.view']
        ]);

        $response->assertRedirect();
        $this->assertTrue($this->learner1->fresh()->hasDirectPermission('users.view'));
    }

    public function test_admin_can_force_logout_user_sessions(): void
    {
        $this->actingAs($this->admin);

        $oldToken = $this->learner1->remember_token;

        $response = $this->post(route('admin.users.logout-sessions', $this->learner1->id));

        $response->assertRedirect();
        $this->assertNotEquals($oldToken, $this->learner1->fresh()->remember_token);
    }

    public function test_admin_can_change_user_status_with_reason(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.users.status', $this->learner1->id), [
            'status' => 'suspended',
            'reason' => 'Comportement suspect sur le forum',
        ]);

        $response->assertRedirect();
        $this->assertEquals('suspended', $this->learner1->fresh()->status);
        
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_status_change',
            'description' => "Statut de l'utilisateur changé en : suspended. Motif : Comportement suspect sur le forum",
        ]);
    }

    public function test_admin_can_export_users_csv(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users.export.advanced', [
            'columns' => ['id', 'first_name', 'last_name', 'email', 'status']
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString($this->learner1->email, $response->streamedContent());
    }

    public function test_admin_can_view_detailed_course_dashboard(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.courses.show', $this->course->id));

        $response->assertStatus(200);
        $response->assertSee($this->course->title);
        $response->assertSee('Inscriptions');
        $response->assertSee('Score Quiz Moyen');
    }

    public function test_admin_can_bulk_archive_courses(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.courses.bulk'), [
            'course_ids' => [$this->course->id],
            'action' => 'archive',
        ]);

        $response->assertRedirect();
        $this->assertEquals('archived', $this->course->fresh()->status);
    }

    public function test_admin_can_download_course_pdf_report(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.courses.report', $this->course->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_crud_sessions_cohorts(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Session
        $response = $this->post(route('admin.sessions.store'), [
            'course_id' => $this->course->id,
            'name' => 'Cohorte A',
            'start_date' => now()->format('Y-m-d'),
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.sessions.index'));
        $this->assertDatabaseHas('sessions_cohorts', [
            'name' => 'Cohorte A',
            'status' => 'active',
        ]);

        $session = SessionCohort::where('name', 'Cohorte A')->firstOrFail();

        // 2. View Session Details
        $response = $this->get(route('admin.sessions.show', $session->id));
        $response->assertStatus(200);
        $response->assertSee('Cohorte A');

        // 3. Edit / Update Session
        $response = $this->put(route('admin.sessions.update', $session->id), [
            'course_id' => $this->course->id,
            'name' => 'Cohorte A Modifiée',
            'start_date' => now()->format('Y-m-d'),
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('admin.sessions.index'));
        $this->assertEquals('completed', $session->fresh()->status);

        // 4. Duplicate Session
        $response = $this->post(route('admin.sessions.duplicate', $session->id));
        $response->assertRedirect(route('admin.sessions.index'));
        $this->assertDatabaseHas('sessions_cohorts', [
            'name' => 'Cohorte A Modifiée - Copie',
            'status' => 'upcoming',
        ]);

        // 5. Delete Session
        $response = $this->delete(route('admin.sessions.destroy', $session->id));
        $response->assertRedirect(route('admin.sessions.index'));
        $this->assertDatabaseMissing('sessions_cohorts', [
            'id' => $session->id,
        ]);
    }

    public function test_admin_can_create_course(): void
    {
        $this->actingAs($this->admin);

        $instructor = User::factory()->create();

        $response = $this->post(route('admin.courses.store'), [
            'title' => 'Nouveau Cours Test Admin',
            'category_id' => $this->category->id,
            'instructor_id' => $instructor->id,
            'price' => 20000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'title' => 'Nouveau Cours Test Admin',
            'price' => 20000,
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_crud_categories(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Category
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Data Science & IA',
            'icon' => 'fas fa-brain',
            'sort_order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Data Science & IA',
            'icon' => 'fas fa-brain',
            'sort_order' => 5,
        ]);

        $cat = Category::where('name', 'Data Science & IA')->firstOrFail();

        // 2. Update Category
        $response = $this->put(route('admin.categories.update', $cat->id), [
            'name' => 'IA Générative',
            'icon' => 'fas fa-robot',
            'sort_order' => 2,
        ]);

        $response->assertRedirect();
        $this->assertEquals('IA Générative', $cat->fresh()->name);

        // 3. Delete Category
        $response = $this->delete(route('admin.categories.delete', $cat->id));
        $response->assertRedirect();
        $this->assertSoftDeleted('categories', [
            'id' => $cat->id,
        ]);
    }

    public function test_admin_can_crud_enrollments(): void
    {
        // Add enrollments.create permission to admin
        $createPerm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'enrollments.create']);
        $this->admin->roles()->first()->givePermissionTo($createPerm);

        $this->actingAs($this->admin);

        // 1. Create Enrollment
        $response = $this->post(route('admin.enrollments.store'), [
            'user_id' => $this->learner1->id,
            'course_id' => $this->course->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->learner1->id,
            'course_id' => $this->course->id,
        ]);

        $enrollment = Enrollment::where('user_id', $this->learner1->id)->where('course_id', $this->course->id)->firstOrFail();

        // 2. Update Enrollment
        $response = $this->put(route('admin.enrollments.update', $enrollment->id), [
            'progress_percentage' => 75,
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertEquals(75, $enrollment->fresh()->progress_percentage);
        $this->assertEquals('completed', $enrollment->fresh()->status);

        // 3. Delete Enrollment
        $response = $this->delete(route('admin.enrollments.delete', $enrollment->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('enrollments', [
            'id' => $enrollment->id,
        ]);
    }

    public function test_admin_can_export_enrollments_to_excel_and_pdf(): void
    {
        $this->actingAs($this->admin);

        // Create an enrollment
        $enrollment = Enrollment::create([
            'user_id' => $this->learner1->id,
            'course_id' => $this->course->id,
            'progress_percentage' => 45,
            'status' => 'active',
        ]);

        // 1. Test Excel Export
        $response = $this->get(route('admin.enrollments.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $this->assertStringContainsString('export_apprenants_', $response->headers->get('Content-Disposition'));

        // 2. Test PDF Export
        $response = $this->get(route('admin.enrollments.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertStringContainsString('export_apprenants_', $response->headers->get('Content-Disposition'));
    }
}
