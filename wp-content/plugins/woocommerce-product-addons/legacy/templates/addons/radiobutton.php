<?php foreach ( $addon['options'] as $i => $option ) :

	$price = apply_filters( 'woocommerce_product_addons_option_price',
		$option['price'] ? '(' . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ) ) . ')' : '',
		$option,
		$i,
		'radiobutton'
	);
	$current_value = 0;

	if ( isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) ) {
		$current_value = (
				isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) &&
				in_array( sanitize_title( $option['label'] ), $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] )
				) ? 1 : 0;
	}
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ) . '-' . $i; ?>">
		<label><input type="radio" class="addon addon-radio" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>[]" data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ); ?>" value="<?php echo sanitize_title( $option['label'] ); ?>" <?php checked( $current_value, 1 ); ?> /> <?php echo wptexturize( $option['label'] . ' ' . $price ); ?></label>
	</p>

<?php endforeach; ?>
