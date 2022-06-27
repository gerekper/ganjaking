<?php
/**
 * WooCommerce_Cart_Addons_Blocks class.
 */

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\StoreApi;

/**
 * Class responsible to deal with the addition to the cart extension API.
 */
class WooCommerce_Cart_Addons_Blocks {

	/**
	 * Stores Rest Extending instance.
	 *
	 * @var ExtendRestApi
	 */
	private static $extend;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'wc_cart_addons_block';

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Gutenberg blocks support.
		add_action( 'init', array( $this, 'register' ) );
		$this->extend();
	}

	/**
	 * Registers front-end hydration scripts for block and the editor script at the footer.
	 */
	public function register() {
		$asset_file_frontend = include plugin_dir_path( __FILE__ ) . '../build/frontend.asset.php';
		wp_enqueue_script(
			'wc-blocks-cart-addons-scripts-frontend',
			plugins_url( '../build/frontend.js', __FILE__ ),
			$asset_file_frontend['dependencies'],
			$asset_file_frontend['version'],
			true
		);

		$asset_file_editor_script = include plugin_dir_path( __FILE__ ) . '../build/index.asset.php';
		wp_enqueue_script(
			'woocommerce-cart-add-ons-editor-script',
			plugins_url( '../build/index.js', __FILE__ ),
			$asset_file_frontend['dependencies'],
			$asset_file_frontend['version'],
			true
		);

		wp_enqueue_style(
			'woocommerce-cart-add-ons-style',
			plugins_url( '../build/index.css', __FILE__ ),
			array(),
			$asset_file_frontend['version']
		);

		register_block_type( 'woocommerce/cart-add-ons' );
	}

	/**
	 * Extends the blocks with information for the cart
	 */
	public function extend() {

		$extend = StoreApi::container()->get( ExtendSchema::class );
		$extend->register_endpoint_data(
			array(
				'endpoint'        => CartSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( $this, 'data_callback' ),
				'schema_callback' => array( $this, 'schema_callback' ),
				'schema_type'     => ARRAY_A,
			)
		);
	}

	/**
	 * Loops over $cart items to find a variable product
	 * and extract the category to return.
	 *
	 * @return array Ids of the categories.
	 */
	public function data_callback() {

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			if ( is_numeric( $product_id ) ) {
				$product = wc_get_product( $product_id );
				if ( 'variable' === $product->get_type() ) {
					$category_ids = $product->get_category_ids();
					return array( 'categories' => $category_ids );
				}
			}
		}
		return array();

	}

	/**
	 * Schema definition for the content of data that is being return on the callback.
	 *
	 * @return array Object describing the scheme.
	 */
	public function schema_callback() {

		return array(
			'categories' => array(
				'description' => __( 'Categories of all variable products in the cart.', 'sfn_cart_addons' ),
				'type'        => 'array',
				'readonly'    => true,
			),
		);
	}
}
