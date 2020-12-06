<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Signup_Controller {
  public function __construct() {
    // Check for errors after the checkout form has been submitted
    add_filter( 'mepr-validate-signup', array($this, 'validate_ca_signup') );

    // Associate sub account if processing corporate account signup
    add_filter( 'mepr-signup-checkout-url', array($this, 'associate_sub_account'), 10, 2 );

    // Handle error view if CA is invalid
    add_filter( 'mepr_view_get_string_/checkout/form', array($this, 'display_error'), 1, 2 );
  }

  public function validate_ca_signup($errors) {
    extract($_POST);

    if(isset($mpca_corporate_account_id)) {
      $ca = MPCA_Corporate_Account::find_by_uuid($mpca_corporate_account_id);

      if(empty($ca->id)) {
        array_push($errors, 'Invalid corporate account (1)');
      } else {
        // Check if the sub-account limit will be exceeded
        $error = $ca->validate();

        if(is_wp_error($error)) {
          array_push($errors, __($error->get_error_message(), 'memberpress-corporate'));
        }

        // Block parent Corporate account user from being able to add themselves as a sub account.
        $is_existing_user = MeprUtils::is_user_logged_in();
        if($is_existing_user) {
          $usr = MeprUtils::get_currentuserinfo();

          if( $usr->ID == $ca->user_id ){
            array_push($errors, __('Cannot Add Yourself as Sub Account', 'memberpress-corporate'));
          }
        }

      }
    }

    return $errors;
  }

  public function associate_sub_account($url, $txn) {
    $mepr_options = MeprOptions::fetch();
    $sa_id = $txn->user()->ID;

    if(isset($_REQUEST['ca'])) {
      $ca = MPCA_Corporate_Account::find_by_uuid($_REQUEST['ca']);

      if(empty($ca->id)) {
        return _e('Invalid corporate account (2)', 'memberpress-corporate');
      }

      $ca->add_sub_account_user($sa_id);

      // Signup email handling
      $mailer = MeprEmailFactory::fetch('Mepr_Sub_Account_Signup_Email');
      if($mailer->enabled()) {
        $mailer->send_sub_account_signup_email($txn);
      }
      else {
        MeprUtils::send_signup_notices($txn, false, false);
      }

      $product = new MeprProduct($txn->product_id);
      $sanitized_title = sanitize_title($product->post_title);
      $query_params = array('membership' => $sanitized_title, 'trans_num' => $txn->trans_num, 'membership_id' => $product->ID);
      // Skip the payment options; set url to be the thank you page instead
      $url = $mepr_options->thankyou_page_url(build_query($query_params));

      // Sub accounts don't need the txn so we delete it here
      $txn->destroy();
    }

    return $url;
  }

  public function display_error($view, $vars) {
    if(isset($_REQUEST['ca'])) {
      $ca = MPCA_Corporate_Account::find_by_uuid($_REQUEST['ca']);

      if(empty($ca->id)) {
        $errors = array(__('Invalid corporate account (3)', 'memberpress-corporate'));
        $view = MeprView::get_string('/shared/errors',compact('errors'));
      }
    }

    return $view;
  }
}
