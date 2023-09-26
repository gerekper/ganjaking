<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Model;

class Url extends Model {

	public function filter_by_user_url( $query ) {
		global $wpdb;

		$value = $this->get_filter_value();
		$sql = $wpdb->prepare( ' = %s', $value );
		if ( 'cpac_empty' === $value ) {
			$sql = " LIKE ''";
		}
		if ( 'cpac_nonempty' === $value ) {
			$sql = " NOT LIKE ''";
		}

		$query->query_where .= " AND {$wpdb->users}.user_url" . $sql;
	}

	public function get_filtering_vars( $vars ) {
		add_action( 'pre_user_query', [ $this, 'filter_by_user_url' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];

		if ( $values = $this->strategy->get_values_by_db_field( 'user_url' ) ) {
			$options = array_combine( $values, $values );
		}

		natcasesort( $options );

		return [
			'order'        => false,
			'options'      => $options,
			'empty_option' => $this->get_empty_labels(),
		];
	}

}