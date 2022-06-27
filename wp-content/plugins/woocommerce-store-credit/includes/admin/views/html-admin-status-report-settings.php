<?php
/**
 * Admin View: Setting Status Report.
 *
 * @package WC_Store_Credit/Admin/Views
 * @since   4.1.1
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
			<th colspan="5" data-export-label="Store Credit">
				<h2><?php esc_html_e( 'Store Credit', 'woocommerce-store-credit' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows information about WooCommerce Store Credit.', 'woocommerce-store-credit' ) ); ?></h2>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Prices entered with tax"><?php esc_html_e( 'Prices entered with tax', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['prices_include_tax'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Show My Account"><?php echo esc_html_x( 'My Account', 'setting label', 'woocommerce-store-credit' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['show_my_account'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Delete after use"><?php echo esc_html_x( 'Delete after use', 'setting label', 'woocommerce-store-credit' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['delete_after_use'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Individual use"><?php echo esc_html_x( 'Individual use', 'setting label', 'woocommerce-store-credit' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['individual_use'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Include tax"><?php echo esc_html_x( 'Include tax', 'setting desc', 'woocommerce-store-credit' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['inc_tax'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Apply to shipping"><?php echo esc_html_x( 'Apply to shipping', 'setting label', 'woocommerce-store-credit' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['apply_to_shipping'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Coupon code format"><?php echo esc_html_x( 'Coupon code format', 'setting label', 'woocommerce-store-credit' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['code_format'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>:</td>
			<td class="help"></td>
			<td>
				<?php
				if ( empty( $data['overrides'] ) ) :
					esc_html_e( '-', 'woocommerce-store-credit' );
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
					<mark class="error"><span class="dashicons dashicons-warning"></span></mark> <a href="https://woocommerce.com/document/fix-outdated-templates-woocommerce/" target="_blank"><?php esc_html_e( 'Learn how to update', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?></a>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php
