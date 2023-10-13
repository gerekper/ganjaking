<?php
/**
 * "Update" modal view.
 *
 * @var array  $items         The items.
 * @var string $version       The current plugin version.
 * @var string $since         The "since" version update.
 * @var string $changelog_url The changelog URL.
 * @var string $close_url     The URL for closing the modal.
 *
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

$classes = array(
	'yith-wcbk-welcome',
	'yith-wcbk-welcome--update',
);
$classes = implode( ' ', $classes );

?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<div class="yith-wcbk-welcome__head">
		<img class="yith-wcbk-welcome__icon" src="<?php echo esc_attr( YITH_WCBK_ASSETS_URL . '/images/plugins/booking.svg' ); ?>"/>
		<div class="yith-wcbk-welcome__title">
			<div class="yith-wcbk-welcome__title__plugin-name"><?php echo esc_html( YITH_WCBK_PLUGIN_NAME ); ?></div>
			<div>
				<?php
				// translators: %s is the plugin version.
				echo esc_html( sprintf( __( 'is successfully updated to version %s.', 'yith-booking-for-woocommerce' ), $version ) );
				?>
			</div>
		</div>
	</div>

	<div class="yith-wcbk-welcome__list-head">
		<div class="yith-wcbk-welcome__list-head__title">
			<?php
			// translators: %s is the plugin version.
			echo esc_html( sprintf( __( 'What\'s new in version %s', 'yith-booking-for-woocommerce' ), $since ) );
			?>
		</div>
		<?php if ( $changelog_url ) : ?>
			<a class="yith-wcbk-welcome__list-head__changelog" target="_blank" href="<?php echo esc_url( $changelog_url ); ?>">
				<?php esc_html_e( 'Check the changelog >', 'yith-booking-for-woocommerce' ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
	yith_wcbk_get_view(
		'welcome/items.php',
		array(
			'items'   => $items,
			'variant' => 'list',
		)
	);
	?>

	<div class="yith-wcbk-welcome__footer">
		<a class="yith-wcbk-welcome__close" href="<?php echo esc_url( $close_url ); ?>"><?php esc_html_e( 'Got it, close this window', 'yith-booking-for-woocommerce' ); ?></a>
	</div>
</div>
