<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Role_Based_Prices_Post_Type' ) ) {

	class YITH_Role_Based_Prices_Post_Type {
		protected static $instance;
		protected static $cache;

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * YITH_Role_Based_Prices_Post_Type constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'add_metaboxes' ) );
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );
			add_action( 'edit_form_advanced', array( $this, 'add_return_to_list_button' ) );
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_type_metaboxes' ) );
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_Role_Based_Prices_Post_Type
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}



		/**
		 * add capabilities
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_capabilities() {

			$capability_type = 'price_rule';
			$caps            = array(
				'edit_post'              => "edit_{$capability_type}",
				'delete_post'            => "delete_{$capability_type}",
				'edit_posts'             => "edit_{$capability_type}s",
				'edit_others_posts'      => "edit_others_{$capability_type}s",
				'publish_posts'          => "publish_{$capability_type}s",
				'read_private_posts'     => "read_private_{$capability_type}s",
				'delete_posts'           => "delete_{$capability_type}s",
				'delete_private_posts'   => "delete_private_{$capability_type}s",
				'delete_published_posts' => "delete_published_{$capability_type}s",
				'delete_others_posts'    => "delete_others_{$capability_type}s",
				'edit_private_posts'     => "edit_private_{$capability_type}s",
				'edit_published_posts'   => "edit_published_{$capability_type}s",
				'create_posts'           => "edit_{$capability_type}s",
			);

			// gets the admin and shop_mamager roles
			$admin        = get_role( 'administrator' );
			$shop_manager = get_role( 'shop_manager' );


			if ( ! is_null( $admin ) ) {

				$this->add_caps( $admin, $caps );
			}

			if ( ! is_null( $shop_manager ) ) {

				$this->add_caps( $shop_manager, $caps );
			}

		}

		/**
		 * @author YITHEMES
		 * @since 1.0.11
		 *
		 * @param WP_Role $role
		 * @param array $caps
		 */
		public function add_caps( $role, $caps ) {

			foreach ( $caps as $key => $cap ) {

				$role->add_cap( $cap );
			}
		}

		/**
		 * Get the tab taxonomy label
		 *
		 * @param   $arg string The string to return. Defaul empty. If is empty return all taxonomy labels
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 * @return array taxonomy label
		 *
		 */
		public function get_taxonomy_label( $arg = '' ) {

			$label = apply_filters( 'yith_role_based_prices_taxonomy_label', array(
					'name'               => _x( 'YITH WooCommerce Role Based Prices', 'post type general name', 'yith-woocommerce-role-based-prices' ),
					'singular_name'      => _x( 'Role Based Prices', 'post type singular name', 'yith-woocommerce-role-based-prices' ),
					'menu_name'          => __( 'Role Based Prices', 'yith-woocommerce-role-based-prices' ),
					'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-role-based-prices' ),
					'all_items'          => __( 'All role based prices', 'yith-woocommerce-role-based-prices' ),
					'view_item'          => __( 'View role based price', 'yith-woocommerce-role-based-prices' ),
					'add_new_item'       => __( 'Add new role based price', 'yith-woocommerce-role-based-prices' ),
					'add_new'            => __( 'Add new role based price', 'yith-woocommerce-role-based-prices' ),
					'edit_item'          => __( 'Edit role based price', 'yith-woocommerce-role-based-prices' ),
					'update_item'        => __( 'Update role based price', 'yith-woocommerce-role-based-prices' ),
					'search_items'       => __( 'Search role based price', 'yith-woocommerce-role-based-prices' ),
					'not_found'          => __( 'No role based price found', 'yith-woocommerce-role-based-prices' ),
					'not_found_in_trash' => __( 'No role based price found in Trash',
						'yith-woocommerce-role-based-prices' ),
				)
			);

			return ! empty( $arg ) ? $label[ $arg ] : $label;
		}

		/**
		 * add metabox to post type
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_metaboxes() {

			/**
			 * @var $metaboxes array metabox_id, metabox_opt
			 */
			$metaboxes = array(
				'yit-role-based-prices-rule-metaboxes' => 'price-rules-metaboxes-options.php',
			);

			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once( YWCRBP_DIR . 'plugin-fw/yit-plugin.php' );
			}

			foreach ( $metaboxes as $key => $metabox ) {
				$args = require_once( YWCRBP_TEMPLATE_PATH . '/metaboxes/' . $metabox );
				$box  = YIT_Metabox( $key );
				$box->init( $args );
			}
		}

		/**
		 * add a button in single post for back to list
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_return_to_list_button() {

			global $post;

			if ( isset( $post ) && 'yith_price_rule' === $post->post_type ) {
				$admin_url = admin_url( 'admin.php' );
				$params    = array(
					'page' => 'yith_wcrbp_panel',
					'tab'  => 'price-rules'
				);

				$list_url = apply_filters( 'yith_wc_role_based_prices_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
				$button   = sprintf( '<a class="button-secondary" href="%s">%s</a>', $list_url,
					__( 'Back to rules',
						'yith-woocommerce-role-based-prices' ) );
				echo $button;
			}
		}

		/**
		 * show custom metabox type
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		public function add_custom_type_metaboxes( $args ) {
			global $post;

			if ( isset( $post ) && 'yith_price_rule' === $post->post_type ) {

				$custom_types = array( 'chosen-user-role', 'ywcrbp-ajax-category', 'ywcrbp-ajax-tag', 'custom-text' );
				if ( in_array( $args['type'], $custom_types ) ) {
					$args['basename'] = YWCRBP_DIR;
					$args['path']     = 'metaboxes/types/';
				}

			}

			return $args;
		}

		/**
		 * return all price rule order by priority
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $args
		 * @param int $product_id
		 *
		 * @return array
		 */
		public function get_price_rule( $args = array(), $product_id ) {
			$defaults = array(
				'posts_per_page' => - 1,
				'post_type'      => 'yith_price_rule',
				'fields'         => 'ids',
				'post_status'    => 'publish'
			);

			$params = wp_parse_args( $args, $defaults );

			$params  = apply_filters( 'yith_wc_role_based_price_params_rule', $params, $product_id );

			$cache_key = md5(json_encode($params));
			if (isset(static::$cache[$cache_key])) {
				return static::$cache[$cache_key];
			}

			$results = get_posts( $params );
			static::$cache[$cache_key] = $results;


			return $results;
		}

		/**
		 * return only global price rule
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_global_price_rule( $is_active = true, $product_id = false ) {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => '_ywcrbp_type_rule',
						'value'   => 'global',
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_active_rule',
						'value'   => $is_active,
						'compare' => '='
					)
				)
			);


			return $this->get_price_rule( $args, $product_id );
		}


		public function get_array_global_price_rule() {

			$user_roles = array_keys( ywcrbp_get_user_role() );

			$global_rules = array();

			foreach ( $user_roles as $role ) {

				$global_rules[ $role ] = $this->get_price_rule_by_user_role( $role );
			}

			return $global_rules;
		}

		/**
		 * return only category price rule
		 * @author YITHEMES
		 *
		 * @param string $user_role
		 * @param int $product_id
		 * @param bool $is_active
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_category_price_rule( $user_role = '', $product_id, $is_active = true ) {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => '_ywcrbp_type_rule',
						'value'   => 'category',
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_active_rule',
						'value'   => $is_active,
						'compare' => '='
					),
				)
			);

			if ( $user_role !== '' ) {

				$args['meta_query'][] = array(
					'key'     => '_ywcrbp_role',
					'value'   => $user_role,
					'compare' => '='
				);
			}


			return $this->get_price_rule( $args, $product_id );
		}

		public function get_exclude_category_price_rule( $user_role = '', $product_id, $is_active = true ) {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => '_ywcrbp_type_rule',
						'value'   => 'exc_category',
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_active_rule',
						'value'   => $is_active,
						'compare' => '='
					),
				)
			);

			if ( $user_role !== '' ) {

				$args['meta_query'][] = array(
					'key'     => '_ywcrbp_role',
					'value'   => $user_role,
					'compare' => '='
				);
			}


			return $this->get_price_rule( $args, $product_id );
		}

		/**
		 * return onlu tag price rule
		 * @author YITHEMES
		 *
		 * @param string $user_role
		 * @param int $product_id
		 * @param bool $is_active
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_tag_price_rule( $user_role = '', $product_id, $is_active = true ) {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => '_ywcrbp_type_rule',
						'value'   => 'tag',
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_active_rule',
						'value'   => $is_active,
						'compare' => '='
					),
				)
			);

			if ( $user_role !== '' ) {

				$args['meta_query'][] = array(
					'key'     => '_ywcrbp_role',
					'value'   => $user_role,
					'compare' => '='
				);
			}

			return $this->get_price_rule( $args, $product_id );

		}

		public function get_exclude_tag_price_rule( $user_role = '', $product_id, $is_active = true ) {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => '_ywcrbp_type_rule',
						'value'   => 'exc_tag',
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_active_rule',
						'value'   => $is_active,
						'compare' => '='
					),
				)
			);

			if ( $user_role !== '' ) {

				$args['meta_query'][] = array(
					'key'     => '_ywcrbp_role',
					'value'   => $user_role,
					'compare' => '='
				);
			}

			return $this->get_price_rule( $args, $product_id );

		}

		/**
		 * @param string $user_role
		 *
		 * @oaram int $product_id
		 *
		 * @param bool $is_active
		 *
		 * @return array
		 */
		public function get_price_rule_by_user_role( $user_role, $product_id, $is_active = true ) {

			$args = array(
				'meta_query' => array(
					array(
						'key'     => '_ywcrbp_role',
						'value'   => $user_role,
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_active_rule',
						'value'   => $is_active,
						'compare' => '='
					),
					array(
						'key'     => '_ywcrbp_type_rule',
						'value'   => 'global',
						'compare' => '='
					),
				)
			);


			return $this->get_price_rule( $args, $product_id );
		}

		/**
		 * return only rule for a particular product categories
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $product_categories
		 * @param int $product_id
		 * @param string $user_role
		 *
		 * @return array
		 */
		public function get_price_rule_by_product_categories( $product_categories, $product_id, $user_role = '', $include_cat = true ) {

			if ( empty( $product_categories ) ) {
				return array();
			}

			if( $include_cat ) {
				$rule_cat = $this->get_category_price_rule( $user_role, $product_id );
			}else{

				$rule_cat = $this->get_exclude_category_price_rule( $user_role, $product_id );
			}
			$results = array();
			$meta_key = $include_cat ? '_ywcrbp_category_product' :	'_ywcrbp_exc_category_product';
			foreach ( $rule_cat as $rule_id ) {

				$rule_categories = get_post_meta( $rule_id, $meta_key, true );

				if ( ! is_array( $rule_categories ) ) {
					$rule_categories = explode( ",", $rule_categories );
				}
				if ( ! empty( $rule_categories ) ) {

					$role_category = array_map( 'intval', $rule_categories );
					$cat_intersect = array_intersect( $product_categories, $role_category );

					$cat_intersect = $include_cat ? !empty( $cat_intersect ) : empty( $cat_intersect );
					if ( $cat_intersect ) {
						$results[] = $rule_id;
					}
				}
			}

			return $results;

		}

		/**
		 * return only rule for a particular product tags
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param array $product_tags
		 * @param int $product_id
		 * @param string $user_role
		 *
		 * @return array
		 */
		public function get_price_rule_by_product_tags( $product_tags, $product_id, $user_role = '', $include_tag = true ) {

			if ( empty( $product_tags ) ) {
				return array();
			}

			if( $include_tag ) {
				$rule_tag = $this->get_tag_price_rule( $user_role, $product_id );
			}else{
				$rule_tag = $this->get_exclude_tag_price_rule( $user_role, $product_id );
			}
			$results = array();
			$meta_key = $include_tag ? '_ywcrbp_tag_product' :	'_ywcrbp_exc_tag_product';

			foreach ( $rule_tag as $rule_id ) {

				$rule_tags = get_post_meta( $rule_id, $meta_key, true );

				if ( ! is_array( $rule_tags ) ) {
					$rule_tags = explode( ",", $rule_tags );
				}
				if ( ! empty( $rule_tags ) ) {

					$rule_tags     = array_map( 'intval', $rule_tags );
					$tag_intersect = array_intersect( $product_tags, $rule_tags );
					$tag_intersect = $include_tag ? ! empty( $tag_intersect ) : empty( $tag_intersect );
					if ( $tag_intersect ) {
						$results[] = $rule_id;
					}
				}
			}

			return $results;

		}

	}
}

/**
 * @author YITHEMES
 * @since 1.0.o0
 * @return YITH_Role_Based_Prices_Post_Type
 */
function YITH_Role_Based_Type() {
	return YITH_Role_Based_Prices_Post_Type::get_instance();

}
