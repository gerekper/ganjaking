<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class WafpNonceModel {
  public static $db_prefix_str = 'wafp_nonce_';
  public static $cookie_name = 'wafp_nonce_cookie';

  public static function setup_nonce() {
    global $post, $wafp_options;

    if(!isset($post) || !isset($post->ID) || $post->ID != $wafp_options->signup_page_id)
      return;

    //Setup a nonce in the user's cookie which is good for 12 hours
    //We won't set this below in process_signup_form since the cookie should be good for 12 hours
    $nonce_data = self::generate();
    self::set_cookie($nonce_data);
    self::cleanup_nonces(); //Delete expired nonce's now
  }

  public static function generate() {
    $rand = md5((uniqid() . base64_encode(uniqid() . substr(str_shuffle(md5(microtime())), 0, 10)))); //BOOM
    $nonce = self::$db_prefix_str . strtolower($rand);
    $ts = time();

    update_option($nonce, $ts);

    return array('ts' => $ts, 'nonce' => $nonce);
  }

  public static function is_valid($nonce, $ts) {
    $db_ts = get_option($nonce, false);

    if($db_ts !== false && $ts == $db_ts)
      return true;

    return false;
  }

  public static function set_cookie($nonce_data) {
    if(!isset($_COOKIE[self::$cookie_name]) && !empty($nonce_data)) {
      setcookie(self::$cookie_name, base64_encode(serialize($nonce_data)), time() + 43200, '/'); //Expires in 12hrs
      return true;
    }

    return false;
  }

  public static function get_cookie_data() {
    if(isset($_COOKIE[self::$cookie_name]))
      return unserialize(base64_decode(urldecode($_COOKIE[self::$cookie_name])));

    return false;
  }

  public static function cleanup_nonces() {
    global $wpdb;

    $time = time() - 43200; //Delete nonce's older than 12hrs

    return $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value <= %d", self::$db_prefix_str."%", $time));
  }
} //End class
