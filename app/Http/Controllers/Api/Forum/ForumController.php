<?php

namespace App\Http\Controllers\Api\Forum;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ForumController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index()
    {
//        $user_role = Auth::user()->roles->pluck('name');
//        if ($user_role[0] == 'admin') {
//            return response()->json('Please login from Client / Therapist Account', $this->permissionDenied);
//        }
        $data = Post::with('comments','user')->latest()->paginate(10);
        return response()->json($data, $this->successStatus);
    }

    public function store_post(Request $request)
    {
//        $user_role = Auth::user()->roles->pluck('name');
//        if ($user_role[0] == 'admin') {
//            return response()->json('Please login from Client / Therapist Account', $this->permissionDenied);
//        }
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_id = Auth()->user()->id;
        $input = $request->except(['picture']);
        $input['user_id'] = $user_id;
        if ($request->has('picture')) {
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
            $input['picture'] = $destination_path;
        }
        Post::create($input);
        return response()->json('Success', $this->storeStatus);
    }

    public function store_comment(Request $request)
    {
//        $user_role = Auth::user()->roles->pluck('name');
//        if ($user_role[0] == 'admin') {
//            return response()->json('Please login from Client / Therapist Account', $this->permissionDenied);
//        }
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'post_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['user_id'] = Auth()->user()->id;
        Comment::create($input);
        return response()->json('Success', $this->storeStatus);
    }

    public function showComment($post_id)
    {
        $comments = Comment::with('user')->where('post_id', $post_id)->get();
        if($comments->isNotEmpty()){
            return response()->json(['success' => $comments], 200);
        }else{
            return response()->json(['status' => 'This Post Has No Comment Yet'], $this->notFound);
        }
    }

}
