<?php

/**
 * SearchWP Database Table: Status.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Index\Tables;

use SearchWP\Index\Engine\Table;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the "searchwp_status" database table
 *
 * @since 4.0
 */
final class StatusTable extends Table {

	/**
	 * @var string Table name.
	 */
	protected $name = 'status';

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
		$this->schema = "statusid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			id varchar(100) NOT NULL COMMENT 'Source ID',
			source varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
			queued timestamp NULL DEFAULT NULL COMMENT 'Whether this entry is queued for indexing',
			indexed timestamp NULL DEFAULT NULL COMMENT 'Whether this entry is indexed',
			omitted timestamp NULL DEFAULT NULL COMMENT 'Whether this entry is omitted',
			site bigint(20) unsigned NOT NULL COMMENT 'Site ID',
			PRIMARY KEY (statusid),
			KEY id_idx (id),
			KEY site_idx (site),
			KEY source_idx (source)";
	}
}
