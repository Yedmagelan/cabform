<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup learner role
        Role::firstOrCreate(['name' => 'apprenant']);
    }

    public function test_registration_requires_a_course_id(): void
    {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['course_id']);
        $this->assertDatabaseMissing('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_registration_fails_if_course_is_not_published(): void
    {
        $instructor = User::factory()->create();
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Draft Course',
            'slug' => 'draft-course',
            'status' => 'draft',
            'price' => 100,
        ]);

        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'course_id' => $course->id,
        ]);

        $response->assertSessionHasErrors(['course_id']);
        $this->assertDatabaseMissing('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_registration_succeeds_with_published_course_and_auto_enrolls(): void
    {
        $instructor = User::factory()->create();
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Laravel Masterclass',
            'slug' => 'laravel-masterclass',
            'status' => 'published',
            'price' => 100,
        ]);

        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'course_id' => $course->id,
        ]);

        $response->assertRedirect(route('learner.pending-activation'));

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'status' => 'pending',
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue($user->hasRole('apprenant'));

        // Verify user is enrolled
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        // Verify logged in
        $this->assertAuthenticatedAs($user);
    }
}
