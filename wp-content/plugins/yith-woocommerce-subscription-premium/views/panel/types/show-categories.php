<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$args = $field;

extract( $field );
$value = !!$value && is_array($value) ? $value : array();

$category_string = array();
$new_value = array();

if ( isset( $value ) ) {
	foreach ( $value as $key => $term_id ) {
		$term      = get_term_by( 'id', $term_id, 'product_cat' );
		if ( $term ) {
			$category_string[ $term->term_id ] = $term->formatted_name .= $term->name . ' (' . $term->count . ')';
			$new_value[]                       = $term->term_id;
		}
	}
}

$category_args = array(
	'type'             => 'hidden',
	'class'            => 'wc-product-search',
	'id'               => $id,
	'name'             => $name,
	'data-placeholder' => __( 'Search Category', 'yith-woocommerce-subscription' ),
	'data-allow_clear' => false,
	'data-selected'    => $category_string,
	'data-multiple'    => true,
	'data-action'      => 'ywsbs_search_categories',
	'value'            => implode( ',', $new_value ),
);

?>
<div id="<?php esc_attr_e( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); ?>  class="yith-plugin-fw-metabox-field-row" <?php echo $custom_attributes; ?> >
    <span class="show_category_list"><?php yit_add_select2_fields( $category_args ); ?></span>
</div>