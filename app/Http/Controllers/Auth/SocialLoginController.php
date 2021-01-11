<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        $this->middleware(['social', 'web']);
    }


    public function redirect($service)
    {
        return Socialite::driver($service)->redirect();
    }

    public function callback($service)
    {
        try {
            $serviceUser = Socialite::driver($service)->user();
        } catch (\Exception $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
        }

        $user = User::where('email', '=', $serviceUser->email)->first();
        if (!$user) {
            $name = $user->name;
            $$userName = $name . uniqid();
            $newUser = User::create([
                'name'        => $name,
                'username'    => $userName,
                'email'       => $user->email,
                'avatar_path' => $user->avatar,
                'password'    => Hash::make(md5(uniqid() . now())),
                'confirmed'   => 1,
                'auth_type'   => $service,
            ]);
        } else {
            if ($user->auth_type != $service) {
                if ($user->auth_type == 'email') {
                    $message = 'This email is already associated with an account, pelase reset your password or login with your email and password below.';
                    return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=' . $message . '&origin=login');
                } else {
                    $message = 'This email has already registered using ' . $service . '. Please login with ' . ucfirst($service) . ', or you may reset your password';

                    return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=' . $message . '&origin=login');
                }

                return response()->json(["errors" => [
                    "message" => $message
                ]], 422);
            }
        }

        return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $this->auth->fromUser($user) . '&origin=' . ($newUser ? 'register' : 'login'));
    }
}
