<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? wc_clean( $_POST[ $addon_key ][ $option_key ] ) : '';
	$price = apply_filters( 'woocommerce_product_addons_option_price',
		$option['price'] ? '(' . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ) ) . ')' : '',
		$option,
		$key,
		'custom_pattern'
	);
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?></label>
		<?php endif; ?>
		<input type="text" pattern="<?php echo esc_attr( $pattern ); ?>" title="<?php echo esc_attr( $title ); ?>" class="input-text addon addon-custom addon-custom-pattern" data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ); ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" value="<?php echo esc_attr( $current_value ); ?>" <?php if ( ! empty( $option['max'] ) ) echo 'maxlength="' . $option['max'] .'"'; ?> />
	</p>

<?php endforeach; ?>
