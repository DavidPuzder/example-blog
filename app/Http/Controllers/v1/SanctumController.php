<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Nova\Http\Controllers\ForgotPasswordController;

/**
 * @group Authentication
 */
class SanctumController extends Controller
{
    /**
     * Register
     *
     * Endpoint for creating new users.
     *
     * @queryParam name required string Username. Example: John Sky
     * @queryParam email required string User email. Example: johnsky@example.net
     * @queryParam password required string User password. Example: password123
     * @queryParam password_confirmation required string User password confirmation. Example: password123
     *
     * @param Request $request
     * @return array|void
     */
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|min:3|max:75',
            'email' => 'required|email|unique:users,email|string|min:3|max:75',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if ($user) {
            $userToken = $user->createToken('app-token', ['*'])->plainTextToken;
            $data['user'] = new UserResource($user);
            $data['token'] = $userToken;
            return $data;
        }
    }

    /**
     * Login
     *
     * Endpoint for logging existing users.
     *
     * @queryParam email required string User email. Example: johnsky@example.net
     * @queryParam password required string User password. Example: password123
     *
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse|array
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where(['email' => $request->email])->first();

        if ($user) {
            $validated = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
            if ($validated) {
                $userToken = $user->createToken('app-token', ['*'])->plainTextToken;
                $data['user'] = new UserResource($user);
                $data['token'] = $userToken;
                return $data;
            }
        }

        return response()->json([
            'code' => 403,
            'message' => __('Bad credentials.')
        ]);
    }

    /**
     * Change Password
     *
     * Endpoint for sending link to reset password.
     *
     * @queryParam email required string User email. Example: johnsky@example.net
     *
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email|string|min:3|max:75'
        ]);
        $sendForgotPasswordLink = new ForgotPasswordController();
        $sendForgotPasswordLink->sendResetLinkEmail($request);
    }
}
