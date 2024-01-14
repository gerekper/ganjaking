<?php
/**
 * The template for displaying the price of an option
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-element-price.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $hide_amount ) || ! isset( $amount ) || ! isset( $original_amount ) ) {
	return;
}

if ( ! isset( $textbeforeprice ) ) {
	$textbeforeprice = '';
}
if ( ! isset( $textafterprice ) ) {
	$textafterprice = '';
}
?>
<?php if ( empty( $hide_amount ) || 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_hide_price_html' ) ) : ?>
<span class="tc-col-auto tc-epo-style-space tc-price-wrap<?php echo ( ! empty( $hide_amount ) ) ? ' tc-hidden' : ''; ?>">
	<?php if ( '' !== $textbeforeprice ) : ?>
	<span class="before-amount"><?php echo apply_filters( 'wc_epo_kses', esc_html( $textbeforeprice ), $textbeforeprice ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>&nbsp;
	<?php endif; ?>
	<span class="price tc-price">
		<span class="amount"><?php echo esc_html( $amount ); ?></span>
	</span>
	<?php if ( '' !== $textafterprice ) : ?>
		&nbsp;<span class="after-amount"><?php echo apply_filters( 'wc_epo_kses', esc_html( $textafterprice ), $textafterprice ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<?php endif; ?>
</span>
<?php endif; ?>
