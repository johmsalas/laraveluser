<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use App\User;
use App\Role;

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
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
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
            $user->roles()->attach(Role::where('name', 'customer')->first()->id);
        }

        \Auth::login($user, true);
        return redirect()->intended($this->redirectTo);
    }
}
