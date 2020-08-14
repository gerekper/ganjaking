<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
wp_enqueue_script( 'woocommerce_admin' );
wp_enqueue_script( 'wc-enhanced-select' );
extract( $args );
$multiple           = isset( $multiple ) && $multiple;
$include_variations = isset( $include_variations ) ? $include_variations : false;

$default_data = array(
    'action'      => !!$include_variations ? 'woocommerce_json_search_products_and_variations' : 'woocommerce_json_search_products',
    'placeholder' => __( 'Search Products', 'yith-woocommerce-membership' )
);
$data         = ( !empty( $data ) && is_array( $data ) ) ? $data : array();
$data         = wp_parse_args( $data, $default_data );

$class = 'wc-product-search';
$name  = isset( $name ) ? $name : '';
$style = isset( $style ) ? $style : '';

if ( isset( $no_value ) && $no_value ) {
    $value = '';
}


if ( !is_array( $value ) && !!$value && is_string( $value ) ) {
    $product_ids = explode( ',', $value );
} else {
    $product_ids = (array) $value;
}

$data_selected = array();

foreach ( $product_ids as $product_id ) {
    $product = wc_get_product( $product_id );
    if ( is_object( $product ) ) {
        $title                        = $product->get_formatted_name();
        $data_selected[ $product_id ] = $title;
    }
}

if ( !$value ) {
    $value = '';
}
?>

<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps[ 'ids' ] ?>"
     data-value="<?php echo $deps[ 'values' ] ?>" <?php endif ?>>

    <label for="<?php echo $id ?>"><?php echo $label ?></label>

    <div style="width:400px;">
        <?php
        yit_add_select2_fields( array(
                                    'id'               => $id,
                                    'name'             => $name,
                                    'class'            => $class,
                                    'style'            => $style,
                                    'data-placeholder' => isset( $data[ 'placeholder' ] ) ? $data[ 'placeholder' ] : '',
                                    'data-selected'    => $data_selected,
                                    'data-allow_clear' => isset( $data[ 'allow_clear' ] ) ? $data[ 'allow_clear' ] : false,
                                    'data-multiple'    => $multiple,
                                    'data-action'      => isset( $data[ 'action' ] ) ? $data[ 'action' ] : false,
                                    'value'            => $value,
                                ) );
        ?>
    </div>

    <span class="description"><?php echo $desc ?></span>
</div>
