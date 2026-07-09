<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\ForumThread;
use App\Services\EnrollmentService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerSpaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_can_track_video_playback_position(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Video Course',
            'slug' => 'video-course',
            'status' => 'published',
            'price' => 0,
            'is_free' => true,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'slug' => 'module-1',
        ]);

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Video Lesson',
            'slug' => 'video-lesson',
            'type' => 'video',
        ]);

        // Enroll user
        $enrollment = (new EnrollmentService())->enroll($user, $course);

        $this->actingAs($user);

        // Track video position to 125 seconds
        $response = $this->post(route('learner.course.lesson.video-position', [
            'slug' => $course->slug,
            'lessonId' => $lesson->id,
        ]), [
            'position' => 125.5,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('progress', [
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
            'video_position_seconds' => 125.5,
        ]);
    }

    public function test_learner_can_use_forum(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create thread
        $response = $this->post(route('learner.forum.thread.store'), [
            'title' => 'My first question',
            'body' => 'I have a question about this course content.',
        ]);

        $thread = ForumThread::first();
        $this->assertNotNull($thread);
        $response->assertRedirect(route('learner.forum.thread.show', $thread->id));

        $this->assertDatabaseHas('forum_threads', [
            'title' => 'My first question',
            'user_id' => $user->id,
        ]);

        // Post a reply
        $response = $this->post(route('learner.forum.reply.store', $thread->id), [
            'body' => 'Here is my reply/answer.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('forum_posts', [
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Here is my reply/answer.',
        ]);
    }

    public function test_learner_can_send_messages_to_instructors(): void
    {
        Role::create(['name' => 'formateur']);
        $instructor = User::factory()->create();
        $instructor->assignRole('formateur');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('learner.messages.store'), [
            'receiver_id' => $instructor->id,
            'subject' => 'Question for Instructor',
            'body' => 'Hello teacher, I have a quick question.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'sender_id' => $user->id,
            'receiver_id' => $instructor->id,
            'subject' => 'Question for Instructor',
            'body' => 'Hello teacher, I have a quick question.',
        ]);
    }

    public function test_learner_can_access_dashboard_and_ajax_courses(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('learner.dashboard'));
        $response->assertStatus(200);

        $responseAjax = $this->get(route('learner.dashboard.courses-ajax'));
        $responseAjax->assertStatus(200);
    }

    public function test_learner_can_view_checkout(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Paid Course',
            'slug' => 'paid-course',
            'status' => 'published',
            'price' => 15000,
            'is_free' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('checkout', $course->slug));
        $response->assertStatus(200);
        $response->assertSee('15 000 FCFA');
    }

    public function test_learner_can_view_and_submit_assignment(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Assignment Course',
            'slug' => 'assignment-course',
            'status' => 'published',
            'price' => 0,
            'is_free' => true,
        ]);

        $assignment = \App\Models\Assignment::create([
            'course_id' => $course->id,
            'title' => 'Devoir 1',
            'description' => 'Sujet du devoir',
            'max_score' => 20,
            'max_submissions' => 2,
            'max_file_size_mb' => 20,
        ]);

        // Enroll user
        (new EnrollmentService())->enroll($user, $course);

        $this->actingAs($user);

        $response = $this->get(route('learner.assignment.show', [$course->slug, $assignment->id]));
        $response->assertStatus(200);
        $response->assertSee('Devoir 1');

        $responseSubmit = $this->post(route('learner.assignment.submit', [$course->slug, $assignment->id]), [
            'content' => 'Ma réponse en ligne au devoir.'
        ]);

        $responseSubmit->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
            'content' => 'Ma réponse en ligne au devoir.',
            'status' => 'submitted',
        ]);
    }
}
