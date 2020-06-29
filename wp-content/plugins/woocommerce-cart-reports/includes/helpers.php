<?php

function cr_detect_search_engines( $useragent ) {
	$search_engines   = array(
		'Googlebot',
		'Slurp',
		'search.msn.com',
		'nutch',
		'simpy',
		'bot',
		'ASPSeek',
		'crawler',
		'msnbot',
		'Libwww-perl',
		'FAST',
		'Baidu',
	);
	$is_search_engine = false;
	foreach ( $search_engines as $search_engine ) {
		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) && false !== stripos( $useragent, $search_engine ) ) {
			$is_search_engine = true;
			break;
		}
	}
	if ( $is_search_engine ) {
		return true;
	}

	return false;

}

