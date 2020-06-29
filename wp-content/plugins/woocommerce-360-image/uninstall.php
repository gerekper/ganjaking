<?php

// Stuff to do on the uninstall / deletion of the plugin

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

    exit();

}


// Delete Saved Options (if they exist)

if ( get_option( 'wc360_fullscreen_enable' ) ) {
  delete_option( 'wc360_fullscreen_enable' );
}
if ( get_option( 'wc360_navigation_enable' ) ) {
  delete_option( 'wc360_navigation_enable' );
}


// Delete Saved Meta (if they exist)

delete_post_meta_by_key( 'wc360_enable' );


// Anyo!
