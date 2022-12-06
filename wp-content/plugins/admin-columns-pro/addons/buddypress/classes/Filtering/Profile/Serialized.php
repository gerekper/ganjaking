<?php

namespace ACA\BP\Filtering\Profile;

use ACA\BP\Filtering;
use WP_User_Query;

class Serialized extends Filtering\Profile {

	/**
	 * @param WP_User_Query $query
	 */
	public function filter_by_callback( $query ) {
		global $wpdb;

		$where = $wpdb->prepare( 'value LIKE %s', '%' . $wpdb->esc_like( serialize( $this->get_filter_value() ) ) . '%' );

		$this->add_sql_where( $query, $where );
	}

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_xprofile_values() as $value ) {
			if ( is_serialized( $value ) ) {
				foreach ( unserialize( $value, [ 'allowed_classes' => false ] ) as $option ) {
					$options[] = $option;
				}
			}
		}

		$options = array_unique( $options );
		$options = array_combine( $options, $options );

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}