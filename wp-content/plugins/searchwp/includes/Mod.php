<?php

/**
 * SearchWP Mod.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Source;

/**
 * Class Mod is responsible for modifying a Query with JOIN/WHERE/ORDER BY clauses.
 *
 * @since 4.0
 */
class Mod {

	/**
	 * The Source for this Mod (optional)
	 *
	 * @since 4.0
	 * @var Source
	 */
	private $source = false;

	/**
	 * Local database table name
	 *
	 * @since 4.0
	 * @var string
	 */
	private $local_table;

	/**
	 * Local table alias
	 *
	 * @since 4.0
	 * @var string
	 */
	private $local_table_alias;

	/**
	 * Foreign table alias.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $foreign_alias;

	/**
	 * JOIN ON clauses.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $on = [];

	/**
	 * WHERE clauses.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $where = [];

	/**
	 * Manually provided WHERE SQL clauses. Each can be a string or callable.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $raw_where_sql = [];

	/**
	 * Manually provided JOIN SQL clauses. Each can be a string or callable.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $raw_join_sql = [];

	/**
	 * ORDER BY clauses.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $order_by = [];

	/**
	 * Weight calculation clauses.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $weights = [];

	/**
	 * Relevance calculation clauses.
	 *
	 * @since 4.1.17
	 * @var array
	 */
	private $relevances = [];

	/**
	 * Custom columns.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $columns = [];

	/**
	 * Values for placeholders
	 *
	 * @since 4.0
	 * @var array
	 */
	private $values   = [];

	/**
	 * Mod constructor. If a Source is not provided, this Mod will apply to the Index.
	 *
	 * @since 4.0
	 * @param string|Source $source Limit this Mod to a Source.
	 */
	function __construct( $source = null ) {
		// The Index may not exist yet.
		if ( ! did_action( 'wp_loaded' ) && ! doing_action( 'wp_loaded' ) ) {
			do_action( 'searchwp\debug\log', 'Mod instantiated before wp_loaded', 'mod' );

			wp_die( new \WP_Error(
				'init',
				__( '\\SearchWP\\Mod cannot be instaniated until the wp_loaded action has fired.','searchwp' )
			) );
		}

		$index = \SearchWP::$index;

		if ( is_string( $source ) ) {
			$source = $index->get_source_by_name( $source );
		}

		$this->foreign_alias = $index->get_alias();

		// If a Source is passed, we can establish a few common defaults.
		if ( $source instanceof Source ) {
			$this->source      = $source;
			$this->local_table = $source->get_db_table();

			$this->on( $source->get_db_id_column(), [ 'column' => 'id' ] );
		} else {
			$this->local_table = $index->get_alias();
		}
	}

	/**
	 * Adds weight modification.
	 *
	 * @since 4.0
	 * @param mixed $sql The prepared SQL.
	 * @return void
	 */
	public function weight( $sql ) {
		$this->weights[] = $sql;
	}

	/**
	 * Adds index weight modification.
	 *
	 * @since 4.1.17
	 * @param mixed $sql The prepared SQL.
	 * @return void
	 */
	public function relevance( $sql ) {
		$this->relevances[] = $sql;
	}

	/**
	 * Adds column modification.
	 *
	 * @since 4.0
	 * @param mixed $sql The prepared SQL.
	 * @return void
	 */
	public function column_as( $sql, $column_name ) {
		$this->columns[ sanitize_key( $column_name ) ] = $sql;
	}

	/**
	 * Getter for custom columns.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_columns() {
		return $this->columns;
	}

	/**
	 * Getter for weight modifications.
	 *
	 * @sine 4.0
	 * @return array Prepared SQL statements for weight modifications.
	 */
	public function get_weights() {
		return $this->weights;
	}

	/**
	 * Getter for index weight modifications.
	 *
	 * @sine 4.1.17
	 * @return array Prepared SQL statements for weight modifications.
	 */
	public function get_relevances() {
		return $this->relevances;
	}

	/**
	 * Generate SQL for JOIN.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_join_sql() {
		global $wpdb;

		$join_where_on_relation = array_key_exists( 'relation', $this->on ) ? $this->on['relation'] : 'AND';
			if ( 'AND' !== $join_where_on_relation || 'OR' !== $join_where_on_relation ) {
				$join_where_on_relation = 'AND';
			}

		$ons = array_filter( array_map( function( $clause ) use ( $wpdb ) {
			if ( empty( $clause['foreign']['value'] ) && empty( $clause['foreign']['column'] ) ) {
				return false;
			}

			if ( ! empty( $clause['foreign']['value'] ) ) {
				return "{$this->local_table_alias}.{$clause['local']} = " . $wpdb->prepare( '%s', $clause['foreign']['value'] );
			} else {
				return "{$this->local_table_alias}.{$clause['local']} = {$this->foreign_alias}.{$clause['foreign']['column']}";
			}
		},
		$this->on ) );

		if ( empty( $ons ) && empty( $this->raw_join_sql ) ) {
			return '';
		}

		return "LEFT JOIN {$this->get_local_table()} {$this->local_table_alias} ON ("
			. implode(
				' ' . $join_where_on_relation . ' ',
				$ons
			)
		. ')';
	}

	/**
	 * Setter for raw JOIN SQL clause.
	 *
	 * @since 4.0
	 * @param string $sql The clause to add.
	 * @return void
	 */
	public function raw_join_sql( $sql ) {
		$this->raw_join_sql[] = $sql;
	}

	/**
	 * Getter for raw JOIN SQL clauses.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public function get_raw_join_sql() {
		return $this->raw_join_sql;
	}

	/**
	 * Setter for raw WHERE SQL clause.
	 *
	 * @since 4.0
	 * @param string $sql The clause to add.
	 * @return void
	 */
	public function raw_where_sql( $sql ) {
		$this->raw_where_sql[] = $sql;
	}

	/**
	 * Getter for raw WHERE SQL clauses.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public function get_raw_where_sql() {
		return $this->raw_where_sql;
	}

	/**
	 * Getter for source.
	 *
	 * @since 4.0
	 * @return SearchWP\Source
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * Setter for WHERE clause.
	 *
	 * @since 4.0
	 * @param array|Source $where The WHERE clauses. Pass Source for Source WHERE.
	 * @return void
	 */
	public function set_where( $where ) {
		if ( is_array( $where ) || $where instanceof Source ) {
			$this->where = $where;
		}
	}

	/**
	 * Getter for WHERE clause.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_where() {
		return $this->where;
	}

	/**
	 * Setter for local table.
	 *
	 * @since 4.0
	 * @param string $table The table name.
	 * @return void
	 */
	public function set_local_table( string $table ) {
		$this->local_table = $table;

		// This also needs to clear the ON clauses because they no longer apply.
		$this->on = [];
	}

	/**
	 * Getter for local table name.
	 *
	 * @since 4.0
	 * @return string The table name.
	 */
	public function get_local_table() {
		return $this->local_table;
	}

	/**
	 * Setter for foreign table alias.
	 *
	 * @since 4.0
	 * @param string $alias The alias to set.
	 * @return void
	 */
	public function set_foreign_alias( string $alias ) {
		$this->foreign_alias = $alias;
	}

	/**
	 * Getter for foreign alias.
	 *
	 * @since 4.0
	 * @return string The alias.
	 */
	public function get_foreign_alias( ) {
		return $this->foreign_alias;
	}

	/**
	 * Setter for local table alias.
	 *
	 * @since 4.0
	 * @param string $alias The alias to set.
	 * @return void
	 */
	public function set_local_table_alias( string $alias ) {
		$this->local_table_alias = $alias;
	}

	/**
	 * Getter for local table alias.
	 *
	 * @since 4.0
	 * @return string The alias.
	 */
	public function get_local_table_alias() {
		return $this->local_table_alias;
	}

	/**
	 * Adds an ON clause.
	 *
	 * @since 4.0
	 * @param string $local  The local column.
	 * @param array $foreign The foreign column. Key should be 'column' when
	 *                       referencing a local column, or 'value' when setting
	 *                       a value to compare against.
	 * @return void
	 */
	public function on( string $local, array $foreign ) {
		$this->on[] = [
			'local'   => $local,
			'foreign' => $foreign,
		];
	}

	/**
	 * Getter for ON clauses.
	 *
	 * @since 4.0
	 * @return array The clauses.
	 */
	public function get_on() {
		return $this->on;
	}

	/**
	 * Adds an ORDER BY clause.
	 *
	 * @since 4.0
	 * @param string $column    The local column name to order by.
	 * @param string $direction 'DESC' or 'ASC'
	 * @param int $priority     Priority of this clause (sorted ASC)
	 * @return void
	 */
	public function order_by( $column, $direction, $priority = 10 ) {
		if ( ! is_numeric( $priority ) ) {
			$priority = 10;
		}

		if ( is_string( $column ) && 'random' === substr( $column, 0, 6 ) ) {
			// The priority takes the place of the direction.
			if ( is_numeric( $direction ) ) {
				$priority = $direction;
			}

			// Check for seed.
			$seed = explode( ':', $column );
			if ( isset( $seed[1] ) ) {
				$seed = intval( $seed[1] );
			} else {
				$seed = '';
			}

			$this->order_by[ $priority ][] = [
				'column'    => "RAND({$seed})",
				'direction' => '',
			];
		} else {
			if ( ! empty( $direction ) ) {
				$direction === 'ASC' ? 'ASC' : 'DESC';
			}

			$this->order_by[ $priority ][] = [
				'column'    => is_callable( $column ) ? $column : $this->local_table_alias . $column,
				'direction' => $direction,
			];
		}
	}

	/**
	 * Getter for ORDER BY clauses.
	 *
	 * @since 4.0
	 * @return array The clauses.
	 */
	public function get_order_by() {
		return $this->order_by;
	}

	/**
	 * Setter for values.
	 *
	 * @since 4.0
	 * @param array $values The values to set.
	 * @return void
	 */
	public function set_values( array $values ) {
		$this->values = $values;
	}

	/**
	 * Getter for values.
	 *
	 * @since 4.0
	 * @return array The values.
	 */
	public function get_values() {
		return $this->values;
	}
}
