<?php

namespace ACP\ThirdParty\YoastSeo\Export;

use ACP\Export;

class Title extends Export\Model {

	public function get_value( $id ) {
		$title = get_post_meta( $id, '_yoast_wpseo_title', true );

		// If no specific
		if ( ! $title ) {
			$title = get_the_title( $id );
		}

		return $title;
	}

}