<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Post\GroupFormatter;

use ACP\Helper\Select\Post\GroupFormatter;
use DateTime;
use WP_Post;

class PostDate implements GroupFormatter {

	public function format( WP_Post $post ): string {
		$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $post->post_date_gmt, wp_timezone() );

		return $date->format( 'F Y' );
	}

}