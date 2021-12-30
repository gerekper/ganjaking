<?php
/**
 * Status Report data for PB.
 *
 * @package  WooCommerce Product Bundles
 * @since    5.7.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><table class="wc_status_table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Product Bundles"><h2><?php esc_html_e( 'Product Bundles', 'woocommerce-product-bundles' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Database Version"><?php esc_html_e( 'Database version', 'woocommerce-product-bundles' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( esc_html__( 'The version of Product Bundles reported by the database. This should be the same as the plugin version.', 'woocommerce-product-bundles' ) ); ?></td>
			<td>
			<?php

				if ( version_compare( $debug_data[ 'db_version' ], WC_PB()->plugin_version( true ), '==' ) ) {
					echo '<mark class="yes">' . esc_html( $debug_data[ 'db_version' ] ) . '</mark>';
				} else {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html( $debug_data[ 'db_version' ] ) . ' - ' . __( 'Database version mismatch.', 'woocommerce-product-bundles' ) . '</mark>';
				}
			?>
			</td>
		</tr>
		<tr>
 			<td data-export-label="Loopback Test"><?php esc_html_e( 'Loopback test', 'woocommerce-product-bundles' ); ?>:</td>
 			<td class="help"><?php echo wc_help_tip( esc_html__( 'Loopback requests are used by Product Bundles to process tasks in the background.', 'woocommerce-product-bundles' ) ); ?></td>
 			<td>
 			<?php

 				if ( 'pass' === $debug_data[ 'loopback_test_result' ] ) {
 					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
 				} elseif ( '' === $debug_data[ 'loopback_test_result' ] ) {
 					echo '<mark class="no">&ndash;</mark>';
 				} else {
 					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . __( 'Loopback test failed.', 'woocommerce-product-bundles' ) . '</mark>';
 				}
 			?>
 			</td>
 		</tr>
		<tr>
			<td data-export-label="Template Overrides"><?php esc_html_e( 'Template overrides', 'woocommerce-product-bundles' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( esc_html__( 'Shows any files overriding the default Product Bundles templates.', 'woocommerce-product-bundles' ) ); ?></td>
			<td><?php

				if ( ! empty( $debug_data[ 'overrides' ] ) ) {

					$total_overrides = count( $debug_data[ 'overrides' ] );

					for ( $i = 0; $i < $total_overrides; $i++ ) {

						$override = $debug_data[ 'overrides' ][ $i ];

						if ( $override[ 'core_version' ] && ( empty( $override[ 'version' ] ) || version_compare( $override[ 'version' ], $override[ 'core_version' ], '<' ) ) ) {

							$current_version = $override[ 'version' ] ? $override[ 'version' ] : '-';

							printf(
								/* Translators: %1$s: Template name, %2$s: Template version, %3$s: Core version. */
								esc_html__( '%1$s version %2$s (out of date)', 'woocommerce-product-bundles' ),
								'<code>' . esc_html( $override[ 'file' ] ) . '</code>',
								'<strong style="color:red">' . esc_html( $current_version ) . '</strong>',
								esc_html( $override[ 'core_version' ] )
							);

						} else {
							echo '<code>' . esc_html( $override[ 'file' ] ) . '</code>';
						}

						if ( ( count( $debug_data[ 'overrides' ] ) - 1 ) !== $i ) {
							echo ', ';
						}

						echo '<br />';
					}

				} else {
					?>&ndash;<?php
				}
			?></td>
		</tr>
	</tbody>
</table>
