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
        $user = Socialite::driver('google')->stateless()->user();
        $findUser = User::where('email', $user->email)->first();

        if ($findUser) {
            // Cek apakah avatar kosong atau bukan dari Google
            if (empty($findUser->avatar) || !str_contains($findUser->avatar, 'googleusercontent.com')) {
                $findUser->update([
                    'avatar' => $user->avatar,
                ]);
            }

            Auth::login($findUser);
            $findUser->notify(new UserLoginNotification());

            return $findUser->roles === 'Owner'
                ? to_route('admin.dashboard')
                : to_route('customer.dashboard');
        } else {
            $newUser = User::create([
                'uuid'   => (string) Str::uuid(),
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar,
                'roles'  => 'Customer',
            ]);

            Auth::login($newUser);
            $newUser->notify(new UserRegisteredNotification());

            return to_route('customer.dashboard');
        }
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

    // protected $maxAttempts = 1;
    // protected $decayMinutes = 120;

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
