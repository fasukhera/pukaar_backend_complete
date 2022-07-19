<?php

namespace App\Http\Controllers\Api\Session;

use App\ClientProfile;
//use App\DonateSession;
use App\Events\OneOnOneSessionEvent;
use App\Events\OneOnOneSessionResponse;
use App\Http\Controllers\Controller;
use App\OneOnOneSession;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Session;
use App\SessionLog;
use Illuminate\Support\Facades\Auth;
use Validator;

class SessionController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public $unknownStatus = 225;
    public $sessionNotPaid = 402;

    public function index(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($request->donation == 1 || $request->donation == 'Donate') {
            if ($user_role[0] == 'admin') {
                $data = Session::with('user')->where('session_status', 'donated')->paginate(10);
            } else {
                $data = Session::with('user')->where('user_id', auth()->user()->id)->where('session_status', 'donated')->paginate(10);
            }
            return response()->json($data, $this->successStatus);
        } else {
            if ($user_role[0] == 'admin') {
                $data = Session::with('user')->where('session_status', '!=', 'donated')->paginate(10);
            } else {
                $data = Session::with('user')->where('user_id', auth()->user()->id)->where('session_status', '!=', 'donated')->paginate(10);
            }
        }
        if ($data == '' || $data == null) {
            return response()->json($data, $this->notFound);
        }
        return response()->json($data, $this->successStatus);
    }

    public function store(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] == 'admin') {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000', // max 10000kb
            'cost' => 'required',
            'number_of_sessions' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        //condition: if user has valid paid session in DB
//        $user_session_valid_check = Session::where('user_id', Auth::user()->id)->where('session_status', 'approved')->first();
//        if ($user_session_valid_check != null || $user_session_valid_check != '') {
        $file = $request->file('picture');
        $current = Carbon::now();
        $current = str_replace(' ', '_', $current);
        $current = str_replace(' ', '', $current);
        $current = str_replace('-', '', $current);
        $current = str_replace(':', '', $current);
        $fileName = $current . '.' . $file->getClientOriginalExtension();
        $destination_path = storage_path('app/public/');
        $file->move($destination_path, $fileName);
        $destination_path = '/storage/' . $fileName;
        $data['picture'] = $destination_path;
        $data['user_id'] = auth()->user()->id;

        $data['cost'] = $request->cost;
        $data['number_of_sessions'] = $request->number_of_sessions;
        if ($request->donation == '1' || $request->donation == 'Yes' || $request->donation == 'yes' || $request->donation == 'Donate') {
            $data['session_status'] = 'donated';
        } else {
            $data['session_status'] = 'pending';
        }
        Session::create($data);
        return response()->json('success', $this->storeStatus);
//        } else
//            return response()->json('No paid sessions found in record', $this->permissionDenied);
    }

    public function destroy($id)
    {
        $user = Session::findOrFail($id);
        $user->delete();
        return response()->json('success', $this->successStatus);
    }

    public function admin_session_index(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] != 'admin') {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        if (!isset($request->donation)) {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
        }
        if ($request->donation == '1' || $request->donation == 'Yes' || $request->donation == 'yes') {
            $data = Session::with('user')->where('session_status', 'donated')->paginate(10);
        } else {
            $data = Session::with('user')->where('session_status', $request->status)->paginate(10);
        }
//        $no_of_sessions = 0;
//        foreach ($data as $record) {
//            $no_of_sessions += $record->number_of_sessions;
//        }
        return response()->json($data, $this->successStatus);
//        return response()->json(['data' => $data, 'no_of_sessions' => $no_of_sessions], $this->successStatus);
    }

    public function admin_session_edit(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] != 'admin') {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $data = Session::with('user')->where('id', $request->id)->first();
        if ($data == null || $data == '') {
            return response()->json($data, $this->notFound);
        }
        return response()->json($data, $this->successStatus);
    }

    public function admin_session_update(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] != 'admin') {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'session_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $data = Session::where('id', $request->session_id)->first();
        if ($data == null || $data == '') {
            return response()->json('Not Found', $this->notFound);
        }
        $data = Session::where('id', $request->session_id)->update([
            'session_status' => $request->status
        ]);
        return response()->json('Success', $this->successStatus);
    }

    public function oneOnOneSession(Request $req)
    {
        //// Unknown status was requested by Adnan since he was unable to handle multiple statuses

        //check assigned therapist
        $client_id = Auth::user()->id;
//        $client_id = $req->client_id;//Auth->id;
        $profile_data = ClientProfile::where('user_id', $client_id)->first();
        if (isset($profile_data->therapist_profile_id)) {
            $therapist = $profile_data->therapist_profile_id;
        } else {
            $therapist = '';
        }
        if ($therapist != null || $therapist != '') {
            $therapist_id = $therapist;
        } else {
            return response()->json(['response' => 'Therapist Not Assigned'], $this->unknownStatus);
        }
        $validator = Validator::make($req->all(), [
            'session_require_time' => 'required'
        ]);
        //request accept
        if (isset($req->request_id) && $req->status == 1) {
            $accept = $req->except('request_id', 'client_id');
            $notify_data = "Request For One On One Session Accepted";
            $accept['data'] = $notify_data;
            $adata = OneOnOneSession::where('id', $req->request_id)->update($accept);
            $therapist = Auth::user();
            $therapist = User::where('id', $therapist->id)->first();
            $data['client_id'] = $req->client_id;
            $data['status'] = $req->status;
            $data['therapist_name'] = $therapist->first_name . $therapist->last_name;
            $data['email'] = $therapist->email;
            $data['mobile_number'] = $therapist->mobile_number;
            $data['data'] = $notify_data;
            event(new OneOnOneSessionResponse($data));

            return response()->json(['response' => $data], $this->unknownStatus);
        } //request reject
        elseif (isset($req->request_id) && $req->status == 2) {
            $user_role = Auth::user()->roles->pluck('name');
            if ($user_role[0] == 'therapist') {
                $reject = $req->except('request_id', 'client_id');
                $notify_data = "Request For One On One Session Rejected";
                $accept['data'] = $notify_data;
                $rdata = OneOnOneSession::where('id', $req->request_id)->update($accept);
                $therapist = Auth::user();
                $therapist = User::where('id', $therapist->id)->first();
                $data['therapist_id'] = Auth::user()->id;
                $data['client_id'] = $req->client_id;
                $data['status'] = $req->status;
                $data['therapist_name'] = $therapist->first_name . $therapist->last_name;
                $data['email'] = $therapist->email;
                $data['mobile_number'] = $therapist->mobile_number;
                $data['data'] = $notify_data;
                event(new OneOnOneSessionResponse($data));
                return response()->json(['response' => $data], $this->unknownStatus);
            } else {
                return response()->json(['response' => 'Permission Denied'], $this->unknownStatus);
            }
        } //request sent
        else {
            //condition: if user has valid paid session in DB
            $user_session_valid_check = Session::where('user_id', $client_id)->where('session_status', 'approved')->first();
            if ($user_session_valid_check != null || $user_session_valid_check != '') {
                $request_sent = OneOnOneSession::where('client_id', $client_id)->where('therapist_id', $therapist_id)->where('status', 0)->first();
                //already sent
                if ($request_sent != null || $request_sent != '') {
//                    $request_already_sent = 'Request already have been sent';
                    return response()->json(['response' => 'Request already have been sent'], $this->unknownStatus);
                } else {
                    $notify_data = "Request For One On One Session Submitted";
                    $request['data'] = $notify_data;
                    $request['therapist_id'] = $therapist_id;
                    $request['client_id'] = $client_id;//Auth->id
                    $request['session_require_time'] = $req->session_require_time;
                    $client = User::where('id', $client_id)->first();
                    $cdata = OneOnOneSession::create($request);
                    $data['request_id'] = $cdata->id;
                    $data['client_id'] = $cdata->client_id;
                    $data['therapist_id'] = $cdata->therapist_id;
                    $data['session_require_time'] = $cdata->session_require_time;
                    $data['status'] = $cdata->status;
                    $data['client_name'] = $client->first_name . $client->last_name;
                    $data['email'] = $client->email;
                    $data['mobile_number'] = $client->mobile_number;
                    $data['data'] = $notify_data;
                    event(new OneOnOneSessionEvent($data));
                    return response()->json(['response' => $data], $this->unknownStatus);
                }
            } else {
                return response()->json(['response' => 'Your payment for one/one session is not submit yet'], $this->unknownStatus);
            }
        }
    }

    //Session Taken
    public function takenSession(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] != 'therapist') {
            return response()->json(['status' => 'Unauthorized'], $this->unauthorizedStatus);
        }
        $user_sessions = Session::where('user_id', $request->user_id)->first();
        $user_no_sessions = $user_sessions->number_of_sessions;
        $num = 5.7;
        $session_taken = $request->session_taken;
        $session_taken_minutes = $session_taken;
        $session_taken = $session_taken / 45;
        if (is_float($session_taken)) {
            $whole = (int)$session_taken;
            $frac = $session_taken - $whole;
            if ($frac < 0.02222222222) {
                $session_taken = floor($session_taken);
            } else {
                $session_taken = ceil($session_taken);
            }
        }
        $session_taken = (int)$session_taken;
        $remaining_sessions = $user_no_sessions - $session_taken;
        if ($remaining_sessions < 0) {
            return response()->json(['status' => 'User have only ' . $user_no_sessions . ' sessions left'], $this->sessionNotPaid);
        }
        $session['no_of_session'] = $remaining_sessions;

        $session = Session::where('user_id', $request->user_id)->update(['number_of_sessions' => $remaining_sessions]);
        $session_log['client_id'] = $request->user_id;
        $session_log['therapist_id'] = Auth::user()->id;
        $session_log['session_taken'] = $session_taken;
        $session_taken_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . "-" . $session_taken_minutes));
        $session_log['session_taken_time'] = $session_taken_time;

        $session_logs = SessionLog::create($session_log);

        return response()->json(['status' => 'Session Taken Successfully'], $this->storeStatus);
    }

    //Session Logs
    public function sessionLogs()
    {
        //admin side
        if (Auth::user()->hasRole('admin')) {
            $session_logs = SessionLog::with('client', 'therapist')->get();
            if ($session_logs->isNotEmpty()) {
                return response()->json($session_logs);
            } else {
                return response()->json(['status' => 'Empty Logs'], 405);
            }
        } //therapist side
        elseif (Auth::user()->hasRole('therapist')) {
            $session_logs = SessionLog::with('client')->where('therapist_id', Auth::user()->id)->get();
            if ($session_logs->isNotEmpty()) {
                return response()->json($session_logs);
            } else {
                return response()->json(['status' => 'Empty Logs'], 405);
            }
        } //client side
        elseif (Auth::user()->hasRole('client')) {
            $session_logs = SessionLog::with('therapist')->where('client_id', Auth::user()->id)->get();
            if ($session_logs->isNotEmpty()) {
                return response()->json($session_logs);
            } else {
                return response()->json(['status' => 'Empty Logs'], 405);
            }

        }
    }
}
