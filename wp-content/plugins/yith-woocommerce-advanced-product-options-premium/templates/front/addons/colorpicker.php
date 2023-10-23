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
$required         = $addon->get_option( 'required', $x, 'no', false ) === 'yes';
$checked          = $addon->get_option( 'default', $x, 'no', false ) === 'yes';
$selected         = $checked ? 'selected' : '';
$colorpicker_show = $addon->get_option( 'colorpicker_show', $x, 'default_color' );
$colorpicker      = $addon->get_option( 'colorpicker', $x, '#ffffff' );
if ( 'placeholder' === $colorpicker_show ) {
	$colorpicker = '';
}
$placeholder   = $addon->get_option( 'placeholder', $x );
$default_color = 'default_color' === $colorpicker_show ? wp_kses_post( $colorpicker ) : '';

$colorpickerstyle = apply_filters( 'yith_wapo_color_picker_input', 'text' );
?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
	class="yith-wapo-option selection-<?php echo esc_attr( $selection_type ); ?> <?php echo esc_attr( $selected ); ?>"
	data-replace-image="<?php echo esc_attr( $image_replacement ); ?>">

	<div class="label <?php echo wp_kses_post( ! empty( $addon_image_position ) ? 'position-' . $addon_image_position : '' ); ?>">

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
				<?php echo $required && ! $hide_option_label && ! empty( wp_kses_post( $addon->get_option( 'label', $x ) ) ) ? '<span class="required">*</span>' : ''; ?>

				<!-- PRICE -->
				<?php echo ! $hide_option_prices ? wp_kses_post( $addon->get_option_price_html( $x, $currency ) ) : ''; ?>
			</label>

			<!-- UNDER / RIGHT IMAGE -->
			<?php
			if ( 'under' === $addon_options_images_position || 'right' === $addon_options_images_position ) {
				//TODO: use wc_get_template() function.
				include YITH_WAPO_DIR . '/templates/front/option-image.php';
			}
			?>
		</div>
		<div class="yith-wapo-colorpicker-container">
			<!-- Colorpicker -->
			<input type="<?php echo esc_attr($colorpickerstyle); ?>"
				class="wp-color-picker yith-wapo-option-value"
				id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
				name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
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
				data-addon-id="<?php echo esc_attr( $addon->id ); ?>"
				data-addon-colorpicker-show="<?php echo esc_attr( $colorpicker_show ); ?>"
				<?php if ( ! empty( $default_color ) ) : ?>
				data-default-color="<?php echo wp_kses_post( $default_color ); ?>"
				<?php endif; ?>
				data-addon-placeholder="<?php echo esc_attr( $placeholder ); ?>"
			<?php echo $addon->get_option( 'required', $x, 'no', false ) === 'yes' ? 'required' : ''; ?>
			/>
		</div>

	</div>

	<!-- TOOLTIP -->
	<?php if ( 'yes' === get_option( 'yith_wapo_show_tooltips' ) && '' !== $addon->get_option( 'tooltip', $x ) ) : ?>
		<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>">
			<span><?php echo wp_kses_post( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
	<?php endif; ?>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description"><?php echo wp_kses_post( $option_description ); ?></p>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if ( 'yes' === $sell_individually ) : ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
