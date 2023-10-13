<?php
/**
 * Global Availability Rules view
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$old_rules   = get_option( 'yith_wcbk_booking_global_cost_ranges', null );
$is_updating = ! is_null( $old_rules ) && defined( 'YITH_WCBK_INSTALLING' ) && YITH_WCBK_INSTALLING;
?>

<?php if ( $is_updating ) : ?>
	<?php
	yith_plugin_fw_get_component(
		array(
			'type'    => 'list-table-blank-state',
			'icon'    => 'reset',
			'message' => implode(
				'<br />',
				array(
					sprintf(
					// translators: %s is the plugin name.
						esc_html__( '%s is updating the global price rules in the background.', 'yith-booking-for-woocommerce' ),
						YITH_WCBK_PLUGIN_NAME
					),
					esc_html__( 'It will just take a few minutes, you\'ll see your rules here when completed.', 'yith-booking-for-woocommerce' ),
				)
			),
		),
		true
	);
	?>
<?php else : ?>
	<div id="yith-wcbk-admin-global-price-rules-root"></div>
<?php endif; ?>
