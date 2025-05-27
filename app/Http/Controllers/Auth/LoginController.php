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
        logger('📥 [Google Login] Callback HIT');

        try {
            $googleUser = Socialite::driver('google')->user();
            logger('✅ [Google Login] Data retrieved', [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar,
            ]);
        } catch (\Exception $e) {
            logger()->error('❌ [Google Login] Failed to retrieve user data', [
                'error' => $e->getMessage(),
            ]);
            return redirect('/login')->withErrors(['msg' => 'Google authentication failed.']);
        }

        $findUser = User::where('email', $googleUser->email)->first();

        if ($findUser) {
            logger('ℹ️ [Google Login] Existing user found', [
                'id' => $findUser->id,
                'uuid' => $findUser->uuid,
            ]);

            if (empty($findUser->avatar) || !str_contains($findUser->avatar, 'googleusercontent.com')) {
                $findUser->update(['avatar' => $googleUser->avatar]);
                logger('🖼️ [Google Login] Avatar updated');
            }

            Auth::login($findUser);
            logger('✅ [Google Login] Existing user logged in', [
                'auth_user' => Auth::user(),
            ]);

            $findUser->notify(new UserLoginNotification());

            return $findUser->roles === 'Owner'
                ? to_route('admin.dashboard')
                : redirect('/pages/customer/dashboard');
        }

        logger('🆕 [Google Login] No user found, creating new one');

        $newUser = User::create([
            'uuid'   => (string) Str::uuid(),
            'name'   => $googleUser->name,
            'email'  => $googleUser->email,
            'avatar' => $googleUser->avatar,
            'roles'  => 'Customer',
        ]);

        logger('✅ [Google Login] New user created', [
            'id' => $newUser->id,
            'uuid' => $newUser->uuid,
        ]);

        Auth::login($newUser);
        logger('✅ [Google Login] New user logged in', [
            'auth_user' => Auth::user(),
        ]);

        $newUser->notify(new UserRegisteredNotification());

        return $newUser->roles === 'Owner'
            ? to_route('admin.dashboard')
            : redirect('/pages/customer/dashboard');
    }

    // send email login
    protected function authenticated($request, $user)
    {
        if($user->roles == 'Owner') {
            return to_route('admin.dashboard');
        } elseif($user->roles == 'Customer') {
            return redirect('/pages/customer/dashboard');
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
