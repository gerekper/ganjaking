<?php

namespace ACP\ThirdParty\YoastSeo\Editing;

use ACP\Editing;

class Title extends Editing\Model {

	public function get_edit_value( $id ) {
		return get_post_meta( $id, '_yoast_wpseo_title', true );
	}

	public function get_view_settings() {
		return [
			'type'        => 'text',
			'placeholder' => __( 'Enter your SEO Title', 'codepress-admin-columns' ),
		];
	}

	public function save( $id, $value ) {
		return false !== update_post_meta( $id, '_yoast_wpseo_title', $value );
	}

}