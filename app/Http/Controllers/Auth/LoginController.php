<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Notifications\UserLoginNotification;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handlerProviderCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return to_route('login')->withErrors(['error' => 'Failed to authenticate with Google. Please try again.']);
        }

        $findUser = User::where('email', $googleUser->email)->first();

        if ($findUser) {

            if (empty($findUser->avatar) || !str_contains($findUser->avatar, 'googleusercontent.com')) {
                $findUser->update(['avatar' => $googleUser->avatar]);
            }

            Auth::login($findUser);

            $findUser->notify(new UserLoginNotification());

            return $findUser->roles === 'Owner'
                ? to_route('admin.dashboard')
                : to_route('customer.dashboard');
        }

        $newUser = User::create([
            'uuid'   => (string) Str::uuid(),
            'name'   => $googleUser->name,
            'email'  => $googleUser->email,
            'avatar' => $googleUser->avatar,
            'roles'  => 'Customer',
        ]);

        Auth::login($newUser);

        $newUser->notify(new UserRegisteredNotification());

        return $newUser->roles === 'Owner'
            ? to_route('admin.dashboard')
            : to_route('customer.dashboard');
    }

    // send email login
    protected function authenticated($request, $user)
    {
        if($user->roles == 'Owner') {
            return to_route('admin.dashboard');
        } elseif($user->roles == 'Customer') {
            return to_route('customer.dashboard');
        }
    }

    protected $maxAttempts = 3;
    protected $decayMinutes = 2;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
