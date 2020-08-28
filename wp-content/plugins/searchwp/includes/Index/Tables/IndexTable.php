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
		$this->schema = "token bigint(20) unsigned NOT NULL COMMENT 'Token ID',
			occurrences bigint(20) unsigned NOT NULL COMMENT 'Number of token occurrences',
			id varchar(255) NOT NULL COMMENT 'Source ID',
			attribute varchar(80) NOT NULL COMMENT 'Attribute name',
			source varchar(80) NOT NULL COMMENT 'Source name',
			site mediumint(9) unsigned NOT NULL DEFAULT '1' COMMENT 'Site ID',
			KEY source_idx (source),
			KEY token_idx (token),
			KEY attribute_idx (attribute)";
	}
}
