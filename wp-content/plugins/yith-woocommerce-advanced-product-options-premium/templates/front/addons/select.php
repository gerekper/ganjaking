<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var string $setting_hide_images
 * @var string $required_message
 * @var array  $settings
 * @var int    $options_total
 * @var string $options_width_select_css
 * @var string $currency
*/

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

extract($settings );

$is_required = 'yes' === $addon_required ? 'required' : '';

?>

<select id="yith-wapo-<?php echo esc_attr( $addon->id ) ?>"
        name="yith_wapo[][<?php echo esc_attr( $addon->id ) ?>]"
        class="yith-wapo-option-value"
        data-addon-id="<?php echo esc_attr( $addon->id ) ?>"
        style="<?php echo esc_attr( $options_width_select_css ) ?>"
	<?php echo esc_attr( $is_required ) ?>
>
<option value="default"><?php echo esc_html( apply_filters( 'yith_wapo_select_option_label', __( 'Select an option', 'yith-woocommerce-product-add-ons' ) ) ); ?></option>
<?php
for ( $x = 0; $x < $options_total; $x++ ) {
	if ( file_exists( YITH_WAPO_DIR . '/templates/front/addons/select-option.php' ) ) {
		$enabled = $addon->get_option( 'addon_enabled', $x, 'yes', false );

		if ( 'yes' === $enabled ) {

			$option_show_image  = $addon->get_option( 'show_image', $x, false, false );
			$option_image       = $option_show_image ? $addon->get_option( 'image', $x ) : '';

			// todo: improve price calculation.
			$price_method = $addon->get_option( 'price_method', $x, 'free', false );
			$price_type   = $addon->get_option( 'price_type', $x, 'fixed', false );
			$price        = $addon->get_price( $x );
			$price_sale   = $addon->get_sale_price( $x );
			$price        = floatval( str_replace( ',', '.', $price ) );
			$price_sale   = '' !== $price_sale ? floatval( str_replace( ',', '.', $price_sale ) ) : '';

			// todo: improve price calculation.
			if ( 'free' === $price_method ) {
				$price      = '0';
				$price_sale = '0';
			} elseif ( 'decrease' === $price_method ) {
				$price      = $price > 0 ? - $price : 0;
				$price_sale = '0';
			} elseif ( 'product' === $price_method ) {
				$price      = $price > 0 ? $price : 0;
				$price_sale = '0';
			} else {
				$price      = $price > 0 ? $price : '0';
				$price_sale = $price_sale >= 0 ? $price_sale : 'undefined';
			}

			wc_get_template(
				'select-option.php',
				apply_filters(
					'yith_wapo_addon_select_option_args',
					array(
						'addon'               => $addon,
						'x'                   => $x,
						'setting_hide_images' => $setting_hide_images,
						'required_message'    => $required_message,
						'settings'            => $settings,
						// Addon options.
						'option_image' => is_ssl() ? str_replace( 'http://', 'https://', $option_image ) : $option_image,
						'price'        => $price,
						'price_method' => $price_method,
						'price_sale'   => $price_sale,
						'price_type'   => $price_type,
						'currency'     => $currency,
					),
					$addon
				),
				'',
				YITH_WAPO_DIR . '/templates/front/addons/'
			);
		}
	}
}

?>
</select>
<p class="option-description">

</p>
