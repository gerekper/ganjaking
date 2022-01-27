<?php
/**
 * Class to handle the plugin routing.
 *
 * @package WC_Instagram/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Router class.
 */
class WC_Instagram_Router {

	/**
	 * The product catalog.
	 *
	 * @var WC_Instagram_Product_Catalog
	 */
	protected static $product_catalog;

	/**
	 * Initializes the routing.
	 *
	 * @since 3.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_rewrite_rules' ), 5 );
		add_filter( 'redirect_canonical', array( __CLASS__, 'redirect_canonical' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'reduce_query_load' ), 99 );
		add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ), 1 );
	}

	/**
	 * Registers custom rewrite rules.
	 *
	 * @since 3.0.0
	 *
	 * @global WP $wp The WordPress instance.
	 */
	public static function add_rewrite_rules() {
		global $wp;

		$wp->add_query_var( 'product_catalog' );

		$rewrite_slug = wc_instagram_get_product_catalog_rewrite_slug();

		add_rewrite_rule( $rewrite_slug . '/([^/]+)\.xml$', 'index.php?product_catalog=$matches[1]', 'top' );
	}

	/**
	 * Stop trailing slashes on product-catalog-xx.xml URLs.
	 *
	 * @since 3.0.0
	 *
	 * @param string $redirect The redirect URL currently determined.
	 * @return bool|string $redirect
	 */
	public static function redirect_canonical( $redirect ) {
		if ( get_query_var( 'product_catalog' ) ) {
			return false;
		}

		return $redirect;
	}

	/**
	 * Checks the current request URI and reduce the query load if possible.
	 *
	 * @since 3.0.0
	 */
	public static function reduce_query_load() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) || ! wc_instagram_is_connected() ) {
			return;
		}

		$request_uri = ltrim( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/' );

		if ( '.xml' !== substr( $request_uri, -4 ) ) {
			return;
		}

		$rewrite_path = ltrim( trailingslashit( wc_instagram_get_product_catalog_rewrite_slug() ), '/' );
		$index        = stripos( $request_uri, $rewrite_path );

		if ( false !== $index ) {
			$catalog_slug    = substr( $request_uri, $index + strlen( $rewrite_path ), -4 );
			$product_catalog = wc_instagram_get_product_catalog( $catalog_slug );

			if ( $product_catalog ) {
				self::$product_catalog = $product_catalog;

				remove_all_actions( 'widgets_init' );
			}
		}
	}

	/**
	 * Checks requests for potential product catalogs queries.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Query $query Main query instance.
	 */
	public static function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() || ! get_query_var( 'product_catalog' ) ) {
			return;
		}

		// Product catalog not found.
		if ( ! self::$product_catalog ) {
			$query->set_404();
			status_header( 404 );

			return;
		}

		$file = self::$product_catalog->get_file( 'xml' );

		self::send_headers( 'product_catalog' );

		echo $file->get_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		remove_all_actions( 'wp_footer' );
		die();
	}

	/**
	 * Sends the HTTP Headers for the specified content type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content_type The content type.
	 */
	private static function send_headers( $content_type ) {
		if ( headers_sent() ) {
			return;
		}

		$headers = self::get_headers_for( $content_type );

		foreach ( $headers as $header => $value ) {
			header( "{$header}: $value" );
		}

		status_header( 200 );
	}

	/**
	 * Gets the HTTP Headers for the specified content type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content_type The content type.
	 * @return array
	 */
	private static function get_headers_for( $content_type ) {
		$headers = array();

		if ( 'product_catalog' === $content_type ) {
			$headers = array(
				'Content-Type' => 'text/xml; charset=' . get_option( 'blog_charset' ),
				'X-Robots-Tag' => 'noindex',
			);
		}

		/**
		 * Filters the HTTP headers we send for the specified content type.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $headers      The HTTP headers we're going to send out.
		 * @param string $content_type The content type.
		 */
		return apply_filters( 'wc_instagram_http_headers', $headers, $content_type );
	}
}

WC_Instagram_Router::init();
