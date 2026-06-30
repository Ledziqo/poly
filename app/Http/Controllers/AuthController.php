<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function signup(): View
    {
        return view('auth.signup');
    }

    public function storeSignup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('subscription.required');
    }

    public function storeLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($this->isAdminLogin($credentials['email'], $credentials['password'])) {
            try {
                DB::table('users')->updateOrInsert(
                    ['email' => $this->adminEmail()],
                    [
                        'name' => 'Aesliex',
                        'password' => Hash::make($this->adminPassword()),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $adminId = DB::table('users')->where('email', $this->adminEmail())->value('id');

                if (! $adminId) {
                    throw new \RuntimeException('Admin user was not found after create/update.');
                }
            } catch (Throwable $exception) {
                throw ValidationException::withMessages([
                    'email' => 'Admin login setup failed: '.$exception->getMessage(),
                ]);
            }

            Auth::loginUsingId($adminId);
            $request->session()->regenerate();
            $request->session()->put('has_tool_access', true);

            return redirect()->route('dashboard');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('subscription.required');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    public function subscriptionRequired(): View
    {
        return view('auth.subscription', [
            'telegram' => $this->telegram(),
        ]);
    }

    public function health(): \Illuminate\Http\Response
    {
        $checks = [];

        foreach (['users', 'sessions', 'portfolios', 'bot_settings', 'markets', 'market_outcomes', 'ai_signals'] as $table) {
            try {
                $checks[] = $table.': '.(DB::getSchemaBuilder()->hasTable($table) ? 'ok' : 'missing');
            } catch (Throwable $exception) {
                $checks[] = $table.': error - '.$exception->getMessage();
            }
        }

        try {
            $checks[] = 'admin user: '.(DB::table('users')->where('email', $this->adminEmail())->exists() ? 'ok' : 'missing');
        } catch (Throwable $exception) {
            $checks[] = 'admin user: error - '.$exception->getMessage();
        }

        return response(implode("\n", $checks), 200)->header('Content-Type', 'text/plain');
    }

    private function isAdminLogin(string $email, string $password): bool
    {
        return strcasecmp($email, $this->adminEmail()) === 0
            && hash_equals($this->adminPassword(), $password);
    }

    private function adminEmail(): string
    {
        return config('polyengine.admin_email') ?: 'Aesliexx@gmail.com';
    }

    private function adminPassword(): string
    {
        return config('polyengine.admin_password') ?: 'Mudi2005';
    }

    private function telegram(): string
    {
        return config('polyengine.telegram') ?: '@Aesliex';
    }
}
