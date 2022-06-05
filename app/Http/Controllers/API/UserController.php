<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SMSNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;
use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    use PasswordValidationRules;

    public function fecth(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Data User Berhasil diambil');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            $credentials = request(['username', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ],'Authentication Failed', 500);
            }

            $user = User::where('username', $request->username)->first();
            if ( ! Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Authenticated');
        }catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }
    }

    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','string','email','max:255','unique:users'],
                'username' => ['required','string','max:255','unique:users'],
                'phone' =>  ['required','string','max:255'],
                'password' => $this->passwordRules(),
                'image' => ['required','image','max:2048'],
            ]);

        //     $basic  = new \Vonage\Client\Credentials\Basic(env("NEXMO_KEY"), env("NEXMO_SECRET"));
        //     $client = new \Vonage\Client($basic);

        //     $response = $client->sms()->send(
        //         new \Vonage\SMS\Message\SMS("$request->phone", 'Hendris', 'Selamat Datang, Terima kasih telah bergabung dengan kami.')
        //     );
            
        //    $response->current();

            $file = $request->file('image')->store('assets/user', 'public');

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'profile_photo_path' => $file,
            ]);

            $user = User::where('username', $request->username)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'User Registered');

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }
    }

    public function updateProfile(Request $request)
    {

        $data = $request->all();

        if($request->file('image'))
        {
            $data['image'] = $request->file('image')->store('assets/user', 'public');
        }
        $data['password'] = Hash::make($request->password);
        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user,'Profile Updated');
    }

    public function profile(Request $request)
    {
        $id = $request->input('id');

        $data = User::find($id);

        if($id)
        {
            return ResponseFormatter::success([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'foto' => $data->profile_photo_path
            ], 'Get User Profile Successfully');
        }

        $all = User::select('id', 'name', 'phone', 'email', 'profile_photo_path')->get();

        return ResponseFormatter::success([
            'users' => $all
        ],'Showing All Users');
    }
}
