<?php
/**
 * "Welcome" modal view.
 *
 * @var array  $items     The items.
 * @var string $close_url The URL for closing the modal.
 *
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

$classes = array(
	'yith-wcbk-welcome',
	'yith-wcbk-welcome--welcome',
);
$classes = implode( ' ', $classes );

?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<div class="yith-wcbk-welcome__head">
		<img class="yith-wcbk-welcome__icon" src="<?php echo esc_attr( YITH_WCBK_ASSETS_URL . '/images/plugins/booking.svg' ); ?>"/>
		<div class="yith-wcbk-welcome__title">
			<div><?php esc_html_e( 'Thank you for using our plugin', 'yith-booking-for-woocommerce' ); ?></div>
			<div class="yith-wcbk-welcome__title__plugin-name"><?php echo esc_html( YITH_WCBK_PLUGIN_NAME ); ?></div>
		</div>
		<div class="yith-wcbk-welcome__description">
			<?php esc_html_e( 'With this plugin you can manage every kind of bookable product (rooms, houses, sports equipment, bikes, etc.) and services (yoga lessons, medical appointments, legal or business consulting, etc.).', 'yith-booking-for-woocommerce' ); ?>
		</div>
	</div>

	<div class="yith-wcbk-welcome__list-head">
		<div class="yith-wcbk-welcome__list-head__title">
			<?php
			// translators: %s is the number of steps.
			echo esc_html( sprintf( __( 'Start with these %s steps:', 'yith-booking-for-woocommerce' ), count( $items ) ) );
			?>
		</div>
	</div>
	<?php
	yith_wcbk_get_view(
		'welcome/items.php',
		array(
			'items'   => $items,
			'variant' => 'steps',
		)
	);
	?>

	<div class="yith-wcbk-welcome__footer">
		<a class="yith-wcbk-welcome__close" href="<?php echo esc_url( $close_url ); ?>"><?php esc_html_e( 'Got it, close this window', 'yith-booking-for-woocommerce' ); ?></a>
	</div>
</div>
