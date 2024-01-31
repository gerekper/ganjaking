<?php
/**
 * Admin View: Setting Status Report.
 *
 * @package WC_OD/Admin/Views
 * @since   1.9.4
 */

/**
 * Template vars.
 *
 * @var array $data The template data.
 */

$has_outdated = false;
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="5" data-export-label="Order Delivery">
				<h2><?php esc_html_e( 'Order Delivery', 'woocommerce-order-delivery' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows information about WooCommerce Order Delivery.', 'woocommerce-order-delivery' ) ); ?></h2>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $data['settings'] as $setting ) : ?>
			<tr>
				<td data-export-label="<?php echo esc_attr( $setting['key'] ); ?>"><?php echo esc_html( $setting['label'] ); ?>:</td>
				<td class="help"></td>
				<td>
					<?php
					if ( isset( $setting['type'] ) && 'bool' === $setting['type'] ) :
						WC_OD_Admin_System_Status::output_bool_html( $setting['value'] );
					else :
						echo esc_html( $setting['value'] );
					endif;
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>:</td>
			<td class="help"></td>
			<td>
				<?php
				if ( empty( $data['overrides'] ) ) :
					echo '-';
				else :
					$overrides_html = array();

					foreach ( $data['overrides'] as $override ) :
						if ( $override['core_version'] && ( empty( $override['version'] ) || version_compare( $override['version'], $override['core_version'], '<' ) ) ) :
							$has_outdated    = true;
							$current_version = ( $override['version'] ? $override['version'] : '-' );

							$overrides_html[] = sprintf(
								/* translators: %1$s: Template name, %2$s: Template version, %3$s: Core version. */
								esc_html__( '%1$s version %2$s is out of date. The core version is %3$s', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
								'<code>' . esc_html( $override['file'] ) . '</code>',
								'<strong style="color: #f00;">' . esc_html( $current_version ) . '</strong>',
								esc_html( $override['core_version'] )
							);
						else :
							$overrides_html[] = esc_html( $override['file'] );
						endif;
					endforeach;

					echo join( ',<br/>', $overrides_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				endif;
				?>
			</td>
		</tr>

		<?php if ( $has_outdated ) : ?>
		<tr>
			<td data-export-label="Outdated Templates"><?php esc_html_e( 'Outdated templates', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>:</td>
			<td class="help"></td>
			<td>
				<mark class="error"><span class="dashicons dashicons-warning"></span></mark> <a href="https://woo.com/document/fix-outdated-templates-woocommerce/" target="_blank"><?php esc_html_e( 'Learn how to update', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?></a>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php
