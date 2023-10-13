<?php
/**
 * Template options in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'YITH_WCBK' ) || exit;
?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Booking Sync', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-settings-section__description">
				<?php esc_html_e( 'Auto-sync the availability of this bookable product with the iCal calendars of external platforms like Booking, Airbnb and HomeAway. This allows you to avoid overbooking and errors.', 'yith-booking-for-woocommerce' ); ?>
			</div>
			<?php

			yith_wcbk_form_field(
				array(
					'title'  => __( 'Import iCal Calendars', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'html',
						'value' => yith_wcbk_get_module_view_html(
							'external-sync',
							'product-tabs/sync-tab/imported-calendars.php',
							array(
								'calendars' => $booking_product ? $booking_product->get_external_calendars( 'edit' ) : array(),
							)
						),
					),
					'desc'   => __( 'Note: you can find the calendar URL on the site you want to sync (Airbnb, Booking, etc.).', 'yith-booking-for-woocommerce' ),
				)
			);

			$last_sync = $booking_product ? $booking_product->get_external_calendars_last_sync( 'edit' ) : 0;
			if ( $last_sync ) {
				yith_wcbk_form_field(
					array(
						'fields' => array(
							'type'  => 'html',
							'value' => sprintf(
								'<div class="yith-wcbk-product-last-sync"><div class="yith-wcbk-product-last-sync__label">%s</div><div class="yith-wcbk-product-last-sync__value">%s</div></div>',
								esc_html__( 'Last sync', 'yith-booking-for-woocommerce' ) . ':',
								yith_wcbk_datetime( $last_sync )
							),
						),
					)
				);
			}

			$key                   = $booking_product ? $booking_product->get_external_calendars_key( 'edit' ) : yith_wcbk_generate_external_calendars_key();
			$export_future_ics_url = add_query_arg(
				array(
					'yith_wcbk_exporter_action' => 'export_future_ics',
					'product_id'                => $post_id,
					'key'                       => $key,
				),
				trailingslashit( home_url() )
			);
			ob_start();
			?>
			<input type="hidden" name="_yith_booking_external_calendars_key" value="<?php echo esc_attr( $key ); ?>"/>
			<?php yith_plugin_fw_copy_to_clipboard( $export_future_ics_url ); ?>
			<a href='<?php echo esc_url( $export_future_ics_url ); ?>' class='yith-wcbk-sync-download-future-ics yith-plugin-fw__button--secondary yith-plugin-fw__button--with-icon'>
				<i class="yith-icon yith-icon-upload"></i>
				<?php esc_html_e( 'Download', 'yith-booking-for-woocommerce' ); ?>
			</a>
			<?php
			$export_future_ics_html = ob_get_clean();

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_sync_export_future_ics_url yith_booking_multi_fields',
					'title'  => __( 'Export iCal Calendar', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'type'  => 'html',
						'value' => $export_future_ics_html,
					),
					'desc'   => __( 'Copy the URL of this product\'s calendar or download it to import it into the external site you want to sync.', 'yith-booking-for-woocommerce' ),
				)
			);
			?>
		</div>
	</div>
</div>
