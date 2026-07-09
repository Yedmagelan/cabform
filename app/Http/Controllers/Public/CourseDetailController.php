<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseDetailController extends Controller
{
    /**
     * Fiche formation détaillée.
     */
    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->published()
            ->with([
                'category',
                'instructor.profile',
                'modules.lessons',
                'reviews' => fn($q) => $q->approved()->with('user')->latest()->take(10),
                'quizzes',
            ])
            ->withCount(['enrollments', 'reviews'])
            ->firstOrFail();

        $relatedCourses = Course::published()
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->with(['category', 'instructor'])
            ->take(3)
            ->get();

        $isEnrolled = auth()->check() ? auth()->user()->enrolledIn($course) : false;

        return view('public.course-detail', compact('course', 'relatedCourses', 'isEnrolled'));
    }
}
