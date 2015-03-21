<?php

/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 5:13 AM
 */
class ClubBuddy
{
    private $db;
    private $core;
    private $reponse;

    public function __construct($core)
    {
        $this->core = $core;
        $this->db = $this->core->getDB();
        $this->response = array("message" => "error verifying your token!");
    }

    private function isTokenValid($token)
    {
        $tempUser = $this->db->GetUserFromToken($token);
        if ($tempUser) {
            return true;
        }
        return false;
    }

    function verifyLoginToken($token)
    {
        //TODO: Check token, if valid return a json response with valid : true
        if ($this->isTokenValid($token)) {
            $this->response = array("valid" => true);
        }

        echo JsonHandler::parse($this->response, true);

    }

    function registerNewUser($phone, $pin)
    {
        if($this->checkIfRegistered($phone, true)){
            echo JsonHandler::parse(array("message" => "user already registered"), true);
            return;
        }

        $tempUser = $this->db->CreateNewUser($phone, $pin);
        if ($tempUser != null)
            echo JsonHandler::parse($tempUser);
        else
            echo JsonHandler::parse(array("message" => "error creating new user"), true);
    }

    function checkIfRegistered($phone, $suppressOutput = false)
    {
        $user = $this->db->GetUserFromID($phone);
        if ($user) {
            if(!$suppressOutput)
                echo JsonHandler::parse(array("registered" => "1"), true);
            return true;
        } else {
            if(!$suppressOutput)
                echo JsonHandler::parse(array("registered" => "0"), true);
            return false;
        }

    }

    function addFriend($token, $id)
    {
        //TODO: Check token, add friend from id.
        if ($this->isTokenValid($token)) {
            if ($this->db->AddFriend($token, $id)) {
                $this->db->getUserFromToken($token);
                echo JsonHandler::parse($this->db->getUserFromToken($token), true);
                return;
            } else {
                echo JsonHandler::parse(array("message" => "error adding friend"), true);
                return;
            }
        }
        echo JsonHandler::parse($this->response, true);
    }

    function removeFriend($token, $id)
    {
        if ($this->isTokenValid($token)) {
            if ($this->db->RemoveFriend($token, $id)) {
                echo JsonHandler::parse($this->getUserFromToken($token), true);
                return;
            } else {
                echo JsonHandler::parse(array("message" => "error removing friend"), true);
                return;
            }
        }

        echo JsonHandler::parse($this->response, true);

    }

    function createSession($token)
    {
        if ($this->isTokenValid($token)) {
            $userID = $this->getUserFromToken($token)->getID();
            $sessionID = $this->db->CreateNewSessionWithUserId($userID);
            echo JsonHandler::parse($this->db->GetSessionFromSessionID($sessionID));
            return;
        }
        echo JsonHandler::parse($this->response, true);
    }

    function closeSession($token)
    {
        if ($this->isTokenValid($token)) {
            $sessionID = $this->getUserFromToken($token)->getCurrentSession();
            $this->db->CloseSessionWithSessionID($sessionID);
            echo JsonHandler::parse($this->db->GetSessionFromSessionID($sessionID));
            return;
        }
        echo JsonHandler::parse($this->response, true);

    }

    function leaveSession($token)
    {
        if ($this->isTokenValid($token)) {

        }
    }

    function getUserFromToken($token)
    {
        return $this->db->GetUserFromToken($token);
    }

    function getHomeFromToken($token)
    {
        $user = $this->getUserFromToken($token);
        echo json_encode($user->getHomeLocation()->getArray());
    }

    function getCurrentSessionFromToken($token)
    {
        $user = $this->getUserFromToken($token);
        return $user->getCurrentSession();
    }

    function isFriend($user, $friendID){
        $friendsList = $this->db->GetUserFromID($friendID)->getFriends();
        if(is_array($friendsList)) {
            foreach ($friendsList as $friend) {
                if ($user->getID() == $friend) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    function addFriendToSession($token, $friendId)
    {
        //TODO: Check token, add friend from id to users current session
        if ($this->isTokenValid($token)) {
            if ($this->isFriend($this->getUserFromToken($token), $friendId)) {
                $sessionObject = $this->db->GetSessionFromSessionID($this->getCurrentSessionFromToken($token));
                if ($sessionObject->getHost() == $this->getUserFromToken($token)->getID()) {
                    if ($this->db->AddSessionFriend($sessionObject, $friendId)) {
                        echo JsonHandler::parse($sessionObject, true);
                    } else {
                        echo JsonHandler::parse(array("message" => "error adding friend"), true);
                    }
                }
                echo JsonHandler::parse(array("message" => "not host of session"), true);
            } else {
                echo JsonHandler::parse(array("message" => "person being invited has not added you back"), true);
            }
        }
    }
    function removeFriendFromSession($token, $friendId)
    {
        if ($this->isTokenValid($token)) {
            $sessionObject = $this->db->GetSessionFromSessionID($this->getCurrentSessionFromToken($token));
            if ($sessionObject->getHost() == $this->getUserFromToken($token)->getID()) {
                if ($this->db->RemoveSessionFriend($sessionObject, $friendId)) {
                    echo JsonHandler::parse($sessionObject, true);
                } else {
                    echo JsonHandler::parse(array("message" => "error removing friend"), true);
                }
            } else {
                echo JsonHandler::parse(array("message" => "not host of session"), true);
            }

        }
    }


    function addLocation($token, $location){
        $homeLocation = json_decode($location);
        if($this->isTokenValid($token)){
            $this->db->addLocation($token, $homeLocation);
        } else {
            echo JsonHandler::parse($this->response, true);;
        }

    }

    function getLocation($token){
        if($this->isTokenValid($token)){
            return $this->db->getLocation($token);
        } else {
            echo JsonHandler::parse($this->response, true);
        }

    }


    function arriveHome($token)
    {
        //TODO: Check token, and push alert all in session that current user arrived safely.


    }

    function panicButton($token)
    {
        //TODO: Check token, and push alert all in session that current user activated panic.


    }
}