<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class ContentTotalImageSize implements FormatValue {

	public function format_value( $post_content ) {
		$urls = array_unique( ac_helper()->image->get_image_urls_from_string( $post_content ) );

		$total_size = 0;

		foreach ( $urls as $url ) {
			$size = ac_helper()->image->get_local_image_size( $url );

			if ( $size > 0 ) {
				$total_size += $size;
			}
		}

		return $total_size > 0
			? $total_size
			: false;
	}

}