<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Account_Controller {
  public function __construct() {
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('mepr_account_nav_content', array($this,'manage_sub_accounts'));
    add_action('mepr-account-subscriptions-actions', array($this,'maybe_add_sub_account_management_link'), 10, 4);
    add_action('wp_ajax_mpca_remove_sub_account', array($this,'ajax_remove_sub_account'));

    // Add hidden field to checkout form if "ca" is present in the URL
    add_action( 'mepr-checkout-before-submit', array($this, 'add_checkout_fields') );
  }

  public function enqueue_scripts() {
    global $post;
    if(MeprUser::is_account_page($post)) {
      // CSS
      wp_enqueue_style('mpca-fontello-mp-corporate', MPCA_FONTS_URL.'/fontello/css/mp-corporate.css', array(), MPCA_VERSION);
      wp_register_style('mpca-clipboardtip', MPCA_CSS_URL . '/tooltipster.bundle.min.css', array(), MPCA_VERSION);
      wp_enqueue_style('mpca-clipboardtip-borderless', MPCA_CSS_URL . '/tooltipster-sideTip-borderless.min.css', array('mpca-clipboardtip'), MPCA_VERSION);
      wp_enqueue_style('mpca-manage-account', MPCA_CSS_URL . '/mpca-manage-account.css', array(), MPCA_VERSION);

      // JS
      wp_register_script('mpca-clipboard-js', MPCA_JS_URL . '/clipboard.min.js', array(), MPCA_VERSION);
      wp_register_script('mpca-tooltipster', MPCA_JS_URL . '/tooltipster.bundle.min.js', array('jquery'), MPCA_VERSION);
      wp_register_script('mpca-copy-to-clipboard', MPCA_JS_URL . '/copy_to_clipboard.js', array('mpca-clipboard-js','mpca-tooltipster'), MPCA_VERSION);

      wp_enqueue_script('mpca-manage-account', MPCA_JS_URL . '/mpca-manage-account.js', array('jquery','suggest','mpca-copy-to-clipboard'));

      // AJAX Localization
      $local_js = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'confirmMsg' => __('Are you sure you want to remove this sub-account?', 'memberpress-corporate')
      );

      wp_localize_script('mpca-manage-account', 'mpcaAjax', $local_js);
    }
  }

  public function ajax_remove_sub_account() {
    if(!isset($_REQUEST['ca'])) {
      return _e('No corporate account specified', 'memberpress-corporate');
    }

    if(!isset($_REQUEST['sa'])) {
      return _e('No sub account specified', 'memberpress-corporate');
    }

    $ca = new MPCA_Corporate_Account( esc_attr($_REQUEST['ca']) );

    if(empty($ca->id)) {
      return _e('Invalid corporate account', 'memberpress-corporate');
    }

    if(!$ca->current_user_has_access()) {
      return _e('Forbidden', 'memberpress-corporate');
    }

    $ca->remove_sub_account_user(esc_attr($_REQUEST['sa']));

    exit;
  }

  public function display_solitary_error($error) {
    $message='';
    $errors = array($error);
    MeprView::render('/shared/errors',compact('errors','message'));
    return false;
  }

  // Check that we have a valid corporate account
  public function validate_manage_sub_accounts_page() {
    if( !isset($_REQUEST['ca']) || empty($_REQUEST['ca']) ) {
      return $this->display_solitary_error(__('No corporate account specified', 'memberpress-corporate'));
    }

    $ca = MPCA_Corporate_Account::find_by_uuid($_REQUEST['ca']);

    if(empty($ca->id)) {
      return $this->display_solitary_error(__('This is an invalid corporate account', 'memberpress-corporate'));
    }

    if(!$ca->current_user_has_access()) {
      return $this->display_solitary_error(__('Forbidden', 'memberpress-corporate'));
    }

    if($ca->is_disabled()) {
      return $this->display_solitary_error(__('Cannot manage sub-accounts. Account disabled.', 'memberpress-corporate'));
    }

    return $ca;
  }

  public function manage_sub_accounts($action) {
    if($action == 'manage_sub_accounts') {

      if(false === ($ca = $this->validate_manage_sub_accounts_page()) ) { return; }

      static $ALREADY_RUN;

      $errors = array();
      $message = '';

      if( !isset($ALREADY_RUN) && MeprUtils::is_post_request() ) {
        $ALREADY_RUN = true;

        if($_REQUEST['manage_sub_accounts_form'] == 'import') {
          $r = $this->import_sub_account_users($ca);
        }
        else if($_REQUEST['manage_sub_accounts_form'] == 'add') {
          $r = $this->add_sub_account_user($ca);
        }

        extract($r); // $errors and $message
      }

      global $post;

      $mepr_options = MeprOptions::fetch();
      $mepr_current_user = MeprUtils::get_currentuserinfo();
      $ca_owner = $ca->user();
      $full_name = $ca_owner->full_name();
      $owner_name = empty($full_name) ? $ca_owner->user_login : $full_name;

      $account_url = MeprUtils::get_permalink($post->ID);
      $delim = MeprAppCtrl::get_param_delimiter_char($account_url);

      $perpage = intval(isset($_REQUEST['perpage']) ? $_REQUEST['perpage'] : 10);
      $currpage = intval(isset($_REQUEST['currpage']) ? $_REQUEST['currpage'] : 1);
      $search = wp_kses((isset($_REQUEST['search']) ? $_REQUEST['search'] : ''), false);

      $res = $ca->sub_account_list_table('last_name','ASC',$currpage,$perpage,$search);

      $sub_accounts = $res['results'];
      $total_sub_accounts = $res['count'];

      $total_pages = max(($total_sub_accounts / $perpage),1);
      $total_pages = (is_int($total_pages) ? $total_pages : (((int)$total_pages)+1));

      $prev_page = (($currpage > 1) ? ($currpage-1) : false);
      $next_page = (($currpage < $total_pages) ? ($currpage+1) : false);

      $app_helper = new MPCA_App_Helper();

      // We now have a valid corporate account
      require(MeprView::file('/mpca-manage-account-template'));
    }
  }

  public function import_sub_account_users($ca) {
    $maxrows = 200;
    $results = array();
    $results['errors'] = $this->validate_import();
    $results['message'] = '';
    $args    = $_POST;


    if(empty($errors)) {
      $file = $_FILES['mpca_sub_accounts_csv'];
      $tmpname = $file['tmp_name'];

      // UPLOAD CSV FILE
      $filename = MeprUtils::random_string(10,true,true) . '.csv';
      $filepath = $this->csv_file_dir() . '/' . $filename;

      // Required: moves uploaded file to new location
      if(!move_uploaded_file($tmpname, $filepath)) {
        $errors[] = __('Error uploading file', 'memberpress-corporate');
      }

      // Add corporate ID to args before import
      $args['corporate_account_id'] = $ca->id;

      // Start the import
      $results = $this->import_from_csv($filepath, $args, $maxrows);
    }

    return $results;
  }

  /** Adds a link to the Account subscription page to manage sub accounts when there's an associated corporate account record.
    * @user - MeprUser object
    * @row - This is not a MeprSubscription but rather a row from the database describing this subscription
    * @transaction - MeprTransaction object
    * @issub - Is a sub?
    */
  public function maybe_add_sub_account_management_link($user, $row, $transaction, $issub) {
    $obj_type = ($issub ? 'subscriptions' : 'transactions');

    $ca = MPCA_Corporate_Account::find_corporate_account_by_obj_id($row->id, $obj_type);

    if(!empty($ca) && isset($ca->id) && !empty($ca->id) && $ca->is_enabled()) {
      ?>
      <a href="<?php echo $ca->sub_account_management_url(); ?>" class="mepr-account-row-action mepr-account-manage-sub-accounts"><?php _e('Sub Accounts', 'memberpress-corporate'); ?></a>
      <?php
    }
  }

  private function validate_import() {
    $errors = array();
    if(MeprUtils::is_post_request() && ($_REQUEST['action'] != 'mpca_sub_account_form')) {

      if(!isset($_REQUEST['action']) || $_REQUEST['action']!='manage_sub_accounts') {
        $errors[] = __('This action can only happen from the Manage Sub Account page', 'memberpress-corporate');
      }

      if(empty($_FILES['mpca_sub_accounts_csv']['tmp_name'])) {
        $errors[] = __('There was an issue uploading your CSV file', 'memberpress-corporate');
      }
    }

    return $errors;
  }

  private function csv_file_dir() {
    $csv_file_path_array = wp_upload_dir();
    $csv_file_path = $csv_file_path_array['basedir'];
    $csv_file_dir = "{$csv_file_path}/mpca/csv";

    if(!is_dir($csv_file_dir)) { // Make sure it exists
      @mkdir($csv_file_dir, 0777, true);
    }

    return $csv_file_dir;
  }

  private function import_from_csv($filepath, $args = array(), $maxrows = 0) {
    $row_num = 0;
    $successful = 0;
    $failed = 0;
    $failed_rows = array();

    require_once(MPCA_IMPORTERS_PATH . '/MpimpCorporateSubAccountsImporter.php');

    $headers = array();
    if(($fh = fopen($filepath, "r")) !== false) {
      for($row_num = 0; (($row = fgetcsv($fh, 1000, ",")) !== false); $row_num++) {
        if($row_num <= 0) {
          $headers = $row;
        }
        else {
          $user = array();

          // Turn into an associative array with the headers as keys
          foreach($row as $i => $cell) {
            $user[$headers[$i]] = $cell;
          }

          try {
            $obj = MpimpImporterFactory::fetch('MpimpCorporateSubAccountsImporter');
            $obj->import($user, $args);
            $successful++;
          }
          catch( Exception $e ) {
            // Log the error message
            MeprUtils::debug_log($e->getMessage());

            $failed_rows[] = sprintf(__('Row %d failed for reason: %s', 'memberpress-corporate'), $row_num, __($e->getMessage(), 'memberpress-corporate'));
            $failed++;
            continue;
          }
        }

        // Stop processing once we hit maxrows (but only if maxrows > 0 ... 0 indicates unlimited maxrows)
        if($maxrows > 0 && ($successful + $failed) >= $maxrows) {
          break;
        }
      }

      fclose($fh);

      return array(
        'message'  => sprintf(__('Imported: %d successful. %d failed', 'memberpress-corporate'), $successful, $failed),
        'errors' => $failed_rows
      );
    }

  }

  public function add_checkout_fields() {
    if(isset($_REQUEST['ca'])) {

      // Add hidden field to the checkout form with CA id as the value
      echo "<input id='mpca-corporate-account-id' name='mpca_corporate_account_id' value='{$_REQUEST['ca']}' type='hidden' />";
    }
  }

  public function add_sub_account_user($ca) {
    $mepr_options = MeprOptions::fetch();
    $errors = array();
    $message = '';

    $userdata = $_REQUEST['userdata'];
    if($mepr_options->username_is_email) {
      $userdata['user_login'] = $userdata['user_email'];
    }
    if(!empty($userdata['existing_login'])) {
      // Find the existing user
      if($user = get_user_by('login', $userdata['existing_login'])) {
        // Admin can add any existing user
        // Corp account owner cannot add admins
        if(MeprUtils::is_mepr_admin() || user_can($user, 'manage_options') === false) {
          $user_id = $user->ID;
        }
        else {
          array_push($errors, __('Cannot Add Existing User', 'memberpress-corporate'));
          return compact('message', 'errors');
        }

        // Block parent Corporate account user from being able to add themselves as a sub account.
        if( $user->ID == $ca->user_id ){
          array_push($errors, __('Cannot Add Yourself as Sub Account', 'memberpress-corporate'));
          return compact('message', 'errors');
        }
      }
      else {
        array_push($errors, __('Existing User Not Found', 'memberpress-corporate'));
        return compact('message', 'errors');
      }
    }
    else {
      // Create the sub account user
      $userdata['user_pass'] = wp_generate_password( $length=12, $include_standard_special_chars=false );
      $user_id = wp_insert_user( $userdata );
      if( ! is_wp_error($user_id) ) {

        if(!empty($_REQUEST['userdata']['first_name'])) {
          update_user_meta($user_id, 'first_name', sanitize_text_field($_REQUEST['userdata']['first_name']));
        }
        if(!empty($_REQUEST['userdata']['last_name'])) {
          update_user_meta($user_id, 'last_name', sanitize_text_field($_REQUEST['userdata']['last_name']));
        }
      }
      else {
        array_push($errors, $user_id->get_error_message());
        return compact('message', 'errors');
      }
    }

    // Associate the sub account user with the corporate account
    $result = $ca->add_sub_account_user( $user_id );

    if( is_wp_error($result) ) {
      array_push($errors, $result->get_error_message());
      return compact('message', 'errors');
    }

    // Send welcome email
    if(empty($userdata['existing_login']) && isset($userdata['welcome'])) {
      $mailer = MeprEmailFactory::fetch('Mepr_Sub_Account_Welcome_Email');
      $transaction = $ca->get_user_sub_account_transaction($user_id);
      $mailer->send_sub_account_welcome_email($transaction);
    }

    $message = __('You successfully added a sub account', 'memberpress-corporate');

    return compact('message', 'errors');
  }
}
