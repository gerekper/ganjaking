<?php

/**
 * Class CT_Ultimate_GDPR_Model_Services
 */
class CT_Ultimate_GDPR_Model_Services {

	/** @var array */
	private $services;

	/** @var self */
	private static $instance;

	/** @var CT_Ultimate_GDPR */
	private $front_controller;

	/**
	 * @return CT_Ultimate_GDPR_Model_Services
	 */
	public static function instance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * CT_Ultimate_GDPR_Model_Services constructor.
	 */
	private function __construct() {
		$this->services = array();
	}

	/**
	 * @param CT_Ultimate_GDPR $front_controller
	 */
	public function set_front_controller( $front_controller ) {
		$this->front_controller = $front_controller;
		return $this;
	}

	/**
	 * @param array $options
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	public function get_services( $options = array(), $type = 'all' ) {

		if ( ! $this->services ) {
			$this->load_services( $options );
		}

		if ( $type == 'all' ) {
			return $this->services;
		}

		$filtered = array();

		/** @var CT_Ultimate_GDPR_Service_Abstract $service */
		foreach ( $this->services as $service ) {

			$method = "is_$type";

			if ( method_exists( $service, $method ) && $service->$method() ) {

				$filtered[ $service->get_id() ] = $service;

			}

		}

		return $filtered;

	}

	/**
	 * @param $service
	 *
	 * @return $this
	 */
	private function add_service( $service ) {

		if ( $service instanceof CT_Ultimate_GDPR_Service_Interface ) {
			if ( $service->is_active() ) {
				$this->services[ $service->get_id() ] = $service;
			}
		}

		return $this;

	}

	/**
	 * Load all services
	 *
	 * @param array $options
	 */
	private function load_services( $options ) {

		$logger = new CT_Ultimate_GDPR_Model_Logger();

		/**
		 * Instantiated services will auto register.
		 */
		$default_services_classes = apply_filters( 'ct_ultimate_gdpr_model_services_default', array(
				'CT_Ultimate_GDPR_Service_Addthis',
				'CT_Ultimate_GDPR_Service_ARForms',
				'CT_Ultimate_GDPR_Service_bbPress',
				'CT_Ultimate_GDPR_Service_Buddypress',
				'CT_Ultimate_GDPR_Service_Caldera_Forms',
				'CT_Ultimate_GDPR_Service_CF7DB',
				'CT_Ultimate_GDPR_Service_WPForms_Lite',
				'CT_Ultimate_GDPR_Service_Contact_Form_7',
				'CT_Ultimate_GDPR_Service_CT_Waitlist',
				'CT_Ultimate_GDPR_Service_Custom_Facebook_Feed',
				'CT_Ultimate_GDPR_Service_Eform',
				'CT_Ultimate_GDPR_Service_Events_Manager',
				'CT_Ultimate_GDPR_Service_Facebook_Pixel',
				'CT_Ultimate_GDPR_Service_Flamingo',
				'CT_Ultimate_GDPR_Service_Formcraft',
				'CT_Ultimate_GDPR_Service_Formidable_Forms',
				'CT_Ultimate_GDPR_Service_GA_Google_Analytics',
				'CT_Ultimate_GDPR_Service_Google_Adsense',
				'CT_Ultimate_GDPR_Service_Google_Analytics',
				'CT_Ultimate_GDPR_Service_Google_Analytics_Dashboard_For_WP',
				'CT_Ultimate_GDPR_Service_Google_Analytics_For_Wordpress',
				'CT_Ultimate_GDPR_Service_Gravity_Forms',
				'CT_Ultimate_GDPR_Service_Hotjar',
				'CT_Ultimate_GDPR_Service_Klaviyo',
				'CT_Ultimate_GDPR_Service_Mailchimp',
				'CT_Ultimate_GDPR_Service_Mailerlite',
				'CT_Ultimate_GDPR_Service_Mailpoet',
				'CT_Ultimate_GDPR_Service_Mailster',
				'CT_Ultimate_GDPR_Service_Metorik_Helper',
				'CT_Ultimate_GDPR_Service_Newsletter',
				'CT_Ultimate_GDPR_Service_Ninja_Forms',
				'CT_Ultimate_GDPR_Service_Order_Delivery_Date_For_Woocommerce',
				'CT_Ultimate_GDPR_Service_Polylang',
				'CT_Ultimate_GDPR_Service_Quform',
				'CT_Ultimate_GDPR_Service_Ultimate_Member',
				'CT_Ultimate_GDPR_Service_Woocommerce',
				'CT_Ultimate_GDPR_Service_Wordfence',
				'CT_Ultimate_GDPR_Service_WP_Comments',
				'CT_Ultimate_GDPR_Service_WP_Foro',
				'CT_Ultimate_GDPR_Service_Wp_Job_Manager',
				'CT_Ultimate_GDPR_Service_WP_Mail_Bank',
				'CT_Ultimate_GDPR_Service_WP_Posts',
				'CT_Ultimate_GDPR_Service_WP_User',
				'CT_Ultimate_GDPR_Service_WP_Simple_Paypal_Shopping_Cart',
				'CT_Ultimate_GDPR_Service_Yandex_Metrica',
				'CT_Ultimate_GDPR_Service_Yith_Woocommerce_Wishlist',
				'CT_Ultimate_GDPR_Service_Formcraft_Form_Builder',
				'CT_Ultimate_GDPR_Service_Youtube',
			)
		);

		foreach ( $default_services_classes as $default_service ) {
			if ( is_a( $default_service, 'CT_Ultimate_GDPR_Service_Interface', true ) ) {
				/** @var CT_Ultimate_GDPR_Service_Interface $service */
				$service = new $default_service( $this->front_controller, $logger );
				if ($service->is_active()){
					$service->set_group(new CT_Ultimate_GDPR_Model_Group());
					$service->init();
				}
			}
		}

		$all_services = apply_filters( 'ct_ultimate_gdpr_load_services', array(), $options, $this->services );

		foreach ( $all_services as $service ) {
			$this->add_service( $service );
		}
	}

	/**
	 * @param $service_id
	 *
	 * @return bool|CT_Ultimate_GDPR_Service_Abstract
	 */
	public function get_service_by_id( $service_id ) {
		$this->get_services();

		return isset( $this->services[ $service_id ] ) ? $this->services[ $service_id ] : false;
	}


}