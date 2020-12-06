<?php


namespace Premmerce\WooCommercePinterest\Installer;

use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \wpdb;

class Installer {

	const DB_VERSION = '1.7.18';

	const DB_VERSION_OPTION = 'woocommerce-pinterest-db-version';


	/**
	 * WPDB isntance
	 *
	 * @var wpdb
	 */
	private $db;

	/**
	 * DB charset collate
	 *
	 * @var string
	 */
	private $charsetCollate;

	/**
	 * GoogleCategoriesImporter instance
	 *
	 * @var GoogleCategoriesImporter
	 */
	private $importer;

	/**
	 * Installer constructor.
	 *
	 * @param wpdb $db
	 * @param GoogleCategoriesImporter $importer
	 */
	public function __construct( wpdb $db, GoogleCategoriesImporter $importer) {
		$this->db             = $db;
		$this->charsetCollate = $this->db->get_charset_collate();
		$this->importer       = $importer;
	}

	/**
	 * Create db table
	 *
	 */
	public function install() {
		$this->createTables();

		try {
			$this->importer->import();
		} catch (PinterestException $e) {
			$container = ServiceContainer::getInstance();
			$container->getLogger()->logPinterestException($e);
			$container->getNotifier()->flash(__('Failed to import product categories taxonomy.', 'woocommerce-pinterest'));
		}
	}


	private function createTables() {
		$sql = $this->generateCreateTablesQueries();

		if (! function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		dbDelta($sql);
	}

	/**
	 * Generate queries for create plugin DB tables
	 *
	 * @return string[]
	 */
	private function generateCreateTablesQueries() {
		$queries = array();

		foreach ($this->getDataBaseStructure() as $tableName => $tableStructure) {
			$queries[] = $this->generateCreateTableQuery($tableName, $tableStructure);
		}

		return $queries;
	}

	/**
	 * Generate query to create DB table
	 *
	 * @param $tableName
	 * @param array $tableStructure
	 *
	 * @return string
	 */
	private function generateCreateTableQuery( $tableName, array $tableStructure) {
		$columns = array();

		$uniqueKeyQueryPart = '';

		foreach ($tableStructure['columns'] as $columnName => $columnDefinition) {
			$columns[] = "{$columnName} {$columnDefinition}";
		}

		$primaryKeyQueryPart = '';
		if ($tableStructure['primary_key']) {
			//two spaces before primary key definition is required by dbDelta() function
			$primaryKeyQueryPart = ",\n PRIMARY KEY  ({$tableStructure['primary_key']})";
		}

		$uniqueColumns = implode(', ', $tableStructure['unique_key_columns']);

		if ($uniqueColumns) {
			$uniqueKeyQueryPart = ",\n UNIQUE KEY row_index ({$uniqueColumns})";
		}

		$sql = sprintf("CREATE TABLE {$tableName} (\n%s\n)\n {$this->charsetCollate};", implode(",\n", $columns) . $primaryKeyQueryPart . $uniqueKeyQueryPart);

		return $sql;
	}

	/**
	 * Return DB structure as array
	 *
	 * @return array
	 */
	private function getDataBaseStructure() {
		$blogPrefix = $this->db->get_blog_prefix();

		$structure = array(
			"{$blogPrefix}woocommerce_pinterest" => array(
				'columns' =>
					array(
						'id' => 'int(11) AUTO_INCREMENT NOT NULL',
						'post_id' => 'int(11) NOT NULL',
						'attachment_id' => 'int(11) NOT NULL',

						'pin_id' => 'varchar(30) NOT NULL',
						'pin_user_id' => 'varchar(30) NOT NULL',
						'board' => 'varchar(30) NOT NULL',

						'action' => 'varchar(30)',
						'status' => 'int(1) NOT NULL',
						'error' => 'longtext',

						'created_at' => 'datetime',
						'updated_at' => 'datetime',

						'produce_at' => 'datetime DEFAULT NULL'
					),
				'primary_key' => 'id',
				'unique_key_columns' => array(
					'post_id',
					'attachment_id',
					'pin_user_id',
					'board'
				),
			),
			"{$blogPrefix}woocommerce_pinterest_boards_mapping" => array(
				'columns' => array(
					'id' => 'int(11) AUTO_INCREMENT NOT NULL',
					'pin_user_id' => 'varchar(30) NOT NULL',
					'entity_id' => 'int (11) NOT NULL',
					'entity_type' => 'varchar(30) NOT NULL',
					'board_id' => 'varchar(30) NOT NULL',
				),
				'primary_key' => 'id',
				'unique_key_columns' => array(
					'pin_user_id',
					'entity_id',
					'entity_type',
					'board_id'
				),
			),
			"{$blogPrefix}woocommerce_pinterest_google_product_categories" => array(
				'columns' => array(
					'id' => 'int(11) NOT NULL',
					'parent_id' => 'int(11) NOT NULL',
					'name' => 'varchar(255)'
				),
				'primary_key' => 'id',
				'unique_key_columns' => array()
			),
			"{$blogPrefix}woocommerce_pinterest_google_categories_mapping" => array(
				'columns' => array(
					'id' => 'int(11) AUTO_INCREMENT NOT NULL',
					'woocommerce_category' => 'int(11) NOT NULL',
					'google_category' => 'int(11) NOT NULL'
				),
				'primary_key' => 'id',
				'unique_key_columns' => array(
					'woocommerce_category'
				)
			)
		);

		return $structure;
	}

	/**
	 * Clear configurations and drop table
	 */
	public static function uninstall() {
		delete_option('woocommerce_pinterest_start_bg');
		delete_option('woocommerce_pinterest_wait');
		delete_option('woocommerce_pinterest_user');
		delete_option('woocommerce_pinterest_token');
		delete_option(self::DB_VERSION_OPTION);
		delete_option('woocommerce_pinterest_settings');

		ServiceContainer::getInstance()->getInstaller()->dropTables();
	}

	/**
	 * Drop plugin tables
	 */
	public function dropTables() {
		$tables = array_keys($this->getDataBaseStructure());

		foreach ($tables as $table) {
			$sql = "DROP TABLE IF EXISTS {$table}";
			$this->db->query($sql);
		}
	}

	/**
	 * Run update process if needed
	 */
	public function update() {
		if ($this->dbUpdateNeeded()) {
			$this->updateTo2DotZero();
		}
	}

	/**
	 * Check if DB needs update
	 *
	 * @return bool
	 *
	 */
	private function dbUpdateNeeded() {
		$currentDBVersion = get_option(self::DB_VERSION_OPTION, '');
		return version_compare(self::DB_VERSION, $currentDBVersion, '>');
	}

	/**
	 * Update DB to version 2.0
	 *
	 */
	private function updateTo2DotZero() {
		$this->install();
		update_option(self::DB_VERSION_OPTION, self::DB_VERSION);
	}
}
