<?php

namespace WeDevs\PM_Pro\Core\WP;

class Output {

	public static function home_page() {
		require_once config( 'frontend.view_path' ) . '/index.html';
	}
}