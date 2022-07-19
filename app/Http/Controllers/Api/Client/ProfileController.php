<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\ClientProfile;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'client') {
            $user_id = auth()->user()->id;
            $data = ClientProfile::with('user', 'therapist', 'therapist.user')->where('user_id', $user_id)->first();
            if ($data == '' || $data == null) {
                return response()->json($data, $this->notFound);
            }
        } else {
            return response()->json("failed", $this->permissionDenied);
        }
        if($request->type == 'therapist_only'){
            $data = $data->therapist;
            return response()->json($data, $this->successStatus);
        }
        return response()->json($data, $this->successStatus);
    }

    public function store(Request $request)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'client') {
            return response()->json('Unauthorized', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'orientation' => ['required', 'string', 'max:255'],
            'religion' => ['required', 'string', 'max:255'],
            'religion_identifier' => ['required', 'string', 'max:255'],
            'medicines' => ['required', 'string', 'max:255'],
            'sleeping_habit' => ['required', 'string', 'max:255'],
            'problem' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $data = $request->all();
        
        $data['user_id'] = auth()->user()->id;
        $check = ClientProfile::where('user_id', $data['user_id'])->first();
        if ($check == '' || $check == null) {
            ClientProfile::create($data);
        } else {
            return response()->json('Already Exist', $this->permissionDenied);
        }
        return response()->json('success', $this->storeStatus);
    }

    public function edit($id)
    {
        $data = ClientProfile::findOrFail($id);
        if ($data == '' || $data == null) {
            return response()->json('', $this->notFound);
        }
        return response()->json($data, $this->storeStatus);
    }

    public function update(Request $request, $id)
    {
        //return $data = $request->all();
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'client') {
            return response()->json('failed', $this->permissionDenied);
        }

        $check = ClientProfile::where('id', $id)->first();

        $data = $request->all();
        
        if ($check == '' || $check == null) {
            return response()->json('Please Create Profile First', $this->permissionDenied);
        } else {
            ClientProfile::where('id', $id)->update($data);
        }
        return response()->json('success', $this->successStatus);
    }

    public function destroy($id)
    {
        $user = ClientProfile::findOrFail($id);
        $user->delete();
        return response()->json('success', $this->successStatus);
    }

    //below method includes user update as well
    public function update_complete_profile(Request $request)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'client') {
            return response()->json('Unauthorized', $this->permissionDenied);
        }
        $user_id = Auth()->user()->id;
        $client_profile = ClientProfile::where('user_id', $user_id)->first();
        if ($client_profile != '' || $client_profile != null) {
            $client_user_input = $request->only([
                'first_name',
                'last_name',
                'email',
                'mobile_number'
            ]);
            $client_profile_input = $request->only([
                'orientation',
                'religion',
                'religion_identifier',
                'medicines',
                'sleeping_habit',
                'problem',
            ]);

            if ($client_profile_input != '' || $client_profile_input != null) {
                ClientProfile::where('user_id', $user_id)->update($client_profile_input);
            } else {
                return response()->json('Already Exist', $this->permissionDenied);
            }
            if ($client_user_input != '' || $client_user_input != null) {
                User::where('id', $user_id)->update($client_user_input);
            } else {
                return response()->json('Already Exist', $this->permissionDenied);
            }
            return response()->json('Success', $this->successStatus);
        } else {
            return response()->json('Already Exist', $this->permissionDenied);
        }
    }

    public function client_therapist(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'client') {
            $user_id = auth()->user()->id;
            $client_data = ClientProfile::with(['therapist.user'])->where('user_id', $user_id)->first();
            if ($client_data == '' || $client_data == null) {
                    $data = $client_data->therapist->user;

                return response()->json($data, $this->notFound);
            }
        } else {
            return response()->json("failed", $this->permissionDenied);
        }
        return response()->json($data, $this->successStatus);
    }
}
