<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;

/**
 * Depth of the current page (number of ancestors + 1)
 * @since 2.3.4
 */
class LinkCount extends AC\Column
	implements Export\Exportable {

	public function __construct() {
		$this->set_type( 'column-linkcount' );
		$this->set_label( __( 'Link Count', 'codepress-admin-columns' ) );
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$links = $this->get_raw_value( $id );

		if ( ! $links ) {
			return $this->get_empty_char();
		}

		$internal = count( $links[0] );
		$external = count( $links[1] );

		return sprintf( '%s | %s',
			ac_helper()->html->tooltip( $internal, __( 'Internal Links', 'codepress-admin-columns' ) ),
			ac_helper()->html->tooltip( $external, __( 'External Links', 'codepress-admin-columns' ) )
		);
	}

	/**
	 * @param $id
	 *
	 * @return array|false
	 */
	public function get_raw_value( $id ) {
		$internal_domains = apply_filters( 'ac/column/linkcount/domains', [ home_url() ] );

		return ac_helper()->html->get_internal_external_links( get_post_field( 'post_content', $id ), $internal_domains );
	}

	public function is_valid() {
		return class_exists( 'DOMDocument', false );
	}

	public function export() {
		return new Export\Model\Post\LinkCount( $this );
	}

}