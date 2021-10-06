<?php
/**
 * Extension Class
 *
 * Handles the extensions. Extensions can be themes or plugins depending on the context
 * You can check what type one extension is by accessing the property type
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Ultimo/Add_On
 * @version     1.0.0
*/

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WU_Addon_Updater')) :

/**
 * WU_Addon_Updater
 */
class WU_Addon_Updater {

  /**
   * Saves the Plugin slug so we can generate relevant URLs and etc
   * @var string
   */
  public $plugin_slug = '';

  /**
   * Plugin anme
   * @var string
   */
  public $plugin_name = '';

  /**
   * Plugin file reference
   * @var string
   */
  public $plugin_file;

  /**
   * Initiates Updater
   * @param string $plugin_slug Plugin slug
   */
  public function __construct($plugin_slug, $plugin_name, $plugin_file) {

    $this->plugin_slug = $plugin_slug;
    $this->plugin_name = $plugin_name;
    $this->plugin_file = $plugin_file;

    // Start!
    $this->add_updates();

  } // end __construct;

  /**
   * Get the update URL for the checker
   * @return string The URL
   */
  public function get_update_URL() {

    return "https://versions.nextpress.co/updates/?action=get_metadata&slug=$this->plugin_slug";

  } // end get_update_URL;

  public function is_golden_ticket() {

    return get_site_transient('wu_golden_ticket', false) ?: 'no';

  }
  
  /**
   *  This function adds our autoupdates functionality so your verifies buyers always get the newest version
   *  of the plugins and themes
   */
  protected function add_updates() {

    // Adds the License Code Field
    add_filter('wu_settings_sections', array($this, 'add_license_fields'));

    // Save the setings
    add_action('wu_after_save_settings', array($this, 'save_license_field'), 10);
    
    // We need to check the purchase code everytime the buyer adds a new one and saves
    add_action('init', array($this, 'check_buyer'));
    
    // Enable our auto updates library
    add_action('init', array($this, 'enable_update_checker'));
    
  } // end add_updates

  /**
   * Get the license status
   * @param  boolean $golden_ticket
   * @return string
   */
  public function get_license_status($golden_ticket = false) {

    if ($golden_ticket == 'yes') {

      return '<span style="color: green">'. __('Status: Activated - Golden Ticket', 'wp-ultimo') .'</span>';

    } // end if;

    $license_status = get_network_option(null, WP_Ultimo()->slugfy("verified-$this->plugin_slug"));

    if (is_object($license_status) && $license_status->success) {

      if ($license_status->purchase->refunded == false) {

        $license_status_string = '<span style="color: green">'. __('Status: Activated', 'wp-ultimo') .'</span>';

      } else {

        $license_status_string = '<span style="color: red">'. __('Status: License no longer valid - Refund issued', 'wp-ultimo') .'</span>';

      }

    } else {

      $license_status_string = '<span style="color: red">'. __('Status: Not Activated - Invalid License Key', 'wp-ultimo') .'</span>';

    } // end if;

    return $license_status_string;

  } // end get_license_status;

  /**
   * Uses hook to overwrite some WordPress network options
   * @param  string $field_slug ID of the field being saved
   * @param  array  $field      Field settings
   * @param  array  $post       POST array
   */
  public function save_license_field() {

    if (!isset($_REQUEST['wu-tab']) || $_REQUEST['wu-tab'] != 'activation') {

      return;

    } // end if;

    // Check for buyer info
    $this->check_buyer();

  } // end wordpress_overwrite;

  /**
   * Add fields to the settings
   * @param array $sections
   */
  public function add_license_fields($sections) {

    if (!isset($sections['activation']['fields']["license_key_$this->plugin_slug"])) {

      // Check if we need or not to add the new heading
      if (!isset($sections['activation']['fields']["addons_license_heading"])) {

        $sections['activation']['fields']["addons_license_heading"] = array(
          'title'         => __('Activate your Add-ons', 'wp-ultimo'),
          'desc'          => __('Use the license key you received with the add-on files to activate your copy and be notified of new versions automatically.', 'wp-ultimo'),
          'type'          => 'heading',
        );

      } // end if;

      $golden_ticket = $this->is_golden_ticket();

      // Get license status
      $license_status_string = $this->get_license_status($golden_ticket);

      // Check for golden Ticket
      if ($golden_ticket == 'yes') {

        $sections['activation']['fields']["license_key_$this->plugin_slug"] = array(
          'title'         => sprintf(__('License Key - %s', 'wp-ultimo'), $this->plugin_name),
          'desc'          => sprintf(__('You have a golden ticket license. There\'s no need to activate each add-on individually.', 'wp-ultimo').'<br>%s', $license_status_string),
          'tooltip'       => '',
          'type'          => 'note',
          'placeholder'   => 'xxxx-xxxx-xxxx-xxxx',
          'default'       => '',
        );

      } else {

        $sections['activation']['fields']["license_key_$this->plugin_slug"] = array(
          'title'         => sprintf(__('License Key - %s', 'wp-ultimo'), $this->plugin_name),
          'desc'          => sprintf(__('Put the code you received alongside the add-on when you finalized your purchase.', 'wp-ultimo').'<br>%s', $license_status_string),
          'tooltip'       => '',
          'type'          => 'password',
          'placeholder'   => 'xxxx-xxxx-xxxx-xxxx',
          'default'       => '',
        );

      } // end if;

    } // end if;

    return $sections;

  } // end add_license_fields;
  
  /**
   * Check if our user is validated or not.
   * Only runs on the save of new purchase code
   */
  public function check_buyer() {

    // Check only if license key is being entered
    if (!isset($_POST["license_key_$this->plugin_slug"])) {

      return;

    } // end if;

    // Check if user has a purchase code
    $purchase_code = $_POST["license_key_$this->plugin_slug"];

    if ($purchase_code === WU_Settings::get_setting("license_key_$this->plugin_slug")) {

      return;

    } // end if;
      
    // Check if we already validated his purchase code
    $return = $this->validate_purchase_code($purchase_code);
    
    // Save new check
    update_network_option(null, WP_Ultimo()->slugfy("verified-$this->plugin_slug"), $return);

    // Display messages
    if ($return->success) {

      WP_Ultimo()->add_message('Activation successfull', 'success', true);

    } else {

      WP_Ultimo()->add_message($return->message, 'error', true);

    }
  
  } // end check_buyer;

  /**
   * Validate buyer purchase code
   * @param  string  $purchase_code The purchase code to be validate
   * @return boolean returns if the user is validate or not
   */
  public function validate_purchase_code($purchase_code) {
    
    $url  = str_replace('action=get_metadata', 'action=verify_license', $this->get_update_URL());
    $url .= "&license_key=$purchase_code";

    $response = wp_remote_get($url, array(
      'timeout'   => 10,
      'sslverify' => false
    ));

    if (!is_wp_error($response)) {

      $body = json_decode(wp_remote_retrieve_body($response));

      return $body;

    } else {

      return false; // $response->get_error_message();

    }
    
  } // end validate_purchase_code;
  
  /**
   * Install AutoUpdates
   */
  public function enable_update_checker() {

    if (!function_exists('WP_Ultimo')) return;

    // Decides from where we are going to get the license_key
    if ($this->is_golden_ticket() == 'yes') {

      $validation_status = get_network_option(null, WP_Ultimo()->slugfy('verified'));

    } else {

      $validation_status = get_network_option(null, WP_Ultimo()->slugfy("verified-$this->plugin_slug"));

    } // end if;
    
    // Check if it's checked
    if ($validation_status && $validation_status->success && $validation_status->purchase->refunded == false) {

      // Requiring library
      require_once WP_Ultimo()->path('inc/updater/plugin-update-checker.php', true);

      $args = array(
        'license_key'  => $validation_status->license_key,
        'beta_program' => (int) WU_Settings::get_setting('beta-program', false),
      );

      $metadata_url = add_query_arg($args, $this->get_update_URL());
      
      // Instantiating it
      $updateChecker = PucFactory::buildUpdateChecker(
        $metadata_url,         // Metadata URL.
        $this->plugin_file,    // Full path to the main plugin file.
        $this->plugin_slug     // Plugin slug. Usually it's the same as the name of the directory.
      );
      
    } // end if;
    
  } // end autoUpdates;

} // end class WU_Addon_Updater;

endif;