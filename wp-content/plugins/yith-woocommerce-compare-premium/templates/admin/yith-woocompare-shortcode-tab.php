<?php
/**
 * Shortcode tab template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.0.0
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

$message = __( 'Select products to add in compare table', 'yith-woocommerce-compare' )
?>

<div id="yith-woocompare-shortcode-tab" class="yith-plugin-fw  yit-admin-panel-container">
	<h2><?php esc_html_e( 'Build your own shortcode', 'yith-woocommerce-compare' ); ?></h2>
	<div class="shortcode-options">

		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="yith_products"><?php esc_html_e( 'Add products', 'yith-woocommerce-compare' ); ?></label>
					</th>
					<td>
						<?php
						yit_add_select2_fields(
							array(
								'class'            => 'wc-product-search yith_woocompare_tab_shortcode_products',
								'id'               => 'yith_products',
								'name'             => 'yith_products',
								'data-placeholder' => __( 'Search for a product..', 'yith-woocommerce-compare' ),
								'data-multiple'    => true,
								'data-action'      => 'woocommerce_json_search_products',
							)
						);
						?>
						<span class="description"><?php echo esc_html( $message ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="shortcode-preview">
			<?php echo '[yith_woocompare_table]'; ?>
		</div>
		<span class="description"><?php esc_html_e( 'Copy and paste this shortcode in your page.', 'yith-woocommerce-compare' ); ?></span>
	</div>
</div>
