<?php
/*
Plugin Name: AG-ALR WP Mail Debug
Plugin URI: http://
Description: WP Mail Debug mode 
Author: Andrea Grillo - Antonio La Rocca
Version: 1.4
*/

define( 'UNIT_TEST_GIFT_CARD_EMAIL_FOLDER', ABSPATH . '/email_sent/unit_test_gift_cards/' );

/* === YITH UPDATE === */
if ( ! function_exists( 'wp_mail' ) ) :
	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ){

		$to = is_array( $to ) ? implode( ',', $to ) : $to;

		$counter = 0;
		$dir = UNIT_TEST_GIFT_CARD_EMAIL_FOLDER;
		$time = time();
		$filename = "[$time] " . sanitize_title( $subject ) . ' - ' . $to . ".html";
		$filename = apply_filters( 'ag_alr_email_file_name', $filename, $time, $subject, $to );
		$filepath = $dir . $filename;

		while( file_exists( $filepath ) ){
			$to_replace = ! $counter ? '.html' : '-' . $counter . '.html';

			$counter ++;

			$filepath = str_replace( $to_replace, '-' . $counter . '.html', $filepath );
		}

		$check = file_put_contents( $filepath, $message );
		return $check > 0 ? true : false;
	}
endif;	

if( ! file_exists( ABSPATH . '/email_sent/unit_test_gift_cards/') ){
	mkdir( UNIT_TEST_GIFT_CARD_EMAIL_FOLDER, 0777, true );
}

// Removing all the old emails from last unit tests
$files = array_diff( scandir( UNIT_TEST_GIFT_CARD_EMAIL_FOLDER ), array( '.', '..' ) );

foreach ( $files as $file )
    unlink( UNIT_TEST_GIFT_CARD_EMAIL_FOLDER . $file );

/* === YITH UPDATE END === */