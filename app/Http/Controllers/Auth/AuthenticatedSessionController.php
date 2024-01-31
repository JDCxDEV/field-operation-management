<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Notifications\Users\Auth\VerificationCode;
use App\Helpers\CodeGeneratorHelper;
use App\Helpers\TwilioHelper;

use DB;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Display the verify code view.
     *
     * @return \Illuminate\View\View
     */
    public function verifyCode(Request $request)
    {
        $authCode = DB::table("two_auth_codes")->where(["user_id" => $request->user])->latest()->first();
        if(Hash::check($request->token, $authCode->token)) {
            return view('auth.verify-code', [
                "auth_code" => $authCode
            ]);
        } else {
            return redirect("/login");
        }
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->ensureIsNotRateLimited();

        $user = User::where(["email" => $request->email])->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            
            /** Saving failed login */
            User::saveLogin($request->only('email'), "failed");

            throw ValidationException::withMessages([
                'invalid_credentials' => "Login Failed. Invalid credentials",
            ]);
        }

        /** Disable login if account is blacklisted or deactivated */
        if($user->blacklisted || !$user->status) {      
            if($user->blacklisted) {
                $message = "Login unsucessful. Your account has been blacklisted."; 
            }

            if(!$user->status) {
                $message = "Login unsucessful. Your account has been deactivated.";                
            }
            abort(403, $message);
        }
        
        if($user) {
            if($user->two_auth) {
                $auth = $this->setup2Auth($user);
                return response()->json([
                    "status" => 200,
                    "verify" => true,
                    "redirect" => route("verify-code", "token={$auth['token']}&user={$auth['user']}&phone={$auth['phone']}&verify=true")
                ]);
            } else {
                Auth::login($user);  
                $this->updateLastLogin();
            }
        }
        return response()->json([
            "status" => 200,
            "redirect" => RouteServiceProvider::HOME
        ]);
    }

    /**
     * Display the verify code view.
     *
     * @return \Illuminate\View\View
     */
    public function validateCode(Request $request)
    {
        $db = DB::table("two_auth_codes")->where(["user_id" => $request->user]);
        $authCode  = $db->latest()->first();

        $title = "Invalid Code";
        $message = "Verification failed. Invalid code, please try again.";
        if($authCode) {
            // check token
            if(Hash::check($request->token, $authCode->token)) {
                // check code
                if(Hash::check($request->code, $authCode->code)) {
                    $expiration = config('auth.two_auth.codes.expire');          
                    if(Carbon::parse($authCode->created_at)->addMinutes($expiration)->isPast()) {
                        $title = "Expired Code";
                        $message = "Verification failed. Code has expired.";
                    } else {
                        $user = User::find($authCode->user_id);
                        Auth::login($user);

                        $this->updateLastLogin();

                        // delete code
                        $db->delete();

                        return response()->json([
                            "status" => 200,
                            "redirect" => RouteServiceProvider::HOME
                        ]);
                    }
                } else {
                    $title = "Invalid Code";
                    $message = "Verification failed. Invalid code, please try again.";                    
                }
            } else {
                $title = "Invalid Code";
                $message = "Verification failed. Invalid token, please try again.";
            }

            return response()->json([
                "status" => 422,
                "title" => $title,
                "message" => $message
            ]); 

        } else {
            return response()->json([
                "status" => 422,
                "title" => $title,
                "message" => $message
            ]);            
        }
    }

    /**
     * Handle an incoming api authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiStore(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        $user = User::where('email', $request->email)->first();
        return response($user);
    }

    /**
     * Verifies user token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiVerifyToken(Request $request)
    {
        $request->validate([
            'api_token' => 'required'
        ]);

        $user = User::where('api_token', $request->api_token)->first();

        if(!$user){
            throw ValidationException::withMessages([
                'token' => ['Invalid token']
            ]);
        }
        return response($user);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $this->logLogoutSession();
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if($request->ajax){
            return response()->json([
                "status" => 200,
                "message" => "Account has been logged out"
            ]);
        }

        return redirect('/');
    }

    /**
     * Setup 2auth by sending code via twilio (sms)
     * 
     */
    public function setup2Auth($user) 
    {
        $user = $user;
        $code = (new CodeGeneratorHelper)->generateCode();
        $token = Str::random(60);
        
        DB::table('two_auth_codes')->insert([
            'user_id' => $user->id,
            'type' => $user->two_auth_type,
            'email' => $user->email,
            'phone' => $user->info->phone,
            'token' => Hash::make($token),
            'code' => Hash::make($code),
            'created_at' => now()
        ]);
        
        $this->sendCode($user, $code, $token);

        return [
            "token" => $token,
            "phone" => $user->phone,
            "user" => $user->id,
        ];
    }

    public function sendCode($user, $code, $token) 
    {

        /**
         * Create Twilio message
         */
        $expiration = config('auth.two_auth.codes.expire');
        $message = "Hello {$user->name}, 
        this is FieldOps. Your OTP code is {$code}. Valid for {$expiration} minutes.
        ";

        if($user->two_auth_type == "sms") { 
            /**
             * Send sms using Twilio
             * 
             */
            $twilio = new TwilioHelper;
            $recipient = $user->info->phone;
            $twilio->sendMessage($recipient, $message);
        } else {
            $url = route("verify-code", "token={$token}&user={$user->id}&phone={$user->info->phone}&verify=true");
            $user->notify(new VerificationCode($code, $url, $expiration));
        }
        
    }

    /**
     * Update auth user last login date
     * 
     */
    public function updateLastLogin() 
    {
        activity()->disableLogging();        
        User::saveLogin(Auth::guard('web')->user()->email, "success");
        Auth::guard('web')->user()->update(['last_login' => now()]);
        activity()->enableLogging();
        activity()
            ->performedOn(Auth::user())
            ->event('logged-in')
            ->log(Auth::user()->name. ' has logged in');        
    }

    /**
     * Log logout session
     * 
     */
    public function logLogoutSession() 
    {
        activity()->disableLogging();        
        User::saveLogin(Auth::guard('web')->user()->email, "success", "logout");
        activity()->enableLogging();
        activity()
            ->performedOn(Auth::user())
            ->event('logged-out')
            ->log(Auth::user()->name. ' has logged out');        
    }
}
