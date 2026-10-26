<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:255|unique:users',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:8|confirmed',
            'role'        => 'required|in:manager,teacher,parent',
            'phone'       => 'required|numeric|digits_between:8,15|unique:users,phone',
            'school_name' => 'nullable|string|max:255',
        ], [
            // رسائل تحقق عربية واضحة (تظهر فوق النموذج دون كسر الصفحة)
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric'  => 'رقم الهاتف يجب أن يحتوي على أرقام فقط.',
            'phone.unique'   => 'هذا الرقم مسجّل مسبقاً، استخدمي رقماً آخر.',
            'phone.digits_between' => 'رقم الهاتف غير صحيح: يجب أن يتكوّن من 8 إلى 15 رقماً دون مسافات أو رموز. مثال: 0932123456 أو 963932123456.',
            'username.unique' => 'اسم المستخدم موجود مسبقاً، اختر اسماً آخر.',
            'email.unique'    => 'البريد الإلكتروني مسجّل مسبقاً.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
        ]);

        // ولي الأمر مفعل فوراً، بينما المدير والمعلم ينتظرون الموافقة
        $status = ($validated['role'] === 'parent') ? 'approved' : 'pending';

        // المدرسة هي الكيان المركزي: عند تسجيل مدير جديد تُنشأ مدرسة جديدة
        // ويرتبط بها كل من يسجل بنفس اسم المدرسة (مدير/معلم/ولي أمر).
        $school = null;

        if (!empty($validated['school_name'])) {
            $school = School::firstOrCreate(
                ['name' => $validated['school_name']],
                ['name' => $validated['school_name']]
            );
        }

        $user = User::create([
            'name'        => $validated['name'],
            'username'    => $validated['username'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'phone'       => $validated['phone'],
            'school_id'   => $school?->id,
            'status'      => $status,
        ]);

        if ($user->status === 'pending') {
            // إرسال إشعار للأدمن أو للـ manager إذا كان المعلم قد سجل في مدرسة معينة
            if ($user->role === 'teacher' && $school) {
                $manager = User::where('school_id', $school->id)->where('role', 'manager')->first();
                if ($manager) {
                    try {
                        $manager->notify(new \App\Notifications\ManagerJoinRequestNotification($user->name, 'teacher'));
                    } catch (\Exception $e) {}
                }
            } elseif ($user->role === 'manager') {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    try {
                        $admin->notify(new \App\Notifications\AdminManagerRequestNotification());
                    } catch (\Exception $e) {}
                }
            }

            return redirect()->route('login')->with('success', 'تم إنشاء الحساب بنجاح، وهو بانتظار موافقة المسؤول.');
        }

        Auth::login($user);
        return redirect()->route('parent.dashboard')->with('success', 'تم إنشاء الحساب وتسجيل الدخول بنجاح.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // فحص حالات الحساب المعطلة أو المعلقة أو المرفوضة
            if (in_array($user->status, ['pending', 'rejected', 'deactivated'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = match ($user->status) {
                    'pending'     => 'حسابك قيد الانتظار لموافقة الإدارة.',
                    'deactivated' => 'حسابك معطل حالياً من قبل الإدارة.',
                    'rejected'    => 'تم رفض طلب تسجيل هذا الحساب.',
                    default       => 'لا يمكنك الدخول بهذا الحساب.',
                };

                return redirect()->route('login')->with('error', $message);
            }

            // تجديد المعرف الخاص بالجلسة لحماية الحساب (Session Fixation Protection)
            $request->session()->regenerate();

            // توجيه المستخدم حسب الدور
            return match ($user->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'manager' => redirect()->route('manager.dashboard'),
                'teacher' => redirect()->route('teacher.dashboard'),
                'parent'  => redirect()->route('parent.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return back()->with('error', 'اسم المستخدم أو كلمة المرور غير صحيحة.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}