<?php

namespace ACP\Helper\Select\Group;

use AC;

class PostTypeType extends AC\Helper\Select\Group {

	/**
	 * @param object                  $post_type
	 * @param AC\Helper\Select\Option $option
	 *
	 * @return string
	 */
	public function get_label( $post_type, AC\Helper\Select\Option $option ) {
		if ( $post_type->public ) {
			return _x( 'Public', 'post_types', 'codepress-admin-columns' );
		}

		return $post_type->show_ui ? _x( 'Custom', 'post_types', 'codepress-admin-columns' ) : _x( 'Hidden', 'post_types', 'codepress-admin-columns' );
	}

	protected function sort( array $groups ) {
		return $groups;
	}

}