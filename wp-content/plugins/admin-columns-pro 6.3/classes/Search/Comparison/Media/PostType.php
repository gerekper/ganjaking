<?php

namespace ACP\Search\Comparison\Media;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class PostType extends Comparison
	implements Comparison\RemoteValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$sub_query = $wpdb->prepare( "SELECT ID from {$wpdb->posts} WHERE post_type = %s", $value->get_value() );

		$bindings = new Bindings();
		$bindings->where( "{$wpdb->posts}.post_parent IN({$sub_query})" );

		return $bindings;
	}

	public function get_values() {
		$options = [];

		foreach ( $this->get_post_types() as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( $post_type_object ) {
				$options[] = new AC\Helper\Select\Option( $post_type_object->name, $post_type_object->labels->singular_name );
			}
		}

		return new AC\Helper\Select\Options( $options );
	}

	/**
	 * Get values by post field
	 * @return array
	 */
	public function get_post_types() {
		global $wpdb;

		$sql = "
			SELECT DISTINCT posts.post_type
			FROM {$wpdb->posts} AS attachments
			INNER JOIN $wpdb->posts AS posts ON attachments.post_parent = posts.ID
			WHERE attachments.post_type = %s
			AND posts.post_type != %s
			AND attachments.post_parent <> ''
			ORDER BY 1
		";

		$values = $wpdb->get_col( $wpdb->prepare( $sql, 'attachment', 'attachment' ) );

		if ( empty( $values ) ) {
			return [];
		}

		return $values;
	}

}