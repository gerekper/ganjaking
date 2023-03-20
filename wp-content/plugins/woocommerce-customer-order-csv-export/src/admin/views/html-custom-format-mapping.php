<?php
/**
 * WooCommerce Customer/Order/Coupon Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * @type string $id the setting ID
 * @type array $headers table headers
 * @type array $mapping existing mappings
 */
?>
<tr valign="top">

	<td class="forminp wc-customer-order-csv-export-field-mapping-container" colspan="2">

		<input type="hidden" name="<?php echo esc_attr( $id ); ?>" value="" />

		<table class="wc-customer-order-csv-export-field-mapping widefat" cellspacing="0">
			<thead>
				<tr>
					<?php foreach ( $headers as $header => $label ) : ?>
						<th class="<?php echo esc_attr( $header ); ?>"><?php echo $label; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $mapping as $mapping_key => $column ) {

				echo '<tr class="field-mapping field-mapping-' . esc_attr( $mapping_key ) . '">';

				foreach ( $headers as $field => $label ) {

					switch ( $field ) {

						case 'sort' :
							echo '<td width="1%" class="sort"></td>';
							break;

						case 'sv-check-column' :
							echo '<td width="1%" class="check-column">
										<input type="checkbox" class="js-select-field" />
									</td>';
							break;

						case 'name' :
							echo '<td class="name">
										<input type="text" name="' . esc_attr( $id ) . '[' . esc_attr( $mapping_key ) . '][' . esc_attr( $field ) . ']" value="' . esc_attr( $column[ $field ] ) . '" class="js-field-name" />
									</td>';
							break;

						case 'source' :

							$html_column_options = '';
							$value               = isset( $column[ $field ] ) ? $column[ $field ] : '';
							$meta_key            = 'meta'   === $value && isset( $column['meta_key'] )     ? $column['meta_key']     : '';
							$static_value        = 'static' === $value && isset( $column['static_value'] ) ? $column['static_value'] : '';

							// trick WC into thinking the hidden placeholder row select is already enhanced.
							// this will allow us to later trigger enhancing the field when a new row is added,
							// so that event bindings work
							$enhanced = '__INDEX__' === $mapping_key ? 'enhanced' : '';

							foreach ( $data_options as $option ) {
								$html_column_options .= '<option value="' . esc_attr( $option ) . '" ' . selected( $value, $option, false ) . '>' . esc_html( $option ) .  '</option>';
							}

							?>

							<td class="data">

								<select name="<?php echo esc_attr( $options['id'] ); ?>[<?php echo esc_attr( $mapping_key ); ?>][source]" class="js-field-key wc-enhanced-select-nostd <?php echo $enhanced; ?>" data-placeholder="<?php esc_attr_e( 'Select a value', 'woocommerce-customer-order-csv-export' ); ?>">
									<?php echo $html_column_options; ?>
									<option value="meta" <?php selected( 'meta', $value ); ?>><?php esc_html_e( 'Meta field...', 'woocommerce-customer-order-csv-export' ); ?></option>
									<option value="static" <?php selected( 'static', $value ); ?>><?php esc_html_e( 'Static value...', 'woocommerce-customer-order-csv-export' ); ?></option>
								</select>

								<label class="js-field-meta-key-label <?php echo ( 'meta' !== $value ? 'hide' : '' ); ?>">
									<?php esc_html_e( 'Meta key:', 'woocommerce-customer-order-csv-export' ); ?>
									<input type="text" name="<?php echo esc_attr( $options['id'] ); ?>[<?php echo esc_attr( $mapping_key ); ?>][meta_key]" value="<?php echo esc_attr( $meta_key ); ?>" class="js-field-meta-key" />
								</label>

								<label class="js-field-static-value-label <?php echo ( 'static' !== $value ? 'hide' : '' ); ?>">
									<?php esc_html_e( 'Value:', 'woocommerce-customer-order-csv-export' ); ?>
									<input type="text" name="<?php echo esc_attr( $options['id'] ); ?>[<?php echo esc_attr( $mapping_key ); ?>][static_value]" value="<?php echo esc_attr( $static_value ); ?>" class="js-field-static-value" />
								</label>

							</td>

							<?php
							break;

						default :
							/**
							 * Allow actors to provide custom fields for column mapping
							 *
							 * @since 4.0.0
							 * @param array $column
							 * @param array $key
							 * @param array $options
							 */
							do_action( 'wc_customer_order_export_field_mapping_column_' . $field, $column, $mapping_key, $options );

							/** @deprecated since 4.1.0 */
							do_action( 'wc_customer_order_export_column_mapping_field_' . $field, $column, $mapping_key, $options );
							break;
					}
				}

				echo '</tr>';
			}
			?>
			<tr class="no-field-mappings <?php if ( count( $mapping ) > 1 ) { echo 'hide'; } ?>">
				<td colspan="<?php echo count( $headers ); ?>">
					<?php esc_html_e( 'There are no mapped columns. Click the Add Column button below to start mapping columns.', 'woocommerce-customer-order-csv-export' ); ?>
				</td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="<?php echo count( $headers ); ?>">
					<a class="button js-add-field-mapping" href="#"><?php esc_html_e( 'Add column', 'woocommerce-customer-order-csv-export' ); ?></a>
					<a class="button js-remove-field-mapping <?php if ( count( $mapping ) < 2 ) { echo 'hide'; } ?>" href="#"><?php esc_html_e( 'Remove selected column(s)', 'woocommerce-customer-order-csv-export' ); ?></a>
					<a class="button js-load-mapping button-secondary" href="#"><?php esc_html_e( 'Load column mapping', 'woocommerce-customer-order-csv-export' ); ?></a>
				</td>
			</tr>
			</tfoot>
		</table>
	</td>
</tr>

<?php
