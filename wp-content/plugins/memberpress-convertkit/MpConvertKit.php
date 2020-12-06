<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of ConvertKit into MemberPress
*/
class MpConvertKit {
  public function __construct() {
    // Storing fields
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field'));
    add_action('mepr-product-advanced-metabox', array($this, 'display_product_override'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_override'));
    add_filter('mepr-validate-account',         array($this, 'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Signup
    add_action('mepr-signup-user-loaded', array($this, 'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_add_tag_to_subscriber'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_remove_tag_from_subscriber'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_convertkit_ping_api_secret', array($this, 'ajax_ping_api_secret'));
    add_action('wp_ajax_mepr_convertkit_get_tags',        array($this, 'ajax_get_tags'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your ConvertKit integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-convertkit');
      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-convertkit-js', MPCONVERTKIT_URL.'/convertkit.js');
    wp_enqueue_script('mp-convertkit-options-js', MPCONVERTKIT_URL.'/convertkit_options.js', array('mp-convertkit-js'));
    wp_localize_script('mp-convertkit-options-js', 'MeprConvertKit', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-convertkit-js', MPCONVERTKIT_URL.'/convertkit.js');
    wp_enqueue_script('mp-convertkit-product-js', MPCONVERTKIT_URL.'/convertkit_product.js', array('mp-convertkit-js'));
  }

  public function update_user_email($errors, $contact) {
    if(!$this->is_enabled_and_authorized() || !empty($errors)) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($contact->user_email != $new_email) {
      //Update the contact record in ConvertKit if it exists
      $this->update_subscriber($contact, $new_email);
    }

    return $errors;
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-convertkit" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprconvertkit_enabled" id="meprconvertkit_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprconvertkit_enabled"><?php _e('Enable ConvertKit', 'memberpress-convertkit'); ?></label>
    </div>
    <div id="convertkit_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-convertkit-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-convertkit-message" class="mepr-hidden mepr-active"></div>
      <div id="meprconvertkit-api-secret">
        <label>
          <span><?php _e('ConvertKit API Secret:', 'memberpress-convertkit'); ?></span>
          <input type="text" name="meprconvertkit_api_secret" id="meprconvertkit_api_secret" value="<?php echo $this->api_secret(); ?>" class="mepr-text-input form-field" size="20" />
          <span id="mepr-convertkit-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-convertkit-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API Secret under your Account settings at ConvertKit.com.', 'memberpress-convertkit'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprconvertkit-options">
        <div id="meprconvertkit-tag-id">
          <label>
            <span><?php _e('ConvertKit Tag:', 'memberpress-convertkit'); ?></span>
            <select name="meprconvertkit_tag_id" id="meprconvertkit_tag_id" data-tagid="<?php echo $this->tag_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
        <br/>
        <div id="meprconvertkit-optin">
          <label>
            <input type="checkbox" name="meprconvertkit_optin" id="meprconvertkit_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-convertkit'); ?></span>
          </label>
          <div>
            <span class="description">
              <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-convertkit'); ?>
            </span>
          </div>
        </div>
        <div id="meprconvertkit-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-convertkit'); ?>
            <input type="text" name="meprconvertkit_text" id="meprconvertkit_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing tag opt-in checkbox.', 'memberpress-convertkit'); ?></span></div>
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
    update_option('meprconvertkit_enabled',     (isset($_POST['meprconvertkit_enabled'])));
    update_option('meprconvertkit_api_secret',  stripslashes($_POST['meprconvertkit_api_secret']));
    update_option('meprconvertkit_tag_id',      (isset($_POST['meprconvertkit_tag_id']))?stripslashes($_POST['meprconvertkit_tag_id']):false);
    update_option('meprconvertkit_optin',       (isset($_POST['meprconvertkit_optin'])));
    update_option('meprconvertkit_text',        stripslashes($_POST['meprconvertkit_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership tag is enabled, and the global tag is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprconvertkit_tag_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprconvertkit_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-convertkit-signup-field">
          <div id="mepr-convertkit-checkbox">
            <input type="checkbox" name="meprconvertkit_opt_in" id="meprconvertkit_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-convertkit-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-convertkit-privacy">
            <small>
              <a href="http://convertkit.com/privacy/" class="mepr-convertkit-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-convertkit'); ?></a>
            </small>
          </div>
        </div>
      </div>
      <?php
     }
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_tag             = (bool)get_post_meta($product->ID, '_meprconvertkit_tag_override', true);
    $override_active_tag_id   = get_post_meta($product->ID, '_meprconvertkit_tag_override_id', true); //Active tag
    $override_inactive_tag_id = get_post_meta($product->ID, '_meprconvertkit_inactive_tag_override_id', true);

    ?>
    <div id="mepr-convertkit" class="mepr-product-adv-item">
      <input type="checkbox" name="meprconvertkit_tag_override" id="meprconvertkit_tag_override" data-api_secret="<?php echo $this->api_secret(); ?>" <?php checked($override_tag); ?> />
      <label for="meprconvertkit_tag_override"><?php _e('ConvertKit Tag for this Membership', 'memberpress-convertkit'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprconvertkit-tag-override',
                                        __('Enable Membership ConvertKit Tag', 'memberpress-convertkit'),
                                        __('The user will have the Active Tag on their subscriber account at ConvertKit when they are active on this Membership. They will have the Inactive Tag set in their subscriber account when they are inactive on this Membership. You must have your ConvertKit API Secret set in the Options before this will work.', 'memberpress-convertkit'));
      ?>

      <div id="meprconvertkit_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Active ConvertKit Tag: ', 'memberpress-convertkit'); ?></label>
        <select name="meprconvertkit_active_tag_override_id" id="meprconvertkit_active_tag_override_id" data-tagid="<?php echo stripslashes($override_active_tag_id); ?>" class="mepr-text-input form-field"></select>
        <br/>
        <label><?php _e('Inactive ConvertKit Tag: ', 'memberpress-convertkit'); ?></label>
        <select name="meprconvertkit_inactive_tag_override_id" id="meprconvertkit_inactive_tag_override_id" data-tagid="<?php echo stripslashes($override_inactive_tag_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprconvertkit_tag_override'])) {
      update_post_meta($product->ID, '_meprconvertkit_tag_override', true);
      update_post_meta($product->ID, '_meprconvertkit_tag_override_id', stripslashes($_POST['meprconvertkit_active_tag_override_id'])); //Active tag
      update_post_meta($product->ID, '_meprconvertkit_inactive_tag_override_id', stripslashes($_POST['meprconvertkit_inactive_tag_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprconvertkit_tag_override', false);
    }
  }

  public function process_signup($user) {
    $mepr_options = MeprOptions::fetch();
    $enabled = (bool)get_post_meta((int)sanitize_text_field($_POST['mepr_product_id']), '_meprconvertkit_tag_override', true);
    //If the per membership tag is enabled, and the global tag is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) {
      return;
    }

    if(!$this->is_optin_enabled() || ($this->is_optin_enabled() && isset($_POST['meprconvertkit_opt_in']))) {
      $this->add_tag_to_subscriber($user, $this->tag_id());
    }
  }

  public function maybe_add_tag_to_subscriber($txn) {
    $contact          = $txn->user();
    $enabled          = (bool)get_post_meta($txn->product_id, '_meprconvertkit_tag_override', true);
    $active_tag_id    = get_post_meta($txn->product_id, '_meprconvertkit_tag_override_id', true);
    $inactive_tag_id  = get_post_meta($txn->product_id, '_meprconvertkit_inactive_tag_override_id', true);

    //Add Active tag
    if($enabled && !empty($active_tag_id)) {
      $this->add_tag_to_subscriber($contact, $active_tag_id);
    }

    //Remove Inactive tag only if it's not the same as the Active tag
    if($enabled && !empty($inactive_tag_id) && $inactive_tag_id !== $active_tag_id) {
      $this->remove_tag_from_subscriber($contact, $inactive_tag_id);
    }

    return false;
  }

  public function maybe_remove_tag_from_subscriber($txn) {
    $contact          = $txn->user();
    $enabled          = (bool)get_post_meta($txn->product_id, '_meprconvertkit_tag_override', true);
    $active_tag_id    = get_post_meta($txn->product_id, '_meprconvertkit_tag_override_id', true);
    $inactive_tag_id  = get_post_meta($txn->product_id, '_meprconvertkit_inactive_tag_override_id', true);

    //Remove Active tag
    if($enabled && !empty($active_tag_id)) {
      $this->remove_tag_from_subscriber($contact, $active_tag_id);
    }

    //Add Inactive tag
    if($enabled && !empty($inactive_tag_id)) {
      $this->add_tag_to_subscriber($contact, $inactive_tag_id);
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function ajax_ping_api_secret() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-convertkit'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['api_secret'])) {
      die(json_encode(array('error' => __('No API Secret was sent', 'memberpress-convertkit'), 'type' => 'memberpress')));
    }

    $api_secret = stripslashes($_POST['api_secret']);

    //Just seeing if our api_secret results in a 200 response code and a bool true
    $resp_body = (array)json_decode($this->call(array(), 'tags', $api_secret));

    if($resp_body === false || empty($resp_body) || isset($resp_body['error'])) {
      die(json_encode(array('error' => __('API Secret incorrect', 'memberpress-convertkit'), 'type' => 'memberpress')));
    }
    else {
      die(json_encode(array('msg' => __('All set!', 'memberpress-convertkit'), 'type' => 'memberpress')));
    }
  }

  public function ajax_get_tags() {
    static $res; //Save on multiple hits per request

    if(isset($res['tags'])) {
      die(json_encode($res['tags']));
    }

    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-convertkit'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['api_secret'])) {
      die(json_encode(array('error' => __('No API Secret was sent', 'memberpress-convertkit'), 'type' => 'memberpress')));
    }

    $api_secret = stripslashes($_POST['api_secret']);

    $res = (array)json_decode($this->call(array(), 'tags', $api_secret));

    if(isset($res['tags'])) {
      die(json_encode($res['tags']));
    }

    die(''); //Silence
  }

  public function add_tag_to_subscriber($contact, $tag_id) {
    $args = array(
      'email'       => $contact->user_email,
      'first_name'  => (string)$contact->first_name
    );

    $resp_body = (array)json_decode($this->call($args, "tags/{$tag_id}/subscribe", null, 'POST'));

    return (!empty($resp_body));
  }

  public function remove_tag_from_subscriber($contact, $tag_id) {
    $id = $this->get_subscriber_id_by_email($contact);

    if(!$id) { return; } //Nada found?

    $args = array(); //No args

    $resp_body = (array)json_decode($this->call($args, "subscribers/{$id}/tags/{$tag_id}", null, 'DELETE'));

    return true;
  }

  public function update_subscriber($contact, $new_email = '') {
    $id = $this->get_subscriber_id_by_email($contact);

    if(!$id) { return; } //Nada found?

    $args = array(
      'email_address' => (!empty($new_email) && is_email($new_email))?$new_email:$contact->user_email,
      'first_name'    => (string)$contact->first_name
    );

    $resp_body = (array)json_decode($this->call($args, "subscribers/{$id}", null, 'PUT'));

    return true;
  }

  public function get_subscriber_id_by_email($contact) {
    $args = array(
      'email_address' => $contact->user_email
    );

    $resp_body = (array)json_decode($this->call($args, "subscribers"));

    if($resp_body && isset($resp_body['total_subscribers']) && (int)$resp_body['total_subscribers'] >= 1) {
      //Just grab the first one on the list, shouldn't be more than one anyways right?
      return $resp_body['subscribers'][0]->id;
    }

    return false;
  }

  private function call($args, $endpoint, $api_secret = null, $method = 'GET') {
    if(is_null($api_secret)) { $api_secret = $this->api_secret(); }

    $url                = "https://api.convertkit.com/v3/{$endpoint}?api_secret={$api_secret}";

    $args               = apply_filters('mepr-convertkit-call-args', $args);

    $wp_args            = array('body' => $args);
    $wp_args['method']  = $method;
    $wp_args['timeout'] = 30;

    $res = wp_remote_request($url, $wp_args);

    if(!is_wp_error($res) && ($res['response']['code'] == 200 || $res['response']['code'] == 201)) {
      return $res['body'];
    }
    else {
      return false;
    }
  }

  private function is_enabled() {
    return get_option('meprconvertkit_enabled', false);
  }

  private function is_authorized() {
    return ($this->api_secret());
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function api_secret() {
    return get_option('meprconvertkit_api_secret', '');
  }

  private function tag_id() {
    return get_option('meprconvertkit_tag_id', false);
  }

  private function is_optin_enabled() {
    return get_option('meprconvertkit_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-convertkit'), get_option('blogname'));
    return get_option('meprconvertkit_text', $default);
  }
} //END CLASS
