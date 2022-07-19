<?php

namespace App\Http\Controllers\Api\UserManagement;

use App\Http\Controllers\Controller;
use App\TherapistProfile;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\User;
use App\PasswordReset;
use App\TherapistService;
use Illuminate\Support\Facades\Auth;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
//Enables us to output flash messaging
use Illuminate\Contracts\Session\Session;
use Validator;
use Carbon\Carbon;
use Mail;

class UserController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['numeric', 'required', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'min:6|required_with:c_password|same:c_password',
            'c_password' => 'min:6',
            'role' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }        
        $input = [];
        //return $input;
        if ($request->role == 'therapist') {
            $validator = Validator::make($request->all(), [
                'about' => 'required',
                'city' => 'required',
                'service_therapist_provider' => 'required',
                'therapist_focus' => 'required',
                'type_of_doctor' => 'required',
                'introduction' => 'required',
                'education' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $input = $request
                ->except([
                    'about',
                    'city',
                    'service_therapist_provider',
                    'therapist_focus',
                    'type_of_doctor',
                    'introduction',
                    'education'
                ]);
        } else {
            $input = $request->all();
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $role_name = $request->role;
        $user->assignRole($role_name);
        if ($request->role == 'therapist') {
            $therapist_profile = $request
                ->except([
                    'first_name',
                    'last_name',
                    'email',
                    'mobile_number',
                    'isVerified',
                    'password',
                    'status_id',
                    'service_therapist_provider'
                ]);

                foreach($request->service_therapist_provider as $service){
                    $service_provided['service'] = $service;
                    $service_provided['user_id'] = $user->id;
                    TherapistService::create($service_provided);
                }
            $therapist_profile['user_id'] = $user->id;
            $therapist = TherapistProfile::create($therapist_profile);
        }
        $success['token'] = $user->createToken('pukaar')->accessToken;
        $success['user_id'] = $user->id;
        $success['first_name'] = $user->first_name;
        $success['last_name'] = $user->last_name;
        $success['email'] = $user->email;
        $success['role'] = $request->role;
        return response()->json(['data' => $success], $this->storeStatus);
    }

    public function displayPicture(Request $request)
    {
        $user_id = Auth::user()->id;
        $check_multiple = User::where('id', $user_id)->first();
        if($check_multiple->display_picture == null || $check_multiple->display_picture == '')
        {
            $validator = Validator::make($request->all(), [
                'display_picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $file = $request->file('display_picture');
            $current = Carbon::now();
            $current = str_replace(' ', '_', $current);
            $current = str_replace(' ', '', $current);
            $current = str_replace('-', '', $current);
            $current = str_replace(':', '', $current);
            $fileName = $current . '.' . $file->getClientOriginalExtension();
            $destination_path = storage_path('/profile/display_picture');
            $file->move($destination_path, $fileName);
            $destination_path = '/profile/display_picture/'.$fileName;
            $display_picture = $destination_path;
            $create = User::where('id', $user_id)->update(['display_picture' => $display_picture]);
            
            return response()->json(['success' => 'Profile Picture Set Successfully'], 201);
        }else {
            $validator = Validator::make($request->all(), [
                'display_picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $file = $request->file('display_picture');
            $current = Carbon::now();
            $current = str_replace(' ', '_', $current);
            $current = str_replace(' ', '', $current);
            $current = str_replace('-', '', $current);
            $current = str_replace(':', '', $current);
            $fileName = $current . '.' . $file->getClientOriginalExtension();
            $destination_path = storage_path('/profile/display_picture');
            $file->move($destination_path, $fileName);
            $destination_path = '/profile/display_picture/'.$fileName;
            $display_picture = $destination_path;
            $create = User::where('id', $user_id)->update(['display_picture' => $display_picture]);

            return response()->json(['success' => 'Profile Picture Update Successfully']);
        }
            
    }

    public function setPasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'min:4|required_with:c_passcode|same:c_passcode',
            'c_passcode' => 'min:4',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $create = User::where('id', auth()->user()->id)->update(['passcode' => $request->passcode]);
        return response()->json(['success' => 'Passcode Set Successfully']);
    }

    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->status_id != 1) {

                return response()->json('User inactive', $this->permissionDenied);
            }
            $success['token'] = $user->createToken('pukaar')->accessToken;
            $success['user_id'] = $user->id;
            $success['first_name'] = $user->first_name;
            $success['last_name'] = $user->last_name;
            $success['email'] = $user->email;
            $success['role'] = Auth::user()->roles->pluck('name');
            return response()->json(['data' => $success], $this->successStatus);
        } else {
            $success['status'] = $this->unauthorizedStatus;
            return response()->json(['error' => $success], $this->unauthorizedStatus);
        }
    }

    public function loginWithPasscode(Request $request)
    {
        $passcode = User::where('id', Auth::user()->id)->where('passcode', $request->passcode)->first();
        if($passcode){
            return response()->json(['success' => 'Passcode Matched'], 200);
        }else{
            return response()->json(['error' => 'Wrong Passcode'], 401);
        }
    }

    public function index(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'admin') {
            $validator = Validator::make($request->all(), [
                'role' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            if ($request->role == 'admin') {
                $data['users'] = User::with('user_status', 'client_profile', 'therapist_profile');

            } elseif ($request->role == 'therapist') {
                $data['users'] = User::role($request->role)->with('user_status', 'therapist_profile');
            } elseif ($request->role == 'client') {
                $data['users'] = User::role($request->role)->with('user_status', 'client_profile', 'client_profile.therapist');
            }
            if (isset($request->type)) {
                $type = $request->type;
            }
            if (isset($request->status)) {
                if ($request->status == '1' || $request->status == 'active') {
                    $data['users'] = $data['users']->where('status_id', '=', 1);
                } elseif ($request->status == '2' || $request->status == 'inactive') {
                    $data['users'] = $data['users']->where('status_id', '=', 2);
                }
            }
            if (isset($type) && $type == 'assigned' && $request->role == 'client') {
                $data['users'] = $data['users']->whereHas('client_profile', function ($q) {
                    $q->where('check_assigned', '=', 2);
                })->latest()->paginate(10);
                return response()->json($data, $this->successStatus);
            } elseif (isset($type) && $type == 'unassigned') {
                $data['users'] = $data['users']
                    ->whereHas('client_profile', function ($q) {
                        $q->where('check_assigned', '<', 2);
                        $q->orwhere('check_assigned', '=', null);
                        $q->orwhere('check_assigned', '=', '');
                    })->whereHas('sessions', function ($q) {
                        $q->where('number_of_sessions', '>', 0);
                        $q->where('session_status', 'approved');
                    })->latest()->paginate(10);;
//                $query = str_replace(array('?'), array('\'%s\''), $data['users']->toSql());
//                $query = vsprintf($query, $data['users']->getBindings());
//                dd($query);
                return response()->json($data, $this->successStatus);
            } elseif (isset($type) && $type == 'reassigned') {
                $data['users'] = $data['users']->whereHas('client_profile', function ($q) {
                    $q->where('check_assigned', '=', 3);
                })->latest()->paginate(10);

                return response()->json($data, $this->successStatus);
            }
            $data['users'] = $data['users']->latest()->paginate(10);
            return response()->json($data, $this->successStatus);
        } elseif ($user_role[0] == 'therapist') {
            $validator = Validator::make($request->all(), [
                'type' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $therapist_profile_id_user = TherapistProfile::where('user_id', Auth::user()->id)->first();
            if (isset($therapist_profile_id_user->id)) {
                $therapist_profile_id_user = $therapist_profile_id_user->id;
            } else {
                return response()->json('Therapist Profile not found', $this->notFound);
            }
            $data['users'] = User::role('client')->with('user_status', 'client_profile');
            if ($request->type == 'assigned') {
                $data['users'] = $data['users']
                    ->whereHas('client_profile', function ($q) use ($therapist_profile_id_user) {
                        $q->where('therapist_profile_id', $therapist_profile_id_user);
                        $q->where('check_assigned', 2);
                    })->latest()->paginate(10);

                return response()->json($data, $this->successStatus);
            } elseif ($request->type == 'unassigned') {
                $data['users'] = $data['users']->whereHas('client_profile', function ($q) use ($therapist_profile_id_user) {
                    $q->where('therapist_profile_id', $therapist_profile_id_user);
                    $q->where('check_assigned', '<', 2);
                    $q->orwhere('check_assigned', '=', null);
                    $q->orwhere('check_assigned', '=', '');
                })->whereHas('sessions', function ($q) {
                    $q->where('number_of_sessions', '>', 0);
                    $q->where('session_status', 'approved');
                })->latest()->paginate(10);
                return response()->json($data, $this->successStatus);
            }
        }
    }

    public function edit($id)
    {
        $dat = User::with('user_status', 'roles', 'client_profile', 'therapist_profile')
            ->findOrFail($id); //Get user with specified id
        $role = $dat->roles->pluck('name');
        if ($role[0] == 'therapist') {
            $data['user'] = User::role('therapist')->with('user_status', 'therapist_profile', 'therapist_profile.client.user')->findOrFail($id);

        } elseif ($role[0] == 'client') {
            $data['user'] = User::role('client')->with('user_status', 'client_profile')->findOrFail($id);
        }
        if ($data['user'] == null || $data['user'] == '') {
            return response()->json($data, $this->notFound);
        }
        return response()->json($data, $this->successStatus);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first(); //Get role specified by id
        //Validate name, email and password fields
        if ($user == '' || $user == null) {
            return response()->json('', $this->notFound);
        }

        $input = $request->except('role');
        $roles = $request['role']; //Retreive all roles
        if ($request->password) {
            $input['password'] = bcrypt($input['password']);
        }
        User::where('id', $id)->update($input);
        $user->fill($input)->save();
        if (isset($roles)) {
            $user->roles()->sync($roles);
            //If one or more role is selected associate user to roles
        } else {
            $user->roles()->detach();
            //If no role is selected remove exisiting role associated to a user
        }
        return response()->json('', $this->successStatus);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json('', $this->successStatus);
    }

    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'numeric', 'max:255'],
            'status_id' => ['required', 'numeric', 'max:255']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        if (User::where('id', $request->user_id)->exists()) {
            User::where('id', $request->user_id)->update(['status_id', $request->status_id]);
        } else {
            return response()->json(['data' => 'User not found'], $this->notFound);
        }
        return response()->json(['data' => 'User status changed'], $this->successStatus);
    }

    public function therapist_edit($id)
    {
        $dat = User::with('user_status', 'roles', 'client_profile', 'therapist_profile')
            ->findOrFail($id); //Get user with specified id
        $role = $dat->roles->pluck('name');
        if ($role[0] == 'therapist') {
            $data['user'] = User::role('therapist')->with('user_status', 'therapist_profile')->findOrFail($id);

        } else {
            return response()->json('Therapist Not found on given ID', $this->notFound);
        }
        if ($data['user'] == null || $data['user'] == '') {
            return response()->json($data, $this->notFound);
        }
        return response()->json($data, $this->successStatus);
    }

    public function therapist_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $id = $request->id;
        $therapist_profile = $request
            ->except([
                'id',
                'c_password',
                'first_name',
                'last_name',
                'email',
                'mobile_number',
                'isVerified',
                'password',
                'status_id'
            ]);
        $input = $request
            ->except([
                'id',
                'c_password',
                'email',
                'mobile_number',
                'about',
                'city',
                'service_therapist_provider',
                'therapist_focus',
                'type_of_doctor',
                'introduction',
                'education'
            ]);
        $input['password'] = bcrypt($request->password);
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'admin') {
            if (User::where('id', '=', $id)->exists()) {
                User::where('id', '=', $id)->update($input);
                $therapist_user_id = User::where('id', '=', $id)->first();
                //profile update
                $therapist_profile_update = TherapistProfile::where('user_id', $therapist_user_id)->update($therapist_profile);
                return response()->json('Therapist Profile Update Successful', $this->successStatus);
            } else {
                return response()->json('User Not Found', $this->notFound);
            }
        } else {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
    }
    
    public function forgetPasscode(Request $request)
    {
        //checking in database if the user email exist before saving
        $check = User::where('email', '=', $request->email)->first();

        if ($check != null) {
            $this->validate($request, [
                'email' => 'required|email',
            ]);
            $code = random_int(100000, 999999);
            $data = ['ResetPasscode' => $code];
            $checkReset = User::where('email', '=', $request->email)->first();
                User::where('email', $request->email)->update([
                    'reset_passkey' => $code
                ]);
                $user['to'] = $request->email;
                Mail::send('mail', $data, function($messages) use ($user){
                    $messages->to($user['to']);
                    $messages->subject('Pukaar App Passcode');
                });
            return response()->json(['success' => 'Code Send!'], $this->successStatus);
        }
        return response()->json(['error' => 'Email does not exist!'], 401);
    }

    public function verifyPasscode(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user->reset_passkey == $request->passcode){
            return response()->json(['success' => 'Passcode Matched'], 200);
        }else{            
            return response()->json(['error' => 'Wrong Passcode'], 400);
        }
    }

    public function passcodeReset(Request $request){
        //checking in database if the user email exist before saving
        $check = User::where('email', '=', $request->email)->first();

        if ($check != null) {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'passcode' => 'required|max:4'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            if($check->reset_passkey == null || $check->reset_passkey == ''){
                return response()->json(['error' => 'Please Request For Forgot Passcode']);
            }else{
                User::where('email', $request->email)->update(
                    [
                        'passcode' => $request->passcode,
                        'reset_passkey' => null
                    ]);
                return response()->json(['success' => 'New Passcode Create Successfully'], $this->successStatus);
            }
        }
        return response()->json(['error' => 'Email does not exist!'], 401);

    }
}