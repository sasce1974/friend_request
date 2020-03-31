<?php

namespace App\Http\Controllers;

use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * Returns all the users from the User model in a JSON
     */
    public function index(){
        return response()->json(User::get(), 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * Returns JSON the data of the requested user by the ID
     */
    public function show($id){
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(["message"=>"Record not found!"], 404);
        }
        return response()->json($user, 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request){

        if(!User::isAuthorized()){
            return response()->json(["message"=>"Not Authorized!"], 401);
        }


        $rules = [
            'name'=>'required|max:100',
            'email'=>'required|email|unique:users',
            'password'=>'required|between:8,20',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return response()->json($user, 201);

    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        if(!User::isAuthorized()){
            return response()->json(["message"=>"Not Authorized!"], 401);
        }

        $user = User::find($id);

        if(is_null($user)){
            return response()->json(["message"=>"Record not found!"], 404);
        }

        $rules = [
            'name'=>'sometimes|required|max:100',
            'email'=>'sometimes|required|email|unique:users',
            'password'=>'sometimes|required|between:8,20',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $input = $request->all();
        if(isset($input['password'])){
            $input['password'] = bcrypt($input['password']);
        }

        $user->update($request->all());
        return response()->json($user, 200);
    }


    public function delete(Request $request, $id){

        if(!User::isAuthorized()){
            return response()->json(["message"=>"Not Authorized!"], 401);
        }

        $user = User::find($id);
        if(is_null($user)){
            return response()->json(["message"=>"Record not found!"], 404);
        }
        $user->delete();
        return response()->json(null, 204);
    }

}
