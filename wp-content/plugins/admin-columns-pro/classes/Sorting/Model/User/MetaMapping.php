<?php

namespace ACP\Sorting\Model\User;

class MetaMapping extends Meta {

	/**
	 * @var array
	 */
	protected $sorted_fields;

	/**
	 * @param string $meta_key
	 * @param array  $sorted_fields
	 */
	public function __construct( $meta_key, $sorted_fields ) {
		parent::__construct( $meta_key );

		$this->sorted_fields = $sorted_fields;
	}

	/**
	 * @return string
	 */
	protected function get_order_by() {
		global $wpdb;
		$fields = implode( "','", array_map( 'esc_sql', $this->sorted_fields ) );

		return sprintf( "ORDER BY FIELD( acsort_usermeta.meta_value, '%s' ) %s, {$wpdb->users}.ID", $fields, $this->get_order() );
	}

}