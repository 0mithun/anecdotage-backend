<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        // return 'email';
        return filter_var( request('email'), FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';
    }



    public function attemptLogin(Request $request)
    {
        // attempt to issue a token to the user based on the login credentials
        $token = $this->guard()->attempt($this->credentials($request));

        if( ! $token){
            return false;
        }

        // Get the authenticated user
        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return false;
        }

        // set the user's token
        $this->guard()->setToken($token);

        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        // get the tokem from the authentication guard (JWT)
        $token = (string)$this->guard()->getToken();

        // extract the expiry date of the token
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }


    protected function sendFailedLoginResponse()
    {
        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "You need to verify your email account"
            ]], 422);
        }

        throw ValidationException::withMessages([
            $this->username() => "Invalid credentials"
        ]);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Logged out successfully!']);
    }



    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebookProvider() {
        return Socialite::driver( 'facebook' )->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleFacebookProviderCallback() {
        if ( request()->has( 'error' ) ) {
            return redirect( '/login' );
        }

        $user = Socialite::driver( 'facebook' )->user();

        return $this->oauthLogin( $user, 'facebook' );
        // $user->token;
    }

    /**
     * Redirect the user to the Twitter authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToTwitterProvider() {
        return Socialite::driver( 'twitter' )->redirect();
    }

    /**
     * Obtain the user information from Twitter.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleTwitterProviderCallback() {
        $user = Socialite::driver( 'twitter' )->user();

        return $this->oauthLogin( $user, 'twitter' );
        // $user->token;
    }

    /**
     * Redirect the user to the Instagram authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToInstagramProvider() {
        // return Socialite::driver('instagram')->redirect();
        return Socialite::with( 'instagram' )->redirect();

    }

    /**
     * Obtain the user information from Instagram.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleInstagramProviderCallback() {
        // $user = Socialite::driver('instagram')->user();
        $user = Socialite::driver( 'instagram' )->user();
        // dd( $user );

        return $this->oauthLogin( $user, 'instagram' );
        // $user->token;
    }

    public function oauthLogin( $user, $provider ) {
        //  dd($user);
        $userExists = User::where( 'email', '=', $user->email )->first();
        if ( !$userExists ) {
            $name = $user->name;
            $splitName = explode( ' ', $name );
            $userName = strtolower( implode( '', $splitName ) . uniqid() );

            if ( count( $splitName ) > 1 ) {
                $firstName = $splitName[0];
                $lastName = $splitName[1];
            } else {
                $firstName = $name;
                $lastName = $name;
            }
            $newUser = User::create( [
                'name'        => $name,
                'username'    => $userName,
                'email'       => $user->email,
                'avatar_path' => $user->avatar,
                'password'    => Hash::make( md5( uniqid() . now() ) ),
                'confirmed'   => 1,
                'auth_type'   => $provider,
            ] );
            Auth::login( $newUser );
        } else {
            if ( $userExists->auth_type != $provider ) {
                if ( $userExists->auth_type == 'email' ) {
                    $message = 'This email is already associated with an account, pelase reset your password or login with your email and password below.';
                } else {
                    $message = 'This email has already registered using ' . $provider . '. Please login with ' . ucfirst( $provider ) . ', or you may reset your password';
                }


                return response()->json(["errors" => [
                    "message" => $message
                ]], 422);
            }
            Auth::login( $userExists );
        }

        return redirect( '/' );
    }


}
