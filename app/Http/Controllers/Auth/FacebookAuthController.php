<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use App\User;

class FacebookAuthController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $fbUser = Socialite::driver('facebook')->user();
        $user = User::where('facebook_id', $fbUser->id)
            ->orWhere('email', $fbUser->email)
            ->first();

        if(!$user) {
            $user = User::create([
                'name' => $fbUser->name,
                'email' => $fbUser->email,
                'facebook_id' => $fbUser->id,
                'active' => true,
            ]);
        }

        \Auth::login($user, true);
        return redirect()->intended($this->redirectTo);
    }
}
