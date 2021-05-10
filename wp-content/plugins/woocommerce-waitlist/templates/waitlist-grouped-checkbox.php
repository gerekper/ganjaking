<?php
/**
 * The template for displaying the checkbox next to products listed on a grouped product page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/waitlist-grouped-checkbox.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 1.9.0
 */
$user_email  = $user ? $user->user_email : ''; ?>
<input type="hidden" class="wcwl_oos_product wcwl_nojs" name="wcwl_<?php echo $product_id; ?>" />
<?php // Don't display anything else if users are required to register (unnecessary clutter)
if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) && ! $user_email ) {
	return;
}
?>
<label for="wcwl_checked_<?php echo $product_id; ?>" class="woocommerce_waitlist_label wcwl_nojs" >
	<?php echo $button_text; ?>
	<input name="wcwl_checked_<?php echo $product_id; ?>" id="wcwl_checked_<?php echo $product_id; ?>" class="wcwl_checkbox wcwl_nojs" type="checkbox" <?php echo $checked; ?> data-product-id="<?php echo $product_id; ?>" data-wpml-lang="<?php echo $lang; ?>"/>
</label>