<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    public function create(Request $request)
    {
        $courses = Course::published()->orderBy('title')->get();
        return view('auth.register', compact('courses'));
    }

    public function store(Request $request, EnrollmentService $enrollmentService)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'course_id' => ['required', Rule::exists('courses', 'id')->where('status', 'published')],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        $user->assignRole('apprenant');

        // Auto-enroll in the selected course as pending validation
        $course = Course::find($request->course_id);
        $enrollmentService->enroll($user, $course, null, 'pending');

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('learner.pending-activation');
    }
}
