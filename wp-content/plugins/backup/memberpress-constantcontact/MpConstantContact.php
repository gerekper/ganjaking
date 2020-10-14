<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of Constant Contact into MemberPress
*/
// require the Constant Contact  lib autoloader
require_once(MPCONSTANTCONTACT_PATH.'/vendor/Ctct/autoload.php');

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Exceptions\CtctException;

class MpConstantContact {
  public function __construct() {
    add_action('mepr_display_autoresponders',   array($this, 'display_option_fields'));
    add_action('mepr-process-options',          array($this, 'store_option_fields'));
    add_action('mepr-user-signup-fields',       array($this, 'display_signup_field'));

    // Signup
    add_action('mepr-process-signup',           array($this, 'process_signup'), 10, 4);

    // Updating tags
    add_action('mepr-account-is-active',   array($this, 'maybe_add_subscriber'));
    add_action('mepr-account-is-inactive', array($this, 'maybe_delete_subscriber'));

    add_action('mepr-product-advanced-metabox', array($this, 'display_product_override'));
    add_action('mepr-product-save-meta',        array($this, 'save_product_override'));
    add_filter('mepr-validate-account',         array($this, 'update_user_email'), 10, 2); //Need to use this hook to get old and new emails

    // Enqueue scripts
    add_action('mepr-options-admin-enqueue-script', array($this, 'admin_enqueue_options_scripts'));
    add_action('mepr-product-admin-enqueue-script', array($this, 'admin_enqueue_product_scripts'));

    // AJAX Endpoints
    add_action('wp_ajax_mepr_constantcontact_ping_apikey', array($this, 'ajax_ping_apikey'));
    add_action('wp_ajax_mepr_constantcontact_get_lists',   array($this, 'ajax_get_lists'));

    // Admin notices
    add_action('admin_notices', array($this, 'maybe_admin_notice'), 3);
  }

  public function maybe_admin_notice() {
    if(defined('MEPR_VERSION') && version_compare(MEPR_VERSION, '1.7.3', '<')) {
      $class = 'notice notice-error';
      $message = __('Your ConstantContact integration with MemberPress may be broken. Please update MemberPress to version 1.7.3 or newer to fix this issue.', 'memberpress', 'memberpress-constantcontact');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
  }

  public function admin_enqueue_options_scripts($hook) {
    wp_register_script('mp-constantcontact-js', MPCONSTANTCONTACT_URL.'/constantcontact.js');
    wp_enqueue_script('mp-constantcontact-options-js', MPCONSTANTCONTACT_URL.'/constantcontact_options.js', array('mp-constantcontact-js'));
    wp_localize_script('mp-constantcontact-options-js', 'MeprConstantContact', array('wpnonce' => wp_create_nonce(MEPR_PLUGIN_SLUG)));
  }

  public function admin_enqueue_product_scripts($hook) {
    wp_register_script('mp-constantcontact-js', MPCONSTANTCONTACT_URL.'/constantcontact.js');
    wp_enqueue_script('mp-constantcontact-product-js', MPCONSTANTCONTACT_URL.'/constantcontact_product.js', array('mp-constantcontact-js'));
  }

  public function update_user_email($errors, $mepr_user) {
    if( !$this->is_enabled_and_authorized() || !empty($errors) ) { return $errors; }

    //Check if the email is even changing before we do anything else
    $new_email = stripslashes($_POST['user_email']);

    if( $mepr_user->user_email != $new_email ) {
      $this->update_subscriber($mepr_user, $new_email);
    }

    return $errors;
  }

  public function update_subscriber(MeprUser $contact, $new_email) {
    $email = $contact->user_email;

    $args = array(
      'email' => $email,
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
    <div id="mepr-constantcontact" class="mepr-autoresponder-config">
      <input type="checkbox" name="meprconstantcontact_enabled" id="meprconstantcontact_enabled" <?php checked($this->is_enabled()); ?> />
      <label for="meprconstantcontact_enabled"><?php _e('Enable Constant Contact', 'memberpress-constantcontact'); ?></label>
    </div>
    <div id="constantcontact_hidden_area" class="mepr-options-sub-pane">
      <div id="mepr-constantcontact-error" class="mepr-hidden mepr-inactive"></div>
      <div id="mepr-constantcontact-message" class="mepr-hidden mepr-active"></div>
      <div id="meprconstantcontact-api-key">
        <label>
          <span><?php _e('Constant Contact API Key ', 'memberpress-constantcontact'); ?></span>

          <?php MeprAppHelper::info_tooltip('meprconstantcontact-api-key',
            __('Constant Contact API Key and Access Token', 'memberpress-constantcontact'),
            __('In order to integrate Constant Contact with your application , you need to get a Constant Contact API key along with an access token. <br/>An API key is required to build an application that integrates with Constant Contact using Constant Contact API. For more details, please visit below link address.', 'memberpress-constantcontact'));
          ?>

          <input type="text" name="meprconstantcontact_api_key" id="meprconstantcontact_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="50" />
          <span id="mepr-constantcontact-valid" class="mepr-active mepr-hidden"></span>
          <span id="mepr-constantcontact-invalid" class="mepr-inactive mepr-hidden"></span>
        </label>
        <div>
        <span class="description">
        <?php _e('You can get your API key at <a href="https://developer.constantcontact.com/api-keys.html" target="_blank">https://developer.constantcontact.com/api-keys.html</a>', 'memberpress-constantcontact'); ?>
        </span>
        </div>
      </div>
      <br/>
      <div id="meprconstantcontact-access-token">
        <label>
          <span><?php _e('Constant Contact Access Token', 'memberpress-constantcontact'); ?></span>
          <?php MeprAppHelper::info_tooltip('meprconstantcontact-access-token',
            __('Constant Contact Access Token', 'memberpress-constantcontact'),
            __('In order for an application to access the Constant Contact API, it needs to have a valid access token. The token is generated when a Constant Contact customer grants the application access to their account.', 'memberpress-constantcontact'));
          ?>
          <input type="text" name="meprconstantcontact_access_token" id="meprconstantcontact_access_token" value="<?php echo $this->access_token(); ?>" class="mepr-text-input form-field" size="50" />
        </label>
        <div>
        <span class="description">
        </span>
        </div>
      </div>
      <br/>
      <div id="meprconstantcontact-options">
        <div id="meprconstantcontact-list-id">
          <label>
            <span><?php _e('Constant Contact Email List:', 'memberpress-constantcontact'); ?></span>
            <select name="meprconstantcontact_list_id" id="meprconstantcontact_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
          </label>
        </div>
        <br/>
<!--        <div id="meprconstantcontact-double-optin">
          <label for="meprconstantcontact_double_optin">
            <input type="checkbox" name="meprconstantcontact_double_optin" id="meprconstantcontact_double_optin" class="form-field" <?php /*checked($this->is_double_optin_enabled()); */?> />
            <span><?php /*_e('Enable Double Opt-in', 'memberpress'); */?></span>
          </label>
          <br/>
            <span class="description">
            <?php /*_e("Members will have to click a confirmation link in an email before being added to your list.", 'memberpress'); */?>
            </span>
        </div>
        <br/>-->
        <div id="meprconstantcontact-optin">
          <label>
            <input type="checkbox" name="meprconstantcontact_optin" id="meprconstantcontact_optin" <?php checked($this->is_optin_enabled()); ?> />
            <span><?php _e('Enable Opt-In Checkbox', 'memberpress-constantcontact'); ?></span>
          </label>
          <div>
      <span class="description">
        <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration pages.', 'memberpress-constantcontact'); ?>
      </span>
          </div>
        </div>
        <div id="meprconstantcontact-optin-text" class="mepr-hidden mepr-options-panel">
          <label><?php _e('Signup Checkbox Label:', 'memberpress-constantcontact'); ?>
            <input type="text" name="meprconstantcontact_text" id="meprconstantcontact_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
          </label>
          <div><span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-in checkbox.', 'memberpress-constantcontact'); ?></span></div>
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
    update_option('meprconstantcontact_enabled',    (isset($_POST['meprconstantcontact_enabled'])));
    update_option('meprconstantcontact_api_key',    stripslashes($_POST['meprconstantcontact_api_key']));
    update_option('meprconstantcontact_access_token',    $_POST['meprconstantcontact_access_token']);
    update_option('meprconstantcontact_list_id',    (isset($_POST['meprconstantcontact_list_id']))?stripslashes($_POST['meprconstantcontact_list_id']):false);
    //update_option('meprconstantcontact_double_optin', (isset($_POST['meprconstantcontact_double_optin'])));
    update_option('meprconstantcontact_optin',    (isset($_POST['meprconstantcontact_optin'])));
    update_option('meprconstantcontact_text',     stripslashes($_POST['meprconstantcontact_text']));
  }

  public function display_signup_field() {
    $mepr_options = MeprOptions::fetch();
    $post = MeprUtils::get_current_post();
    $prd = MeprProduct::is_product_page($post);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't see this
    if($prd !== false) {
      $enabled = (bool)get_post_meta($prd->ID, '_meprconstantcontact_list_override', true);

      if($enabled && $mepr_options->disable_global_autoresponder_list) { return; }
    }

    if($this->is_enabled_and_authorized() and $this->is_optin_enabled()) {
      $optin = (MeprUtils::is_post_request())?isset($_POST['meprconstantcontact_opt_in']):$mepr_options->opt_in_checked_by_default;

      ?>
      <div class="mp-form-row">
        <div class="mepr-constantcontact-signup-field">
          <div id="mepr-constantcontact-checkbox">
            <input type="checkbox" name="meprconstantcontact_opt_in" id="meprconstantcontact_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
            <span class="mepr-constantcontact-message"><?php echo $this->optin_text(); ?></span>
          </div>
          <div id="mepr-constantcontact-privacy">
            <small>
              <a href="http://www.constantcontact.com/legal/privacy-statement" class="mepr-constantcontact-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-constantcontact'); ?></a>
            </small>
          </div>
        </div>
      </div>
    <?php
    }
  }

  public function process_signup($txn_amount, $user, $prod_id, $txn_id) {
    $mepr_options = MeprOptions::fetch();
    $prd = new MeprProduct($prod_id);
    $enabled = (bool)get_post_meta($prd->ID, '_meprconstantcontact_list_override', true);

    //If the per product list is enabled, and the global list is disabled -- then we should be sure the member doesn't get added
    if(!$this->is_enabled_and_authorized() || ($enabled && $mepr_options->disable_global_autoresponder_list)) { return; }

    if(!$this->is_optin_enabled() || ($this->is_optin_enabled() && isset($_POST['meprconstantcontact_opt_in']))) {
      $this->add_subscriber($user, $this->list_id());
    }
  }

  public function maybe_add_subscriber($txn) {
    $enabled = (bool)get_post_meta($txn->product_id, '_meprconstantcontact_list_override', true);
    $list_id = get_post_meta($txn->product_id, '_meprconstantcontact_list_override_id', true);

    $user = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->add_subscriber($user, $list_id);
    }

    return false;
  }

  public function maybe_delete_subscriber($txn) {
    $enabled = (bool)get_post_meta($txn->product_id, '_meprconstantcontact_list_override', true);
    $list_id = get_post_meta($txn->product_id, '_meprconstantcontact_list_override_id', true);

    $user = $txn->user();

    if($enabled && !empty($list_id) && $this->is_enabled_and_authorized()) {
      return $this->delete_subscriber($user, $list_id);
    }

    return false;
  }

  public function validate_signup_field($errors) {
    // Nothing to validate -- if ever
  }

  public function display_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    $override_list = (bool)get_post_meta($product->ID, '_meprconstantcontact_list_override', true);
    $override_list_id = get_post_meta($product->ID, '_meprconstantcontact_list_override_id', true);

    ?>
    <div id="mepr-constantcontact" class="mepr-product-adv-item">
      <input type="checkbox" name="meprconstantcontact_list_override" id="meprconstantcontact_list_override" data-access-token="<?php echo $this->access_token(); ?>" data-apikey="<?php echo $this->apikey(); ?>" <?php checked($override_list); ?> />
      <label for="meprconstantcontact_list_override"><?php _e('Constant Contact Email list for this Membership', 'memberpress-constantcontact'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprconstantcontact-list-override',
        __('Enable Membership Constant Contact List', 'memberpress-constantcontact'),
        __('If this is set the member will be added to this email list when their payment is completed for this membership. If the member cancels or you refund their subscription, they will be removed from the list automatically. You must have your Constant Contact API key and Access Token set in the Options before this will work.', 'memberpress-constantcontact'));
      ?>

      <div id="meprconstantcontact_override_area" class="mepr-hidden product-options-panel">
        <label><?php _e('Constant Contact Email List: ', 'memberpress-constantcontact'); ?></label>
        <select name="meprconstantcontact_list_override_id" id="meprconstantcontact_list_override_id" data-listid="<?php echo stripslashes($override_list_id); ?>" class="mepr-text-input form-field"></select>
      </div>
    </div>
  <?php
  }

  public function save_product_override($product) {
    if(!$this->is_enabled_and_authorized()) { return; }

    if(isset($_POST['meprconstantcontact_list_override'])) {
      update_post_meta($product->ID, '_meprconstantcontact_list_override', true);
      update_post_meta($product->ID, '_meprconstantcontact_list_override_id', stripslashes($_POST['meprconstantcontact_list_override_id']));
    }
    else {
      update_post_meta($product->ID, '_meprconstantcontact_list_override', false);
    }
  }

  public function ping_apikey() {
    return $this->call('account_info', array(), '', '');
  }

  public function ajax_ping_apikey() {
    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) or !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) or !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-constantcontact'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['access_token'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-constantcontact'), 'type' => 'memberpress')));
    }

    die($this->call('account_info', array(), $_POST['apikey'], $_POST['access_token']));
  }

  public function get_lists() {
    $args = array();

    return $this->call('get_list', $args, '', '');
  }

  public function ajax_get_lists() {
    $args = array("ids" => "all"); //A comma-separated list of subscription form ID's of lists you wish to view. Pass "all" to view all lists.

    // Validate nonce and user capabilities
    if(!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], MEPR_PLUGIN_SLUG) || !MeprUtils::is_mepr_admin()) {
      die(json_encode(array('error' => __('Hey yo, why you creepin\'?', 'memberpress-constantcontact'), 'type' => 'memberpress')));
    }

    // Validate inputs
    if(!isset($_POST['apikey']) || !isset($_POST['access_token'])) {
      die(json_encode(array('error' => __('No apikey code was sent', 'memberpress-constantcontact'), 'type' => 'memberpress')));
    }

    die($this->call('get_list', $args, $_POST['apikey'], $_POST['access_token']));
  }

   public function add_subscriber(MeprUser $contact, $list_id) {
    $args = array(
      'email' => $contact->user_email,
      'list' => $list_id ,
      'first_name' => $contact->first_name,
      'last_name' => $contact->last_name,
    );

     $res = $this->call('add_contact', $args );

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
      'list' => $list_id,
    );

    $res = $this->call('delete_contact',$args);

    if($res != 'error') {
      return true;
    }
    else {
      return false;
    }
  }

  private function call( $endpoint, $args=array(), $apikey=null, $access_token=null ) {
    if( is_null($apikey) ) { $apikey = $this->apikey(); }
    if( is_null($access_token) ) { $access_token = $this->access_token(); }

    $cc = new ConstantContact( $apikey );
    $response = false;

    if( $endpoint == 'account_info'){
      try {
        $response = $cc->getAccountInfo($access_token);
      }
      catch (CtctException $ex) {
        $response = false;
      }
    }
    else if( $endpoint == 'get_list'){
      try {
        $response = $cc->getLists($access_token);
      }
      catch (CtctException $ex) {
        $response = false;
      }
    }
    else if( $endpoint == 'add_contact' ){
      try {
        // check to see if a contact with the email address already exists in the account
        $response = $cc->getContactByEmail( $access_token, $args['email']);

        // create a new contact if one does not exist
        if ( empty($response->results) ) {
          //Creating Contact

          $contact = new Contact();
          $contact->addEmail($args['email']);
          $contact->addList($args['list']);
          $contact->first_name = $args['first_name'];
          $contact->last_name = $args['last_name'];

          $response = $cc->addContact( $access_token, $contact, true );
        }
        else { // update the existing contact if address already existed
          //Add a list to the Contact

          $contact = $response->results[0];
          $contact->addList($args['list']);

          $contact->first_name = $args['first_name'];
          $contact->last_name = $args['last_name'];

          $response = $cc->updateContact( $access_token, $contact, true);
        }

      // catch any exceptions thrown during the process
      }
      catch (CtctException $ex) {
        $response = false;
      }
    }
    elseif (  $endpoint == 'delete_contact'  ){
      try {
        // check to see if a contact with the email address already exists in the account
        $res = $cc->getContactByEmail($access_token, $args['email']);

        if ( !empty($res->results) ) {
          $contact = $res->results[0];

          $response = $cc->deleteContactFromList( $access_token, $contact, $args['list'] );
        }
      }
      catch (CtctException $ex){
        $response = false;
      }
    }
    elseif( $endpoint == 'update_email') {
      try {
        // check to see if a contact with the email address already exists in the account
        $response = $cc->getContactByEmail( $access_token, $args['email']);

        // update the existing contact if address already existed
        if ( !empty($response->results) ) {
          //Updating Contact
          $contact = $response->results[0];

          // Update email address to new email address
          $contact->email_addresses[0]->email_address = $args['new_email'];

          $response = $cc->updateContact( $access_token, $contact, true);
        }

      // catch any exceptions thrown during the process
      }
      catch (CtctException $ex) {
        $response = false;
      }
    }

    if ( $response ) {
      return json_encode($response);
    }
    else {
      return 'error';
    }
  }

  // I realize these are more like model methods
  // but we want everything centralized here people
  private function is_enabled() {
    return get_option('meprconstantcontact_enabled', false);
  }

  private function is_authorized() {
    $apikey = get_option('meprconstantcontact_api_key', '');
    $access_token = get_option('meprconstantcontact_access_token', '');
    return (!empty($apikey) and !empty($access_token));
  }

  private function is_enabled_and_authorized() {
    return ($this->is_enabled() and $this->is_authorized());
  }

  private function apikey() {
    return get_option('meprconstantcontact_api_key', '');
  }

  private function access_token() {
    return get_option('meprconstantcontact_access_token', '');
  }

  private function list_id() {
    return get_option('meprconstantcontact_list_id', false);
  }

  private function is_double_optin_enabled() {
    return get_option('meprconstantcontact_double_optin', true);
  }

  private function is_optin_enabled() {
    return get_option('meprconstantcontact_optin', true);
  }

  private function optin_text() {
    $default = sprintf(__('Sign Up for the %s Newsletter', 'memberpress-constantcontact'), get_option('blogname'));
    return get_option('meprconstantcontact_text', $default);
  }

} //END CLASS
