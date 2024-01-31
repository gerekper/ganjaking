<?php
/**
 * Handle the frontend scripts.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Frontend_Scripts.
 */
class WC_Store_Credit_Frontend_Scripts {

	/**
	 * Init.
	 *
	 * @since 3.7.0
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues scripts.
	 *
	 * @since 3.7.0
	 *
	 * @global WP_Post $post Current post.
	 */
	public static function enqueue_scripts() {
		global $post;

		self::register_styles();
		self::register_scripts();

		if ( is_account_page() ) {
			wp_enqueue_style( 'wc-store-credit' );
		}

		if ( is_cart() || is_checkout() ) {
			wp_enqueue_style( 'wc-store-credit' );
			wp_enqueue_script( 'wc-store-credit-cart' );
		}

		if ( is_product() ) {
			$product = wc_store_credit_get_product( $post->ID );

			if ( $product instanceof WC_Store_Credit_Product && ( $product->allow_different_receiver() || $product->allow_custom_amount() || $product->get_preset_amounts() ) ) {
				wp_enqueue_style( 'wc-store-credit' );
				wp_enqueue_script( 'wc-store-credit-single-product' );
			}
		}
	}

	/**
	 * Registers styles.
	 *
	 * @since 3.7.0
	 */
	private static function register_styles() {
		$styles = array(
			'general' => array(
				'handle' => 'wc-store-credit',
				'path'   => 'store-credit.css',
			),
		);

		foreach ( $styles as $data ) {
			self::register_style( $data );
		}
	}

	/**
	 * Registers scripts.
	 *
	 * @since 3.7.0
	 */
	private static function register_scripts() {
		$suffix  = wc_store_credit_get_scripts_suffix();
		$scripts = array(
			'single-product' => array(
				'handle' => 'wc-store-credit-single-product',
				'path'   => "single-product{$suffix}.js",
				'deps'   => array( 'jquery' ),
			),
			'cart'           => array(
				'handle' => 'wc-store-credit-cart',
				'path'   => "cart{$suffix}.js",
				'deps'   => array( 'jquery' ),
			),
		);

		foreach ( $scripts as $data ) {
			self::register_script( $data );
		}
	}

	/**
	 * Registers a style asset.
	 *
	 * @since 3.7.0
	 * @see wp_register_style()
	 *
	 * @param array $args The style data.
	 */
	private static function register_style( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'handle'  => '',
				'path'    => '',
				'deps'    => array(),
				'version' => WC_STORE_CREDIT_VERSION,
				'media'   => 'all',
			)
		);

		if ( empty( $args['handle'] ) || empty( $args['path'] ) ) {
			return;
		}

		$src = WC_STORE_CREDIT_URL . "assets/css/{$args['path']}";

		wp_register_style( $args['handle'], $src, $args['deps'], $args['version'], $args['media'] );
	}

	/**
	 * Registers a script asset.
	 *
	 * @since 3.7.0
	 * @see wp_register_script()
	 *
	 * @param array $args The script data.
	 */
	private static function register_script( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'handle'    => '',
				'path'      => '',
				'deps'      => array(),
				'version'   => WC_STORE_CREDIT_VERSION,
				'in_footer' => true,
			)
		);

		if ( empty( $args['handle'] ) || empty( $args['path'] ) ) {
			return;
		}

		$src = WC_STORE_CREDIT_URL . "assets/js/frontend/{$args['path']}";

		wp_register_script( $args['handle'], $src, $args['deps'], $args['version'], $args['in_footer'] );
	}
}

WC_Store_Credit_Frontend_Scripts::init();
