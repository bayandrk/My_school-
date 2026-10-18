<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;

// ==========================================
// 1. مسارات الضيوف (Guest Routes)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==========================================
// 2. مسارات المسجلين (Authenticated Routes)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --------------------------------------
    // أ. مسارات الأدمن (Admin)
    // --------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/managers/deactivated', [AdminController::class, 'deactivatedManagers'])->name('managers.deactivated');
        Route::patch('/managers/{manager}/approve', [AdminController::class, 'approveManager'])->name('managers.approve');
        Route::delete('/managers/{manager}/reject', [AdminController::class, 'rejectManager'])->name('managers.reject');
        Route::patch('/managers/{manager}/deactivate', [AdminController::class, 'deactivateManager'])->name('managers.deactivate');
        Route::patch('/managers/{manager}/reactivate', [AdminController::class, 'reactivateManager'])->name('managers.reactivate');
        Route::delete('/managers/{manager}', [AdminController::class, 'destroyManager'])->name('managers.destroy');
    });

    // --------------------------------------
    // ب. مسارات المدير (Manager)
    // --------------------------------------
    Route::prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
        Route::patch('/teachers/{teacher}/deactivate', [ManagerController::class, 'deactivateTeacher'])->name('teachers.deactivate');
        Route::patch('/teachers/{teacher}/reactivate', [ManagerController::class, 'reactivateTeacher'])->name('teachers.reactivate');
        Route::delete('/teachers/{teacher}/reject', [ManagerController::class, 'rejectTeacher'])->name('teachers.reject');
        Route::get('/profile', [ManagerController::class, 'profile'])->name('profile');
        Route::put('/profile', [ManagerController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/branding', [ManagerController::class, 'updateBranding'])->name('update-branding');
        Route::delete('/profile', [ManagerController::class, 'destroyProfile'])->name('profile.destroy');
        Route::get('/classes', [ManagerController::class, 'classes'])->name('classes');
        Route::get('/teachers', [ManagerController::class, 'teachers'])->name('teachers');
        Route::get('/children/{child}/edit', [ManagerController::class, 'editChild'])->name('children.edit');
        Route::put('/children/{child}', [ManagerController::class, 'updateChild'])->name('children.update');
        Route::delete('/children/{child}', [ManagerController::class, 'destroyChild'])->name('children.destroy');
        Route::delete('/teachers/{teacher}', [ManagerController::class, 'destroyTeacher'])->name('teachers.destroy');
        Route::get('/class-scores', [ManagerController::class, 'classScores'])->name('class.scores');
        Route::get('/class-notes', [ManagerController::class, 'classNotes'])->name('class.notes');
        Route::get('/teachers/deactivated', [ManagerController::class, 'deactivatedTeachers'])->name('teachers.deactivated');
        Route::patch('/children/{child}/archive', [ManagerController::class, 'archiveChild'])->name('children.archive');
        Route::patch('/children/{child}/restore', [ManagerController::class, 'restoreChild'])->name('children.restore');
        Route::get('/children/archived', [ManagerController::class, 'archivedChildren'])->name('children.archived');
        Route::get('/assignments', [ManagerController::class, 'assignmentsIndex'])->name('assignments');
        Route::post('/assign-teacher-class', [ManagerController::class, 'assignTeacherToClass'])->name('assign-teacher-class');
        Route::post('/unassign-teacher-class', [ManagerController::class, 'unassignTeacherClass'])->name('unassign-teacher-class');
        Route::patch('/teachers/{teacher}/approve', [ManagerController::class, 'approveTeacher'])->name('teachers.approve');
    });

    // --------------------------------------
    // ج. مسارات المعلم (Teacher)
// --------------------------------------
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
    Route::put('/profile', [TeacherController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [TeacherController::class, 'destroyProfile'])->name('profile.destroy');

    Route::get('/children/create', [TeacherController::class, 'createChild'])->name('children.create');
    Route::post('/children', [TeacherController::class, 'storeChild'])->name('children.store');
    Route::get('/children/{child}/edit', [TeacherController::class, 'editChild'])->name('children.edit');
    Route::put('/children/{child}', [TeacherController::class, 'updateChild'])->name('children.update');
    Route::delete('/children/{child}', [TeacherController::class, 'destroyChild'])->name('children.destroy');

    Route::get('/classes', [TeacherController::class, 'classes'])->name('classes');
    Route::get('/daily-changes', [TeacherController::class, 'dailyChanges'])->name('daily-changes');
    Route::get('/records', [TeacherController::class, 'dailyChanges'])->name('records');
    
    Route::post('/daily-changes', [TeacherController::class, 'storeDailyChange'])->name('daily-changes.store');
    Route::put('/daily-changes/{dailyChange}', [TeacherController::class, 'updateDailyChange'])->name('daily-changes.update');
    Route::delete('/daily-changes/{dailyChange}', [TeacherController::class, 'destroyDailyChange'])->name('daily-changes.destroy');
    Route::get('/student/{student_id}/records', [TeacherController::class, 'studentRecords'])->name('student.records');

    Route::get('/classes/{class_name}/scores', [TeacherController::class, 'classScores'])->name('class.scores');
    Route::get('/classes/{class_name}/notes', [TeacherController::class, 'classNotes'])->name('class.notes');

    // 📩 مسار صفحة ملاحظات الأهل الجديد
    Route::get('/parent-notes', [TeacherController::class, 'parentNotes'])->name('parent-notes');

    // 🔔 مسار قراءة إشعارات المعلم وقراءتها عبر الكنترولر
    Route::get('/notification/{id}/read', [TeacherController::class, 'readNotification'])->name('notification.read');
    Route::get('/parent-notes', [TeacherController::class, 'parentNotes'])->name('parent-notes');
    Route::post('/parent-notes/{child}/reply', [TeacherController::class, 'replyToParentNote'])->name('parent-notes.reply');
}); 
    
    // --------------------------------------
    // د. مسارات ولي الأمر (Parent)
    // --------------------------------------
    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [ParentController::class, 'profile'])->name('profile');
        Route::put('/profile', [ParentController::class, 'updateProfile'])->name('profile.update');
        Route::delete('/profile', [ParentController::class, 'destroyProfile'])->name('profile.destroy');
        Route::get('/children/{child}', [ParentController::class, 'showChild'])->name('child.show');
        Route::put('/children/{child}/parent-note', [ParentController::class, 'updateChildNote'])->name('update-child-note');
        Route::post('/children/{child}/send-teacher-note', [ParentController::class, 'sendTeacherNote'])->name('send-teacher-note');
        Route::get('/parent-notes', [TeacherController::class, 'parentNotes'])->name('parent-notes');
         Route::get('/notifications/{id}/read', [TeacherController::class, 'readNotification'])->name('notification.read');
        //  مسار قراءة إشعارات ولي الأمر (سيصبح اسمه parent.notification.read)
        Route::get('/notification/{id}/read', function ($id) {
            $notification = auth()->user()->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
                return redirect($notification->data['url'] ?? route('parent.dashboard'));
            }
            return redirect()->back();
        })->name('notification.read');
    });
});