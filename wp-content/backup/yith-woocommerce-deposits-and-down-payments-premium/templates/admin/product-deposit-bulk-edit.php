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

 <fieldset class="inline-edit-col-center" >
	 <label for="_enable_deposit" class="alignleft">
		 <span class="title"><?php _e( 'Deposit?', 'yith-woocommerce-deposits-and-down-payments' ) ?></span>
		 <span class="input-text-wrap">
			 <select name="_enable_deposit" id="enable_deposit" class="enable_deposit">
				 <option value="default" <?php selected( $enable_deposit == 'default' || empty( $enable_deposit ) ) ?> ><?php _e( 'Default', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="yes" <?php selected( $enable_deposit, 'yes' ) ?> ><?php _e( 'Yes', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				 <option value="no" <?php selected( $enable_deposit, 'no' ) ?> ><?php _e( 'No', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
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
</fieldset>