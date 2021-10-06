<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Calendar', 'woocommerce-bookings' ); ?></h2>

	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
		<input type="hidden" name="post_type" value="wc_booking" />
		<input type="hidden" name="page" value="booking_calendar" />
		<input type="hidden" name="calendar_month" value="<?php echo absint( $month ); ?>" />
		<input type="hidden" name="calendar_year" value="<?php echo absint( $year ); ?>" />
		<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
		<input type="hidden" name="tab" value="calendar" />

		<?php require 'html-calendar-nav.php'; ?>

		<?php if ( ! WC_BOOKINGS_GUTENBERG_EXISTS ) { ?>
			<script type="text/javascript">
				<?php global $wp_locale; ?>
				jQuery( function() {
					jQuery( '.calendar_day' ).datepicker( {
						dateFormat: 'yy-mm-dd',
						firstDay: <?php echo esc_attr( get_option( 'start_of_week' ) ); ?>,
						monthNames: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->month )  ) ); ?>' ) ),
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
							document.location.search += '&calendar_day=' + inputDate + '&view=day';
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
					$timestamp     = $start_time;
					$current_date  = date( 'Y-m-d', current_time( 'timestamp' ) );
					$index         = 0;
					$this->colours = $this->get_event_color_styles( $this->events ); 
					while ( $timestamp <= $end_time ) :
							$timestamp_date = date( 'Y-m-d', $timestamp );
							$is_today       = $timestamp_date === $current_date;
							?>
							<td width="14.285%" class="<?php
							if ( date( 'n', $timestamp ) != absint( $month ) ) {
								echo 'calendar-diff-month';
							}

							if ( ( $timestamp + DAY_IN_SECONDS ) < current_time( 'timestamp' ) ) {
								echo ' wc-bookings-passed-day';
							} elseif ( $this->is_day_unavailable( $timestamp_date ) ) {
								echo ' wc-bookings-unavailable-day';
							}
							?>">
								<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_booking&page=booking_calendar&view=day&tab=calendar&calendar_day=' . date( 'Y-m-d', $timestamp ) ) ); ?>"<?php
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
										$this->list_bookings(
											date( 'd', $timestamp ),
											date( 'm', $timestamp ),
											date( 'Y', $timestamp )
										);
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
	</form>
</div>
