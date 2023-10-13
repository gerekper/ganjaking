<?php
/**
 * Google Calendar settings.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$google_calendar = YITH_WCBK_Google_Calendar::get_instance();

$updated = isset( $_GET['updated'] ) ? absint( $_GET['updated'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( false !== $updated ) {
	if ( $updated ) {
		// translators: %s is the number of updated bookings.
		$message = sprintf( _n( 'Google Calendar: %s booking updated!', 'Google Calendar: %s bookings updated!', $updated, 'yith-booking-for-woocommerce' ), $updated );
	} else {
		$message = __( 'Google Calendar: no booking to update!', 'yith-booking-for-woocommerce' );
	}
	echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}

$classes   = array();
$classes[] = $google_calendar->is_calendar_sync_enabled() ? 'yith-wcbk-google-calendar--is-sync-enabled' : 'yith-wcbk-google-calendar--is-not-sync-enabled';

$classes = implode( ' ', $classes );
?>

<div id="yith-wcbk-google-calendar-settings" class="<?php echo esc_attr( $classes ); ?>">
	<input type="hidden" name="yith-wcbk-gcal-action" value="save-options"/>
	<?php wp_nonce_field( 'yith-wcbk-gcal-action', 'yith-wcbk-gcal-nonce' ); ?>

	<div id="yith-wcbk-google-calendar-tab__main" class="yith-wcbk-settings-content">
		<?php
		$google_calendar->display();
		?>
	</div>

	<div id="yith-wcbk-google-calendar-tab__settings" class="yith-wcbk-settings-content">
		<div class="yith-wcbk-settings-section">
			<div class="yith-wcbk-settings-section__title">
				<h3><?php esc_html_e( 'Settings', 'yith-booking-for-woocommerce' ); ?></h3>
			</div>
			<div class="yith-wcbk-settings-section__content">
				<div class="yith-plugin-fw__panel__option yith-plugin-fw__panel__option--onoff">
					<div class="yith-plugin-fw__panel__option__label">
						<label for="yith-wcbk-gcal-options__debug">
							<?php esc_html_e( 'Debug', 'yith-booking-for-woocommerce' ); ?>
						</label>
					</div>
					<div class="yith-plugin-fw__panel__option__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'    => 'yith-wcbk-gcal-options__debug',
								'type'  => 'onoff',
								'name'  => 'yith-wcbk-gcal-options[debug]',
								'value' => $google_calendar->is_debug() ? 'yes' : 'no',
							),
							true,
							false
						);
						?>
					</div>
					<div class="yith-plugin-fw__panel__option__description">
						<?php
						echo esc_html(
							sprintf(
							// translators: %s is  logs tab path (YITH > Booking > Logs).
								__( 'If enabled, the plugin will add some Google Calendar related debug logs that will be available in the "%s" tab.', 'yith-booking-for-woocommerce' ),
								sprintf(
									'YITH > Booking > %s > %s',
									_x( 'Tools', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
									_x( 'Logs', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
								)
							)
						);
						?>
					</div>
				</div>

				<div class="yith-plugin-fw__panel__option yith-wcbk-google-calendar-settings__show-if-sync-enabled">
					<div class="yith-plugin-fw__panel__option__label">
						<label for="yith-wcbk-gcal-options__debug">
							<?php esc_html_e( 'Synchronize', 'yith-booking-for-woocommerce' ); ?>
						</label>
					</div>
					<div class="yith-plugin-fw__panel__option__content">
						<?php
						$synchronize_settings  = array(
							'creation'      => __( 'on booking creation', 'yith-booking-for-woocommerce' ),
							'update'        => __( 'on booking update', 'yith-booking-for-woocommerce' ),
							'status-update' => __( 'on booking status update', 'yith-booking-for-woocommerce' ),
							'deletion'      => __( 'on booking deletion', 'yith-booking-for-woocommerce' ),
						);
						$events_to_synchronize = $google_calendar->get_booking_events_to_synchronize();
						?>

						<div id="yith-wcbk-google-calendar-settings-synchronize-booking-events-container">
							<?php foreach ( $synchronize_settings as $key => $label ) : ?>
								<?php
								$_id = 'yith-wcbk-google-calendar-booking-events-to-synchronize-' . $key;
								?>
								<div>
									<input type="checkbox" name="yith-wcbk-gcal-options[booking-events-to-synchronize][]" id="<?php echo esc_attr( $_id ); ?>"
											value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $events_to_synchronize, true ) ); ?>/>
									<label for="<?php echo esc_attr( $_id ); ?>"><?php echo esc_html( $label ); ?></label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="yith-plugin-fw__panel__option yith-plugin-fw__panel__option--onoff yith-wcbk-google-calendar-settings__show-if-sync-enabled">
					<div class="yith-plugin-fw__panel__option__label">
						<label for="yith-wcbk-gcal-option__add-note-on-sync">
							<?php esc_html_e( 'Add note on sync', 'yith-booking-for-woocommerce' ); ?>
						</label>
					</div>
					<div class="yith-plugin-fw__panel__option__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'    => 'yith-wcbk-gcal-option__add-note-on-sync',
								'type'  => 'onoff',
								'name'  => 'yith-wcbk-gcal-options[add-note-on-sync]',
								'value' => $google_calendar->is_add_note_on_sync_enabled() ? 'yes' : 'no',
							),
							true,
							false
						);
						?>
					</div>
					<div class="yith-plugin-fw__panel__option__description">
						<?php esc_html_e( 'If enabled, the plugin will add a note to the booking when it\'s synchronized on Google Calendar.', 'yith-booking-for-woocommerce' ); ?>
					</div>
				</div>

				<div class="yith-plugin-fw__panel__option yith-wcbk-google-calendar-settings__show-if-sync-enabled">
					<div class="yith-plugin-fw__panel__option__label">
						<label>
							<?php esc_html_e( 'Event name will include', 'yith-booking-for-woocommerce' ); ?>
						</label>
					</div>
					<div class="yith-plugin-fw__panel__option__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'type'    => 'radio',
								'name'    => 'yith-wcbk-gcal-options[event-name-format]',
								'value'   => $google_calendar->get_event_name_format(),
								'options' => array(
									'#{id} {product_name}'               => esc_html__( 'Booking ID and product name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 Rome Apartment</code>',
									'#{id} {user_name}'                  => esc_html__( 'Booking ID and user name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 John Doe</code>',
									'#{id} {product_name} ({user_name})' => esc_html__( 'Booking ID, product name and user name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 Rome Apartment (John Doe)</code>',
									'#{id} {user_name} ({product_name})' => esc_html__( 'Booking ID, user name and product name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 John Doe (Rome Apartment)</code>',
								),
							),
							true,
							false
						);
						?>
					</div>
					<div class="yith-plugin-fw__panel__option__description">
						<?php esc_html_e( 'Choose what to show as name of the synchronized event in Google Calendar.', 'yith-booking-for-woocommerce' ); ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ( $google_calendar->is_calendar_sync_enabled() ) : ?>
			<?php
			$actions = array(
				'sync-bookings'       => array(
					'title' => __( 'Synchronize not synchronized bookings', 'yith-booking-for-woocommerce' ),
					'label' => __( 'Sync bookings', 'yith-booking-for-woocommerce' ),
					'url'   => yith_wcbk()->google_calendar_sync()->get_action_url( 'sync-new-bookings' ),
				),
				'force-sync-bookings' => array(
					'title' => __( 'Synchronize all bookings (Force)', 'yith-booking-for-woocommerce' ),
					'label' => __( 'Force sync bookings', 'yith-booking-for-woocommerce' ),
					'url'   => yith_wcbk()->google_calendar_sync()->get_action_url( 'force-sync-all-bookings' ),
				),
			);
			if ( yith_wcbk()->background_processes->is_group_running( YITH_WCBK_Google_Calendar_Sync::BG_PROCESS_GROUP ) ) {
				$already_in_sync_message = sprintf(
					'%s <a href="%s">%s &rarr;</a>',
					esc_html__( 'Synchronizing your bookings in the background.', 'yith-booking-for-woocommerce' ),
					esc_url(
						add_query_arg(
							array(
								'page'   => 'wc-status',
								'tab'    => 'action-scheduler',
								's'      => YITH_WCBK_Google_Calendar_Sync::BG_PROCESS_GROUP,
								'status' => 'pending',
							),
							admin_url( 'admin.php' )
						)
					),
					esc_html__( 'View progress', 'yith-booking-for-woocommerce' )
				);

				$actions['sync-bookings']['html']       = $already_in_sync_message;
				$actions['force-sync-bookings']['html'] = $already_in_sync_message;
			}
			?>
			<div class="yith-wcbk-settings-section">
				<div class="yith-wcbk-settings-section__title">
					<h3><?php esc_html_e( 'Actions', 'yith-booking-for-woocommerce' ); ?></h3>
				</div>
				<div class="yith-wcbk-settings-section__content">
					<div class="yith-wcbk-google-calendar-tab__actions">
						<?php foreach ( $actions as $_action ) : ?>
							<?php
							list ( $action_title, $action_label, $action_url, $action_html ) = yith_plugin_fw_extract( $_action, 'title', 'label', 'url', 'html' );

							?>
							<div class="yith-wcbk-google-calendar-tab__action">
								<div class="yith-wcbk-google-calendar-tab__action__name"><?php echo esc_html( $action_title ); ?></div>
								<div class="yith-wcbk-google-calendar-tab__action__button">
									<?php if ( $action_html ) : ?>
										<?php echo wp_kses_post( $action_html ); ?>
									<?php else : ?>
										<a href='<?php echo esc_url( $action_url ); ?>' class='yith-plugin-fw__button--update'><?php echo esc_html( $action_label ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

		<?php endif; ?>
	</div>
</div>
