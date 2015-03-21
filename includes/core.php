<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 4:01 AM
 */


include_once('includes/config.php');
include_once('includes/clubbuddy.php');

include_once('classes/IJsonSerializable.php');

include_once('classes/database.php');
include_once('classes/friends.php');
include_once('classes/session.php');
include_once('classes/homelocation.php');
include_once('classes/jsonhandler.php');
include_once('classes/user.php');

class Core {
    private $config;
    private $clubBuddy;
    private $db;

    function __construct(){
        $this->config       = new Config($this);
        $this->db           = new Database($this, $this->config->getDBConfig());
        $this->clubBuddy    = new ClubBuddy($this);
    }


    function getClubBuddy(){
        return $this->clubBuddy;
    }

    function getDB(){
        return $this->db;
    }

    public function writeDebug($str){

        if(Config::isDebugging())
            echo "[DEBUG] " . $str . "<br />";

    }


}