<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SetupController extends Controller
{
    private array $commands = [
        'migrate' => ['label' => 'Run database migrations', 'command' => 'migrate', 'params' => ['--force' => true]],
        'sync-markets' => ['label' => 'Sync Polymarket markets', 'command' => 'poly:sync-markets', 'params' => ['--limit' => 100]],
        'sync-orderbooks' => ['label' => 'Sync order books', 'command' => 'poly:sync-orderbooks', 'params' => ['--limit' => 150]],
        'score-signals' => ['label' => 'Score AI opportunities', 'command' => 'poly:score-signals', 'params' => ['--limit' => 300]],
        'run-bot' => ['label' => 'Run paper bot once', 'command' => 'poly:run-bot', 'params' => []],
        'refresh-portfolio' => ['label' => 'Refresh portfolio PnL', 'command' => 'poly:refresh-portfolio', 'params' => []],
        'optimize-clear' => ['label' => 'Clear Laravel cache', 'command' => 'optimize:clear', 'params' => []],
        'create-admin' => ['label' => 'Create/update admin login', 'command' => null, 'params' => []],
        'config-cache' => ['label' => 'Cache production config', 'command' => 'config:cache', 'params' => []],
    ];

    public function index(Request $request): View|RedirectResponse
    {
        if (! $this->allowed($request)) {
            return view('setup.locked');
        }

        return view('setup.index', [
            'commands' => $this->commands,
            'token' => $request->query('token'),
            'lastOutput' => session('setup_output'),
            'lastStatus' => session('setup_status'),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        abort_unless($this->allowed($request), 403);

        $data = $request->validate([
            'action' => ['required', 'string'],
        ]);

        abort_unless(isset($this->commands[$data['action']]), 404);

        $definition = $this->commands[$data['action']];

        if ($data['action'] === 'create-admin') {
            $email = config('polyengine.admin_email') ?: 'Aesliexx@gmail.com';
            $password = config('polyengine.admin_password') ?: 'Mudi2005';

            User::updateOrCreate(
                ['email' => $email],
                ['name' => 'Aesliex', 'password' => Hash::make($password)]
            );

            return redirect()
                ->route('setup.index', ['token' => $request->query('token') ?: $request->input('token')])
                ->with('setup_status', 'Admin login created or updated.')
                ->with('setup_output', "Admin email: {$email}\nTool access: enabled after login.");
        }

        $status = Artisan::call($definition['command'], $definition['params']);

        return redirect()
            ->route('setup.index', ['token' => $request->query('token') ?: $request->input('token')])
            ->with('setup_status', $status === 0 ? 'Command completed.' : "Command exited with status {$status}.")
            ->with('setup_output', trim(Artisan::output()) ?: 'No command output.');
    }

    private function allowed(Request $request): bool
    {
        if (! config('setup.enabled')) {
            return false;
        }

        $token = config('setup.token');

        if (! $token) {
            return app()->environment('local');
        }

        return hash_equals($token, (string) $request->query('token', $request->input('token', '')));
    }
}
