<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Calendar', 'woocommerce-bookings' ); ?></h2>

	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
		<input type="hidden" name="post_type" value="wc_booking" />
		<input type="hidden" name="page" value="booking_calendar" />
		<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
		<input type="hidden" name="tab" value="calendar" />
		<input type="hidden" name="calendar_day" value="<?php echo esc_attr( $day ); ?>" />

		<?php include( 'html-calendar-nav.php' ); ?>

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
						numberOfMonths: 1,
						beforeShow: function( input, datePicker) {
							datePicker.dpDiv.addClass('wc-bookings-ui-datpicker-widget');
						}
					} );
				} );
			</script>
		<?php } ?>

		<div class="calendar_spacer">
			<div class="calendar_spacer_corner"></div>
		</div>

		<div class="calendar_scroll_container">
			<div class="calendar_days">
				<ul class="hours">
					<?php for ( $i = 0; $i < 24; $i ++ ) : ?>
						<li><label>
						<?php
							echo esc_html( date_i18n( 'ga', strtotime( "midnight +{$i} hour" ) ) );
						?>
						</label></li>
					<?php endfor; ?>
				</ul>
				<ul class="bookings">
					<?php $this->list_bookings_for_day(); ?>
					<?php $this->list_global_availability_for_day(); ?>
				</ul>
			</div>
		</div>
	</form>
</div>
