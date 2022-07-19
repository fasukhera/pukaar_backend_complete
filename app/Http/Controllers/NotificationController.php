<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\notification;
use Auth;
use App\Events\NotificationEvent;

class NotificationController extends Controller
{
    public function index($userId){
        $users = User::where('id', '!=', Auth::id())->get();
        $friendInfo = User::findOrFail($userId);
        $myInfo = User::find(Auth::id());

        $this->data['users'] = $users;
        $this->data['friendInfo'] = $friendInfo;
        $this->data['myInfo'] = $myInfo;

        return view('notification.notify', $this->data);
    }

    public function notify(Request $request){
        $request->validate([
            'reciever_id' => 'required',
        ]);
        $data = collect([
            'sender_id' => Auth::User()->id,
            'sender_name' => Auth::User()->first_name,
            'reciever_id' => $request->reciever_id,
            'data'=> 'Request For Chat',
        ]);
        event(new NotificationEvent($data));
        $notify['sender_id'] = Auth::User()->id;
        $notify['reciever_id'] = $request->reciever_id;
        $notify['data'] = 'Request For Chat';

        notification::create($notify);
        return response()->json(['success' => "Notification Send Successfully"]);
    }

    public function getNotification($userId){
        $notification = notification::with('sender')->where('reciever_id', $userId)->get();
        return response()->json($notification);
    }
}
