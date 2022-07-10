<?php
/**
 * Output template overrides in theme.
 *
 * @package WooCommerce/Bookings
 */

?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Templates"><h2><?php esc_html_e( 'WooCommerce Bookings template overrides', 'woocommerce-bookings' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows any files that are overriding the default WooCommerce Bookings template pages.', 'woocommerce-bookings' ) ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'woocommerce' ); ?></td>
			<td class="help">&nbsp;</td>
			<td>
				<?php
				$total_overrides = count( $override_files );
				for ( $i = 0; $i < $total_overrides; $i++ ) {
					$override = $override_files[ $i ];
					if ( $override['core_version'] && ( empty( $override['version'] ) || version_compare( $override['version'], $override['core_version'], '<' ) ) ) {
						$current_version = $override['version'] ? $override['version'] : '-';
						printf(
							/* Translators: %1$s: Template name, %2$s: Template version, %3$s: Core version. */
							esc_html__( '%1$s version %2$s is out of date. The core version is %3$s', 'woocommerce' ),
							'<code>' . esc_html( $override['file'] ) . '</code>',
							'<strong style="color:red">' . esc_html( $current_version ) . '</strong>',
							esc_html( $override['core_version'] )
						);
					} else {
						echo esc_html( $override['file'] );
					}
					if ( ( count( $override_files ) - 1 ) !== $i ) {
						echo ', ';
					}
					echo '<br />';
				}
				?>
			</td>
		</tr>
	</tbody>
</table>

