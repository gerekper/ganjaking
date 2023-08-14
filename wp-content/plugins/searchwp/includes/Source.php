<?php
/**
 * SearchWP Source Class.
 *
 * @package     SearchWP
 * @copyright   Copyright (c) 2019
 * @license     https://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.0
 */
namespace SearchWP;

use SearchWP\Rule;
use SearchWP\Utils;
use SearchWP\Option;
use SearchWP\Entries;
use SearchWP\Attribute;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class Source represents a data type that can be indexed by defining
 *     - How to track index status
 *     - How to define weight-able attributes
 *     - How to define rules for exclusion/limiting
 *     - How to retrieve data to be indexed
 *     - How to load data when a result is found
 *
 * @since 4.0
 */
abstract class Source implements \JsonSerializable {

	/**
	 * Name used for canonical reference to source.
	 *
	 * @since 4.0
	 * @var   string
	 */
	protected $name = '';

	/**
	 * Labels for this Source.
	 *
	 * @since 4.0
	 * @var   string[]
	 */
	protected $labels = [];

	/**
	 * Options for this Source. Options are stored per Source per Engine.
	 *
	 * @since 4.0
	 * @var   array
	 */
	private $options = [];

	/**
	 * Rules for this Source. Returns an array of arrays, each of which represents
	 * an rule that influences availability of Entries for this Source. Each Rule
	 * can be applied to this Source per Engine and it will control what Entries
	 * are considered/excluded. Stored as groups.
	 *
	 * Example Rules for WP_Post: Taxonomy, publish date.
	 *
	 * @since 4.0
	 * @var   Rule[]
	 */
	protected $rules = [];

	/**
	 * Notices for this Source. Displayed on the settings screen when applicable.
	 *
	 * @since 4.0
	 * @var   string[]
	 */
	private $notices = [];

	/**
	 * Database table name used to track index status.
	 *
	 * @since 4.0
	 * @var   string
	 */
	protected $db_table  = '';

	/**
	 * Column name used to track index status.
	 *
	 * @since 4.0
	 * @var   string
	 */
	protected $db_id_column = '';

	/**
	 * Clauses to control which rows are retrieved from the database.
	 *
	 * @since 4.0
	 * @var   array
	 */
	private $db_where = [];

	/**
	 * Defines all Source Attributes. Returns an array of arrays, each of which represents
	 * an attribute that can receive a weight in the engine configuration and algorithm.
	 * Each Attribute has the following keys:
	 *     `name`    (string)  The name of the Attribute.
	 *     `label`   (string)  The label of the Attribute.
	 *     `default` (int)     The default weight of the attribute (set to zero to omit as default)
	 *     `options` (array)   Optional. Defines instances of this Attribute, each considered separately.
	 *     `data`    (mixed)   Defines the data for an Entry of this Source with this Attribute.
	 *                             - 1st parameter is the Entry ID as per db_id_column.
	 *                             - 2nd parameter (when applicable) is the chosen option from 'options'
	 *
	 * Example Attributes for WP_Post: Title, Slug, Excerpt, Custom Fields, Taxonomies
	 *
	 * @since 4.0
	 * @var   Attribute[]
	 */
	protected $attributes = [];

	/**
	 * Whether this Source has been initialized.
	 *
	 * @since 4.0
	 * @var boolean
	 */
	private $initialized = false;

	/**
	 * Initializes a Source. Required once a Source has been instantiated.
	 *
	 * @since 4.0
	 */
	public function init() {
		if ( strlen( $this->name ) > 80 ) {
			add_action( 'searchwp\debug\log', 'Name too long (max 80 chars): ' . $this->name, 'source' );
			return;
		}

		// Assume valid Sources (to save db calls) but allow for proactive checks.
		if ( $this->initialized || ( apply_filters( 'searchwp\source\check_db', false ) && ! $this->is_valid() ) ) {
			return;
		}

		$this->set_attributes();
		$this->set_options();
		$this->set_rules();
		$this->set_db_where();
		$this->set_notices();
		$this->initialized = true;
	}

	/**
	 * Clone.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function __clone() {
		$this->attributes = array_map( function ( $attribute ) {
			return clone $attribute;
		}, $this->attributes );

		$this->rules = array_map( function ( $rule ) {
			return clone $rule;
		}, $this->rules );
	}

	/**
	 * Getter for validity of this Source.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public function is_valid() {
		return $this->validate_db_table();
	}

	/**
	 * Maps an Entry for this Source to its native model.
	 *
	 * @since  4.0
	 * @return mixed
	 */
	public function entry( Entry $entry, $doing_query = false ) {
		return new \stdClass();
	}

	/**
	 * Defines database column/value pairs to restrict available records.
	 *
	 * Returns array of arrays that each define a column/value specification.
	 * Set array key 'relation' with a value AND or OR to handle combination logic. Default is AND.
	 *
	 * Each array within the array is essentially a WHERE clause on the database table with the following keys:
	 *     `column`  (string)       The table column name.
	 *     `value`   (string|array) The column value.
	 *                                  Can be array only when `compare` is 'IN', 'NOT IN'
	 *     `compare` (string)       The operator used to test the comparison.
	 *                                  Possible values include ‘=’, ‘!=’, ‘>’, ‘>=’, ‘<‘, ‘<=’, ‘IN’, ‘NOT IN’
	 *                                  Default value is ‘=’.
	 *                              Not suported at this time: ‘LIKE’, ‘NOT LIKE’, ‘BETWEEN’, ‘NOT BETWEEN’,
	 *                                  ‘EXISTS’ and ‘NOT EXISTS’. Considering for future addition.
	 *     `type`    (string)       The column data type, used for escaping.
	 *                                  Possible values include ‘NUMERIC’, ‘CHAR’. Default value is ‘CHAR’.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function db_where() {
		return [];
	}

	/**
	 * Allows for additional WHERE definition(s) which can include additional
	 * database table considerations. NOTE: Queries returned here should be
	 * properly prepared prior to being returned.
	 *
	 * @return array Array of prepared SQL query strings to be executed.
	 */
	protected function db_id_in() {
		return [];
	}

	/**
	 * Add class hooks once. This implementation is handled internally and
	 * should be fired only once else Source hooks will be duplicated.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function add_hooks() {
		return;
	}

	/**
	 * Setter for Source Notices.
	 *
	 * @since 4.0
	 * @return array
	 */
	private function set_notices() {
		if ( ! method_exists( $this, 'notices' ) ) {
			return [];
		}

		$this->notices = array_filter(
			(array) $this->notices( $this->notices ),
			function( $notice ) {
				return $notice instanceof Notice;
			}
		);
	}

	/**
	 * Getter for Notices.
	 *
	 * @since 4.0
	 * @return Notice[]
	 */
	public function get_notices() {
		return (array) $this->notices;
	}

	/**
	 * Set validated Attributes for this Source. An Attribute is something that receives
	 * relevance when performing a search. Separate attributes should be created for
	 * anything that could potentially benefit from a unique weight.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_attributes() {
		$attributes = $this->attributes;
		$this->attributes = [];

		if ( empty( $attributes ) || ! is_array( $attributes ) ) {
			return;
		}

		// Attributes are initially definied as arrays, so we're going to ensure proper Attributes.
		foreach ( $attributes as $attribute ) {
			if ( ! $attribute instanceof Attribute ) {
				$attribute = new Attribute( $attribute );
			}

			$this->attributes[ $attribute->get_name() ] = $attribute;

			// If this Attribute has Options, we need to set up an AJAX callback to retrieve Option Values.
			if ( ! $attribute->options_static() ) {
				$attribute->options_ajax_tag = str_replace( '-', '_',
					sanitize_title_with_dashes( SEARCHWP_PREFIX . "{$this->name}_attribute_{$attribute->get_name()}_options" )
				);

				// This callback may already exist, and we only need one.
				if ( ! has_action( 'wp_ajax_' . $attribute->options_ajax_tag, [ $this, 'get_attribute_options_via_ajax' ] ) ) {
					add_action( 'wp_ajax_' . $attribute->options_ajax_tag, [ $this, 'get_attribute_options_via_ajax' ] );
				}
			}
		}
	}

	/**
	 * Returns the Options available for Weight Transfer for this Source.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function weight_transfer_options() {
		return [
			[ 'option' => new Option( 'id', __( 'To Entry ID', 'searchwp' ) ), ],
		];
	}

	/**
	 * Establish Source options based on imposed restrictions.
	 *
	 * @since 4.0
	 */
	private function set_options() {
		$weight_transfer_options = array_filter(
			(array) $this->weight_transfer_options(),
			function( $option ) {
				// Must be Options and can have only a limited set of Option values because we need to
				// know what each setting does, and therefore we define the logic of each setting.
				return $option['option'] instanceof Option
					&& in_array( $option['option']->get_value(), [ 'id', 'col' ], true );
			}
		);

		if ( empty( $weight_transfer_options ) ) {
			return;
		}

		$this->options = [
			'weight_transfer' => [
				'label'   => __( 'Transfer Weight', 'searchwp' ),
				'tooltip' => __( 'Transfer the weight of the search result to the parent entry (if applicable)', 'searchwp' ),
				'options' => $weight_transfer_options,
			],
		];
	}

	/**
	 * Setter for Option config.
	 *
	 * @since 4.0
	 * @param string $option Option name.
	 * @param array $config Option config.
	 * @return void
	 */
	public function set_option_config( $option, $config ) {
		if ( ! array_key_exists( $option, $this->options ) ) {
			return;
		}

		$this->options[ $option ]['enabled'] = ! empty( $config['enabled'] );
		$this->options[ $option ]['value']   = isset( $config['value'] ) ? $config['value'] : '';

		if ( is_string( $this->options[ $option ]['value'] ) ) {
			$this->options[ $option ]['value'] = trim( $this->options[ $option ]['value'] );
		}

		foreach( $this->options[ $option ]['options'] as $optionObj ) {
			if ( $config['option'] !== $optionObj['option']->get_value() ) {
				continue;
			}

			$this->options[ $option ]['option'] = $config['option'];
		}
	}

	/**
	 * Getter for options.
	 *
	 * @since 4.0
	 * @return array[]
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Set validated Rules for this Source. Rules control Source Entry availability
	 * per engine and can faciliate limiting results to taxonomy terms or date range(s).
	 * Separate Rules should be made available for individual criteria.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_rules() {
		$rules = $this->rules;
		$this->rules = [];

		if ( empty( $rules ) || ! is_array( $rules ) ) {
			$this->rules = [];
			return;
		}

		// Rules are initially definied as arrays, we need to instantiate proper Rules.
		foreach ( $rules as $rule ) {
			if ( ! $rule instanceof Rule ) {
				$rule = new Rule( $rule );
			}

			$this->rules[ $rule->get_name() ] = $rule;

			// If this Rule has Options, we need to set up an AJAX callback to retrieve Option Values.
			if ( is_array( $rule->get_options() ) ) {
				$rule->option_values_ajax_tag = str_replace( '-', '_',
					sanitize_title_with_dashes( SEARCHWP_PREFIX . "{$this->name}_rule_{$rule->get_name()}_option_values" )
				);

				// This callback may already exist, and we only need one.
				if ( ! has_action( 'wp_ajax_' . $rule->option_values_ajax_tag, [ $this, 'get_rule_option_values_via_ajax' ] ) ) {
					add_action( 'wp_ajax_' . $rule->option_values_ajax_tag, [ $this, 'get_rule_option_values_via_ajax' ] );
				}
			}
		}
	}

	/**
	 * AJAX callback to retrieve Rule option values for a single Rule option.
	 *
	 * @since 4.0
	 * @return string JSON representation of results.
	 */
	public function get_rule_option_values_via_ajax() {

		Utils::check_ajax_permissions();

		$rule    = isset( $_REQUEST['rule'] )    ? Utils::decode_string( $_REQUEST['rule'] )   : false;
		$option  = isset( $_REQUEST['option'] )  ? Utils::decode_string( $_REQUEST['option'] ) : false;
		$search  = isset( $_REQUEST['search'] )  ? Utils::decode_string( $_REQUEST['search'] ) : false;
		$include = isset( $_REQUEST['include'] ) ? $_REQUEST['include'] : [];

		if ( ! $rule || ! $option || ! array_key_exists( $rule, $this->rules ) ) {
			wp_send_json_error();
		}

		$rule_options = $this->rules[ $rule ]->get_options( 'value' );
		if ( ! in_array( $option, $rule_options ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $this->rules[ $rule ]->get_values( $option, $search, $include ) );
	}

	/**
	 * AJAX callback to retrieve Attribute Options.
	 *
	 * @since 4.0
	 * @return string JSON representation of results.
	 */
	public function get_attribute_options_via_ajax() {

		Utils::check_ajax_permissions();

		$source    = isset( $_REQUEST['source'] )    ? Utils::decode_string( $_REQUEST['source'] ) : false;
		$attribute = isset( $_REQUEST['attribute'] ) ? Utils::decode_string( $_REQUEST['attribute'] ) : false;
		$search    = isset( $_REQUEST['search'] )    ? Utils::decode_string( $_REQUEST['search'] )    : false;
		$include   = isset( $_REQUEST['include'] )   ? $_REQUEST['include']                    : [];

		if ( ! $attribute || ! isset( $this->attributes[ $attribute ] ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $this->get_attribute_options( $this->attributes[ $attribute ], $search, $include ) );
	}

	/**
	 * Retrieve Options for an Attribute of this Source.
	 *
	 * @since 4.0
	 * @param Attribute   $attribute Attribute to consider.
	 * @param bool|String $search    Finds Options LIKE $search.
	 * @param array       $include   Option values to include.
	 * @return Options[]
	 */
	public function get_attribute_options( Attribute $attribute, $search = false, array $include = [] ) {
		$options = $attribute->get_options( $search, $include );

		if ( ! is_array( $options ) ) {
			return $options;
		}

		$options = array_values( (array) apply_filters(
			'searchwp\source\attribute\options',
			$options,
			[
				'source'    => $this->name,
				'attribute' => $attribute->get_name(),
				'search'    => $search,
				'include'   => $include,
			]
		) );

		return array_filter( $options, function( $option ) {
			return $option instanceof Option;
		} );
	}

	/**
	 * Getter for the Attributes of this Source.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Getter for the Rules of this Source.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_rules() {
		return $this->rules;
	}

	/**
	 * Getter for single Attribute.
	 *
	 * @since 4.0
	 * @param string $attribute Attribute name.
	 * @return mixed|false The Attribute.
	 */
	public function get_attribute( string $attribute ) {
		return array_key_exists( $attribute, $this->attributes ) ? $this->attributes[ $attribute ] : false;
	}

	/**
	 * Getter for single Rule.
	 *
	 * @since 4.0
	 * @param string $rule Rule name.
	 * @return mixed|false The Rule.
	 */
	public function get_rule( string $rule ) {
		return array_key_exists( $rule, $this->rules ) ? $this->rules[ $rule ] : false;
	}

	/**
	 * Validate the existing db_where method as defined by the Source. Ensure that the referenced
	 * database columns exist and that all clauses are in a valid format.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_db_where() {
		$clauses = $this->db_where();

		if ( ! is_array( $clauses ) ) {
			$clauses = [];
		}

		if ( empty( $clauses ) ) {
			$this->db_where = false;
			return;
		}

		// Establish our relation, and then clear it out of the clauses.
		$relation = ! empty( $clauses['relation'] ) && 'OR' === $clauses['relation'] ? 'OR' : 'AND';
		if ( isset( $clauses['relation'] ) ) {
			unset( $clauses['relation'] );
		}

		$this->db_where = array_filter( array_map( function( $clause ) {
			if ( apply_filters( 'searchwp\source\check_db', false ) ) {
				if ( ! Utils::valid_db_column( $this->db_table, $this->db_id_column ) ) {
					return false;
				}
			}

			return Utils::validate_clause_args( $clause );
		}, $clauses ) );

		$this->db_where['relation'] = $relation;
	}

	/**
	 * Convert the db_where clauses into something usable when preparing SQL queries. Generates
	 * separate arrays of values and placeholders for use in SQL queries sent to $wpdb->prepare.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function db_where_as_values_placeholders( $alias = false, $clauses = false ) {
		$db_table = $alias ? $alias : $this->db_table;
		$clauses  = ! is_array( $clauses ) ? $this->db_where : $clauses;

		return Utils::parse_where( $db_table, $clauses );
	}

	/**
	 * Builds SQL clause to assist in finding Source Entry IDs in the index.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function apply_db_id_in( &$sql, &$query_values ) {
		$source_id_in = $this->db_id_in();

		if ( empty( $source_id_in ) ) {
			return;
		}

		// Validate the relation.
		$source_id_in_relation = array_key_exists( 'relation', $source_id_in ) ? $source_id_in['relation'] : 'AND';

		if ( 'AND' !== $source_id_in_relation || 'OR' !== $source_id_in_relation ) {
			$source_id_in_relation = 'AND';
		}

		$source_id_in = array_map( function( $sql_id_in ) {
			return ' EXISTS (' . $sql_id_in . ')';
		}, $source_id_in );

		$sql['where'][] = implode( $source_id_in_relation, $source_id_in );
	}

	/**
	 * Queries the index to determine which IDs are not indexed for the submitted Source.
	 *
	 * @since 4.0
	 * @param int    $limit The maximum number of IDs to find.
	 * @return array
	 */
	public function get_unhandled_ids( $limit = 1000 ) {
		global $wpdb;

		$pre_unhandled_ids = apply_filters( 'searchwp\source\pre_get_unhandled_ids', null, $this, $limit );
		if ( is_array( $pre_unhandled_ids ) ) {
			return $pre_unhandled_ids;
		}

		$index = \SearchWP::$index;

		// Doing this in a single query doesn't scale very far. As a result we are going to make two separate
		// queries and compute our next set of unindexed IDs based on different IDs that are in the source table
		// but not in the index table. Considered using the ID column of the source table as a sorted column
		// and comparing that to a sorted ID column in the status table but ran into an issue of that not working
		// unless there was already at least one entry in the status table to compare by. Another consideration
		// is that we don't know the format of the source IDs. Likely numeric but not a certainty. This should work
		// well enough despite it being two queries.
		$ids_in_index = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT id
				FROM {$index->get_tables()['status']->table_name}
				WHERE source = %s AND site = %d",
				$this->get_name(),
				get_current_blog_id()
			)
		);

		// Determine which IDs are not indexed.
		$all_unindexed_ids = array_values(
			array_diff( $this->get_entry_db_records(), $ids_in_index )
		);

		if ( -1 !== $limit ) {
			$ids = array_slice( $all_unindexed_ids, 0, absint( $limit ) );
		} else {
			$ids = $all_unindexed_ids;
		}

		return $ids;
	}

	/**
	 * Retrieves Entry records from this database table.
	 *
	 * @since 4.0
	 * @param boolean $count_only Whether to return only the Entry count instead of the ID column values.
	 * @return int|array
	 */
	public function get_entry_db_records( $count_only = false ) {
		global $wpdb;

		$source_table  = $this->get_db_table();
		$source_column = $this->get_db_id_column();

		$select = "{$source_table}.{$source_column}";
		if ( $count_only ) {
			$select = 'SQL_CALC_FOUND_ROWS ' . $select;
		}

		// Structure the query.
		$query_values = [];
		$sql = [
			'select' => [ $select, ],
			'from'   => [ "{$source_table}", ],
			'where'  => [ '1=1', ],
		];

		$this->apply_where( $sql, $query_values );
		$this->apply_db_id_in( $sql, $query_values );
		$this->apply_rules( $sql, $query_values );

		// Build and execute the query.
		$sql['select'] = implode( ', ', $sql['select'] );
		$sql['from']   = implode( ', ', $sql['from'] );
		$sql['where']  = implode( ' AND ', $sql['where'] );

		$sql = "SELECT {$sql['select']} FROM {$sql['from']} WHERE {$sql['where']}";

		if ( ! empty( $query_values ) ) {
			$sql = $wpdb->prepare( $sql, $query_values );
		}

		if ( $count_only ) {
			$sql .= " LIMIT 1 ";
		}

		$results = $wpdb->get_col( $sql );

		if ( $count_only ) {
			return (int) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
		} else {
			return $results;
		}
	}

	/**
	 * Retrieves, formats, and applies db_where clauses.
	 *
	 * @since 4.0
	 * @param array $sql SQL query structured as associative array using placeholders.
	 * @param array $values Placeholder values.
	 * @return void
	 */
	private function apply_where( &$sql, &$query_values ) {
		$where_clauses = $this->db_where_as_values_placeholders();

		if ( ! empty( $where_clauses['values'] ) && ! empty( $where_clauses['placeholders'] ) ) {
			$query_values   = array_merge( $query_values, $where_clauses['values'] );
			$sql['where'][] = '( ' . implode( ' AND ', $where_clauses['placeholders'] ) . ' ) ';
		}
	}

	/**
	 * Applies Rules SQL when finding unindexed Entry IDs.
	 *
	 * @since 4.0
	 * @param array $sql SQL query structured as associative array using placeholders.
	 * @param array $values Placeholder values.
	 * @return void
	 */
	private function apply_rules( &$sql, &$query_values ) {
		$sql['where'] = array_merge( $sql['where'], $this->get_rules_as_sql_clauses() );
	}

	/**
	 * Retrieves Rules that have been reassembed into Rule groups.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_rules_as_groups( $apply_rules = true ) {
		// Rules are stored according to their group index. Rules of the same group MUST remain
		// together as that implements the OR logic we're looking for. Multiple groups of Rules
		// apply AND logic among one another. We need to build this relationship.
		$rules = array_filter( $this->rules, function( $rule ) {
			// If there are no settings, the Rule has not been used and therefore does not apply.
			return ! empty( $rule->get_settings() );
		} );

		// Iterate over our Rules and reconstruct our groups based on the settings (grouped by index.)
		$rule_groups = [];
		foreach ( $rules as $rule ) {
			foreach( $rule->get_settings() as $rule_group_index => $rule_group_settings ) {
				foreach( $rule_group_settings as $rule_group_logic => $rules_settings ) {
					if ( ! isset( $rule_groups[ $rule_group_index ] ) ) {
						$rule_groups[ $rule_group_index ] = [
							'type'  => $rule_group_logic,
							'rules' => [],
						];
					}

					$rule_groups[ $rule_group_index ]['rules'] = array_merge(
						$rule_groups[ $rule_group_index ]['rules'],
						array_map( function( $settings ) use ( $rule, $apply_rules ) {
							$settings['condition'] = isset( $settings['condition'] )
								? Utils::validate_compare_arg( $settings['condition'] ) : '=';

							return $apply_rules ? $rule->get_application( $settings ) : array_merge(
								$settings,
								[ 'rule' => $rule->get_name() ]
							);
						}, $rules_settings )
					);
				}
			}
		}

		ksort( $rule_groups );

		return $rule_groups;
	}

	/**
	 * Retrieve rule applications for this Source.
	 *
	 * @since 4.0
	 * @param Engine $engine Engine to consider.
	 * @param string $alias Table alias to use. Defaults to Source table.column.
	 * @return array Rule application SQL clauses.
	 */
	public function get_rules_as_sql_clauses( $alias = '' ) {
		global $wpdb;

		$sql   = [];
		$source_table_col = "{$this->get_db_table()}.{$this->get_db_id_column()}";
		$alias = ! empty( $alias ) ? $alias : $source_table_col;

		// Generate WHERE clauses that implement each Rule group.
		foreach( $this->get_rules_as_groups() as $rule_group ) {
			$type  = $rule_group['type'];
			$rules = $rule_group['rules'];

			// If any Rule application returns an array of IDs, we need to convert that into a subquery.
			$rules = array_map( function( $rule ) use ( $source_table_col, $wpdb ) {
				if ( ! is_array( $rule ) ) {
					return $rule;
				}

				// If it's an empty array, we need to force zero results.
				if ( empty( $rule ) ) {
					$rule = [ '' ];
				}

				return $wpdb->prepare( "
					SELECT {$source_table_col}
					FROM {$this->get_db_table()}
					WHERE {$source_table_col} IN ("
						. implode( ',', array_fill( 0, count( $rule ), '%s' ) ) .
					')',
					$rule );
			}, $rules );

			// Logic inside Rule group is OR.
			$imploded_clauses = '(' . implode( ") OR {$source_table_col} IN (", $rules ) . ')';

			// Logic between Rule groups is AND (to match the Query implementation).
			$sql[] = "{$alias} {$type} (
				SELECT {$source_table_col} FROM {$this->get_db_table()}
				WHERE {$source_table_col} IN " . $imploded_clauses . ' )';
		}

		return $sql;
	}

	/**
	 * Retrieves unindexed Entries for the submitted Source.
	 *
	 * @since 4.0
	 * @param int $limit The maximum number of IDs to find.
	 * @return Entries
	 */
	public function get_unindexed_entries( $limit = 50 ) {
		return new Entries( $this, $this->get_unhandled_ids( $limit ) );
	}

	/**
	 * Validates the tracking table to ensure both the table and column exist.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function validate_db_table() {
		return Utils::valid_db_table( $this->db_table )
				&& Utils::valid_db_column( $this->db_table, $this->db_id_column );
	}

	/**
	 * Getter for validated tracking table name.
	 *
	 * @since 4.0
	 * @return string The tracking table name.
	 */
	public function get_db_table() {
		return $this->db_table;
	}

	/**
	 * Getter for validated tracking ID column name.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_db_id_column() {
		return $this->db_id_column;
	}

	/**
	 * Getter for source name.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Getter for labels.
	 *
	 * @since 4.0
	 */
	public function get_labels() {
		return $this->labels;
	}

	/**
	 * Getter for label.
	 *
	 * @since 4.0
	 * @param string $type The type of label to return.
	 * @return string
	 */
	public function get_label( $type = 'plural' ) {
		return array_key_exists( (string) $type, $this->labels ) ? $this->labels[ $type ] : $this->labels;
	}

	/**
	 * Provides the model to use when representing this Source as JSON.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function jsonSerialize(): array {

		$attributes = $this->get_attributes();
		if ( ! empty( $attributes ) ) {
			$attributes = call_user_func_array( 'array_merge',
				array_map( function( $attribute ) {
					$settings = $attribute->get_settings();

					// If the Options are not static, we're going to set up our AJAX tag.
					if ( ! empty( $attribute->options_ajax_tag ) ) {
						$options_ajax_tag = $attribute->options_ajax_tag;
						$options = [];
					} else {
						$options_ajax_tag = false;
						$options = Utils::normalize_options( $this->get_attribute_options( $attribute ) );
					}

					// But if there are existing settings, we need the Options for those.
					if ( is_array( $settings ) && ! empty( $settings ) ) {
						$options = Utils::normalize_options(
							$this->get_attribute_options( $attribute, false, array_keys( $settings ) )
						);
					}

					// Allow for 'special' Options that deserve an extra call-out.
					$special_options = Utils::normalize_options(
						array_filter( (array)
							apply_filters(
								'searchwp\source\attribute\options\special',
								[], [
									'source'    => $this->name,
									'attribute' => $attribute->get_name(),
								]
							), function( $option ) {
								return $option instanceof Option;
							}
						) );

					return [
						$attribute->get_name() => [
							'name'         => $attribute->get_name(),
							'label'        => $attribute->get_label( $this ),
							'notes'        => $attribute->get_notes(),
							'tooltip'      => $attribute->get_tooltip(),
							'settings'     => $settings,
							'default'      => $attribute->get_default(),
							'options'      => $options,
							'allow_custom' => $attribute->allow_custom,
							'special'      => $special_options,
							'get_options'  => $options_ajax_tag,
						]
					];
				}, array_values( $attributes ) )
			);
		}

		$rules = $this->get_rules();
		if ( ! empty( $rules ) ) {
			$rules = call_user_func_array( 'array_merge',
				array_filter( array_map( function( $rule ) {
					$options = Utils::normalize_options( $rule->get_options() );
					$values  = [];

					// If this Rule has Options defined, but they're empty, it's useless.
					if ( is_array( $options ) && empty( $options ) ) {
						return false;
					}

					if ( is_array( $options ) ) {
						// We aren't going to provide all possible values here, so we're setting up an AJAX callback.
						$rule_values_ajax_tag = $rule->option_values_ajax_tag;
					} else {
						$rule_values_ajax_tag = false;
						$values = $rule->get_values();
					}

					return [
						$rule->get_name() => [
							'name'       => $rule->get_name(),
							'label'      => $rule->get_label(),
							'notes'      => $rule->get_notes(),
							'tooltip'    => $rule->get_tooltip(),
							'options'    => $options,
							'conditions' => $rule->get_conditions(),
							'values'     => $values,
							'get_values' => $rule_values_ajax_tag,
							'settings'   => [], // This is just for the model.
						]
					];
				}, array_values( (array) $this->get_rules() ) ) )
			);
		}

		$rule_groups = $this->get_rules_as_groups( false );
		if ( ! empty( $rule_groups ) ) {
			// We're going to replace chosen Values with Option objects for a better UX. The save routine converts back.
			$rule_groups = array_map( function( $rule_group ) use ( $rules ) {
				return [
					'type'  => $rule_group['type'],
					'rules' => array_map( function( $rule ) use ( $rules ) {
						// We only need to proceed if there are Rule Options or Values to process.
						if (
							! is_array( $rules[ $rule['rule'] ]['options'] )
							&& ! is_array( $rules[ $rule['rule'] ]['values'] )
						 ) {
							return $rule;
						}

						// Convert all Value values to Option objects.
						return [
							'option'    => $rule['option'],
							'condition' => $rule['condition'],
							'rule'      => $rule['rule'],
							'value'     => ! is_array( $rule['value'] ) ? $rule['value']
								: array_values( array_map(
									function( $value ) {
										// We want to trigger jsonSerialize().
										return json_decode( json_encode( $value ), true );
									},
									$this->get_rules()[ $rule['rule'] ]->get_values(
										$rule['option'],
										false,
										$rule['value']
									)
								) ),
						];
					}, $rule_group['rules'] ),
				];
			}, $rule_groups );
		}

		$options = $this->get_options();

		if ( ! empty( $options ) ) {
			$options = array_map( function( $option, $config ) {
				// TODO: Refactor this conditional. It controls whether a dropdown of options are available
				// or a text field for an ID. It was built for weight transfer, but it's too locked in to that
				// and it doesn't even work properly because there's no proper setup/check in place.
				if ( isset( $config['option'] ) ) {
					$option_value = $config['option'];
				} elseif ( isset( $config['options'] ) && is_array( $config['options'] ) && isset( $config['options'][0] ) ) {
					// Retrieve the value from the first Option.
					$option_value = $config['options'][0]['option']->get_value();
				} else {
					$option_value = 'id';
				}

				return [
					'name'    => $option,
					'label'   => $config['label'],
					'tooltip' => $config['tooltip'],
					'options' => array_map( function( $option ) {
						// Trigger jsonSerialize for these Options.
						return json_decode( json_encode( $option['option'] ), true );
					}, $config['options'] ),
					'option'  => $option_value,
					'value'   => isset( $config['value'] ) ? $config['value'] : '',
					'enabled' => isset( $config['enabled'] ) ? (bool) $config['enabled'] : false,
				];
			}, array_keys( $options ), array_values( $options ) );
		}

		return [
			'name'       => $this->name,
			'labels'     => $this->labels,
			'attributes' => $attributes,
			'rules'      => $rules,
			'ruleGroups' => $rule_groups,
			'options'    => $options,
			'notices'    => array_map( function( $notice ) {
				return json_decode( json_encode( $notice ), true );
			}, $this->get_notices() ),
		];
	}

	/**
	 * Gets permalink for Source Entry ID.
	 *
	 * @since 4.1.14
	 * @param int $id ID of the Entry
	 * @return null|string
	 */
	public static function get_permalink( int $id ) {
		return null;
	}

	/**
	 * Gets edit link for Source Entry ID.
	 *
	 * @since 4.1.14
	 * @param int $id ID of the Entry
	 * @return null|string
	 */
	public static function get_edit_link( int $id ) {
		return null;
	}
}
