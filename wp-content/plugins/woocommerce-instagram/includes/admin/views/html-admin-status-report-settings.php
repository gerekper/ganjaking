<?php
/**
 * Admin View: Settings Status Report.
 *
 * @package WC_Instagram/Admin/Views
 * @since   3.6.1
 */

/**
 * Globals.
 *
 * @global array $data The template data.
 */
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="5" data-export-label="Instagram"><h2><?php esc_html_e( 'Instagram', 'woocommerce-instagram' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows information about WooCommerce Instagram.', 'woocommerce-instagram' ) ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Connected"><?php esc_html_e( 'Connected to Facebook', 'woocommerce-instagram' ); ?>:</td>
			<td class="help"></td>
			<td colspan="3">
				<?php if ( $data['is_connected'] ) : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php else : ?>
					<mark class="error"><span class="dashicons dashicons-no-alt"></span></mark>
				<?php endif; ?>
			</td>
		</tr>

		<tr>
			<td data-export-label="Facebook page"><?php esc_html_e( 'Facebook page', 'woocommerce-instagram' ); ?>:</td>
			<td class="help"></td>
			<td colspan="3">
				<?php if ( $data['has_page'] ) : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php else : ?>
					<mark class="error"><span class="dashicons dashicons-no-alt"></span></mark>
				<?php endif; ?>
			</td>
		</tr>

		<tr>
			<td data-export-label="Catalog permalink"><?php esc_html_e( 'Catalog permalink', 'woocommerce-instagram' ); ?>:</td>
			<td class="help"></td>
			<td colspan="3"><?php echo esc_html( $data['catalog_permalink'] ); ?></td>
		</tr>

		<tr>
			<td data-export-label="Catalog interval"><?php esc_html_e( 'Catalog interval', 'woocommerce-instagram' ); ?>:</td>
			<td class="help"></td>
			<td colspan="3"><?php echo esc_html( $data['catalogs_interval'] ); ?>h</td>
		</tr>
	</tbody>
</table>
<?php
