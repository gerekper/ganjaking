<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

$args = $field;
extract( $field );


$value = !!$value && is_array($value) ? $value : array();

$category_string = array();
$new_value = array();

if ( isset( $value['categories'] ) ) {
	foreach ( $value['categories'] as $key => $term_id ) {
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
	'id'               =>  $id. "[categories]",
	'name'             =>  $name. "[categories]",
	'data-placeholder' => __( 'Search Category', 'yith-point-of-sale-for-woocommerce' ),
	'data-allow_clear' => false,
	'data-selected'    => $category_string,
	'data-multiple'    => true,
	'data-action'      => 'yith_pos_search_categories',
	'value'            => implode( ',', $new_value ),
);
?>
<div id="<?php esc_attr_e( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); ?> class="yith-plugin-fw-metabox-field-row">
    <span class="show_category_label"><?php  echo __( 'Category to', 'yith-point-of-sale-for-woocommerce' )?></span>
    <span class="show_category_select"><?php  yith_plugin_fw_get_field(
		array(
			'id'      => $id . "[type]",
			'name'             =>  $name. "[type]",
			'class'   => 'wc-enhanced-select no-bottom',
			'type'    => 'select',
			'label'   => '',
			'options' => array(
				'include' => __( 'Include', 'yith-point-of-sale-for-woocommerce' ),
				'exclude' => __( 'Exclude', 'yith-point-of-sale-for-woocommerce' ),
			),
			'value' => isset( $value['type'] ) ? $value['type'] : 'include'

        ), true, false ); ?></span>
    <span class="show_category_list"><?php yit_add_select2_fields( $category_args ); ?></span>
</div>