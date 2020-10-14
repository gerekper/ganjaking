<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of Sendy into MemberPress
*/
class MpSendy {
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
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_sendy_ping_apikey',  array($this, 'ajax_ping_apikey'));
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_enqueue_script('mp-sendy-options-js', MPSENDY_URL.'/sendy_options.js');
    wp_localize_script('mp-sendy-options-js', 'MeprSendy', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_enqueue_script('mp-sendy-product-js', MPSENDY_URL.'/sendy_product.js');
  }

  public function update_user_email($errors, $contact) {
    if(!$this->is_enabled_and_authorized()) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($contact->user_email != $new_email) {
      $this->update_subscriber($contact, $this->list_id(), $new_email);

      $products = $contact->active_product_subscriptions('products');

      if(!empty($products)) {
        foreach($products as $prd) {
          $enabled = (bool)get_post_meta($prd->ID, '_meprsendy_list_override', true);
          $list_id = get_post_meta($prd->ID, '_meprsendy_list_override_id', true);

          if($enabled && !empty($list_id)) {
            $this->update_subscriber($contact, $list_id, $new_email);
          }
        }
      }
    }

    return $errors;
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-sendy" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprsendy_enabled" id="meprsendy_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprsendy_enabled"><?php _e('Enable Sendy', 'memberpress-sendy'); ?></label>
    </div>
    <div id="sendy_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-sendy-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-sendy-message" class="mepr-hidden mepr-active"></div>
      <div id="meprsendy-domain">
        <label>
          <span><?php _e('Sendy Domain:', 'memberpress-sendy'); ?></span>
          <input type="text" name="meprsendy_domain" id="meprsendy_domain" value="<?php echo $this->domain(); ?>" class="mepr-text-input form-field" size="20" />
        </label>
        <div>
          <span class="description">
            <?php _e('This is the domain name used to access your Sendy install. Valid Examples: <b>http://sendy.mysite.com</b> or <b>https://mysite.com/sendy</b>.', 'memberpress-sendy'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprsendy-api-key">
        <label>
          <span><?php _e('Sendy API Key:', 'memberpress-sendy'); ?></span>
          <input type="text" name="meprsendy_api_key" id="meprsendy_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="20" />
          <span id="mepr-sendy-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-sendy-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API key under the Administrator\'s Account Settings in your Sendy install.', 'memberpress-sendy'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprsendy-options">
        <div id="meprsendy-list-id">
          <label>
            <span><?php _e('Sendy List ID:', 'memberpress-sendy'); ?></span>
            <input type="text" name="meprsendy_list_id" id="meprsendy_list_id" value="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field" />
          </label>
        <div>
          <span class="description">
            <?php _e('This ID can be found under "View all lists" section under the ID column.', 'memberpress-sendy'); ?>
          </span>
        </div>
        </div>
        <br/>
        <div id="meprsendy-optin">
          <label>
            <input type="checkbox" name="meprsendy_optin" id="meprsendy_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-sendy'); ?></span>
          </label>
          <div>
            <span class="description">
              <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-sendy'); ?>
            </span>
          </div>
        </div>
        <div id="meprsendy-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-sendy'); ?>
            <input type="text" name="meprsendy_text" id="meprsendy_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-sendy'); ?></span></div>
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
    update_option('meprsendy_enabled',      (isset($_POST['meprsendy_enabled'])));
    update_option('meprsendy_domain',       rtrim(trim(stripslashes($_POST['meprsendy_domain'])), '/'));
    update_option('meprsendy_api_key',      trim(stripslashes($_POST['meprsendy_api_key'])));
    update_option('meprsendy_list_id',      (isset($_POST['meprsendy_list_id']))?trim(stripslashes($_POST['meprsendy_list_id'])):false);
    update_option('meprsendy_optin',        (isset($_POST['meprsendy_optin'])));
    update_option('meprsendy_text',         stripslashes($_POST['meprsendy_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprsendy_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprsendy_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-sendy-signup-field">
          <div id="mepr-sendy-checkbox">
            <input type="checkbox" name="meprsendy_opt_in" id="meprsendy_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-sendy-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-sendy-privacy">
            <small><?php _e('We Respect Your Privacy', 'memberpress-sendy'); ?></small>
          </div>
        </div>
      </div>
      <?php
     }
  }

  public function process_signup($txn) {
    $mepr_options = MeprOptions::fetch();

    $prd = $txn->product();
    $contact = $txn->user();

    $enabled = (bool)get_post_meta($prd->ID, '_meprsendy_list_override', true);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) {
      return;
    }

    if(!$this->is_optin_enabled() || ($this->is_optin_enabled() and isset($_POST['meprsendy_opt_in']))) {
      $this->add_subscriber($contact, $this->list_id());
    }
  }

  public function process_status_changes($obj, $sub_status = false) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if($obj instanceof MeprTransaction && $sub_status !== false && $sub_status == MeprSubscription::$active_str) {
      return; //This is an expiring transaction which is part of an active subscription, so don't remove the contact from the list
    }

    $contact = new MeprUser($obj->user_id);

    //Member is active so let's not remove them
    if(in_array($obj->product_id, $contact->active_product_subscriptions('ids', true))) {
      $this->maybe_add_subscriber($obj, $contact);
    }
    else {
      $this->maybe_remove_subscriber($obj, $contact);
    }
  }

  public function maybe_add_subscriber($obj, $contact) {
    $enabled = (bool)get_post_meta($obj->product_id, '_meprsendy_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_meprsendy_list_override_id', true);

    if($enabled && !empty($list_id)) {
      if(!$this->is_subscribed($contact, $list_id)) {
        return $this->add_subscriber($contact, $list_id);
      }
    }

    return false;
  }

  public function maybe_remove_subscriber($obj, $contact) {
    $enabled = (bool)get_post_meta($obj->product_id, '_meprsendy_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_meprsendy_list_override_id', true);

    if($enabled && !empty($list_id)) {
      if($this->is_subscribed($contact, $list_id)) {
        return $this->remove_subscriber($contact, $list_id);
      }
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_list = (bool)get_post_meta($product->ID, '_meprsendy_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprsendy_list_override_id', true);

    ?>
    <div id="mepr-sendy" class="mepr-product-adv-item">
      <input type="checkbox" name="meprsendy_list_override" id="meprsendy_list_override" data-domain="<?php echo $this->domain(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_list); ?> />
      <label for="meprsendy_list_override"><?php _e('Sendy List for this Membership', 'memberpress-sendy'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprsendy-list-override',
                                        __('Enable Membership Sendy List', 'memberpress-sendy'),
                                        __('If this is set the member will be added to this list when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the list automatically. You must have your Sendy Domain and API key set in the Options before this will work.', 'memberpress-sendy'));
      ?>

      <div id="meprsendy_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Sendy List ID: ', 'memberpress-sendy'); ?></label>
        <input type="text" name="meprsendy_list_override_id" id="meprsendy_list_override_id" value="<?php echo stripslashes($override_list_id); ?>" class="mepr-text-input form-field" />
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprsendy_list_override'])) {
      update_post_meta($product->ID, '_meprsendy_list_override', true);
      update_post_meta($product->ID, '_meprsendy_list_override_id', trim(stripslashes($_POST['meprsendy_list_override_id'])));
    }
    else {
      update_post_meta($product->ID, '_meprsendy_list_override', false);
    }
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) or !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) or !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-sendy'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['domain']) || !isset($_POST['apikey'])) {
      die(json_encode(array('error' => __('No Domain or API Key was sent', 'memberpress-sendy'), 'type' => 'memberpress')));
    }

    $domain = stripslashes($_POST['domain']);
    $apikey = stripslashes($_POST['apikey']);

    $args = array('list_id' => 'AFakeListID'); //Just pass garbage here

    //Just seeing if our apikey result in a 200 response code and not an API key error
    $resp_body = $this->call('/api/subscribers/active-subscriber-count.php', $args, $domain, $apikey);

    if(strtolower(trim($resp_body)) != 'list does not exist') { //We'll get this error if the API key and Domain are valid
      die(json_encode(array('error' => __('Incorrect Domain or API Key', 'memberpress-sendy'), 'type' => 'memberpress')));
    }
    else {
      die(json_encode(array('msg' => __('All set!', 'memberpress-sendy'), 'type' => 'memberpress')));
    }
  }

  public function is_subscribed($contact, $list_id) {
    $args = array(
      'email'   => $contact->user_email,
      'list_id' => $list_id
    );

    $resp_body = $this->call('/api/subscribers/subscription-status.php', $args);

    if($resp_body === false || strtolower(trim($resp_body)) != 'subscribed') {
      return false;
    }

    //They should be subscribed eh?
    return true;
  }

  public function add_subscriber($contact, $list_id, $new_email = null) {
    $args = array(
      'name'    => (string)$contact->first_name . ' ' . (string)$contact->last_name,
      'email'   => (is_null($new_email))?$contact->user_email:$new_email,
      'list'    => $list_id
    );

    $args = MeprHooks::apply_filters('mepr_sendy_add_or_update_subscriber_args', $args, $contact, $list_id, $new_email);

    $resp_body = $this->call('/subscribe', $args);

    return ($resp_body !== false);
  }

  public function remove_subscriber($contact, $list_id) {
    $args = array(
              'email' => $contact->user_email,
              'list'  => $list_id
            );

    $resp_body = $this->call('/unsubscribe', $args);

    return ($resp_body !== false);
  }

  //Sendy's API is very limited so we have to unsubscribe the user's old email and then add them as a new person ugh :(
  public function update_subscriber($contact, $list_id, $new_email) {
    $this->remove_subscriber($contact, $list_id);
    $this->add_subscriber($contact, $list_id, $new_email);
  }

  private function call($endpoint, $args, $domain = null, $apikey = null) {
    if(is_null($domain)) { $domain = $this->domain(); }
    if(is_null($apikey)) { $apikey = $this->apikey(); }

    //Do we need an API key for this one?
    if(strpos($endpoint, '/api/') !== false) {
      $args['api_key'] = $apikey;
    }
    else {
      $args['boolean'] = 'true'; //Give us textual feedback here please
    }

    $args               = apply_filters('mepr-sendy-call-args', $args);
    $url                = "{$domain}{$endpoint}";
    $wp_args            = array('body' => $args);
    $wp_args['method']  = 'POST';
    $wp_args['timeout'] = 30;

    $res = wp_remote_request($url, $wp_args);

    if(!is_wp_error($res) && $res['response']['code'] == 200) {
      return $res['body'];
    }
    else {
      return false;
    }
  }

  private function is_enabled() {
    return get_option('meprsendy_enabled', false);
  }

  private function is_authorized() {
    return ($this->apikey() && $this->domain());
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function domain() {
    return get_option('meprsendy_domain', '');
  }

  private function apikey() {
    return get_option('meprsendy_api_key', '');
  }

  private function list_id() {
    return get_option('meprsendy_list_id', false);
  }

  private function is_optin_enabled() {
    return get_option('meprsendy_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-sendy'), get_option('blogname'));
    return get_option('meprsendy_text', $default);
  }
} //END CLASS
