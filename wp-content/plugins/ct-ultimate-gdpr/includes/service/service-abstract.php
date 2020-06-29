<?php

/**
 * Interface CT_Ultimate_GDPR_Service_Abstract
 */
abstract class CT_Ultimate_GDPR_Service_Abstract implements CT_Ultimate_GDPR_Service_Interface {


	/**
	 * @var CT_Ultimate_GDPR_Model_Logger $logger
	 */
	protected $logger;

	/**
	 * Collected data array
	 *
	 * @var array
	 */
	protected $collected = array();

	/**
	 * User set by controllers
	 *
	 * @var CT_Ultimate_GDPR_Model_User
	 */
	protected $user;

	/**
	 * Group object to compare privacy levels
	 *
	 * @var CT_Ultimate_GDPR_Model_Group|CT_Ultimate_CCPA_Model_Group
	 */
	protected $group;

	/**
	 * @var CT_Ultimate_CCPA|CT_Ultimate_GDPR
	 */
	protected $front_controller;

	/**
	 * Run on init
	 * @return void
	 */
	abstract public function init();

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	abstract public function collect();

	/**
	 * Get service name
	 *
	 * @return mixed
	 */
	abstract public function get_name();

	/**
	 * Is it active, eg. whether related plugin is enabled. Used mainly by Data Access controller
	 *
	 * @return bool
	 */
	abstract public function is_active();

	/**
	 * Can data be forgotten by this service?
	 *
	 * @return bool
	 */
	abstract public function is_forgettable();

	/**
	 * Forget specific user data
	 *
	 * @throws Exception
	 * @return void
	 */
	abstract public function forget();

	/**
	 * Add admin option fields
	 *
	 * @return mixed
	 */
	abstract public function add_option_fields();

	/**
	 * Do optional action on front
	 *
	 * @return mixed
	 */
	abstract public function front_action();

	/**
	 * Can user be subscribe to a newsletter by this service?
	 *
	 * @return bool
	 */
	public function is_subscribeable() {
		return false;
	}

	/**
	 * Unsubscribe user from all newsletters
	 *
	 * @throws Exception
	 * @return void
	 */
	public function unsubscribe() {
	}

	/**
	 * Get group levels of this service
	 *
	 * @return array
	 */
	public function get_group_levels() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_group_levels", array( $this->get_group()->get_level_convenience() ) );
	}

	/**
	 * @return CT_Ultimate_CCPA_Controller_Admin|CT_Ultimate_GDPR_Controller_Admin
	 */
	protected function get_admin_controller(  ) {
		return $this->front_controller->get_admin_controller();
	}

	/**
	 * @return CT_Ultimate_CCPA|CT_Ultimate_GDPR
	 */
	protected function get_front_controller(  ) {
		return $this->front_controller;
	}

	/**
	 * Set group object and level
	 */
	public function set_group($group) {
		$this->group = $group;
		$this->group->add_levels( $this->get_group_levels() );
	}

	/**
	 * @return CT_Ultimate_CCPA_Model_Group|CT_Ultimate_GDPR_Model_Group
	 */
	public function get_group() {
		return apply_filters("ct_ultimate_gdpr_service_{$this->get_id()}_group_model", $this->group);
	}

	/**
	 * CT_Ultimate_GDPR_Service_Abstract constructor.
	 *
	 * @param $logger
	 * @param CT_Ultimate_CCPA|CT_Ultimate_GDPR $front_controller
	 */
	public function __construct( $front_controller, $logger ) {

		$this->front_controller    = $front_controller;
		$this->logger              = $logger;

		add_action( 'current_screen', array( $this, 'add_option_fields' ), 20 );

		if ( $this->is_active() ) {

			add_filter( 'ct_ultimate_gdpr_load_services', array( $this, 'register' ) );
			add_filter( "ct_ultimate_gdpr_breach_recipients_{$this->get_id()}", array(
				$this,
				"breach_recipients_filter"
			) );
		}

	}

	/** Dequeue scripts with specified content (external cookies)
	 *
	 * @param array $scripts
	 * @param bool $force
	 *
	 * @return array
	 */
	public function script_blacklist_filter( $scripts, $force = false ) {
		return $scripts;
	}

	/**
	 * Meant to be extended. Lists all cookies (or just prefixes) this service is using.
	 *
	 * @param $cookies
	 * @param bool $force
	 *
	 * @return mixed
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {
		return $cookies;
	}

	/**
	 * Render field for setting custom service description
	 */
	public function render_description_field() {

		$admin      = $this->get_admin_controller();
		$field_name = "services_{$this->get_id()}_description";

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='10' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, $this->get_description() ),
            $admin->get_option_value_escaped( $field_name, $this->get_service_name() )
		);

	}
    /**
     * Render field for setting custom service description
     */
    public function render_name_field() {

	    $admin      = $this->get_admin_controller();
        $field_name = "services_{$this->get_id()}_service_name";

        printf(
            "<textarea class='ct-ultimate-gdpr-field-name' id='%s' name='%s' rows='1' cols='100'>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, $this->get_service_name() )
        );

    }
	/**
	 * Simple render of collected user data
	 *
	 * @param bool $human_readable
	 *
	 * @return string
	 */
	public function render_collected( $human_readable = false ) {

		$return                         = '';
		$data                           = '';
		$removeEmptyField               = CT_Ultimate_GDPR::instance()
			->get_admin_controller()
			->get_option_value(
				"dataaccess_remove_empty_fields",
				'',
				$this->front_controller->find_controller('access')->get_id()
			);

		if ( ! empty( $this->collected ) ) {
			if ( $removeEmptyField == 'on' ) {
				$data = $this->dataaccess_array_remove_empty_fields( $this->collected );

			}else{
				$data = $this->collected;
			}

			if ( $human_readable ) {
				$return = $this->render_human_data( $data );
			} else {
				$return = ct_ultimate_gdpr_json_encode( $data );
			}
		}

		return apply_filters( 'ct_ultimate_gdpr_service_render_collected', $return, $this->collected, $human_readable, $this->get_id() );
	}

	/**
	 * @param array $array
	 *
	 * @return array
	 */
	public function dataaccess_array_remove_empty_fields( $array ){

		foreach ( $array as $key => $value ) {
			if( is_array( $value ) || is_object( $value ) ){
				$value = $array[$key] = $this->dataaccess_array_remove_empty_fields( $value );
			}

			if ( empty( $value ) ) {
				if( is_array( $array ) ){
					unset( $array[ $key ] );
				} elseif ( is_object( $array ) ) {
					unset( $array->$key );
				}
			}
		}
		return $array;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function render_human_data( $data ) {

		$return = '';

		if ( is_array( $data ) || is_object( $data ) ) {
			foreach ( $data as $item_key => $item ) {
				if ( is_array( $item ) || is_object( $item ) ) {
					if ( is_array( $item ) && isset( $item[0] ) && ! is_array( $item[0] ) && ! is_object( $item[0] ) ) {
						$return .= "$item_key: $item[0]" . PHP_EOL;
					} else {
						$return .= $this->render_human_data( $item );
					}
				} else {
					$return .= "$item_key: $item" . PHP_EOL;
				}
			}
		} else {
			$return .= $data;
		}

		return $return;

	}

	/**
	 * Get service description
	 *
	 * @return string
	 */
	public function get_description() {

		$user_description = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_description", '', $this->front_controller->find_controller('services')->get_id() );
		$return           = $user_description ? $user_description : $this->get_default_description();
		$return           = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_description", $return );

		return $return;
	}

    /**
     * Get service name
     *
     * @return string
     */
    public function get_service_name() {

        $user_description = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_service_name", '', $this->front_controller->find_controller('services')->get_id() );
        $return           = $user_description ? $user_description : $this->get_name();
        $return           = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_service_name", $return );

        return $return;
    }

	/**
	 * Get service id (based on class name)
	 *
	 * @return string
	 */
	public function get_id() {
		return strtolower( str_replace( 'CT_Ultimate_GDPR_Service_', '', get_called_class() ) );
	}

	/**
	 * Get default service description
	 *
	 * @return string
	 */
	protected function get_default_description() {
		return '';
	}

	/**
	 * Register add service to the collection
	 *
	 * @param $services
	 *
	 * @return array
	 */
	public function register( $services ) {

		if ( $this->is_active() ) {
			$services[] = $this;
		}

		return $services;
	}

	/**
	 * Set target user
	 *
	 * @param CT_Ultimate_GDPR_Model_User $user
	 *
	 * @return $this
	 */
	public function set_user( $user ) {

		$this->user = $user;

		return $this;

	}

	/**
	 * Set collected data
	 *
	 * @param array $collected
	 *
	 * @return $this
	 */
	protected function set_collected( $collected ) {
		$this->collected = apply_filters( 'ct_ultimate_gdpr_service_collected', $collected, $this->get_id(), $this->user );

		return $this;
	}

	/**
	 * Add breach recipients filter
	 *
	 * @param array $recipients
	 *
	 * @return array
	 */
	public function breach_recipients_filter( $recipients ) {
		return $recipients;
	}

	/**
	 * Log user accepted the consent checkbox
	 */
	protected function log_user_consent() {

		$this->logger->consent( array(
			'type'       => $this->get_id(),
			'time'       => time(),
			'user_id'    => wp_get_current_user()->ID,
			'user_ip'    => ct_ultimate_gdpr_get_permitted_user_ip(),
			'user_agent' => ct_ultimate_gdpr_get_permitted_user_agent()
		) );

	}

	/**
	 * Is breach filter option enabled for this service?
	 * @return bool
	 */
	protected function is_breach_enabled() {

		$enabled_array = $this->get_admin_controller()->get_option_value( 'breach_services' );
		if ( ! is_array( $enabled_array ) || ! in_array( $this->get_id(), $enabled_array ) ) {
			return false;
		}

		return true;

	}
}