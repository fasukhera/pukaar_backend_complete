<?php

namespace App\Http\Controllers\Api\Package;

use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\Request;
use Validator;

class PackageController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index()
    {
        $data = Package::get();
        return response()->json($data, $this->successStatus);
    }

    public function store(Request $request)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'number_of_sessions' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $data = $request->all();
        Package::create($data);
        return response()->json('success', $this->storeStatus);
    }

    public function edit($id)
    {
        $data = Package::findOrFail($id);
        if ($data == '' || $data == null) {
            return response()->json('Not Found', $this->notFound);
        }
        return response()->json($data, $this->storeStatus);
    }

    public function update(Request $request, $id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $data = $request->all();
        Package::where('id', $id)->update($data);
        return response()->json('success', $this->successStatus);
    }

    public function destroy($id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $user = Package::findOrFail($id);
        $user->delete();
        return response()->json('success', $this->successStatus);
    }
}
