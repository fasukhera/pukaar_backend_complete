<?php

namespace App\Http\Controllers\Api\Diary;

use App\ClientProfile;
use App\Diary;
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Controllers\Controller;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Validator;

class DiaryController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        $profile_id = ClientProfile::where('user_id', $user_id)->first();
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
        //return $start_date;
        $qCheck = Diary::where(function ($query) use ($profile_id, $start_date, $end_date) {
            $query->where('client_profile_id', $profile_id->id);
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
    }

    //get graphic diary
    public function getGraphicDiary(Request $request)           
    {
        if(auth()->user()->hasRole('client')){
            $user_id = auth()->user()->id;
            $profile_id = ClientProfile::where('user_id', $user_id)->first();
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
            //return $start_date;
            $qCheck = Diary::where(function ($query) use ($profile_id, $start_date, $end_date) {
                $query->where('client_profile_id', $profile_id->id);
                $query->where(function ($q) use ($start_date, $end_date) {
                    $q->whereDate('created_at', '>=', $start_date . '%');
                    $q->WhereDate('created_at', '<=', $end_date . '%');
                });
            });

            $data = $qCheck->paginate($number_of_records)->appends(['start_date' => $start_date, 'end_date' => $end_date]);
            
            $graph = array();

            if($data->isNotEmpty()){
                $anxiety_value = 0;
                $confidence_value = 0;
                $positive = 0;
                $negetive = 0;
                foreach($data as $diary=>$values){
                    $graph[] = array('mood' => $values->mood, 'anxiety' => $values->anxiety, 'confidence' => $values->self_confidence);
                    $anxiety_value +=  $values->anxiety;
                    $confidence_value =+ $values->self_confidence;
                    if($values->mood == 'Ok'){
                        $positive += 1;
                    }
                    elseif($values->mood != 'Ok'){
                        $negetive += 1;
                    }
                }
                $graph[] = array('total_mood' => count($graph),'anxiety' => $anxiety_value,'confidence' => $confidence_value,
                'positive_entries' => $positive,'negetive_entries' => $negetive);
                /* $graph['total_mood'] = count($graph);
                $graph['anxiety'] = $anxiety_value;
                $graph['confidence'] = $confidence_value;
                $graph['positive_entries'] = $positive;
                $graph['negetive_entries'] = $negetive; */

                return response()->json(['graph' => $graph, 'start_date' => $start_date, 'end_date' => $end_date], $this->successStatus);
            }else{
                return response()->json(['error' => 'Empty Record'], 401);
            }
        }else{
            return response()->json(['status'=>'Please Login Form Client Account'], 401);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mood' => ['required', 'string', 'max:255'],
            'anxiety' => ['required', 'string', 'max:255'],
            'energy' => ['required', 'string', 'max:255'],
            'self_confidence' => ['required', 'string', 'max:255'],
            'feeling' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_id = auth()->user()->id;
        $profile_id = ClientProfile::where('user_id', $user_id)->first();
        if (!isset($profile_id->id)) {
            return response()->json('Profile Not found', $this->notFound);
        }
        $data = $request->all();
        $data['client_profile_id'] = $profile_id->id;
        $data = Diary::create($data);
        return response()->json('Success', $this->successStatus);
    }

    public function edit($id)
    {
        $data = Diary::findOrFail($id);
        if ($data == '' || $data == null) {
            return response()->json('', $this->notFound);
        }
        return response()->json($data, $this->storeStatus);
    }

    public function update(Request $request, $id)
    {
        $user_id = auth()->user()->id;
        $profile_id = ClientProfile::where('user_id', $user_id)->first();
        if (!isset($profile_id->id)) {
            return response()->json('Profile Not found', $this->notFound);
        }
        $data = $request->all();
        $data = Diary::where('id', $id)->update($data);
        return response()->json('Diary Update', $this->successStatus);
    }

    public function destroy($id)
    {
        $user = Diary::findOrFail($id);
        $user->delete();
        return response()->json('success', $this->successStatus);
    }

    public function shareDiary(Request $request){
        if(auth()->user()->hasRole('client')){
            $client_profile = ClientProfile::where('user_id', auth()->user()->id)->first();
            $share = Diary::where('client_profile_id', $client_profile->id)->update(['status' => $request->status]);
            return response()->json('Your Diary Shared with Therapist', 201);
        }else{
            return response()->json('Please login from client account', 401);
        }
    }
}
