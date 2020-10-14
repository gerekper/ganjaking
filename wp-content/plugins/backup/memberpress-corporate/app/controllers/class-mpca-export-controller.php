<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Export_Controller {
  public function __construct() {
    add_action('wp_ajax_mpca_export_csv', array($this, 'ajax_export_csv'));
  }

  public function ajax_export_csv() {
    if(!isset($_REQUEST['ca'])) {
      _e('No corporate account specified', 'memberpress-corporate');
      status_header(404);
      exit;
    }

    $ca = new MPCA_Corporate_Account( esc_attr($_REQUEST['ca']) );

    if(empty($ca->id)) {
      _e('Unable to export due to Invalid corporate account', 'memberpress-corporate');
      status_header(500);
      exit;
    }

    if(!$ca->current_user_has_access()) {
      _e('Forbidden', 'memberpress-corporate');
      status_header(403);
      exit;
    }

    $filename = $ca->sub_id() . '_sub_accounts_' . uniqid() . '.csv';

    header( 'Content-Type: text/csv' );
    header( "Content-Disposition: attachment; filename=\"{$filename}\"" );

    $header = array(
      'email', 'username', 'first_name', 'last_name'
    );

    $user_objs = $ca->sub_users();

    $users = array();
    foreach($user_objs as $user_obj) {
      $users[] = array(
        $user_obj->user_email,
        $user_obj->user_login,
        $user_obj->first_name,
        $user_obj->last_name,
      );
    }

    $out = fopen('php://output', 'w');
    fputcsv($out, $header);

    foreach($users as $user) {
      fputcsv($out, $user);
    }

    fclose($out);
    exit;
  }

}

