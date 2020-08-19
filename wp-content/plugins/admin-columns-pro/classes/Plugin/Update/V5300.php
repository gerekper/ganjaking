<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Type\ListScreenId;
use ACP\Search\SegmentRepository;

class V5300 extends Update {

	protected function set_version() {
		$this->version = '5.3.0';
	}

	public function apply_update() {
		global $wpdb;

		$meta_key_prefix = $wpdb->prefix . 'ac_preferences_segments_';

		$sql = "
			SELECT *
			FROM {$wpdb->usermeta}
			WHERE `meta_key` LIKE '{$meta_key_prefix}%'
		";

		$results = $wpdb->get_results( $sql );

		if ( ! is_array( $results ) ) {
			return;
		}

		$repository = new SegmentRepository();

		foreach ( $results as $row ) {
			$list_id = str_replace( $meta_key_prefix, '', $row->meta_key );

			if ( ! ListScreenId::is_valid_id( $list_id ) ) {
				continue;
			}

			$value = unserialize( $row->meta_value );

			if ( empty( $value['segments'] ) ) {
				continue;
			}

			foreach ( $value['segments'] as $segment_data ) {
				$data = unserialize( $segment_data['data'] );

				$repository->create(
					new ListScreenId( $list_id ),
					(int) $row->user_id,
					(string) $segment_data['name'],
					$data['url_parameters'],
					false
				);
			}
		}
	}

}