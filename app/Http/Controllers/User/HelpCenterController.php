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
        // Validasi input + captcha field
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|max:1000',
            'cf-turnstile-response' => 'required',
        ]);

        // Verifikasi CAPTCHA
        $captchaResponse = $request->input('cf-turnstile-response');

        $verify = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET'),
            'response' => $captchaResponse,
            'remoteip' => $request->ip(),
        ]);

        if (!($verify->json()['success'] ?? false)) {
            return back()->with('error', 'CAPTCHA verification failed. Please try again.');
        }

        try {
            Mail::send('mail.contact', [
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ], function ($message) use ($request) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'BudgetKu Website');
                $message->to('dev@budgetku.com');
                $message->subject('New Contact Form Submission');
                $message->replyTo($request->email, $request->name);
            });

            return back()
                ->with('success', 'Your message has been sent successfully!')
                ->withFragment('contact');
        } catch (\Throwable $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
