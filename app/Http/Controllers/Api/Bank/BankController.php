<?php

namespace App\Http\Controllers\Api\Bank;

use App\Bank;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class BankController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index()
    {
        $data = Bank::get();
        return response()->json($data, $this->successStatus);
    }

    public function store(Request $request)
    {
        $user_role = Auth::user()->roles->pluck('name');
        if ($user_role[0] != 'admin') {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'bank_name' => ['required', 'string', 'max:255'],
            'branch_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_title' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $bank_exists = Bank::first();
        if (Bank::exists()) {
            Bank::where('id', '=', $bank_exists->id)->update($request->all());
        } else {
            Bank::create($request->all());
        }
        return response()->json('success', $this->storeStatus);
    }

    public function edit($id)
    {
        $data = Bank::findOrFail($id);
        if ($data == '' || $data == null) {
            return response()->json('', $this->notFound);
        }
        return response()->json($data, $this->storeStatus);
    }

    public function update(Request $request, $id)
    {
        if (Auth()->user()->cannot('be_admin')) {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        $data = $request->be_admin();
        Bank::where('id', $id)->update($data);
        return response()->json('success', $this->successStatus);
    }

    public function destroy($id)
    {
        if (Auth()->user()->cannot('be_admin')) {
            return response()->json('Permission Denied', $this->permissionDenied);
        }
        $user = Bank::findOrFail($id);
        $user->delete();
        return response()->json('success', $this->successStatus);
    }
}
