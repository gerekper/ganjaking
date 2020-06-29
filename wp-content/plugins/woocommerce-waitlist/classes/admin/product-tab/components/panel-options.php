<?php
/**
 * HTML required for each single options panel on the waitlist tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$options     = get_post_meta( $product_id, 'wcwl_options', true );
$stock_level = get_option( 'woocommerce_waitlist_minimum_stock' );
if ( ! $options ) {
	$options = array( 'enable_waitlist' => 'true', 'enable_stock_trigger' => 'false', 'minimum_stock' => $stock_level );
	update_post_meta( $product_id, 'wcwl_options', $options );
}
?>
<div class="options wcwl_tab_content" data-panel="options">
	<fieldset>
		<input type="checkbox" <?php if ( 'true' == $options['enable_waitlist'] ) {
			echo 'checked="checked"';
		} ?> name="enable_waitlist"/>
		<label for="enable_waitlist">
			<?php _e( 'Enable users to join a waitlist for this product', 'woocommerce-waitlist' ); ?>
		</label>
	</fieldset>
	<fieldset>
		<input type="checkbox" <?php if ( 'true' == $options['enable_stock_trigger'] ) {
			echo 'checked="checked"';
		} ?> name="enable_stock_trigger"/>
		<label for="enable_stock_trigger">
			<?php _e( 'Check this box to override the default setting for the minimum stock required for this product before waitlist users are notified that it is back in stock', 'woocommerce-waitlist' ); ?>
		</label>
	</fieldset>
	<fieldset>
		<input type="number" data-default-stock="<?php echo $stock_level; ?>" value="<?php echo $options['minimum_stock']; ?>" name="minimum_stock" <?php if ( 'false' == $options['enable_stock_trigger'] ) {
			echo 'disabled';
		} ?>/>
		<label for="minimum_stock" <?php if ( 'false' == $options['enable_stock_trigger'] ) {
			echo 'class="wcwl_disabled"';
		} ?>>
			<?php _e( 'Minimum stock amount before users are notified that item is back in stock', 'woocommerce-waitlist' ); ?>
		</label>
	</fieldset>
	<button type="button" class="button primary" data-nonce="<?php echo wp_create_nonce( 'wcwl-update-nonce' ); ?>"><?php _e( 'Update Options', 'woocommerce-waitlist' ); ?></button>
</div>
