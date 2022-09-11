<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Renders any linked social profiles on my account page.
 *
 * @type array $linked_profiles profiles that are already linked to the current user's account
 * @type array $available_providers all available social login providers
 * @type string $return_url
 *
 * @version 2.6.2
 * @since 1.1.0
 */
?>

<div class="wc-social-login-profile">

	<h2><?php esc_html_e( 'Social Login Accounts', 'woocommerce-social-login' ); ?></h2>

	<?php if ( $linked_profiles ) : ?>

		<?php

		$add_more_link = '';

		if ( count( $linked_profiles ) < count( $available_providers ) ) {
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			$add_more_link = ' ' . sprintf( __( '%1$sAdd more...%2$s', 'woocommerce-social-login' ), '<a href="#" class="js-show-available-providers">', '</a>' );
		}

		?>
		<p><?php echo esc_html__( 'Your account is connected to the following social login providers.', 'woocommerce-social-login' ) . $add_more_link; ?></p>

		<table class="shop_table shop_table_responsive wc-social-login-linked-profiles">

			<thead>
				<tr>
					<th><?php esc_html_e( 'Provider', 'woocommerce-social-login' ); ?></th>
					<th><?php esc_html_e( 'Account', 'woocommerce-social-login' ); ?></th>
					<th colspan="2"><?php esc_html_e( 'Last login', 'woocommerce-social-login' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $linked_profiles as $provider_id => $profile ) : ?>

					<?php

					$provider        = wc_social_login()->get_provider( $provider_id );
					$login_timestamp = get_user_meta( get_current_user_id(), '_wc_social_login_' . $provider_id . '_login_timestamp', true );

					?>
					<tr>
						<td data-title="<?php esc_attr_e( 'Provider', 'woocommerce-social-login' ); ?>">
							<?php printf( '<span class="social-badge social-badge-%1$s"><span class="si si-%1$s"></span>%2$s</span> ', esc_attr( $provider->get_id() ), esc_html( $provider->get_title() ) ); ?>
						</td>
						<td data-title="<?php esc_attr_e( 'Account', 'woocommerce-social-login' ); ?>">
							<?php

							/**
							 * Filters the profile identifier displayed to the user.
							 *
							 * @since 1.0.0
							 *
							 * @param string $profile_identifier user email or display name
							 */
							echo esc_html( apply_filters( 'wc_social_login_profile_identifier', $profile->has_email() ? $profile->get_email() : $profile->get_display_name() ) );

							?>
						</td>
						<td data-title="<?php esc_attr_e( 'Last login', 'woocommerce-social-login' ); ?>">
							<?php

							if ( $login_timestamp ) :
								/* translators: Placeholders: %1$s - date, %2$s - time */
								printf( esc_html__( '%1$s @ %2$s', 'woocommerce-social-login' ), date_i18n( wc_date_format(), $login_timestamp ), date_i18n( wc_time_format(), $login_timestamp ) );
							else :
								esc_html_e( 'Never', 'woocommerce-social-login' );
							endif;

							?>
						</td>
						<td class="profile-actions">
							<a href="<?php echo esc_url( wp_nonce_url( $provider->get_auth_url( $return_url, 'unlink' ), 'unlink' ) ); ?>" class="button unlink-social-login-profile">
								<?php echo esc_html_x( 'Unlink', 'Action to unlink an account from a social profile', 'woocommerce-social-login' ); ?>
							</a>
						</td>
					</tr>

				<?php endforeach; ?>

			</tbody>

		</table>

	<?php else : ?>

		<p>
			<?php /* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				printf( esc_html__( 'You have no social login profiles connected. %1$sConnect one now%2$s', 'woocommerce-social-login' ), '<a href="#" class="js-show-available-providers">', '</a>' );
			?>
		</p>

	<?php endif; ?>

	<div class="wc-social-login-available-providers" style="display:none;">

		<p><?php esc_html_e( 'You can link your account to the following providers:', 'woocommerce-social-login' ); ?></p>

		<?php woocommerce_social_login_link_account_buttons( wc_customer_edit_account_url() ); ?>

	</div>

</div>
<?php
