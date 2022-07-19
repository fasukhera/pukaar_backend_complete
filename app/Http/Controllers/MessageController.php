<?php

namespace App\Http\Controllers;

use App\UserMessage;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Message;
use App\Events\PrivateMessageEvent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'reciever_id' => 'required',
        ]);
        $sender_id = Auth::id();
        $reciever_id = $request->reciever_id;
        $chat_check1 = UserMessage::where('sender_id', $sender_id)->where('reciever_id', $reciever_id)->first();
        $chat_check2 = UserMessage::where('sender_id', $reciever_id)->where('reciever_id', $sender_id)->first();
        if ($chat_check1 || $chat_check2) {
            $messages = UserMessage::with('messages', 'receiver', 'sender')
                ->whereHas('receiver', function ($q) use ($reciever_id, $sender_id) {
                    $q->where('reciever_id', $reciever_id);
                    $q->orWhere('reciever_id', $sender_id);
                })
                ->orWhereHas('sender', function ($q) use ($sender_id, $reciever_id) {
                    $q->where('sender_id', $sender_id);
                    $q->orWhere('sender_id', $reciever_id);
                })->get();
            return response()->json(['messages' => $messages]);
        } else {
            return response()->json(['error' => 'Empty Chat']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function conversation($userId)
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $friendInfo = User::findOrFail($userId);
        $myInfo = User::find(Auth::id());

        $this->data['users'] = $users;
        $this->data['friendInfo'] = $friendInfo;
        $this->data['myInfo'] = $myInfo;
        $this->data['users'] = $users;

        return view('message.conversation', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'reciever_id' => 'required',
        ]);
        $sender_id = Auth::id();
        $reciever_id = $request->reciever_id;

        $message = new Message();
        $message->message = $request->message;

        if ($message->save()) {
            try {
                $message->users()->attach($sender_id, ['reciever_id' => $reciever_id]);
                $sender = User::where('id', $sender_id)->first();

                $data = [];
                $data['sender_id'] = $sender_id;
                $data['sender_name'] = $sender->name;
                $data['reciever_id'] = $reciever_id;
                $data['content'] = $message->message;
                $data['created_at'] = $message->created_at->toDateTimeString();
                $data['message_id'] = $message->id;

                event(new PrivateMessageEvent($data));

                return response()->json([
                    'data' => $data,
                    'success' => true,
                    'message' => 'Message Sent Successfully'
                ]);
            } catch (\Exception $e) {
                $message->delete();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
