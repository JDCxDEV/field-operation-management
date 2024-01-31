<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Account\SettingsEmailRequest;
use App\Http\Requests\Account\SettingsInfoRequest;
use App\Http\Requests\Account\SettingsPasswordRequest;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Helpers\GoogleStorageHelper;
use App\Helpers\TwilioHelper;

use Auth;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $id = $request->user_id;

        $user = auth()->user();
        $isAuth = true;
        if($id) {
            $isAuth = false; 
            $user = User::find($id);
            if(!$user) {
                return redirect()->route("users.index");
            }
        }
        $info = $user->info;

        // get the default inner page
        return view('pages.users.overview', compact('info', 'user', 'isAuth'));        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function overview(Request $request)
    {
        $id = $request->user_id;

        $user = auth()->user();
        $isAuth = true;
        if($id) {
            $isAuth = false;
            $user = User::find($id);
        }
        $info = $user->info;

        // get the default inner page
        return view('pages.account.overview.overview', compact('info', 'user', 'isAuth'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SettingsInfoRequest $request, $id)
    {
        // save user name
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone' => 'required|min:10'
        ]);

        /** Validated phone number */ 
        $twilio = new TwilioHelper;
        if($twilio->validatePhoneNumber($request->phone)["status"] == "invalid") {
            throw ValidationException::withMessages([
                'phone' => "Invalid phone number",
            ]);            
        }
        
        $user = auth()->user();
        if($id) {
            $user = User::find($id);
        }

        $user->update($validated);

        // save on user info
        $info = UserInfo::where('user_id', $user->id)->first();

        if ($info === null) {
            // create new model
            $info = new UserInfo();
        }

        // attach this info to the current user
        $info->user()->associate($user);

        foreach ($request->only(array_keys($request->rules())) as $key => $value) {
            if (is_array($value)) {
                $value = serialize($value);
            }
            $info->$key = $value;
        }


        $gcs = new GoogleStorageHelper;

        // include to save avatar
        if ($avatar = $this->upload()) {
            $gcs->deleteFile($info->avatar);
            $info->avatar = $avatar;
        }

        if ($request->boolean('avatar_remove')) {
            $gcs->deleteFile($info->avatar);
            $info->avatar = null;
        }

        $info->save();

        return response()->json([
            "status" => 200,
            "message" => "Profile updated"
        ]);
    }

    /**
     * Function for upload avatar image
     *
     * @param  string  $folder
     * @param  string  $key
     * @param  string  $validation
     *
     * @return false|string|null
     */
    public function upload($folder = 'images', $key = 'avatar', $validation = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|sometimes')
    {
        request()->validate([$key => $validation]);

        $file = null;
        if (request()->hasFile($key)) {
            $gcs = new GoogleStorageHelper;
            $file = $gcs->storeFile(request()->file($key), $folder);
        }

        return $file;
    }

    /**
     * Function to accept request for change email
     *
     * @param  SettingsEmailRequest  $request
     */
    public function changeEmail(SettingsEmailRequest $request, $id)
    {
        // prevent change email for demo account
        // if ($request->input('current_email') === 'demo@demo.com') {
        //     return redirect()->intended('account/settings');
        // }

        $user = auth()->user();
        if($id) {
            $user = User::find($id);
        }

        $user->update(['email' => $request->input('email')]);

        if ($request->expectsJson()) {
            return response()->json($request->all());
        }

        if($id) {
            return redirect()->back();
        }

        return redirect()->intended('account/settings');
    }

    /**
     * Function to accept request for change password
     *
     * @param  SettingsPasswordRequest  $request
     */
    public function changePassword(SettingsPasswordRequest $request, $id)
    {
        // prevent change password for demo account
        // if ($request->input('current_email') === 'demo@demo.com') {
        //     return redirect()->intended('account/settings');
        // }

        $user = auth()->user();
        if($id) {
            $user = User::find($id);
        }

        $user->update(['password' => Hash::make($request->input('password'))]);

        if ($request->expectsJson()) {
            return response()->json($request->all());
        }

        if($id) {
            return redirect()->back();            
        }
        
        return redirect()->intended('account/settings');
    }

    /**
     * Updating 2FA
     *
     * @param  Illuminate\Http\Request  $request
     */
    public function updateTwoAuth(Request $request, $id)
    {
        $user = auth()->user();
        if($id) {
            $user = User::find($id);
        }
        activity()->disableLogging();        
        $user->update(['two_auth' => $request->two_auth ? true: false, "two_auth_type" => $request->two_auth_type]);
        activity()->enableLogging();
        
        return response()->json([
            "status" => 200,
        ]);        
    }

    /**
     * Updating 2FA
     *
     * @param  Illuminate\Http\Request  $request
     */
    public function verify($id)
    {
        $user = auth()->user();
        if($id) {
            $user = User::find($id);
        }
        activity()->disableLogging();        
        $user->update(['verified' => true]);
        activity()->enableLogging();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('activated')
            ->log($user->name. ' account has been activated'); 
        
        return response()->json([
            "status" => 200,
        ]);        
    }
}
