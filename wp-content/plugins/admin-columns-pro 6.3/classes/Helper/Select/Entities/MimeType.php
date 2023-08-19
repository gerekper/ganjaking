<?php

namespace ACP\Helper\Select\Entities;

use AC;
use ACP\Helper\Select\Value;

class MimeType extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\MimeType();
		}

		$entities = $this->get_mimetypes( $args['post_type'] );

		parent::__construct( $entities, $value );
	}

	public function get_total_pages() {
		return 1;
	}

	public function get_page() {
		return 1;
	}

	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

	/**
	 * @param string $post_type
	 *
	 * @return object[]
	 */
	private function get_mimetypes( $post_type ) {
		global $wpdb;

		$sql = "
			SELECT DISTINCT post_mime_type
			FROM {$wpdb->posts}
			WHERE post_type = %s
			AND post_mime_type <> ''
			ORDER BY 1
		";

		$values = $wpdb->get_col( $wpdb->prepare( $sql, $post_type ) );

		if ( empty( $values ) ) {
			return [];
		}

		return $values;
	}

}