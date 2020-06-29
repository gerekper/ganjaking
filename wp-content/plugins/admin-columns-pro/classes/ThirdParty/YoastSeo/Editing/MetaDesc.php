<?php

namespace ACP\ThirdParty\YoastSeo\Editing;

use ACP\Editing;

class MetaDesc extends Editing\Model {

	public function get_edit_value( $id ) {
		return get_post_meta( $id, '_yoast_wpseo_metadesc', true );
	}

	public function get_view_settings() {
		return [
			'type'        => 'textarea',
			'placeholder' => __( 'Enter your SEO Meta Description', 'codepress-admin-columns' ),
		];
	}

	public function save( $id, $value ) {
		return false !== update_post_meta( $id, '_yoast_wpseo_metadesc', $value );
	}

}