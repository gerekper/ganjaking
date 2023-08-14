<?php

/**
 * SearchWP Database Table: Index.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Index\Tables;

use SearchWP\Index\Engine\Table;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the "searchwp_index" database table
 *
 * @since 4.0
 */
final class IndexTable extends Table {

	/**
	 * @var string Table name.
	 */
	protected $name = 'index';

	/**
	 * @var bool
	 */
	protected $global = true;

	/**
	 * @var string Table charset.
	 */
	protected $charset = 'utf8mb4';

	/**
	 * @var string Table collation.
	 */
	protected $collate = 'utf8mb4_unicode_520_ci';

	/**
	 * @var string Database version.
	 */
	protected $version = 202002021;

	/**
	 * @var array Upgrade routines.
	 */
	protected $upgrades = [
		'202002021' => 202002021,
	];

	/**
	 * Index table constructor.
	 *
	 * @access public
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Setup the database schema.
	 *
	 * @access protected
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "indexid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			token bigint(20) unsigned NOT NULL COMMENT 'Token ID',
			occurrences bigint(20) unsigned NOT NULL COMMENT 'Number of token occurrences',
			id varchar(100) NOT NULL COMMENT 'Source ID',
			attribute varchar(80) NOT NULL COMMENT 'Attribute name',
			source varchar(80) NOT NULL COMMENT 'Source name',
			site mediumint(9) unsigned NOT NULL DEFAULT '1' COMMENT 'Site ID',
			PRIMARY KEY (indexid),
			KEY source_idx (source),
			KEY token_idx (token),
			KEY entry_idx (id, source, site),
			KEY attribute_idx (attribute)";
	}

	/**
	 * Upgrade to version 202002021
	 * - Add `entry_idx` index
	 *
	 * @since 4.1.5
	 *
	 * @return boolean True if upgrade was successful, false otherwise.
	 */
	protected function __202002021() {
		$this->get_db()->query( "ALTER TABLE {$this->table_name} CHANGE `id` `id` varchar(100) NOT NULL COMMENT 'Source ID'" );
		$this->get_db()->query( "ALTER TABLE {$this->table_name} ADD INDEX `entry_idx` (`id`, `source`, `site`)" );

		// Return success/fail
		return $this->is_success( true );
	}
}
