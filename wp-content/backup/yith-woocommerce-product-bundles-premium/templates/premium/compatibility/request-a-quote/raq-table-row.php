<?php
/**
 * @var WC_Product $prod
 * @var string     $link
 * @var string     $title
 * @var string     $image
 * @var string     $quantity
 * @var array      $raq_content
 * @var string     $key
 *
 */
$raq      = array();
$_product = $prod;

$bundle_info  = isset( $raq_info[ 'yith-bundle-add-to-cart-params' ] ) ? $raq_info[ 'yith-bundle-add-to-cart-params' ] : array();

?>


<tr class="<?php echo esc_attr( apply_filters( 'yith_ywraq_item_class', 'cart_item yith-wcpb-child-of-bundle-table-item', $raq_content, $key ) ); ?>"
    <?php echo esc_attr( apply_filters( 'yith_ywraq_item_attributes', '', $raq_content, $key ) ); ?>>

    <td class="product-remove">
    </td>

    <td class="product-thumbnail" style="text-align:center;border: 1px solid #eee;">
        <?php echo $image; ?>
    </td>

    <td class="product-name">
        <?php
        if ( $_product->get_sku() != '' && get_option( 'ywraq_show_sku' ) == 'yes' ) {
            $title .= apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
        }
        ?>
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
        <?php
        // Meta data
        $item_data = array();

        // Variation data
        if ( !empty( $variation_id ) && !empty( $variations ) ) {

            foreach ( $variations as $name => $value ) {
	            $label = $name;
	            $name  = strtolower( $name );
	            $value = isset( $bundle_info[ 'yith_bundle_attribute_' . $name . '_' . $id ] ) ? $bundle_info[ 'yith_bundle_attribute_' . $name . '_' . $id ] : '';

	            if ( '' === $value ) {
		            continue;
	            }
	            $name     = 'attribute_' . $name;
	            $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

	            // If this is a term slug, get the term's nice name
	            if ( taxonomy_exists( $taxonomy ) ) {
		            $term = get_term_by( 'slug', $value, $taxonomy );
		            if ( ! is_wp_error( $term ) && $term && $term->name ) {
			            $value = $term->name;
		            }
		            $label = wc_attribute_label( $taxonomy );

	            } else {

		            if ( strpos( $name, 'attribute_' ) !== false ) {
			            $custom_att = str_replace( 'attribute_', '', $name );

			            if ( $custom_att != '' ) {
				            $label = wc_attribute_label( $custom_att );
			            }
		            }

	            }

	            $item_data[] = array(
		            'key'   => $label,
		            'value' => $value
	            );
            }
        }
        $item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data, $raq, $_product, false );


        // Output flat or in list format
        if ( sizeof( $item_data ) > 0 ) {
            foreach ( $item_data as $data ) {
                echo esc_html( $data[ 'key' ] ) . ': ' . wp_kses_post( $data[ 'value' ] ) . "\n";
            }
        }


        ?>
    </td>


    <td class="product-quantity">
        <?php echo $quantity; ?>
    </td>
    <?php if ( get_option( 'ywraq_hide_total_column', 'yes' ) == 'no' ): ?>
        <td class="product-subtotal">
        </td>
    <?php endif ?>
</tr>