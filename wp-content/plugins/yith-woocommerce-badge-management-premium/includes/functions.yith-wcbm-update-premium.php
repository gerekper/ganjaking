<?php
/**
 * Premium Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Functions
 * @version 1.0.0
 */

/**
 * Require the update function to be used in this file
 */
require 'functions.yith-wcbm-update.php';

if ( ! function_exists( 'yith_wcbm_update_200_badges_meta_premium' ) ) {
	/**
	 * Update Badges meta Premium
	 *
	 * @return bool If true will repeat the process, otherwise it will stop
	 * @since 2.0.0
	 */
	function yith_wcbm_update_200_badges_meta_premium() {
		$args      = array(
			'posts_per_page' => 10,
			'fields'         => 'ids',
			'post_type'      => YITH_WCBM_Post_Types::$badge,
			'post_status'    => 'any',
			'meta_key'       => '_badge_meta', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		);
		$badge_ids = get_posts( $args );
		if ( ! $badge_ids ) {
			return false;
		}

		foreach ( $badge_ids as $badge_id ) {
			yith_wcbm_update_badge_meta_premium( $badge_id );
		}

		return true;
	}
}

if ( ! function_exists( 'yith_wcbm_update_200_products_badge_meta_premium' ) ) {
	/**
	 * Update Products Badge meta
	 *
	 * @return bool
	 */
	function yith_wcbm_update_200_products_badge_meta_premium() {
		$args        = array(
			'posts_per_page' => 10,
			'fields'         => 'ids',
			'post_type'      => 'product',
			'post_status'    => 'any',
			'meta_key'       => '_yith_wcbm_product_meta', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		);
		$product_ids = get_posts( $args );

		if ( ! $product_ids ) {
			return false;
		}

		foreach ( $product_ids as $product_id ) {
			yith_wcbm_update_product_badge_meta_premium( $product_id );
		}

		return true;
	}
}

if ( ! function_exists( 'yith_wcbm_update_200_badge_rules' ) ) {
	/**
	 * Update Badges Rules function
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	function yith_wcbm_update_200_badge_rules() {
		global $wpdb;
		$options_table = $wpdb->options;

		// Category and Shipping-class Badge rules updating.

		$types = array(
			'category'       => _x( 'Category Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
			'shipping-class' => _x( 'Shipping Class Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
		);
		foreach ( $types as $type => $title ) {
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value as badge_id FROM $options_table WHERE option_name LIKE %s AND option_value <> %s", "yith-wcbm-$type-badge-%", 'none' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( $results ) {
				$rule_data = array(
					'post_title'  => $title,
					'post_status' => 'publish',
					'post_type'   => YITH_WCBM_Post_Types_Premium::$badge_rule,
					'meta_input'  => array(
						'_type' => $type,
					),
				);
				$rule_id   = wp_insert_post( $rule_data );
				if ( $rule_id ) {
					$table = YITH_WCBM_DB::get_badge_rules_table_name();

					foreach ( $results as $association ) {
						$data = array(
							'rule_id'  => $rule_id,
							'type'     => $type,
							'value'    => str_replace( "yith-wcbm-$type-badge-", '', $association->option_name ),
							'badge_id' => absint( $association->badge_id ),
							'enabled'  => 1,
						);
						if ( $wpdb->insert( $table, $data, array( '%d', '%s', '%s', '%d', '%d' ) ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
							delete_option( $association->option_name );
						}
					}
				}
			}
		}

		// Product badge rules updating.

		$newer_then         = absint( get_option( 'yith-wcbm-badge-newer-than', 5 ) );
		$low_stock_quantity = absint( get_option( 'yith-wcbm-low-stock-qty', 3 ) );
		delete_option( 'yith-wcbm-badge-newer-than' );
		delete_option( 'yith-wcbm-low-stock-qty' );

		$product_rules = array(
			'recent'       => array(
				'title'           => _x( 'Recent Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'old_option_name' => 'yith-wcbm-recent-products-badge',
				'meta'            => array(
					'_newer_then' => $newer_then > 0 ? $newer_then : 5,
				),
			),
			'on-sale'      => array(
				'title'           => _x( 'On sale Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'old_option_name' => 'yith-wcbm-on-sale-badge',
			),
			'featured'     => array(
				'title'           => _x( 'Featured Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'old_option_name' => 'yith-wcbm-featured-badge',
			),
			'in-stock'     => array(
				'title'           => _x( 'In stock Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'old_option_name' => 'yith-wcbm-in-stock-badge',
			),
			'out-of-stock' => array(
				'title'           => _x( 'Out of stock Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'old_option_name' => 'yith-wcbm-out-of-stock-badge',
			),
			'low-stock'    => array(
				'title'           => _x( 'Low Stock Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'old_option_name' => 'yith-wcbm-low-stock-badge',
				'meta'            => array(
					'_low_stock_quantity' => $low_stock_quantity > 0 ? $low_stock_quantity : 3,
				),
			),
		);
		foreach ( $product_rules as $type => $rule_options ) {
			$badge_id = absint( get_option( $rule_options['old_option_name'], '' ) );
			if ( $badge_id ) {
				delete_option( $rule_options['old_option_name'] );
				$meta      = array(
					'_type' => 'product',
				);
				$rule_data = array(
					'post_title'  => $rule_options['title'],
					'post_status' => 'publish',
					'post_type'   => YITH_WCBM_Post_Types_Premium::$badge_rule,
					'meta_input'  => isset( $rule_options['meta'] ) ? array_merge( $meta, $rule_options['meta'] ) : $meta,
				);
				$rule_id   = wp_insert_post( $rule_data );
				if ( $rule_id ) {
					$table = YITH_WCBM_DB::get_badge_rules_table_name();
					$data  = array(
						'rule_id'  => $rule_id,
						'type'     => 'product',
						'value'    => $type,
						'badge_id' => $badge_id,
						'enabled'  => 1,
					);
					$wpdb->insert( $table, $data, array( '%d', '%s', '%s', '%d', '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				}
			}
		}

		// Auction badge rules updating.
		$auction_options = array(
			'badge-auction-started'     => get_option( 'yith-wcbm-auction-badge-started', 'none' ),
			'badge-auction-not-started' => get_option( 'yith-wcbm-auction-badge-not-started', 'none' ),
			'badge-auction-finished'    => get_option( 'yith-wcbm-auction-badge-finished', 'none' ),
		);
		$auction_options = array_diff( $auction_options, array( 'none' ) );
		if ( $auction_options ) {
			$rule_data = array(
				'post_title'  => _x( 'Auction Products Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'post_status' => 'publish',
				'post_type'   => YITH_WCBM_Post_Types_Premium::$badge_rule,
				'meta_input'  => array( '_type' => 'auction' ),
			);
			$rule_id   = wp_insert_post( $rule_data );
			if ( $rule_id ) {
				foreach ( $auction_options as $status => $badge_id ) {
					if ( 'none' !== $badge_id ) {
						$table = YITH_WCBM_DB::get_badge_rules_table_name();
						$data  = array(
							'rule_id'  => $rule_id,
							'type'     => 'auction',
							'value'    => $status,
							'badge_id' => absint( $badge_id ),
							'enabled'  => 1,
						);
						$wpdb->insert( $table, $data, array( '%d', '%s', '%s', '%d', '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					}
				}
			}
		}

		// Dynamic badge rules updating.
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value as badge_id FROM $options_table WHERE option_name LIKE %s AND option_value <> %s", 'yith-wcbm-dynamic-pricing-badge-%', 'none' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $results ) {
			$rule_data = array(
				'post_title'  => _x( 'Dynamic Pricing Rule', '[ADMIN] Title of the Automatically created Badge Rule', 'yith-woocommerce-badges-management' ),
				'post_status' => 'publish',
				'post_type'   => YITH_WCBM_Post_Types_Premium::$badge_rule,
				'meta_input'  => array(
					'_type' => 'dynamic-pricing',
				),
			);
			$rule_id   = wp_insert_post( $rule_data );
			if ( $rule_id ) {
				$dynamic_rules_associations = array();
				foreach ( $results as $option ) {
					$dynamic_rules_associations[ str_replace( 'yith-wcbm-dynamic-pricing-badge-', '', $option->option_name ) ] = array( 'badge_id' => $option->badge_id );
				}

				$table = YITH_WCBM_DB::get_badge_rules_table_name();

				if ( defined( 'YITH_YWDPD_PREMIUM' ) && YITH_YWDPD_PREMIUM && defined( 'YITH_YWDPD_VERSION' ) && version_compare( YITH_YWDPD_VERSION, '3.0.0', '>=' ) && is_callable( 'ywdpd_get_price_rules' ) ) {
					$dynamic_rules = ywdpd_get_price_rules();

					foreach ( $dynamic_rules as $dynamic_rule ) {
						if ( array_key_exists( $dynamic_rule->get_key(), $dynamic_rules_associations ) ) {
							$dynamic_rules_associations[ $dynamic_rule->get_key() ]['post_id'] = $dynamic_rule->get_id();
						}
					}
				} else {
					$posts_table = $wpdb->posts;
					$meta_table  = $wpdb->postmeta;

					$sql                       = "SELECT post_id as rule_id, meta_value as rule_key FROM $meta_table INNER JOIN $posts_table WHERE post_type = 'ywdpd_discount' AND meta_value IN (" . implode( ',', array_fill( 0, count( $dynamic_rules_associations ), '%s' ) ) . ") AND meta_key = '_key';";
					$dynamic_rules_keys_to_ids = $wpdb->get_results( $wpdb->prepare( $sql, array_keys( $dynamic_rules_associations ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
					$dynamic_rules_keys_to_ids = $dynamic_rules_keys_to_ids ? array_combine( array_column( $dynamic_rules_keys_to_ids, 'rule_key' ), array_column( $dynamic_rules_keys_to_ids, 'rule_id' ) ) : array();

					foreach ( $dynamic_rules_keys_to_ids as $dynamic_rule_key => $dynamic_rule_id ) {
						if ( array_key_exists( $dynamic_rule_key, $dynamic_rules_associations ) ) {
							$dynamic_rules_associations[ $dynamic_rule_key ]['post_id'] = $dynamic_rule_id;
						}
					}
				}

				foreach ( $dynamic_rules_associations as $rule_key => $rule_options ) {
					if ( isset( $rule_options['post_id'], $rule_options['badge_id'] ) ) {
						$data = array(
							'rule_id'  => $rule_id,
							'type'     => 'dynamic-pricing',
							'value'    => absint( $rule_options['post_id'] ),
							'badge_id' => absint( $rule_options['badge_id'] ),
							'enabled'  => 1,
						);
						if ( $wpdb->insert( $table, $data, array( '%d', '%s', '%s', '%d', '%d' ) ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
							delete_option( 'yith-wcbm-dynamic-pricing-badge-' . $rule_key );
						}
					}
				}
			}
		}

		return false;
	}
}

