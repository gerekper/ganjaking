<?php
/**
 * Status Report data for CP.
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.13.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><table class="wc_status_table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Composite Products"><h2><?php esc_html_e( 'Composite Products', 'woocommerce-composite-products' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Database Version"><?php esc_html_e( 'Database version', 'woocommerce-composite-products' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( esc_html__( 'The version of Composite Products reported by the database. This should be the same as the version of the extension.', 'woocommerce-composite-products' ) ); ?></td>
			<td>
			<?php

				$version = WC_CP()->version;

				// Remove suffixes.
				$version_parts = explode( '-', $version );
				$version       = sizeof( $version_parts ) === 2 ? $version_parts[ 0 ] : $version;

				if ( version_compare( $debug_data[ 'db_version' ], $version, '==' ) ) {
					echo '<mark class="yes">' . esc_html( $debug_data[ 'db_version' ] ) . '</mark>';
				} else {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html( $debug_data[ 'db_version' ] ) . ' - ' . __( 'Database version mismatch.', 'woocommerce-composite-products' ) . '</mark>';
				}
			?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Loopback Test"><?php esc_html_e( 'Loopback test', 'woocommerce-composite-products' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( esc_html__( 'Loopback requests are used by Composite Products to process tasks in the background.', 'woocommerce-composite-products' ) ); ?></td>
			<td>
			<?php

				if ( 'pass' === $debug_data[ 'loopback_test_result' ] ) {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				} elseif ( '' === $debug_data[ 'loopback_test_result' ] ) {
					echo '<mark class="no">&ndash;</mark>';
				} else {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . __( 'Loopback test failed.', 'woocommerce-composite-products' ) . '</mark>';
				}
			?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Template Overrides"><?php esc_html_e( 'Template overrides', 'woocommerce-composite-products' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( esc_html__( 'Shows any files overriding the default Composite Products templates.', 'woocommerce-composite-products' ) ); ?></td>
			<td><?php

				if ( ! empty( $debug_data[ 'overrides' ] ) ) {

					$total_overrides = count( $debug_data[ 'overrides' ] );

					for ( $i = 0; $i < $total_overrides; $i++ ) {

						$override = $debug_data[ 'overrides' ][ $i ];

						if ( $override[ 'core_version' ] && ( empty( $override[ 'version' ] ) || version_compare( $override[ 'version' ], $override[ 'core_version' ], '<' ) ) ) {

							$current_version = $override[ 'version' ] ? $override[ 'version' ] : '-';

							printf(
								/* Translators: %1$s: Template name, %2$s: Template version, %3$s: Core version. */
								esc_html__( '%1$s version %2$s (out of date)', 'woocommerce-composite-products' ),
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
