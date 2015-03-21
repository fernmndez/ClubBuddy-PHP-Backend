<?php
/*  Index - Entry point for ClubBuddy API
 *
 *  Manages the entire API, and is the entry point for all API calls
 * 
 *  Operations Control Room App
 *  HackFSU @ FSU - 03/20 - 03/23
 */

include('includes/core.php');


$core = new Core();
$ClubBuddy = $core->getClubBuddy();

$apiMethod = $_SERVER['REQUEST_URI'];
$apiMethod = explode('/', $apiMethod);

switch($apiMethod[1]){
    case "check":
            $ClubBuddy->checkIfRegistered($apiMethod[2]);
        break;

    case "register":
            $ClubBuddy->registerNewUser($apiMethod[2], $apiMethod[3]);
        break;

    case "location":
        switch($apiMethod[2]){
            case "add":
                $ClubBuddy->addLocation($apiMethod[3], $apiMethod[4]);
                break;
            case "get":
                $ClubBuddy->getLocation($apiMethod[3], $apiMethod[4]);
                break;
            default:
                do404();
                break;
        }
        break;

    case "friend":
        switch($apiMethod[2]){
            case "add":
                $ClubBuddy->addFriend($apiMethod[3], $apiMethod[4]);
                break;
            case "remove":
                $ClubBuddy->removeFriend($apiMethod[3], $apiMethod[4]);
                break;
            default:
                do404();
                break;
        }
        break;

    case "session":
        switch($apiMethod[2]){
            case "add":
                $ClubBuddy->addFriendToSession($apiMethod[3], $apiMethod[4]);
                break;
            case "create":
                $ClubBuddy->createSession($apiMethod[3]);
                break;
            case "close":
                $ClubBuddy->closeSession($apiMethod[3]);
                break;
            case "remove":
                $ClubBuddy->removeFriendFromSession($apiMethod[3], $apiMethod[4]);
                break;
            default:
                do404();
                break;
        }
        break;

    case "verify":
        $ClubBuddy->verifyLoginToken($apiMethod[2]);
        break;

    default:
        do404();

}

function do404(){

    http_response_code(404);
    echo "404 Page Not Found";

}