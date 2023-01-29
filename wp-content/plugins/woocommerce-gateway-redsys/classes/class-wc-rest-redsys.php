<?php
/**
 * Class WC Rest Redsys
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2013 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC Rest Redsys
 */
class WC_REST_Redsys {
	/**
	 * You can extend this class with
	 * WP_REST_Controller / WC_REST_Controller / WC_REST_Products_V2_Controller / WC_REST_CRUD_Controller etc.
	 * Found in packages/woocommerce-rest-api/src/Controllers/
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	protected $rest_base = 'redsys'; // phpcs:ignore Squiz.Commenting.VariableComment.Missing

	/**
	 * Get custom data.
	 *
	 * @param WP_REST_Request $data Request.
	 *
	 * @return array
	 */
	public function get_custom( $data ) {
		return array( 'redsys' => 'Data' );
	}
	/**
	 * Register the routes API for Redsys.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_custom' ),
				'permission_callback' => '__return_true',
			)
		);
	}
}
add_filter( 'woocommerce_rest_api_get_rest_namespaces', 'redsys_custom_api' );

/**
 * Add custom API
 *
 * @param array $controllers Controllers.
 *
 * @return array
 */
function redsys_custom_api( $controllers ) {
	$controllers['wc/v3']['redsys'] = 'WC_REST_Redsys';

	return $controllers;
}
