<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_enrolled_in_a_course(): void
    {
        // Suppress warning/deprecation log outputs
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'status' => 'published',
            'price' => 100,
        ]);

        $service = new EnrollmentService();
        $enrollment = $service->enroll($user, $course);

        $this->assertEquals('active', $enrollment->status);
        $this->assertEquals($user->id, $enrollment->user_id);
        $this->assertEquals($course->id, $enrollment->course_id);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
    }
}
