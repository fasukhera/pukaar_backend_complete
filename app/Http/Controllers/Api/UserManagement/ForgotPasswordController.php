<?php

namespace App\Http\Controllers\Api\UserManagement;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\ResetPassword;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\User;

class ForgotPasswordController extends Controller
{
    public $successStatus = 200;

    function send(Request $request)
    {
        //checking in database if the user email exist before saving
        $check = User::where('email', '=', $request->email)->first();

        if ($check != null) {
            $this->validate($request, [
                'email' => 'required|email',
            ]);
            $data = uniqid();
            $subject = "Reset Password Token";
            $headers = 'From: info@pukaar.com' . "\r\n" .
            'Reply-To: '.$request->email.'' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
            mail($request->email, $subject, $data, $headers);
            $checkReset = ResetPassword::where('email', '=', $request->email)->first();
            if ($checkReset == null) {
                ResetPassword::create([
                    'email' => $request->email,
                    'token' => $data
                ]);

            } else {
                ResetPassword::where('email', '=', $request->email)
                    ->update([
                        'token' => $data
                    ]);

            }
            return response()->json(['success' => 'Check email for code!'], $this->successStatus);
        }
        return response()->json(['error' => 'Email does not exist!'], 401);
    }


}
