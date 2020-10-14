<?php
namespace memberpress\courses\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;

class Options extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('mpcs_admin_general_options', array($this, 'general'));
    add_action('wp_ajax_logo_uploader', array($this, 'dnd_logo_uploader'));

  }

  public function route() {
    if(lib\Utils::is_post_request() && isset($_POST['mpcs-options'])) {
      \update_option('mpcs-options',$_POST['mpcs-options']);
    }

    $options = \get_option('mpcs-options');

    require_once(base\VIEWS_PATH . '/admin/options/form.php');
  }

  public function general($options) {
    require_once(base\VIEWS_PATH . '/admin/options/general.php');
  }

// handle uploaded file here
function dnd_logo_uploader (){

  check_ajax_referer('photo-upload');

  // you can use WP's wp_handle_upload() function:
  $file = $_FILES['async-upload'];
  $status = wp_handle_upload($file, array('test_form'=>true, 'action' => 'logo_uploader'));
  $id = wp_insert_attachment( array(
    'post_mime_type' => $status['type'],
    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file['name'])),
    'post_content' => '',
    'post_status' => 'inherit'
  ), $status['file']);

  // and output the results or something...
  $response = array(
    'url' => $status['url'],
    'id' => $id,
  );

  echo json_encode($response);
  exit;
}



}
