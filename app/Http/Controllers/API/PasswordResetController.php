<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor_Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Firebase\JWT\JWT;
use Carbon\Carbon;
use Firebase\JWT\Key;
use Illuminate\Validation\Rules\Password;
use App\Traits\Api_Response_Trait;

class PasswordResetController extends Controller
{
    use Api_Response_Trait;

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $doctor = Doctor_Account::where('email', $request->email)->first();

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found.'], 404);
        }

        $payload = [
            'iss' => "laravel-jwt",
            'sub' => $doctor->id,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(60)->timestamp
        ];
        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        $resetLink = url("/api/reset-password?token={$token}");
        Mail::raw("Use this link to reset your password: $resetLink", function($message) use ($request) {
            $message->to($request->email)
                    ->subject('Password Reset Link');
        });

        return response()->json(['message' => 'Reset link sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        try {

            $decoded = JWT::decode($request->token, new Key(env('JWT_SECRET'), 'HS256'));

            $user = Doctor_Account::find($decoded->sub);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'Password has been reset successfully.']);
        } 
        catch (\Exception $e) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }
    }

    public function change_password(Request $request)
    {
        try{
            $validated = $request->validate([
                'current_password' => ['required'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);
    
            if (!Hash::check($validated['current_password'], $request->user()->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 400);
            }
    
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);
    
            return response()->json([
                'message' => 'Password changed successfully'
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while changing the password.'], 400);
        }
    }
}