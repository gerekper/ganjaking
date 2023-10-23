<?php
/**
 * YITH WAPO DB Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_DB' ) ) {

	/**
	 * YITH_WAPO Class
	 */
	class YITH_WAPO_DB {

		const YITH_WAPO_BLOCKS              = 'yith_wapo_blocks';
		const YITH_WAPO_ADDONS              = 'yith_wapo_addons';
        const YITH_WAPO_BLOCKS_ASSOCIATIONS = 'yith_wapo_blocks_assoc';

		/**
		 * Single instance of the class
		 *
		 * @since 4.0.0
		 * @var YITH_WAPO_DB
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 4.0.0
		 * @return YITH_WAPO_DB
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

		}

		/**
		 * Get all the blocks.
		 *
		 * @return array
		 */
		public function yith_wapo_get_blocks( $visible = false ) {
			global $wpdb;

			$conditions  = apply_filters( 'yith_wapo_get_blocks_conditions' , array() );
			$query_where = array();
			$blocks      = array();

			// Get only visible blocks.
			if ( $visible ) {
				$conditions['visibility'] = 1;
			}

			// Create the WHERE condition.
			if ( ! empty( $conditions ) ) {

				foreach ( $conditions as $column => $value ) {
					$query_where[] = $column . '=' . $value;
				}

				$query_where = ' WHERE ' . implode( ' AND ', $query_where );
			} else {
				$query_where = '';
			}

            $blocks_table = $wpdb->prefix . self::YITH_WAPO_BLOCKS;

			$query   = "SELECT id FROM {$blocks_table} {$query_where} ORDER BY priority ASC";
            $blocks  = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

			return $blocks;
		}

		/**
		 * Get Addons by Block ID Function
		 *
		 * @param int $block_id Block ID.
		 * @return array
		 */
		public function yith_wapo_get_addons_by_block_id( $block_id, $visible = false ) {
			global $wpdb;

            if ( 'new' === $block_id ) {
                return array();
            }

			$conditions  = array();
			$query_where = array();
			$addons      = array();

			if ( $block_id ) {
				$conditions[ 'block_id' ] = $block_id;
			}
			if ( $visible ) {
				$conditions[ 'visibility' ] = 1;
			}

			// Create the WHERE condition.
			if ( ! empty( $conditions ) ) {

				foreach ( $conditions as $column => $value ) {
					$query_where[] = $column . '=' . $value;
				}

				$query_where = ' WHERE ' . implode( ' AND ', $query_where );
			} else {
				$query_where = '';
			}

			$query   = "SELECT id FROM {$wpdb->prefix}yith_wapo_addons {$query_where} ORDER BY priority ASC";
			$results = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

			foreach ( $results as $key => $addon ) {
				$addons[] = yith_wapo_instance_class( 'YITH_WAPO_Addon',
                    array(
                        'id'   => $addon->id,
                    )
                );
			}

			return apply_filters( 'yith_wapo_addons_by_block_id', $addons, $block_id );
		}

		/**
		 * Get Addons by Block ID Function
         *
         * @param \WC_Product $product The product.
         *
         * @return array
         */
		public function yith_wapo_get_blocks_by_product( $product = null, $variation = null, $visible = false ) {
			global $wpdb;

            $blocks = array();

            if ( is_numeric( $product ) ) {
                $product = wc_get_product( $product );
            }

            if ( ! $product instanceof WC_Product ) {
                return $blocks;
            }

            if ( is_numeric( $variation ) ) {
                $variation = wc_get_product( $variation );
            }

            $product_id = $product->get_id();

            if ( $variation instanceof WC_Product_Variation ) {
                $product_id = $variation->get_id();
            }
            $product_cats = apply_filters( 'yith_wapo_get_original_category_ids', $product->get_category_ids(), $product, apply_filters( 'yith_wapo_addon_product_id', $product_id ) );
            $product_cats = ! ! $product_cats ? ( '(\'' . implode( '\', \'', $product_cats ) . '\')' ) : '(\'0\')';

            $is_logged_in = is_user_logged_in();
            $user_id      = get_current_user_id();
            $user         = get_user_by( 'id', $user_id );
            $user_roles   = $user instanceof WP_User ? $user->roles : '';
            $user_roles   = ! ! $user_roles ? ( '(\'' . implode( '\', \'', $user_roles ) . '\')' ) : '(\'0\')';

            $blocks_table       = $wpdb->prefix . YITH_WAPO()->db::YITH_WAPO_BLOCKS;
            $associations_table = $wpdb->prefix . YITH_WAPO()->db::YITH_WAPO_BLOCKS_ASSOCIATIONS;

            $logged_in_association = $is_logged_in ? 'logged_users' : 'guest_users';

            $membership_plans = '(\'0\')'; // Default.

            if ( defined( 'YITH_WCMBS_PREMIUM' ) ) {
                $member             = YITH_WCMBS_Members()->get_member( $user_id );
                $membership_plans   = $member->get_membership_plans( array( 'return' => 'id' ) );
                $membership_plans   = ! ! $membership_plans ? ( '(\'' . implode( '\', \'', $membership_plans ) . '\')' ) : '(\'0\')';
            }

            $vendor_ids = apply_filters( 'yith_wapo_get_blocks_by_product_set_vendor', array( 0 ), $product ); // All.
            $vendor_ids = ! ! $vendor_ids ? ( '(\'' . implode( '\', \'', $vendor_ids ) . '\')' ) : '(\'0\')';

            $query = $wpdb->prepare( "
            SELECT blocks.id
            FROM $blocks_table as blocks
            WHERE
                (
                    visibility = %d
                )
                AND
                (
                    id IN (
                            SELECT id FROM $blocks_table
                            WHERE vendor_id IN $vendor_ids
                        )
                )
                AND
                (
                    (
                        id IN (
                            SELECT id FROM $blocks_table
                            WHERE product_association = %s
                        )
                        OR
                        id IN (
                            SELECT DISTINCT(id) FROM $blocks_table as i1
                            JOIN $associations_table as a1 on a1.rule_id = i1.id
                            WHERE product_association = %s 
                                AND ( type = %s AND object = '%d' ) OR ( type = %s AND object IN $product_cats )
                        )
                    )
                    AND 
                    (
                        id IN (
                            SELECT id FROM $blocks_table
                            WHERE user_association IN ( %s, '$logged_in_association' )
                        )
                            OR 
                        id IN (
                            SELECT DISTINCT(id) FROM $blocks_table as i2
                            JOIN $associations_table as a2 on a2.rule_id = i2.id
                            WHERE user_association = %s
                                AND ( type = %s AND object IN $user_roles )
                        )
                            OR 
                        id IN (
                            SELECT DISTINCT(id) FROM $blocks_table as i3
                            JOIN $associations_table as a3 on a3.rule_id = i3.id
                            WHERE user_association = %s
                                AND ( type = %s AND object IN $membership_plans )
                        )
                    )
                )
                AND
                ( 
                    id NOT IN (
                        SELECT DISTINCT(rule_id)
                        FROM $blocks_table as i4
                        JOIN $associations_table as a4 on a4.rule_id = i4.id
                        WHERE exclude_products = %d AND type = %s AND object = '%d'
                    )
                    AND 
                    id NOT IN (
                        SELECT DISTINCT(rule_id)
                        FROM $blocks_table as i5
                        JOIN $associations_table as a5 on a5.rule_id = i5.id
                        WHERE exclude_products = %d AND type = %s AND object IN $product_cats
                    )
                    AND 
                    id NOT IN (
                        SELECT DISTINCT(rule_id)
                        FROM $blocks_table as i6
                        JOIN $associations_table as a6 on a6.rule_id = i6.id
                        WHERE exclude_users = %d AND type = %s AND object IN $user_roles
                    )
                )
            ORDER BY priority
            ",
                array(
                    $visible,
                    'all',
                    'products',
                    'product',
                    $product_id,
                    'category',
                    'all',
                    'user_roles',
                    'user_role',
                    'membership',
                    'membership',
                    1,
                    'excluded_product',
                    $product_id,
                    1,
                    'excluded_category',
                    1,
                    'excluded_user_role'

                )
            );

            $blocks = $wpdb->get_col( $query );

			return apply_filters( 'yith_wapo_get_blocks_by_product', $blocks, $product, $variation, $visible );
		}


	}
}

/**
 * Unique access to instance of YITH_WAPO_DB class
 *
 * @since 4.0.0
 * @return YITH_WAPO_DB
 */
function YITH_WAPO_DB() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_DB::get_instance();
}
