<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 4:25 AM
 */

class User implements IJsonSerializable {

    private $id;
    private $pin;
    private $token;
    private $homeLat;
    private $homeLong;
    private $friends;
    private $currentsession;
    private $lastSession;
    private $lastLogin;
    private $ip;

    private $homeLocation;

    public function __construct(){
        $this->homeLocation = new HomeLocation($this->homeLat, $this->homeLong);
    }

    function getID(){
        return $this->id;
    }

    function getCurrentSession(){
        return $this->currentsession;
    }

    function getHomeLocation(){
        return $this->homeLocation;
    }

    function getFriends(){
        return $this->friends;
    }

    function getIP(){
        return $this->ip;
    }

    /**
     * @return mixed all the variables from the object
     */
    function toArray()
    {
        $userArray = array(
            "id" => $this->getID(),
            "friends" => $this->getFriends(),
            "currentsession" => $this->currentsession,
            "home" => JsonHandler::parse($this->homeLocation),

        );
        return $userArray;
    }
}