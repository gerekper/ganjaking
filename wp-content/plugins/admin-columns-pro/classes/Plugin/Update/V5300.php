<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Plugin\Version;
use AC\Type\ListScreenId;
use DateTime;
use Exception;

class V5300 extends Update {

	public function __construct() {
		parent::__construct( new Version( '5.3.0' ) );
	}

	public function apply_update() {
		$this->create_table();
		$this->migrate_bookmarks();
	}

	public function migrate_bookmarks() {
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
					$list_screen_id = new ListScreenId( $list_id );

					$wpdb->insert(
						$wpdb->prefix . 'ac_segments',
						[
							'list_screen_id' => $list_screen_id->get_id(),
							'user_id'        => (int) $row->user_id,
							'name'           => (string) $segment_data['name'],
							'url_parameters' => serialize( $url_parameters ),
							'global'         => false,
							'date_created'   => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
						],
						[
							'%s',
							'%d',
							'%s',
							'%s',
							'%d',
							'%s',
						]
					);
				} catch ( Exception $e ) {
					continue;
				}
			}
		}
	}

	public function create_table() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . 'ac_segments';

		$table = "
			CREATE TABLE $table_name (
				id bigint(20) unsigned NOT NULL auto_increment,
				list_screen_id varchar(20) NOT NULL default '',
				user_id bigint(20),
				name varchar(255) NOT NULL default '',
				url_parameters mediumtext,
				global tinyint(1),
				date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY (id)
			) $collate;
		";

		dbDelta( $table );
	}

}