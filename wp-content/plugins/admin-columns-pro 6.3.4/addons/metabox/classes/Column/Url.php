<?php

namespace ACA\MetaBox\Column;

use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

class Url extends Text {

	use FilteredHtmlFormatTrait;

	public function get_value( $id ) {
		$url = parent::get_value( $id );

		return ac_helper()->html->link( $url, urldecode( str_replace( [ 'http://', 'https://' ], '', $url ) ) );
	}

}