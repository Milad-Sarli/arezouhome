<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function sendOtp(Request $request, OtpService $otp)
    {
        $request->validate(['phone' => ['required', 'string']]);

        try {
            $otp->send($request->string('phone'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(array_filter([
            'message' => 'کد تأیید ارسال شد.',
            'debug_code' => OtpService::debugCode($request->string('phone')),
        ]));
    }

    public function verifyOtp(Request $request, OtpService $otp)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string'],
        ]);

        try {
            $user = $otp->verify($request->string('phone'), $request->string('code'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'token' => $user->createToken('storefront')->plainTextToken,
            'user' => ['id' => $user->id, 'name' => $user->name, 'phone' => $user->phone],
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
        ]);

        try {
            $phone = OtpService::normalize($data['phone']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (User::where('phone', $phone)->exists()) {
            return response()->json(['message' => 'این شماره قبلاً ثبت شده؛ وارد شو.'], 422);
        }

        $user = User::create([
            'name' => $data['name'],
            'phone' => $phone,
            'email' => $phone.'@arezouhome.test',
            'password' => $data['password'],
            'password_set_at' => now(),
        ]);

        return response()->json([
            'token' => $user->createToken('storefront')->plainTextToken,
            'user' => ['id' => $user->id, 'name' => $user->name, 'phone' => $user->phone],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $phone = OtpService::normalize($data['phone']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $user = User::where('phone', $phone)->first();

        if (! $user || ! $user->password_set_at || ! Hash::check($data['password'], $user->password)) {
            $message = $user && ! $user->password_set_at
                ? 'برای این شماره رمزی ثبت نشده؛ با کد پیامک وارد شو.'
                : 'شماره یا رمز اشتباه است.';

            return response()->json(['message' => $message], 401);
        }

        return response()->json([
            'token' => $user->createToken('storefront')->plainTextToken,
            'user' => ['id' => $user->id, 'name' => $user->name, 'phone' => $user->phone],
        ]);
    }

    public function setPassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $data['password'],
            'password_set_at' => now(),
        ]);

        return response()->json(['message' => 'رمز عبور ثبت شد.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $user->update(['name' => $data['name']]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'خارج شدید.']);
    }
}
