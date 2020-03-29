<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Friends;

class FriendsController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * This function returns the users that are friends
     * with the user with the parameter $id.
     * Column 'user_one' from the 'friends' table records the user id that sends friend requests.
     * Column 'user_two' from the 'friends' table records the user id to whom the friend requests
     * is sent.
     */
    public function friends ($id){
        $my_id = Auth::id();

        /** Two quarries gets the users data from the first column, then from the second
        * one of the friends table that are associated with the user ID (parameter @$id) */

        $sql1 = "SELECT f.id AS 'Request ID', u.id AS 'User ID', u.name, u.email FROM users u ,friends f WHERE u.id = f.user_two AND f.user_one = ? AND f.accepted = 1 AND NOT u.id = ?";
        $sql2 = "SELECT f.id AS 'Request ID', u.id AS 'User ID', u.name, u.email FROM users u ,friends f WHERE u.id = f.user_one AND f.user_two = ? AND f.accepted = 1 AND NOT u.id = ?";

        $friends1 = DB::select($sql1, [$id, $my_id]);
        $friends2 = DB::select($sql2, [$id, $my_id]);

        if(empty($friends1) && empty($friends2)){
            return response()->json(["message"=>"Records not found!"], 404);
        }

        $friends = array();
        foreach ($friends1 as $friend){
            $friends[] = $friend;
        }
        foreach ($friends2 as $friend){
            $friends[] = $friend;
        }

        return response()->json($friends, 200);
    }



    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * function createFriendRequest use param $id to send friend request to an user with id:$id
     * $my_id is the id of the authenticated user - it is used in the first query to check if there
     * is no existing friend request to the same user, and in the second query to save it in the
     * first column (user_one)
     */
    public function createFriendRequest ($id){
        $my_id = Auth::id();

        //Check if the friend request is not sent to the self
        if($my_id === $id) return response()->json(["message"=>"Request can not be sent to the self!"], 400);

        //Check if the user exists
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(["message"=>"User not found!"], 404);
        }

        //Check if there is already have sent a request to this user
        $requested = Friends::where([['user_one','=', $my_id], ['user_two', '=', $id]])->first('id');

        //If there is no friend request sent to this user, send it (save in table friends) and return message
        If(!is_null($requested)){
            return response()->json(["message"=>"Friend request already sent for this user!"],400);
        }else{

            $friend_request = new Friends();

            $friend_request->user_one = $my_id;
            $friend_request->user_two = $id;
            $friend_request->accepted = 0;
            $friend_request->rejected = 0;

            $result = $friend_request->save();

            if($result){
                return response()->json(["message"=>"Friend request sent!"], 200);
            }else{
                return response()->json(["message"=>"Record not inserted!"], 500);
            }

        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @getFriendRequests() return all the friend requests towards the
     * authenticated user along with the users ID, emails and names,
     * or 404 if no friend requests.
     */
    public function getFriendRequests(){
        $my_id = Auth::id();

        $sql = "SELECT f.*, u.id AS 'User ID', u.name, u.email FROM users u, friends f WHERE 
                f.user_two = ? AND f.user_two = u.id AND f.accepted = 0 AND f.rejected = 0";
        $friend_requests = DB::select($sql, [$my_id]);

        if(empty($friend_requests)){
            return response()->json(["message"=>"Record not found!"], 404);
        }
        return response()->json($friend_requests, 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @acceptFriendRequest() creates an object from the friend request by the provided
     * param $id, checks if the friend request exists and if it is related to the
     * authenticated user, then updates the 'accepted' column from the 'friends' table
     * and returns status 200.
     * If authenticated user is not the the same as in column 'user_two', status 403 is returned.
     */
    public function acceptFriendRequest($id){
        $my_id = Auth::id();
        $friend_request = Friends::find($id);
        if(is_null($friend_request))return response()->json(["message"=>"Record not found!"], 404);

        if($friend_request->user_two === $my_id){
            $friend_request->accepted = 1;
            $result = $friend_request->save();
            if($result){
                return response()->json(["message"=>"Friend request accepted!"], 200);
            }else{
                return response()->json(["message"=>"Record not changed!"], 500);
            }
        }else{
            return response()->json(["message"=>"Forbidden!"], 403);
        }

    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @rejectFriendRequest() creates an object from the friend request by the provided
     * param $id, checks if the friend request exists and if it is related to the
     * authenticated user, then updates the 'rejected' column from the 'friends' table
     * and returns status 200.
     * If authenticated user is not the the same as in column 'user_two', status 403 is returned.
     */
    public function rejectFriendRequest($id){
        $my_id = Auth::id();
        $friend_request = Friends::find($id);
        if(is_null($friend_request))return response()->json(["message"=>"Record not found!"], 404);

        if($friend_request->user_two === $my_id){
            $friend_request->rejected = 1;
            $result = $friend_request->save();
            if($result){
                return response()->json(["message"=>"Friend request rejected!"], 200);
            }else{
                return response()->json(["message"=>"Record not changed!"], 500);
            }
        }else{
            return response()->json(["message"=>"Forbidden!"], 403);
        }
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @cancelFriendRequest() creates an object from the friend request by the provided
     * param $id, checks if the friend request exists and if it is created by the
     * authenticated user, then deletes that record from the 'friends' table
     * and returns status 200.
     * If authenticated user is not the the same as in column 'user_one', status 403 is returned.
     */
    public function cancelFriendRequest($id){
        $my_id = Auth::id();
        $friend_request = Friends::find($id);
        if(is_null($friend_request))return response()->json(["message"=>"Record not found!"], 404);

        if($friend_request->user_one === $my_id){
            $result = $friend_request->delete();
            if($result){
                return response()->json(["message"=>"Friend request canceled!"], 200);
            }else{
                return response()->json(["message"=>"Record not changed!"], 500);
            }
        }else{
            return response()->json(["message"=>"Forbidden!"], 403);
        }
    }

}
