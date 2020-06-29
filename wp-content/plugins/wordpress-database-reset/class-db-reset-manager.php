<?php

if ( ! class_exists( 'DB_Reset_Manager' ) ) :

  class DB_Reset_Manager {

    private $version;

    public function __construct( $version ) {
      $this->version = $version;
    }

    public function run() {
      if ( is_admin() ) {
        $this->load_admin();
      }
    }

    private function load_admin() {
      $admin = new DB_Reset_Admin( $this->get_version() );
      $admin->run();
    }

    private function get_version() {
      return $this->version;
    }

  }

endif;
