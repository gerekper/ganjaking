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
 * @var string $option_image
 * @var string $price
 * @var string $price_method
 * @var string $price_sale
 * @var string $price_type
 * @var string $currency
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

extract( $settings );

$hide_options_prices = apply_filters( 'yith_wapo_hide_option_prices', $hide_option_prices, $addon );
$hide_options_prices = wc_string_to_bool( $hide_options_prices );

$image_replacement = '';
if ( 'addon' === $addon_image_replacement ) {
	$image_replacement = $addon_image;
} elseif ( ! empty( $option_image ) && 'options' === $addon_image_replacement ) {
	$image_replacement = $option_image;
}

$image_replacement = is_ssl() ? str_replace( 'http://', 'https://', $image_replacement ) : $image_replacement;

$selected           = $addon->get_option( 'default', $x, 'no' ) === 'yes' ? 'selected="selected"' : '';
$option_description = $addon->get_option( 'description', $x );

$option_disabled = apply_filters( 'yith_wapo_select_option_disabled', false, $addon, $x );

?>

<option value="<?php echo esc_attr( $x ); ?>" <?php echo esc_attr( $selected ); ?>
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
	    data-image="<?php echo esc_attr( $option_image ); ?>"
	    data-replace-image="<?php echo esc_attr( $image_replacement ); ?>"
	    data-description="<?php echo wp_kses_post( $option_description ); ?>"
        <?php echo $option_disabled ? 'disabled' : ''; ?>
>
	<?php echo wp_kses_post( $addon->get_option( 'label', $x ) ); ?>
	<?php echo ! $hide_options_prices ? wp_kses_post( $addon->get_option_price_html( $x, $currency ) ) : ''; ?>
</option>
