<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Helpers\MailgunHelper;

use DB;
use Hash;
use Carbon\Carbon;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // $status = Password::sendResetLink(
        //     $request->only('email')
        // );

        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $request->email, 
            'token' => Hash::make($token), 
            'created_at' => Carbon::now()
        ]);
        $url = env("APP_URL") . "/reset-password/". $token .'?email='.$request->email;

        $user = User::where("email", $request->email)->first();

        $mailgun = new MailgunHelper;
        $mailgun->sendMail("Forgot Password", $request->email, $user->name, $url, "forgot-password");
        
        return response()->json([
            "status" => "passwords.sent"
        ]);
    }

    /**
     * Handle an incoming api password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            throw ValidationException::withMessages([
                'email' => ['User with such email doesn\'t exist']
            ]);
        }

        return response('Password reset email successfully sent.');
    }
}
