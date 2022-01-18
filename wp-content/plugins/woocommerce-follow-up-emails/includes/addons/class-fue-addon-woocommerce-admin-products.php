<?php

/**
 * Class FUE_Addon_Woocommerce_Admin_Products
 */
class FUE_Addon_Woocommerce_Admin_Products {

	/**
	 * Register hooks
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts') );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tab' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_lists' ), 10, 2 );

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'add_customer_to_lists' ) );
	}

	public function init() {
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panel' ) );
	}

	public function add_scripts() {
		$screen = get_current_screen();

		if ( $screen->id != 'product' ) {
			return;
		}

		wp_enqueue_script( 'fue-wc-products', FUE_TEMPLATES_URL .'/js/wc-products.js', array( 'jquery' ), FUE_VERSION );
		wp_localize_script( 'fue-wc-products', 'FUE_Products', array(
			'ajax_loader'            => FUE_TEMPLATES_URL .'/images/ajax-loader.gif',
			'add_new_fue_list_nonce' => wp_create_nonce( 'add_new_fue_list' ),
		) );
	}

	public function add_product_data_tab( $tabs ) {
		$tabs['follow-ups'] = array(
			'label'  => __( 'Follow-ups', 'follow_up_emails' ),
			'target' => 'follow_ups_product_data',
			'class'  => array('hide_if_external'),
		);

		return $tabs;
	}

	public function add_product_data_panel() {
		global $post;
		?>
		<div id="follow_ups_product_data" class="panel woocommerce_options_panel">

			<div class="options_group">
				<p><?php esc_html_e('Add purchaser of this product to the following list(s)', 'follow_up_emails'); ?></p>
				<p class="form-field fue_lists_field">
					<?php
					$product_lists = self::get_lists( $post->ID );
					$lists = Follow_Up_Emails::instance()->newsletter->get_lists();

					if ( !empty( $lists ) ) {
						foreach ( $lists as $list ) {
							echo '
								<label>
									<input type="checkbox" name="fue_lists[]" value="'. esc_attr( $list['id'] ) .'" '. checked( true, in_array( $list['id'], $product_lists ), false ) .' />
									'. esc_html( $list['list_name'] ) .'
								</label>
							<br/>';
						}
					}
					?>
				</p>
			</div>
			<div class="options_group">
				<p class="form-field">
					<label for="new_fue_list"><?php esc_html_e('New List', 'follow_up_emails'); ?></label>
					<input type="text" class="short" id="new_fue_list" style="width: 40%;" />
					<input type="button" id="add_new_fue_list" class="button add-new-fue-list" value="<?php esc_attr_e('Add', 'follow_up_emails'); ?>" />
				</p>
			</div>

		</div>
	<?php
	}

	/**
	 * Store the lists to the postmeta table as _fue_lists
	 *
	 * @param int $post_id
	 * @param $post
	 */
	public function save_lists( $post_id, $post ) {
		if ( isset( $_POST['fue_lists'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce handled before action.
			$lists = array_map( 'absint', $_POST['fue_lists'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce handled before action.
			update_post_meta( $post_id, '_fue_lists', $lists );
		} else {
			delete_post_meta( $post_id, '_fue_lists' );
		}
	}

	/**
	 * Add the billing email of the order to the lists assigned to all
	 * the purchased products in the order
	 *
	 * @param int $order_id
	 */
	public function add_customer_to_lists( $order_id ) {
		$order = WC_FUE_Compatibility::wc_get_order( $order_id );

		foreach ( $order->get_items() as $item ) {
			$product_lists = self::get_lists( $item['product_id'] );

			if ( empty( $product_lists ) ) {
				continue;
			}

			fue_add_subscriber_to_list( $product_lists, array(
				'email'      => WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ),
				'first_name' => WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ),
				'last_name'  => WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' ),
			) );
		}
	}

	/**
	 * Get the lists assigned to a product
	 *
	 * @param int $product_id
	 * @return array
	 */
	public static function get_lists( $product_id ) {
		$lists = get_post_meta( $product_id, '_fue_lists', true );

		if ( !$lists ) {
			$lists = array();
		}

		return $lists;
	}

}

return new FUE_Addon_Woocommerce_Admin_Products();
