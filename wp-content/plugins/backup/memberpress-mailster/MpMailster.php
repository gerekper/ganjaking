<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of Mailster into MemberPress
*/
class MpMailster  {
  public function __construct() {
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field'));
    add_action('mepr-product-advanced-metabox', array($this, 'display_product_override'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_override'));

    // Signup
    add_action('mepr-signup', array($this, 'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_add_subscriber_to_list'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_remove_subscriber_from_list'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your Mailster integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-mailster');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_enqueue_script('mp-mailster-options-js', MPMAILSTER_URL.'/mailster_options.js');
    wp_localize_script('mp-mailster-options-js', 'MeprMailster', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_enqueue_script('mp-mailster-product-js', MPMAILSTER_URL.'/mailster_product.js');
  }

  public function display_option_fields() {
    $all_lists  = $this->get_lists();
    $list_id    = $this->list_id();

    ?>
    <div id="mepr-mailster" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprmailster_enabled" id="meprmailster_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprmailster_enabled"><?php _e('Enable Mailster', 'memberpress-mailster', 'memberpress-mailpoet'); ?></label>
    </div>
    <?php if($this->mailster_active()): ?>
      <?php if(!$this->mailster_adding_subscribers()): //Mailster is already syncing with user_register -- so nothing to do here ?>
        <div id="mailster_hidden_area" class="mepr-options-sub-pane">
          <div id="meprmailster-options">
            <div id="meprmailster-list-id">
              <label>
                <span><?php _e('Global Mailster List:', 'memberpress-mailster', 'memberpress-mailpoet'); ?></span>
                <select name="meprmailster_list_id" id="meprmailster_list_id" class="mepr-text-input form-field">
                  <?php foreach($all_lists as $l): ?>
                    <option value="<?php echo $l->ID; ?>" <?php selected($l->ID, $list_id); ?>><?php echo stripslashes($l->name); ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>
            <br/>
            <div id="meprmailster-double-optin">
              <label>
                <input type="checkbox" name="meprmailster_double_optin" id="meprmailster_double_optin" <?php checked($this->is_double_optin_enabled()); ?> />
                <span><?php _e('Enable Double Opt-In Email', 'memberpress-mailster', 'memberpress-mailpoet'); ?></span>
              </label>
              <div>
                <span class="description">
                  <?php _e('Member must verify their email address before being subscribed.', 'memberpress-mailster', 'memberpress-mailpoet'); ?>
                </span>
              </div>
            </div>
            <br/>
            <div id="meprmailster-optin">
              <label>
                <input type="checkbox" name="meprmailster_optin" id="meprmailster_optin" <?php checked($this->is_optin_enabled()); ?> />
                <span><?php _e('Enable Opt-In Checkbox', 'memberpress-mailster', 'memberpress-mailpoet'); ?></span>
              </label>
              <div>
                <span class="description">
                  <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-mailster', 'memberpress-mailpoet'); ?>
                </span>
              </div>
            </div>
            <div id="meprmailster-optin-text" class="mepr-hidden mepr-options-panel">
              <label><?php _e('Signup Checkbox Label:', 'memberpress-mailster', 'memberpress-mailpoet'); ?>
                <input type="text" name="meprmailster_text" id="meprmailster_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
              </label>
              <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-mailster', 'memberpress-mailpoet'); ?></span></div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div id="mailster_hidden_area" class="mepr-options-sub-pane">
          <p><?php _e('Nothing to configure here as Mailster is already configured to "Add people who are added via the backend or any third party plugin". You can still configure per-membership lists though. Just be sure to save the MemberPress options with the "Enable Mailster" box checked.', 'memberpress-mailster', 'memberpress-mailpoet'); ?>
          <br/><br/>
          <p><?php printf(__('To configure the Global list settings, either %sdisable that setting%s in Mailster, or make sure none of your per-membership lists are selected there -- otherwise you may have issues with users getting added to lists they should not be on.', 'memberpress-mailster', 'memberpress-mailpoet'), '<a href="'.admin_url('edit.php?post_type=newsletter&page=mailster_settings#wordpress-users').'">', '</a>'); ?></p>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div id="mailster_hidden_area" class="mepr-options-sub-pane">
        <p><?php printf(__('Mailster Plugin not found. You must install the %sMailster Plugin%s to use this integration.', 'memberpress-mailster', 'memberpress-mailpoet'), '<a href="https://mailster.co/" target="_blank">', '</a>'); ?></p>
      </div>
    <?php endif; ?>
    <?php
  }

  public function store_option_fields() {
    if(!$this->mailster_adding_subscribers()) { //if mailster is handling user_register - let's not worry about these
      update_option('meprmailster_enabled',      (isset($_POST['meprmailster_enabled'])));
      update_option('meprmailster_list_id',      (isset($_POST['meprmailster_list_id']))?stripslashes($_POST['meprmailster_list_id']):false);
      update_option('meprmailster_double_optin', (isset($_POST['meprmailster_double_optin'])));
      update_option('meprmailster_optin',        (isset($_POST['meprmailster_optin'])));
      update_option('meprmailster_text',         (isset($_POST['meprmailster_text']))?stripslashes($_POST['meprmailster_text']):$this->optin_text());
    }
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprmailster_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprmailster_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-mailster-signup-field">
          <div id="mepr-mailster-checkbox">
            <input type="checkbox" name="meprmailster_opt_in" id="meprmailster_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-mailster-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-mailster-privacy">
            <small>
              <?php _e('We Respect Your Privacy', 'memberpress-mailster', 'memberpress-mailpoet'); ?>
            </small>
          </div>
        </div>
      </div>
      <?php
     }
  }

  public function process_signup($txn) {
    if(!$this->is_enabled()) { return; }
    if($this->mailster_adding_subscribers()) { return; } //If mailster is syncing with user_register - we don't need to be here

    $mepr_options = MeprOptions::fetch();
    $prd          = $txn->product();
    $user         = $txn->user();
    $enabled      = (bool)get_post_meta($prd->ID, '_meprmailster_list_override', true);
    $status       = ($this->is_double_optin_enabled())?0:1; //0=pending - 1=subscribed

    //First add the subscriber
    mailster('subscribers')->add_from_wp_user($user->ID, array('status' => $status)); //will bail if the subscriber already exists

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added to the global list
    if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }

    if(!$this->is_optin_enabled() || ($this->is_optin_enabled() and isset($_POST['meprmailster_opt_in']))) {
      $subscriber_id = $this->get_mailster_ID($user);
      $this->add_subscriber_to_list($subscriber_id, $this->list_id());
    }
  }

  public function maybe_add_subscriber_to_list($txn) {
    $enabled = (bool)get_post_meta($txn->product_id, '_meprmailster_list_override', true);
    $list_id = get_post_meta($txn->product_id, '_meprmailster_list_override_id', true);
    $user    = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled()) {
      $subscriber_id = $this->get_mailster_ID($user);
      return $this->add_subscriber_to_list($subscriber_id, $list_id);
    }

    return false;
  }

  public function maybe_remove_subscriber_from_list($txn) {
    $enabled = (bool)get_post_meta($txn->product_id, '_meprmailster_list_override', true);
    $list_id = get_post_meta($txn->product_id, '_meprmailster_list_override_id', true);
    $user    = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled()) {
      $subscriber_id = $this->get_mailster_ID($user);
      return $this->remove_subscriber_from_list($subscriber_id, $list_id);
    }

    return false;
  }

  public function display_product_override($product) {
    if(!$this->is_enabled()) { return; }

    $all_lists = $this->get_lists();
    $override_list = (bool)get_post_meta($product->ID, '_meprmailster_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprmailster_list_override_id', true);

    ?>
    <div id="mepr-mailster" class="mepr-product-adv-item">
      <input type="checkbox" name="meprmailster_list_override" id="meprmailster_list_override" <?php checked($override_list); ?> />
      <label for="meprmailster_list_override"><?php _e('Mailster list for this Membership', 'memberpress-mailster', 'memberpress-mailpoet'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprmailster-list-override',
                                        __('Enable Membership Mailster List', 'memberpress-mailster', 'memberpress-mailpoet'),
                                        __('If this is set the member will be added to this list when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the list automatically.', 'memberpress-mailster', 'memberpress-mailpoet'));
      ?>

      <div id="meprmailster_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Mailster List: ', 'memberpress-mailster', 'memberpress-mailpoet'); ?></label>
        <select name="meprmailster_list_override_id" id="meprmailster_list_override_id" class="mepr-text-input form-field">
          <?php foreach($all_lists as $l): ?>
            <option value="<?php echo $l->ID; ?>" <?php selected($l->ID, $override_list_id); ?>><?php echo stripslashes($l->name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled()) { return; }

    if(isset($_POST['meprmailster_list_override'])) {
      update_post_meta($product->ID, '_meprmailster_list_override', true);
      update_post_meta($product->ID, '_meprmailster_list_override_id', stripslashes($_POST['meprmailster_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprmailster_list_override', false);
    }
  }

  public function get_lists() {
    if(!$this->mailster_active()) { return array(); }

    return mailster('lists')->get();
  }

  public function add_subscriber_to_list($subscriber_id, $list_id) {
    if($subscriber_id) {
      mailster('subscribers')->assign_lists($subscriber_id, $list_id);
      return true;
    }

    return true;
  }

  public function remove_subscriber_from_list($subscriber_id, $list_id) {
    if($subscriber_id) {
      mailster('subscribers')->unassign_lists($subscriber_id, $list_id);
      return true;
    }

    return false;
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function get_mailster_ID($user) {
    $subscriber = mailster('subscribers')->get_by_wpid($user->ID);
    if(isset($subscriber->ID) && (int)$subscriber->ID > 0) {
      return $subscriber->ID;
    }

    return false;
  }

  private function is_enabled() {
    return ($this->mailster_active() && get_option('meprmailster_enabled', false));
  }

  private function mailster_active() {
    return function_exists('mailster');
  }

  private function mailster_adding_subscribers() {
    if(function_exists('mailster_option')) {
      return mailster_option('register_other');
    }

    return false;
  }

  private function list_id() {
    return get_option('meprmailster_list_id', false);
  }

  private function is_optin_enabled() {
    return get_option('meprmailster_optin', true);
  }

  private function is_double_optin_enabled() {
    return get_option('meprmailster_double_optin', false);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-mailster', 'memberpress-mailpoet'), get_option('blogname'));
    return get_option('meprmailster_text', $default);
  }
} //END CLASS
