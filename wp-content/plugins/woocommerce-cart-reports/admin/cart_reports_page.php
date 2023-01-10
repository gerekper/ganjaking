<?php

class AV8_Cart_Reports_Page {

	public function __construct() {
		add_action( 'woocommerce_reports_charts', array( $this, 'cart_manager_tab' ) );
	}

	public function cart_manager_tab( $tabs ) {
		$tabs['carts'] = array(
			'title'   => __( 'Carts', 'woocommerce' ),
			'reports' => array(
				'carts_by_date'    => array(
					'title'       => __( 'Carts By Date', 'woocommerce' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => 'get_report_cart_reports'
				),
				'carts_by_product' => array(
					'title'       => __( 'Carts By Product', 'woocommerce' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => 'get_report_cart_reports'
				),
			)
		);

		return $tabs;
	}
} //END CLASS


function get_report_cart_reports( $name ) {
	$name  = sanitize_title( str_replace( '_', '-', $name ) );
	$class = 'WC_Report_' . str_replace( '-', '_', $name );
	include_once 'reports/class-wc-report-' . $name . '.php';


	if ( ! class_exists( $class ) ) {
		return;
	}

	$report = new $class();
	$report->output_report();
}

/**
 *
 *
 */
function woocommerce_tooltip_js_carts() {
	?>
	function showTooltip(x, y, contents) {
	jQuery('
	<div id="tooltip">' + contents + '</div>').css( {
	position: 'absolute',
	display: 'none',
	top: y + 5,
	left: x - 50,
	padding: '5px 10px',
	border: '3px solid #3da5d5',
	background: '#288ab7'
	}).appendTo("body").fadeIn(200);
	}

	var previousPoint = null;
	jQuery("#placeholder").bind("plothover", function (event, pos, item) {
	if (item) {
	if (previousPoint != item.dataIndex) {
	previousPoint = item.dataIndex;

	jQuery("#tooltip").remove();

	if (item.series.label=="<?php echo esc_js( __( 'Converted Carts', 'woocommerce' ) ); ?>") {

	var y = item.datapoint[1].toFixed(2);
	showTooltip(item.pageX, item.pageY, item.series.label + " - " + Math.round(y));

	} else if (item.series.label=="<?php echo esc_js( __( 'Open & Abandoned Carts', 'woocommerce' ) ); ?>") {

	var y = item.datapoint[1];
	showTooltip(item.pageX, item.pageY, item.series.label + " - " + Math.round(y));

	} else {

	var y = item.datapoint[1];
	showTooltip(item.pageX, item.pageY, y);
	}
	}
	}
	else {
	jQuery("#tooltip").remove();
	previousPoint = null;
	}
	});
	<?php
}

function carts_abandoned_within_range( $where = '' ) {
	global $start_date, $end_date, $woocommerce_cart_reports_options, $offset;
	$timeout = $woocommerce_cart_reports_options['timeout'];

	$current_date = date( 'Y-m-d' );

	if ( $end_date == strtotime( $current_date ) ) {
		$end_date = time() + ( $offset * 3600 );
		$before   = date( 'Y-m-d G:i:s', $end_date - $timeout );
	} else {
		$before = date( 'Y-m-d', strtotime( '+1 day', $end_date ) );
	}

	$after   = date( 'Y-m-d', (int) $start_date );

	return sprintf( ' AND post_modified > %s AND post_modified < %s', $after, $before );
}


?>
