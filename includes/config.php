<?php
/**
 * Created by PhpStorm.
 * User: Fernando Mendez
 * Date: 3/21/2015
 * Time: 4:02 AM
 */


class Config {
    private $core;
    private $cfg;

    function __construct($core){
        include "conf.php";

        $this->cfg = $config;
        $this->core = $core;
        $this->core->writeDebug('Initializing <b>configuration</b>', $this->file);

    }

    public function getDBConfig(){
        if (!empty($this->cfg)) {
            return $this->cfg['database'];
        } else {
            throw new ErrorException("Config not found");
        }
    }

    public function getTimeZone(){
        $this->core->writeDebug("Getting timezone");
        return $this->cfg['timezone'];

    }

    public static function isDebugging(){
        include "conf.php";
        return $config['debug'];
    }
}