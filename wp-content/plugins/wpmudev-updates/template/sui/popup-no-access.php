<?php
/**
 * Dashboard popup template: No Access!
 *
 * This popup is displayed when a user is logged in and can view the current
 * Dashboard page, but the WPMUDEV account does not allow him to use the
 * features on the current page.
 * Usually this is displayed when a member has a single license and visits the
 * Plugins or Themes page (he cannot install new plugins or themes).
 *
 * Following variables are passed into the template:
 *   $is_logged_in
 *   $urls
 *   $username
 *   $reason
 *   $auto_show
 *
 * @since   4.0.0
 * @package WPMUDEV_Dashboard
 */

/** @var  WPMUDEV_Dashboard_Sui_Page_Urls $urls */
$url_upgrade = $urls->remote_site . 'hub/account/';

$url_upgrade = add_query_arg( array(
    'utm_source' 	=> 'wpmudev-dashboard',
    'utm_medium' 	=> 'plugin',
    'utm_campaign' 	=> 'dashboard_expired_modal_reactivate',
), $url_upgrade );

$url_logout  = $urls->dashboard_url . '&clear_key=1';
$url_refresh = wp_nonce_url( add_query_arg( 'action', 'check-updates' ), 'check-updates', 'hash' );
$reason_text = __( "Whoops, looks like you’ve logged in with an expired membership. Reactivate your membership to get access to pro features, 24/7 support and The Hub website management tools.", 'wpmudev' );

if( 'single' === $reason ){
	$reason_text = sprintf( __( "Whoops, looks like you have a single plugin membership. Upgrade your WPMU Dev membership to unlock pro features for this website. %s Note you can still use the Support & Settings tabs", 'wpmudev' ), '<br>' );
}

?>
<div class="sui-dialog sui-dialog-alt sui-dialog-sm" tabindex="-1" aria-hidden="true" id="upgrade-membership">

	<div class="sui-dialog-overlay"></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">

		<div class="sui-box" role="document">
			<form>

				<div class="sui-box-header sui-block-content-center">
					<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Upgrade Membership!', 'wpmudev' ); ?></h3>
				</div>

				<div class="sui-box-body sui-block-content-center" style="padding: 0 30px;">
					<p id="dialogDescription">
						<?php
						// @codingStandardsIgnoreStart: Reason contains HTML, no escaping!
						echo wp_kses_post( $reason_text );
						// @codingStandardsIgnoreEnd
						?>
					</p>

					<div class="sui-block-content-center">
						<a href="<?php echo esc_url( $url_upgrade ); ?>" class="sui-button sui-button-blue sui-button-md" target="_blank">
						<?php 'single' === $reason ? esc_html_e( 'Upgrade Membership', 'wpmudev' ) : esc_html_e( 'Reactivate Membership', 'wpmudev' ); ?>
						</a>
					</div>

				</div>

				<div class="sui-box-footer membership-upgrade-footer" style="padding: 30px;">
					<a href="<?php echo esc_url( $url_refresh ); ?>">
						<i class="sui-icon-refresh" aria-hidden="true"></i>
						<?php esc_html_e( 'Refresh Status', 'wpmudev' ); ?>
					</a>
					<a href="<?php echo esc_url( $url_logout ); ?>">
						<i class="sui-icon-logout" aria-hidden="true"></i>
						<?php esc_html_e( 'Switch Account', 'wpmudev' ); ?>
					</a>
				</div>
				<div class="sui-block-content-center">
					<img
						src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new.png' ); ?>"
						srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-dashboard-modal-upgrade.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-dashboard-modal-upgrade@2x.png' ); ?> 2x"
						alt="Upgrade"
						aria-hidden="true"
						style = "vertical-align: middle;"
					/>
				</div>
			</form>
		</div>

	</div>

</div>

<script type="text/javascript">
	jQuery(document).on('wpmud.ready', function () {
		if (typeof window.wpmudevDashboardAdminDialog === 'function') {
			var dialog        = document.getElementById('upgrade-membership');
			var upgradeDialog = new wpmudevDashboardAdminDialog(dialog, jQuery('.sui-wrap').get(0));
			//disable modal dissmiss on keypress
			upgradeDialog._bindKeypress = undefined;
			upgradeDialog.show();
			setTimeout(function () {
				if (jQuery('#upgrade-membership').attr('aria-hidden')) {
					jQuery('#upgrade-membership').removeAttr('aria-hidden');
				}
			}, 2000);
		}
	});
</script>
