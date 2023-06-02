<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Mail\Login;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/pages/dashboard';

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handlerProviderCallback(Request $request)
    {
        $user = Socialite::driver('google')->user();
        $findUser = User::where('email', $user->email)->first();
        // $text = "
        //     Dear " . $findUser->name . " Terdeteksi Login pada tanggal " . Carbon::now()->isoFormat('D MMMM Y') . " pukul " . Carbon::now()->format('H:i:s') .
        //     " dengan IP Address " . Request::ip() .
        //     "\n\n Jika bukan anda yang melakukan login, segera amankan akun dengan keyword /amankan.
        // ";
        if ($findUser) {
            // $findUser->update([
            //     'last_login_at' => Carbon::now()->toDateTimeString(),
            //     'last_login_ip' => Request::ip(),
            // ]);
            Auth::login($findUser);
            // sendText($findUser->telegram_id, $text);
            // Mail::to(
            //     $findUser->email
            // )->send(new Login($findUser));
            return redirect()->intended('pages/dashboard');
        } else {
            return redirect()->route('login');
        }
    }

    // send email login
    protected function authenticated($request, $user)
    {
        // $text = "
        //     Dear " . $user->name . " Terdeteksi Login pada tanggal " . Carbon::now()->isoFormat('D MMMM Y') . " pukul " . Carbon::now()->format('H:i:s') .
        //     " dengan IP Address " . Request::ip() .
        //     " Jika bukan anda yang melakukan login, segera amankan akun dengan keyword /amankan.
        // ";
        // $user->update([
        //     'last_login_at' => Carbon::now()->toDateTimeString(),
        //     'last_login_ip' => Request::ip(),
        // ]);
        // sendText($user->telegram_id, $text);
        // Mail::to($request->user())->send(new Login($user));
    }

    protected $maxAttempts = 1;
    protected $decayMinutes = 1;

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
