<?php
/**
 * Class for handling the Account Funds deposit product in admin
 *
 * @package WC_Account_Funds/Admin
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Account_Funds_Admin_Product
 */
class WC_Account_Funds_Admin_Product {

	/**
	 * Constructor.
	 *
	 * WP hooks.
	 */
	public function __construct() {
		add_filter( 'product_type_selector', array( $this, 'product_types' ) );
		add_action( 'woocommerce_process_product_meta_deposit', array( $this, 'process_product_deposit' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_write_panel' ) );
	}

	/**
	 * Add deposit product type.
	 *
	 * @param array $types Product types.
	 * @return array
	 */
	public function product_types( $types ) {
		$types['deposit'] = __( 'Account Funds Deposit', 'woocommerce-account-funds' );

		return $types;
	}

	/**
	 * Save deposit product.
	 *
	 * @param int $post_id Post ID.
	 */
	public function process_product_deposit( $post_id ) {
		$product = wc_get_product( $post_id );
		$product->set_virtual( true );
		$product->save();
	}

	/**
	 * Hide fields with JS
	 */
	public function product_write_panel() {
		?>
		<script type="text/javascript">
			jQuery('.show_if_simple').addClass( 'show_if_deposit' );
			jQuery('#_virtual, #_downloadable').closest('label').addClass( 'hide_if_deposit' );
		</script>
		<?php
	}
}

new WC_Account_Funds_Admin_Product();
