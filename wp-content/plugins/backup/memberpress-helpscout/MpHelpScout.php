<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpHelpScout {

  public function __construct() {
    add_action('mepr_display_general_options',array($this,'display_options'));
    add_action('mepr-process-options', array($this,'store_options'));
    add_action('wp_ajax_nopriv_mepr_helpscout_custom_app', array($this,'ajax_helpscout_custom_app'));
  }

  public function ajax_helpscout_custom_app() {
    $signature = $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'];

    $json = file_get_contents('php://input');
    if ($this->validate($json, $signature)) {
      $data = json_decode($json);

      ob_start();
      require(MPHELPSCOUT_VIEW_PATH.'/sidebar.php');
      $html = ob_get_clean();

      echo json_encode(compact('html'));
    }

    exit;
  }

  public function display_options() {
    $helpscout_enabled = get_option('mepr_helpscout_custom_app_enabled');
    $helpscout_secret  = get_option('mepr_helpscout_custom_app_secret');
    require(MPHELPSCOUT_VIEW_PATH.'/options.php');
  }

  public function store_options() {
    $helpscout_enabled = isset($_POST['mepr_helpscout_custom_app_enabled']);
    $helpscout_secret  = $_POST['mepr_helpscout_custom_app_secret'];
    update_option('mepr_helpscout_custom_app_enabled', $helpscout_enabled);
    update_option('mepr_helpscout_custom_app_secret', $helpscout_secret);
  }

  private function validate($data, $signature) {
    $helpscout_secret  = get_option('mepr_helpscout_custom_app_secret');
    $calculated = base64_encode(hash_hmac('sha1', $data, $helpscout_secret, true));
    return $signature == $calculated;
  }
}

