<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH
 * @package YITH License & Upgrade Framework
 */

$default_marketplace           = 'yith';
$to_active_products            = $this->get_to_active_products();
$activated_products            = $this->get_activated_products();
$no_active_products            = $this->get_no_active_licence_key();
$expired_products              = isset( $no_active_products['106'] ) ? $no_active_products['106'] : array();
$banned_products               = isset( $no_active_products['107'] ) ? $no_active_products['107'] : array();
$notice                        = isset( $notice ) ? $notice : '';
$notice_class                  = ! empty( $notice ) ? 'yith-notice notice-success visible' : 'yith-notice notice-success';
$num_members_products_activate = $this->get_number_of_membership_products();
$debug                         = isset( $_REQUEST['yith-license-debug'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$renew_url                     = 'https://yithemes.com/my-account/my-subscriptions/';
$to_active_products_count      = count( $to_active_products );
?>

<div class="yit-container product-licence-activation yith-plugin-ui">
	<h2 id="yith-page-title"><?php esc_html_e( 'YITH License Activation', 'yith-plugin-upgrade-fw' ); ?></h2>
	<!-- To Active Products -->

	<div class="to-active-wrapper">
		<h3 id="products-to-active" class="to-active">
			<?php echo esc_html_x( 'Activate your licenses', 'Page Title', 'yith-plugin-upgrade-fw' ); ?>
			<span class="spinner"></span>
		</h3>
		<span id="yith-no-license-to-enabled-message"
				class="yith-license-<?php echo empty( $to_active_products ) ? 'visible' : 'hide'; ?>">
			<?php echo esc_html_x( 'Your licenses are active.', 'License section message', 'yith-plugin-upgrade-fw' ); ?>
		</span>
		<div id="yith-license-from-wrapper"
				class="<?php echo empty( $to_active_products ) ? 'yith-license-hide' : 'yith-license-visible'; ?>">
			<p id="yith-licence-issue-how-to">
				<?php
				$how_to = sprintf(
					'%s <a href="%s" target="_blank"  rel="nofollow noopener">%s</a>.',
					esc_html_x( 'Are you having issues with the license activation?', '[Part of]: Are you having issues with the license activation? Read this article', 'yith-plugin-upgrade-fw' ),
					esc_html( '//support.yithemes.com/hc/en-us/articles/360012568594-License-activation-issues' ),
					esc_html_x( 'Read this article', '[Part of]: Are you having issues with the license activation? Read this article', 'yith-plugin-upgrade-fw' )
				);
				echo $how_to; //@codingStandardsIgnoreLine
				?>
			</p>
			<form id="yith-license-activation" class="to-active-form count-<?php echo intval( $to_active_products_count ); ?>"
					method="post" action="<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>"
					data-count="<?php echo intval( $to_active_products_count ); ?>">
				<?php if ( $debug ) : ?>
					<input type="hidden" name="debug" value="yes"/>
				<?php endif ?>
				<div class="to-active-table">
					<p class="yith-license-form-row product-name yith-products-list-wrapper">
						<label for="yith-products-list" class="yith-select-plugin">
							<?php printf( "%s:", esc_html( _nx( 'Plugin', 'Choose the plugin', $to_active_products_count, 'Form Label', 'yith-plugin-upgrade-fw' ) ) ); ?>
						</label>
						<select
							<?php
							if ( 1 === $to_active_products_count ) {
								echo 'disabled="disabled"';
							}
							?>
								autocomplete="off"
								id="yith-products-list"
								name="product_name"
								class="wc-enhanced-select yith-products-list"
						>
							<?php foreach ( $to_active_products as $init => $info ) : ?>
								<option
									<?php echo isset( $_GET['plugin'] ) && esc_html( $_GET['plugin'] ) == $info['product_id'] ? 'selected' : ''; ?>
									data-textdomain="<?php echo esc_attr( $info['TextDomain'] ); ?>"
									data-init="<?php echo esc_attr( $init ); ?>"
									data-marketplace="<?php echo esc_attr( $info['marketplace'] ); ?>">
									<?php echo esc_html( $this->display_product_name( $info['Name'] ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<span class="error-message product"></span>
					</p>
					<p class="yith-license-form-row yith-account-email-wrapper">
						<label for="yith-account-email" class="yith-email">
							<?php
							$new_email_url = sprintf(
								'%s:',
								esc_html_x( 'E-mail account with YITH', 'Link on activation license panel', 'yith-plugin-upgrade-fw' )
							);
							echo $new_email_url; //@codingStandardsIgnoreLine
							?>
						</label>
						<input type="text" id="yith-account-email" autocomplete="off" name="email"
								placeholder="<?php echo esc_html_x( 'Enter the e-mail address for this license', 'Placeholder', 'yith-plugin-upgrade-fw' ); ?>"
								value="" class="user-email"/>
						<span class="error-message email"></span>
					</p>
					<p class="yith-license-form-row yith-licence-key-wrapper">
						<label for="yith-licence-key" class="yith-license-key">
							<?php
							$find_license_key_url = sprintf(
								'%s: <a href="%s" target="_blank" rel="nofollow noopener" tabindex="-1">%s</a>',
								esc_html_x( 'License key', 'Link on activation license panel', 'yith-plugin-upgrade-fw' ),
								$renew_url,
								esc_html_x( 'Where to find it ?', 'Link on activation license panel', 'yith-plugin-upgrade-fw' )
							);
							echo $find_license_key_url; //@codingStandardsIgnoreLine
							?>
						</label>
						<input type="text" autocomplete="off" name="licence_key" maxlength="36" placeholder="<?php echo esc_html_x( 'Enter the license key', 'Placeholder', 'yith-plugin-upgrade-fw' ); ?>" value="" class="licence-key" id="yith-licence-key"/>
						<input type="submit" name="submit" value="<?php echo esc_html_x( 'Activate', 'Button Label', 'yith-plugin-upgrade-fw' ); ?>" class="button-primary button-licence licence-activation" data-formid="yith-license-activation"/>
						<span class="error-message license-key"></span>
					</p>
					<input type="hidden" name="action" value="yith_activate-<?php echo esc_attr( $this->get_product_type() ); ?>"/>
					<input type="hidden" name="product_init" id="product_init" value=""/>
					<input type="hidden" name="marketplace" id="marketplace" value=""/>
				</div>
			</form>
		</div>
	</div>
	<!-- Activated Products -->

	<div id="activated-product-wrapper">
		<h3 id="activated-products">
			<?php echo esc_html_x( 'Licenses Activated', 'Section Title', 'yith-plugin-upgrade-fw' ); ?>
			<span class="spinner"></span>
		</h3>
		<span id="no-license-enabled-message"
				class="yith-license-<?php echo ( empty( $activated_products ) && empty( $expired_products ) && empty( $banned_products ) ) ? 'visible' : 'hide'; ?>">
			<?php echo esc_html_x( 'No licenses have been activated.', 'License section message', 'yith-plugin-upgrade-fw' ); ?>
		</span>
		<div id="licence-check-section-wrapper"
				class="yith-license-<?php echo ( ! empty( $activated_products ) || ! empty( $expired_products ) || ! empty( $banned_products ) ) ? 'visible' : 'hide'; ?>">
			<div class="licence-check-section">
				<form method="post" id="licence-check-update" action="<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>">
					<span class="licence-label" style="display: block;">
						<label for=""><?php esc_html_e( "If you've already renewed a license and the expiry date is not updated, click to update.", 'yith-plugin-upgrade-fw' ); ?></label>
						<input type="submit" name="submit" value="<?php echo esc_html_x( 'Update Expiry Dates', 'button label', 'yith-plugin-upgrade-fw' ); ?>" class="button-primary button-licence licence-check" id="yith-licence-check-btn"/>
						<div class="spinner"></div>
					</span>
					<input type="hidden" name="action" value="yith_update_licence_information-<?php echo esc_attr( $this->get_product_type() ); ?>"/>
				</form>
				<div id="yith-licence-notice-wrapper">
					<div id="yith-licence-notice" class="<?php echo esc_attr( $notice_class ); ?>">
						<p class="yith-licence-notice-message"><?php echo esc_html( $notice ); ?></p>
					</div>
				</div>

				<table id="yith-enabled-license" class="activated-table" summary="<?php esc_html_e( 'Licenses Activated', 'yith-plugin-upgrade-fw' ); ?>">
					<thead>
					<tr>
						<th class="plugin"><?php echo esc_html_x( 'License:', 'Table Field', 'yith-plugin-upgrade-fw' ); ?></th>
						<th class="email"><?php echo esc_html_x( 'Email:', 'Table Field', 'yith-plugin-upgrade-fw' ); ?></th>
						<th class="license-key"><?php echo esc_html_x( 'License Key:', 'Table Field', 'yith-plugin-upgrade-fw' ); ?></th>
						<th class="remaining"><?php echo esc_html_x( 'Licenses used:', 'Table Field', 'yith-plugin-upgrade-fw' ); ?></th>
						<th class="expire-on"><?php echo esc_html_x( 'Expires on:', 'Table Field', 'yith-plugin-upgrade-fw' ); ?></th>
						<th id="yith-licence-actions"></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$products = array_merge( $activated_products, $expired_products, $banned_products );
					foreach ( $products as $init => $info ) :
						$license_info_default = array( 'marketplace' => 'yith' );
						$info['licence']      = wp_parse_args( $info['licence'], $license_info_default );
						$info['init']         = $init;
						$this->show_activation_row( $info );
						?>
					<?php endforeach; ?>
					</tbody>
					<tfoot></tfoot>
				</table>
			</div>

		</div>
	</div>
</div>
