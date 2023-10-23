<?php
/**
 * @package Polylang-WC
 */

/**
 * Handles the language information displayed for orders.
 *
 * @since 0.1
 * @since 1.9 Changed the class to abstract.
 */
abstract class PLLWC_Admin_Orders {
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
	 * Adds the language columns to the orders list table.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	abstract public function custom_columns();

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
	 * @since 1.9 The second param has been renamed to `$order` and accepts `WC_Order` and `int`.
	 *
	 * @param string       $column Column name.
	 * @param WC_Order|int $order  Order object when using HPOS, or order ID otherwise.
	 * @return void
	 */
	public function order_column( $column, $order ) {
		if ( $order instanceof WC_Order ) {
			$order = $order->get_id();
		}

		$lang = $this->data_store->get_language( $order );
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
	 * @param string $screen_id Screen id of the order edit page.
	 * @return void
	 */
	public function add_meta_boxes( $screen_id ) {
		if ( ! $this->is_allowed_screen( $screen_id ) ) {
			return;
		}

		remove_meta_box( 'ml_box', $screen_id, 'side' ); // Remove Polylang metabox if necessary.
		add_meta_box( 'pllwc_box', __( 'Language', 'polylang-wc' ), array( $this, 'order_language' ), $screen_id, 'side', 'high' );
		PLLWC_Filter_WC_Pages::init();
	}

	/**
	 * Displays the Languages metabox.
	 *
	 * @since 0.1
	 *
	 * @param object $order Order object.
	 * @return void
	 */
	abstract public function order_language( $order );

	/**
	 * Displays the Languages metabox.
	 *
	 * @since 1.9
	 *
	 * @param int $order_id Order id.
	 * @return void
	 */
	protected function display_language_metabox( $order_id ) {
		$lang = $this->data_store->get_language( $order_id );
		$lang = $lang ? $lang : pll_default_language();

		$dropdown = new PLL_Walker_Dropdown();

		// NOTE: the class "tags-input" allows to include the field in the autosave $_POST ( see autosave.js ).
		$args = array(
			'name'     => 'post_lang_choice',
			'class'    => 'post_lang_choice tags-input',
			'selected' => $lang,
			'flag'     => true,
		);

		$languages     = PLL()->model->get_languages_list();
		$dropdown_html = $dropdown->walk( $languages, -1, $args );

		wp_nonce_field( 'pll_language', '_pll_nonce' );

		$flags_data = array();
		foreach ( $languages as $language ) {
			$flags_data[ $language->slug ] = empty( $language->flag ) ? esc_html( $language->slug ) : $language->flag;
		}
		$flags_data = (string) wp_json_encode( $flags_data );

		printf(
			'<p><strong>%1$s</strong></p>
			<label class="screen-reader-text" for="post_lang_choice">%1$s</label>
			<div id="select-post-language" data-flags="%2$s">%3$s</div>',
			esc_html__( 'Language', 'polylang-wc' ),
			esc_attr( $flags_data ),
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

	/**
	 * Checks if the current screen is allowed.
	 *
	 * @since 1.9
	 *
	 * @param string $screen_id Optional screen id, defaults to the current screen.
	 * @return bool
	 */
	protected function is_allowed_screen( $screen_id = '' ) {
		if ( empty( $screen_id ) ) {
			$screen = get_current_screen();

			if ( empty( $screen ) ) {
				return false;
			}

			$screen_id = $screen->id;
		}

		return in_array( $screen_id, $this->get_allowed_screens(), true );
	}

	/**
	 * Returns a list of allowed screens.
	 *
	 * @since 1.9
	 *
	 * @return string[]
	 */
	protected function get_allowed_screens() {
		return $this->data_store->get_post_types();
	}
}
