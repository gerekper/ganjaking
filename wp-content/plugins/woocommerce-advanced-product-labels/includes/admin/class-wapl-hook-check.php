<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Hook check.
 *
 * Class for the WAPL hook check related functions.
 *
 * @author		Jeroen Sormani
 * @version		1.0.0
 */
class WAPL_Hook_Check {


	/**
	 * Constructor.
	 *
	 * @since NEWVERSION
	 */
	public function __construct() {
		// Check if the hook check is done
		add_action( 'wp', array( $this, 'hook_check_start' ) );

		// Hook check
		add_action( 'admin_notices', array( $this, 'missing_hook_notice' ) );
	}


	/**
	 * Hook check start.
	 *
	 * Make sure the hook check is performed correctly.
	 *
	 * @since NEWVERSION
	 */
	public function hook_check_start() {
		if ( ! isset( $_GET['wapl-hook-check'], $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'] ) ) {
			return;
		}

		ob_start();

		add_action( 'shutdown', array( $this, 'hook_check_shutdown' ), 0 );
	}


	/**
	 * Hook check end.
	 *
	 * Make sure hook check returns data and not page source.
	 *
	 * @since NEWVERSION
	 */
	function hook_check_shutdown() {
		ob_end_clean();

		$missing_hooks = array();
		if ( ( is_shop() || is_product_category() ) && ! did_action( 'woocommerce_before_shop_loop_item_title' ) ) {
			$missing_hooks[] = 'woocommerce_before_shop_loop_item_title';
		}

		if ( is_product() && ! did_action( 'woocommerce_product_thumbnails' ) ) {
			$missing_hooks[] = 'woocommerce_product_thumbnails';
		}

		wp_send_json( array(
			'success'       => true,
			'missing_hooks' => $missing_hooks
		) );
	}


	/**
	 * Missing hook notice.
	 *
	 * Check and add a notice when there are hooks missing.
	 *
	 * @since NEWVERSION
	 */
	public function missing_hook_notice() {
		if ( ! is_admin() || ! Woocommerce_Advanced_Product_Labels()->admin->is_wapl_page() ) {
			return;
		}

		$r = get_transient( 'wapl-hook-check' );
		if ( ! $r || ( current_user_can( 'manage_options' ) && isset( $_GET['force-wapl-hook-check'] ) ) ) {
			$r = $this->_do_hook_check();
		}

		set_transient( 'wapl-hook-check', $r, DAY_IN_SECONDS );

		if ( ! isset( $r->missing_hooks ) || empty( $r->missing_hooks ) ) {
			return;
		}

		if ( ( ! isset( $_GET['tab'] ) || $_GET['tab'] !== 'labels' ) && get_post_type() !== 'wapl' ) {
			return;
		}

		$url = add_query_arg( 'force-wapl-hook-check', 1 );
		?><div class="notice notice-warning is-dismissible">
			<p><?php
				_e( 'It appears your theme may be missing the required hook to display labels.', 'woocommerce-advanced-product-labels' );
				?> <a href="https://docs.woocommerce.com/document/advanced-product-labels-labels-not-showing/"><?php _e( 'Read documentation', 'woocommerce-advanced-product-labels' ); ?></a>.
				<a href="<?php echo esc_url( $url); ?>" class="alignright"><?php _e( 'Re-check', 'woocommerce-advanced-product-labels' ); ?></a>
			</p>
		</div><?php
	}


	/**
	 * Hook check.
	 *
	 * Load the shop page to check if the hook is being executed. Output is being
	 * suppressed by ob_start().
	 *
	 * @since NEWVERSION
	 */
	private function _do_hook_check( $force = false ) {
		$nonce = substr( wp_hash( wp_nonce_tick() . '|' . -1 . '|' . 0 . '|', 'nonce' ), - 12, 10 );
		$url = add_query_arg( array( 'wapl-hook-check' => 1, 'nonce' => $nonce ), wc_get_page_permalink( 'shop' ) );
		if ( $force ) {
			$url = add_query_arg( 'force', true, $url );
		}

		$r = wp_remote_get( $url );

		if ( is_wp_error( $r ) ) {
			return json_encode( array( 'success' => false, 'error' => $r->get_error_message() ) );
		}

		return json_decode( wp_remote_retrieve_body( $r ) );
	}


}
