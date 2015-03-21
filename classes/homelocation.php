<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 4:58 AM
 */

class HomeLocation implements IJsonSerializable{

    private $lat;
    private $long;

    public function __construct($lat, $long){
        $this->lat = $lat;
        $this->long = $long;
    }

    function getLat(){
        return $this->lat;
    }

    function getLong(){
        return $this->long;
    }

    function toArray(){

        $homeArray = array(
            'lat' => $this->lat,
            'long' => $this->long,

        );

        return $homeArray;

    }

}