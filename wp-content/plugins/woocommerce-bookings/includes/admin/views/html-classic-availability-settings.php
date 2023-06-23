<?php
/**
 * Output or save global availability settings.
 *
 * @package WooCommerce/Bookings
 */

if (
	isset( $_POST['Submit'] )
	&& isset( $_POST['global_availability_nonce'] )
	&& wp_verify_nonce( wc_clean( wp_unslash( $_POST['global_availability_nonce'] ) ), 'submit_global_availability' )
	&& current_user_can( 'edit_global_availabilities' )
) {
	// Save the field values.
	if ( ! empty( $_POST['bookings_availability_submitted'] ) ) {
		if ( ! empty( $_POST['wc_booking_availability_deleted'] ) ) {
			$deleted_ids = array_map( 'absint', explode( ',', wc_clean( wp_unslash( $_POST['wc_booking_availability_deleted'] ) ) ) );

			foreach ( $deleted_ids as $delete_id ) {
				if ( current_user_can( 'delete_global_availability', $delete_id ) ) {
					$availability_object = new WC_Global_Availability( $delete_id );
					$availability_object->delete();
				}
			}
		}

		$types = isset( $_POST['wc_booking_availability_type'] ) ? wc_clean( wp_unslash( $_POST['wc_booking_availability_type'] ) ) : array();

		$row_size = count( $types );

		for ( $i = 0; $i < $row_size; $i++ ) {
			if ( isset( $_POST['wc_booking_availability_id'][ $i ] ) ) {
				$current_id = intval( $_POST['wc_booking_availability_id'][ $i ] );
			} else {
				$current_id = 0;
			}

			if ( $current_id && ! current_user_can( 'edit_global_availability', $current_id ) ) {
				continue;
			}

			$availability = new WC_Global_Availability( $current_id );
			$availability->set_range_type( $types[ $i ] );

			if ( isset( $_POST['wc_booking_availability_bookable'][ $i ] ) ) {
				$availability->set_bookable( wc_clean( wp_unslash( $_POST['wc_booking_availability_bookable'][ $i ] ) ) );
			}

			if ( isset( $_POST['wc_booking_availability_title'][ $i ] ) ) {
				$availability->set_title( sanitize_text_field( wp_unslash( $_POST['wc_booking_availability_title'][ $i ] ) ) );
			}

			if ( isset( $_POST['wc_booking_availability_gcal_event_id'][ $i ] ) ) {
				$availability->set_gcal_event_id( wc_clean( wp_unslash( $_POST['wc_booking_availability_gcal_event_id'][ $i ] ) ) );
			}
			if ( isset( $_POST['wc_booking_availability_priority'][ $i ] ) ) {
				$availability->set_priority( intval( $_POST['wc_booking_availability_priority'][ $i ] ) );
			}
			$availability->set_ordering( $i );

			switch ( $availability->get_range_type() ) {
				case 'custom':
					if ( isset( $_POST['wc_booking_availability_from_date'][ $i ] ) && isset( $_POST['wc_booking_availability_to_date'][ $i ] ) ) {
						$availability->set_from_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_from_date'][ $i ] ) ) );
						$availability->set_to_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_to_date'][ $i ] ) ) );
					}
					break;
				case 'months':
					if ( isset( $_POST['wc_booking_availability_from_month'][ $i ] ) && isset( $_POST['wc_booking_availability_to_month'][ $i ] ) ) {
						$availability->set_from_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_from_month'][ $i ] ) ) );
						$availability->set_to_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_to_month'][ $i ] ) ) );
					}
					break;
				case 'weeks':
					if ( isset( $_POST['wc_booking_availability_from_week'][ $i ] ) && isset( $_POST['wc_booking_availability_to_week'][ $i ] ) ) {
						$availability->set_from_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_from_week'][ $i ] ) ) );
						$availability->set_to_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_to_week'][ $i ] ) ) );
					}
					break;
				case 'days':
					if ( isset( $_POST['wc_booking_availability_from_day_of_week'][ $i ] ) && isset( $_POST['wc_booking_availability_to_day_of_week'][ $i ] ) ) {
						$availability->set_from_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_from_day_of_week'][ $i ] ) ) );
						$availability->set_to_range( wc_clean( wp_unslash( $_POST['wc_booking_availability_to_day_of_week'][ $i ] ) ) );
					}
					break;
				case 'rrule':
					// Do nothing rrules are read only for now.
					break;
				case 'time':
				case 'time:1':
				case 'time:2':
				case 'time:3':
				case 'time:4':
				case 'time:5':
				case 'time:6':
				case 'time:7':
					if ( isset( $_POST['wc_booking_availability_from_time'][ $i ] ) && isset( $_POST['wc_booking_availability_to_time'][ $i ] ) ) {
						$availability->set_from_range( wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_from_time'][ $i ] ) ) );
						$availability->set_to_range( wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_to_time'][ $i ] ) ) );
					}
					break;
				case 'time:range':
				case 'custom:daterange':
					if ( isset( $_POST['wc_booking_availability_from_time'][ $i ] ) && isset( $_POST['wc_booking_availability_to_time'][ $i ] ) ) {
						$availability->set_from_range( wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_from_time'][ $i ] ) ) );
						$availability->set_to_range( wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_to_time'][ $i ] ) ) );
					}
					if ( isset( $_POST['wc_booking_availability_from_date'][ $i ] ) && isset( $_POST['wc_booking_availability_to_date'][ $i ] ) ) {
						$availability->set_from_date( wc_clean( wp_unslash( $_POST['wc_booking_availability_from_date'][ $i ] ) ) );
						$availability->set_to_date( wc_clean( wp_unslash( $_POST['wc_booking_availability_to_date'][ $i ] ) ) );
					}
					break;
			}

			$availability->save();
		}

		do_action( 'wc_bookings_global_availability_on_save' );
		echo '<div class="updated"><p>' . esc_html__( 'Settings saved', 'woocommerce-bookings' ) . '</p></div>';
	}
}

/* @var WC_Global_Availability_Data_Store $global_availabilities_data_store */
$global_availabilities_data_store = WC_Data_Store::load( 'booking-global-availability' );

$can_lazy_load_availability_rules  = false;
$global_availabilities             = $global_availabilities_data_store->get_all();
$show_title                        = true;
$show_google_event                 = isset( $_GET['show'] ) && 'google-events' === $_GET['show'];
$global_availability_rule_per_page = $show_google_event ? 250 : count( $global_availabilities );
?>

<form method="post" action="" id="bookings_settings">
	<input type="hidden" name="bookings_availability_submitted" value="1" />
	<div id="poststuff">
		<div class="postbox">
			<div class="inside">
				<p><?php esc_html_e( 'This section will set the availability of your store (e.g. open and closed hours). All bookable products will adopt your store\'s availability.', 'woocommerce-bookings' ); ?></p>
				<p>
					<?php
					/* translators: %1$s is replaced with the opening anchor tag; %2$s with the closing */
					printf(
						esc_html__( 'For more information on setting up store availability rules, please see our %1$sdocumentation%2$s.', 'woocommerce-bookings' ),
						'<a href="https://woocommerce.com/document/booking-settings/#section-1" target="_blank" rel="noopener">',
						'</a>'
					); ?>
				</p>

				<ul class="subsubsub">
					<?php if ( $global_availabilities_data_store->has_google_event() ) : ?>
						<li class="availability-rules">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_settings&show=availability-rules' ) ); ?>"
							   class="<?php echo $show_google_event ? '' : 'current'; ?>">
								<?php esc_html_e( 'Availability Rules', 'woocommerce-bookings' ); ?>
							</a> |
						</li>
						<li class="google-events">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_settings&show=google-events' ) ); ?>"
							   class="<?php echo $show_google_event ? 'current' : ''; ?>">
								<?php esc_html_e( 'Google Events', 'woocommerce-bookings' ); ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>

				<?php if ( defined( 'WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR' ) && WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR ) : ?>
					<div class="wc-bookings-store-availability-nav-classic">
						<?php require_once 'html-availability-views-nav.php'; ?>
					</div>
				<?php endif; ?>

				<div class="table_grid" id="bookings_availability">
					<table class="widefat">
						<thead>
							<tr>
								<?php if ( ! $show_google_event ) : ?>
									<th class="sort" width="1%">&nbsp;</th>
								<?php endif; ?>
								<th><?php esc_html_e( 'Range type', 'woocommerce-bookings' ); ?></th>
								<th><?php esc_html_e( 'Range', 'woocommerce-bookings' ); ?></th>
								<th></th>
								<th></th>
								<th><?php esc_html_e( 'Bookable', 'woocommerce-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'If not bookable, users won\'t be able to choose this block for their booking.', 'woocommerce-bookings' ) ); ?>">[?]</a></th>
								<th><?php esc_html_e( 'Title', 'woocommerce-bookings' ); ?></th>
								<th><?php esc_html_e( 'Priority', 'woocommerce-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( get_wc_booking_priority_explanation() ); ?>">[?]</a></th>
								<?php do_action( 'woocommerce_bookings_extra_global_availability_fields_header' ); ?>
								<th class="remove" width="1%">&nbsp;</th>
							</tr>
						</thead>
						<?php if ( ! $show_google_event ) : ?>
							<tfoot>
							<tr>
								<th colspan="7">
									<a href="#" class="button add_row" data-row="
										<?php
										ob_start();
										$availability = new WC_Global_Availability();
										include 'html-booking-availability-fields.php';
										$html = ob_get_clean();
										echo esc_attr( $html );
										?>">
										<?php esc_html_e( 'Add Range', 'woocommerce-bookings' ); ?>
									</a>
									<?php wc_booking_rules_explanation_html(); ?>
								</th>
							</tr>
							</tfoot>
						<?php endif; ?>
						<tbody id="availability_rows">
							<?php
							$live_global_availabilities_counter = 0;
							if ( ! empty( $global_availabilities ) && is_array( $global_availabilities ) ) {
								foreach ( $global_availabilities as $index => $availability ) {
									if ( $availability->has_past() ) {
										continue;
									}

									if (
										// Hide availability rules from Google event store availability rules.
										( $show_google_event && ! $availability->get_gcal_event_id() ) ||
										// Hide Google event rules from Google event store availability rules.
										( ! $show_google_event && $availability->get_gcal_event_id() )
									) {
										continue;
									}

									include 'html-booking-availability-fields.php';
									++$live_global_availabilities_counter;

									// Check whether availability rules remaining to render.
									// Remaining availability rules will load by ajax request on client side.
									if ( $global_availability_rule_per_page === $live_global_availabilities_counter ) {
										$can_lazy_load_availability_rules = $index !== array_key_last( $global_availabilities );
										break;
									}
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<p><?php esc_html_e( 'Past availability are hidden from this list.', 'woocommerce-bookings' ); ?></p>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'woocommerce-bookings' ); ?>"/>
		<input type="hidden" name="wc_booking_availability_deleted" value="" class="wc-booking-availability-deleted"/>
		<?php wp_nonce_field( 'submit_global_availability', 'global_availability_nonce' ); ?>

		<!-- These fields will be used to lazy load availability rules. -->
		<input type="hidden" name="can-lazy-load-availability-rules" value="<?php echo absint( $can_lazy_load_availability_rules ); ?>" disabled>
		<input type="hidden" name="lazy_load_availability_rules_nonce" value="<?php echo wp_create_nonce( 'lazy_load_availability_rules' ); ?>" disabled>
		<input type="hidden" name="availability-rules-per-page" value="<?php echo absint( $global_availability_rule_per_page ); ?>" disabled>
	</p>
</form>
