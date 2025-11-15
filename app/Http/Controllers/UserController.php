<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRegister;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get all hosts (animateurs)
     */
    public function hosts()
    {
        $hosts = User::where('role', 'animateur')->get();
        return response()->json($hosts);
    }

    /**
     * Get all users
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Show a specific host
     */
    public function show(User $host)
    {
        $this->authorize('view',$host);

        return response()->json($host);
    }

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        $request->validated();

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email ou mot de passe invalide'], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(LoginRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email introuvable'], 404);
        }

        $user->update([
            'password' => $request->password
        ]);

        return response()->json(['message' => 'Mot de passe réinitialisé']);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * Update user
     */
    public function update(UpdateRegister $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update($request->validated());

        return response()->json([
            'message' => 'Utilisateur mis à jour',
            'user' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}
