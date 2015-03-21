<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 10:13 AM
 */

class Friends implements IJsonSerializable {

    private $friends;

    public function __construct($list){
        $this->populateFriendsList($list);
    }

    public function addFriend($userID){
        array_push($this->friends, $userID);
    }

    private function populateFriendsList($list){
        $this->friends = json_decode($list, true);
    }

    public function removeFriend($user){
        $tempFriendList = array();
        $removed = false;
        foreach($this->friends as $friend){
            if($friend != $user){
                $removed = true;
                array_push($tempFriendList, $friend);
            }
        }
        $this->friends = $tempFriendList;
        return $removed;
    }

    public function toArray(){
        return $this->friends;
    }


}