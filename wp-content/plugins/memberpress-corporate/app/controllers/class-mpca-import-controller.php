<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Import_Controller {
  public function __construct() {
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

    // Handle the ajax submission
    add_action('wp_ajax_mpca_find_corporate_accounts', array($this, 'ajax_find_corporate_accounts'));
  }

  public function enqueue_scripts() {
    wp_enqueue_script('mphelpers', MEPR_URL . '/js/mphelpers.js', array('suggest'));
    wp_enqueue_script('mpca-import', MPCA_URL . '/public/js/mpca-import.js');
  }

  public function ajax_find_corporate_accounts() {
    $user_login = sanitize_text_field( $_REQUEST['username'] );

    if ( ( $user_id = username_exists( $user_login ) ) ) {
      $response = array('corporate_accounts' => array());
      $user = new MeprUser($user_id);

      $corporate_accounts = MPCA_Corporate_Account::get_all_by_user_id($user_id);

      foreach( $corporate_accounts as $corporate_account_rec) {
        $corporate_account = new MPCA_Corporate_Account();
        $corporate_account->load_from_array($corporate_account_rec);

        $sub = $corporate_account->get_obj();
        $product = $sub->product();

        $status = (($sub->is_active()) ? __('Active', 'memberpress-corporate') : __('Inactive', 'memberpress-corporate'));
        $option = array(
          'text' => "{$product->post_title} (ID: {$sub->subscr_id}, Status: {$status})",
          'value' => "{$corporate_account->id}"
        );

        $response['corporate_accounts'][] = $option;
      }

      header( "Content-Type: application/json" );
      echo json_encode($response);
    }
    else {
      status_header(404);
    }

    exit;
  }

}
