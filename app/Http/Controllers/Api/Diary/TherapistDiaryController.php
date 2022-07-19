<?php

namespace App\Http\Controllers\Api\Diary;

use App\ClientProfile;
use App\Diary;
use App\Http\Controllers\Controller;
use App\TherapistDiary;
use App\TherapistProfile;
use App\User;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;


class TherapistDiaryController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'string', 'max:255']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_role = Auth::user()->roles->pluck('name');
        $user_id = Auth()->user()->id;
        if ($user_role[0] != 'therapist') {
            return response()->json('Please login from Therapist Account', $this->permissionDenied);
        }
        $therapist_profile_id = TherapistProfile::where('user_id', $user_id)->first();
        $client_profile_id = User::where('id', $request->client_id)->first();
        $client_profile_role = $client_profile_id->roles->pluck('name');
        if ($client_profile_role[0] == 'client') {
            $client_profile_id = ClientProfile::where('user_id', $client_profile_id->id)->first();
        } else {
            return response()->json('Given ID is not of Client', $this->notFound);
        }
        if (!isset($therapist_profile_id->id)) {
            return response()->json('Profile Not found', $this->notFound);
        }
        //filters
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $number_of_records = $request->number_of_records;
        if ($number_of_records == '' || $number_of_records == null) {
            $number_of_records = 10;
        }
        if ($start_date == '' || $start_date == null) {
            $start_date = Date('Y-m-d');
        }
        if ($end_date == '' || $end_date == null) {
            $end_date = Date('Y-m-d H:i:s');
        }
        $therapist_profile_id = $therapist_profile_id->id;
        $client_profile_id = $client_profile_id->id;
        $qCheck = TherapistProfile::
        with('user', 'therapist_client_diary', 'therapist_client_diary.client_profile', 'therapist_client_diary.client_profile.user')
            ->where('id', $therapist_profile_id)
            ->whereHas('therapist_client_diary', function ($q) use ($therapist_profile_id) {
                $q->where('therapist_profile_id', $therapist_profile_id);
            })
            ->whereHas('therapist_client_diary', function ($q) use ($client_profile_id) {
                $q->where('client_profile_id', $client_profile_id);
            })
            ->where(function ($query) use ($start_date, $end_date) {
                $query->where(function ($q) use ($start_date, $end_date) {
                    $q->where('created_at', '>=', $start_date . '%');
                    $q->orWhere('created_at', '<=', $end_date . '%');
                });
            });
//        $query = str_replace(array('?'), array('\'%s\''), $qCheck->toSql());
//        $query = vsprintf($query, $qCheck->getBindings());
//        dd($query);
//        $qCheck = User::with('therapist_profile', 'therapist_profile.therapist_client_diary.client')
//            ->whereHas('therapist_profile', function ($q) use ($therapist_profile_id) {
//                $q->where('id', $therapist_profile_id);
//            })
//            ->whereHas('therapist_profile.therapist_client_diary.client_profile', function ($q1) use ($client_profile_id) {
//                $q1->where('client_profile_id', $client_profile_id);
//            });

//            $qCheck->where(function ($query) use ($start_date, $end_date) {
//                $query->where(function ($q) use ($start_date, $end_date) {
//                    $q->where('created_at', '>=', $start_date . '%');
//                    $q->orWhere('created_at', '<=', $end_date . '%');
//                });
//            });

        $data = $qCheck->paginate($number_of_records)->appends(['start_date' => $start_date, 'end_date' => $end_date]);
        return response()->json(['data' => $data, 'start_date' => $start_date, 'end_date' => $end_date], $this->successStatus);

    }

    public function edit(Request $request, $id)
    {
        $user_role = Auth::user()->roles->pluck('name');
        $user_id = Auth()->user()->id;
        if ($user_role[0] != 'therapist') {
            return response()->json('Please login from Therapist Account', $this->permissionDenied);
        }

        //filters
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $number_of_records = $request->number_of_records;
        if ($number_of_records == '' || $number_of_records == null) {
            $number_of_records = 10;
        }
        if ($start_date == '' || $start_date == null) {
            $start_date = Date('Y-m-d');
        }
        if ($end_date == '' || $end_date == null) {
            $end_date = Date('Y-m-d H:i:s');
        }

        $qCheck = TherapistProfile::
        with('user', 'therapist_client_diary', 'therapist_client_diary.client_profile', 'therapist_client_diary.client_profile.user')
            ->whereHas('therapist_client_diary', function ($q) use ($id) {
                $q->where('id', $id);
            })
            ->where(function ($query) use ($start_date, $end_date) {
                $query->where(function ($q) use ($start_date, $end_date) {
                    $q->where('created_at', '>=', $start_date . '%');
                    $q->orWhere('created_at', '<=', $end_date . '%');
                });
            });

        $data = $qCheck->paginate($number_of_records)->appends(['start_date' => $start_date, 'end_date' => $end_date]);
        return response()->json(['data' => $data, 'start_date' => $start_date, 'end_date' => $end_date], $this->successStatus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mood' => ['required', 'string', 'max:255'],
            'anxiety' => ['required', 'string', 'max:255'],
            'energy' => ['required', 'string', 'max:255'],
            'self_confidence' => ['required', 'string', 'max:255'],
            'feeling' => ['required', 'string', 'max:255'],
            'client_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_role = Auth::user()->roles->pluck('name');
        $user_id = Auth()->user()->id;
        if ($user_role[0] != 'therapist') {
            return response()->json('Please login from Therapist Account', $this->permissionDenied);
        }
        $input = $request->except(['client_id']);
        $client_profile_id = ClientProfile::where('user_id', $request->client_id)->first();
        if (isset($client_profile_id->id)) {
            $input['client_profile_id'] = $client_profile_id->id;
        } else {
            return response()->json('Client Profile does not exists', $this->notFound);
        }
        $therapist_profile_id = TherapistProfile::where('user_id', $user_id)->first();
        if (isset($therapist_profile_id->id)) {
            $input['therapist_profile_id'] = $therapist_profile_id->id;
        } else {
            return response()->json('Therapist Profile does not exists', $this->notFound);
        }

        $store = TherapistDiary::create($input);
        return response()->json('Success', $this->storeStatus);

    }
    //getclientdiary
    public function getClientDiary(Request $request){
        if(auth()->user()->hasRole('therapist')){
            $profile_id = ClientProfile::where('user_id', $request->client_id)->first();
            if (!isset($profile_id->id)) {
                return response()->json('Profile Not found', $this->notFound);
            }
            //filters
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $number_of_records = $request->number_of_records;
            if ($number_of_records == '' || $number_of_records == null) {
                $number_of_records = 10;
            }
            if ($start_date == '' || $start_date == null) {
                $start_date = Date('Y-m-d');
            }
            if ($end_date == '' || $end_date == null) {
                $end_date = Date('Y-m-d H:i:s');
            }
            $qCheck = Diary::where(function ($query) use ($profile_id, $start_date, $end_date) {
                $query->where('client_profile_id', $profile_id->id)->where('status', 1);
                $query->where(function ($q) use ($start_date, $end_date) {
                    $q->whereDate('created_at', '>=', $start_date . '%');
                    $q->WhereDate('created_at', '<=', $end_date . '%');
                });
            });
            
            $data = $qCheck->paginate($number_of_records)->appends(['start_date' => $start_date, 'end_date' => $end_date]);

            if($data->isNotEmpty()){
                return response()->json(['data' => $data, 'start_date' => $start_date, 'end_date' => $end_date], $this->successStatus);
            }else{
                return response()->json(['error' => 'Empty Record'], 401);
            }
        }else{
            return response()->json('Please login from therapist account', 401);
        }
    }
}
