<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Services\CertificateService;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_can_be_generated(): void
    {
        Storage::fake('public');

        $instructor = User::factory()->create();
        $user = User::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'status' => 'published',
            'price' => 100,
        ]);

        $enrollment = (new EnrollmentService())->enroll($user, $course);

        $template = CertificateTemplate::create([
            'name' => 'Default Template',
            'is_default' => true,
            'is_active' => true,
        ]);

        $service = new CertificateService();
        $certificate = $service->generate($user, $course, $enrollment);
        $certificate->refresh();

        $this->assertEquals('generated', $certificate->status);
        $this->assertEquals($user->id, $certificate->user_id);
        $this->assertEquals($course->id, $certificate->course_id);

        $this->assertNotNull($certificate->pdf_path);
        Storage::disk('public')->assertExists($certificate->pdf_path);
    }
}
