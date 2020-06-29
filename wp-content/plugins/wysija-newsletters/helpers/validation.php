<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_validation extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    /**
     * Validate if a string is an URL
     * @param string $url
     * @return boolean
     * 
     * Refer: http://www.krio.me/how-to-validate-a-url-php/
     */
    function isUrl($url){
        $pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
        return (bool)preg_match($pattern, $url);
    }
}