<?php
/**
 * Admin View: Product Catalogs Status Report.
 *
 * @package WC_Instagram/Admin/Views
 * @since   3.6.1
 */

/**
 * Template vars.
 *
 * @var array $data {
 *    The template data.
 *
 *    @type WC_Instagram_Product_Catalog[] $catalogs An array with the catalog objects.
 * }
 */
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="7" data-export-label="Product Catalogs"><h2><?php echo esc_html_x( 'Product catalogs', 'product catalogs: table column', 'woocommerce-instagram' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows the product catalogs created with WooCommerce Instagram.', 'woocommerce-instagram' ) ); ?></h2></th>
		</tr>
		<tr>
			<td><strong><?php echo esc_html_x( 'Title', 'product catalogs: table column', 'woocommerce-instagram' ); ?></strong></td>
			<td class="help"></td>
			<td><strong><?php echo esc_html_x( 'Products', 'product catalogs: table column', 'woocommerce-instagram' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Variations', 'product catalogs: table column', 'woocommerce-instagram' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Tax location', 'product catalogs: table column', 'woocommerce-instagram' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Stock', 'product catalogs: table column', 'woocommerce-instagram' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Last update (UTC)', 'product catalogs: table column', 'woocommerce-instagram' ); ?></strong></td>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $data['catalogs'] as $catalog ) :
			$last_modified = $catalog->get_file( 'xml' )->get_last_modified();
			$last_update   = ( $last_modified ? $last_modified->date( 'Y-m-d H:i' ) : '-' );
			$tax_location  = wc_instagram_get_formatted_product_catalog_tax_location( $catalog, '-' );
			$include_stock = $catalog->get_include_stock();

			$extra_info = array(
				'Feed URL: ' . esc_url( wc_instagram_get_product_catalog_url( $catalog->get_slug() ) ),
				'Variations: ' . wc_bool_to_string( $catalog->get_include_variations() ),
				'Tax location: ' . $tax_location,
				'Stock: ' . wc_bool_to_string( $include_stock ),
				'Last update (UTC): ' . $last_update,
				'Images: ' . $catalog->get_images_option(),
				'Description: ' . $catalog->get_description_field(),
				'Variation Description: ' . $catalog->get_variation_description_field(),
			);

			if ( $include_stock ) :
				$extra_info[] = 'Default stock quantity: ' . $catalog->get_stock_quantity();
				$extra_info[] = 'Backorder stock quantity: ' . $catalog->get_backorder_stock_quantity();
			endif;

			echo '<tr>';
			printf(
				'<td><a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a></td>',
				esc_url( wc_instagram_get_product_catalog_url( $catalog->get_slug() ) ),
				esc_attr( __( 'View product catalog feed', 'woocommerce-instagram' ) ),
				esc_html( $catalog->get_title() )
			);
			echo '<td></td>';
			echo '<td>' . count( $catalog->get_product_ids() ) . '<span style="display: none;"> product(s), ' . esc_html( join( ', ', $extra_info ) ) . '</span></td>';
			echo '<td>' . esc_html( wc_instagram_bool_to_string( $catalog->get_include_variations() ) ) . '</td>';
			echo '<td>' . esc_html( $tax_location ) . '</td>';
			echo '<td>' . esc_html( wc_instagram_bool_to_string( $include_stock ) ) . '</td>';
			echo '<td>' . esc_html( $last_update ) . '</td>';
			echo '</tr>';
		endforeach;
		?>
	</tbody>
</table>
<?php
