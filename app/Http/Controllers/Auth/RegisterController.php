<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home'; // This will not be used after override

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Override the default register method to prevent auto-login.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Don't login the user — redirect to login page with message
        return redirect('/login')->with('success', 'Registration successful!');
    }

    /**
     * Validate incoming registration data.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after validation.
     */
    protected function create(array $data)
    {
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // Optional: Uncomment if using status logic
            // 'status' => 'inactive', 
        ]);

        // Add default settings for the user
        $this->createDefaultSettings($user->id);

        return $user;
    }

    /**
     * Insert default settings for the new user.
     */
    protected function createDefaultSettings($userId)
    {
        $defaultSettings = [
            ['setting_key' => 'logo', 'setting_value' => 'img/dashboardlogo.svg', 'user_id' => $userId],
            ['setting_key' => 'address', 'setting_value' => 'Default Address', 'user_id' => $userId],
            ['setting_key' => 'phone_number', 'setting_value' => '0000000000', 'user_id' => $userId],
        ];

        Setting::insert($defaultSettings);
    }
}
