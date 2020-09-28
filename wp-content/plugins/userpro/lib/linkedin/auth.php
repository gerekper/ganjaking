<?php

// Load WordPress functions
if (!defined('ABSPATH')) {
    //If wordpress isn't loaded load it up.
    $full_path_to_file = __FILE__;
    if (strpos($full_path_to_file, '/') !== false) {
        $path_array = explode('/', $full_path_to_file);
    }
    else{
        $path_array = explode("\\", $full_path_to_file);
    }
    $path = "";
    foreach($path_array as $path_piece){
        if(strpos($path_piece, 'wp-content') === false){
            $path .= $path_piece . '/';
        }
        else{
            break;
        }
    }

    include_once $path . 'wp-load.php';
}

if (!empty($_GET['code'])) {
    require(userpro_path . 'functions/socials/LinkedinAuth.php');

    $linkedin = new LinkedinAuth();

    $linkedin->login($_GET['code']);

}
