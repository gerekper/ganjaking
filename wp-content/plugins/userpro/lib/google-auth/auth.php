<?php
// Load WordPress functions
if (!defined('ABSPATH')) {
    //If wordpress isn't loaded load it up.
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wp-load.php';
}
if (empty($_GET['error']) && !empty($_GET['code'])) {
    require(userpro_path . 'functions/socials/GoogleAuth.php');
// Create Google Auth object and send auth code to get user information.
    $googleAuth = new GoogleAuth();
    $googleAuth->login($_GET['code']);
}



