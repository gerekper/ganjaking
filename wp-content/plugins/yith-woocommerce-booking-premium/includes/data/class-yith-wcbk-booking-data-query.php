<?php
/**
 * Class YITH_WCBK_Booking_Data_Query
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Booking_Data_Query' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Data_Query
	 *
	 * @since   3.0.0
	 */
	class YITH_WCBK_Booking_Data_Query {

		/**
		 * Table aliases
		 *
		 * @var array
		 */
		private $table_aliases = array();

		/**
		 * Database table that where the primary objects are stored.
		 *
		 * @var string
		 */
		public $primary_table;

		/**
		 * Column in primary_table that represents the ID of the object.
		 *
		 * @var string
		 */
		public $primary_id_column;

		/**
		 * Array of columns and related types
		 *
		 * @var array
		 */
		public $primary_column_types;

		/**
		 * The data query
		 *
		 * @var array
		 */
		public $data_query = array();

		/**
		 * YITH_WCBK_Booking_Data_Query constructor.
		 *
		 * @param array $data_query The Query Args.
		 */
		public function __construct( $data_query = array() ) {
			if ( ! ! $data_query ) {
				$this->data_query = $data_query;
			}
		}

		/**
		 * Get the SQL clauses.
		 *
		 * @param string $primary_table        The primary table.
		 * @param string $primary_id_column    The primary ID column.
		 * @param array  $primary_column_types The primary column types.
		 *
		 * @return string[]
		 */
		public function get_sql( $primary_table, $primary_id_column, $primary_column_types ) {
			$this->primary_table        = $primary_table;
			$this->primary_id_column    = $primary_id_column;
			$this->primary_column_types = $primary_column_types;

			return $this->get_sql_clauses();
		}

		/**
		 * Retrieve the sql clauses.
		 *
		 * @return string[]
		 */
		private function get_sql_clauses() {
			$sql = $this->get_sql_for_data_query( $this->data_query );

			return $sql;
		}

		/**
		 * Get the SQL for a data query.
		 *
		 * @param array $query the Data Query array.
		 * @param int   $depth Depth.
		 *
		 * @return string[]
		 */
		public function get_sql_for_data_query( $query, $depth = 0 ) {
			$relation   = 'AND';
			$sql_chunks = array(
				'join'  => array(),
				'where' => array(),
			);
			$sql        = array(
				'join'  => '',
				'where' => '',
			);
			$indent     = '';
			for ( $i = 0; $i < $depth; $i ++ ) {
				$indent .= '  ';
			}

			foreach ( $query as $key => $clause ) {
				if ( 'relation' === $key ) {
					$relation = in_array( $query['relation'], array( 'AND', 'OR' ), true ) ? $query['relation'] : 'AND';
				} elseif ( is_array( $clause ) ) {
					if ( $this->is_first_order_clause( $clause ) ) {
						$clause_sql = $this->get_sql_for_clause( $clause );

						$where_count = count( $clause_sql['where'] );
						if ( ! $where_count ) {
							$sql_chunks['where'][] = '';
						} elseif ( 1 === $where_count ) {
							$sql_chunks['where'][] = $clause_sql['where'][0];
						} else {
							$sql_chunks['where'][] = '( ' . implode( ' AND ', $clause_sql['where'] ) . ' )';
						}

						$sql_chunks['join'] = array_merge( $sql_chunks['join'], $clause_sql['join'] );
					} else {
						$clause_sql = $this->get_sql_for_data_query( $clause, $depth + 1 );

						$sql_chunks['where'][] = $clause_sql['where'];
						$sql_chunks['join'][]  = $clause_sql['join'];
					}
				}
			}

			// Filter to remove empties.
			$sql_chunks['join']  = array_filter( $sql_chunks['join'] );
			$sql_chunks['where'] = array_filter( $sql_chunks['where'] );

			// Filter duplicate JOIN clauses and combine into a single string.
			if ( ! empty( $sql_chunks['join'] ) ) {
				$sql['join'] = implode( ' ', array_unique( $sql_chunks['join'] ) );
			}

			// Generate a single WHERE clause with proper brackets and indentation.
			if ( ! empty( $sql_chunks['where'] ) ) {
				$sql['where'] = '( ' . "\n  " . $indent . implode( ' ' . "\n  " . $indent . $relation . ' ' . "\n  " . $indent, $sql_chunks['where'] ) . "\n" . $indent . ')';
			}

			// Reset Post Meta Aliases.
			$this->reset_table_alias( 'pm' );

			return $sql;
		}

		/**
		 * True if this is a first-order clause.
		 *
		 * @param array $clause The clause.
		 *
		 * @return bool
		 */
		protected function is_first_order_clause( $clause = array() ) {
			return isset( $clause['key'] ) || isset( $clause['data-type'] );
		}

		/**
		 * Parse a clause.
		 *
		 * @param array $clause The clause.
		 *
		 * @return array
		 */
		protected function parse_clause( $clause = array() ) {
			$clause['data-type'] = $clause['data-type'] ?? 'post-meta';
			$default_compare     = 'term' === $clause['data-type'] ? 'IN' : '=';

			if ( 'post-meta' === $clause['data-type'] && isset( $clause['compare'] ) ) {
				$clause['operator'] = $clause['compare'];
				unset( $clause['compare'] );
			}

			if ( isset( $clause['operator'] ) ) {
				$clause['operator'] = strtoupper( $clause['operator'] );
			} else {
				$clause['operator'] = isset( $clause['value'] ) && is_array( $clause['value'] ) ? 'IN' : $default_compare;
			}

			if ( 'term' === $clause['data-type'] ) {
				$clause['field'] = $clause['field'] ?? 'term_id';
			}

			return $clause;
		}

		/**
		 * Get Clause vars
		 *
		 * @param array $clause The clause.
		 *
		 * @return array
		 */
		protected function get_clause_vars( $clause = array() ) {
			global $wpdb;
			$operator    = $clause['operator'];
			$where_value = false;
			$value       = false;

			if ( 'term' === $clause['data-type'] ) {
				$allowed_operators = array( 'IN', 'NOT IN', 'AND' );
				$operator          = in_array( $operator, $allowed_operators, true ) ? $operator : 'IN';

				if ( array_key_exists( 'terms', $clause ) && array_key_exists( 'taxonomy', $clause ) ) {
					$terms = $clause['terms'];
					$args  = array(
						'get'                    => 'all',
						'number'                 => 0,
						'taxonomy'               => $clause['taxonomy'],
						'update_term_meta_cache' => false,
						'orderby'                => 'none',
					);

					switch ( $clause['field'] ) {
						case 'slug':
							$args['slug'] = $terms;
							break;
						case 'name':
							$args['name'] = $terms;
							break;
						case 'term_taxonomy_id':
							$args['term_taxonomy_id'] = $terms;
							break;
						default:
							$args['include'] = wp_parse_id_list( $terms );
							break;
					}

					$term_query = new WP_Term_Query();
					$term_list  = $term_query->query( $args );

					if ( ! is_wp_error( $term_list ) ) {
						$terms       = wp_list_pluck( $term_list, 'term_taxonomy_id' );
						$value       = $terms;
						$where_value = '(' . implode( ',', $terms ) . ')';
					}
				}
			} else {
				$key               = $clause['key'];
				$non_prefixed_key  = '_' === substr( $key, 0, 1 ) ? substr( $key, 1 ) : $key;
				$type              = array_key_exists( $non_prefixed_key, $this->primary_column_types ) ? $this->primary_column_types[ $non_prefixed_key ] : 'CHAR';
				$allowed_operators = array( '=', '!=', '>', '<', '>=', '<=', 'IN', 'LIKE', 'NOT LIKE', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'NOT EXISTS' );
				$operator          = in_array( $operator, $allowed_operators, true ) ? $operator : '=';

				if ( array_key_exists( 'value', $clause ) ) {
					$value       = $clause['value'];
					$placeholder = '%s';

					if ( in_array( $operator, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) ) {
						if ( ! is_array( $value ) ) {
							$value = preg_split( '/[,\s]+/', $value );
						}
					} else {
						$value = trim( $value );
					}

					switch ( $type ) {
						case 'INT':
							$placeholder = '%d';
							$value       = is_array( $value ) ? array_map( 'absint', $value ) : absint( $value );
							break;
						case 'DATETIME':
							$placeholder = '%s';
							if ( is_array( $value ) ) {
								$value = array_map(
									function ( $date ) {
										return is_numeric( $date ) ? gmdate( 'Y-m-d H:i:s', $date ) : $date;
									},
									$value
								);
							} else {
								if ( is_numeric( $value ) ) {
									$value = gmdate( 'Y-m-d H:i:s', $value );
								}
							}
							break;
					}

					switch ( $operator ) {
						case 'IN':
						case 'NOT IN':
							$operator_string = '(' . substr( str_repeat( ",{$placeholder}", count( $value ) ), 1 ) . ')';
							$where_value     = $wpdb->prepare( $operator_string, $value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
							break;

						case 'BETWEEN':
						case 'NOT BETWEEN':
							$where_value = $wpdb->prepare( "{$placeholder} AND {$placeholder}", $value[0], $value[1] ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
							break;

						case 'LIKE':
						case 'NOT LIKE':
							$value       = '%' . $wpdb->esc_like( $value ) . '%';
							$where_value = $wpdb->prepare( '%s', $value );
							break;

						default:
							$where_value = $wpdb->prepare( $placeholder, $value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
							break;

					}
				}
			}

			return array(
				'operator'    => $operator,
				'where_value' => $where_value,
				'value'       => $value,
			);
		}

		/**
		 * Get the SQL for the clause.
		 *
		 * @param array $clause The clause.
		 *
		 * @return array[]
		 */
		protected function get_sql_for_clause( $clause = array() ) {
			global $wpdb;
			$clause     = $this->parse_clause( $clause );
			$key        = $clause['key'] ?? '';
			$data_type  = $clause['data-type'];
			$sql_chunks = array(
				'where' => array(),
				'join'  => array(),
			);

			$primary_column_types = $this->primary_column_types;
			$non_prefixed_key     = '_' === substr( $key, 0, 1 ) ? substr( $key, 1 ) : $key;

			$clause_vars = $this->get_clause_vars( $clause );
			$operator    = $clause_vars['operator'];
			$where_value = $clause_vars['where_value'];
			$value       = $clause_vars['value'];

			if ( 'post-meta' === $data_type && in_array( $non_prefixed_key, array_keys( $primary_column_types ), true ) ) {
				// Lookup internal data.
				if ( false !== $where_value ) {
					$sql_chunks['where'][] = "{$this->primary_table}.{$non_prefixed_key} {$operator} {$where_value}";
				}
			} else {

				switch ( $data_type ) {
					case 'term':
						$alias                   = $this->unique_table_alias( 'bk_tt' );
						$join_table_column_value = 'term_taxonomy_id';
						if ( false !== $where_value && false !== $value ) {
							$join  = '';
							$where = '';
							if ( 'IN' === $operator ) {
								$join  = "LEFT JOIN {$wpdb->term_relationships} as $alias ON ({$this->primary_table}.{$this->primary_id_column} = $alias.object_id)";
								$where = "{$alias}.{$join_table_column_value} {$operator} {$where_value}";
							} elseif ( 'NOT IN' === $operator ) {
								$where = "$this->primary_table.$this->primary_id_column NOT IN (
											SELECT object_id
											FROM $wpdb->term_relationships
											WHERE term_taxonomy_id IN {$where_value}
										)";
							} elseif ( 'AND' === $operator ) {
								$terms     = $value;
								$num_terms = count( $terms );

								$where = "(
									SELECT COUNT(1)
									FROM $wpdb->term_relationships
									WHERE term_taxonomy_id IN {$where_value}
									AND object_id = $this->primary_table.$this->primary_id_column
								) = $num_terms";
							}

							$sql_chunks['join'][]  = $join;
							$sql_chunks['where'][] = $where;
						}

						break;
					case 'post-meta':
					default:
						$alias                   = $this->unique_table_alias( 'bk_pm' );
						$join_table_column_value = 'meta_value';

						if ( 'NOT EXISTS' === $operator ) {
							$join_on               = $wpdb->prepare( "{$alias}.meta_key = %s", $key ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$join                  = "LEFT JOIN {$wpdb->postmeta} as $alias ON ({$this->primary_table}.{$this->primary_id_column} = $alias.post_id AND $join_on)";
							$sql_chunks['join'][]  = $join;
							$sql_chunks['where'][] = "{$alias}.meta_id IS NULL";
						} else {
							$join = "INNER JOIN {$wpdb->postmeta} as $alias ON ({$this->primary_table}.{$this->primary_id_column} = $alias.post_id)";

							if ( false !== $where_value ) {
								$sql_chunks['join'][]  = $join;
								$sql_chunks['where'][] = $wpdb->prepare( "{$alias}.meta_key = %s", $key ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$sql_chunks['where'][] = "{$alias}.{$join_table_column_value} {$operator} {$where_value}";
							}
						}
				}
			}

			return $sql_chunks;
		}

		/**
		 * Retrieve an unique table alias.
		 *
		 * @param string $key The key.
		 *
		 * @return string
		 */
		private function unique_table_alias( $key ) {
			if ( ! isset( $this->table_aliases[ $key ] ) ) {
				$this->table_aliases[ $key ] = 1;
			} else {
				$this->table_aliases[ $key ] ++;
			}

			return $key . '_' . $this->table_aliases[ $key ];
		}

		/**
		 * Reset table alias
		 *
		 * @param string $key The key.
		 */
		private function reset_table_alias( $key ) {
			if ( isset( $this->table_aliases[ $key ] ) ) {
				unset( $this->table_aliases[ $key ] );
			}
		}
	}
}
