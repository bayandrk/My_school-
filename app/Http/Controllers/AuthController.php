<?php

namespace App\Http\Controllers;

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
            'phone'       => 'required|numeric',
            'school_name' => 'nullable|string|max:255',
        ]);

        // ولي الأمر مفعل فوراً، بينما المدير والمعلم ينتظرون الموافقة
        $status = ($validated['role'] === 'parent') ? 'approved' : 'pending';

        $user = User::create([
            'name'        => $validated['name'],
            'username'    => $validated['username'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'phone'       => $validated['phone'],
            'school_name' => $validated['school_name'] ?? null,
            'status'      => $status,
        ]);

        if ($user->status === 'pending') {
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