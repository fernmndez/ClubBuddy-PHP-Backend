<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 5:06 AM
 */

class Session implements IJsonSerializable {

    private $id;
    private $active;
    private $host;
    private $friends;
    private $sessiontime;

    function getID(){
        return $this->id;
    }

    function isActive(){
        return $this->active;
    }

    function getFriends(){
        return $this->friends;
    }

    function getHost(){
        return $this->host;
    }

    function isInSession($user){

    }

    function getSessionTime(){
        return $this->sessiontime;
    }

    /**
     * @return mixed all the variables from the object
     */
    function toArray()
    {
        $sessionArray = array(
            "id" => $this->getID(),
            "active" => $this->isActive(),
            "friends" => $this->getFriends(),
            "host" => $this->getHost(),
            "sessiontime" => $this->getSessionTime(),

        );
        return $sessionArray;
    }
}