<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of CampaignMonitor into MemberPress
*/

class MpCampaignMonitor {
  public function __construct() {
    add_action('mepr_display_autoresponders', array($this,'display_option_fields'));
    add_action('mepr-process-options', array($this,'store_option_fields'));
    add_action('mepr-user-signup-fields', array($this,'display_signup_field'));
    add_action('mepr-signup', array($this,'process_signup'));
    add_action('mepr-txn-store', array($this,'process_status_changes'));
    add_action('mepr-subscr-store', array($this,'process_status_changes'));
    add_action('mepr-txn-expired', array($this,'process_status_changes'), 10, 2);
    add_action('mepr-product-advanced-metabox', array($this,'display_product_override'));
    add_action('mepr-product-save-meta', array($this,'save_product_override'));
    add_filter('mepr-validate-account', array($this,'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this,'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this,'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_campaignmonitor_ping_apikey', array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_campaignmonitor_get_lists',   array($this, 'ajax_get_lists'));
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-campaignmonitor-js', MPCAMPAIGNMONITOR_URL.'/campaignmonitor.js');
    wp_enqueue_script('mp-campaignmonitor-options-js', MPCAMPAIGNMONITOR_URL.'/campaignmonitor_options.js', array('mp-campaignmonitor-js'));
    wp_localize_script('mp-campaignmonitor-options-js', 'MeprCampaignMonitor', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-campaignmonitor-js', MPCAMPAIGNMONITOR_URL.'/campaignmonitor.js');
    wp_enqueue_script('mp-campaignmonitor-product-js', MPCAMPAIGNMONITOR_URL.'/campaignmonitor_product.js', array('mp-campaignmonitor-js'));
  }

  public function update_user_email($errors, $mepr_user) {
    if(!$this->is_enabled_and_authorized()) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($mepr_user->user_email != $new_email) {
      //First let's update the global list_id
      $this->update_subscriber($mepr_user, $this->list_id(), $new_email);

      // Let's update the product list_id
      $products = $mepr_user->active_product_subscriptions('products');

      if(!empty($products)) {
        foreach($products as $prd) {
          $enabled = (bool)get_post_meta($prd->ID, '_meprcampaignmonitor_list_override', true);
          $list_id = get_post_meta($prd->ID, '_meprcampaignmonitor_list_override_id', true);

          if($enabled && !empty($list_id)) {
            $this->update_subscriber($mepr_user, $list_id, $new_email);
          }
        }
      }
    }

    return $errors;
  }

  public function update_subscriber(MeprUser $contact, $list_id, $new_email) {
    $args = array(
      'list' => $list_id,
      'email' => $contact->user_email,
      'name' => $contact->first_name . ' ' . $contact->last_name,
      'new_email' => $new_email
    );

    $res = $this->call('update_email', $args);

    if($res != 'error') {
      return true;
    }
    else {
      return false;
    }
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-campaignmonitor">
      <input type="checkbox" name="meprcampaignmonitor_enabled" id="meprcampaignmonitor_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprcampaignmonitor_enabled"><?php _e('Enable Campaign Monitor', 'memberpress-campaignmonitor'); ?></label>
    </div>
    <div id="campaignmonitor_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-campaignmonitor-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-campaignmonitor-message" class="mepr-hidden mepr-active"></div>

      <div id="meprcampaignmonitor-client-id">
        <label>
          <span><?php _e('Campaign Monitor Client ID', 'memberpress-campaignmonitor'); ?></span>
          <?php MeprAppHelper::info_tooltip('meprcampaignmonitor-client-id',
              __('Campaign Monitor Client ID', 'memberpress-campaignmonitor'),
              __('In order for an application to access the Campaign Monitor API, it needs to have a valid Client ID.', 'memberpress-campaignmonitor'));
          ?>
          <input type="text" name="meprcampaignmonitor_client_id" id="meprcampaignmonitor_client_id" value="<?php echo $this->clientid(); ?>" class="mepr-text-input form-field" size="50" />
        </label>
        <div>
                <span class="description">
                     <?php _e('You can get your Campaign Monitor Client ID under Manage Account > API keys', 'memberpress-campaignmonitor'); ?>
                </span>
        </div>
      </div>
      <br/>
      <div id="meprcampaignmonitor-api-key">
        <label>
          <span><?php _e('Campaign Monitor API Key ', 'memberpress-campaignmonitor'); ?></span>

          <?php MeprAppHelper::info_tooltip('meprcampaignmonitor-api-key',
              __('Campaign Monitor API Key', 'memberpress-campaignmonitor'),
              __('In order to integrate Campaign Monitor with your application , you need to get a Campaign Monitor API key.', 'memberpress-campaignmonitor'));
          ?>

          <input type="text" name="meprcampaignmonitor_api_key" id="meprcampaignmonitor_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="50" />
          <span id="mepr-campaignmonitor-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-campaignmonitor-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
              <span class="description">
                <?php _e('You can get your Campaign Monitor API key under Manage Account > API keys', 'memberpress-campaignmonitor'); ?>
              </span>
        </div>
      </div>
      <br/>
      <div id="meprcampaignmonitor-options">
        <div id="meprcampaignmonitor-list-id">
          <label>
            <span><?php _e('Campaign Monitor Email List:', 'memberpress-campaignmonitor'); ?></span>
            <select name="meprcampaignmonitor_list_id" id="meprcampaignmonitor_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
        <br/>
        <!--                <div id="meprcampaignmonitor-double-optin">
                    <label for="meprcampaignmonitor_double_optin">
                        <input type="checkbox" name="meprcampaignmonitor_double_optin" id="meprcampaignmonitor_double_optin" class="form-field" <?php /*checked($this->is_double_optin_enabled()); */?> />
                        <span><?php /*_e('Enable Double Opt-in', 'memberpress-campaignmonitor'); */?></span>
                    </label>
                    <br/>
                      <span class="description">
                        <?php /*_e("Members will have to click a confirmation link in an email before being added to your list.", 'memberpress-campaignmonitor'); */?>
                      </span>
                </div>
                <br/>-->
        <div id="meprcampaignmonitor-optin">
          <label>
            <input type="checkbox" name="meprcampaignmonitor_optin" id="meprcampaignmonitor_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-campaignmonitor'); ?></span>
          </label>
          <div>
            <span class="description">
              <?php _e('If checked, an opt-in checkbox will appear on all of your product registration pages.', 'memberpress-campaignmonitor'); ?>
            </span>
          </div>
        </div>
        <div id="meprcampaignmonitor-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-campaignmonitor'); ?>
            <input type="text" name="meprcampaignmonitor_text" id="meprcampaignmonitor_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-campaignmonitor'); ?></span></div>
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
    update_option('meprcampaignmonitor_enabled',      (isset($_POST['meprcampaignmonitor_enabled'])));
    update_option('meprcampaignmonitor_client_id',      $_POST['meprcampaignmonitor_client_id']);
    update_option('meprcampaignmonitor_api_key',      stripslashes($_POST['meprcampaignmonitor_api_key']));
    update_option('meprcampaignmonitor_list_id',      (isset($_POST['meprcampaignmonitor_list_id']))?stripslashes($_POST['meprcampaignmonitor_list_id']):false);
    //update_option('meprcampaignmonitor_double_optin', (isset($_POST['meprcampaignmonitor_double_optin'])));
    update_option('meprcampaignmonitor_optin',        (isset($_POST['meprcampaignmonitor_optin'])));
    update_option('meprcampaignmonitor_text',         stripslashes($_POST['meprcampaignmonitor_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprcampaignmonitor_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprcampaignmonitor_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-campaignmonitor-signup-field">
          <div id="mepr-campaignmonitor-checkbox">
            <input type="checkbox" name="meprcampaignmonitor_opt_in" id="meprcampaignmonitor_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-campaignmonitor-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-campaignmonitor-privacy">
            <small>
              <a href="https://www.campaignmonitor.com/privacy/" class="mepr-campaignmonitor-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-campaignmonitor'); ?></a>
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

    $enabled = (bool)get_post_meta($prd->ID, '_meprcampaignmonitor_list_override', true);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) { return; }

    // if !is_option_enabled then opted_in is always true
    $opted_in = true;
    if($this->is_optin_enabled()) { $opted_in = isset($_POST['meprcampaignmonitor_opt_in']); }

    if($opted_in) {
      $this->add_subscriber($usr, $this->list_id());
    }
  }

  public function process_status_changes($obj, $sub_status = false) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if($obj instanceof MeprTransaction && $sub_status !== false && $sub_status == MeprSubscription::$active_str) {
      return; //This is an expiring transaction which is part of an active subscription, so don't remove the user from the list
    }

    $usr = $obj->user();

    //Member is active so let's not remove them
    if(in_array($obj->product_id, $usr->active_product_subscriptions('ids', true))) {
      $this->maybe_add_subscriber($obj, $usr);
    }
    else {
      $this->maybe_delete_subscriber($obj, $usr);
    }
  }

  public function maybe_add_subscriber($obj, $usr) {
    $enabled = (bool)get_post_meta($obj->product_id, '_meprcampaignmonitor_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_meprcampaignmonitor_list_override_id', true);

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->add_subscriber($usr, $list_id);
    }

    return false;
  }

  public function maybe_delete_subscriber($obj, $usr) {
    $enabled = (bool)get_post_meta($obj->product_id, '_meprcampaignmonitor_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_meprcampaignmonitor_list_override_id', true);

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->delete_subscriber($usr, $list_id);
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_list = (bool)get_post_meta($product->ID, '_meprcampaignmonitor_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprcampaignmonitor_list_override_id', true);

    ?>
    <div id="mepr-campaignmonitor" class="mepr-product-adv-item">
      <input type="checkbox" name="meprcampaignmonitor_list_override" id="meprcampaignmonitor_list_override" data-clientid="<?php echo $this->clientid(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_list); ?> />
      <label for="meprcampaignmonitor_list_override"><?php _e('Campaign Monitor Email list for this Product', 'memberpress-campaignmonitor'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprcampaignmonitor-list-override',
          __('Enable Product Campaign Monitor List', 'memberpress-campaignmonitor'),
          __('If this is set the member will be added to this email list when their payment is completed for this product. If the member cancels or you refund their subscription, they will be removed from the list automatically. In order to do that, please set Unsubscribe settings to "Only remove them from this list".  You must have your Campaign Monitor Client ID and API key set in the Options before this will work.', 'memberpress-campaignmonitor'));
      ?>

      <div id="meprcampaignmonitor_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Campaign Monitor Email List: ', 'memberpress-campaignmonitor'); ?></label>
        <select name="meprcampaignmonitor_list_override_id" id="meprcampaignmonitor_list_override_id" data-listid="<?php echo stripslashes($override_list_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprcampaignmonitor_list_override'])) {
      update_post_meta($product->ID, '_meprcampaignmonitor_list_override', true);
      update_post_meta($product->ID, '_meprcampaignmonitor_list_override_id', stripslashes($_POST['meprcampaignmonitor_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprcampaignmonitor_list_override', false);
    }
  }

  public function ping_apikey() {
    return $this->call('client_info', array(), '', '');
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) or !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) or !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-campaignmonitor'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['clientid']) || !isset($_POST['apikey'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-campaignmonitor'), 'type' => 'memberpress')));
    }

    die($this->call('client_info', array() ,$_POST['clientid'] ,$_POST['apikey']));
  }

  public function get_lists() {
    $args = array();

    return $this->call('get_list', $args, '', '');
  }

  public function ajax_get_lists() {
    $args = array("ids" => "all"); //A comma-separated list of subscription form ID's of lists you wish to view. Pass "all" to view all lists.

    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-campaignmonitor'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['clientid']) || !isset($_POST['apikey'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-campaignmonitor'), 'type' => 'memberpress')));
    }

    die($this->call('get_list', $args, $_POST['clientid'], $_POST['apikey']));
  }

  public function add_subscriber(MeprUser $contact, $list_id) {
    $args = array(
        'email' => $contact->user_email,
        'list' => $list_id,
        'name' => $contact->first_name . ' ' . $contact->last_name
    );

    $res = $this->call('add_subscriber', $args);

    if($res != 'error') {
      return true;
    }
    else {
      return false;
    }
  }

  /* unsubscribe */
  public function delete_subscriber(MeprUser $contact, $list_id) {
    $args = array(
        'email' => $contact->user_email,
        'list' => $list_id
    );

    $res = $this->call('delete_subscriber', $args);

    if($res != 'error') {
      return true;
    }
    else {
      return false;
    }
  }

  private function call($endpoint, $args = array(), $clientid = null, $apikey = null) {
    if(is_null($clientid)) { $clientid = $this->clientid(); }
    if(is_null($apikey)) { $apikey = $this->apikey(); }

    $auth = array('api_key' => $apikey);

    if($endpoint == 'client_info') {
      require_once(MPCAMPAIGNMONITOR_PATH .'/vendor/createsend/csrest_clients.php');

      $wrap = new CS_REST_Clients($clientid, $auth);
      $result = $wrap->get();

      if($result->was_successful()) {
        $response = $result->response;
      } else {
        $response = false;
      }
    }
    elseif($endpoint == 'get_list') {
      require_once(MPCAMPAIGNMONITOR_PATH .'/vendor/createsend/csrest_clients.php');

      $wrap = new CS_REST_Clients($clientid, $auth);
      $result = $wrap->get_lists();

      if($result->was_successful()) {
        $response = $result->response;
      } else {
        $response = false;
      }
    }
    elseif( $endpoint == 'add_subscriber' ) {
      require_once(MPCAMPAIGNMONITOR_PATH .'/vendor/createsend/csrest_subscribers.php');

      $wrap = new CS_REST_Subscribers($args["list"], $auth);
      $result = $wrap->add(array(
          'EmailAddress' => $args["email"],
          'Name' => $args["name"],
          'Resubscribe' => true
      ));

      if($result->was_successful()) {
        $response = true;
      } else {
        $response = false;
      }
    }
    elseif($endpoint == 'delete_subscriber') { // unsubscribe
      require_once(MPCAMPAIGNMONITOR_PATH .'/vendor/createsend/csrest_subscribers.php');

      $wrap = new CS_REST_Subscribers($args["list"], $auth);
      $result = $wrap->unsubscribe($args["email"]);

      if($result->was_successful()) {
        $response = true;
      } else {
        $response = false;
      }
    }
    elseif($endpoint == 'update_email') {
      require_once(MPCAMPAIGNMONITOR_PATH .'/vendor/createsend/csrest_subscribers.php');

      $wrap = new CS_REST_Subscribers($args["list"], $auth);
      $result = $wrap->update($args["email"], array(
          'EmailAddress' => $args["new_email"],
          'Name' => $args["name"],
          'Resubscribe' => false //Don't readd them if they've unsub'd
      ));

      if($result->was_successful()) {
        $response = true;
      } else {
        $response = false;
      }
    }

    if($response) {
      return json_encode($response);
    }
    else {
      return 'error';
    }
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return get_option('meprcampaignmonitor_enabled', false);
  }

  private function is_authorized() {
    $clientid = get_option('meprcampaignmonitor_client_id', '');
    $apikey = get_option('meprcampaignmonitor_api_key', '');

    return (!empty($apikey) and !empty($clientid));
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function apikey() {
    return get_option('meprcampaignmonitor_api_key', '');
  }

  private function clientid() {
    return get_option('meprcampaignmonitor_client_id', '');
  }

  private function list_id() {
    return get_option('meprcampaignmonitor_list_id', false);
  }

  private function is_double_optin_enabled() {
    return get_option('meprcampaignmonitor_double_optin', true);
  }

  private function is_optin_enabled() {
    return get_option('meprcampaignmonitor_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-campaignmonitor'), get_option('blogname'));
    return get_option('meprcampaignmonitor_text', $default);
  }
} //END CLASS
