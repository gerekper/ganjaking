<?php
/**
 * Variable product add to cart in loop
 *
 * @author  Yithemes
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="variations_form cart in_loop" data-product_id="<?php echo $product_id ?>" data-active_variation="" data-product_variations="<?php echo $data_product_variations ?>">
	<?php foreach ( $attributes as $name => $options ) :

		// check for default attribute
		if ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) {
			$selected_value = $selected_attributes[ sanitize_title( $name ) ];
		} else {
			$selected_value = '';
		}

		?>
		<div class="<?php echo 'variations ' . sanitize_title( $name ); ?>">

			<select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>" data-attribute_name="attribute_<?php echo sanitize_title( $name ); ?>"
				<?php if( isset( $attributes_types[$name] ) ) echo 'data-type="' . $attributes_types[$name] . '"'; ?> data-default_value="<?php echo esc_attr( $selected_value ); ?>">
				<option value=""><?php echo apply_filters( 'yith_wccl_empty_option_loop_label', __( 'Choose an option', 'yith-woocommerce-color-label-variations' ) ); ?></option>
				<?php

				if ( is_array( $options ) ) {

					// Get terms if this is a taxonomy - ordered
					if ( taxonomy_exists( $name ) ) {

						$terms = wc_get_product_terms( $product_id, $name, array( 'fields' => 'all' ) );

						foreach ( $terms as $term ) {
							if ( ! in_array( $term->slug, $options ) ) {
								continue;
							}
							$value    = ywccl_get_term_meta( $term->term_id, $name . '_yith_wccl_value');
							$tooltip  = ywccl_get_term_meta( $term->term_id, $name . '_yith_wccl_tooltip');
							echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . ' data-value="'. $value . '" data-tooltip="' . $tooltip . '">' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
						}
					} else {

						foreach ( $options as $option ) {
							echo '<option value="' . esc_attr( $option ) . '"' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
						}
					}
				}
				?>
			</select>
		</div>
	<?php endforeach;?>
</div>