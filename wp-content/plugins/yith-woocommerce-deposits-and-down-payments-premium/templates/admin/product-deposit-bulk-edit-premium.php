 <?php
/**
 * Product deposit Quick/Bulk edit template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly
?>

 <fieldset class="inline-edit-col-right" xmlns="http://www.w3.org/1999/html">
	 <label for="_enable_deposit" class="alignleft">
		 <span class="title" ><?php _e( 'Deposit?', 'yith-woocommerce-deposits-and-down-payments' ) ?></span>
		 <span class="input-text-wrap">
			 <select name="_enable_deposit" id="enable_deposit" class="enable_deposit">
				 <option value="default" <?php selected( $enable_deposit == 'default' || empty( $enable_deposit ) ) ?> ><?php _e( 'Default', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="yes" <?php selected( $enable_deposit, 'yes' ) ?> ><?php _e( 'Yes', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="no" <?php selected( $enable_deposit, 'no' ) ?> ><?php _e( 'No', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			 </select>
		 </span>
	 </label>
	 <label for="_deposit_default" class="alignleft" style="display: block;">
		 <span class="title" ><?php _e( 'Default?', 'yith-woocommerce-deposits-and-down-payments' ) ?></span>
		 <span class="input-text-wrap">
			 <select name="_deposit_default" id="deposit_default" class="deposit_default">
				 <option value="default" <?php selected( $deposit_default == 'default' || empty( $deposit_default ) ) ?> ><?php _e( 'Default', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="yes" <?php selected( $deposit_default, 'yes' ) ?> ><?php _e( 'Yes', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="no" <?php selected( $deposit_default, 'no' ) ?> ><?php _e( 'No', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			 </select>
		 </span>
	 </label>
	 <br class="clear">
	 <label for="_enable_deposit" class="alignleft">
		 <span class="title"><?php _e( 'Force?', 'yith-woocommerce-deposits-and-down-payments' ) ?></span>
		 <span class="input-text-wrap">
			 <select name="_force_deposit" id="force_deposit" class="force_deposit">
				 <option value="default" <?php selected( $force_deposit == 'default' || empty( $force_deposit ) ) ?> ><?php _e( 'Default', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="yes" <?php selected( $force_deposit, 'yes' ) ?> ><?php _e( 'Force deposit', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="no" <?php selected( $force_deposit, 'no' ) ?> ><?php _e( 'Allow deposit', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			 </select>
		 </span>
	 </label>
	 <br class="clear">
	 <label for="_create_balance_orders" class="alignleft">
		 <?php /* @since 1.2.0 */ ?>
		 <span class="title"><?php _e( 'How to pay balance orders?', 'yith-woocommerce-deposits-and-down-payments' ) ?></span>
		 <span class="input-text-wrap">
			 <select name="_create_balance_orders" id="create_balance_orders" class="create_balance_orders">
				 <option value="default" <?php selected( $create_balance_orders == 'default' || empty( $force_deposit ) ) ?> ><?php _e( 'Default', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="yes" <?php selected( $create_balance_orders, 'yes' ) ?> ><?php _e( 'Let users pay the balance online (pending payment)', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="no" <?php selected( $create_balance_orders, 'no' ) ?> ><?php _e( 'Customers will pay the balance through other means (on hold)', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			 </select>
		 </span>
	 </label>
	 <br class="clear">
	 <label for="_product_note" class="alignleft">
		 <span class="title"><?php _e( 'Notes', 'yith-woocommerce-deposits-and-down-payments' ) ?></span>
		 <span class="input-text-wrap">
	        <textarea name="_product_note" id="_product_note" cols="30" rows="10"><?php echo esc_html( $product_note ) ?></textarea>
		 </span>
	 </label>
</fieldset>