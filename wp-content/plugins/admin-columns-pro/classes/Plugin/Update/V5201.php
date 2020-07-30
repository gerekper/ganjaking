<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use ACP\Search\Middleware\Mapping;
use ACP\Search\Middleware\Mapping\Rule;

class V5201 extends Update {

	protected function set_version() {
		$this->version = '5.2.1';
	}

	public function apply_update() {
		global $wpdb;

		$sql = "
			SELECT *
			FROM {$wpdb->usermeta}
			WHERE `meta_key` LIKE '{$wpdb->prefix}ac_preferences_search_segments_%'
		";

		$preferences = $wpdb->get_results( $sql );

		if ( ! is_array( $preferences ) ) {
			return;
		}

		foreach ( $preferences as $preference ) {
			$segments = unserialize( $preference->meta_value );

			foreach ( $segments['segments'] as $k => $segment ) {
				$data = unserialize( $segment['data'] );

				$data['url_parameters'] = [];

				if ( isset( $data['rules'] ) ) {
					if ( is_array( $data['rules'] ) && $data['rules'] ) {
						$mapped_rules = [];

						foreach ( $data['rules'] as $rule ) {
							$mapped_rules[] = $this->map_rule( $rule );
						}

						$data['url_parameters']['ac-rules'] = json_encode( [
							'condition' => 'AND',
							'rules'     => $mapped_rules,
							'valid'     => true,
						] );
					}

					unset( $data['rules'] );
				}

				if ( isset( $data['order'] ) ) {
					$data['url_parameters']['order'] = $data['order'];
					unset( $data['order'] );
				}

				if ( isset( $data['orderby'] ) ) {
					$data['url_parameters']['orderby'] = $data['orderby'];
					unset( $data['orderby'] );
				}

				$segments['segments'][ $k ]['data'] = serialize( $data );
			}

			$meta_key = str_replace( '_search_segments_', '_segments_', $preference->meta_key );

			update_user_meta(
				$preference->user_id,
				$meta_key,
				$segments
			);
		}
	}

	private function map_rule( $rule ) {
		$rule_mapping = new Rule( Mapping::RESPONSE );
		$operator_mapping = new Mapping\Operator( Mapping::RESPONSE );
		$value_type = new Mapping\ValueType( Mapping::RESPONSE );

		return [
			$rule_mapping->name        => $rule['name'],
			$rule_mapping->operator    => $operator_mapping->{$rule['operator']},
			$rule_mapping->value       => $rule['value'],
			$rule_mapping->value_type  => $value_type->{$rule['value_type']},
			$rule_mapping->value_label => $rule['value_label'],
		];
	}

}