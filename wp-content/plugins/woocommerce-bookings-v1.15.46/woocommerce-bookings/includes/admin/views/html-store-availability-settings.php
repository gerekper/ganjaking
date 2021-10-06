<?php
/**
 * Output or save global availability settings.
 *
 * @package WooCommerce/Bookings
 */

$month = isset( $_REQUEST['calendar_month'] ) ? absint( $_REQUEST['calendar_month'] ) : current_time( 'n' );
$year  = isset( $_REQUEST['calendar_year'] ) ? absint( $_REQUEST['calendar_year'] ) : current_time( 'Y' );
$day   = isset( $_REQUEST['calendar_day'] ) ? wc_clean( $_REQUEST['calendar_day'] ) : current_time( 'Y-m-d' );

if ( $year < ( date( 'Y' ) - 10 ) || $year > 2100 ) {
	$year = date( 'Y' );
}

if ( $month > 12 ) {
	$month = 1;
	$year ++;
}

if ( $month < 1 ) {
	$month = 12;
	$year --;
}

/*
 * WordPress start_of_week is in date format 'w'.
 * We are changing it to 'N' because we want ISO-8601.
 * Monday is our reference first day of the week.
 */
$start_of_week           = absint( get_option( 'start_of_week', 1 ) );
$start_of_week           = $start_of_week === 0 ? 7 : $start_of_week;

// On which day of the week the month starts
$month_start_day_of_week = absint( date( 'N', strtotime( "$year-$month-01" ) ) );

/*
 * Calculate column where the month start will be placed.
 * This calculates true modulo ( never negative ).
 */
$start_column            = ( 7 + ( $month_start_day_of_week - $start_of_week ) % 7 ) % 7;

/*
 * Calcu start date: how many days from the previous month we need to include,
 * in order to have calendar without empty days in the first row.
 */
$start_time              = strtotime( "-{$start_column} day", strtotime( "$year-$month-01" ) );

// How many days the month has.
$month_number_of_days    = date( 't', strtotime( "$year-$month-01" ) );

// On which day of the week the month ends.
$month_end_day_of_week   = absint( date( 'N', strtotime( "$year-$month-$month_number_of_days" ) ) );

/*
* Calculate column where the last day of month will be placed.
* This calculates true modulo ( never negative ).
*/
$end_column             = ( 7 + ( $month_end_day_of_week - $start_of_week ) % 7 ) % 7;

/*
 * Calculate end date: how many days from the next month we need to include.
 * We want to have calendar without empty days in the last row.
 */
$end_padding            = 6 - $end_column;
$end_time               = strtotime( "+{$end_padding} day midnight", strtotime( "$year-$month-$month_number_of_days" ) );
?>

<div id="wc-bookings-store-availability" class="wc_bookings_calendar_form">
	<p><?php esc_html_e( 'This section will set the availability of your store (ie Open and closed hours). All bookable products will adopt your store\'s availability.', 'woocommerce-bookings' ); ?></p>

	<?php require_once 'html-availability-nav.php'; ?>

	<div class="table_grid">

		<?php if ( ! WC_BOOKINGS_GUTENBERG_EXISTS ) { ?>
			<script type="text/javascript">
				<?php global $wp_locale; ?>
				jQuery( function() {
					jQuery( '.calendar_day' ).datepicker( {
						dateFormat: 'yy-mm-dd',
						firstDay: <?php echo esc_attr( get_option( 'start_of_week' ) ); ?>,
						monthNames: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->month ) ) ); ?>' ) ),
						monthNamesShort: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->month_abbrev ) ) ); ?>' ) ),
						dayNames: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->weekday ) ) ); ?>' ) ),
						dayNamesShort: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->weekday_abbrev ) ) ); ?>' ) ),
						dayNamesMin: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->weekday_initial ) ) ); ?>' ) ),
						defaultDate: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( $default_date ) ); ?>' ) ),
						numberOfMonths: 1,
						beforeShow: function( input, datePicker ) {
							datePicker.dpDiv.addClass('wc-bookings-ui-datpicker-widget');
						},
						onSelect: function( inputDate ) {
							document.location.search += '&calendar_day=' + inputDate;
						},
					} );
				} );
			</script>
		<?php } ?>

		<table class="wc_bookings_calendar widefat">
			<thead>
				<tr>
					<?php for ( $ii = get_option( 'start_of_week', 1 ); $ii < get_option( 'start_of_week', 1 ) + 7; $ii ++ ) : ?>
						<th><?php echo esc_html( date_i18n( _x( 'D', 'date format', 'woocommerce-bookings' ), strtotime( "next sunday +{$ii} day" ) ) ); ?></th>
					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php
					$timestamp        = $start_time;
					$current_date     = date( 'Y-m-d', current_time( 'timestamp' ) );
					$index            = 0;
					$class_names_list = array( 'wc-bookings__store-availability-day' );

					if ( date( 'n', $timestamp ) != absint( $month ) ) {
						$class_names_list[] = 'calendar-diff-month';
					}

					if ( ( $timestamp + DAY_IN_SECONDS ) < current_time( 'timestamp' ) ) {
						$class_names_list[] = 'wc-bookings-passed-day';
					} elseif ( false ) {
						$class_names_list[] = 'wc-bookings-unavailable-day';
					}
					$class_names = join( ' ', $class_names_list );

					$rules = WC_Data_Store::load( 'booking-global-availability' )->get_all_as_array(
						array( array(
							'key'     => 'range_type',
							'value'   => 'store_availability',
							'compare' => '=',
						) ),
						date( 'Y-m-d', $start_time ),
						date( 'Y-m-d', $end_time )
					);

					function rules_on_day( $rules, $timestamp_date ) {
						return array_filter( $rules, function( $rule ) use ( $timestamp_date ) {
							return date( 'Y-m-d', strtotime( $rule['start_date'] ) ) === $timestamp_date;
						} );
					}

					while ( $timestamp <= $end_time ) :
							$timestamp_date = date( 'Y-m-d', $timestamp );
							$is_today       = $timestamp_date === $current_date;
							?>
							<td width="14.285%" class="<?php echo $class_names ?>" data-timestamp="<?php echo $timestamp ?>">
								<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_booking&page=booking_calendar&tab=calendar&calendar_day=' . date( 'Y-m-d', $timestamp ) ) ); ?>"<?php
								echo ' class="day_link';
								if ( $is_today ) {
									echo ' current_day';
								}
								?>">
									<?php echo esc_html( date( 'j', $timestamp ) ); ?>
								</a>
								<div class="bookings">
									<ul>
										<?php
											foreach ( rules_on_day( $rules, $timestamp_date ) as $rule ) {
												printf( '<li>%s</li>', esc_html( $rule['title'] ) );
											}
										?>
									</ul>
								</div>
							</td>
							<?php
							$timestamp = strtotime( '+1 day', $timestamp );
							$index ++;

							if ( 0 === $index % 7 ) {
								echo '</tr><tr>';
							}
						endwhile;
					?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
