<?php


add_action('plugins_loaded', function() {
  load_plugin_textdomain('wp-media-folders', false, dirname(plugin_basename(__FILE__)) . '/languages');
});


add_action('wp_enqueue_media', function() {
  feml_enqueue_assets();
});


function feml_enqueue_assets() {
  wp_enqueue_script('FEML/main', feml_script_url(), feml_script_deps(), FEML_VERSION, false);
  wp_localize_script('FEML/main', 'femlSettings', feml_get_settings());
}



function feml_script_deps() {
  global $pagenow;

  $deps = array();

  if ($pagenow === 'upload.php') {
    $deps[] = 'media-grid';
    $deps[] = 'media';
  } else {
    $deps[] = 'media-editor';
  }

  return $deps;
}

function feml_script_url() {
  $url = plugins_url('js/main.js', dirname(__FILE__));

  if (defined('FEML_DEBUG')) {
    ini_set('display_errors', 1);
    $url = FEML_SCRIPT_URL;
  }

  return $url;
}


function feml_get_settings() {
  global $pagenow;

  $settings = array(
    'ajaxURL' => admin_url('admin-ajax.php'),
    'isAdmin' => feml_services_users_isAdmin(),
    'isUploadPage' => $pagenow === 'upload.php',
  );

  return $settings;
}


function feml_error_perms() {
  wp_send_json_error(array('message' => __('Sorry, you are not allowed to access this page.')));
}

function feml_error_wp($wpErr) {
  wp_send_json_error(array('message' => $wpErr->get_error_message()));
}

function feml_error($message) {
  wp_send_json_error(array('message' => $message));
}

function feml_pattern(&$arr, $type) {
  $patterns = array_map('feml_db_' . substr($type, 1), $arr);
  return implode(', ', $patterns);
}

function feml_db_s($value) {
  return '%s';
}

function feml_db_d($value) {
  return '%d';
}
