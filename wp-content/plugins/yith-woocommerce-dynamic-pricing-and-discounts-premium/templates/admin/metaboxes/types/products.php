<?php
/**
 * Products.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
extract( $args );
$value          = get_post_meta( $post->ID, $id, true );
$value          = ! is_array( $value ) ? explode( ',', $value ) : $value;
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
<div id="<?php echo esc_attr( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); //phpcs:ignore ?>
	class="yith-plugin-fw-metabox-field-row">
	<?php else : ?>
	<div id="<?php echo esc_attr( $id ); ?>-container"
		<?php
		if ( isset( $deps ) ) :
			?>
			data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $deps['ids'] ); ?>" data-value="<?php echo esc_attr( $deps['values'] ); ?>" <?php endif ?>>
		<?php endif; ?>
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>

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
				'value'            => implode( ',', $value ),
				'style'            => 'width:90%',
			);

			yit_add_select2_fields( $args );
		}
		?>

	</div>
