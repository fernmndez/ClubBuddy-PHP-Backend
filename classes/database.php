<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 7:00 AM
 */

class Database {
    private $core;
    private $db;

    function __construct($core, $db){
        $this->core = $core;
        $this->db = $this->connectDB($db);
        $this->core->writeDebug("Database Controller initialized");
    }

    private function connectDB($db){

        $dbName = $db['name'];
        $dbUser = $db['username'];
        $dbPass = $db['password'];
        $dbHost = $db['host'];
        $dbPort = $db['port'];

        $this->core->writeDebug("Connecting to database $dbName with username $dbUser");
        try{
            return new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass);
        } catch(Exception $e) {
            die("Unable to establish database link!");
        }

    }

    function CreateNewUser($userID, $pin){
        $token = md5($userID + $pin);
        $dbQuery = $this->db->prepare("INSERT INTO
        `user` (`id`, `pin`, `token`, `homeLat`, `homeLong`, `friends`, `currentsession`, `lastsession`, `lastlogin`, `ip`)
        VALUES (:id, :pin, :token, '0.000', '0.000', :friends, '', '', CURRENT_TIMESTAMP, :ip);");

        $dbQuery->bindParam(':id', $userID, PDO::PARAM_STR);
        $dbQuery->bindParam(':pin', $pin, PDO::PARAM_INT);
        $dbQuery->bindParam(':token', $token, PDO::PARAM_STR);
        $dbQuery->bindValue(':friends', '[]');
        $dbQuery->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $dbQuery->execute();


        return $this->GetUserFromToken($token)[0];
    }

    function AddLocation($token, $location){
        $dbQuery = $this->db->prepare("UPDATE `user` SET `homeLat`=:lat, `homeLong`=:long WHERE `token`=:token");
        $dbQuery->bindParam(':lat', $location->getLat(), PDO::PARAM_STR);
        $dbQuery->bindParam(':long', $location->getLong(), PDO::PARAM_STR);
        $dbQuery->bindParam(':token', $token, PDO::PARAM_STR);
        return $dbQuery->execute();
    }

    function GetLocation($token){
        $dbQuery = $this->db->prepare("SELECT * FROM `user` WHERE `token` = :token");
        $dbQuery->bindParam(':token', $token, PDO::PARAM_STR);
        $dbQuery->execute();
        $tempLocation = $dbQuery->fetchAll(PDO::FETCH_CLASS, "User");
        return $tempLocation->getHomeLocation();
    }


    function AddFriend($token, $friendID){
        $tempUser = $this->GetUserFromToken($token);
        $tempFriends = new Friends($tempUser->getFriends());
        $tempFriends->addFriend($friendID);
        return $this->SaveFriends($token, $tempFriends);
    }

    function RemoveFriend($token, $friendID){
        $tempUser = $this->GetUserFromToken($token);
        $tempFriends = new Friends($tempUser->getFriends());
        $removed = $tempFriends->removeFriend($friendID);
        $this->SaveFriends($token, $tempFriends);
        return $removed;
    }

    function SaveFriends($token, $friendsList){
        $friendsList = JsonHandler::parse($friendsList);
        $dbQuery = $this->db->prepare("UPDATE `user` SET `friends`=:friends WHERE `token`=:token");
        $dbQuery->bindParam(':token', $token, PDO::PARAM_STR);
        $dbQuery->bindParam(':friends', $friendsList, PDO::PARAM_STR);
        return $dbQuery->execute();

    }
    function AddSessionFriend($session, $friendID){
        //$tempSession = $this->GetSessionFromSessionID($this->GetUserFromToken($token)->getCurrentSession());
        $tempFriends = new Friends($session->getFriends());
        $tempFriends->addFriend($friendID);
        return $this->SaveSessionFriends($session->getID(), $tempFriends);

    }
    function RemoveSessionFriend($session, $friendID){
        //$tempSession = $this->GetUserFromToken($token)->getCurrentSession();
        $tempFriends = new Friends($session->getFriends());
        $tempFriends->removeFriend($friendID);
        return $this->SaveSessionFriends($session->getID(), $tempFriends);

    }
    function SaveSessionFriends($sessionToken, $friendsList){
        $friendsList = JsonHandler::parse($friendsList);
        $dbQuery = $this->db->prepare("UPDATE `session` SET `friends`=:friends WHERE `id`=:token");
        $dbQuery->bindParam(':token', $sessionToken, PDO::PARAM_STR);
        $dbQuery->bindParam(':friends', $friendsList, PDO::PARAM_STR);
        $db = $dbQuery->execute();
        return $db;

    }

    function CreateNewSessionWithUserId($userID){
        $user = $this->GetUserFromID($userID);
        $this->CloseSessionWithSessionID($user->getCurrentSession);

        $sessionID = md5($userID + time());
        $dbQuery = $this->db->prepare("INSERT INTO `club_buddy`.`session`
                (`id`, `active`, `host`, `friends`, `sessiontime`)
        VALUES (:id, :active, :host, :friends, CURRENT_TIMESTAMP)");

        $dbQuery->bindParam(':id', $sessionID, PDO::PARAM_STR);
        $dbQuery->bindValue(':active', 1, PDO::PARAM_INT);
        $dbQuery->bindParam(':host', $userID, PDO::PARAM_INT);
        $dbQuery->bindValue(':friends', '[]');
        $dbQuery->execute();
        $this->UpdateActiveSession($userID, $sessionID);
        return $sessionID;

    }
    function CloseSessionWithSessionID($sessionID){
        $dbQuery = $this->db->prepare("UPDATE `club_buddy`.`session` SET `active`='0' WHERE `id`=:sid");
        $dbQuery->bindParam(':sid', $sessionID, PDO::PARAM_STR);
        $dbQuery->execute();

    }
    function GetSessionFromSessionID($sessionID){
        $dbQuery = $this->db->prepare("SELECT * FROM `session` WHERE `id` = :id");
        $dbQuery->bindParam(':id', $sessionID, PDO::PARAM_STR);
        $dbQuery->execute();
        $tempSession = $dbQuery->fetchAll(PDO::FETCH_CLASS, "Session");
        return $tempSession[0];
    }

    function UpdateActiveSession($userID, $sessionID){
        $lastSession = $this->GetUserFromID($userID)->getCurrentSession();
        $dbQuery = $this->db->prepare("UPDATE `club_buddy`.`user` SET `lastsession`=:sid WHERE `id`=:uid");
        $dbQuery->bindParam(':uid', $userID, PDO::PARAM_STR);
        $dbQuery->bindParam(':sid', $lastSession, PDO::PARAM_STR);
        $dbQuery->execute();

        $dbQuery = $this->db->prepare("UPDATE `club_buddy`.`user` SET `currentsession`=:sid WHERE `id`=:uid");
        $dbQuery->bindParam(':uid', $userID, PDO::PARAM_STR);
        $dbQuery->bindParam(':sid', $sessionID, PDO::PARAM_STR);
        $dbQuery->execute();

    }

    function GetUserFromID($id){
        $dbQuery = $this->db->prepare("SELECT * FROM `user` WHERE `id` = :id");
        $dbQuery->bindParam(':id', $id, PDO::PARAM_INT);
        $dbQuery->execute();
        $tempUser = $dbQuery->fetchAll(PDO::FETCH_CLASS, "User");
        return $tempUser[0];
    }

    function GetUserFromToken($token){
        $dbQuery = $this->db->prepare("SELECT * FROM `user` WHERE `token` = :token");
        $dbQuery->bindParam(':token', $token, PDO::PARAM_STR);
        $dbQuery->execute();
        $tempUser = $dbQuery->fetchAll(PDO::FETCH_CLASS, "User");
        return $tempUser[0];
    }


}