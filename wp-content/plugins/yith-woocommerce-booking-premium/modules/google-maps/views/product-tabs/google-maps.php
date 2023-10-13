<?php
/**
 * Google Maps product tabs
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 *
 * @package YITH\Booking\Modules\GoogleMaps
 */

defined( 'YITH_WCBK' ) || exit;

$panel_settings_url        = add_query_arg(
	array(
		'page'    => YITH_WCBK_Admin::PANEL_PAGE,
		'tab'     => 'settings',
		'sub_tab' => 'settings-general-settings',
	),
	admin_url( 'admin.php' )
);
$panel_settings_breadcrumb = implode(
	' > ',
	array(
		'YITH',
		'Booking',
		_x( 'Settings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		_x( 'General Settings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
	)
);
$panel_settings_link       = sprintf( '<a href="%s">%s</a>', $panel_settings_url, $panel_settings_breadcrumb );
?>

<div class="yith-wcbk-settings-section">
	<div class="yith-wcbk-settings-section__title">
		<h3><?php esc_html_e( 'Google Maps', 'yith-booking-for-woocommerce' ); ?></h3>
		<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
	</div>
	<div class="yith-wcbk-settings-section__content">
		<?php
		yith_wcbk_form_field(
			array(
				'class'    => '_yith_booking_location_field',
				'title'    => __( 'Location', 'yith-booking-for-woocommerce' ),
				'desc'     => implode(
					'<br />',
					array(
						sprintf(
						// translators: %s is the Booking Settings link.
							__( 'Set your Google Maps API Keys in %s and then enter your address in this field.', 'yith-booking-for-woocommerce' ),
							$panel_settings_link
						),
						sprintf(
						// translators: %s is the [booking_map] shortcode.
							__( 'You can put a map in product page by using the %s shortcode.', 'yith-booking-for-woocommerce' ),
							'<strong>[booking_map]</strong>'
						),
					)
				),
				'fields'   => array(
					'type'  => 'text',
					'value' => $booking_product ? $booking_product->get_location( 'edit' ) : '',
					'id'    => '_yith_booking_location',
					'class' => 'yith-wcbk-google-maps-places-autocomplete',
				),
				'priority' => 60,
			)
		);
		?>
	</div>
</div>
