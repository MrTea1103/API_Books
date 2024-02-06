<?php

namespace App\Http\Controllers;

use App\Mail\DemoMail;
use App\Models\PasswordReset;
use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {


        // Check if user exists
        if (!users::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Create a password reset token
        $token = Str::random(60);
        PasswordReset::create([
            'email' => $request->email,
            'token' => $token,
        ]);

        // Send email with the reset token
        Mail::to($request->email)->send(new DemoMail([
            'title' => 'Mã xác nhận',
            'body' => $token,
        ]));

        return response()->json(['message' => 'Books API gửi bạn mã xác nhận']);
    }

    public function reset(Request $request)
    {

        // Find the user by email
        $user = users::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Find the token in the password resets table
        $passwordReset = PasswordReset::where('email', $request->email)->where('token', $request->token)->first();
        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Check if the password field is empty
        if (!$request->filled('password')) {
            return response()->json(['message' => 'Không để trống password'], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the password reset record
        $passwordReset->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
