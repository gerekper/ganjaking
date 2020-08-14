<?php
/**
 * Brands field.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array  $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

extract( $args );

$value = get_post_meta( $post->ID, $id, true );
$value = ! is_array( $value ) ? explode( ',', $value ) : $value;

// Products.
$tags_string = array();
if ( ! empty( $value ) ) {
	foreach ( $value as $key => $term_id ) {
		$search_by = is_numeric( $term_id ) ? 'id' : 'slug';
		$term      = get_term_by( $search_by, $term_id, YITH_WCBR::$brands_taxonomy );
		if ( $term ) {
			$tags_string[ $term->term_id ] = $term->formatted_name .= $term->name . ' (' . $term->count . ')';
		} else {
			unset( $value[ $key ] );
		}
	}
}

?>

<?php if ( function_exists( 'yith_field_deps_data' ) ) : ?>
<div id="<?php echo esc_attr( $id ); ?>-container" <?php echo yith_field_deps_data( $args );  //phpcs:ignore?> class="yith-plugin-fw-metabox-field-row">
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
			'class'            => 'yith-brands-select wc-product-search',
			'id'               => $id,
			'name'             => $name,
			'data-placeholder' => esc_attr( $placeholder ),
			'data-allow_clear' => true,
			'data-selected'    => $tags_string,
			'data-multiple'    => true,
			'data-action'      => 'ywdpd_brand_search',
			'value'            => implode( ',', $value ),
			'style'            => 'width:90%',
		);

		yit_add_select2_fields( $args );
	}
	?>

</div>
