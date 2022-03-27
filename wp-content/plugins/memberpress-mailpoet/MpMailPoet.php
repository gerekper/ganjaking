<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of MailPoet into MemberPress
*/
class MpMailPoet  {
  public function __construct() {
    add_action('mepr_display_autoresponders', array($this, 'display_option_fields'));
    add_action('mepr-process-options',        array($this, 'store_option_fields'));
    add_action('mepr-user-signup-fields',     array($this, 'display_signup_field'));

    // Signup
    add_action('mepr-signup-user-loaded', array($this, 'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_add_subscriber'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_remove_subscriber'));

    add_action('mepr-product-advanced-metabox', array($this, 'display_product_override'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_override'));

    add_filter('mepr-save-account', array($this, 'update_user'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your MailPoet integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-mailpoet');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_enqueue_script('mp-mailpoet-options-js', MPMAILPOET_URL.'/mailpoet_options.js');
    wp_localize_script('mp-mailpoet-options-js', 'MeprMailPoet', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_enqueue_script('mp-mailpoet-product-js', MPMAILPOET_URL.'/mailpoet_product.js');
  }

  public function update_user($mepr_user) {
    if(!$this->is_enabled()) { return; }

    //MailPoet does this for us, so we literally just have to call this static method
    if(!$this->is_version_three()) {
      WYSIJA::hook_edit_WP_subscriber($mepr_user->ID);
    }
    else {
      // Nothing to do here for V3
    }
  }

  public function display_option_fields() {
    $all_lists = $this->get_lists();
    $list_id = $this->list_id();

    ?>
    <div id="mepr-mailpoet" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprmailpoet_enabled" id="meprmailpoet_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprmailpoet_enabled"><?php _e('Enable MailPoet', 'memberpress-mailpoet'); ?></label>
    </div>
    <?php if($this->mailpoet_active()): ?>
      <div id="mailpoet_hidden_area" class="mepr-options-sub-pane">
        <div id="meprmailpoet-options">
          <div id="meprmailpoet-list-id">
            <label>
              <span><?php _e('MailPoet List:', 'memberpress-mailpoet'); ?></span>
              <select name="meprmailpoet_list_id" id="meprmailpoet_list_id" class="mepr-text-input form-field">
                <?php foreach($all_lists as $l): ?>
                  <option value="<?php echo $l['list_id']; ?>" <?php selected($l['list_id'], $list_id); ?>><?php echo $l['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
          <br/>
          <div id="meprmailpoet-optin">
            <label>
              <input type="checkbox" name="meprmailpoet_optin" id="meprmailpoet_optin" <?php checked($this->is_optin_enabled()); ?> />
              <span><?php _e('Enable Opt-In Checkbox', 'memberpress-mailpoet'); ?></span>
            </label>
            <div>
              <span class="description">
                <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-mailpoet'); ?>
              </span>
            </div>
          </div>
          <div id="meprmailpoet-optin-text" class="mepr-hidden mepr-options-panel">
            <label><?php _e('Signup Checkbox Label:', 'memberpress-mailpoet'); ?>
              <input type="text" name="meprmailpoet_text" id="meprmailpoet_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
            </label>
            <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-mailpoet'); ?></span></div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div id="mailpoet_hidden_area" class="mepr-options-sub-pane">
        <p><?php _e('MailPoet Plugin not found. You must install the MailPoet Plugin to use this integration.', 'memberpress-mailpoet'); ?></p>
      </div>
    <?php endif; ?>
    <?php
  }

  public function store_option_fields() {
    update_option('meprmailpoet_enabled',      (isset($_POST['meprmailpoet_enabled'])));
    update_option('meprmailpoet_list_id',      (isset($_POST['meprmailpoet_list_id']))?stripslashes($_POST['meprmailpoet_list_id']):false);
    update_option('meprmailpoet_optin',        (isset($_POST['meprmailpoet_optin'])));
    update_option('meprmailpoet_text',         (isset($_POST['meprmailpoet_text']))?stripslashes($_POST['meprmailpoet_text']):$this->optin_text());
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprmailpoet_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprmailpoet_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-mailpoet-signup-field">
          <div id="mepr-mailpoet-checkbox">
            <input type="checkbox" name="meprmailpoet_opt_in" id="meprmailpoet_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-mailpoet-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-mailpoet-privacy">
            <small>
              <?php _e('We Respect Your Privacy', 'memberpress-mailpoet'); ?>
            </small>
          </div>
        </div>
      </div>
      <?php
     }
  }

  public function process_signup($user) {
    global $wp_current_filter;
    $old_wp_current_filter = $wp_current_filter;

    $mepr_options = MeprOptions::fetch();

    $enabled = (bool)get_post_meta((int)sanitize_text_field($_POST['mepr_product_id']), '_meprmailpoet_list_override', true);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled() || ($enabled && $mepr_options->disable_global_autoresponder_list)) { return; }

    if(!$this->is_optin_enabled() || ($this->is_optin_enabled() && isset($_POST['meprmailpoet_opt_in']))) {
      $this->add_subscriber($user, $this->list_id());
    }
  }

  public function maybe_add_subscriber($txn) {
    $enabled  = (bool)get_post_meta($txn->product_id, '_meprmailpoet_list_override', true);
    $list_id  = get_post_meta($txn->product_id, '_meprmailpoet_list_override_id', true);
    $user     = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled()) {
      //If status is -1 (unsubscribed), then the user has explicitly asked for no more contact so don't re-add them
      $exists = $this->email_exists($user->user_email, true);

      if(!$exists || ($exists && $exists['status'] >= 0 && !$this->exists_in_list($exists['user_id'], $list_id, $user->user_email))) {
        return $this->add_subscriber($user, $list_id);
      }
    }

    return false;
  }

  public function maybe_remove_subscriber($txn) {
    $enabled  = (bool)get_post_meta($txn->product_id, '_meprmailpoet_list_override', true);
    $list_id  = get_post_meta($txn->product_id, '_meprmailpoet_list_override_id', true);
    $user     = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled()) {
      return $this->remove_subscriber($user, $list_id);
    }

    return false;
  }

  public function display_product_override($product) {
    if(!$this->is_enabled()) { return; }

    $all_lists = $this->get_lists();
    $override_list = (bool)get_post_meta($product->ID, '_meprmailpoet_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprmailpoet_list_override_id', true);

    ?>
    <div id="mepr-mailpoet" class="mepr-product-adv-item">
      <input type="checkbox" name="meprmailpoet_list_override" id="meprmailpoet_list_override" <?php checked($override_list); ?> />
      <label for="meprmailpoet_list_override"><?php _e('MailPoet list for this Membership', 'memberpress-mailpoet'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprmailpoet-list-override',
                                        __('Enable Membership MailPoet List', 'memberpress-mailpoet'),
                                        __('If this is set the member will be added to this list when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the list automatically.', 'memberpress-mailpoet'));
      ?>

      <div id="meprmailpoet_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('MailPoet List: ', 'memberpress-mailpoet'); ?></label>
        <select name="meprmailpoet_list_override_id" id="meprmailpoet_list_override_id" class="mepr-text-input form-field">
          <?php foreach($all_lists as $l): ?>
            <option value="<?php echo $l['list_id']; ?>" <?php selected($l['list_id'], $override_list_id); ?>><?php echo $l['name']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled()) { return; }

    if(isset($_POST['meprmailpoet_list_override'])) {
      update_post_meta($product->ID, '_meprmailpoet_list_override', true);
      update_post_meta($product->ID, '_meprmailpoet_list_override_id', stripslashes($_POST['meprmailpoet_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprmailpoet_list_override', false);
    }
  }

  public function get_lists() {
    if(!$this->mailpoet_active()) { return array(); }
    $lists = array();

    //This will return an array of results with the name and list_id of each mailing list
    if(!$this->is_version_three()) {
      $model_list = WYSIJA::get('list', 'model');
      $lists = $model_list->get(array('name', 'list_id'), array('is_enabled' => 1));
    } else {
      try {
        $lists = \MailPoet\API\API::MP('v1')->getLists();
      }
      catch (Exception $e) {
        return array();
      }

      foreach($lists as $i => $data) {
        $lists[$i]['list_id'] = $data['id']; //Add missing list_id map for backwards compat
      }
    }

    return (empty($lists)) ? array() : $lists;
  }

  public function email_exists($email, $add_status = false) {
    if($this->is_version_three()) {
      return $this->email_exists_version_three($email, $add_status);
    }

    $model_user = WYSIJA::get('user', 'model');
    $model_user->getFormat = ARRAY_A;

    $exists = $model_user->getOne(array('user_id', 'status'), array('email' => $email));

    if($exists) {
      if($add_status) {
        return $exists;
      }
      else {
        return $exists['user_id'];
      }
    }

    return false;
  }

  public function email_exists_version_three($email, $add_status = false) {
    try {
      $subscriber_data = \MailPoet\API\API::MP('v1')->getSubscriber($email);
    }
    catch (Exception $e) {
      return false;
    }

    if($subscriber_data) {
      if($add_status) {
        $mapped_status = ($subscriber_data['status'] == 'unsubscribed') ? -1 : 1;
        return array('status' => $subscriber_data['status'], 'user_id' => $subscriber_data['id']);
      }
      else {
        return $subscriber_data['id'];
      }
    }

    return false;
  }

  //user_id is NOT a WP_User ID, it's MailPoet user id
  public function exists_in_list($user_id, $list_id, $email) {
    if($this->is_version_three()) {
      return $this->exists_in_list_version_three($user_id, $list_id, $email);
    }

    global $wpdb;

    $exists_in_list = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wysija_user_list WHERE user_id = {$user_id} AND list_id = {$list_id} AND unsub_date <= 0");

    return ($exists_in_list > 0);
  }

  public function exists_in_list_version_three($user_id, $list_id, $email) {
    try {
      $subscriber_data = \MailPoet\API\API::MP('v1')->getSubscriber($email);
    }
    catch (Exception $e) {
      return false;
    }

    if($subscriber_data && isset($subscriber_data['subscriptions'])) {
      foreach($subscriber_data['subscriptions'] as $sub) {
        if($sub['segment_id'] == $list_id && $sub['status'] != 'unsubscribed') {
          return true;
        }
      }
    }

    return false;
  }

  public function add_subscriber(MeprUser $contact, $list_id) {
    $user_data = array(
      'email'     => $contact->user_email,
      'firstname' => $contact->first_name,
      'lastname'  => $contact->last_name
    );

    $data_subscriber = array(
      'user' => $user_data,
      'user_list' => array(
        'list_ids' => array($list_id)
      )
    );

    if(!$this->is_version_three()) {
      $helper_user = WYSIJA::get('user', 'helper');
      $helper_user->addSubscriber($data_subscriber);
    }
    else {
      //Some mapping from 2.0 to 3.0
      $user_data['first_name'] = $user_data['firstname'];
      $user_data['last_name'] = $user_data['lastname'];
      unset($user_data['firstname']);
      unset($user_data['lastname']);

      $exists = $this->email_exists($user_data['email']); //The user "should" ALWAYS exist by this point but just in case

      if($exists !== false) {
        try {
          $request_args = apply_filters('mepr-mailpoet-subscribe-args', array('send_confirmation_email' => false));

          \MailPoet\API\API::MP('v1')->subscribeToList($user_data['email'], $list_id, $request_args);
        }
        catch (Exception $e) {
          return false;
        }
      }
    }

    return true;
  }

  public function remove_subscriber(MeprUser $contact, $list_id) {
    if(($user_id = $this->email_exists($contact->user_email)) !== false) {
      if(!$this->is_version_three()) {
        $helper_user = WYSIJA::get('user', 'helper');
        $helper_user->removeFromLists(array($list_id), array($user_id));
      }
      else {
        try {
          \MailPoet\API\API::MP('v1')->unsubscribeFromList($user_id, $list_id);
        }
        catch (Exception $e) {
          return false;
        }
      }

      return true;
    }

    return false;
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return ($this->mailpoet_active() && get_option('meprmailpoet_enabled', false));
  }

  private function mailpoet_active() {
    return (class_exists('WYSIJA') || class_exists('\\MailPoet\\API\\API'));
  }

  private function is_version_three() {
    return class_exists('\\MailPoet\\API\\API');
  }

  private function list_id() {
    return get_option('meprmailpoet_list_id', false);
  }

  private function is_optin_enabled() {
    return get_option('meprmailpoet_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-mailpoet'), get_option('blogname'));
    return get_option('meprmailpoet_text', $default);
  }
} //END CLASS
