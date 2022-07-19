<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Reset;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public $successStatus = 200;

    public function update(Request $request)
    {
        $data = Reset::where('email', '=', $request->email)->first();
        $dataa = User::where('email', '=', $request->email)->first();
        if ($dataa == null) {
            return response()->json(['error' => 'User does not exist!'], 401);
        }
        else
            if ($request->code == $data->code) {
            User::where('email', '=', $request->email)->update(['password' => Hash::make($request['password'])]);
            return response()->json(['success' => 'Password Updated!'], $this->successStatus);
        } else {
            return response()->json(['error' => 'Something went Wrong!'], 401);
        }
    }

    function index()
    {
        return view('new_password');
    }
}
