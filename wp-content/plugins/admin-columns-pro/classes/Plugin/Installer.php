<?php

namespace ACP\Plugin;

use AC;
use ACP\Search\SegmentRepository;

final class Installer implements AC\Installer {

	public function install() {
		$this->create_tables();
	}

	private function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$collate = $wpdb->get_charset_collate();

		// Segments
		$tables[] = "
			CREATE TABLE " . $wpdb->prefix . SegmentRepository::TABLE . " (
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

		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
	}

}