<?php
/**
 * Subscription Products Framework Field Template.
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract( $field );
$product_string = array();

if ( ! empty( $value ) ) {
	foreach ( $value as $key => $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$product_string[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name() ) );
		} else {
			unset( $value[ $key ] );
		}
	}
}
?>
<?php if ( function_exists( 'yith_field_deps_data' ) ) : ?>
<div
	id="<?php echo esc_attr( $id ); ?>-container" <?php echo $custom_attributes; ?> <?php echo yith_field_deps_data( $field ); //phpcs:ignore ?>>
	<?php else : ?>
	<div id="<?php echo esc_attr( $id ); ?>-container"
		<?php
		if ( isset( $deps ) ) :
			?>
			data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $deps['ids'] ); ?>" data-value="<?php echo esc_attr( $deps['values'] ); ?>" <?php endif ?>>
		<?php endif; ?>


		<?php
		if ( function_exists( 'yit_add_select2_fields' ) ) {
			$args = array(
				'type'             => 'hidden',
				'class'            => 'wc-product-search',
				'id'               => $id,
				'name'             => $name,
				'data-placeholder' => esc_attr( $placeholder ),
				'data-allow_clear' => true,
				'data-selected'    => $product_string,
				'data-multiple'    => true,
				'value'            => is_array( $value ) ? implode( ',', $value ) : '',
				'style'            => 'width:90%',
				'data-action'      => 'ywsbs_json_search_ywsbs_products',
			);

			yit_add_select2_fields( $args );
		}
		?>
	</div>
