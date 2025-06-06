<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HelpCenterController extends Controller
{
    public function index()
    {
        return view('v2.user.help-center.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|max:1000',
            'cf-turnstile-response' => 'required|string',
        ]);

        $captchaResponse = $request->input('cf-turnstile-response');

        $verify = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET'),
            'response' => $captchaResponse,
            'remoteip' => $request->ip(),
        ]);

        Log::info('Turnstile response', $verify->json());

        if (!($verify->json()['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA verification failed. Please try again.',
            ]);
        }

        try {
            Mail::send('mail.help-email-center', [
                'name' => $request->name,
                'email' => $request->email,
                'bodyMessage' => $request->message,
            ], function ($message) use ($request) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'BudgetKu Website');
                $message->to('hi@wikukarno.com');
                $message->subject('New Contact Form Submission');
                $message->replyTo($request->email, $request->name);
            });

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Mail error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ]);
        }
    }
}
