<?php
/**
 * Class YITH_WCBK_Global_Availability_Rule_Data_Store
 * Data store for global availability rule.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Global_Availability_Rule_Data_Store
 *
 * @since 5.0.0
 */
class YITH_WCBK_Global_Availability_Rule_Data_Store extends YITH_WCBK_Custom_Table_Data_Store {

	/**
	 * Keys to prop.
	 *
	 * @var string[]
	 */
	protected $keys_to_props = array(
		'name'             => 'name',
		'type'             => 'type',
		'enabled'          => 'enabled',
		'date_ranges'      => 'date_ranges',
		'availabilities'   => 'availabilities',
		'priority'         => 'priority',
		'exclude_products' => 'exclude_products',
	);

	/**
	 * Create
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 */
	public function create( &$rule ) {
		global $wpdb;
		$data = $this->get_data_to_update( $rule, true );

		$result = $wpdb->insert( $wpdb->yith_wcbk_global_availability_rules, $data );
		$id     = $result ? (int) $wpdb->insert_id : 0;

		if ( $id ) {
			$rule->set_id( $id );
			$this->update_associations( $rule );
			$rule->apply_changes();

			do_action( 'yith_wcbk_global_availability_rule_created', $rule );

			self::clear_caches();
		}
	}

	/**
	 * Read
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 *
	 * @throws Exception If invalid object.
	 */
	public function read( &$rule ) {
		global $wpdb;
		$rule->set_defaults();

		if ( ! $rule->get_id() ) {
			throw new Exception( __( 'Invalid rule.', 'yith-booking-for-woocommerce' ) );
		}

		$props = $wpdb->get_row(
			$wpdb->prepare( "SELECT * from {$wpdb->yith_wcbk_global_availability_rules} where id=%d", $rule->get_id() ),
			ARRAY_A
		);

		if ( ! $props ) {
			throw new Exception( __( 'Invalid rule.', 'yith-booking-for-woocommerce' ) );
		}

		$props = array_map( 'maybe_unserialize', $props );

		$rule->set_props( $props );

		$this->read_associations( $rule );

		$rule->set_object_read( true );

		do_action( 'yith_wcbk_global_availability_rule_read', $rule );
	}

	/**
	 * Update
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 */
	public function update( &$rule ) {
		global $wpdb;
		$data    = $this->get_data_to_update( $rule, true );
		$updated = false;

		if ( $data ) {
			$result = $wpdb->update( $wpdb->yith_wcbk_global_availability_rules, $data, array( 'id' => $rule->get_id() ) );

			if ( $result ) {
				$updated = true;
			}
		}

		if ( $this->update_associations( $rule ) ) {
			$updated = true;
		}

		if ( $updated ) {
			$rule->apply_changes();
			do_action( 'yith_wcbk_global_availability_rule_updated', $rule );

			self::clear_caches();
		}
	}

	/**
	 * Delete
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 * @param array                              $args Arguments.
	 *
	 * @return bool True if deleted correctly, false otherwise.
	 */
	public function delete( &$rule, $args = array() ) {
		global $wpdb;

		$result = false;
		$id     = $rule->get_id();

		if ( $id ) {
			$object = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->yith_wcbk_global_availability_rules} WHERE id = %d", $id ) );

			if ( $object ) {
				do_action( 'yith_wcbk_global_availability_rule_before_delete', $rule );

				$result = $wpdb->delete( $wpdb->yith_wcbk_global_availability_rules, array( 'id' => $id ) );

				$this->delete_associations( $rule );

				$rule->set_id( 0 );
				do_action( 'yith_wcbk_global_availability_rule_deleted', $id );

				self::clear_caches();
			}
		}

		return $result;
	}

	/**
	 * Get data to update.
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule  The rule.
	 * @param bool                               $force Force flag: set true to retrieve all data.
	 *
	 * @return array
	 */
	protected function get_data_to_update( $rule, $force = false ) {
		$props_to_update    = ! $force ? $this->get_props_to_update( $rule, $this->keys_to_props ) : $this->keys_to_props;
		$serializable_props = array( 'date_ranges', 'availabilities' );

		$data = array();
		foreach ( $props_to_update as $key => $prop ) {
			$value = $rule->{"get_$prop"}( 'edit' );

			if ( in_array( $prop, array( 'enabled', 'exclude_products' ), true ) ) {
				$value = wc_string_to_bool( $value );
			}

			if ( in_array( $prop, $serializable_props, true ) ) {
				$value = maybe_serialize( $value );
			}

			$data[ $key ] = $value;
		}

		return $data;
	}

	/**
	 * Read excluded product ids.
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 *
	 * @return array
	 */
	protected function read_excluded_product_ids( $rule ) {
		global $wpdb;

		return array_map(
			'absint',
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT object_id FROM $wpdb->yith_wcbk_global_availability_rules_associations WHERE rule_id = %d",
					$rule->get_id()
				)
			)
		);
	}

	/**
	 * Read associations.
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 */
	protected function read_associations( &$rule ) {
		$rule->set_excluded_product_ids( $this->read_excluded_product_ids( $rule ) );
	}

	/**
	 * Update associations.
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 *
	 * @return bool
	 */
	protected function update_associations( $rule ) {
		global $wpdb;
		$props_to_update = $this->get_props_to_update( $rule, array( 'excluded_product_ids' => 'excluded_product_ids' ) );
		$updated         = false;

		foreach ( $props_to_update as $prop ) {
			switch ( $prop ) {
				case 'excluded_product_ids':
					$old_ids   = $this->read_excluded_product_ids( $rule );
					$new_ids   = $rule->get_excluded_product_ids( 'edit' );
					$to_add    = array_diff( $new_ids, $old_ids );
					$to_remove = array_filter( array_map( 'absint', array_diff( $old_ids, $new_ids ) ) );

					foreach ( $to_add as $object_id ) {
						$updated = true;
						$wpdb->insert(
							$wpdb->yith_wcbk_global_availability_rules_associations,
							array(
								'rule_id'   => $rule->get_id(),
								'object_id' => $object_id,
							)
						);
					}

					if ( $to_remove ) {
						$updated       = true;
						$to_remove_ids = "'" . implode( "', '", $to_remove ) . "'";
						$wpdb->query(
							$wpdb->prepare(
								"DELETE FROM $wpdb->yith_wcbk_global_availability_rules_associations WHERE rule_id = %d AND object_id IN ($to_remove_ids)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$rule->get_id()
							)
						);
					}

					break;
			}
		}

		return $updated;
	}

	/**
	 * Delete associations.
	 *
	 * @param YITH_WCBK_Global_Availability_Rule $rule The rule.
	 */
	protected function delete_associations( $rule ) {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->yith_wcbk_global_availability_rules_associations WHERE rule_id = %d",
				$rule->get_id()
			)
		);
	}

	/**
	 * Query items
	 *
	 * @param array $args Arguments.
	 *
	 * @return YITH_WCBK_Global_Availability_Rule[]|int[]|int
	 */
	public function query( $args = array() ) {
		global $wpdb;
		$defaults           = array(
			'items_per_page' => 10,
			'order'          => 'ASC',
			'order_by'       => 'priority',
			'return'         => 'objects',
			'paginate'       => false,
			'product_id'     => false,
			'enabled'        => null,
		);
		$args               = wp_parse_args( $args, $defaults );
		$table              = $wpdb->yith_wcbk_global_availability_rules;
		$associations_table = $wpdb->yith_wcbk_global_availability_rules_associations;
		$alias              = 'global_availability_rules';

		$select       = "SELECT DISTINCT {$alias}.id FROM {$table} as {$alias} ";
		$select_count = "SELECT COUNT(DISTINCT {$alias}.id) FROM {$table} as {$alias} ";

		$where         = '';
		$join          = '';
		$where_clauses = array();
		$join_clauses  = array();

		if ( null !== $args['enabled'] ) {
			$enabled         = wc_string_to_bool( $args['enabled'] ) ? 1 : 0;
			$where_clauses[] = $wpdb->prepare( "{$alias}.enabled = %d", $enabled ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		if ( null !== $args['product_id'] ) {
			$product_id = absint( $args['product_id'] );
			if ( $product_id ) {
				$where_clauses[] = "{$alias}.id NOT IN (
					SELECT DISTINCT(id) FROM $table as gar1
					JOIN $associations_table as a1 on a1.rule_id = gar1.id
					WHERE exclude_products = 1 AND object_id = '$product_id')";
			}
		}

		if ( $where_clauses ) {
			$where = ' WHERE ' . implode( ' AND ', $where_clauses ) . ' ';
		}
		if ( $join_clauses ) {
			$join = ' ' . implode( ' ', $join_clauses ) . ' ';
		}

		$args['order'] = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		$order_by      = "{$alias}.{$args[ 'order_by' ]}";
		$order         = " ORDER BY $order_by {$args[ 'order' ]} ";

		$limits = '';
		if ( $args['items_per_page'] >= 0 && 'count' !== $args['return'] ) {
			$offset = $args['page'] > 1 ? absint( ( $args['page'] - 1 ) * $args['items_per_page'] ) . ', ' : '';
			$limits = ' LIMIT ' . $offset . $args['items_per_page'];
		}

		$query       = $select . $join . $where . $order . $limits;
		$query_count = $select_count . $join . $where;

		if ( 'ids' === $args['return'] ) {
			$items = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$items = array_map( 'intval', $items );
		} elseif ( 'count' === $args['return'] ) {
			$items = absint( $wpdb->get_var( $query_count ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$items = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$items = array_filter( array_map( 'yith_wcbk_get_global_availability_rule', $items ) );
		}

		if ( 'count' !== $args['return'] ) {
			$total = absint( $wpdb->get_var( $query_count ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( $args['paginate'] ) {
				$items = (object) array(
					'items'         => $items,
					'total'         => $total,
					'max_num_pages' => $args['items_per_page'] > 0 ? ceil( $total / $args['items_per_page'] ) : 1,
				);
			}
		}

		return $items;
	}

	/**
	 * Get the greatest priority.
	 *
	 * @return int
	 */
	public static function get_greatest_priority() {
		global $wpdb;

		$max = $wpdb->get_var( "SELECT MAX(priority) FROM {$wpdb->yith_wcbk_global_availability_rules}" );

		return ! ! $max ? absint( $max ) : 0;
	}

	/**
	 * Get the greatest priority for the specific company.
	 *
	 * @param array $values The values.
	 *
	 * @return array
	 */
	public static function update_priorities( $values ) {
		global $wpdb;
		$results = array();
		foreach ( $values as $value ) {
			$id       = absint( $value['id'] ?? 0 );
			$priority = $value['priority'] ?? false;
			if ( ! ! $id && false !== $priority ) {
				$priority = absint( $priority );
				$wpdb->update( $wpdb->yith_wcbk_global_availability_rules, array( 'priority' => $priority ), array( 'id' => $id ) );
				$results[] = compact( 'id', 'priority' );
			}
		}

		self::clear_caches();

		return $results;
	}

	/**
	 * Clear caches related to availability rules.
	 *
	 * @return void
	 */
	protected static function clear_caches() {
		yith_wcbk_invalidate_product_cache();
	}

}
