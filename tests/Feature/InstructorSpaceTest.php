<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Models\Enrollment;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorSpaceTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer le rôle formateur
        if (!Role::where('name', 'formateur')->exists()) {
            Role::create(['name' => 'formateur']);
        }

        // Créer un formateur
        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('formateur');

        // Créer une catégorie parente
        $this->category = Category::create([
            'name' => 'Design',
            'slug' => 'design',
            'is_active' => true,
        ]);
    }

    public function test_instructor_dashboard_is_accessible(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->get(route('instructor.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('instructor.dashboard');
    }

    public function test_instructor_can_create_course_draft_via_wizard(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->postJson(route('instructor.courses.store'), [
            'title' => 'Nouveau cours UI/UX',
            'subtitle' => 'Apprendre Figma',
            'category_id' => $this->category->id,
            'description' => 'Description détaillée du cours.',
            'level' => 'debutant',
            'language' => 'fr',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Nouveau cours UI/UX',
            'instructor_id' => $this->instructor->id,
            'status' => 'draft',
        ]);
    }

    public function test_instructor_can_configure_grading_rubric(): void
    {
        $this->actingAs($this->instructor);

        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'title' => 'Cours Figma',
            'slug' => 'cours-figma',
            'category_id' => $this->category->id,
            'description' => 'Desc',
            'status' => 'draft',
        ]);

        $response = $this->post(route('instructor.assignments.store', $course->id), [
            'title' => 'Projet Pratique Figma',
            'description' => 'Concevoir une landing page.',
            'max_score' => 20,
            'passing_score' => 10,
            'max_file_size_mb' => 5,
            'max_submissions' => 1,
            'rubric' => [
                [
                    'id' => 'crit-1',
                    'title' => 'Qualité visuelle',
                    'description' => 'Harmonie des couleurs et alignements',
                    'max_points' => 10,
                ],
                [
                    'id' => 'crit-2',
                    'title' => 'Ergonomie',
                    'description' => 'Facilité de navigation',
                    'max_points' => 10,
                ],
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assignments', [
            'title' => 'Projet Pratique Figma',
            'max_score' => 20,
        ]);

        $assignment = Assignment::where('title', 'Projet Pratique Figma')->first();
        $this->assertNotNull($assignment->rubric);
        $this->assertEquals('Qualité visuelle', $assignment->rubric[0]['title']);
    }

    public function test_instructor_can_grade_submission_using_rubric(): void
    {
        Role::create(['name' => 'apprenant']);
        $student = User::factory()->create();
        $student->assignRole('apprenant');

        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'title' => 'Cours Sketch',
            'slug' => 'cours-sketch',
            'category_id' => $this->category->id,
            'description' => 'Desc',
            'status' => 'published',
        ]);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Projet Sketch',
            'description' => 'Desc',
            'max_score' => 20,
            'passing_score' => 10,
            'rubric' => [
                [
                    'id' => 'crit-1',
                    'title' => 'Critère 1',
                    'description' => 'Desc',
                    'max_points' => 10,
                ],
                [
                    'id' => 'crit-2',
                    'title' => 'Critère 2',
                    'description' => 'Desc',
                    'max_points' => 10,
                ],
            ]
        ]);

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'content' => 'Mon projet sketch est prêt.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($this->instructor);

        $response = $this->post(route('instructor.submissions.grade', [$course->id, $assignment->id, $submission->id]), [
            'status' => 'graded',
            'feedback' => 'Excellent travail dans l\'ensemble.',
            'rubric_grades' => [
                'crit-1' => [
                    'score' => 9,
                    'comment' => 'Très bonne harmonie.',
                ],
                'crit-2' => [
                    'score' => 8,
                    'comment' => 'Bien pensé.',
                ],
            ]
        ]);

        $response->assertRedirect();
        
        $submission = $submission->fresh();
        $this->assertEquals(17.00, $submission->score);
        $this->assertTrue($submission->passed);
        $this->assertEquals('graded', $submission->status);
    }
}
