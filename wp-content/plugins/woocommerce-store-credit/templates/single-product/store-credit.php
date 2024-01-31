<?php
/**
 * Single product store credit.
 *
 * @package WC_Store_Credit/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wc-store-credit-product-container">
	<?php
	/**
	 * Hook: wc_store_credit_single_product_content.
	 *
	 * @since 4.0.0
	 *
	 * @hooked WC_Store_Credit_Product_Addons::preset_amounts_content - 10
	 * @hooked WC_Store_Credit_Product_Addons::custom_amount_content - 20
	 * @hooked WC_Store_Credit_Product_Addons::different_receiver_content - 30
	 */
	do_action( 'wc_store_credit_single_product_content' );
	?>
</div>
