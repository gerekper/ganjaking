<?php
/**
 * Photography loop collections tools.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$term      = get_queried_object();
$term_id   = $term->term_id;
$term_name = $term->slug;

if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
	$class = '';
} else {
	$class = ' legacy-quantity';
}

?>

<div class="tools">
	<div class="global-quantity<?php echo $class; ?>">
		<?php _e( 'Select', 'woocommerce-photography' ); ?>
		<?php
			wc_get_template( 'global/quantity-input.php', array(
				'input_name'  => '',
				'input_value' => apply_filters( 'wc_photography_collections_quantity_input_value', 0, $term_id, $term_name ),
				'classes'     => apply_filters( 'wc_photography_collections_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $term_id, $term_name ),
				'min_value'   => apply_filters( 'wc_photography_collections_quantity_input_min', 0, $term_id, $term_name ),
				'max_value'   => apply_filters( 'wc_photography_collections_quantity_input_max', '', $term_id, $term_name ),
				'input_id'    => uniqid( 'quantity_' ),
				'step'        => apply_filters( 'wc_photography_collections_quantity_input_step', 1 ),
				'pattern'     => apply_filters( 'wc_photography_collections_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
				'inputmode'   => apply_filters( 'wc_photography_collections_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
			) );
		?>
		<?php _e( 'of each photo', 'woocommerce-photography' ); ?>
	</div>

	<button type="submit" class="button"><?php _e( 'Add to cart', 'woocommerce-photography' ); ?></button>
</div>
