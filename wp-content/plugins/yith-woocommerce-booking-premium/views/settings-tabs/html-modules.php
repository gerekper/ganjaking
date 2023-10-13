<?php
/**
 * Modules tab content.
 *
 * @var array $available_modules     The available modules data.
 * @var array $non_available_modules The non-available modules data.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit();

$premium_url = yith_plugin_fw_add_utm_data( YITH_WCBK_PREMIUM_LANDING_URL, YITH_WCBK_SLUG, 'modules-tab-button-upgrade', 'extended' );
?>
<div class="yith-wcbk-modules">
	<div class="modules">
		<?php foreach ( $available_modules as $module_data ) : ?>
			<?php yith_wcbk_get_view( 'settings-tabs/html-module.php', compact( 'module_data' ) ); ?>
		<?php endforeach; ?>
	</div>
	<?php if ( $non_available_modules ) : ?>
		<div class="premium-modules">
			<div class="premium-modules__header">
				<div class="premium-modules__title">
					<?php esc_html_e( 'Need more? Get our premium modules!', 'yith-booking-for-woocommerce' ); ?>
				</div>
				<div class="premium-modules__description">
					<?php esc_html_e( 'Upgrade to the premium version to get more advanced modules.', 'yith-booking-for-woocommerce' ); ?>
				</div>
				<div class="premium-modules__cta">
					<a class="yith-wcbk-get-premium-button" href="<?php echo esc_url( $premium_url ); ?>"><?php esc_html_e( 'Get premium', 'yith-booking-for-woocommerce' ); ?></a>
				</div>
			</div>
			<div class="modules">
				<?php foreach ( $non_available_modules as $module_data ) : ?>
					<?php yith_wcbk_get_view( 'settings-tabs/html-module.php', compact( 'module_data' ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
