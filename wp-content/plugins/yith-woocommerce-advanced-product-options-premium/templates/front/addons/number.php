<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var int    $x
 * @var string $setting_hide_images
 * @var string $required_message
 * @var array  $settings
 * @var string $image_replacement
 * @var string $option_description
 * @var string $option_image
 * @var string $price
 * @var string $price_method
 * @var string $price_sale
 * @var string $price_type
 * @var string $currency
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

//Settings configuration.
extract($settings );

$hide_options_prices = apply_filters( 'yith_wapo_hide_option_prices', $hide_option_prices, $addon );

$hide_option_images  = wc_string_to_bool( $hide_option_images );
$hide_option_label   = wc_string_to_bool( $hide_option_label );
$hide_option_prices  = wc_string_to_bool( $hide_option_prices );
$hide_product_prices = wc_string_to_bool( $hide_product_prices );

$image_replacement = $addon->get_image_replacement( $addon, $x );

// Options configuration.
$default_value = '';
$minimum_value = '';
$maximum_value = '';

$required           = $addon->get_option( 'required', $x, 'no', false ) === 'yes';

$number_limit       = $addon->get_option( 'number_limit', $x );
if ( 'yes' === $number_limit ) {
	$minimum_value = $addon->get_option( 'number_limit_min', $x );
	$maximum_value = $addon->get_option( 'number_limit_max', $x );
}

$show_number_option = $addon->get_option( 'show_number_option', $x, 'default', false );
if ( 'default' === $show_number_option ) {
	$default_value = $addon->get_option( 'default_number', $x, '', false );
}

$default_value = apply_filters( 'yith_wapo_default_addon_number', $default_value, $addon );
$step_value    = apply_filters( 'yith_wapo_default_addon_number_step', '', $addon, $x );

$allow_decimals = apply_filters( 'yith_wapo_allow_decimals_number', false );

?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
     class="yith-wapo-option quantity-addon"
     data-replace-image="<?php echo esc_attr( $image_replacement ); ?>">

	<div class="label <?php echo wp_kses_post( ! empty( $addon_image_position ) ? 'position-' . $addon_image_position : '' ); ?>" for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">

		<div class="option-container">

			<!-- ABOVE / LEFT IMAGE -->
			<?php
			if ( 'above' === $addon_options_images_position || 'left' === $addon_options_images_position ) {
				//TODO: use wc_get_template() function.
				include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
			?>

			<!-- LABEL -->
			<label class="yith-wapo-addon-label" for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">
				<?php echo ! $hide_option_label ? wp_kses_post( $addon->get_option( 'label', $x ) ) : ''; ?>
				<?php echo $required ? '<span class="required">*</span>' : ''; ?>

				<!-- PRICE -->
				<?php echo ! $hide_option_prices && 'value_x_product' !== $price_method ? wp_kses_post( $addon->get_option_price_html( $x, $currency ) ) : ''; ?>
			</label>

			<!-- UNDER / RIGHT IMAGE -->
			<?php
			if ( 'under' === $addon_options_images_position || 'right' === $addon_options_images_position ) {
				//TODO: use wc_get_template() function.
				include YITH_WAPO_DIR . '/templates/front/option-image.php';
			}
			?>

		</div>
		<div class="input-number quantity">
			<!-- INPUT -->
			<input type="number"
			       id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
			       class="yith-wapo-option-value"
			       name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
			       placeholder="0"
				<?php if ( 'yes' === $number_limit ) : ?>
					min="<?php echo esc_attr( $minimum_value ); ?>"
					max="<?php echo esc_attr( $maximum_value ); ?>"
				<?php endif; ?>
				   data-price="<?php echo esc_attr( $price ); ?>"
				<?php
				if ( $price > 0 ) {
					?>
					data-price-sale="<?php echo esc_attr( $price_sale ); ?>"
					<?php
				}
				?>
				   data-price-type="<?php echo esc_attr( $price_type ); ?>"
				   data-price-method="<?php echo esc_attr( $price_method ); ?>"
				   data-first-free-enabled="<?php echo esc_attr( $first_options_selected ); ?>"
				   data-first-free-options="<?php echo esc_attr( $first_free_options ); ?>"
				   data-addon-id="<?php echo esc_attr( $addon->id ); ?>"
				<?php echo $addon->get_option( 'required', $x, 'no', false ) === 'yes' ? 'required' : ''; ?>
				<?php if ( '' !== $default_value ) : ?>
					value="<?php echo esc_attr( $default_value ); ?>"
				<?php endif ?>
				<?php if ( '' !== $step_value ) : ?>
					step="<?php echo esc_attr( $step_value ); ?>"
				<?php endif ?>
				   >

			<?php do_action( 'woocommerce_after_quantity_input_field' ); ?>
		</div>
	</div>

	<!-- TOOLTIP -->
	<?php if ( $addon->get_option( 'tooltip', $x ) !== '' ) : ?>
		<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>">
			<span><?php echo esc_attr( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
	<?php endif; ?>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description">
			<?php echo wp_kses_post( $option_description ); ?>
		</p>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if ( 'yes' === $sell_individually ) : ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
