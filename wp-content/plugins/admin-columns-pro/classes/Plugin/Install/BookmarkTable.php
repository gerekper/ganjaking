<?php

namespace ACP\Plugin\Install;

use AC\Plugin\Install;
use ACP\Bookmark\SegmentRepository;

class BookmarkTable implements Install {

	public function install() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$collate = $wpdb->get_charset_collate();

		$table = "
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

		dbDelta( $table );
	}

}