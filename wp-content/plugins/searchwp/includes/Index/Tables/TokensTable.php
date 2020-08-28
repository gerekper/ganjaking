<?php

/**
 * SearchWP Database Table: Tokens.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Index\Tables;

use SearchWP\Index\Engine\Table;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the "searchwp_tokens" database table
 *
 * @since 4.0
 */
final class TokensTable extends Table {

	/**
	 * @var string Table name.
	 */
	protected $name = 'tokens';

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
	protected $collate = 'utf8mb4_bin';

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
		$this->schema = "id bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Canonical ID for this token',
		token varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
		stem varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY token (token),
		KEY token_idx (token(2)),
		KEY stem_idx (stem(2))";
	}
}
