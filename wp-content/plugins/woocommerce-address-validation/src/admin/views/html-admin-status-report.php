<?php
/**
 * WooCommerce Address Validation
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Address Validation to newer
 * versions in the future. If you wish to customize WooCommerce Address Validation for your
 * needs please refer to http://docs.woocommerce.com/document/address-validation/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Status report for the active provider.
 *
 * @type null|\WC_Address_Validation_Provider $provider
 *
 * @version 2.3.2
 * @since 2.3.2
 */

?>
<table
	 id="wc-address-validation"
	 class="wc_status_table widefat"
	 cellspacing="0">

	<thead>
		<tr>
			<th colspan="3" data-export-label="Address Validation">
				<h2><?php esc_html_e( 'Address Validation Provider', 'woocommerce-address-validation' ); ?><?php echo wc_help_tip( __( 'This sections shows information about the active Address Validation provider.', 'woocommerce-address-validation' ) ); ?></h2>
			</th>
		</tr>
	</thead>

	<tbody>

		<tr>
			<td data-export-label="Active Provider"><?php esc_html_e( 'Active Provider', 'woocommerce-address-validation' ); ?>:</td>
			<td class="help">&nbsp;</td>
			<td>
				<?php if ( ! empty( $provider ) ) :
					echo esc_html( $provider->get_title() ); ?>
				<?php else : ?>
					<mark class="error"><span class="dashicons dashicons-warning"></span><?php esc_html_e( 'No active provider available', 'woocommerce-address-validation' ); ?></mark>
				<?php endif; ?>
			</td>
		</tr>

		<?php if ( ! empty( $provider ) ) : ?>

			<tr>
				<td data-export-label="Configured"><?php esc_html_e( 'Configured', 'woocommerce-address-validation' ); ?>:</td>
				<td class="help">&nbsp;</td>
				<td>
					<?php if ( $provider->is_configured() ) : ?>
						<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
					<?php else : ?>
						<mark class="error"><span class="dashicons dashicons-no-alt"></span><?php esc_html_e( 'Not configured', 'woocommerce-address-validation' ); ?></mark>
					<?php endif; ?>
				</td>
			</tr>

			<?php if ( $provider->is_configured() ) : ?>

				<?php foreach ( $provider->get_form_fields() as $option_id => $field_data ) : ?>

					<?php if ( ! strpos( $option_id, 'key' ) && ! strpos( $option_id, 'secret' ) && ! strpos( $option_id, 'pass' ) ) : ?>

						<?php $value = $provider->get_option( $option_id ); ?>
						<?php $value = empty( $value ) && ! empty( $field_data['default'] ) ? $field_data['default'] : $value; ?>

						<?php if ( null !== $value && isset( $field_data['title'] ) ) : ?>

							<?php $value = is_array( $value ) ? trim( implode( ', ', $value ) ) : trim( $value ); ?>

							<tr>
								<td data-export-label="<?php esc_html_e( $field_data['title'] ); ?>"><?php esc_html_e( $field_data['title'] ); ?>:</td>
								<td class="help">&nbsp;</td>
								<?php if ( '' === $value ) : ?>
									<td><mark class="no"><span class="dashicons dashicons-minus"></span></td>
								<?php else : ?>
									<td><code><?php echo esc_html( ucfirst( $value ) ); ?></code></td>
								<?php endif; ?>
							</tr>

						<?php endif; ?>

					<?php endif; ?>

				<?php endforeach; ?>

				<?php if ( $general_settings = wc_address_validation()->get_admin_instance()->get_settings_page() ) : ?>

					<?php foreach ( $general_settings->get_settings() as $setting ) : ?>

						<?php if ( ! empty( $setting['id'] ) && 'wc_address_validation_active_provider' !== $setting['id'] ) : ?>

							<?php $value = get_option( $setting['id'] ); ?>

							<?php if ( $value && ! empty( $setting['name'] ) ) :

								$setting_name = str_replace( array( ':', '?' ), '', $setting['name'] ); ?>

								<tr>
									<td data-export-label="<?php echo esc_html( $setting_name ); ?>"><?php echo esc_html( $setting_name ); ?>:</td>
									<td class="help">&nbsp;</td>
									<td><code><?php echo esc_html( ucfirst( $value ) ); ?></code></td>
								</tr>

							<?php endif; ?>

						<?php endif; ?>

					<?php endforeach; ?>

				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>
	</tbody>
</table>
<?php
