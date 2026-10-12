<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(Request $request): View|RedirectResponse
    {
        abort_if(config('store.demo_mode'), 404);

        if (Auth::check()) {
            return redirect()->intended(route('home'));
        }

        return view('auth.login', [
            'redirect' => $this->safeRedirect($request->query('redirect')),
            'checkoutRequired' => $request->query('msg') === 'checkout_required',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        abort_if(config('store.demo_mode'), 404);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:4096'],
            'redirect' => ['nullable', 'string', 'max:2048'],
        ]);
        $destination = $this->safeRedirect($data['redirect'] ?? null);

        try {
            $users = User::query()
                ->where('trang_thai', true)
                ->where(static function ($query) use ($data): void {
                    $query->where('ho_ten', $data['username'])
                        ->orWhere('email', strtolower($data['username']));
                })
                ->orderBy('id')
                ->get();
        } catch (QueryException $exception) {
            Log::error('Laravel login users could not be loaded.', [
                'exception' => $exception->getMessage(),
            ]);

            return back()->withInput($request->only('username', 'redirect'))
                ->with('auth_error', 'Chưa thể đăng nhập lúc này. Vui lòng thử lại sau.');
        }

        $user = $users->first(
            static fn (User $candidate): bool => Hash::check($data['password'], $candidate->mat_khau),
        );
        if (! $user) {
            return back()->withInput($request->only('username', 'redirect'))
                ->with('auth_error', 'Tên người dùng hoặc mật khẩu chưa chính xác.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $destination ? redirect()->to($destination) : redirect()->intended(route('home'));
    }

    public function registerForm(Request $request): View|RedirectResponse
    {
        abort_if(config('store.demo_mode'), 404);

        if (Auth::check()) {
            return redirect()->intended(route('home'));
        }

        return view('auth.register', [
            'redirect' => $this->safeRedirect($request->query('redirect')),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        abort_if(config('store.demo_mode'), 404);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+().\-\s]+$/'],
            'password' => ['required', 'string', 'min:8', 'max:4096'],
            'confirm_password' => ['required', 'same:password'],
            'redirect' => ['nullable', 'string', 'max:2048'],
            'terms' => ['accepted'],
        ]);
        $data['email'] = strtolower($data['email']);
        $destination = $this->safeRedirect($data['redirect'] ?? null);

        try {
            if (User::query()->where('ho_ten', $data['username'])->exists()) {
                return back()->withInput($request->except('password', 'confirm_password'))
                    ->withErrors(['username' => 'Tên người dùng này đã được đăng ký.']);
            }

            $user = User::create([
                'ho_ten' => $data['username'],
                'email' => $data['email'],
                'so_dien_thoai' => $data['phone'] ?? null,
                'mat_khau' => $data['password'],
            ]);
        } catch (QueryException $exception) {
            if (str_starts_with((string) $exception->getCode(), '23')) {
                return back()->withInput($request->except('password', 'confirm_password'))
                    ->withErrors(['email' => 'Email này đã được đăng ký.']);
            }
            Log::error('Laravel registration failed.', [
                'exception' => $exception->getMessage(),
            ]);

            return back()->withInput($request->except('password', 'confirm_password'))
                ->with('auth_error', 'Không thể tạo tài khoản lúc này. Vui lòng thử lại.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $destination ? redirect()->to($destination) : redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function safeRedirect(mixed $destination): ?string
    {
        if (! is_string($destination) || $destination === '' || ! str_starts_with($destination, '/')) {
            return null;
        }
        if (
            str_starts_with($destination, '//')
            || str_contains($destination, '\\')
            || preg_match('/[\x00-\x1F\x7F]/', $destination)
        ) {
            return null;
        }

        $parts = parse_url($destination);

        return $parts === false || isset($parts['scheme']) || isset($parts['host'])
            ? null
            : $destination;
    }
}
