<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of ActiveCampaign into MemberPress
*/
class MpActiveCampaign {
  public function __construct() {
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field'));
    add_action('mepr-signup',                   array($this, 'process_signup'));
    add_action('mepr-txn-store',                array($this, 'process_status_changes'));
    add_action('mepr-subscr-store',             array($this, 'process_status_changes'));
    add_action('mepr-txn-expired',              array($this, 'process_status_changes'), 10, 2);
    add_action('mepr-product-advanced-metabox', array($this, 'display_product_override'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_override'));
    add_filter('mepr-validate-account',         array($this, 'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this,'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this,'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_activecampaign_ping_apikey', array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_activecampaign_get_lists',   array($this, 'ajax_get_lists'));
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-activecampaign-js', MPACTIVECAMPAIGN_URL.'/activecampaign.js');
    wp_enqueue_script('mp-activecampaign-options-js', MPACTIVECAMPAIGN_URL.'/activecampaign_options.js', array('mp-activecampaign-js'));
    wp_localize_script('mp-activecampaign-options-js', 'MeprActiveCampaign', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-activecampaign-js', MPACTIVECAMPAIGN_URL.'/activecampaign.js');
    wp_enqueue_script('mp-activecampaign-product-js', MPACTIVECAMPAIGN_URL.'/activecampaign_product.js', array('mp-activecampaign-js'));
  }

  public function update_user_email($errors, $mepr_user) {
    if(!$this->is_enabled_and_authorized()) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($mepr_user->user_email != $new_email) {
      //First let's update the global list_id
      $this->update_subscriber($mepr_user, $this->list_id(), $new_email);

      $products = $mepr_user->active_product_subscriptions('products');

      if(!empty($products)) {
        foreach($products as $prd) {
          $enabled = (bool)get_post_meta($prd->ID, '_mepractivecampaign_list_override', true);
          $list_id = get_post_meta($prd->ID, '_mepractivecampaign_list_override_id', true);

          if($enabled && !empty($list_id)) {
            $this->update_subscriber($mepr_user, $list_id, $new_email);
          }
        }
      }
    }

    return $errors;
  }

  public function update_subscriber(MeprUser $contact, $list_id, $new_email) {
    $email = $contact->user_email;
    $contact_id = $this->contact_id($email);

    $args = array(
      'id' => $contact_id,
      'email' => $new_email,
      'p' => array($list_id => $list_id)
    );

    $res = (array)json_decode($this->call_post('/admin/api.php?api_action=contact_edit&overwrite=0',$args));//overwrite=0: only update included post parameters

    if($res["result_code"] == 1) {
      return true;
    }
    else {
      return false;
    }
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-activecampaign" class="mepr-autoresponder-config">
      <input type="checkbox" name="mepractivecampaign_enabled" id="mepractivecampaign_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="mepractivecampaign_enabled"><?php _e('Enable ActiveCampaign', 'memberpress-activecampaign'); ?></label>
    </div>
    <div id="activecampaign_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-activecampaign-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-activecampaign-message" class="mepr-hidden mepr-active"></div>
      <div id="mepractivecampaign-account">
        <label>
          <span><?php _e('Active Campaign Account:', 'memberpress-activecampaign'); ?></span>
          <input type="text" name="mepractivecampaign_account" id="mepractivecampaign_account" value="<?php echo $this->account(); ?>" class="mepr-text-input form-field" size="20" />
        </label>
        <div>
        <span class="description">
          <?php _e('Your ActiveCampaign account ID. Typically something like: 1234567890123', 'memberpress-activecampaign'); ?>
        </span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-api-key">
        <label>
          <span><?php _e('ActiveCampaign API Key:', 'memberpress-activecampaign'); ?></span>
          <input type="text" name="mepractivecampaign_api_key" id="mepractivecampaign_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="90" />
          <span id="mepr-activecampaign-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-activecampaign-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
      <span class="description">
      <?php _e('You can find your API key under your Account settings at activecampaign.com.', 'memberpress-activecampaign'); ?>
      </span>
        </div>
      </div>
      <br/>
      <div id="mepractivecampaign-options">
        <div id="mepractivecampaign-list-id">
          <label>
            <span><?php _e('ActiveCampaign List:', 'memberpress-activecampaign'); ?></span>
            <select name="mepractivecampaign_list_id" id="mepractivecampaign_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
<!--        <br/>
        <div id="mepractivecampaign-double-optin">
          <label for="mepractivecampaign_double_optin">
            <input type="checkbox" name="mepractivecampaign_double_optin" id="mepractivecampaign_double_optin" class="form-field" <?php /*checked($this->is_double_optin_enabled()); */?> />
            <span><?php /*_e('Enable Double Opt-in', 'memberpress'); */?></span>
          </label><br/>
      <span class="description">
      <?php /*_e("Members will have to click a confirmation link in an email before being added to your list.", 'memberpress'); */?>
      </span>
        </div>-->
        <br/>
        <div id="mepractivecampaign-optin">
          <label>
            <input type="checkbox" name="mepractivecampaign_optin" id="mepractivecampaign_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-activecampaign'); ?></span>
          </label>
          <div>
      <span class="description">
        <?php _e('If checked, an opt-in checkbox will appear on all of your product registration pages.', 'memberpress-activecampaign'); ?>
      </span>
          </div>
        </div>
        <div id="mepractivecampaign-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-activecampaign'); ?>
            <input type="text" name="mepractivecampaign_text" id="mepractivecampaign_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-activecampaign'); ?></span></div>
        </div>
      </div>
    </div>
  <?php
  }

  public function validate_option_fields($errors) {
    // Nothing to validate yet -- if ever
  }

  public function update_option_fields() {
    // Nothing to do yet -- if ever
  }

  public function store_option_fields() {
    update_option('mepractivecampaign_enabled', (isset($_POST['mepractivecampaign_enabled'])));
    update_option('mepractivecampaign_account', $_POST['mepractivecampaign_account']);
    update_option('mepractivecampaign_api_key', stripslashes($_POST['mepractivecampaign_api_key']));
    update_option('mepractivecampaign_list_id', (isset($_POST['mepractivecampaign_list_id']))?stripslashes($_POST['mepractivecampaign_list_id']):false);
    // update_option('mepractivecampaign_double_optin', (isset($_POST['mepractivecampaign_double_optin'])));
    update_option('mepractivecampaign_optin', (isset($_POST['mepractivecampaign_optin'])));
    update_option('mepractivecampaign_text', stripslashes($_POST['mepractivecampaign_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_mepractivecampaign_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['mepractivecampaign_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-activecampaign-signup-field">
          <div id="mepr-activecampaign-checkbox">
            <input type="checkbox" name="mepractivecampaign_opt_in" id="mepractivecampaign_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-activecampaign-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-activecampaign-privacy">
            <small>
              <a href="http://www.activecampaign.com/help/privacy-policy/" class="mepr-activecampaign-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-activecampaign'); ?></a>
            </small>
          </div>
        </div>
      </div>
    <?php
    }
  }

  public function process_signup($txn) {
    $mepr_options = MeprOptions::fetch();

    $usr = $txn->user();
    $prd = $txn->product();

    $enabled = (bool)get_post_meta($prd->ID, '_mepractivecampaign_list_override', true);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) { return; }

    if(!$this->is_optin_enabled() || ($this->is_optin_enabled() && isset($_POST['mepractivecampaign_opt_in']))) {
      $this->add_subscriber($usr, $this->list_id());
    }
  }

  public function process_status_changes($obj, $sub_status = false) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if($obj instanceof MeprTransaction && $sub_status !== false && $sub_status == MeprSubscription::$active_str) {
      return; //This is an expiring transaction which is part of an active subscription, so don't remove the user from the list
    }

    $user = new MeprUser($obj->user_id);

    //Member is active so let's not remove them
    if(in_array($obj->product_id, $user->active_product_subscriptions('ids', true))) {
      $this->maybe_add_subscriber($obj, $user);
    }
    else {
      $this->maybe_delete_subscriber($obj, $user);
    }
  }

  public function maybe_add_subscriber($obj, $user) {
    $enabled = (bool)get_post_meta($obj->product_id, '_mepractivecampaign_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_mepractivecampaign_list_override_id', true);

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      $contact_id = $this->contact_id($user->user_email);

      if($contact_id === false) { //Contact already exists? If so, let's just re-subscribe them
        return $this->add_subscriber($user, $list_id);
      }
      else {
        //Don't remove them if they've paused
        if($obj instanceof MeprSubscription) {
          if($obj->status == MeprSubscription::$suspended_str) {
            return true;
          }
        }
        elseif($obj instanceof MeprTransaction && (int)$obj->subscription_id > 0) {
          $sub = new MeprSubscription($obj->subscription_id);

          if($sub->status == MeprSubscription::$suspended_str) {
            return true;
          }
        }

        //Made it here, so delete the subscriber
        return $this->undelete_subscriber($user, $list_id, $contact_id);
      }
    }

    return false;
  }

  public function maybe_delete_subscriber($obj, $user) {
    $enabled = (bool)get_post_meta($obj->product_id, '_mepractivecampaign_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_mepractivecampaign_list_override_id', true);

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->delete_subscriber($user, $list_id);
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_list = (bool)get_post_meta($product->ID, '_mepractivecampaign_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_mepractivecampaign_list_override_id', true);

    ?>
    <div id="mepr-activecampaign" class="mepr-product-adv-item">
      <input type="checkbox" name="mepractivecampaign_list_override" id="mepractivecampaign_list_override" data-account="<?php echo $this->account(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_list); ?> />
      <label for="mepractivecampaign_list_override"><?php _e('Active Campaign list for this Product', 'memberpress-activecampaign'); ?></label>

      <?php MeprAppHelper::info_tooltip('mepractivecampaign-list-override',
        __('Enable Product ActiveCampaign List', 'memberpress-activecampaign'),
        __('If this is set the member will be added to this list when their payment is completed for this product. If the member cancels or you refund their subscription, they will be removed from the list automatically. You must have your ActiveCampaign API key set in the Options before this will work.', 'memberpress-activecampaign'));
      ?>

      <div id="mepractivecampaign_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('ActiveCampaign List: ', 'memberpress-activecampaign'); ?></label>
        <select name="mepractivecampaign_list_override_id" id="mepractivecampaign_list_override_id" data-listid="<?php echo stripslashes($override_list_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
  <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['mepractivecampaign_list_override'])) {
      update_post_meta($product->ID, '_mepractivecampaign_list_override', true);
      update_post_meta($product->ID, '_mepractivecampaign_list_override_id', stripslashes($_POST['mepractivecampaign_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_mepractivecampaign_list_override', false);
    }
  }

  public function ping_apikey() {
    return $this->call('/admin/api.php?api_action=account_view', array(), '', '');
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) or !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) or !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-activecampaign'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['account'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-activecampaign'), 'type' => 'memberpress')));
    }

    die($this->call('/admin/api.php?api_action=account_view', array(), $_POST['account'], $_POST['apikey']));
  }

  public function get_lists() {
    $args = array('limit' => 100); //100 is the max we can get -- defaults to 25 which isn't cutting it for some of our users

    return $this->call('/admin/api.php?api_action=list_list', $args, '', '');
  }

  public function ajax_get_lists() {
    $args = array("ids" => "all"); //A comma-separated list of subscription form ID's of lists you wish to view. Pass "all" to view all lists.

    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-activecampaign'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['account'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-activecampaign'), 'type' => 'memberpress')));
    }

    die($this->call('/admin/api.php?api_action=list_list', $args, $_POST['account'], $_POST['apikey']));
  }

  public function add_subscriber(MeprUser $contact, $list_id) {
    $args = array(
      'email' => $contact->user_email,
      'p' => array($list_id => $list_id),   // list ID: p[1] = 1
      'status' => array($list_id => 1),     // 1: active, 2: unsubscribed
      'instantresponders' => array($list_id => 1), // Whether or not to set "send instant responders." Examples: 1 = yes, 0 = no.
      'first_name' => $contact->first_name,
      'last_name' => $contact->last_name,
      'ip4' => $contact->user_ip
    );

    $args = MeprHooks::apply_filters('mepr-activecampaign-add-subscriber-args', $args, $contact);

    $res = (array)json_decode($this->call_post('/admin/api.php?api_action=contact_add', $args ));

    if($res["result_code"] == 1) {
     return true;
    }
    else {
     return false;
    }
  }

  /* re-subscribe the contact to the list they were unsubscribed from */
  public function undelete_subscriber(MeprUser $contact, $list_id, $contact_id) {

    if($contact_id == false) { return; }

    $args = array(
      'id'          => $contact_id,
      'email'       => $contact->user_email,
      'p'           => array($list_id => $list_id),
      'status'      => array($list_id => 1), // 1: subscribed
      'first_name'  => $contact->first_name,
      'last_name'   => $contact->last_name,
    );

    $res = (array)json_decode($this->call_post('/admin/api.php?api_action=contact_edit&overwrite=0',$args));

    if($res["result_code"] == 1) {
      return true;
    }
    else {
      return false;
    }
  }

  /* unsubscribe */
  public function delete_subscriber(MeprUser $contact, $list_id) {
    $contact_id = $this->contact_id_in_list($list_id, $contact->user_email); //Make sure this person is actually in this list before we remove them

    if($contact_id == false) { return; }

    $args = array(
      'id'          => $contact_id,
      'email'       => $contact->user_email,
      'p'           => array($list_id => $list_id),
      'status'      => array($list_id => 2), // 2: unsubscribed
      'first_name'  => $contact->first_name,
      'last_name'   => $contact->last_name,
    );

    $res = (array)json_decode($this->call_post('/admin/api.php?api_action=contact_edit&overwrite=0',$args));

    if($res["result_code"] == 1) {
      return true;
    }
    else {
      return false;
    }
  }

  // HTTP method : GET
  private function call($endpoint,$args=array(),$account=null,$apikey=null) {
    if( is_null($apikey) ) { $apikey = $this->apikey(); }
    if( is_null($account) ) { $account = $this->account(); }

    $url = "https://" . $account . ".api-us1.com" . $endpoint; // e.g. :https://account.api-us1.com
    $url .= "&api_key=" . $apikey;
    $url .= "&api_output=json";

    foreach ($args as $key => $value) {
      $url .= "&" . $key . "=" .$value;
      //$url .= "&" . $key . "=" . urlencode( $value );
    }

    $wp_args = array();
    $res = wp_remote_get( $url, $wp_args );

    if(!is_wp_error($res)) {
      return $res['body'];
    }
    else {
      return false;
    }
  }

  // HTTP method : POST
  private function call_post($endpoint,$args=array(),$account=null,$apikey=null) {
    if( is_null($apikey) ) { $apikey = $this->apikey(); }
    if( is_null($account) ) { $account = $this->account(); }

    $url = "http://" . $account . ".api-us1.com" . $endpoint; // e.g. :http://account.api-us1.com/admin/api.php?api_action=contact_add
    $url .= "&api_key=" . $apikey;
    $url .= "&api_output=json";

    $wp_args = array( 'body' =>  $args );
    $res = wp_remote_post( $url, $wp_args );

    if(!is_wp_error($res)) {
      return $res['body'];
    }
    else {
      return false;
    }
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return get_option('mepractivecampaign_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('mepractivecampaign_api_key', '');
    return !empty($apikey);
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function account() {
    return get_option('mepractivecampaign_account', '');
  }

  private function apikey() {
    return get_option('mepractivecampaign_api_key', '');
  }

  private function list_id() {
    return get_option('mepractivecampaign_list_id', false);
  }

  private function is_double_optin_enabled() {
    return get_option('mepractivecampaign_double_optin', true);
  }

  private function is_optin_enabled() {
    return get_option('mepractivecampaign_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-activecampaign'), get_option('blogname'));
    return get_option('mepractivecampaign_text', $default);
  }

  private function contact_id($email){
    $args = array(
      'email' => $email
    );

    $res = (array)json_decode($this->call('/admin/api.php?api_action=contact_view_email',$args));

    if($res["result_code"] == 1) {
      return $res['id'];
    }
    else {
      return false;
    }
  }

  //Used mostly to make sure a user exists in a list before we try and set their status to unsubscribed since a user can be in multiple lists
  private function contact_id_in_list($list_id, $email, $status = 1) {
    $args = array('filters[listid]' => $list_id,
                  'filters[email]'  => $email,
                  'filters[status]' => $status, //1 = subscribed, 2 = unsubscribed
                  'full'            => 0);

    $res = (array)json_decode($this->call('/admin/api.php?api_action=contact_list', $args));

    if($res["result_code"] == 1) {
      foreach($res as $key => $r) {
        if(isset($r->id) && $key == 0) { //First match only -- there should only ever be one match though
          return $r->id;
        }
      }
    }
    else {
      return false;
    }
  }
} //END CLASS
