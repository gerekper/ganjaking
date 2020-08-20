<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CT_Ultimate_GDPR_Controller_Abstract
 *
 */
abstract class CT_Ultimate_GDPR_Controller_Abstract implements CT_Ultimate_GDPR_Controller_Interface {

	/**
	 * @var array $options Admin options
	 */
	protected $options;

	/**
	 * Current user model
	 *
	 * @var CT_Ultimate_GDPR_Model_User $user
	 */
	protected $user;

	/**
	 * @var array $view_options Front view options
	 */
	protected $view_options;

	/**
	 * @var string $id Controller id
	 */
	protected $id;

	/**
	 * @var CT_Ultimate_GDPR_Model_Logger
	 */
	protected $logger;

	/**
	 * Get unique controller id (page name, option id)
	 */
	abstract public function get_id();

	/**
	 * Init after construct
	 */
	abstract public function init();

	/**
	 * Do actions on frontend
	 */
	abstract public function front_action();

	/**
	 * Do actions in admin (general)
	 */
	abstract public function admin_action();

	/**
	 * Do actions on current admin page
	 */
	abstract protected function admin_page_action();

	/**
	 * Get view template string
	 * @return string
	 */
	abstract public function get_view_template();

	/**
	 * Add menu page (if not added in admin controller)
	 */
	abstract public function add_menu_page();

	/**
	 * @return mixed
	 */
	abstract public function add_option_fields();

	/**
	 * CT_Ultimate_GDPR_Data_Access constructor.
	 *
	 * @param CT_Ultimate_GDPR_Model_Logger $logger
	 */
	final public function __construct( $logger ) {

		$this->logger = $logger;
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20 );
		add_action( 'current_screen', array( $this, 'add_option_fields_function' ), 5 );
		add_action( 'current_screen', array( $this, 'add_option_fields' ) );
		add_action( 'current_screen', array( $this, 'current_screen_action' ), 20 );
		$this->set_user( new CT_Ultimate_GDPR_Model_User() );
		$this->set_view_options( array() );

	}

	/**
	 * Make sure option fields functions are present
	 */
	public function add_option_fields_function() {
		include_once( ABSPATH . 'wp-admin/includes/template.php' );
	}

	/**
	 * Set admin options
	 *
	 * @param array $options
	 *
	 * @return CT_Ultimate_GDPR_Controller_Abstract
	 */
	public function set_options( $options ) {
		$options       = is_array( $options ) ? $options : array();
		$this->options = $options;

		return $this;
	}

	/**
	 * @param CT_Ultimate_GDPR_Model_User $user
	 *
	 * @return CT_Ultimate_GDPR_Controller_Abstract
	 */
	public function set_user( CT_Ultimate_GDPR_Model_User $user ) {

		$this->user = $user;

		return $this;
	}

	/**
	 * Get controller admin option
	 *
	 * @param $name
	 * @param string $default
	 *
	 * @param string $translation_type for wpml object translation
	 *
	 * @param bool $allow_empty
	 *
	 * @return mixed
	 */
	protected function get_option( $name, $default = '', $translation_type = '', $allow_empty = true ) {

		if ( is_array( $this->options ) && isset( $this->options[ $name ] ) ) {

			if ( ! empty( $this->options[ $name ] ) || $allow_empty ) {

				return $translation_type ? ct_ultimate_gdpr_wpml_translate_id( $this->options[ $name ], $translation_type ) : $this->options[ $name ];

			}

		}

		return $default;
	}

	/**
	 * Check if current page is controllers admin page
	 *
	 * @return bool
	 */
	protected function is_admin_page_active() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		if ( false === stripos( get_current_screen()->id, $this->get_id() ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Do admin page action
	 */
	public function current_screen_action() {

		if ( $this->is_admin_page_active() ) {
			$this->admin_page_action();
		}

	}

	/**
	 * @param mixed $view_options
	 *
	 * @return CT_Ultimate_GDPR_Controller_Abstract
	 */
	protected function set_view_options( $view_options = array() ) {
		$this->view_options = $view_options;

		return $this;
	}

	/**
	 * @param $key
	 * @param string|array $value
	 *
	 * @return CT_Ultimate_GDPR_Controller_Abstract
	 */
	protected function add_view_option( $key, $value = '' ) {

		if ( isset( $this->view_options[ $key ] ) && is_array( $this->view_options[ $key ] ) ) {
			if ( is_array( $value ) ) {
				$this->view_options[ $key ] = array_merge( $this->view_options[ $key ], $value );
			} else {
				$this->view_options[ $key ][] = $value;
			}
		} else {
			$this->view_options[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Render menu page action
	 */
	public function render_menu_page() {
		ct_ultimate_gdpr_locate_template( $this->get_view_template(), true, $this->view_options );
	}

	/**
	 * Return array of default controller admin options
	 *
	 * @return array
	 */
	public function get_default_options() {
		return array();
	}

	/**
	 * @return array
	 */
	public function get_request_array() {
		return apply_filters( 'ct_ultimate_gdpr_controller_request_array', $_REQUEST );
	}

	/**
	 * @return array
	 */
	public function get_options_to_export() {

		$options = $this->options;
		$excluded = $this->get_excluded_keys_options_to_export();

		foreach ( $excluded as $key ) {
			unset( $options[ $key ] );
		}

		return apply_filters( 'ct_ultimate_gdpr_controller_' . $this->get_id() . '_options_to_export', $options, $excluded );
	}

	/**
	 * @return array
	 */
	private function get_excluded_keys_options_to_export() {
		return array();
	}

	/**
	 * send a response if the request an ajax action
	 * @param $notice
	 */
	public function send_ajax_response(){
		wp_send_json( array('notices' =>  CT_Ultimate_GDPR_Model_Front_View::instance()->get('notices' ) ));
	}
}
