<?php
/**
 * Rules Class
 *
 * @package YITH\BadgeManagementPremium\Classes
 * @since   2.0
 * @author  YITH <plugins@yithemes.com>
 */

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery

if ( ! class_exists( 'YITH_WCBM_Badge_Rules' ) ) {
	/**
	 * Class YITH_WCBM_Badge_Rules
	 */
	class YITH_WCBM_Badge_Rules {
		/**
		 * Class instance
		 *
		 * @var YITH_WCBM_Badge_Rules
		 */
		protected static $instance;

		/**
		 * Return the class instance
		 *
		 * @return YITH_WCBM_Badge_Rules
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_Badge_Rules constructor.
		 */
		private function __construct() {

			add_filter( 'wp_insert_post_data', array( $this, 'check_badge_rule_title_to_prevent_duplicate' ), 10, 2 );

			add_filter( 'post_updated_messages', array( $this, 'badge_rule_updated_messages' ) );
			add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_badge_rule_updated_messages' ), 10, 2 );

			// Register data store.
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );

			// Print fields in metabox.
			add_action( 'yith_wcbm_print_badge_rule_associations_field', array( $this, 'print_badge_rule_associations_field' ) );
			add_action( 'yith_wcbm_print_schedule_dates_badge_rule_field', array( $this, 'print_schedule_dates_badge_rule_field' ) );

			// AJAX calls handler.
			add_action( 'wp_ajax_yith_wcbm_badge_rule_toggle_enable', array( $this, 'ajax_toggle_enable_rule' ) );

			add_filter( 'yith_plugin_fw_metabox_yith-wcbm-badge-rules_field_pre_get_value', array( $this, 'initialize_value_in_metabox_field' ), 10, 4 );

			// Handle meta saving.
			add_action( 'save_post_' . YITH_WCBM_Post_Types_Premium::$badge_rule, array( $this, 'save_badge_rule' ) );
			add_action( 'admin_action_yith_wcbm_clone_badge_rule', array( $this, 'clone_badge_rule' ) );
			add_action( 'delete_post', array( $this, 'delete_badge_rule' ) );

			add_filter( 'post_class', array( $this, 'add_post_class_in_invalid_rule' ), 10, 3 );
			add_filter( 'get_edit_post_link', array( $this, 'edit_post_link_in_invalid_rule' ), 10, 2 );

			add_action( 'woocommerce_new_order', array( $this, 'invalidate_badge_rules_cache' ) );
		}

		/**
		 * Filter edit post link for Badge Rules with an invalid type.
		 *
		 * @param string $link    The post link.
		 * @param int    $post_id The post ID.
		 *
		 * @return string
		 */
		public function edit_post_link_in_invalid_rule( $link, $post_id ) {
			$rule = yith_wcbm_get_badge_rule( $post_id );
			if ( $rule && ! in_array( $rule->get_type( 'edit' ), array_keys( yith_wcbm_get_badge_rules_types() ), true ) ) {
				return '#';
			}

			return $link;
		}

		/**
		 * Check Badge rule title to prevent duplicate
		 *
		 * @param array $data    Post Data.
		 * @param array $postarr Post Array.
		 *
		 * @return array
		 */
		public function check_badge_rule_title_to_prevent_duplicate( $data, $postarr ) {
			if ( YITH_WCBM_Post_Types_Premium::$badge_rule === $data['post_type'] ) {
				$data['post_title'] = yith_wcbm_get_unique_post_title( $data['post_title'], $postarr['ID'], $postarr['post_type'] );
			}

			return $data;
		}

		/**
		 * Change post default messages for Badge Rule post type
		 *
		 * @param array $messages The Badge Rule messages.
		 *
		 * @return array
		 */
		public function badge_rule_updated_messages( $messages ) {
			global $post_type;
			if ( YITH_WCBM_Post_Types_Premium::$badge_rule === $post_type ) {
				$messages['post'][1] = __( 'Badge Rule saved.', 'yith-woocommerce-badges-management' );
				$messages['post'][4] = __( 'Badge Rule saved.', 'yith-woocommerce-badges-management' );
				$messages['post'][6] = __( 'Badge Rule created.', 'yith-woocommerce-badges-management' );
				$messages['post'][7] = __( 'Badge Rule saved.', 'yith-woocommerce-badges-management' );
				$messages['post'][8] = __( 'Badge Rule saved.', 'yith-woocommerce-badges-management' );
			}

			return $messages;
		}

		/**
		 * Change bulk post default messages for Badge post type
		 *
		 * @param array $messages    The badge messages.
		 * @param array $bulk_counts The bulk badge counts.
		 *
		 * @return array
		 */
		public function bulk_badge_rule_updated_messages( $messages, $bulk_counts ) {
			global $post_type;
			if ( YITH_WCBM_Post_Types_Premium::$badge_rule === $post_type ) {
				// translators: %s is the deleted badge rules number.
				$messages['post']['deleted'] = _n( '%s badge rule deleted.', '%s badge rules deleted.', $bulk_counts['deleted'] );
			}

			return $messages;
		}

		/**
		 * Add Badge rule Data Store to WC ones.
		 *
		 * @param array $data_stores WC Data Stores.
		 *
		 * @return array
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['badge_rule']             = 'YITH_WCBM_Badge_Rule_Data_Store_CPT';
			$data_stores['associative_badge_rule'] = 'YITH_WCBM_Associative_Badge_Rule_Data_Store_CPT';

			$data_stores['badge_rule_tag']            = 'YITH_WCBM_Badge_Rule_Tag_Data_Store_CPT';
			$data_stores['badge_rule_product']        = 'YITH_WCBM_Badge_Rule_Product_Data_Store_CPT';
			$data_stores['badge_rule_category']       = 'YITH_WCBM_Badge_Rule_Category_Data_Store_CPT';
			$data_stores['badge_rule_shipping_class'] = 'YITH_WCBM_Badge_Rule_Shipping_Class_Data_Store_CPT';

			return $data_stores;
		}

		/**
		 * Handle Badge Rule saving
		 *
		 * @param int $post_id Badge Rule ID.
		 */
		public function save_badge_rule( $post_id ) {
			if ( ( isset( $_POST['yith_wcbm_badge_rule_security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcbm_badge_rule_security'] ) ), 'yith_wcbm_save_badge_rule' ) ) ){
				$rule = yith_wcbm_get_badge_rule( $post_id );
				if ( $rule ) {
					$props = $rule->get_internal_props_from_request();
					$rule->set_props( $props );
					$rule->save();
				}
			}
		}

		/**
		 * Clone badge rules action handler
		 */
		public function clone_badge_rule() {
			if ( isset( $_REQUEST['post'], $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'yith_wcbm_clone_badge_rule' ) ) {
				$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

				$rule = get_post( $id );

				if ( ! $rule || YITH_WCBM_Post_Types_Premium::$badge_rule !== $rule->post_type ) {
					// translators: %s: Badge Rule ID.
					wp_die( esc_html( sprintf( __( 'Error while duplicating badge rule: badge rule #%s not found', 'yith-woocommerce-badges-management' ), $id ) ) );
				}

				$rule = yith_wcbm_get_badge_rule( $rule );
				if ( $rule ) {
					$clone = clone $rule;
					$clone->set_id( 0 );
					$clone->set_title( yith_wcbm_get_unique_post_title( $clone->get_title(), 0, YITH_WCBM_Post_Types_Premium::$badge_rule ) );
					$clone->save();
				}
			}

			wp_safe_redirect( add_query_arg( array( 'post_type' => YITH_WCBM_Post_Types_Premium::$badge_rule ), admin_url( 'edit.php' ) ) );
		}

		/**
		 * Handle Badge Rule deleting
		 *
		 * @param int $post_id Badge Rule ID.
		 */
		public function delete_badge_rule( $post_id ) {
			if ( get_post_type( $post_id ) === YITH_WCBM_Post_Types_Premium::$badge_rule ) {
				$rule = yith_wcbm_get_badge_rule( $post_id );
				if ( $rule ) {
					remove_action( 'delete_post', array( $this, 'delete_badge_rule' ) );
					$rule->delete( true );
					add_action( 'delete_post', array( $this, 'delete_badge_rule' ) );
				}
			}
		}

		/**
		 * Print Badge Rules Associations field
		 *
		 * @param array $args Field Options.
		 */
		public function print_badge_rule_associations_field( $args ) {
			$rule = yith_wcbm_get_badge_rule();
			// translators: 1. a select with identifier of product groups; 2. is the Badge select.
			$args['text'] = _x( 'In products of %1$s show %2$s', '[ADMIN] Field used in Badge Rules editing page (Category and Tag types)', 'yith-woocommerce-badges-management' );

			$args['badge_field']                = array(
				'id'   => 'yith-wcbm-rule-badge',
				'name' => ! empty( $args['name'] ) ? $args['name'] . '[{{data.ruleID}}][badge]' : '',
				'type' => 'ajax-posts',
				'data' => array(
					'placeholder'          => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
					'post_type'            => YITH_WCBM_Post_Types::$badge,
					'minimum_input_length' => '1',
				),
			);
			$args['associations_field']['name'] = ! empty( $args['name'] ) ? $args['name'] . '[{{data.ruleID}}][association]' : '';
			$args['value']                      = (array) ( ! $rule ? $args['value'] : $rule->get_associations() );
			yith_wcbm_get_view( 'fields/badge-rule-associations.php', $args );
		}

		/**
		 * Print Schedule Dates Badge Rules field
		 *
		 * @param array $args Field Options.
		 */
		public function print_schedule_dates_badge_rule_field( $args ) {
			$rule = get_post();

			// translators: 1. is the datepicker "From date"; 2. is the datepicker "To date".
			$args['text']          = esc_html_x( '%1$s to %2$s', '[ADMIN] Field used in Badge Rules editing page (Scheduling range)', 'yith-woocommerce-badges-management' );
			$args['schedule_from'] = array(
				'type'  => 'datepicker',
				'data'  => $args['data'],
				'id'    => $args['id'] . '_from',
				'name'  => str_replace( $args['id'], $args['id'] . '_from', $args['name'] ),
				'value' => date_i18n( 'd-m-Y', get_post_meta( $rule->ID, $args['id'] . '_from', true ) ),
			);
			$args['schedule_to']   = array(
				'type'  => 'datepicker',
				'data'  => $args['data'],
				'id'    => $args['id'] . '_to',
				'name'  => str_replace( $args['id'], $args['id'] . '_to', $args['name'] ),
				'value' => date_i18n( 'd-m-Y', get_post_meta( $rule->ID, $args['id'] . '_to', true ) ),
			);

			yith_wcbm_get_view( 'fields/schedule-dates-badge-rule.php', $args );
		}

		/**
		 * AJAX Toggle Badge Rule enable state
		 */
		public function ajax_toggle_enable_rule() {
			$response = array( 'success' => false );
			if ( isset( $_POST['security'], $_POST['rule_id'], $_POST['rule_enabled'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'yith_wcbm_badge_rules' ) && get_post_type( absint( $_POST['rule_id'] ) ) === YITH_WCBM_Post_Types_Premium::$badge_rule ) {
				$rule = yith_wcbm_get_badge_rule( absint( $_POST['rule_id'] ) );
				$rule->set_enabled( wc_bool_to_string( 'yes' === sanitize_text_field( wp_unslash( $_POST['rule_enabled'] ) ) ) );
				$rule->save();
				$response['success'] = true;
			}
			wp_send_json( $response );
			exit();
		}

		/**
		 * Filter the value initialized in metabox fields
		 *
		 * @param null   $value      The value.
		 * @param int    $post_id    The post ID.
		 * @param string $field_name The field name.
		 * @param array  $field      The field.
		 *
		 * @return mixed
		 */
		public function initialize_value_in_metabox_field( $value, $post_id, $field_name, $field ) {
			static $rule = null;
			if ( is_null( $rule ) ) {
				$rule = yith_wcbm_get_badge_rule( $post_id );
			}
			$prop   = preg_replace( '/yith_wcbm_badge_rule|\[|\]/m', '', $field['name'] );
			$getter = 'get' . $prop;
			if ( method_exists( $rule, $getter ) ) {
				$value = $rule->$getter();
				if ( '_badge' === $prop && ! yith_wcbm_get_badge_object( $value ) ) {
					$value = null;
				}
			}

			return $value;
		}

		/**
		 * Add class to identify invalid badge rules
		 *
		 * @param array  $classes The list of classes.
		 * @param string $class   The class.
		 * @param int    $post_id The post ID.
		 *
		 * @return array
		 */
		public function add_post_class_in_invalid_rule( $classes, $class, $post_id ) {
			$rule = yith_wcbm_get_badge_rule( $post_id );
			if ( $rule && ! in_array( $rule->get_type( 'edit' ), array_keys( yith_wcbm_get_badge_rules_types() ), true ) ) {
				$classes[] = 'yith-wcbm-badge-rule--disabled';
			}

			return $classes;
		}

		/*
		|--------------------------------------------------------------------------
		| Badge Rule product and badge associations getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get product Badges from rules
		 *
		 * @param int|WC_Product|WP_Post $product Product or Product ID.
		 *
		 * @return array
		 */
		public function get_product_badge_ids_from_rules( $product ) {
			$badges  = array();
			$product = wc_get_product( $product );
			if ( $product ) {
				$product_id = $product->get_id();
				$user_id    = function_exists( 'get_current_user_id' ) ? get_current_user_id() : false;
				foreach ( yith_wcbm_get_badge_rules_types() as $rule_type_id => $rule_type ) {
					$rule_type_badges = array();
					if ( array_key_exists( 'callback', $rule_type ) && is_callable( $rule_type['callback'] ) ) {
						$rule_type_badges = call_user_func( $rule_type['callback'], $product_id, $user_id );
					}
					$rule_type_for_filter = preg_replace( '/[^a-zA-Z0-9_]/m', '', str_replace( '-', '_', $rule_type_id ) );
					$rule_type_badges     = apply_filters( 'yith_wcbm_get_product_badges_from_' . $rule_type_for_filter . '_rules', $rule_type_badges, $product_id );
					$badges               = array_merge( $badges, $rule_type_badges );
				}
			}

			return $badges;
		}

		/**
		 * Get product badges from product rules
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return array
		 */
		public function get_product_badges_from_product_rules( $product_id ) {
			$badges  = array();
			$product = wc_get_product( $product_id );

			if ( $product ) {
				$properties = array(
					'all',
					'recent',
					'on-sale',
					'featured',
					'in-stock',
					'out-of-stock',
					'back-order',
					'low-stock',
					'bestsellers',
				);
				$properties = array_fill_keys( $properties, false );
				foreach ( $properties as $property => &$value ) {
					switch ( $property ) {
						case 'all':
						case 'recent':
						case 'bestsellers':
							$value = true;
							break;
						case 'on-sale':
							$value = yith_wcbm_product_is_on_sale( $product );
							break;
						case 'featured':
							$value = $product->is_featured();
							break;
						case 'in-stock':
							$value = $product->is_in_stock() && ! $product->is_on_backorder();
							break;
						case 'out-of-stock':
							$value = ! $product->is_in_stock();
							break;
						case 'back-order':
							$value = $product->is_on_backorder();
							break;
						case 'low-stock':
							$value = $product->get_manage_stock();
							break;
					}
				}

				global $wpdb;
				$table      = $wpdb->prefix . YITH_WCBM_DB::BADGE_RULES_ASSOCIATIONS_TABLE;
				$prod_props = array_keys( array_filter( $properties ) );
				$prod_props = "'" . implode( "','", $prod_props ) . "'";

				$sql = "SELECT rule_id, badge_id FROM $table WHERE type = 'product' AND enabled = 1 AND value IN ({$prod_props});";

				$results  = $wpdb->get_results( $sql ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				$rule_ids = array();
				foreach ( array_unique( array_column( $results, 'rule_id' ) ) as $rule_id ) {
					if ( yith_wcbm_is_badge_rule_valid( $rule_id, $product->get_id() ) ) {
						$rule_ids[] = $rule_id;
					}
				}

				foreach ( $results as $result ) {
					if ( in_array( $result->rule_id, $rule_ids, true ) ) {
						$badges[] = $result->badge_id;
					}
				}
			}

			return $badges;
		}

		/**
		 * Get product badges from category badge rules
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return array
		 */
		public function get_product_badges_from_category_rules( $product_id ) {
			$badges = array();
			if ( yith_wcbm_is_wpml_parent_based_on_default_language() ) {
				$prod_cats = yith_wcbm_get_terms_in_default_language( $product_id, 'product_cat' );
			} else {
				$prod_cats = get_the_terms( $product_id, 'product_cat' );
			}

			if ( ! empty( $prod_cats ) ) {
				$cat_ids = array_column( $prod_cats, 'term_id' );
				$where   = array(
					array(
						'column' => 'type',
						'value'  => 'category',
					),
					array(
						'column'  => 'value',
						'value'   => $cat_ids,
						'compare' => 'IN',
					),
				);

				$results  = $this->get_associations( $where );
				$rule_ids = array();
				foreach ( array_unique( array_column( $results, 'rule_id' ) ) as $rule_id ) {
					if ( yith_wcbm_is_badge_rule_valid( $rule_id, $product_id ) ) {
						$rule_ids[] = $rule_id;
					}
				}

				foreach ( $results as $result ) {
					if ( in_array( $result->rule_id, $rule_ids, true ) ) {
						$badges[] = $result->badge_id;
					}
				}
			}

			return array_unique( $badges );
		}

		/**
		 * Get product badges from shipping class badge rules
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return array
		 */
		public function get_product_badges_from_shipping_class_rules( $product_id ) {
			$badges = array();

			// Shipping Class.
			if ( yith_wcbm_is_wpml_parent_based_on_default_language() ) {
				$shipping_classes = yith_wcbm_get_terms_in_default_language( $product_id, 'product_shipping_class' );
			} else {
				$shipping_classes = get_the_terms( $product_id, 'product_shipping_class' );
			}
			if ( $shipping_classes && ! is_wp_error( $shipping_classes ) ) {
				$shipping_class_id = current( $shipping_classes )->term_id;

				$where = array(
					array(
						'column' => 'type',
						'value'  => 'shipping-class',
					),
					array(
						'column' => 'value',
						'value'  => $shipping_class_id,
					),
				);

				$results = $this->get_associations( $where );

				$rule_ids = array();
				foreach ( array_unique( array_column( $results, 'rule_id' ) ) as $rule_id ) {
					if ( yith_wcbm_is_badge_rule_valid( $rule_id, $product_id ) ) {
						$rule_ids[] = $rule_id;
					}
				}

				foreach ( $results as $result ) {
					if ( in_array( $result->rule_id, $rule_ids, true ) ) {
						$badges[] = $result->badge_id;
					}
				}
			}

			return array_unique( $badges );
		}

		/**
		 * Get product badges from tag badge rules
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return array
		 */
		public function get_product_badges_from_tag_rules( $product_id ) {
			$badges = array();

			if ( yith_wcbm_is_wpml_parent_based_on_default_language() ) {
				$tags = yith_wcbm_get_terms_in_default_language( $product_id, 'product_tag' );
			} else {
				$tags = get_the_terms( $product_id, 'product_tag' );
			}
			if ( $tags && ! is_wp_error( $tags ) ) {
				$tag_ids = array_column( $tags, 'term_id' );
				$where   = array(
					array(
						'column' => 'type',
						'value'  => 'tag',
					),
					array(
						'column'  => 'value',
						'value'   => $tag_ids,
						'compare' => 'IN',
					),
				);

				$results = $this->get_associations( $where );

				$rule_ids = array();
				foreach ( array_unique( array_column( $results, 'rule_id' ) ) as $rule_id ) {
					if ( yith_wcbm_is_badge_rule_valid( $rule_id, $product_id ) ) {
						$rule_ids[] = $rule_id;
					}
				}

				foreach ( $results as $result ) {
					if ( in_array( $result->rule_id, $rule_ids, true ) ) {
						$badges[] = $result->badge_id;
					}
				}
			}

			return array_unique( $badges );
		}

		/**
		 * Get Associations from db
		 *
		 * @param array $where Where conditions.
		 *
		 * @return object[]
		 */
		public function get_associations( $where ) {
			global $wpdb;
			$table = $wpdb->prefix . YITH_WCBM_DB::BADGE_RULES_ASSOCIATIONS_TABLE;

			$where = $this->get_sql_where_from_array( $where );

			$sql = "SELECT rule_id, badge_id FROM $table WHERE {$where};";

			return $wpdb->get_results( $sql ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		}

		/**
		 * Get SQL Where from conditions array
		 *
		 * @param array $where Where conditions.
		 *
		 * @return string
		 */
		private function get_sql_where_from_array( $where ) {
			$sql = '';
			if ( isset( $where['column'] ) ) {
				$defaults = array(
					'value'   => 1,
					'compare' => '=',
				);
				$where    = wp_parse_args( $where, $defaults );
				$value    = $where['value'];
				if ( is_string( $value ) ) {
					$value = "'$value'";
				} elseif ( is_array( $value ) ) {
					$value = is_string( current( $value ) ) ? "'" . implode( "', '", $value ) . "'" : implode( ', ', $value );
					$value = '(' . $value . ')';
				}
				$sql = $where['column'] . ' ' . $where['compare'] . ' ' . $value;
			} elseif ( is_array( $where ) ) {
				$relation   = 'AND';
				$conditions = array();
				foreach ( $where as $key => $value ) {
					if ( 'relation' === $key ) {
						$relation = 'AND' === strtoupper( $value ) ? 'AND' : 'OR';
					} else {
						$conditions[] = $this->get_sql_where_from_array( $value );
					}
				}
				$sql = ' ( ' . implode( ' ' . $relation . ' ', $conditions ) . ' ) ';
			}

			return $sql;
		}

		/**
		 * Invalidate badge rules cache
		 */
		public function invalidate_badge_rules_cache() {
			delete_transient( 'yith_wcbm_bestsellers_products' );
		}
	}
}

if ( ! function_exists( 'yith_wcbm_badge_rules' ) ) {
	/**
	 * Get the class instance
	 *
	 * @return YITH_WCBM_Badge_Rules
	 */
	function yith_wcbm_badge_rules() {
		return YITH_WCBM_Badge_Rules::get_instance();
	}
}
