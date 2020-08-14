<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract( $args );

global $post;
$placeholder_txt = isset( $placeholder ) ? $placeholder : '';
$is_multiple     = isset( $multiple ) && $multiple;
$multiple        = ( $is_multiple ) ? 'true' : 'false';

$category_ids = get_post_meta( $post->ID, $id, true );
if ( ! is_array( $category_ids ) ) {
	$category_ids = explode( ',', get_post_meta( $post->ID, $id, true ) );
}
$json_ids = array();


if ( $category_ids ) {

	foreach ( $category_ids as $category_id ) {

		$cat_name = get_term_by( 'id', $category_id, 'product_cat' );
		if ( ! empty( $cat_name ) ) {
			$json_ids[ $category_id ] = '#' . $cat_name->term_id . '-' . $cat_name->name;
		}
	}
}

$select2_args = array(
	'id'               => $id,
	'class'            => 'wc-product-search',
	'name'             => $name,
	'data-multiple'    => true,
	'data-placeholder' => $placeholder_txt,
	'data-action'      => 'yit_role_price_json_search_product_categories',
	'data-selected'    => $json_ids,
	'value'            => implode( ',', array_keys( $json_ids ) ),
	'style'            => 'width:300px;'
);


$deps_html = '';
if ( function_exists( 'yith_field_deps_data' ) ) {
	$deps_html = yith_field_deps_data( $args );
} else {
	if ( isset( $deps ) ) {
		$deps_ids    = $deps['ids'];
		$deps_values = $deps['values'];
		$deps_html   = "data-field='$id' data-dep='{$deps_ids}' data-value='$deps_values'";
	}
}
?>

<div id="<?php echo $id ?>-container" <?php echo $deps_html ?> >

    <label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	<?php yit_add_select2_fields( $select2_args ); ?>
    <span class="desc inline"><?php echo $desc ?></span>
</div>

