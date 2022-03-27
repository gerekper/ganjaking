<?php

namespace ACP\Export\Model\Post;

use AC;
use ACP\Export\Model;

/**
 * Comment Count exportability model
 * @since 4.1
 */
class CommentCount extends Model {

	public function get_value( $id ) {
		$setting = $this->column->get_setting( 'comment_count' );
		$value = false;

		if ( $setting instanceof AC\Settings\Column\CommentCount ) {
			$value = $setting->get_comment_count( $id );
		}

		return $value;
	}

}