<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );
require_once "class-fue-api-client.php";

// These are used from the user profile.
$consumer_key = 'ck_5856c55bae4052df925aa9875e790bc2'; // Add your own Consumer Key here
$consumer_secret = 'cs_9d54dd8e26e494abc33a915d337f1c7b'; // Add your own Consumer Secret here
$wp_url = 'http://fue4.dev/'; // Add the home URL to the store you want to connect to here

// Initialize the class
$fue_api = new FUE_API_Client( $consumer_key, $consumer_secret, $wp_url );

// Get all emails
print_r( $fue_api->get_emails() );

// Get a single email by id
print_r( $fue_api->get_email( 2011 ) );
