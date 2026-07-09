<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequential_unlock_prevents_access_to_next_lesson_until_previous_is_completed(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Sequential Course',
            'slug' => 'seq-course',
            'status' => 'published',
            'price' => 0,
            'is_free' => true,
            'sequential_unlock' => true,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'slug' => 'module-1',
            'sort_order' => 1,
        ]);

        $lesson1 = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1',
            'slug' => 'lesson-1',
            'sort_order' => 1,
            'type' => 'text',
        ]);

        $lesson2 = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 2',
            'slug' => 'lesson-2',
            'sort_order' => 2,
            'type' => 'text',
        ]);

        // Enroll user
        $enrollment = (new EnrollmentService())->enroll($user, $course);

        // Login as learner
        $this->actingAs($user);

        // Accessing lesson 1 should be successful (first lesson is always unlocked)
        $response = $this->get(route('learner.course.lesson', ['slug' => $course->slug, 'lessonId' => $lesson1->id]));
        $response->assertStatus(200);

        // Accessing lesson 2 directly should be redirected (as lesson 1 is not completed yet)
        $response = $this->get(route('learner.course.lesson', ['slug' => $course->slug, 'lessonId' => $lesson2->id]));
        $response->assertRedirect(route('learner.course.player', $course->slug));

        // Mark lesson 1 as completed
        (new ProgressService())->markLessonComplete($enrollment, $lesson1);

        // Accessing lesson 2 now should be successful!
        $response = $this->get(route('learner.course.lesson', ['slug' => $course->slug, 'lessonId' => $lesson2->id]));
        $response->assertStatus(200);
    }

    public function test_instructor_can_duplicate_course(): void
    {
        Role::create(['name' => 'formateur']);
        $instructor = User::factory()->create();
        $instructor->assignRole('formateur');

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Course to Duplicate',
            'slug' => 'dup-course',
            'status' => 'published',
            'price' => 100,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'slug' => 'module-1',
            'sort_order' => 1,
        ]);

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1',
            'slug' => 'lesson-1',
            'sort_order' => 1,
        ]);

        $this->actingAs($instructor);

        $response = $this->post(route('instructor.courses.duplicate', $course->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check duplicated course exists in DB
        $this->assertDatabaseHas('courses', [
            'title' => 'Copie de Course to Duplicate',
            'status' => 'draft',
            'version' => 1,
        ]);

        $duplicatedCourse = Course::where('title', 'Copie de Course to Duplicate')->first();
        $this->assertNotNull($duplicatedCourse);
        $this->assertCount(1, $duplicatedCourse->modules);
        $this->assertCount(1, $duplicatedCourse->lessons);
    }

    public function test_instructor_can_increment_course_version(): void
    {
        Role::create(['name' => 'formateur']);
        $instructor = User::factory()->create();
        $instructor->assignRole('formateur');

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Course to Version',
            'slug' => 'ver-course',
            'status' => 'published',
            'price' => 100,
            'version' => 1,
        ]);

        $this->actingAs($instructor);

        $response = $this->post(route('instructor.courses.version', $course->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(2, $course->fresh()->version);
    }
}
