<?php

$loop = 0;
$current_value = isset( $_POST['addon-' . sanitize_title( $addon['field-name'] ) ] ) ? wc_clean( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) : '';
?>
<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
	<select class="addon addon-select" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>">

		<?php if ( empty( $addon['required'] ) ) : ?>
			<option value=""><?php _e('None', 'woocommerce-product-addons'); ?></option>
		<?php else : ?>
			<option value=""><?php _e('Select an option...', 'woocommerce-product-addons'); ?></option>
		<?php endif; ?>

		<?php foreach ( $addon['options'] as $i => $option ) :
			$loop ++;
			$price = apply_filters( 'woocommerce_product_addons_option_price',
				$option['price'] ? '(' . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ) ) . ')' : '',
				$option,
				$i,
				'select'
			);
			?>
			<option data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ); ?>" value="<?php echo sanitize_title( $option['label'] ) . '-' . $loop; ?>" <?php selected( $current_value, sanitize_title( $option['label'] ) . '-' . $loop ); ?>><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?></option>
		<?php endforeach; ?>

	</select>
</p>
