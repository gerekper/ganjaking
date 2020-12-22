<?php

namespace WBCR\Factory_439\Premium\Interfaces;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd
 * @version       1.0
 */
interface License {

	public function get_key();

	public function get_hidden_key();

	public function get_expiration_time( $format = 'time' );

	public function get_sites_quota();

	public function get_count_active_sites();

	public function is_valid();

	public function is_lifetime();

}