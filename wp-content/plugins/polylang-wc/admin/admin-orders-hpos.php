<?php
/**
 * @package Polylang-WC
 */

/**
 * Handles the language information displayed for orders when using HPOS.
 *
 * @since 1.9
 */
class PLLWC_Admin_Orders_HPOS extends PLLWC_Admin_Orders {

	/**
	 * Constructor.
	 *
	 * @since 1.9
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'pll_admin_ajax_params', array( $this, 'set_pll_order_id' ) );
		add_filter( 'pll_admin_current_language', array( $this, 'set_current_language' ) );

		add_action( 'woocommerce_after_order_object_save', array( $this, 'save_order_language' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_order_script' ) );
	}

	/**
	 * Adds the language columns to the orders list table.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function custom_columns() {
		$translated_order_types = $this->data_store->get_post_types( 'display' );

		foreach ( $translated_order_types as $translated_order_type ) {
			add_filter( 'woocommerce_' . $translated_order_type . '_list_table_columns', array( PLLWC()->admin_orders, 'add_order_column' ), 100 );
			add_action( 'woocommerce_' . $translated_order_type . '_list_table_custom_column', array( PLLWC()->admin_orders, 'order_column' ), 10, 2 );
		}
	}

	/**
	 * Displays the Languages metabox in HPOS context.
	 *
	 * @since 1.9
	 *
	 * @param WC_Order $order Order object.
	 * @return void
	 */
	public function order_language( $order ) {
		$this->display_language_metabox( $order->get_id() );
	}

	/**
	 * Returns a list of allowed screens.
	 *
	 * @since 1.9
	 *
	 * @return string[]
	 */
	protected function get_allowed_screens() {
		return array_map( 'wc_get_page_screen_id', parent::get_allowed_screens() );
	}

	/**
	 * Add pll_order_id parameter in the list of parameters of the admin ajax request.
	 *
	 * @since 1.9
	 *
	 * @param array $params List of parameters to add to the admin ajax request.
	 * @return array Modified list of parameters to add to the admin ajax request.
	 */
	public function set_pll_order_id( $params ) {
		if ( $this->is_allowed_screen() && isset( $_GET['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$params['pll_order_id'] = (int) $_GET['id']; // phpcs:ignore WordPress.Security.NonceVerification
		}
		return $params;
	}

	/**
	 * Sets the current language in order screen when using HPOS.
	 *
	 * @since 1.9
	 *
	 * @param PLL_Language|bool $current_language The current language already set.
	 * @return PLL_Language|bool
	 */
	public function set_current_language( $current_language ) {
		if ( ! empty( $_GET['page'] ) && ! empty( $_GET['id'] ) && is_numeric( $_GET['id'] ) && 'admin.php' === $GLOBALS['pagenow'] && 'wc-orders' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			// Case for the order checkout link.
			$lang = PLL()->model->post->get_language( (int) $_GET['id'] ); // phpcs:ignore WordPress.Security.NonceVerification
		} elseif ( wp_doing_ajax() && ! empty( $_GET['pll_order_id'] ) && is_numeric( $_GET['pll_order_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			// Case for filtering products search by the order language.
			$lang = PLL()->model->post->get_language( (int) $_GET['pll_order_id'] ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		return empty( $lang ) ? $current_language : $lang;
	}

	/**
	 * Saves order language from admin when HPOS is enabled and order custom tables authoritative.
	 *
	 * @since 1.9
	 *
	 * @param WC_Order $order Order object being saved.
	 * @return void
	 */
	public function save_order_language( $order ) {
		if ( ! isset( $_GET['id'], $_GET['page'], $_GET['action'], $_POST['post_lang_choice'], $_POST['_pll_nonce'] ) ) {
			return;
		}

		if ( ! $this->is_allowed_screen() || 'edit' !== $_GET['action'] ) {
			return;
		}

		check_admin_referer( 'pll_language', '_pll_nonce' );

		$new_lang   = PLL()->model->get_language( sanitize_key( $_POST['post_lang_choice'] ) );
		$order_lang = $this->data_store->get_language( $order->get_id() );

		if ( empty( $new_lang ) || ( ! empty( $order_lang ) && $new_lang->slug === $order_lang ) ) {
			return;
		}

		$this->data_store->set_language( $order->get_id(), $new_lang->slug );

		/*
		 * Due to how WooCommerce saves the order in a POST request without redirecting.
		 * Updating the current language with the new language is required
		 * as it's initialized with the language before the update.
		 */
		PLL()->curlang = $new_lang;
	}

	/**
	 * Enqueues order edit page script.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function enqueue_order_script() {
		if ( ! $this->is_allowed_screen() ) {
			return;
		}

		wp_dequeue_script( 'pll_classic-editor' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'pllwc_order', plugins_url( '/js/build/order' . $suffix . '.js', PLLWC_FILE ), array(), PLLWC_VERSION, true );
	}
}
