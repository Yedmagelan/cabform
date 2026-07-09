<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CourseDetailController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Learner\DashboardController as LearnerDashboardController;
use App\Http\Controllers\Learner\CourseController as LearnerCourseController;
use App\Http\Controllers\Learner\QuizController as LearnerQuizController;
use App\Http\Controllers\Learner\ProfileController;
use App\Http\Controllers\Learner\MessageController as LearnerMessageController;
use App\Http\Controllers\Learner\ForumController as LearnerForumController;
use App\Http\Controllers\Instructor\InstructorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CrudController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web — CabForm LMS
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════════════════
// ROUTES PUBLIQUES
// ═══════════════════════════════════════════════════════════════════════

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/course/{slug}', [CourseDetailController::class, 'show'])->name('course.show');

// Pages statiques
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

Route::get('/page/{slug}', [PageController::class, 'page'])->name('page.show');

// Pages légales statiques
Route::view('/mentions-legales', 'public.legal.mentions-legales')->name('legal.mentions');
Route::view('/cgu', 'public.legal.cgu')->name('legal.cgu');
Route::view('/confidentialite', 'public.legal.confidentialite')->name('legal.confidentialite');
Route::view('/cookies', 'public.legal.cookies')->name('legal.cookies');

// Vérification de certificat
Route::get('/verify-certificate', [PageController::class, 'verifyCertificate'])->name('certificate.verify');

// Blog
Route::get('/blog', [PageController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [PageController::class, 'blogShow'])->name('blog.show');

// ═══════════════════════════════════════════════════════════════════════
// ROUTES AUTHENTIFIÉES (Apprenant)
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'verified', 'user_status'])->prefix('learner')->name('learner.')->group(function () {
    Route::get('/dashboard', [LearnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pending-activation', [LearnerDashboardController::class, 'pendingActivation'])->name('pending-activation');
    Route::get('/dashboard/courses-ajax', [LearnerDashboardController::class, 'coursesAjax'])->name('dashboard.courses-ajax');
    Route::get('/certificates', [LearnerDashboardController::class, 'certificates'])->name('certificates');
    Route::get('/profile', [LearnerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/2fa', [ProfileController::class, 'update2fa'])->name('profile.2fa');

    // Messagerie
    Route::get('/messages', [LearnerMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [LearnerMessageController::class, 'store'])->name('messages.store');

    // Forum
    Route::get('/forum', [LearnerForumController::class, 'index'])->name('forum.index');
    Route::post('/forum', [LearnerForumController::class, 'storeThread'])->name('forum.thread.store');
    Route::get('/forum/thread/{id}', [LearnerForumController::class, 'show'])->name('forum.thread.show');
    Route::post('/forum/thread/{id}/reply', [LearnerForumController::class, 'storeReply'])->name('forum.reply.store');

    // Cours — Lecteur & Progression
    Route::get('/course/{slug}', [LearnerCourseController::class, 'player'])->name('course.player');
    Route::get('/course/{slug}/lesson/{lessonId}', [LearnerCourseController::class, 'player'])->name('course.lesson');
    Route::post('/course/{slug}/lesson/{lessonId}/complete', [LearnerCourseController::class, 'completeLesson'])->name('course.lesson.complete');
    Route::post('/course/{slug}/lesson/{lessonId}/track', [LearnerCourseController::class, 'trackTime'])->name('course.lesson.track');
    Route::post('/course/{slug}/lesson/{lessonId}/video-position', [LearnerCourseController::class, 'updateVideoPosition'])->name('course.lesson.video-position');
    Route::post('/course/{slug}/enroll-free', [LearnerCourseController::class, 'enrollFree'])->name('course.enroll-free');

    // Signets & Notes (Bookmarks)
    Route::post('/course/{slug}/lesson/{lessonId}/bookmark', [LearnerCourseController::class, 'storeBookmark'])->name('course.lesson.bookmark');
    Route::get('/course/{slug}/lesson/{lessonId}/bookmarks', [LearnerCourseController::class, 'getBookmarks'])->name('course.lesson.bookmarks');
    Route::delete('/course/{slug}/lesson/{lessonId}/bookmark/{bookmarkId}', [LearnerCourseController::class, 'deleteBookmark'])->name('course.lesson.bookmark.delete');

    // Devoirs (Assignments)
    Route::get('/course/{slug}/assignment/{assignmentId}', [LearnerCourseController::class, 'assignmentShow'])->name('assignment.show');
    Route::post('/course/{slug}/assignment/{assignmentId}/submit', [LearnerCourseController::class, 'assignmentSubmit'])->name('assignment.submit');

    // Quiz
    Route::get('/course/{slug}/quiz/{quizId}', [LearnerQuizController::class, 'show'])->name('quiz.show');
    Route::post('/course/{slug}/quiz/{quizId}', [LearnerQuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/course/{slug}/quiz/{quizId}/result/{attemptId}', [LearnerQuizController::class, 'result'])->name('quiz.result');

    // Commandes & Factures (Orders)
    Route::get('/orders', [LearnerDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{id}', [LearnerDashboardController::class, 'orderShow'])->name('orders.show');
});

// ═══════════════════════════════════════════════════════════════════════
// ROUTES FORMATEUR
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'verified', 'role:formateur,administrateur'])->prefix('instructor')->name('instructor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');

    // Statistiques & Rapports
    Route::get('/statistics', [\App\Http\Controllers\Instructor\StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/courses/{courseId}/statistics', [\App\Http\Controllers\Instructor\StatisticsController::class, 'courseStats'])->name('courses.statistics');
    Route::get('/courses/{courseId}/export-csv', [\App\Http\Controllers\Instructor\ReportController::class, 'exportCsv'])->name('courses.export-csv');
    Route::get('/courses/{courseId}/export-pdf', [\App\Http\Controllers\Instructor\ReportController::class, 'exportPdf'])->name('courses.export-pdf');

    // Cours (Wizard + Onglets)
    Route::get('/courses', [\App\Http\Controllers\Instructor\CourseController::class, 'index'])->name('courses');
    Route::get('/courses/create', [\App\Http\Controllers\Instructor\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\Instructor\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [\App\Http\Controllers\Instructor\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [\App\Http\Controllers\Instructor\CourseController::class, 'update'])->name('courses.update');
    Route::post('/courses/{course}/autosave', [\App\Http\Controllers\Instructor\CourseController::class, 'autosave'])->name('courses.autosave');
    Route::post('/courses/{course}/publish', [\App\Http\Controllers\Instructor\CourseController::class, 'publish'])->name('courses.publish');
    Route::post('/courses/{course}/duplicate', [\App\Http\Controllers\Instructor\CourseController::class, 'duplicate'])->name('courses.duplicate');
    Route::post('/courses/{course}/version', [\App\Http\Controllers\Instructor\CourseController::class, 'incrementVersion'])->name('courses.version');
    Route::post('/courses/{course}/archive', [\App\Http\Controllers\Instructor\CourseController::class, 'archive'])->name('courses.archive');
    Route::post('/courses/{course}/restore', [\App\Http\Controllers\Instructor\CourseController::class, 'restore'])->name('courses.restore');
    Route::delete('/courses/{course}', [\App\Http\Controllers\Instructor\CourseController::class, 'destroy'])->name('courses.delete');
    
    // Ajax cascades
    Route::get('/categories/{categoryId}/subcategories', [\App\Http\Controllers\Instructor\CourseController::class, 'getSubcategories']);

    // Modules
    Route::post('/courses/{courseId}/modules', [\App\Http\Controllers\Instructor\ModuleController::class, 'store'])->name('modules.store');
    Route::put('/courses/{courseId}/modules/{moduleId}', [\App\Http\Controllers\Instructor\ModuleController::class, 'update'])->name('modules.update');
    Route::post('/courses/{courseId}/modules/reorder', [\App\Http\Controllers\Instructor\ModuleController::class, 'reorder'])->name('modules.reorder');
    Route::post('/courses/{courseId}/modules/{moduleId}/duplicate', [\App\Http\Controllers\Instructor\ModuleController::class, 'duplicate'])->name('modules.duplicate');
    Route::delete('/courses/{courseId}/modules/{moduleId}', [\App\Http\Controllers\Instructor\ModuleController::class, 'destroy'])->name('modules.delete');

    // Leçons
    Route::post('/courses/{courseId}/modules/{moduleId}/lessons', [\App\Http\Controllers\Instructor\LessonController::class, 'store'])->name('lessons.store');
    Route::get('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}/edit', [\App\Http\Controllers\Instructor\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}', [\App\Http\Controllers\Instructor\LessonController::class, 'update'])->name('lessons.update');
    Route::post('/courses/{courseId}/modules/{moduleId}/lessons/reorder', [\App\Http\Controllers\Instructor\LessonController::class, 'reorder'])->name('lessons.reorder');
    Route::post('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}/duplicate', [\App\Http\Controllers\Instructor\LessonController::class, 'duplicate'])->name('lessons.duplicate');
    Route::get('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}/preview', [\App\Http\Controllers\Instructor\LessonController::class, 'preview'])->name('lessons.preview');
    Route::delete('/courses/{courseId}/modules/{moduleId}/lessons/{lessonId}', [\App\Http\Controllers\Instructor\LessonController::class, 'destroy'])->name('lessons.delete');

    // Bibliothèque de médias (Resources)
    Route::get('/resources/library', [\App\Http\Controllers\Instructor\ResourceController::class, 'library'])->name('resources.library');
    Route::post('/resources/upload', [\App\Http\Controllers\Instructor\ResourceController::class, 'upload'])->name('resources.upload');
    Route::delete('/resources/{id}', [\App\Http\Controllers\Instructor\ResourceController::class, 'destroy'])->name('resources.delete');

    // Quiz
    Route::post('/courses/{courseId}/quiz', [\App\Http\Controllers\Instructor\QuizController::class, 'store'])->name('quiz.store');
    Route::get('/courses/{courseId}/quiz/{quizId}/edit', [\App\Http\Controllers\Instructor\QuizController::class, 'edit'])->name('quiz.edit');
    Route::put('/courses/{courseId}/quiz/{quizId}', [\App\Http\Controllers\Instructor\QuizController::class, 'update'])->name('quiz.update');
    Route::post('/courses/{courseId}/quiz/{quizId}/duplicate', [\App\Http\Controllers\Instructor\QuizController::class, 'duplicate'])->name('quiz.duplicate');
    Route::get('/courses/{courseId}/quiz/{quizId}/results', [\App\Http\Controllers\Instructor\QuizController::class, 'results'])->name('quiz.results');
    Route::delete('/courses/{courseId}/quiz/{quizId}', [\App\Http\Controllers\Instructor\QuizController::class, 'destroy'])->name('quiz.delete');

    // Questions
    Route::post('/quizzes/{quizId}/questions', [\App\Http\Controllers\Instructor\QuestionController::class, 'store'])->name('questions.store');
    Route::put('/quizzes/{quizId}/questions/{questionId}', [\App\Http\Controllers\Instructor\QuestionController::class, 'update'])->name('questions.update');
    Route::post('/quizzes/{quizId}/questions/reorder', [\App\Http\Controllers\Instructor\QuestionController::class, 'reorder'])->name('questions.reorder');
    Route::post('/quizzes/{quizId}/questions/{questionId}/duplicate', [\App\Http\Controllers\Instructor\QuestionController::class, 'duplicate'])->name('questions.duplicate');
    Route::delete('/quizzes/{quizId}/questions/{questionId}', [\App\Http\Controllers\Instructor\QuestionController::class, 'destroy'])->name('questions.delete');

    // Devoirs (Assignments)
    Route::post('/courses/{courseId}/assignments', [\App\Http\Controllers\Instructor\AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/courses/{courseId}/assignments/{assignmentId}/edit', [\App\Http\Controllers\Instructor\AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/courses/{courseId}/assignments/{assignmentId}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'update'])->name('assignments.update');
    Route::post('/courses/{courseId}/assignments/{assignmentId}/duplicate', [\App\Http\Controllers\Instructor\AssignmentController::class, 'duplicate'])->name('assignments.duplicate');
    Route::delete('/courses/{courseId}/assignments/{assignmentId}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'destroy'])->name('assignments.delete');

    // Soumissions de devoirs (Submissions)
    Route::get('/courses/{courseId}/assignments/{assignmentId}/submissions', [\App\Http\Controllers\Instructor\SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/courses/{courseId}/assignments/{assignmentId}/submissions/{submissionId}', [\App\Http\Controllers\Instructor\SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/courses/{courseId}/assignments/{assignmentId}/submissions/{submissionId}/grade', [\App\Http\Controllers\Instructor\SubmissionController::class, 'grade'])->name('submissions.grade');
    Route::post('/courses/{courseId}/assignments/{assignmentId}/submissions/{submissionId}/reject', [\App\Http\Controllers\Instructor\SubmissionController::class, 'reject'])->name('submissions.reject');
    Route::post('/courses/{courseId}/assignments/{assignmentId}/submissions/bulk-grade', [\App\Http\Controllers\Instructor\SubmissionController::class, 'bulkGrade'])->name('submissions.bulk-grade');
    Route::get('/courses/{courseId}/assignments/{assignmentId}/submissions-export', [\App\Http\Controllers\Instructor\SubmissionController::class, 'export'])->name('submissions.export');

    // Cohortes & Sessions
    Route::get('/courses/{courseId}/sessions', [\App\Http\Controllers\Instructor\SessionController::class, 'index'])->name('sessions.index');
    Route::get('/courses/{courseId}/sessions/create', [\App\Http\Controllers\Instructor\SessionController::class, 'create'])->name('sessions.create');
    Route::post('/courses/{courseId}/sessions', [\App\Http\Controllers\Instructor\SessionController::class, 'store'])->name('sessions.store');
    Route::get('/courses/{courseId}/sessions/{sessionId}', [\App\Http\Controllers\Instructor\SessionController::class, 'show'])->name('sessions.show');
    Route::post('/courses/{courseId}/sessions/{sessionId}/add-student', [\App\Http\Controllers\Instructor\SessionController::class, 'addStudent'])->name('sessions.add-student');
    Route::delete('/courses/{courseId}/sessions/{sessionId}/remove-student/{enrollmentId}', [\App\Http\Controllers\Instructor\SessionController::class, 'removeStudent'])->name('sessions.remove-student');
    Route::post('/courses/{courseId}/sessions/{sessionId}/close', [\App\Http\Controllers\Instructor\SessionController::class, 'close'])->name('sessions.close');

    // Suivi apprenants (StudentProgress)
    Route::get('/courses/{courseId}/students', [\App\Http\Controllers\Instructor\StudentProgressController::class, 'index'])->name('students');
    Route::get('/courses/{courseId}/students/{studentId}', [\App\Http\Controllers\Instructor\StudentProgressController::class, 'show'])->name('students.show');
    Route::post('/courses/{courseId}/students/bulk-action', [\App\Http\Controllers\Instructor\StudentProgressController::class, 'bulkAction'])->name('students.bulk-action');
    Route::get('/courses/{courseId}/students/{studentId}/report-pdf', [\App\Http\Controllers\Instructor\StudentProgressController::class, 'exportPdf'])->name('students.export-pdf');

    // Certificats (Certificates)
    Route::get('/courses/{courseId}/certificates', [\App\Http\Controllers\Instructor\CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/courses/{courseId}/certificates/generate/{studentId}', [\App\Http\Controllers\Instructor\CertificateController::class, 'generate'])->name('certificates.generate');
    Route::post('/courses/{courseId}/certificates/{certificateId}/revoke', [\App\Http\Controllers\Instructor\CertificateController::class, 'revoke'])->name('certificates.revoke');

    // Forum de modération
    Route::get('/courses/{courseId}/forum', [\App\Http\Controllers\Instructor\ForumController::class, 'index'])->name('forum.index');
    Route::get('/courses/{courseId}/forum/{threadId}', [\App\Http\Controllers\Instructor\ForumController::class, 'show'])->name('forum.show');
    Route::post('/courses/{courseId}/forum/{threadId}/pin', [\App\Http\Controllers\Instructor\ForumController::class, 'pin'])->name('forum.pin');
    Route::post('/courses/{courseId}/forum/{threadId}/lock', [\App\Http\Controllers\Instructor\ForumController::class, 'lock'])->name('forum.lock');
    Route::post('/courses/{courseId}/forum/{threadId}/reply', [\App\Http\Controllers\Instructor\ForumController::class, 'reply'])->name('forum.reply');
    Route::delete('/courses/{courseId}/forum/{threadId}', [\App\Http\Controllers\Instructor\ForumController::class, 'destroyThread'])->name('forum.destroy-thread');
    Route::delete('/courses/{courseId}/forum/{threadId}/posts/{postId}', [\App\Http\Controllers\Instructor\ForumController::class, 'destroyPost'])->name('forum.destroy-post');

    // Messagerie 1-to-1 & Annonces
    Route::get('/messages', [\App\Http\Controllers\Instructor\MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/send', [\App\Http\Controllers\Instructor\MessageController::class, 'send'])->name('messages.send');
    Route::get('/courses/{courseId}/announcements', [\App\Http\Controllers\Instructor\MessageController::class, 'announcements'])->name('announcements.index');
    Route::post('/courses/{courseId}/announcements', [\App\Http\Controllers\Instructor\MessageController::class, 'postAnnouncement'])->name('announcements.store');
    Route::delete('/courses/{courseId}/announcements/{announcementId}', [\App\Http\Controllers\Instructor\MessageController::class, 'destroyAnnouncement'])->name('announcements.delete');

    // Notifications in-app
    Route::get('/notifications', [\App\Http\Controllers\Instructor\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Instructor\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Instructor\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Instructor\NotificationController::class, 'destroy'])->name('notifications.delete');
    Route::post('/notifications/settings', [\App\Http\Controllers\Instructor\NotificationController::class, 'saveSettings'])->name('notifications.settings');

    // Profil public & perso
    Route::get('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Instructor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/security', [\App\Http\Controllers\Instructor\ProfileController::class, 'updateSecurity'])->name('profile.security');
    Route::post('/profile/toggle-2fa', [\App\Http\Controllers\Instructor\ProfileController::class, 'toggle2fa'])->name('profile.toggle-2fa');
    Route::post('/profile/logout-others', [\App\Http\Controllers\Instructor\ProfileController::class, 'logoutOtherDevices'])->name('profile.logout-others');
});

// ═══════════════════════════════════════════════════════════════════════
// ROUTES ADMIN
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'verified', 'role:administrateur,gestionnaire'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Index routes
    Route::get('/users', [CrudController::class, 'usersIndex'])->name('users.index');
    Route::get('/courses', [CrudController::class, 'coursesIndex'])->name('courses.index');
    Route::get('/categories', [CrudController::class, 'categoriesIndex'])->name('categories.index');
    Route::get('/quizzes', [CrudController::class, 'quizzesIndex'])->name('quizzes.index');
    Route::get('/enrollments', [CrudController::class, 'enrollmentsIndex'])->name('enrollments.index');
    Route::get('/enrollments/export', [CrudController::class, 'enrollmentsExport'])->name('enrollments.export');
    Route::get('/partners', [CrudController::class, 'partnersIndex'])->name('partners.index');
    Route::get('/payments', [CrudController::class, 'paymentsIndex'])->name('payments.index');
    Route::get('/orders', [CrudController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/coupons', [CrudController::class, 'couponsIndex'])->name('coupons.index');
    Route::get('/certificates', [CrudController::class, 'certificatesIndex'])->name('certificates.index');
    Route::get('/pages', [CrudController::class, 'pagesIndex'])->name('pages.index');
    Route::get('/blog', [CrudController::class, 'blogIndex'])->name('blog.index');
    Route::get('/banners', [CrudController::class, 'bannersIndex'])->name('banners.index');
    Route::get('/faqs', [CrudController::class, 'faqsIndex'])->name('faqs.index');
    Route::get('/audit-logs', [CrudController::class, 'auditLogsIndex'])->name('audit-logs.index');
    Route::get('/settings', [CrudController::class, 'settingsIndex'])->name('settings.index');
    Route::get('/reports', [CrudController::class, 'reportsIndex'])->name('reports.index');
    Route::post('/reports/export', [CrudController::class, 'reportsExport'])->name('reports.export');

    // Advanced Users
    Route::post('/users/bulk', [CrudController::class, 'usersBulkAction'])->name('users.bulk');
    Route::get('/users/export/advanced', [CrudController::class, 'usersExportAdvanced'])->name('users.export.advanced');
    Route::get('/users/{id}', [CrudController::class, 'userShow'])->name('users.show');
    Route::post('/users/{id}/permissions', [CrudController::class, 'userPermissionsOverride'])->name('users.permissions');
    Route::post('/users/{id}/logout-sessions', [CrudController::class, 'userLogoutSessions'])->name('users.logout-sessions');
    Route::post('/users/{id}/status', [CrudController::class, 'userChangeStatus'])->name('users.status');

    // Advanced Courses
    Route::get('/courses/{id}', [CrudController::class, 'courseShow'])->name('courses.show');
    Route::post('/courses/bulk', [CrudController::class, 'coursesBulkAction'])->name('courses.bulk');
    Route::get('/courses/{id}/report', [CrudController::class, 'courseReportPdf'])->name('courses.report');

    // Sessions Cohortes
    Route::resource('sessions', \App\Http\Controllers\Admin\SessionCohortController::class);
    Route::post('/sessions/{id}/close', [\App\Http\Controllers\Admin\SessionCohortController::class, 'close'])->name('sessions.close');
    Route::post('/sessions/{id}/duplicate', [\App\Http\Controllers\Admin\SessionCohortController::class, 'duplicate'])->name('sessions.duplicate');

    // CRUD Store/Update/Delete
    Route::post('/users', [CrudController::class, 'userStore'])->name('users.store');
    Route::put('/users/{id}', [CrudController::class, 'userUpdate'])->name('users.update');
    Route::delete('/users/{id}', [CrudController::class, 'userDelete'])->name('users.delete');

    Route::post('/categories', [CrudController::class, 'categoryStore'])->name('categories.store');
    Route::put('/categories/{id}', [CrudController::class, 'categoryUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [CrudController::class, 'categoryDelete'])->name('categories.delete');

    Route::post('/courses', [CrudController::class, 'courseStore'])->name('courses.store');
    Route::post('/courses/{id}/publish', [CrudController::class, 'coursePublish'])->name('courses.publish');
    Route::post('/courses/{id}/duplicate', [CrudController::class, 'duplicateCourse'])->name('courses.duplicate');
    Route::post('/courses/{id}/version', [CrudController::class, 'incrementVersion'])->name('courses.version');
    Route::delete('/courses/{id}', [CrudController::class, 'courseDelete'])->name('courses.delete');

    Route::post('/enrollments', [CrudController::class, 'enrollmentStore'])->name('enrollments.store');
    Route::put('/enrollments/{id}', [CrudController::class, 'enrollmentUpdate'])->name('enrollments.update');
    Route::delete('/enrollments/{id}', [CrudController::class, 'enrollmentDelete'])->name('enrollments.delete');

    Route::post('/coupons', [CrudController::class, 'couponStore'])->name('coupons.store');
    Route::delete('/coupons/{id}', [CrudController::class, 'couponDelete'])->name('coupons.delete');

    Route::post('/pages', [CrudController::class, 'pageStore'])->name('pages.store');
    Route::put('/pages/{id}', [CrudController::class, 'pageUpdate'])->name('pages.update');
    Route::delete('/pages/{id}', [CrudController::class, 'pageDelete'])->name('pages.delete');

    Route::post('/blog', [CrudController::class, 'blogStore'])->name('blog.store');
    Route::put('/blog/{id}', [CrudController::class, 'blogUpdate'])->name('blog.update');
    Route::delete('/blog/{id}', [CrudController::class, 'blogDelete'])->name('blog.delete');

    Route::post('/banners', [CrudController::class, 'bannerStore'])->name('banners.store');
    Route::delete('/banners/{id}', [CrudController::class, 'bannerDelete'])->name('banners.delete');

    Route::post('/faqs', [CrudController::class, 'faqStore'])->name('faqs.store');
    Route::put('/faqs/{id}', [CrudController::class, 'faqUpdate'])->name('faqs.update');
    Route::delete('/faqs/{id}', [CrudController::class, 'faqDelete'])->name('faqs.delete');

    Route::put('/settings', [CrudController::class, 'settingsUpdate'])->name('settings.update');

    Route::post('/certificates/{id}/generate', [CrudController::class, 'certificateGenerate'])->name('certificates.generate');
    Route::post('/certificates/{id}/revoke', [CrudController::class, 'certificateRevoke'])->name('certificates.revoke');
});

// ═══════════════════════════════════════════════════════════════════════
// ROUTES PAIEMENT CINETPAY
// ═══════════════════════════════════════════════════════════════════════

Route::middleware('auth')->get('/checkout/{slug}', [PaymentController::class, 'checkout'])->name('checkout');
Route::middleware('auth')->post('/payment/{slug}/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::post('/payment/notify', [PaymentController::class, 'notify'])->name('payment.notify')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class ?? \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/payment/success/{orderId}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/return', [PaymentController::class, 'return'])->name('payment.return');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

// Auth routes (Laravel Breeze)
require __DIR__.'/auth.php';
