<?php
/**
 * The Template for displaying start of field.
 *
 * @version 3.0.0
 */
global $product;

$price_display          = '';
$title_format           = ! empty( $addon['title_format'] ) ? $addon['title_format'] : '';
$addon_type             = ! empty( $addon['type'] ) ? $addon['type'] : '';
$addon_price            = ! empty( $addon['price'] ) ? $addon['price'] : '';
$addon_price_type       = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$adjust_price           = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$required               = ! empty( $addon['required'] ) ? $addon['required'] : '';
$has_per_person_pricing = ( isset( $addon['wc_booking_person_qty_multiplier'] ) && 1 === $addon['wc_booking_person_qty_multiplier'] ) ? true : false;
$has_per_block_pricing  = ( ( isset( $addon['wc_booking_block_qty_multiplier'] ) && 1 === $addon['wc_booking_block_qty_multiplier'] ) || ( isset( $addon['wc_accommodation_booking_block_qty_multiplier'] ) && 1 === $addon['wc_accommodation_booking_block_qty_multiplier'] ) ) ? true : false;
$product_title          = WC_Product_Addons_Helper::is_wc_gte( '3.0' ) ? $product->get_name() : $product->post_title;

if ( 'checkbox' !== $addon_type && 'multiple_choice' !== $addon_type && 'custom_price' !== $addon_type ) {
	$price_prefix = 0 < $addon_price ? '+' : '';
	$price_type   = $addon_price_type;
	$adjust_price = $adjust_price;
	$price_raw    = apply_filters( 'woocommerce_product_addons_price_raw', $addon_price, $addon );
	$required     = '1' == $required;

	if ( 'percentage_based' === $price_type ) {
		$price_display = apply_filters( 'woocommerce_product_addons_price',
			'1' == $adjust_price && $price_raw ? '(' . $price_prefix . $price_raw . '%)' : '',
			$addon,
			0,
			$addon_type
		);
	} else {
		$price_display = apply_filters( 'woocommerce_product_addons_price',
			'1' == $adjust_price && $price_raw ? '(' . $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) ) . ')' : '',
			$addon,
			0,
			$addon_type
		);
	}
}
?>

<div class="wc-pao-addon-container <?php echo $required ? 'wc-pao-required-addon' : ''; ?> wc-pao-addon wc-pao-addon-<?php echo sanitize_title( $name ); ?>" data-product-name="<?php echo esc_attr( $product_title ); ?>">

	<?php do_action( 'wc_product_addon_start', $addon ); ?>

	<?php
	if ( $name ) {
		if ( 'heading' === $addon_type ) {
		?>
			<h3 class="wc-pao-addon-heading"><?php echo wptexturize( $name ); ?></h3>
		<?php
		} else {
			switch ( $title_format ) {
				case 'heading':
					?>
					<h3 class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>"><?php echo wptexturize( $name ); ?> <?php echo $required ? '<em class="required" title="' . __( 'Required field', 'woocommerce-product-addons' ) . '">*</em>&nbsp;' : ''; ?><?php echo wp_kses_post( $price_display ); ?></h3>
					<?php
					break;
				case 'hide':
					?>
					<label class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>" style="display:none;"></label>
					<?php
					break;
				case 'label':
				default:
					?>
					<label for="<?php echo 'addon-' . esc_attr( wptexturize( $addon['field_name'] ) ); ?>" class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>"><?php echo wptexturize( $name ); ?> <?php echo $required ? '<em class="required" title="' . __( 'Required field', 'woocommerce-product-addons' ) . '">*</em>&nbsp;' : ''; ?><?php echo wp_kses_post( $price_display ); ?></label>
					<?php
					break;
			}
		}
	}
	?>
	<?php
	if ( $display_description ) {
		?>
		<?php echo '<div class="wc-pao-addon-description">' . wpautop( wptexturize( $description ) ) . '</div>'; ?>
	<?php }; ?>

	<?php do_action( 'wc_product_addon_options', $addon ); ?>
