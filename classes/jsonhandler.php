<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 6:14 AM
 */
class JsonHandler {

    public static function parse($obj, $alreadyArray = false){
        header("Content-Type: application/json");
        if($obj instanceof IJsonSerializable) {
            return json_encode($obj->toArray());
        }else {
            if ($alreadyArray) {
                return stripslashes(json_encode($obj));
            } else {
                throw new InvalidArgumentException("Object does not implement IJsonSerializable");
            }
        }
    }


}