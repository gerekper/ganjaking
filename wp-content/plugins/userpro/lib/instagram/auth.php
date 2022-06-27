<?php

if ( !defined('ABSPATH') ) {
	$full_path_to_file = __FILE__;
    $path_array = explode('/', $full_path_to_file);
    $path = "";
    foreach($path_array as $path_piece){
        if(strpos($path_piece, 'wp-content') === false){
            $path .= $path_piece . '/';
        }
        else{
            break;
        }
	}
	
	include_once $path . '/wp-load.php';
}
if(isset($_GET['code'])){
	require_once(userpro_path.'functions/socials/InstagramAuth.php');

    // Send auth code to Instagram Object
	$instagram = new InstagramAuth();
	$instagram->login($_GET['code']);
}