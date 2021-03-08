<?php

namespace App\Http\Controllers\Crud;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request) 
    {
        $users = User::orderBy('first_name', 'ASC')
            ->paginate(3);
        return response()->json([
            "total" => $users->total(),
            "perPage" => $users->perPage(),
            "currentPage" => $users->currentPage(),
            "lastPage" => $users->lastPage(),
            'data' => UserResource::collection($users)
        ], 200);
    }

    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|file|mimes:jpeg,jpg,bmp,png|max:10000',
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:users',
            'gender' => 'required|in:male,female',
            
        ]);

        if ($validator->fails()) {
            return response()->json([
               'message' => 'Validation Error',
               'formErrors' => $validator->errors()
            ], 412);
        }

        $extension_name = $request->avatar->getClientOriginalExtension();
        $image_path = $request->avatar->storeAS('images', uniqid().'.'.$extension_name);

        $user = new User();
        $user->avatar = $image_path;
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->save();

        return response()->json([
            'message' => 'New user has been added successfully!',
            'images' => UserResource::collection(User::all()),
        ], 200);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
               'message' => 'Validation Error',
               'formErrors' => $validator->errors()
            ], 412);
        }

        $keyword = strtoupper($request->input('query'));
      
        $searchUsers = User::where('first_name', 'LIKE', $keyword.'%')->get();
        
        return response()->json([
            'data' => UserResource::collection($searchUsers)
        ], 200);
    }
}
