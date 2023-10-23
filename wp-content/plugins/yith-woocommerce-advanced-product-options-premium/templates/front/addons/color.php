<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var YITH_WAPO_Addon $addon
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
$color_swatch_style = get_option( 'yith_wapo_style_color_swatch_style', 'rounded' );

$required   = $addon->get_option( 'required', $x, 'no', false ) === 'yes';
$checked    = $addon->get_option( 'default', $x, 'no', false ) === 'yes';
$selected   = $checked ? 'selected' : '';
$color_type = $addon->get_option( 'color_type', $x, 'color' );
$color_a    = $addon->get_option( 'color', $x, '', false );
$color_b    = $addon->get_option( 'color_b', $x, '', false );

$v3_check = false;
// Todo: remove this check in future version. "Dingle" was a typo error before 3.1 version.
$color_type = 'dingle' === $color_type ? 'single' : $color_type;
if ( 'single' === $color_type ) {
	$v3_check = true;
}
if ( 'color' === $color_type && '' === $color_b ) {
	$v3_check = true;
}

$background = ( $v3_check || '' === $color_b ) ? $color_a : '-webkit-linear-gradient( left, ' . esc_attr( $color_a ) . ' 50%, ' . esc_attr( $color_b ) . ' 50%)';

?>

<div
	class="yith-wapo-option selection-<?php echo esc_attr( $selection_type ); ?> <?php echo esc_attr( $selected ) . ' ' . esc_html( $color_swatch_style ); ?>"
	data-replace-image="<?php echo esc_attr( $image_replacement ); ?>"
>

	<input type="checkbox"
		id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
		class="yith-proteo-standard-checkbox yith-wapo-option-value"
		name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
		value="<?php echo esc_attr( $addon->get_option( 'label', $x ) ); ?>"
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
		<?php echo $required ? 'required' : ''; ?>
		<?php echo $checked ? 'checked="checked"' : ''; ?>
		style="display: none;">

	<!-- LABEL -->
	<div class="label <?php echo wp_kses_post( ! empty( $addon_image_position ) ? 'position-' . $addon_image_position : '' ); ?>">

		<div class="color-container">
			<span class="color" style="<?php echo in_array( $color_type, array( 'color', 'single', 'double' ) ) ? 'background:' . wp_kses_post( $background ) : ''; ?>">
				<?php
				if ( 'image' === $color_type ) {
					echo '<img src="' . esc_attr( $addon->get_option( 'color_image', $x ) ) . '">'; }
				?>
			</span>

            <div class="option-container">

                <!-- ABOVE / LEFT IMAGE -->
                <?php
                if ( 'above' === $addon_options_images_position || 'left' === $addon_options_images_position ) {
                    //TODO: use wc_get_template() function.
                    include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
                ?>

                <small><?php echo ! $hide_option_label ? wp_kses_post( $addon->get_option( 'label', $x ) ) : ''; ?></small>
                <?php echo ! $hide_option_prices ? wp_kses_post( $addon->get_option_price_html( $x, $currency ) ) : ''; ?>

                <!-- UNDER / RIGHT IMAGE -->
                <?php
                if ( 'under' === $addon_options_images_position || 'right' === $addon_options_images_position ) {
                    //TODO: use wc_get_template() function.
                    include YITH_WAPO_DIR . '/templates/front/option-image.php';
                }
                ?>

            </div>


		</div>

		<!-- TOOLTIP -->
		<?php if ( 'yes' === get_option( 'yith_wapo_show_tooltips' ) && '' !== $addon->get_option( 'tooltip', $x ) ) : ?>
			<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>">
			<span><?php echo wp_kses_post( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
		<?php endif; ?>

	</div>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description"><?php echo wp_kses_post( $option_description ); ?></p>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if ( 'yes' === $sell_individually ) : ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
