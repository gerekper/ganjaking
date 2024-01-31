<?php
/**
 * Store Credit Product: Preset amounts.
 *
 * @package WC_Store_Credit/Templates
 * @version 4.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array $preset_amounts An array with the predefined credit amounts.
 * @var bool  $allow_custom   Whether the Store Credit product allows setting a custom amount.
 */
?>
<div class="store-credit-preset-amounts-container">
	<h3 class="store-credit-preset-amounts-title"><?php esc_html_e( 'Choose a different amount:', 'woocommerce-store-credit' ); ?></h3>

	<div class="store-credit-preset-amounts">
		<?php foreach ( $preset_amounts as $amount ) : ?>
			<a class="button store-credit-preset-amount" data-value="<?php echo esc_attr( $amount ); ?>">
				<?php echo wp_kses_post( wc_price( $amount, array( 'decimals' => 0 ) ) ); ?>
			</a>
		<?php endforeach; ?>

		<?php if ( $allow_custom ) : ?>
			<a class="button store-credit-preset-amount custom-amount" data-value="custom">
				<?php esc_html_e( 'Other', 'woocommerce-store-credit' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
