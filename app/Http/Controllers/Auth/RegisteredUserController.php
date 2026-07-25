<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'plan' => ['required', 'in:free,starter,business,enterprise'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $plan = $request->plan ?? 'free';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'merchant',
            'plan' => $plan,
            'trial_ends_at' => now()->addDays(14), // Trial pour tous les plans
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan' => $plan,
            'status' => 'active',
            'trial_ends_at' => now()->addDays(14), // Trial pour tous les plans
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('subscription.index');
    }
}
