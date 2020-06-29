<?php
/**
 * The Template for displaying select field.
 *
 * @version 3.0.0
 */

$loop = 0;
$field_name = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$required   = ! empty( $addon['required'] ) ? $addon['required'] : '';
$current_value = isset( $_POST['addon-' . sanitize_title( $field_name ) ] ) ? wc_clean( $_POST[ 'addon-' . sanitize_title( $field_name ) ] ) : '';
?>
<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo sanitize_title( $field_name ); ?>">
	<select class="wc-pao-addon-field wc-pao-addon-select" name="addon-<?php echo sanitize_title( $field_name ); ?>" id="addon-<?php echo sanitize_title( $field_name ); ?>" <?php if ( WC_Product_Addons_Helper::is_addon_required( $addon ) ) { echo 'required'; } ?>>

		<?php if ( empty( $required ) ) { ?>
			<option value=""><?php esc_html_e( 'None', 'woocommerce-product-addons' ); ?></option>
		<?php } else { ?>
			<option value=""><?php esc_html_e( 'Select an option...', 'woocommerce-product-addons' ); ?></option>
		<?php } ?>

		<?php foreach ( $addon['options'] as $i => $option ) {
			$loop++;
			$price        = ! empty( $option['price'] ) ? $option['price'] : '';
			$price_prefix = 0 < $price ? '+' : '';
			$price_type   = ! empty( $option['price_type'] ) ? $option['price_type'] : '';
			$price_raw    = apply_filters( 'woocommerce_product_addons_option_price_raw', $price, $option );
			$label        = ( '0' === $option['label'] ) || ! empty( $option['label'] ) ? $option['label'] : '';

			if ( 'percentage_based' === $price_type ) {
				$price_for_display = apply_filters( 'woocommerce_product_addons_option_price',
					$price_raw ? '(' . $price_prefix . $price_raw . '%)' : '',
					$option,
					$i,
					'select'
				);
			} else {
				$price_for_display = apply_filters( 'woocommerce_product_addons_option_price',
					$price_raw ? '(' . $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) ) . ')' : '',
					$option,
					$i,
					'select'
				);
			}

			$price_display = WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw );

			if ( 'percentage_based' === $price_type ) {
				$price_display = $price_raw;
			}
			?>
			<option data-raw-price="<?php echo esc_attr( $price_raw ); ?>" data-price="<?php echo esc_attr( $price_display ); ?>" data-price-type="<?php echo esc_attr( $price_type ); ?>" value="<?php echo sanitize_title( $label ) . '-' . $loop; ?>" data-label="<?php echo esc_attr( wptexturize( $label ) ); ?>"><?php echo wptexturize( $label ) . ' ' . $price_for_display; ?></option>
		<?php } ?>

	</select>
</p>
