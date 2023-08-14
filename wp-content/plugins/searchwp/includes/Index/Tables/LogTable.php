<?php

/**
 * SearchWP Database Table: Log.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Index\Tables;

use SearchWP\Index\Engine\Table;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the "searchwp_log" database table
 *
 * @since 4.0
 */
final class LogTable extends Table {

	/**
	 * @var string Table name.
	 */
	protected $name = 'log';

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
	protected $version = 201912091;

	/**
	 * @var array Upgrade routines.
	 */
	protected $upgrades = array();

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
		$this->schema = "logid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			query varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Search query for this search',
			tstamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of this search',
			hits mediumint(9) unsigned NOT NULL COMMENT 'Number of results for this search',
			engine varchar(191) NOT NULL DEFAULT 'default' COMMENT 'Engine used for this search',
			site mediumint(9) unsigned NOT NULL DEFAULT '1' COMMENT 'Site where this search took place',
			PRIMARY KEY (logid),
			KEY site_idx (site),
			KEY engine_idx (engine),
			KEY query_idx (query)";
	}
}
