<?php
/**
 * Admin View: Select categories type
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<tr valign="top">
	<th scope="row" class="select_categories">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
	</th>
	<td class="forminp forminp-color plugin-option">

		<div id="<?php echo esc_attr( $id ); ?>-container" class="yit_options rm_option rm_input rm_text">
			<div class="option">
                <?php yit_add_select2_fields( array(
                    'class'             => 'wc-product-search',
                    'id'                => $id,
                    'name'              => $id,
                    'data-selected'     => $json_ids,
                    'data-placeholder'  => __( 'Search for a category...', 'yith-woocommerce-one-click-checkout' ),
                    'data-multiple'     => true,
                    'data-action'       => 'yith_wocc_search_product_cat',
                    'value'             => is_array( $categories ) ? implode( ',', $categories ) : $categories,
                    'style'             => 'width: 50%;'
                ) ); ?>
				<span class="description"><?php echo wp_kses_post( $desc ); ?></span>
			</div>
		</div>

	</td>
</tr>