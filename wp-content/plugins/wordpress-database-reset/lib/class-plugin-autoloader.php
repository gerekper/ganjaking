<?php

if ( ! class_exists( 'Plugin_Autoloader' ) ) :

  class Plugin_Autoloader {

    public $directory = __DIR__;

    public function __construct( $directory = '' ) {
      $this->set_directory( $directory );
      spl_autoload_register( array( $this, 'autoload' ) );
    }

    public function set_directory( $directory ) {
      if ( ! empty( $directory ) ) {
        $this->directory = $directory;
      }
    }

    public function autoload( $class ) {
      $file = $this->directory . '/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
      $this->get_class( $file );
    }

    private function get_class( $file ) {
      if ( file_exists( $file ) ) {
        require_once( $file );
      }
    }

  }

endif;
