<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Type\ListScreenId;
use ACP\Bookmark\SegmentRepository;
use Exception;

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
				if ( empty( $segment_data['name'] ) || empty( $segment_data['data'] ) ) {
					continue;
				}

				$data = unserialize( $segment_data['data'] );

				if ( empty( $data ) ) {
					continue;
				}

				$url_parameters = isset( $data['url_parameters'] ) && is_array( $data['url_parameters'] )
					? $data['url_parameters']
					: [];

				try {
					$repository->create(
						new ListScreenId( $list_id ),
						(int) $row->user_id,
						(string) $segment_data['name'],
						$url_parameters,
						false
					);
				} catch ( Exception $e ) {
					continue;
				}
			}
		}
	}

}