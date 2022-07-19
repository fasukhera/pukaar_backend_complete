<?php

namespace App\Http\Controllers\Api\AssignTherapist;

use App\ClientProfile;
use App\Events\ChangeTherapistRequest;
use App\Http\Controllers\Controller;
use App\TherapistProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Validator;

class AssignTherapistController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function available_therapist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $status = $request->status;
        $data = User::role('therapist')->with('therapist_profile', 'therapist_profile.client', 'user_status')
            ->whereHas('user_status', function ($q) use ($status) {
                $q->where('name', '=', $status);
            })
            ->whereHas('therapist_profile.client', function ($q) {
                $q->where('check_assigned', '=', 2);
            })->paginate(10);
        return response()->json($data, $this->successStatus);
    }

    public function forward_therapist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_profile_id' => ['required', 'numeric'],
            'therapist_profile_id' => ['required', 'numeric']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'admin') {
            $checkClientProfile = ClientProfile::findOrFail($request->client_profile_id);
            $checkTherapistProfile = TherapistProfile::findOrFail($request->therapist_profile_id);

            if ($checkClientProfile != null || $checkClientProfile != '' && $checkTherapistProfile != null || $checkTherapistProfile != ''
                && $checkClientProfile->check_assigned <= 1) {
                $assignment = ClientProfile::where('id', $request->client_profile_id)->update([
                    'therapist_profile_id' => $request->therapist_profile_id,
                    'check_assigned' => 2
                ]);
                return response()->json('success', $this->successStatus);
            } else {
                $assignment = ClientProfile::where('id', $request->client_profile_id)->update([
                    'therapist_profile_id' => $request->therapist_profile_id,
                    'check_assigned' => 3
                ]);
                return response()->json('Please Create Client  & Therapist profile first', $this->notFound);

            }
        }
        return response()->json('success', $this->successStatus);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'numeric'],
            'therapist_id' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $status = $request->status;
        $data['user'] = User::role('therapist')->with('therapist_profile', 'user_status')
            ->whereHas('user_status', function ($q) use ($status) {
                // Query the name field in status table
                $q->where('name', '=', $status); // '=' is optional
            })->findOrFail($id); //Get user with specified id

        if ($data['user'] == null || $data['user'] == '') {
            return response()->json($data, $this->notFound);
        }
        return response()->json($data, $this->successStatus);
    }

    public function accept_therapist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'therapist_id' => ['required', 'numeric'],
            'client_id' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

//        $user_id = Auth::user()->id;

        $therapist = TherapistProfile::where('user_id', $request->therapist_id)->first();
        if (isset($therapist->id)) {
            $therapist_id = $therapist->id;
        } else {
            return response()->json('failed', $this->notFound);
        }
        $assigning = ClientProfile::where('user_id', $request->client_id)->update([
            'therapist_profile_id' => $therapist_id,
            'check_assigned' => 2
        ]);
        return response()->json('success', $this->successStatus);
    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $status = $request->status;
        $data['user'] = User::role('therapist')->with('therapist_profile', 'user_status')
            ->whereHas('user_status', function ($q) use ($status) {
                // Query the name field in status table
                $q->where('name', '=', $status); // '=' is optional
            })->findOrFail($id); //Get user with specified id

        if ($data['user'] == null || $data['user'] == '') {
            return response()->json($data, $this->notFound);
        }
        return response()->json($data, $this->successStatus);
    }

    public function changeTherapist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'change_therapist_reason' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'client') {
            $therapist =  ClientProfile::where('user_id', Auth::user()->id)->where('therapist_profile_id', '!=', null)->first();
            if($therapist){
                $user_id = Auth::user()->id;
                $admin = User::where('id', 1)->first();
                $admin_id = $admin->id;
                $profile = ClientProfile::where('user_id', $user_id)->first();
                if (isset($profile->id)) {
                    $check_therapist_status = $profile->change_therapist;
                } else {
                    return response()->json('Please fill Client form first', $this->permissionDenied);
                }
                if ($check_therapist_status == null || $check_therapist_status == 0) {
                    $status_update['change_therapist'] = 1;
                    $status_update['change_therapist_reason'] = implode(', ', $request->change_therapist_reason);
                    //$status_update['change_therapist_reason'] = $request->change_therapist_reason;
                    $therapist_status_update = ClientProfile::where('user_id', $user_id)->update($status_update);
                    //user
                    $user = User::where('id', $user_id)->first();
                    //therapist
                    $therapist = User::where('id', $profile->therapist_profile_id)->first();
                    $data['client_id'] = $user->id;
                    $data['client_name'] = $user->first_name . " " . $user->last_name;
                    $data['client_emali'] = $user->email;
                    $data['therapist_id'] = $therapist->id;
                    $data['therapist_name'] = $therapist->first_name . " " . $therapist->last_name;
                    $data['therapist_email'] = $therapist->email;
                    $data['Admin'] = $admin_id;
                    event(new ChangeTherapistRequest($data));
                    return response()->json(['success' => 'request sent', 'data' => $data]);
                } else {
                    return response()->json(['success' => 'request already sent']);
                }
            }else{
                return response()->json('Therapist not assigned yet', $this->permissionDenied);
            }
        } else {
            return response()->json('Please login from Client account', $this->permissionDenied);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getChangetherapistReq()
    {
        $requests = ClientProfile::where('change_therapist', 1)->get();
        if ($requests) {
            $data['clients'] = ClientProfile::with('user')->where('change_therapist', 1)->paginate(10);
            foreach ($data['clients'] as $key => $client) {
                $data['clients'][$key]->change_therapist_reason = explode(', ', $client->change_therapist_reason);
            }
            return response()->json(['success' => 'Change Therapist Clients', 'data' => $data]);
        } else {
            return response()->json(['success' => 'Empty Requests']);
        }
    }

    public function getAssignedTherapist()
    {
        if(Auth::user()->hasRole('client')){
            $patient_id = Auth::user()->id;
            $check_therapist = ClientProfile::where('user_id', $patient_id)->where('therapist_profile_id', '!=', null)->first();
            if($check_therapist){
                $get_therapist = TherapistProfile::with('credentials')->where('id', $check_therapist->therapist_profile_id)->first();
                return response()->json(['success' => $get_therapist], 200);
            }else{
                return response()->json(['error' => 'Therapist Not Assigned Yet'], $this->badRequest);
            }
        }else {
            return response()->json('Please login from Client account', $this->permissionDenied);
        }
        
    }

    //therapist can get their patient profile
    public function getClientProfile(Request $request)          
    {
        if(auth()->user()->hasRole('therapist')){
            $client_profile = ClientProfile::with('user')->where('user_id', $request->client_id)->first();
            if($client_profile){
                return response()->json(['data' => $client_profile], 200);
            }else{
                return response()->json(['error' => 'Profile Not Found'],401);
            }
        }else{
            return response()->json(['error'=>'please login form therapist account'], 401); 
        }
    }
    
//admin can get assignd patient profiles
    public function getAssignPatient()
    {
        if(auth()->user()->hasRole('admin')){
            $assigned_patients = ClientProfile::with('user','diary')->where('check_assigned', 2)->get();
            if($assigned_patients){
                return response()->json(['data' => $assigned_patients], 200);
            }else{
                return response()->json('Empty Record', $this->notFound);
            }
        }
        else{
            return response()->json(['error'=>'Please Login From Admin Account'],401);
        }
    }

}
