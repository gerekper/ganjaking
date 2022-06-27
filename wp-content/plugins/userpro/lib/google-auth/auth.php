<?php
// Load WordPress functions
if (!defined('ABSPATH')) {
    $path = preg_replace('/wp-content.*$/','',__DIR__);
    require_once $path . '/wp-load.php';
}
if (empty($_GET['error']) && !empty($_GET['code'])) {
    require_once(userpro_path . 'functions/socials/GoogleAuth.php');
// Create Google Auth object and send auth code to get user information.
    $googleAuth = new GoogleAuth();
    $googleAuth->login($_GET['code']);
}



