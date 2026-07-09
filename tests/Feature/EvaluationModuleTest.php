<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\CertificateTemplate;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_shows_limited_questions_from_bank_and_stores_them_in_session(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Evaluation Course',
            'slug' => 'eval-course',
            'status' => 'published',
            'price' => 0,
            'is_free' => true,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'slug' => 'module-1',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'module_id' => $module->id,
            'title' => 'Question Bank Quiz',
            'type' => 'quiz',
            'questions_per_attempt' => 2,
            'shuffle_questions' => true,
        ]);

        // Create 4 questions
        for ($i = 1; $i <= 4; $i++) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => 'Question ' . $i,
                'type' => 'mcq',
            ]);

            Answer::create([
                'question_id' => $question->id,
                'answer_text' => 'Correct Answer',
                'is_correct' => true,
            ]);
        }

        // Enroll user
        (new EnrollmentService())->enroll($user, $course);

        $this->actingAs($user);

        // Access the quiz
        $response = $this->get(route('learner.quiz.show', ['slug' => $course->slug, 'quizId' => $quiz->id]));

        $response->assertStatus(200);

        // Verify session has selected question IDs
        $this->assertTrue(session()->has('quiz_questions_' . $quiz->id));
        $selectedIds = session('quiz_questions_' . $quiz->id);
        $this->assertCount(2, $selectedIds);
    }

    public function test_submitting_final_exam_generates_certificate_upon_passing(): void
    {
        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Certified Course',
            'slug' => 'cert-course',
            'status' => 'published',
            'price' => 0,
            'is_free' => true,
            'is_certified' => true,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'slug' => 'module-1',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'module_id' => $module->id,
            'title' => 'Final Exam',
            'type' => 'exam',
            'passing_score' => 75.00,
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'What is 1+1?',
            'type' => 'mcq',
        ]);

        $correctAnswer = Answer::create([
            'question_id' => $question->id,
            'answer_text' => '2',
            'is_correct' => true,
        ]);

        CertificateTemplate::create([
            'name' => 'Default',
            'is_default' => true,
            'is_active' => true,
        ]);

        // Enroll user
        (new EnrollmentService())->enroll($user, $course);

        $this->actingAs($user);

        // Set session with the question
        session(['quiz_questions_' . $quiz->id => [$question->id]]);

        // Submit correct answer
        $response = $this->post(route('learner.quiz.submit', ['slug' => $course->slug, 'quizId' => $quiz->id]), [
            'answers' => [
                $question->id => $correctAnswer->id,
            ]
        ]);

        // Assert redirect to results page
        $response->assertRedirect();

        // Check quiz attempt passed
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'passed' => true,
            'score' => 100.00,
        ]);

        // Check certificate generated in database
        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'generated',
        ]);
    }
}
