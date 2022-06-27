<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_Social{

    public function __construct()
    {
        UP_SocialAjax::instance();

        $this->includes();

    }


    public function includes(){

        require_once userpro_path . "includes/addons/social/up-social-template-functions.php";

    }

}