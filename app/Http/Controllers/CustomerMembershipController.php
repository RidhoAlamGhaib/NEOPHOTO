<?php

namespace App\Http\Controllers;

use App\Concerns\PasswordValidationRules;
use App\Models\CustomerMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomerMembershipController extends Controller
{
    use PasswordValidationRules;

    public function register(Request $request): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge([
                'email' => Str::lower(trim($request->input('email'))),
            ]);
        }
        if (is_string($request->input('phone'))) {
            $request->merge([
                'phone' => preg_replace('/[^0-9+]/', '', $request->input('phone')),
            ]);
        }

        $validated = $request->validateWithBag('membershipRegistration', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customer_memberships', 'email')],
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{7,20}$/',
                Rule::unique('customer_memberships', 'phone'),
            ],
            'password' => $this->passwordRules(),
        ]);

        $membership = CustomerMembership::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ]);

        Auth::guard('member')->login($membership);
        $request->session()->regenerate();

        return redirect()->route('index');
    }

    public function login(Request $request): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge([
                'email' => Str::lower(trim($request->input('email'))),
            ]);
        }

        $credentials = $request->validateWithBag('membershipLogin', [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('member')->attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The email or password is incorrect.',
            ], 'membershipLogin')->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();
        $request->session()->regenerate();

        return redirect()->route('index');
    }
}
