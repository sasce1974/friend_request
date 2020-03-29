<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        //Check if $my_id already exists with this $id (user)...
        $requested = DB::select("SELECT id FROM friends WHERE user_one = ? AND user_two = ?", [$my_id, $id]);
        If(count($requested) > 0){
            return response()->json(["message"=>"Friend request already sent for this user!"], 304);
        }else{
            $sql = DB::insert("INSERT INTO friends (user_one, user_two, accepted, rejected) VALUES (?, ?, 0, 0)",[$my_id, $id]);
            if($sql){
                return response()->json(["message"=>"Friend request sent!"], 200);
            }else{
                return response()->json(["message"=>"Record not inserted!"], 500);
            }

        }
    }


    public function acceptFriendRequest($id){
        $sql = "UPDATE "


    }

}
