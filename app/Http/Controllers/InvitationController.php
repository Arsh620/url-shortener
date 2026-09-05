<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    // Show the invitation form
    public function create()
    {
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Define which roles each user type is allowed to invite
        // SuperAdmin can only invite Admin (who will head a new company)
        // Admin can invite Admin or Member into their own company
        $allowedRoles = match($user->role) {
            'superadmin' => ['admin'],
            'admin'      => ['admin', 'member'],
            default      => [],
        };

        // If no allowed roles, this user cannot invite anyone
        if (empty($allowedRoles)) {
            abort(403);
        }

        $request->validate([
            'email'        => 'required|email|unique:users,email|unique:invitations,email',
            'role'         => 'required|in:' . implode(',', $allowedRoles),
            // SuperAdmin must provide a new company name since they are creating a new company
            'company_name' => $user->role === 'superadmin' ? 'required|string|max:255|unique:companies,name' : 'nullable',
        ]);

        // SuperAdmin creates a new company when inviting an Admin
        // Admin invites into their existing company
        if ($user->role === 'superadmin') {
            $company = Company::create(['name' => $request->company_name]);
            $companyId = $company->id;
        } else {
            $companyId = $user->company_id;
        }

        // Generate a secure random token valid for 7 days
        $invitation = Invitation::create([
            'email'      => $request->email,
            'role'       => $request->role,
            'company_id' => $companyId,
            'token'      => Str::random(32),
            'invited_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);


        return back()->with('success', 'Invitation sent successfully!')
                     ->with('invitation_link', url('/invite/accept/' . $invitation->token));
    }

    // Show the registration form for the invited user
    public function accept($token)
    {
        // Ensure the token exists and has not expired
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function register(Request $request, $token)
    {
        // Re-validate token on registration to prevent expired token abuse
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        // Create the user with role and company assigned from the invitation
        // invited_by tracks who sent the invitation
        $user = User::create([
            'name'       => $request->name,
            'email'      => $invitation->email,
            'password'   => bcrypt($request->password),
            'role'       => $invitation->role,
            'company_id' => $invitation->company_id,
            'invited_by' => $invitation->invited_by,
        ]);

        // Delete invitation after use so it cannot be reused
        $invitation->delete();

        auth()->login($user);

        return redirect()->route('dashboard');
    }
}
