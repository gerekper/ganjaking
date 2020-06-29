<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

function ls_doing_it_wrong( $function, $message, $version ) {

	do_action( 'doing_it_wrong_run', $function, $message, $version );

    if( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true ) ) {
		trigger_error(
			sprintf(
				'%1$s was called <strong>incorrectly</strong>. %2$s',
				$function,
				$message
			)
		);
    }
}

function ls_wp_timezone() {

	try {
		return new DateTimeZone( ls_wp_timezone_string() );

	} catch (Exception $e) {
		return new DateTimeZone( 'UTC' );
	}
}


function ls_wp_timezone_string() {
	$timezone_string = get_option( 'timezone_string' );

    if ( $timezone_string ) {
        return $timezone_string;
    }

    $offset  = (float) get_option( 'gmt_offset' );
    $hours   = (int) $offset;
    $minutes = ( $offset - $hours );

    $sign      = ( $offset < 0 ) ? '-' : '+';
    $abs_hour  = abs( $hours );
    $abs_mins  = abs( $minutes * 60 );
    $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

    return $tz_offset;
}

function ls_date($format, $timestamp = null, $timezone = null ) {

	if( ! $timezone ) {
		$timezone = ls_wp_timezone();
	}

	if( ! $timestamp ) {
		$timestamp = time();
	}

	$datetime = date_create( '@' . $timestamp );
	$datetime->setTimezone( $timezone );

	return $datetime->format( $format );
}


function ls_date_create_for_timezone( $dateStr ) {

	$date = date_create( $dateStr, ls_wp_timezone() );
	return $date->format('U');
}

function layerslider_convert() {

	// Get old sliders if any
	$sliders = get_option('layerslider-slides', array());
	$sliders = is_array($sliders) ? $sliders : unserialize($sliders);

	// Create new storage in DB
	layerslider_create_db_table();

	// Iterate over them
	if(!empty($sliders) && is_array($sliders)) {
		foreach($sliders as $key => $slider) {
			LS_Sliders::add($slider['properties']['title'], $slider);
		}
	}

	// Remove old data and exit
	delete_option('layerslider-slides');
	wp_redirect( admin_url('admin.php?page=layerslider') );
	exit;
}


function lsSliderById($id) {

	ls_doing_it_wrong(
		__FUNCTION__,
		sprintf(
			'Deprecated function: use %sLS_Sliders::find( $id )%s instead.',
			'<a href="https://layerslider.kreaturamedia.com/developers/#sliders-intro" target="_blank">',
			'</a>'
		),
		'6.11.0'
	);

	$args = is_numeric($id) ? (int) $id : array('limit' => 1);
	$slider = LS_Sliders::find($args);

	if($slider == null) {
		return false;
	}

	return $slider;
}

function lsSliders($limit = 50, $desc = true, $withData = false) {

	ls_doing_it_wrong(
		__FUNCTION__,
		sprintf(
			'Deprecated function: use %sLS_Sliders::find( [ $options ] )%s instead.',
			'<a href="https://layerslider.kreaturamedia.com/developers/#sliders-intro" target="_blank">',
			'</a>'
		),
		'6.11.0'
	);

	$args = array();
	$args['limit'] = $limit;
	$args['order'] = ($desc === true) ? 'DESC' : 'ASC';
	$args['data'] = ($withData === true) ? true : false;

	$sliders = LS_Sliders::find($args);

	// No results
	if($sliders == null) {
		return array();
	}

	return $sliders;
}

?>
