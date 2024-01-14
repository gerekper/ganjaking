<?php

defined('ABSPATH') || exit;

if (!class_exists('SRP_REST_Server')) {

	/**
	 * Class responsible for loading the REST API and all REST API namespaces.
	 */
	class SRP_REST_Server {

		/**
		 * REST API namespaces and endpoints.
		 *
		 * @var array
		 */
		protected static $controllers = array();

		/**
		 * Hook into WordPress ready to init the REST API as needed.
		 */
		public static function init() {
			// Register rest routes.
			add_action('rest_api_init', array( __CLASS__, 'register_rest_routes' ));
		}

		/**
		 * Register REST API routes.
		 */
		public static function register_rest_routes() {
			if (!class_exists('WP_REST_Controller')) {
				return;
			}
						
			$namespaces = self::get_rest_namespaces();
			foreach ($namespaces as $namespace => $controllers) {
				foreach ($controllers as $controller_name => $controller_class) {
					include 'class-srp-rest-' . $controller_name . '-controller.php';
					self::$controllers[$namespace][$controller_name] = new $controller_class();
					self::$controllers[$namespace][$controller_name]->register_routes();
				}
			}
		}

		/**
		 * Get API namespaces - new namespaces should be registered here.
		 *
		 * @return array List of Namespaces and Main controller classes.
		 */
		protected static function get_rest_namespaces() {
			/**
			 * Get the rest namespaces.
			 * 
			 * @param array $namespaces 
			 * @since 1.0
			 */
			return apply_filters('srp_rest_api_get_rest_namespaces', array(
				'srp/v1' => self::get_v1_controllers(),
			));
		}

		/**
		 * List of controllers in the srp/v1 namespace.
		 *
		 * @return array
		 */
		protected static function get_v1_controllers() {
			return array(
				'earning' => 'SRP_REST_Earning_Controller',
				'redeeming' => 'SRP_REST_Redeeming_Controller',
			);
		}
	}

	SRP_REST_Server::init();
}
