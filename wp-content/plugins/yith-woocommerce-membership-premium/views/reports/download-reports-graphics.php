<?php
/*
 * Template for Reports Page
 */
?>

<?php


$user_id = ! empty( $user_id ) ? $user_id : '';

$download_types = apply_filters( 'yith_wcmbs_download_report_graphics_download_types', array( '' => '#3994D8' ), $user_id );

$ten_days_start_timestamp = yith_wcmbs_local_strtotime_midnight_to_utc( '-9days' );
$ten_days_end_timestamp   = yith_wcmbs_local_strtotime_midnight_to_utc( 'tomorrow' );
$ten_days_start_date      = date( 'Y-m-d H:i:s', $ten_days_start_timestamp );
$ten_days_end_date        = date( 'Y-m-d H:i:s', $ten_days_end_timestamp );

$ten_days_results           = array();
$ten_days_date_result_array = array();
$ten_days_max_per_type      = array();
foreach ( $download_types as $download_type => $type_color ) {

	$ten_days_args = array(
		'select'   => 'timestamp_date as date, COUNT(*) as count',
		'group_by' => 'day(timestamp_date)',
		'order_by' => 'timestamp_date',
		'order'    => 'ASC',
		'where'    => array(
			array(
				'key'     => 'type',
				'value'   => $download_type,
				'compare' => '=',
			),
			array(
				'key'     => 'timestamp_date',
				'value'   => $ten_days_start_date,
				'compare' => '>=',
			),
			array(
				'key'     => 'timestamp_date',
				'value'   => $ten_days_end_date,
				'compare' => '<',
			),
		),
	);

	if ( ! empty( $user_id ) ) {
		$ten_days_args['where'][] = array(
			'key'   => 'user_id',
			'value' => $user_id,

		);
	}

	$ten_days_results[ $download_type ] = YITH_WCMBS_Downloads_Report()->get_download_reports( $ten_days_args );

	foreach ( $ten_days_results[ $download_type ] as $ten_days_result ) {
		$ten_days_current_date                                                  = date( 'j M', strtotime( $ten_days_result->date ) );
		$ten_days_date_result_array[ $download_type ][ $ten_days_current_date ] = $ten_days_result->count;
	}

	$ten_days_max_per_type[ $download_type ] = 0;
	if ( ! empty( $ten_days_date_result_array[ $download_type ] ) ) {
		$ten_days_max_per_type[ $download_type ] = absint( max( $ten_days_date_result_array[ $download_type ] ) );
	}
}


$ten_days_counter_row = '';
$ten_days_label_row   = '';
$ten_days_max         = absint( max( array_values( $ten_days_max_per_type ) ) );

for ( $i = 0; $i < 10; $i ++ ) {
	$ten_days_current_date = date( 'j M', strtotime( '+' . $i . 'days', $ten_days_start_timestamp ) );
	$grafic                = '';
	$loop                  = 0;

	foreach ( $download_types as $download_type => $type_color ) {
		$ten_days_counter = 0;
		if ( isset( $ten_days_date_result_array[ $download_type ][ $ten_days_current_date ] ) ) {
			$ten_days_counter = absint( $ten_days_date_result_array[ $download_type ][ $ten_days_current_date ] );
		}

		$height = $ten_days_max > 0 ? ( $ten_days_counter / $ten_days_max * 100 ) : 0;
		$color  = wc_hex_darker( $type_color, ( 100 - $height ) / 2 );
		$width  = 70 / count( $download_types );
		$left   = 15 + $width * $loop;
		$grafic .= "<div class='yith-wcmbs-reports-downloads-table-date-grafic tips-top' data-tip='$ten_days_counter' style='height:$height%; width:$width%; left:$left%; background:$color;'></div>";
		$loop ++;
	}
	$ten_days_counter_row .= "<td>$grafic</td>";
	$ten_days_label_row   .= '<th>' . $ten_days_current_date . '</th>';
}


$first_day_current_month       = yith_wcmbs_local_strtotime_midnight_to_utc( date( 'Y-m-01', yith_wcmbs_local_strtotime() ) );
$twelve_months_start_timestamp = strtotime( date( 'Y-m-01', strtotime( '-11 months', $first_day_current_month ) ) );
$twelve_months_end_timestamp   = yith_wcmbs_local_strtotime_midnight_to_utc( 'tomorrow' );
$twelve_months_start_date      = date( 'Y-m-d H:i:s', $twelve_months_start_timestamp );
$twelve_months_end_date        = date( 'Y-m-d H:i:s', $twelve_months_end_timestamp );

$twelve_months_results           = array();
$twelve_months_max_per_type      = array();
$twelve_months_date_result_array = array();

foreach ( $download_types as $download_type => $type_color ) {
	$twelve_months_args = array(
		'select'   => 'timestamp_date as date, COUNT(*) as count',
		'group_by' => 'month(timestamp_date)',
		'order_by' => 'timestamp_date',
		'order'    => 'ASC',
		'where'    => array(
			array(
				'key'     => 'type',
				'value'   => $download_type,
				'compare' => '=',
			),
			array(
				'key'     => 'timestamp_date',
				'value'   => $twelve_months_start_date,
				'compare' => '>=',
			),
			array(
				'key'     => 'timestamp_date',
				'value'   => $twelve_months_end_date,
				'compare' => '<',
			),
		),
	);

	if ( ! empty( $user_id ) ) {
		$twelve_months_args['where'][] = array(
			'key'   => 'user_id',
			'value' => $user_id,

		);
	}

	$twelve_months_args = apply_filters( 'yith_wcmbs_download_report_graphics_twelve_months_args', $twelve_months_args, $user_id );

	$twelve_months_results[ $download_type ] = YITH_WCMBS_Downloads_Report()->get_download_reports( $twelve_months_args );

	foreach ( $twelve_months_results[ $download_type ] as $twelve_months_result ) {
		$twelve_months_current_date                                                       = date( 'M', strtotime( $twelve_months_result->date ) );
		$twelve_months_date_result_array[ $download_type ][ $twelve_months_current_date ] = $twelve_months_result->count;
	}

	$twelve_months_max_per_type[ $download_type ] = 0;
	if ( ! empty( $twelve_months_date_result_array[ $download_type ] ) ) {
		$twelve_months_max_per_type[ $download_type ] = absint( max( $twelve_months_date_result_array[ $download_type ] ) );
	}
}

$twelve_months_counter_row = '';
$twelve_months_label_row   = '';
$twelve_months_max         = absint( max( array_values( $twelve_months_max_per_type ) ) );
$twelve_months_max         = apply_filters( 'yith_wcmbs_download_report_graphics_twelve_months_max', $twelve_months_max, $user_id );

for ( $i = 0; $i < 12; $i ++ ) {
	$twelve_months_current_date = date( 'M', strtotime( '+' . $i . 'months', $twelve_months_start_timestamp ) );
	$grafic                     = '';
	$loop                       = 0;
	foreach ( $download_types as $download_type => $type_color ) {
		$twelve_months_counter = 0;
		if ( isset( $twelve_months_date_result_array[ $download_type ][ $twelve_months_current_date ] ) ) {
			$twelve_months_counter = absint( $twelve_months_date_result_array[ $download_type ][ $twelve_months_current_date ] );
		}

		$height = $twelve_months_max > 0 ? ( $twelve_months_counter / $twelve_months_max * 100 ) : 0;
		$color  = wc_hex_darker( $type_color, ( 100 - $height ) / 2 );
		$width  = 70 / count( $download_types );
		$left   = 15 + $width * $loop;
		$grafic .= "<div class='yith-wcmbs-reports-downloads-table-date-grafic tips-top' data-tip='$twelve_months_counter' style='height:$height%; width:$width%; left:$left%; background:$color;'></div>";
		$loop ++;
	}
	$twelve_months_counter_row .= "<td>$grafic</td>";
	$twelve_months_label_row   .= '<th>' . $twelve_months_current_date . '</th>';
}
?>
	<div class="yith-wcmbs-tabs">
		<ul>
			<li><a href="#yith-wcmbs-reports-downloads-ten-days"><?php esc_html_e( '10 days', 'yith-woocommerce-membership' ); ?></a></li>
			<li><a href="#yith-wcmbs-reports-downloads-twelve-months"><?php esc_html_e( '12 months', 'yith-woocommerce-membership' ); ?></a></li>
		</ul>

		<div id="yith-wcmbs-reports-downloads-ten-days">
			<p class="yith-wcmbs-reports-downloads-max"> <?php echo sprintf( esc_html__( 'Max downloads: %s', 'yith-woocommerce-membership' ), '<strong>' . esc_html( $ten_days_max ) . '</strong>' ) ?></p>
			<table class="yith-wcmbs-reports-downloads-table-date">
				<tr>
					<?php echo wp_kses_post( $ten_days_counter_row ); ?>
				</tr>
				<tr>
					<?php echo wp_kses_post( $ten_days_label_row ); ?>
				</tr>
			</table>
		</div>
		<div id="yith-wcmbs-reports-downloads-twelve-months">
			<p class="yith-wcmbs-reports-downloads-max"> <?php echo sprintf( esc_html__( 'Max downloads: %s', 'yith-woocommerce-membership' ), '<strong>' . esc_html( $twelve_months_max ) . '</strong>' ) ?></p>
			<table class="yith-wcmbs-reports-downloads-table-date">
				<tr>
					<?php echo wp_kses_post( $twelve_months_counter_row ); ?>
				</tr>
				<tr>
					<?php echo wp_kses_post( $twelve_months_label_row ); ?>
				</tr>
			</table>
		</div>
	</div>

<?php do_action( 'yith_wcmbs_download_report_after_graphics', $user_id ); ?>