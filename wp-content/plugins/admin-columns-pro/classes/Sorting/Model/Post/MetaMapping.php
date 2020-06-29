<?php

namespace ACP\Sorting\Model\Post;

class MetaMapping extends Meta {

	/**
	 * @var array
	 */
	private $sorted_fields;

	/**
	 * @param string $meta_key
	 * @param array  $sorted_fields
	 */
	public function __construct( $meta_key, array $sorted_fields ) {
		parent::__construct( $meta_key );

		$this->sorted_fields = $sorted_fields;
	}

	protected function get_orderby() {
		global $wpdb;

		$fields = implode( "','", array_map( 'esc_sql', $this->sorted_fields ) );

		return sprintf( "FIELD( acsort_postmeta.meta_value, '%s' ) %s, {$wpdb->posts}.ID", $fields, $this->get_order() );
	}

}