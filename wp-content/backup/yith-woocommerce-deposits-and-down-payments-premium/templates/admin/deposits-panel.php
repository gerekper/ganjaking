<?php
/**
 * Deposits Admin Panel
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

<div id="yith_wcdp_panel_deposits">
	<form id="plugin-fw-wc" class="general-deposits-table" method="post">

		<h3><?php _e( 'User role deposits', 'yith-woocommerce-deposits-and-down-payments' ) ?></h3>
		<div class="yith-users-roles-new-deposit">
			<h4><?php _e( 'Add new user role deposit', 'yith-woocommerce-deposits-and-down-payments' ) ?></h4>
			<?php
			yit_add_select2_fields( array(
				'name' => 'yith_new_user_role_deposit[role]',
				'class' => 'yith-roles-select wc-product-search',
				'data-action' => 'json_search_roles',
				'data-placeholder' => __( 'Search for a role&hellip;', 'yith-woocommerce-deposits-and-down-payments' ),
				'style' => 'width: 300px;'
			) );
			?>
			<select name="yith_new_user_role_deposit[type]" class="yith-deposit-type wc-enhanced-select" style="width: 100px;">
				<option value="amount"><?php _e( 'Amount', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				<option value="rate"><?php _e( 'Rate', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			</select>
			<input type="number" name="yith_new_user_role_deposit[value]" min="0" max="999999" step="any" value="" style="max-width: 100px;" />
			<input type="submit" class="yith-role-deposit-submit button button-primary" value="<?php echo esc_attr( __( 'Add Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) ?>" />
		</div>

		<?php $role_deposits_table->display() ?>

		<div class="clear separator"></div>

		<h3><?php _e( 'Product deposits', 'yith-woocommerce-deposits-and-down-payments' ) ?></h3>
		<div class="yith-products-new-deposit">
			<h4><?php _e( 'Add new product deposit', 'yith-woocommerce-deposits-and-down-payments' ) ?></h4>
			<?php
			yit_add_select2_fields( array(
				'name' => 'yith_new_product_deposit[product]',
				'class' => 'yith-products-select wc-product-search',
				'data-placeholder' => __( 'Search for a product&hellip;', 'yith-woocommerce-deposits-and-down-payments' ),
				'style' => 'width: 300px;'
			) );
			?>
			<select name="yith_new_product_deposit[type]" class="yith-deposit-type wc-enhanced-select" style="width: 100px;">
				<option value="amount"><?php _e( 'Amount', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				<option value="rate"><?php _e( 'Rate', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			</select>
			<input type="number" name="yith_new_product_deposit[value]" min="0" max="999999" step="any" value="" style="max-width: 100px;" />
			<input type="submit" class="yith-products-commission-submit button button-primary" value="<?php echo esc_attr( __( 'Add Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) ?>" />
		</div>

		<?php $product_deposits_table->display() ?>

		<div class="clear separator"></div>

		<h3><?php _e( 'Deposits for product categories', 'yith-woocommerce-deposits-and-down-payments' ) ?></h3>
		<div class="yith-products-categories-new-deposit">
			<h4><?php _e( 'Add new product category deposit', 'yith-woocommerce-deposits-and-down-payments' ) ?></h4>
			<select name="yith_new_product_category_deposit[term]" class="yith-categories-select wc-enhanced-select" data-allow_clear="1" data-placeholder="<?php _e( 'Search for a category&hellip;', 'yith-woocommerce-deposits-and-down-payments' ); ?>" style="width: 300px;">
				<option></option>
				<?php if( ! empty( $product_categories ) ): ?>
				<?php foreach( $product_categories as $term_id => $term_name ): ?>
						<option value="<?php echo esc_attr( $term_id )?>"><?php echo $term_name?></option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<select name="yith_new_product_category_deposit[type]" class="wc-enhanced-select yith-deposit-type" style="width: 100px;">
				<option value="amount"><?php _e( 'Amount', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
				<option value="rate"><?php _e( 'Rate', 'yith-woocommerce-deposits-and-down-payments' ) ?></option>
			</select>
			<input type="number" name="yith_new_product_category_deposit[value]" min="0" max="999999" step="any" value="" style="max-width: 100px;" />
			<input type="submit" class="yith-products-commission-submit button button-primary" value="<?php echo esc_attr( __( 'Add Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) ?>" />
		</div>

		<?php $product_category_deposits_table->display() ?>

	</form>
</div>