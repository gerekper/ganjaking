<?php

/*
Plugin Name: Unlimited Elements for Elementor (Premium)
Plugin URI: http://unlimited-elements.com
Description: Unlimited Elements Pro - Huge Widgets Pack for Elementor Page Builder, with html/css/js widget creator and editor
Author: Unlimited Elements
Version: 1.5.92
Update URI: https://api.freemius.com
Author URI: http://unlimited-elements.com
Text Domain: unlimited-elements-for-elementor
Domain Path: /languages

* Tested up to: 6.4
* Elementor tested up to: 3.18.3
* Elementor Pro tested up to: 3.18.2
*/
class uepFsNull {
public function is_paying() {
return true;
}
public function can_use_premium_code() {
return true;
}
public function can_use_premium_code__premium_only() {
return true;
}
}

if ( !defined( "UNLIMITED_ELEMENTS_INC" ) ) {
    define( "UNLIMITED_ELEMENTS_INC", true );
}

if ( !function_exists( 'unl_fs' ) ) {
    // Create a helper function for easy SDK access.
    function unl_fs()
    {
        global  $unl_fs ;
        
        if ( !isset( $unl_fs ) ) {
           $unl_fs = new uepFsNull();
        }
        
        return $unl_fs;
    }
    
    // Init Freemius.
    unl_fs();
    // Signal that SDK was initiated.
    do_action( 'unl_fs_loaded' );
}

$mainFilepath = __FILE__;
$currentFolder = dirname( $mainFilepath );
$pathProvider = $currentFolder . "/provider/";
try {
    
    if ( class_exists( "GlobalsUC" ) ) {
        define( "UC_BOTH_VERSIONS_ACTIVE", true );
    } else {
        $pathAltLoader = $pathProvider . "provider_alt_loader.php";
        
        if ( file_exists( $pathAltLoader ) ) {
            require $pathAltLoader;
        } else {
            require_once $currentFolder . '/includes.php';
            require_once GlobalsUC::$pathProvider . "core/provider_main_file.php";
        }
    
    }

} catch ( Exception $e ) {
    $message = $e->getMessage();
    $trace = $e->getTraceAsString();
    echo  "<br>" ;
    echo  esc_html( $message ) ;
    echo  "<pre>" ;
    print_r( $trace );
}