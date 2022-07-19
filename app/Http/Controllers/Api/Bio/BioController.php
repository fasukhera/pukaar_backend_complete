<?php

namespace App\Http\Controllers\Api\Bio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bio;
use Auth;
use Validator;
use Carbon\Carbon;

class BioController extends Controller
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
            $data = Bio::with('user')->where('user_id', $user_id)->first();
            if ($data == '' || $data == null) {
                return response()->json($data, $this->notFound);
            }
        } else {
            return response()->json("failed", $this->permissionDenied);
        }
        return response()->json($data, $this->successStatus);
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
            'state' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $data = $request->all();

        $file = $request->file('picture');
        $current = Carbon::now();
        $current = str_replace(' ', '_', $current);
        $current = str_replace(' ', '', $current);
        $current = str_replace('-', '', $current);
        $current = str_replace(':', '', $current);
        $fileName = $current . '.' . $file->getClientOriginalExtension();
        $destination_path = storage_path('app/public/Bio');
        $file->move($destination_path, $fileName);
        $destination_path = '/storage/' . $fileName;
        $data['picture'] = $destination_path;

        $data['user_id'] = auth()->user()->id;

        /* Bio::create($data);
        return response()->json('success', $this->storeStatus); */
        $check = Bio::where('user_id', $data['user_id'])->first();
        if ($check == '' || $check == null) {
            Bio::create($data);
        } else {
            return response()->json('Already Exist', $this->permissionDenied);
        }
        return response()->json('success', $this->storeStatus);
    }

    public function edit($id)
    {
        $data = Bio::findOrFail($id);
        if ($data == '' || $data == null) {
            return response()->json('', $this->notFound);
        }
        return response()->json($data, $this->storeStatus);
    }

    public function update(Request $request)
    {    
        $data = $request->all();
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $current = Carbon::now();
            $current = str_replace(' ', '_', $current);
            $current = str_replace(' ', '', $current);
            $current = str_replace('-', '', $current);
            $current = str_replace(':', '', $current);
            $fileName = $current . '.' . $file->getClientOriginalExtension();
            $destination_path = storage_path('app/public/Bio');
            $file->move($destination_path, $fileName);
            $destination_path = '/storage/' . $fileName;
            $data['picture'] = $destination_path;
        }

        $check = Bio::where('user_id', auth()->user()->id)->first();
        
        if ($check == '' || $check == null) {
            return response()->json('Please Create Profile First', $this->permissionDenied);
        } else {
            Bio::where('id', $check->id)->update($data);
        }
        return response()->json('success', $this->successStatus);
    }

    public function destroy($id)
    {
        $user = Bio::findOrFail($id);
        $user->delete();
        return response()->json('success', $this->successStatus);
    }
}
