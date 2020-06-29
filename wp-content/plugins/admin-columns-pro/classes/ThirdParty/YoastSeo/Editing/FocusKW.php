<?php

namespace ACP\ThirdParty\YoastSeo\Editing;

use ACP\Editing;

class FocusKW extends Editing\Model {

	public function get_edit_value( $id ) {
		return get_post_meta( $id, '_yoast_wpseo_focuskw', true );
	}

	public function get_view_settings() {
		return [
			'type'        => 'text',
			'placeholder' => __( 'Enter your SEO Focus Keywords', 'codepress-admin-columns' ),
		];
	}

	public function save( $id, $value ) {
		return false !== update_post_meta( $id, '_yoast_wpseo_focuskw', $value );
	}

}