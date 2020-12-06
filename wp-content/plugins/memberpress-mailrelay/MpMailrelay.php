<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of Mailrelay into MemberPress
*/
class MpMailrelay {
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
    add_action('wp_ajax_mepr_mailrelay_ping_apikey',  array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_mailrelay_get_groups',   array($this, 'ajax_get_groups'));
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-mailrelay-js', MPMAILRELAY_URL.'/mailrelay.js');
    wp_enqueue_script('mp-mailrelay-options-js', MPMAILRELAY_URL.'/mailrelay_options.js', array('mp-mailrelay-js'));
    wp_localize_script('mp-mailrelay-js', 'MeprMailrelayL10n', array('please' => '-- '.__('Please Select'.' --', 'memberpress-mailrelay')));
    wp_localize_script('mp-mailrelay-options-js', 'MeprMailrelay', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-mailrelay-js', MPMAILRELAY_URL.'/mailrelay.js');
    wp_enqueue_script('mp-mailrelay-product-js', MPMAILRELAY_URL.'/mailrelay_product.js', array('mp-mailrelay-js'));
    wp_localize_script('mp-mailrelay-js', 'MeprMailrelayL10n', array('please' => '-- '.__('Please Select'.' --', 'memberpress-mailrelay')));
  }

  public function update_user_email($errors, $contact) {
    if(!$this->is_enabled_and_authorized()) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($contact->user_email != $new_email) {
      //Update the contact record in Mailrelay if it exists
      if($this->email_exists($contact->user_email)) {
        $this->update_subscriber($contact, $new_email);
      }

      /* NO NEED TO UPDATE EACH PER-MEMBERSHIP GROUP LIKE WE DO IN THE OTHER INTEGRATIONS RIGHT HERE
         BECAUSE MAILRELAY USES ONE USER ACCOUNT ACCROSS ALL GROUPS -- SO UPDATE ONE AND DONE
         A ONE -> MANY RELATIONSHIP. */
    }

    return $errors;
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-mailrelay" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprmailrelay_enabled" id="meprmailrelay_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprmailrelay_enabled"><?php _e('Enable Mailrelay', 'memberpress-mailrelay'); ?></label>
    </div>
    <div id="mailrelay_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-mailrelay-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-mailrelay-message" class="mepr-hidden mepr-active"></div>
      <div id="meprmailrelay-domain">
        <label>
          <span><?php _e('Mailrelay Domain:', 'memberpress-mailrelay'); ?></span>
          <input type="text" name="meprmailrelay_domain" id="meprmailrelay_domain" value="<?php echo $this->domain(); ?>" class="mepr-text-input form-field" size="20" />
        </label>
        <div>
          <span class="description">
            <?php _e('This is the domain name used to access your Mailrelay control panel. Ex: username.ip-zone.com', 'memberpress-mailrelay'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprmailrelay-api-key">
        <label>
          <span><?php _e('Mailrelay API Key:', 'memberpress-mailrelay'); ?></span>
          <input type="text" name="meprmailrelay_api_key" id="meprmailrelay_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="20" />
          <span id="mepr-mailrelay-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-mailrelay-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API key under your Account settings at Mailrelay.com.', 'memberpress-mailrelay'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprmailrelay-options">
        <div id="meprmailrelay-group-id">
          <label>
            <span><?php _e('Mailrelay Group:', 'memberpress-mailrelay'); ?></span>
            <select name="meprmailrelay_group_id" id="meprmailrelay_group_id" data-groupid="<?php echo $this->group_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
        <br/>
        <div id="meprmailrelay-optin">
          <label>
            <input type="checkbox" name="meprmailrelay_optin" id="meprmailrelay_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-mailrelay'); ?></span>
          </label>
          <div>
            <span class="description">
              <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-mailrelay'); ?>
            </span>
          </div>
        </div>
        <div id="meprmailrelay-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-mailrelay'); ?>
            <input type="text" name="meprmailrelay_text" id="meprmailrelay_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing group opt-in checkbox.', 'memberpress-mailrelay'); ?></span></div>
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
    update_option('meprmailrelay_enabled',      (isset($_POST['meprmailrelay_enabled'])));
    update_option('meprmailrelay_domain',       stripslashes($_POST['meprmailrelay_domain']));
    update_option('meprmailrelay_api_key',      stripslashes($_POST['meprmailrelay_api_key']));
    update_option('meprmailrelay_optin',        (isset($_POST['meprmailrelay_optin'])));
    update_option('meprmailrelay_text',         stripslashes($_POST['meprmailrelay_text']));

    //Attempt to fix a bug Javier was having where sometimes the Group would get reset to the top option in the list (maybe a JS issue?)
    if(isset($_POST['meprmailrelay_group_id']) && $_POST['meprmailrelay_group_id'] == 'please-select') {
      return; //Do nothing here
    }
    update_option('meprmailrelay_group_id',     (isset($_POST['meprmailrelay_group_id']))?stripslashes($_POST['meprmailrelay_group_id']):false);
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership group is enabled, and the global group is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprmailrelay_group_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprmailrelay_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-mailrelay-signup-field">
          <div id="mepr-mailrelay-checkbox">
            <input type="checkbox" name="meprmailrelay_opt_in" id="meprmailrelay_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-mailrelay-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-mailrelay-privacy">
            <small>
              <a href="http://mailrelay.com/es/politica-de-privacidad" class="mepr-mailrelay-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-mailrelay'); ?></a>
            </small>
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

    $enabled = (bool)get_post_meta($prd->ID, '_meprmailrelay_group_override', true);

    //If the per membership group is enabled, and the global group is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) {
      return;
    }

    if(!$this->is_optin_enabled() or ($this->is_optin_enabled() and isset($_POST['meprmailrelay_opt_in']))) {
      $this->add_subscriber($contact, $this->group_id());
    }
  }

  public function process_status_changes($obj, $sub_status = false) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if($obj instanceof MeprTransaction && $sub_status !== false && $sub_status == MeprSubscription::$active_str) {
      return; //This is an expiring transaction which is part of an active subscription, so don't remove the contact from the group
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
    $enabled = (bool)get_post_meta($obj->product_id, '_meprmailrelay_group_override', true);
    $group_id = get_post_meta($obj->product_id, '_meprmailrelay_group_override_id', true);

    if($enabled && !empty($group_id)) {
      if(!$this->is_subscribed($contact, $group_id)) {
        return $this->add_subscriber($contact, $group_id);
      }
    }

    return false;
  }

  public function maybe_remove_subscriber($obj, $contact) {
    $enabled = (bool)get_post_meta($obj->product_id, '_meprmailrelay_group_override', true);
    $group_id = get_post_meta($obj->product_id, '_meprmailrelay_group_override_id', true);

    if($enabled && !empty($group_id)) {
      if($this->is_subscribed($contact, $group_id)) {
        return $this->remove_subscriber($contact, $group_id);
      }
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_group = (bool)get_post_meta($product->ID, '_meprmailrelay_group_override', true);
    $override_group_id = get_post_meta($product->ID, '_meprmailrelay_group_override_id', true);

    ?>
    <div id="mepr-mailrelay" class="mepr-product-adv-item">
      <input type="checkbox" name="meprmailrelay_group_override" id="meprmailrelay_group_override" data-domain="<?php echo $this->domain(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_group); ?> />
      <label for="meprmailrelay_group_override"><?php _e('Mailrelay Group for this Membership', 'memberpress-mailrelay'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprmailrelay-group-override',
                                        __('Enable Membership Mailrelay Group', 'memberpress-mailrelay'),
                                        __('If this is set the member will be added to this group when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the group automatically. You must have your Mailrelay Domain and API key set in the Options before this will work.', 'memberpress-mailrelay'));
      ?>

      <div id="meprmailrelay_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Mailrelay Group: ', 'memberpress-mailrelay'); ?></label>
        <select name="meprmailrelay_group_override_id" id="meprmailrelay_group_override_id" data-groupid="<?php echo stripslashes($override_group_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    //Attempt to fix a bug Javier was having where sometimes the Group would get reset to the top option in the list (maybe a JS issue?)
    if(isset($_POST['meprmailrelay_group_override_id']) && $_POST['meprmailrelay_group_override_id'] == 'please-select') {
      return; //Do nothing here
    }

    if(isset($_POST['meprmailrelay_group_override'])) {
      update_post_meta($product->ID, '_meprmailrelay_group_override', true);
      update_post_meta($product->ID, '_meprmailrelay_group_override_id', stripslashes($_POST['meprmailrelay_group_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprmailrelay_group_override', false);
    }
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) or !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) or !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-mailrelay'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['domain']) || !isset($_POST['apikey'])) {
      die(json_encode(array('error' => __('No Domain or API Key was sent', 'memberpress-mailrelay'), 'type' => 'memberpress')));
    }

    $domain = stripslashes($_POST['domain']);
    $apikey = stripslashes($_POST['apikey']);

    $args = array(
              'function' => 'setReturnType',
              'returnType' => 'json'
            );

    //Just seeing if our apikey result in a 200 response code and a bool true
    $resp_body = (array)json_decode($this->call($args, $domain, $apikey));

    if($resp_body === false || isset($resp_body['error'])) {
      die(json_encode(array('error' => __('Domain or API Key incorrect', 'memberpress-mailrelay'), 'type' => 'memberpress')));
    }
    else {
      die(json_encode(array('msg' => __('All set!', 'memberpress-mailrelay'), 'type' => 'memberpress')));
    }
  }

  public function ajax_get_groups() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-mailrelay'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['domain']) || !isset($_POST['apikey'])) {
      die(json_encode(array('error' => __('No Domain or API Key was sent', 'memberpress-mailrelay'), 'type' => 'memberpress')));
    }

    $domain = stripslashes($_POST['domain']);
    $apikey = stripslashes($_POST['apikey']);

    $args = array(
              'function' => 'getGroups',
              'offset' => 0,
              'count' => 100
            );

    $res = (array)json_decode($this->call($args, $domain, $apikey));

    die(json_encode($res['data']));
  }

  public function is_subscribed($contact, $group_id) {
    $args = array(
      'function'  => 'getSubscribers',
      'offset'    => 0,
      'count'     => 1,
      'email'     => $contact->user_email
    );

    $resp_body = (array)json_decode($this->call($args));

    if($resp_body === false || !isset($resp_body['data']) || count($resp_body['data']) == 0) {
      return false;
    }

    return in_array($group_id, $resp_body['data'][0]->groups);
  }

  public function email_exists($email) {
    $args = array(
      'function'  => 'getSubscribers',
      'offset'    => 0,
      'count'     => 1,
      'email'     => $email
    );

    $resp_body = (array)json_decode($this->call($args));

    if($resp_body === false || !isset($resp_body['data']) || count($resp_body['data']) == 0) {
      return false;
    }

    return (count($resp_body['data']) >= 1);
  }

  public function get_existing_groups($contact) {
    $args = array(
      'function'  => 'getSubscribers',
      'offset'    => 0,
      'count'     => 1,
      'email'     => $contact->user_email
    );

    $resp_body = (array)json_decode($this->call($args));

    if($resp_body === false || !isset($resp_body['data']) || count($resp_body['data']) == 0) {
      return array();
    }

    return $resp_body['data'][0]->groups;
  }

  public function get_existing_id($contact) {
    $args = array(
      'function'  => 'getSubscribers',
      'offset'    => 0,
      'count'     => 1,
      'email'     => $contact->user_email
    );

    $resp_body = (array)json_decode($this->call($args));

    if($resp_body === false || !isset($resp_body['data']) || count($resp_body['data']) == 0) {
      return false;
    }

    return $resp_body['data'][0]->id;
  }

  public function add_subscriber($contact, $group_id) {
    $existing_groups = $this->get_existing_groups($contact);
    $existing_id     = $this->get_existing_id($contact);

    if(!in_array($group_id, $existing_groups)) {
      $existing_groups[] = $group_id; //Add in this group
    }

    $args = array(
      'function'  => 'addSubscriber',
      'email'     => $contact->user_email,
      'name'      => (string)$contact->first_name . ' ' . (string)$contact->last_name,
      'groups'    => $existing_groups
    );

    //If the contact already exists, then we need to just update them
    if($existing_id !== false) {
      $args['function'] = 'updateSubscriber';
      $args['id']       = $existing_id;
    }

    $resp_body = (array)json_decode($this->call($args));

    return ($resp_body !== false);
  }

  public function update_subscriber($contact, $new_email) {
    $existing_groups  = $this->get_existing_groups($contact);
    $existing_id      = $this->get_existing_id($contact);

    if(!is_array($existing_groups) || $existing_id === false) { return false; }

    $args = array(
              'function'  => 'updateSubscriber',
              'id'        => $existing_id,
              'email'     => $new_email,
              'name'      => (string)$contact->first_name . ' ' . (string)$contact->last_name,
              'groups'    => $existing_groups
            );

    $resp_body = (array)json_decode($this->call($args));

    return ($resp_body !== false);
  }

  public function remove_subscriber($contact, $group_id) {
    $existing_groups  = $this->get_existing_groups($contact);
    $new_groups       = array_diff($existing_groups, array($group_id)); //tricky
    $existing_id      = $this->get_existing_id($contact);

    $args = array(
              'function'  => 'updateSubscriber',
              'id'        => $existing_id,
              'email'     => $contact->user_email,
              'name'      => (string)$contact->first_name . ' ' . (string)$contact->last_name,
              'groups'    => $new_groups
            );

    $resp_body = (array)json_decode($this->call($args));

    return ($resp_body !== false);
  }

  private function call($args, $domain = null, $apikey = null) {
    if(is_null($domain)) { $domain = $this->domain(); }
    if(is_null($apikey)) { $apikey = $this->apikey(); }

    $args['apiKey']     = $apikey;
    $args               = apply_filters('mepr-mailrelay-call-args', $args);

    $url                = "http://{$domain}/ccm/admin/api/version/2/&type=json";

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
    return get_option('meprmailrelay_enabled', false);
  }

  private function is_authorized() {
    return ($this->apikey() && $this->domain());
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function domain() {
    return get_option('meprmailrelay_domain', '');
  }

  private function apikey() {
    return get_option('meprmailrelay_api_key', '');
  }

  private function group_id() {
    return get_option('meprmailrelay_group_id', false);
  }

  private function is_optin_enabled() {
    return get_option('meprmailrelay_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-mailrelay'), get_option('blogname'));
    return get_option('meprmailrelay_text', $default);
  }
} //END CLASS
