<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_version;
?>
<div class="tablenav">
	<?php if ( ! WC_BOOKINGS_GUTENBERG_EXISTS ) { ?>
		<div class="date_selector">
			<div>
				<a class="prev" href="<?php
					echo esc_url( add_query_arg( array(
						'calendar_year'  => $month == 1 ? $year - 1 : $year,
						'calendar_month' => $month == 1 ? 12 : $month - 1,
					) ) );
				?>">&larr;</a>
			</div>
			<div>
				<input type="text" name="calendar_day" class="calendar_day" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( date_i18n( 'F', mktime( 0, 0, 0, $month, 10 ) ) . ' ' . $year ); ?>" />
			</div>
			<div>
				<a class="next" href="<?php
					echo esc_url( add_query_arg( array(
						'calendar_year'  => $month == 12 ? $year + 1 : $year,
						'calendar_month' => $month == 12 ? 1 : $month + 1,
					) ) );
				?>">&rarr;</a>
			</div>
		</div>
	<?php } else { ?>
		<div class="date-selector-popover">
			<div class="current_month">
				<a id="wc-bookings-datepicker-container-month">
					<?php echo current_time( 'F Y' ); ?>
					<span> ▾ </span>
				</a>
			</div>
			<div>
				<a class="change-date prev" href="<?php
					echo esc_url( add_query_arg( array(
						'calendar_year'  => $month == 1 ? $year - 1 : $year,
						'calendar_month' => $month == 1 ? 12 : $month - 1,
					) ) );
				?>">&larr;</a>
			</div>
			<div>
				<a class="change-date next" href="<?php
					echo esc_url( add_query_arg( array(
						'calendar_year'  => $month == 12 ? $year + 1 : $year,
						'calendar_month' => $month == 12 ? 1 : $month + 1,
					) ) );
				?>">&rarr;</a>
			</div>
		</div>
	<?php } ?>
	<?php
		if ( defined( 'WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR' ) && WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR ) {
			require_once 'html-availability-views-nav.php';
		}
	?>

</div>
