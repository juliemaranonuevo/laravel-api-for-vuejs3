<?php

namespace App\Http\Controllers\Crud;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
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
}
