<?php
/**
 * @package Polylang-WC
 */

/**
 * Handles the language information displayed for orders.
 *
 * @since 0.1
 */
class PLLWC_Admin_Orders {
	/**
	 * Order language data store.
	 *
	 * @var PLLWC_Order_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'order_language' );

		add_action( 'wp_loaded', array( $this, 'custom_columns' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'admin_order_actions' ) );
		add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'admin_order_actions' ) );
	}

	/**
	 * Removes the standard Polylang languages columns for the orders list table
	 * and replace them with one unique column.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function custom_columns() {
		$class = PLL()->filters_columns;
		remove_filter( 'manage_edit-shop_order_columns', array( $class, 'add_post_column' ), 100 );
		remove_action( 'manage_shop_order_posts_custom_column', array( $class, 'post_column' ) );

		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_column' ), 100 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'order_column' ), 10, 2 );
	}

	/**
	 * Adds the language column in the orders list table.
	 *
	 * @since 0.1
	 *
	 * @param string[] $columns List of table columns.
	 * @return string[] modified list of columns.
	 */
	public function add_order_column( $columns ) {
		// Don't add the column when the admin language filter is active.
		if ( empty( PLL()->curlang ) ) {
			$columns['language'] = '<span class="order_language tips" data-tip="' . __( 'Language', 'polylang-wc' ) . '">' . __( 'Language', 'polylang-wc' ) . '</span>';
		}

		return $columns;
	}

	/**
	 * Fills the language column of each order.
	 *
	 * @since 0.1
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Order ID.
	 * @return void
	 */
	public function order_column( $column, $post_id ) {
		$lang = $this->data_store->get_language( $post_id );
		$lang = PLL()->model->get_language( $lang );

		if ( 'language' === $column && $lang ) {
			echo $lang->flag ? $lang->flag . '<span class="screen-reader-text">' . esc_html( $lang->name ) . '</span>' : esc_html( $lang->slug ); // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Add the languages metabox for orders.
	 * Add filters specific to the orders page.
	 *
	 * @since 0.1
	 *
	 * @param string $post_type Post type name.
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {
		if ( 'shop_order' === $post_type ) {
			remove_meta_box( 'ml_box', $post_type, 'side' ); // Remove Polylang metabox.
			add_meta_box( 'pllwc_box', __( 'Language', 'polylang-wc' ), array( $this, 'order_language' ), $post_type, 'side', 'high' );

			// Translate the checkout page url in the order language for the customer payment page link included in pending orders details.
			add_filter( 'option_woocommerce_checkout_page_id', 'pll_get_post' );
		}
	}

	/**
	 * Displays the Languages metabox.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function order_language() {
		global $post_ID;

		$lang = $this->data_store->get_language( $post_ID );
		$lang = $lang ? $lang : pll_default_language();

		$dropdown = new PLL_Walker_Dropdown();

		// NOTE: the class "tags-input" allows to include the field in the autosave $_POST ( see autosave.js ).
		$args = array(
			'name'     => 'post_lang_choice',
			'class'    => 'post_lang_choice tags-input',
			'selected' => $lang,
			'flag'     => true,
		);

		$dropdown_html = $dropdown->walk( PLL()->model->get_languages_list(), -1, $args );

		wp_nonce_field( 'pll_language', '_pll_nonce' );

		printf(
			'<p><strong>%1$s</strong></p>
			<label class="screen-reader-text" for="post_lang_choice">%1$s</label>
			<div id="select-post-language">%2$s</div>',
			esc_html__( 'Language', 'polylang-wc' ),
			$dropdown_html // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * Add our pll_ajax_backend parameter to the WooCommerce admin order actions urls.
	 *
	 * @since 1.0.4
	 *
	 * @param array $actions Admin order actions.
	 * @return array
	 */
	public function admin_order_actions( $actions ) {
		if ( isset( $actions['status']['actions'] ) ) {
			$actions = $actions['status']['actions'];
		}

		foreach ( $actions as $key => $arr ) {
			if ( false !== strpos( $arr['url'], 'admin-ajax.php' ) ) {
				$actions[ $key ]['url'] = add_query_arg( 'pll_ajax_backend', 1, $arr['url'] );
			}
		}
		return $actions;
	}
}
