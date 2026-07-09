<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Certificate;
use App\Models\Coupon;
use App\Models\Page;
use App\Models\BlogPost;
use App\Models\Banner;
use App\Models\Faq;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\Partner;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Admin CRUD Controller — Gère les opérations CRUD pour toutes les entités du back-office.
 * Chaque méthode préfixée par le nom de la ressource.
 */
class CrudController extends Controller
{
    // ═══════════════════════════ USERS ═══════════════════════════
    public function usersIndex(Request $request)
    {
        Gate::authorize('users.view');
        $query = User::with('roles')->withCount('enrollments');
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('first_name', 'like', "%{$request->search}%")->orWhere('last_name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%"));
        }
        if ($request->filled('role')) { $query->role($request->role); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    // ═══════════════════════════ COURSES ═════════════════════════
    public function coursesIndex(Request $request)
    {
        Gate::authorize('courses.view');
        $query = Course::with(['category', 'instructor'])->withCount('enrollments');
        if ($request->filled('search')) { $query->search($request->search); }
        if ($request->filled('category')) { $query->where('category_id', $request->category); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $courses = $query->latest()->paginate(20);
        $categories = Category::active()->ordered()->get();
        return view('admin.courses.index', compact('courses', 'categories'));
    }

    // ═══════════════════════════ CATEGORIES ══════════════════════
    public function categoriesIndex()
    {
        Gate::authorize('categories.manage');
        $categories = Category::withCount('courses')->ordered()->get();
        return view('admin.categories.index', compact('categories'));
    }

    // ═══════════════════════════ QUIZZES ═════════════════════════
    public function quizzesIndex()
    {
        Gate::authorize('quizzes.view');
        $quizzes = Quiz::with(['course', 'module'])->withCount(['questions', 'attempts'])->latest()->paginate(20);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    // ═══════════════════════════ ENROLLMENTS ═════════════════════
    public function enrollmentsIndex(Request $request)
    {
        Gate::authorize('enrollments.view');
        $query = Enrollment::with(['user', 'course']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(20);
        
        $courses = Course::published()->get();
        $users = User::role('apprenant')->get(); // Get all learners

        return view('admin.enrollments.index', compact('enrollments', 'courses', 'users'));
    }

    public function enrollmentsExport(Request $request)
    {
        Gate::authorize('enrollments.view');

        $query = Enrollment::with(['user', 'course']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->get();
        $format = $request->input('format', 'excel');

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.enrollments.pdf', compact('enrollments'));
            return $pdf->download('export_apprenants_' . date('Ymd_His') . '.pdf');
        }

        // Default to Excel (CSV)
        $fileName = 'export_apprenants_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['ID Inscription', 'Apprenant', 'Email', 'Formation', 'Progression (%)', 'Statut', 'Date d\'inscription']);

            foreach ($enrollments as $e) {
                fputcsv($file, [
                    $e->id,
                    $e->user->full_name ?? '-',
                    $e->user->email ?? '-',
                    $e->course->title ?? '-',
                    $e->progress_percentage,
                    $e->status,
                    $e->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ═══════════════════════════ PARTNERS ════════════════════════
    public function partnersIndex()
    {
        Gate::authorize('partners.view');
        $partners = Partner::with('user')->withCount('enrollments')->latest()->paginate(20);
        return view('admin.partners.index', compact('partners'));
    }

    // ═══════════════════════════ PAYMENTS ════════════════════════
    public function paymentsIndex(Request $request)
    {
        Gate::authorize('payments.view');
        $query = Payment::with(['user', 'order']);
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('method')) { $query->where('method', $request->method); }
        $payments = $query->latest()->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    // ═══════════════════════════ ORDERS ══════════════════════════
    public function ordersIndex(Request $request)
    {
        Gate::authorize('orders.view');
        $query = Order::with(['user', 'items.course', 'payment']);
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $orders = $query->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    // ═══════════════════════════ COUPONS ═════════════════════════
    public function couponsIndex()
    {
        Gate::authorize('coupons.manage');
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    // ═══════════════════════════ CERTIFICATES ════════════════════
    public function certificatesIndex(Request $request)
    {
        Gate::authorize('certificates.view');
        $query = Certificate::with(['user', 'course']);
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $certificates = $query->latest()->paginate(20);
        return view('admin.certificates.index', compact('certificates'));
    }

    // ═══════════════════════════ CMS PAGES ═══════════════════════
    public function pagesIndex()
    {
        Gate::authorize('pages.manage');
        $pages = Page::latest()->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    // ═══════════════════════════ BLOG ════════════════════════════
    public function blogIndex()
    {
        Gate::authorize('blog.manage');
        $posts = BlogPost::with('author')->latest()->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    // ═══════════════════════════ BANNERS ═════════════════════════
    public function bannersIndex()
    {
        Gate::authorize('banners.manage');
        $banners = Banner::orderBy('sort_order')->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    // ═══════════════════════════ FAQ ═════════════════════════════
    public function faqsIndex()
    {
        Gate::authorize('faqs.manage');
        $faqs = Faq::orderBy('sort_order')->paginate(20);
        return view('admin.faqs.index', compact('faqs'));
    }

    // ═══════════════════════════ AUDIT LOGS ══════════════════════
    public function auditLogsIndex(Request $request)
    {
        $query = AuditLog::with('user');
        if ($request->filled('action')) { $query->where('action', $request->action); }
        $logs = $query->latest()->paginate(30);
        return view('admin.audit-logs.index', compact('logs'));
    }

    // ═══════════════════════════ SETTINGS ════════════════════════
    public function settingsIndex()
    {
        Gate::authorize('settings.manage');
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    // ═══════════════════════════ REPORTS ═════════════════════════
    public function reportsIndex()
    {
        Gate::authorize('reports.view');

        $stats = [
            'users_total' => \App\Models\User::count(),
            'users_new_month' => \App\Models\User::where('created_at', '>=', now()->startOfMonth())->count(),
            'revenue_total' => \App\Models\Order::where('status', 'paid')->sum('total'),
            'orders_count' => \App\Models\Order::where('status', 'paid')->count(),
            'courses_total' => \App\Models\Course::count(),
            'avg_progress' => \App\Models\Enrollment::avg('progress_percentage') ?? 0,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function reportsExport(Request $request)
    {
        Gate::authorize('reports.view');

        $type = $request->input('type');
        $fileName = 'export_' . $type . '_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to prevent excel issues with accents
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($type === 'users') {
                fputcsv($file, ['ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Date d\'inscription']);
                $users = \App\Models\User::latest()->get();
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->first_name,
                        $user->last_name,
                        $user->email,
                        $user->phone,
                        $user->created_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            } elseif ($type === 'financial') {
                fputcsv($file, ['ID', 'Utilisateur', 'Total', 'Statut', 'Moyen de paiement', 'Date']);
                $orders = \App\Models\Order::with(['user', 'payment'])->latest()->get();
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->id,
                        $order->user?->full_name ?? 'Inconnu',
                        $order->total,
                        $order->status,
                        $order->payment?->method ?? 'N/A',
                        $order->created_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            } elseif ($type === 'courses') {
                fputcsv($file, ['ID', 'Titre', 'Formateur', 'Prix', 'Inscrits', 'Version', 'Statut']);
                $courses = \App\Models\Course::with('instructor')->latest()->get();
                foreach ($courses as $course) {
                    fputcsv($file, [
                        $course->id,
                        $course->title,
                        $course->instructor?->full_name ?? 'Inconnu',
                        $course->price,
                        $course->enrollment_count,
                        $course->version,
                        $course->status,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ╔═══════════════════════════════════════════════════════════════════╗
    // ║                     STORE / UPDATE / DELETE                      ║
    // ╚═══════════════════════════════════════════════════════════════════╝

    // ── Users CRUD ───────────────────────────────────────────
    public function userStore(\App\Http\Requests\Admin\StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        AuditLog::log('user_create', auth()->user(), 'App\Models\User', $user->id, "Utilisateur créé: {$user->full_name}");

        return back()->with('success', 'Utilisateur créé avec succès.');
    }

    public function userUpdate(\App\Http\Requests\Admin\UpdateUserRequest $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validated();

        $user->update($validated);

        if ($request->filled('role')) {
            $user->syncRoles([$validated['role']]);
        }

        AuditLog::log('user_update', auth()->user(), 'App\Models\User', $user->id, "Utilisateur modifié: {$user->full_name}");

        return back()->with('success', 'Utilisateur mis à jour.');
    }

    public function userDelete(int $id)
    {
        Gate::authorize('users.delete');
        $user = User::findOrFail($id);
        $user->delete();
        AuditLog::log('user_delete', auth()->user(), 'App\Models\User', $id, "Utilisateur supprimé: {$user->full_name}");
        return back()->with('success', 'Utilisateur supprimé.');
    }



    // ── Courses Admin ────────────────────────────────────────
    public function coursePublish(int $id)
    {
        $course = Course::findOrFail($id);
        Gate::authorize('publish', $course);
        $course->update(['status' => $course->status === 'published' ? 'draft' : 'published', 'published_at' => now()]);
        AuditLog::log('course_publish', auth()->user(), 'App\Models\Course', $id, "Cours publié/dépublié: {$course->title}");
        return back()->with('success', 'Statut de la formation mis à jour.');
    }

    public function courseDelete(int $id)
    {
        $course = Course::findOrFail($id);
        Gate::authorize('delete', $course);
        $course->delete();
        AuditLog::log('course_delete', auth()->user(), 'App\Models\Course', $id, "Cours supprimé: {$course->title}");
        return back()->with('success', 'Formation supprimée.');
    }

    public function duplicateCourse(int $id)
    {
        $course = Course::with('modules.lessons.resources')->findOrFail($id);
        Gate::authorize('update', $course);

        $newCourse = $course->replicate();
        $newCourse->title = 'Copie de ' . $course->title;
        $newCourse->slug = $course->slug . '-copie-' . time();
        $newCourse->status = 'draft';
        $newCourse->published_at = null;
        $newCourse->version = 1;
        $newCourse->save();

        foreach ($course->modules as $module) {
            $newModule = $module->replicate();
            $newModule->course_id = $newCourse->id;
            $newModule->save();

            foreach ($module->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->module_id = $newModule->id;
                $newLesson->save();

                foreach ($lesson->resources as $resource) {
                    $newResource = $resource->replicate();
                    $newResource->lesson_id = $newLesson->id;
                    $newResource->save();
                }
            }
        }

        AuditLog::log('course_duplicate', auth()->user(), 'App\Models\Course', $id, "Cours dupliqué: {$course->title} (Nouveau ID: {$newCourse->id})");
        return back()->with('success', 'Formation dupliquée avec succès comme brouillon.');
    }

    public function incrementVersion(int $id)
    {
        $course = Course::findOrFail($id);
        Gate::authorize('update', $course);

        $course->increment('version');
        AuditLog::log('course_version', auth()->user(), 'App\Models\Course', $id, "Version du cours incrémentée à {$course->version}: {$course->title}");

        return back()->with('success', 'Nouvelle version (' . $course->version . ') créée avec succès.');
    }

    // ── Enrollments Admin ────────────────────────────────────
    public function enrollmentStore(Request $request)
    {
        Gate::authorize('enrollments.create');
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $enrollment = app(\App\Services\EnrollmentService::class)->enroll(
            User::find($validated['user_id']),
            Course::find($validated['course_id'])
        );

        return back()->with('success', 'Inscription créée.');
    }

    public function enrollmentUpdate(Request $request, int $id)
    {
        Gate::authorize('enrollments.view'); // Allow users with enrollment view permission to update progression
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,completed,suspended,cancelled',
        ]);

        $enrollment->update($validated);

        if (in_array($validated['status'], ['active', 'completed'])) {
            if ($enrollment->user->status === 'pending') {
                $enrollment->user->update(['status' => 'active']);
            }
        }

        AuditLog::log('enrollment_update', auth()->user(), 'App\Models\Enrollment', $id, "Inscription mise à jour pour {$enrollment->user->full_name} sur {$enrollment->course->title} (Progression : {$validated['progress_percentage']}%)");

        return back()->with('success', 'Inscription mise à jour avec succès.');
    }

    public function enrollmentDelete(int $id)
    {
        Gate::authorize('enrollments.view'); // Allow users with enrollment view permission to cancel
        $enrollment = Enrollment::findOrFail($id);
        $user_name = $enrollment->user->full_name;
        $course_title = $enrollment->course->title;
        $enrollment->delete();

        AuditLog::log('enrollment_delete', auth()->user(), 'App\Models\Enrollment', $id, "Inscription supprimée pour {$user_name} sur {$course_title}");

        return back()->with('success', 'Inscription supprimée.');
    }

    // ── Coupons CRUD ─────────────────────────────────────────
    public function couponStore(Request $request)
    {
        Gate::authorize('coupons.manage');
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $validated['is_active'] = true;
        $validated['used_count'] = 0;
        Coupon::create($validated);

        return back()->with('success', 'Coupon créé.');
    }

    public function couponDelete(int $id)
    {
        Gate::authorize('coupons.manage');
        Coupon::findOrFail($id)->delete();
        return back()->with('success', 'Coupon supprimé.');
    }

    // ── Pages CMS CRUD ───────────────────────────────────────
    public function pageStore(Request $request)
    {
        Gate::authorize('pages.manage');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        Page::create($validated);

        return back()->with('success', 'Page créée.');
    }

    public function pageUpdate(Request $request, int $id)
    {
        Gate::authorize('pages.manage');
        $page = Page::findOrFail($id);
        $page->update($request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]));
        return back()->with('success', 'Page mise à jour.');
    }

    public function pageDelete(int $id)
    {
        Gate::authorize('pages.manage');
        Page::findOrFail($id)->delete();
        return back()->with('success', 'Page supprimée.');
    }

    // ── Blog CRUD ────────────────────────────────────────────
    public function blogStore(Request $request)
    {
        Gate::authorize('blog.manage');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']) . '-' . \Illuminate\Support\Str::random(4);
        $validated['author_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return back()->with('success', 'Article créé.');
    }

    public function blogUpdate(Request $request, int $id)
    {
        Gate::authorize('blog.manage');
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
        ]);

        $post->update($validated);

        return back()->with('success', 'Article mis à jour.');
    }

    public function blogDelete(int $id)
    {
        Gate::authorize('blog.manage');
        BlogPost::findOrFail($id)->delete();
        return back()->with('success', 'Article supprimé.');
    }

    // ── Banners CRUD ─────────────────────────────────────────
    public function bannerStore(Request $request)
    {
        Gate::authorize('banners.manage');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['sort_order'] = Banner::count();
        $validated['is_active'] = true;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($validated);

        return back()->with('success', 'Bannière créée.');
    }

    public function bannerDelete(int $id)
    {
        Gate::authorize('banners.manage');
        Banner::findOrFail($id)->delete();
        return back()->with('success', 'Bannière supprimée.');
    }

    // ── FAQ CRUD ─────────────────────────────────────────────
    public function faqStore(Request $request)
    {
        Gate::authorize('faqs.manage');
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        $validated['sort_order'] = Faq::count();
        $validated['is_active'] = true;
        Faq::create($validated);

        return back()->with('success', 'FAQ ajoutée.');
    }

    public function faqUpdate(Request $request, int $id)
    {
        Gate::authorize('faqs.manage');
        Faq::findOrFail($id)->update($request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
        ]));
        return back()->with('success', 'FAQ mise à jour.');
    }

    public function faqDelete(int $id)
    {
        Gate::authorize('faqs.manage');
        Faq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ supprimée.');
    }

    // ── Settings ─────────────────────────────────────────────
    public function settingsUpdate(Request $request)
    {
        Gate::authorize('settings.manage');
        foreach ($request->except('_token', '_method') as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        AuditLog::log('settings_update', auth()->user(), null, null, 'Paramètres mis à jour');

        return back()->with('success', 'Paramètres mis à jour.');
    }

    // ── Certificates Admin ───────────────────────────────────
    public function certificateGenerate(int $enrollmentId)
    {
        Gate::authorize('generate', Certificate::class);
        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($enrollmentId);

        $certificate = app(\App\Services\CertificateService::class)->generate(
            $enrollment->user,
            $enrollment->course,
            $enrollment
        );

        return back()->with('success', 'Certificat généré : ' . $certificate->certificate_number);
    }

    public function certificateRevoke(Request $request, int $id)
    {
        $certificate = Certificate::findOrFail($id);
        Gate::authorize('revoke', $certificate);
        app(\App\Services\CertificateService::class)->revoke($certificate, $request->reason ?? '');

        return back()->with('success', 'Certificat révoqué.');
    }

    // ── Advanced Users & Bulk Actions ────────────────────────
    public function usersBulkAction(Request $request)
    {
        Gate::authorize('users.view');

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:delete,activate,suspend,change_role',
            'role' => 'required_if:action,change_role|string',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        if ($action === 'delete') {
            User::whereIn('id', $userIds)->delete();
            AuditLog::log('user_bulk_delete', auth()->user(), 'App\Models\User', null, "Suppression en masse de " . count($userIds) . " utilisateurs.");
        } elseif ($action === 'activate') {
            User::whereIn('id', $userIds)->update(['status' => 'active']);
            AuditLog::log('user_bulk_activate', auth()->user(), 'App\Models\User', null, "Activation en masse de " . count($userIds) . " utilisateurs.");
        } elseif ($action === 'suspend') {
            User::whereIn('id', $userIds)->update(['status' => 'suspended']);
            AuditLog::log('user_bulk_suspend', auth()->user(), 'App\Models\User', null, "Suspension en masse de " . count($userIds) . " utilisateurs.");
        } elseif ($action === 'change_role') {
            $role = $request->role;
            $users = User::whereIn('id', $userIds)->get();
            foreach ($users as $user) {
                $user->syncRoles([$role]);
            }
            AuditLog::log('user_bulk_role_change', auth()->user(), 'App\Models\User', null, "Modification en masse du rôle pour " . count($userIds) . " utilisateurs vers " . $role);
        }

        return back()->with('success', 'Actions en masse exécutées avec succès.');
    }

    public function userShow(int $id)
    {
        Gate::authorize('users.view');

        $user = User::with(['roles', 'profile'])->findOrFail($id);

        $enrollments = Enrollment::where('user_id', $user->id)->with('course')->get();
        
        $payments = Payment::where('user_id', $user->id)->with('order')->get();
        
        $certificates = Certificate::where('user_id', $user->id)->with('course')->get();
        
        $activity = AuditLog::where('user_id', $user->id)->latest()->take(100)->get();

        $permissions = \Spatie\Permission\Models\Permission::all();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.users.show', compact('user', 'enrollments', 'payments', 'certificates', 'activity', 'permissions', 'userPermissions', 'directPermissions'));
    }

    public function userPermissionsOverride(Request $request, int $id)
    {
        Gate::authorize('users.view');

        $user = User::findOrFail($id);
        
        // Sync direct permissions (override)
        $user->syncPermissions($request->input('permissions', []));

        AuditLog::log('user_permissions_override', auth()->user(), 'App\Models\User', $id, "Permissions personnalisées modifiées pour : {$user->full_name}");

        return back()->with('success', 'Permissions personnalisées appliquées avec succès.');
    }

    public function userLogoutSessions(Request $request, int $id)
    {
        Gate::authorize('users.view');

        $user = User::findOrFail($id);
        
        // Force session logout by changing remember_token
        $user->forceFill([
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        AuditLog::log('user_logout_sessions', auth()->user(), 'App\Models\User', $id, "Sessions réinitialisées pour : {$user->full_name}");

        return back()->with('success', 'Toutes les sessions de l\'utilisateur ont été réinitialisées.');
    }

    public function userChangeStatus(Request $request, int $id)
    {
        Gate::authorize('users.view');

        $request->validate([
            'status' => 'required|in:active,suspended,inactive,pending',
            'reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($id);
        $oldStatus = $user->status;
        $user->update(['status' => $request->status]);

        if ($oldStatus === 'pending' && $request->status === 'active') {
            // Activate all pending enrollments of this user
            foreach ($user->enrollments()->where('status', 'pending')->get() as $enrollment) {
                $enrollment->update(['status' => 'active', 'enrolled_at' => now()]);
                $enrollment->course->increment('enrollment_count');
            }
        }

        AuditLog::log('user_status_change', auth()->user(), 'App\Models\User', $id, "Statut de l'utilisateur changé en : {$request->status}. Motif : {$request->reason}");

        return back()->with('success', 'Statut de l\'utilisateur mis à jour.');
    }

    public function usersExportAdvanced(Request $request)
    {
        Gate::authorize('users.view');

        $columns = $request->input('columns', ['id', 'first_name', 'last_name', 'email', 'status', 'created_at']);
        $role = $request->input('role');
        $status = $request->input('status');

        $query = User::with('roles');

        if ($role) {
            $query->role($role);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $users = $query->latest()->get();

        $fileName = 'users_export_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            $headerRow = [];
            foreach ($columns as $column) {
                $headerRow[] = ucfirst(str_replace('_', ' ', $column));
            }
            fputcsv($file, $headerRow);

            foreach ($users as $user) {
                $row = [];
                foreach ($columns as $column) {
                    if ($column === 'role') {
                        $row[] = $user->roles->pluck('name')->implode(', ');
                    } else {
                        $row[] = $user->{$column} ?? '';
                    }
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Advanced Courses & Bulk Actions ──────────────────────
    public function courseShow(int $id)
    {
        Gate::authorize('courses.view');

        $course = Course::with(['category', 'instructor', 'modules.lessons', 'modules.quizzes'])->findOrFail($id);

        $enrollmentsCount = Enrollment::where('course_id', $id)->count();
        $completedCount = Enrollment::where('course_id', $id)->where('status', 'completed')->count();
        $avgProgress = Enrollment::where('course_id', $id)->avg('progress_percentage') ?? 0;
        
        $totalRevenue = Order::whereHas('items', function($q) use ($id) {
            $q->where('course_id', $id);
        })->where('status', 'paid')->sum('total');

        $quizIds = $course->modules->flatMap->quizzes->pluck('id')->toArray();
        $attemptsCount = \App\Models\QuizAttempt::whereIn('quiz_id', $quizIds)->count();
        $avgQuizScore = \App\Models\QuizAttempt::whereIn('quiz_id', $quizIds)->avg('score') ?? 0;

        $stats = [
            'enrolled_total' => $enrollmentsCount,
            'completed' => $completedCount,
            'avg_progress' => round($avgProgress, 1),
            'avg_quiz_score' => round($avgQuizScore, 1),
            'revenue_total' => $totalRevenue,
            'attempts_count' => $attemptsCount,
        ];

        // Retrieve course items list (enrolled users)
        $enrollments = Enrollment::where('course_id', $id)->with('user')->paginate(10);

        // Retrieve reviews
        $reviews = \App\Models\Review::where('course_id', $id)->with('user')->paginate(10);

        return view('admin.courses.show', compact('course', 'stats', 'enrollments', 'reviews'));
    }

    public function coursesBulkAction(Request $request)
    {
        Gate::authorize('courses.view');

        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'action' => 'required|in:delete,archive,publish,change_category',
            'category_id' => 'required_if:action,change_category|exists:categories,id',
        ]);

        $courseIds = $request->course_ids;
        $action = $request->action;

        if ($action === 'delete') {
            Course::whereIn('id', $courseIds)->delete();
            AuditLog::log('course_bulk_delete', auth()->user(), 'App\Models\Course', null, "Suppression en masse de " . count($courseIds) . " formations.");
        } elseif ($action === 'archive') {
            Course::whereIn('id', $courseIds)->update(['status' => 'archived']);
            AuditLog::log('course_bulk_archive', auth()->user(), 'App\Models\Course', null, "Archivage en masse de " . count($courseIds) . " formations.");
        } elseif ($action === 'publish') {
            Course::whereIn('id', $courseIds)->update(['status' => 'published', 'published_at' => now()]);
            AuditLog::log('course_bulk_publish', auth()->user(), 'App\Models\Course', null, "Publication en masse de " . count($courseIds) . " formations.");
        } elseif ($action === 'change_category') {
            $catId = $request->category_id;
            Course::whereIn('id', $courseIds)->update(['category_id' => $catId]);
            AuditLog::log('course_bulk_category_change', auth()->user(), 'App\Models\Course', null, "Déplacement en masse de " . count($courseIds) . " formations vers la catégorie ID " . $catId);
        }

        return back()->with('success', 'Actions en masse sur les formations exécutées avec succès.');
    }

    public function courseReportPdf(int $id)
    {
        Gate::authorize('courses.view');

        $course = Course::with(['category', 'instructor'])->findOrFail($id);

        $enrollmentsCount = Enrollment::where('course_id', $id)->count();
        $completedCount = Enrollment::where('course_id', $id)->where('status', 'completed')->count();
        $avgProgress = Enrollment::where('course_id', $id)->avg('progress_percentage') ?? 0;

        $totalRevenue = Order::whereHas('items', function($q) use ($id) {
            $q->where('course_id', $id);
        })->where('status', 'paid')->sum('total');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.courses.report', compact('course', 'enrollmentsCount', 'completedCount', 'avgProgress', 'totalRevenue'));

        return $pdf->download('rapport_formation_' . $course->slug . '.pdf');
    }

    public function courseStore(Request $request)
    {
        Gate::authorize('courses.view');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'instructor_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']) . '-' . \Illuminate\Support\Str::random(5);
        $validated['status'] = 'draft';
        $validated['is_free'] = ($validated['price'] == 0);

        $course = Course::create($validated);

        AuditLog::log('course_create', auth()->user(), 'App\Models\Course', $course->id, "Formation créée par l'administrateur : {$course->title}");

        return back()->with('success', 'Formation créée avec succès.');
    }

    public function categoryStore(Request $request)
    {
        Gate::authorize('categories.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'required|integer|min:0',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category = Category::create($validated);

        AuditLog::log('category_create', auth()->user(), 'App\Models\Category', $category->id, "Catégorie créée : {$category->name}");

        return back()->with('success', 'Catégorie créée avec succès.');
    }

    public function categoryUpdate(Request $request, int $id)
    {
        Gate::authorize('categories.manage');

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'required|integer|min:0',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        AuditLog::log('category_update', auth()->user(), 'App\Models\Category', $category->id, "Catégorie mise à jour : {$category->name}");

        return back()->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function categoryDelete(int $id)
    {
        Gate::authorize('categories.manage');

        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        AuditLog::log('category_delete', auth()->user(), 'App\Models\Category', $id, "Catégorie supprimée : {$name}");

        return back()->with('success', 'Catégorie supprimée avec succès.');
    }
}
