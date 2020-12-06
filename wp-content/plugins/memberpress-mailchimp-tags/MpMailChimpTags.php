<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of MailChimp into MemberPress
*/
class MpMailChimpTags {
  public function __construct() {
    // Storing fields
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-product-advanced-metabox', array($this, 'display_membership_options'));
    add_action('mepr-product-save-meta',        array($this, 'save_membership_options'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field')); //Used just for privacy link, no opt-in box
    add_filter('mepr-validate-account',         array($this, 'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Signup
    add_action('mepr-signup-user-loaded', array($this, 'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'add_tag'));
    add_action('mepr-account-is-inactive', array($this, 'remove_tag'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_mailchimptags_ping_apikey', array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_mailchimptags_get_lists',   array($this, 'ajax_get_lists'));
    add_action('wp_ajax_mepr_mailchimptags_get_tags',   array($this, 'ajax_get_tags'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your MailChimp integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-mailchimp-tags');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-mailchimptags-js', MPMAILCHIMPTAGS_URL.'/mailchimptags.js');
    wp_enqueue_script('mp-mailchimptags-options-js', MPMAILCHIMPTAGS_URL.'/mailchimptags_options.js', array('mp-mailchimptags-js'));
    wp_localize_script('mp-mailchimptags-options-js', 'MeprMailChimpTags', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-mailchimptags-js', MPMAILCHIMPTAGS_URL.'/mailchimptags.js');
    wp_enqueue_script('mp-mailchimptags-product-js', MPMAILCHIMPTAGS_URL.'/mailchimptags_product.js', array('mp-mailchimptags-js'));
    wp_localize_script('mp-mailchimptags-product-js', 'MeprMailChimpTags', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function display_option_fields() {
    require(MPMAILCHIMPTAGS_PATH.'/views/options.php');
  }

  public function validate_option_fields($errors) {
    // Nothing to validate yet -- if ever
  }

  public function update_option_fields() {
    // Nothing to do yet -- if ever
  }

  public function store_option_fields() {
    update_option('meprmailchimptags_enabled',        (int)(isset($_POST['meprmailchimptags_enabled'])));
    update_option('meprmailchimptags_api_key',        stripslashes($_POST['meprmailchimptags_api_key']));
    update_option('meprmailchimptags_list_id',        (isset($_POST['meprmailchimptags_list_id']))?stripslashes($_POST['meprmailchimptags_list_id']):false);
    update_option('meprmailchimptags_global_tag_id',  (isset($_POST['meprmailchimptags_tag_id']))?stripslashes($_POST['meprmailchimptags_tag_id']):false);
    update_option('meprmailchimptags_double_opt_in',  (int)(isset($_POST['meprmailchimptags_double_opt_in'])));
    update_option('meprmailchimptags_optin',          (int)(isset($_POST['meprmailchimptags_optin'])));
    update_option('meprmailchimptags_optin_text',     stripslashes($_POST['meprmailchimptags_optin_text']));
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_membership_options($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $add_tag = (bool)get_post_meta($product->ID, '_meprmailchimptags_add_tag', true);
    $tag = get_post_meta($product->ID, '_meprmailchimptags_tag_id', true);

    require(MPMAILCHIMPTAGS_PATH.'/views/membership.php');
  }

  public function save_membership_options($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprmailchimptags_add_tags']) && !empty($_POST['meprmailchimptags_tag_id'])) {
      update_post_meta($product->ID, '_meprmailchimptags_add_tag', true);
      update_post_meta($product->ID, '_meprmailchimptags_tag_id', stripslashes($_POST['meprmailchimptags_tag_id']));
    }
    elseif(!isset($_POST['meprmailchimptags_add_tags'])) {
      update_post_meta($product->ID, '_meprmailchimptags_add_tag', false);
    }
    else {
      //Do nothing, because an ajax lookup could have failed before they saved the membership -- so we don't want to wipe out the setting they had before the ajax failure
    }
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post         = MeprUtils::get_current_post();
    $prd          = false;

    if($post !== false) {
      $prd = MeprProduct::is_product_page($post);
    }

    //If the per membership tag is enabled, and the global audience option is disabled -- then we should be sure the member doesn't see this
    if($prd !== false && isset($prd->ID) && $prd->ID) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprmailchimptags_add_tag', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() && $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprmailchimptags_opt_in']):$mepr_options->opt_in_checked_by_default;
      require(MPMAILCHIMPTAGS_PATH.'/views/opt-in.php');
    }
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_REQUEST['wpnonce']) || !wp_verify_nonce($_REQUEST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('status' => 'failed', 'message' => __('Hey yo, why you creepin\'?', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_REQUEST['apikey'])) {
      die(json_encode(array('status' => 'failed', 'message' => __('No apikey code was sent', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    $res = $this->call('', array(), 'GET', $_REQUEST['apikey']);

    if($res && isset($res['response']['code']) && $res['response']['code'] == 200) {
      die(json_encode(array('status' => 'succeeded', 'message' => __('Everything\'s Chimpy!', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    die(json_encode(array('status' => 'failed', 'message' => __('Something went wrong. Double check your input.', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
  }

  public function ajax_get_lists() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('status' => 'failed', 'message' => __('Hey yo, why you creepin\'?', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey'])) {
      die(json_encode(array('status' => 'failed', 'message' => __('No apikey code was sent', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    $args = array(
              'fields'  => 'lists.id,lists.name',
              'count'   => 1000
            );

    $res = $this->call('lists', $args, 'GET', $_POST['apikey']);

    if($res && isset($res['response']['code']) && $res['response']['code'] == 200) {
      die($res['body']);
    }

    die(json_encode(array('status' => 'failed', 'message' => __('An unknown error occured', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
  }

  public function ajax_get_tags() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('status' => 'failed', 'message' => __('Hey yo, why you creepin\'?', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey'])) {
      die(json_encode(array('status' => 'failed', 'message' => __('No apikey code was sent', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }
    if(!isset($_POST['listid'])) {
      die(json_encode(array('status' => 'failed', 'message' => __('No listid present', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
    }

    $args = array(
              'type'    => 'text',
              'fields'  => 'merge_fields.merge_id,merge_fields.name',
              'count'   => 1000
            );

    $res = $this->call("lists/{$_POST['listid']}/merge-fields", $args, 'GET', $_POST['apikey']);

    if($res && isset($res['response']['code']) && $res['response']['code'] == 200) {
      die($res['body']);
    }

    die(json_encode(array('status' => 'failed', 'message' => __('An unknown error occured', 'memberpress-mailchimp-tags'), 'type' => 'memberpress')));
  }

  //Updates a user's email address
  public function update_user_email($errors, $mepr_user) {
    if(!$this->is_enabled_and_authorized()|| !empty($errors)) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if(is_email($new_email) && $mepr_user->user_email != $new_email) {
      $hashed   = $this->contact_hash($mepr_user->user_email);
      $list_id  = $this->list_id();
      $args = array('email_address' => $new_email);
      //Doing this blindly for now
      $this->call("lists/{$list_id}/members/{$hashed}", $args, 'PATCH');
    }

    return $errors;
  }

  //Adds the global tag
  public function process_signup( $user ) {
    if( ! $this->is_enabled_and_authorized() ) { return; }

    $mepr_options   = MeprOptions::fetch();
    $enabled        = (bool) get_post_meta( (int) sanitize_text_field( $_POST['mepr_product_id'] ), '_meprmailchimptags_add_tag', true );
    $global_tag_val = '1'; //1 by default yo
    $list_id        = $this->list_id();
    $gtid           = (int) $this->global_tag_id();
    $global_tag_tag = $this->get_tag_tag( $gtid, $list_id );

    //Mabe set global tag val to 0 for two situations
    if( ( $enabled && $mepr_options->disable_global_autoresponder_list ) ||
        ( $this->is_optin_enabled() && !isset( $_POST['meprmailchimptags_opt_in'] ) ) ) {
      $global_tag_val = '0';
    }

    if( $this->is_unsubscribed( $user->user_email ) ) {
      // Unsubscribed from audience some time in the past. We have to double opt-in them here or they cannot be re-added.
      $status = 'pending';
    }
    elseif( $global_tag_val == '0' ) {
      // If they're not a 1 for the global audience, we'll silently add them to the audience still for per-membership tagging (no double opt-in here)
      $status = 'subscribed';
    }
    else {
      $status = $this->get_double_opt_in_status( $user->user_email );
    }

    $args = array(
      'email_address' => $user->user_email,
      'status'        => $status,
      'status_if_new' => $status,
      'merge_fields'  => array( 'FNAME'             => $user->first_name,
                                'LNAME'             => $user->last_name,
                                "{$global_tag_tag}" => $global_tag_val
                         )
    );

    $args = MeprHooks::apply_filters( 'mepr-mailchimptags-add-subscriber-args', $args, $user );

    $this->update_subscriber( $user, $args );
  }

  public function add_tag($txn) {
    $contact = $txn->user();

    $list_id  = $this->list_id();
    $add_tag  = (bool)get_post_meta($txn->product_id, '_meprmailchimptags_add_tag', true);
    $tag_id   = get_post_meta($txn->product_id, '_meprmailchimptags_tag_id', true);
    $gtid     = (int)$this->global_tag_id();

    if(empty($add_tag) || empty($tag_id)) { return; }

    //Get the MMERGEX Tag keys
    $tag_tag        = $this->get_tag_tag($tag_id, $list_id);
    $global_tag_tag = $this->get_tag_tag($gtid, $list_id);

    $args = array(
      'email_address' => $contact->user_email,
      'status_if_new' => $this->get_double_opt_in_status($contact->user_email), //Will add this user to the audience if they don't exist yet
      'merge_fields'  => array( 'FNAME'             => $contact->first_name,
                                'LNAME'             => $contact->last_name,
                                // "{$global_tag_tag}" => '1',
                                "{$tag_tag}"        => 'active'
                         )
    );

    if($this->is_unsubscribed($contact->user_email)) {
      $args['status'] = 'pending'; //Will resend double opt-in email if user exists, but is unsubscribed
    }

    $args = MeprHooks::apply_filters('mepr-mailchimptags-add-tag-args', $args, $contact);

    $this->update_subscriber($contact, $args);
  }

  public function remove_tag($txn) {
    $contact = $txn->user();

    $list_id  = $this->list_id();
    $add_tag  = (bool)get_post_meta($txn->product_id, '_meprmailchimptags_add_tag', true);
    $tag_id   = get_post_meta($txn->product_id, '_meprmailchimptags_tag_id', true);
    $gtid     = (int)$this->global_tag_id();

    if(empty($add_tag) || empty($tag_id)) { return; }

    //Get the MMERGEX Tag key
    $tag_tag        = $this->get_tag_tag($tag_id, $list_id);
    $global_tag_tag = $this->get_tag_tag($gtid, $list_id);

    $args = array(
      'email_address' => $contact->user_email,
      'status_if_new' => 'pending', //Shouldn't happen, but just in case - let's send them a double opt-in by setting them to "pending"
      'merge_fields'  => array( 'FNAME'             => $contact->first_name,
                                'LNAME'             => $contact->last_name,
                                // "{$global_tag_tag}" => '1',
                                "{$tag_tag}"        => 'inactive'
                         )
    );

    $args = MeprHooks::apply_filters('mepr-mailchimptags-remove-tag-args', $args, $contact);

    $this->update_subscriber($contact, $args);
  }

  //Updates a subscriber, or adds them if new
  public function update_subscriber(MeprUser $contact, $args) {
    $hashed   = $this->contact_hash($contact->user_email);
    $list_id  = $this->list_id();

    $res = $this->call("lists/{$list_id}/members/{$hashed}", $args, 'PUT');
  }

  public function is_unsubscribed($email) {
    $hashed   = $this->contact_hash($email);
    $list_id  = $this->list_id();

    $res = $this->call("lists/{$list_id}/members/{$hashed}", array('fields' => 'status'), 'GET');

    //Some error, or the user doesn't exist maybe?
    if(!$res || !isset($res['response']['code']) || $res['response']['code'] != 200) {
      return false;
    }

    $res = json_decode($res['body']);

    return (isset($res->status) && strtolower(trim($res->status)) == 'unsubscribed');
  }

  public function exists_in_list($email) {
    $hashed   = $this->contact_hash($email);
    $list_id  = $this->list_id();

    $res = $this->call("lists/{$list_id}/members/{$hashed}", array('fields' => 'status'), 'GET');
    //Some error, or the user doesn't exist maybe?
    if(!$res || !isset($res['response']['code'])) {
      return false;
    }

    return ($res['response']['code'] == 200);
  }

  //Get's the MMERGEX tag key
  private function get_tag_tag($tag_id, $list_id) {
    $res = $this->call("lists/{$list_id}/merge-fields/{$tag_id}");
    $res = json_decode($res['body']);
    return $res->tag;
  }

  private function call($endpoint, $args = array(), $method = 'GET', $apikey = null) {
    if(is_null($apikey)) { $apikey = $this->apikey(); }

    $dc = $this->get_datacenter($apikey);
    $url = "https://{$dc}.api.mailchimp.com/3.0/{$endpoint}";

    $wp_args = array(
      'headers'     =>  array(
                          "Content-Type"  => "application/json",
                          "Authorization" => "Basic " . base64_encode(uniqid().":".$apikey),
                        ),
      'timeout'     => 60,
      'sslverify'   => false,
      'method'      => strtoupper($method),
      'httpversion' => '1.1',
      'body'        => array()
    );

    if(strtoupper($method) == 'GET' || strtoupper($method) == 'DELETE') {
      $url .= '?' . http_build_query($args);
    }
    else {
      $wp_args['body'] = json_encode($args);
    }

    $res = wp_remote_request($url, $wp_args);

    if(!is_wp_error($res)) {
      return $res;
    }
    else {
      return false;
    }
  }

  private function get_datacenter($apikey) {
    $dc = explode('-', $apikey);
    return isset($dc[1]) ? $dc[1] : '';
  }

  private function is_enabled() {
    return get_option('meprmailchimptags_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('meprmailchimptags_api_key', '');
    return !empty($apikey);
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() && $this->is_authorized());
  }

  private function is_optin_enabled() {
    return get_option('meprmailchimptags_optin', false);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-mailchimp-tags'), get_option('blogname'));
    return get_option('meprmailchimptags_optin_text', $default);
  }

  private function apikey() {
    return get_option('meprmailchimptags_api_key', '');
  }

  private function list_id() {
    return get_option('meprmailchimptags_list_id', false);
  }

  private function global_tag_id() {
    return get_option('meprmailchimptags_global_tag_id', '');
  }

  private function double_opt_in() {
    return get_option('meprmailchimptags_double_opt_in', true);
  }

  private function get_double_opt_in_status($email) {
    $exists = $this->exists_in_list($email);

    // This prevents us from accidentally setting an existing subscriber back to pending
    if($exists) { return 'subscribed'; }

    return ($this->double_opt_in()) ? 'pending' : 'subscribed';
  }

  private function contact_hash($email) {
    return md5(strtolower($email));
  }
} //END CLASS
