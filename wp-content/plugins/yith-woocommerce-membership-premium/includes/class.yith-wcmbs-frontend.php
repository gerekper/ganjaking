<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	class YITH_WCMBS_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;

		protected $_woocommerce_product_actions                        = array();
		protected $_woocommerce_product_actions_priority_uptodate      = false;
		protected $_woocommerce_product_actions_removed                = false;
		protected $_woocommerce_product_shop_actions                   = array();
		protected $_woocommerce_product_shop_actions_priority_uptodate = false;
		protected $_woocommerce_product_shop_actions_removed           = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}


		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {
			$this->_init_woocommerce_hooks();

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			$hide_contents_option = yith_wcmbs_settings()->get_hide_contents();
			switch ( $hide_contents_option ) {
				case 'alternative_content':
					add_filter( 'the_content', array( $this, 'filter_content_for_membership' ), 999 );
					if ( ! YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
						add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'control_product_access_in_shop' ) );
						add_action( 'woocommerce_before_main_content', array( $this, 'control_product_access_in_product_page' ) );
					}

					// Shop Page Restriction: hide Shop for non-members if shop has restricted access.
					add_action( 'woocommerce_before_main_content', array( $this, 'alternative_content_for_shop' ), 0 );
					add_action( 'woocommerce_after_main_content', array( $this, 'alternative_content_for_shop' ), 999 );

					break;

				case 'redirect':
					if ( ! YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
						add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'control_product_access_in_shop' ) );
					}
					add_action( 'get_header', array( $this, 'redirect_if_not_have_access' ) );
					break;

				case 'all':
				default:
					if ( ! YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
						add_action( 'woocommerce_product_is_visible', array( $this, 'filter_product_is_visible' ), 10, 2 );
					}

					// Filter Post, Pages and products.
					add_action( 'pre_get_posts', array( $this, 'hide_not_allowed_posts' ) );
					add_filter( 'the_posts', array( $this, 'filter_posts' ) );
					add_filter( 'get_pages', array( $this, 'filter_posts' ) );

					// Filter nav menu.
					add_filter( 'wp_nav_menu_objects', array( $this, 'filter_nav_menu_pages' ), 10, 2 );

					// Filter next and previous post link.
					add_filter( 'get_next_post_where', array( $this, 'filter_adiacent_post_where' ), 10, 3 );
					add_filter( 'get_previous_post_where', array( $this, 'filter_adiacent_post_where' ), 10, 3 );

					// Shop Page Restriction: hide Shop for non-members if shop has restricted access.
					add_filter( 'template_include', array( $this, 'hide_shop_for_non_members' ), 99 );
					break;
			}

			add_action( 'woocommerce_add_to_cart_validation', array( $this, 'validate_product_add_to_cart' ), 10, 2 );
			add_action( 'woocommerce_checkout_registration_required', array( $this, 'checkout_registration_required' ) );
			add_action( 'woocommerce_after_my_account', array( $this, 'print_membership_history' ) );

			add_filter( 'body_class', array( $this, 'add_membership_class_to_body' ) );

			// Membership discounts.
			// Set priority to 30, to avoid issues in combination with YITH Multi Currency Switcher.
			add_filter( 'woocommerce_product_is_on_sale', array( $this, 'set_on_sale_with_membership_discount' ), 30, 2 );
			add_filter( 'woocommerce_product_get_price', array( $this, 'discount_product_price' ), 30, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'discount_product_price' ), 30, 2 );
			add_filter( 'woocommerce_variation_prices', array( $this, 'discount_variation_prices' ), 30, 2 );

			YITH_WCMBS_Messages_Manager_Frontend();

			add_filter( 'yith_proteo_myaccount_custom_icon', array( $this, 'customize_my_account_proteo_icon' ), 10, 2 );
		}

		/**
		 * Add membership CSS classes to body
		 *
		 * @param array $classes
		 *
		 * @return array
		 * @since 1.3.17
		 */
		public function add_membership_class_to_body( $classes ) {
			$member             = YITH_WCMBS_Members()->get_member( get_current_user_id() );
			$membership_classes = array();

			if ( yith_wcmbs_has_full_access() ) {
				$membership_classes[] = 'yith-wcmbs-has-full-access';
			}

			if ( $member->is_member() ) {
				$membership_classes[] = 'yith-wcmbs-member';
			}

			$plan_ids = $member->get_membership_plans();
			foreach ( $plan_ids as $plan_id ) {
				$membership_classes[] = "yith-wcmbs-member-{$plan_id}";
			}

			return array_merge( $classes, $membership_classes );
		}

		/**
		 * Print the alternative content for Shop
		 */
		public function alternative_content_for_shop() {
			$shop_page_id                    = wc_get_page_id( 'shop' );
			if ( ! YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), $shop_page_id ) ) {
				switch ( current_action() ) {
					case 'woocommerce_before_main_content':
						ob_start();
						break;
					case 'woocommerce_after_main_content':
						$shop_content = ob_get_clean();
						$alternative_content = yith_wcmbs_get_alternative_content( $shop_page_id );
						woocommerce_output_content_wrapper();
						if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
							<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
						<?php endif;
						echo yith_wcmbs_stylize_content( $alternative_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						woocommerce_output_content_wrapper_end();
						break;
				}
			}
		}

		/**
		 * Hide Shop for non-members
		 * if shop has restricted access
		 * Note: hide the shop also if alternative_content is set!
		 *
		 * @param $template
		 *
		 * @return string
		 * @since 1.2.10
		 */
		public function hide_shop_for_non_members( $template ) {
			if ( is_post_type_archive( 'product' ) &&
				 ! YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), wc_get_page_id( 'shop' ) ) &&
				 $template_404 = get_404_template()
			) {
				$template = $template_404;
			}

			return $template;
		}

		/**
		 * check for Checkout registration required for membership products
		 *
		 * @param bool $required
		 *
		 * @return bool
		 */
		public function checkout_registration_required( $required ) {
			if ( 'yes' === yith_wcmbs_settings()->get_option( 'yith-wcmbs-enable-guest-checkout' ) || $required || is_user_logged_in() ) {
				return $required;
			}

			foreach ( WC()->cart->cart_contents as $key => $item ) {
				if ( isset( $item['variation_id'] ) && 0 !== $item['variation_id'] ) {
					$prod_id = $item['variation_id'];
				} elseif ( isset( $item['product_id'] ) && 0 !== $item['product_id'] ) {
					$prod_id = $item['product_id'];
				} else {
					$prod_id = 0;
				}
				if ( $prod_id > 0 && YITH_WCMBS_Manager()->get_plan_by_membership_product( $prod_id ) ) {
					$required = true;
					break;
				}
			}

			return $required;
		}


		/**
		 * Unset posts with alternative content, if the option "hide-content" = 'alternative_content'
		 *
		 * @param array $post_ids
		 *
		 * @access public
		 * @return array
		 * @since  1.0.0
		 */
		public function unset_posts_with_alternative_content( $post_ids ) {
			$new_post_ids = array();

			if ( ! empty( $post_ids ) && yith_wcmbs_settings()->is_alternative_content_enabled() ) {
				foreach ( $post_ids as $id ) {
					$alternative_content = yith_wcmbs_get_alternative_content( $id );
					if ( empty( $alternative_content ) ) {
						$new_post_ids[] = $id;
					}
				}
			} else {
				return $post_ids;
			}

			return $new_post_ids;
		}


		/**
		 * Filter Adiacent Posts (next and previous)
		 *
		 * @param string $where
		 * @param bool   $in_same_term
		 * @param array  $excluded_terms
		 *
		 * @access public
		 * @return string
		 * @since  1.0.0
		 */
		public function filter_adiacent_post_where( $where, $in_same_term, $excluded_terms ) {
			if ( ! yith_wcmbs_has_full_access() ) {
				$plans_including_all_posts = yith_wcmbs_get_plans_including_all_posts( 'post' );
				$allowed_post_ids          = false;
				if ( ! $plans_including_all_posts || ! yith_wcmbs_user_has_membership( 0, $plans_including_all_posts ) ) {
					$allowed_post_ids = YITH_WCMBS_Manager()->get_allowed_post_ids_for_user();
					$allowed_post_ids = ! ! $allowed_post_ids ? $allowed_post_ids : array( 0 );
				}

				if ( $allowed_post_ids !== false ) {
					$where .= " AND p.ID IN (" . implode( ',', $allowed_post_ids ) . ')';
				}
			}

			return $where;
		}

		/**
		 * Filter Nav Menu Pages
		 *
		 * @param $items array
		 * @param $args  array
		 *
		 * @access public
		 * @return array
		 * @since  1.0.0
		 */
		public function filter_nav_menu_pages( $items, $args ) {
			$current_user_id      = get_current_user_id();
			$non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );

			// $non_allowed_post_ids = $this->unset_posts_with_alternative_content( $non_allowed_post_ids );

			foreach ( $items as $key => $post ) {
				if ( is_object( $post ) && isset( $post->object_id ) && in_array( absint( $post->object_id ), $non_allowed_post_ids ) ) {
					unset( $items[ $key ] );
				}
			}

			return $items;
		}

		/**
		 * Filter pre get posts Query
		 *
		 * @param $query WP_Query
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function hide_not_allowed_posts( $query ) {
			$suppress_filter = isset( $query->query['yith_wcmbs_suppress_filter'] ) ? $query->query['yith_wcmbs_suppress_filter'] : false;

			$restricted_post_types = YITH_WCMBS_Manager()->post_types;
			if ( YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
				$restricted_post_types = array_diff( $restricted_post_types, array( 'product' ) );
			}
			$post_type      = isset( $query->query['post_type'] ) ? $query->query['post_type'] : false;
			$queried_object = $query->get_queried_object();
			if ( $queried_object && ! $post_type && isset( $queried_object->post_type ) ) {
				$post_type = $queried_object->post_type;
			}

			$wc_query = $query->get( 'wc_query' );

			if ( $query->is_category || $query->is_tag || $query->is_posts_page || ( $query->is_main_query() && $query->is_home() && 'posts' === get_option( 'show_on_front', 'posts' ) ) ) {
				$post_type = 'post';
			} elseif ( $wc_query && 'product_query' === $wc_query ) {
				$post_type = 'product';
			}

			if ( false === $post_type ) {
				// Handle queries without post_type set, since the default post_type is 'post'.
				$post_type = 'post';
			}

			$is_restricted_post_type = in_array( $post_type, $restricted_post_types );

			if ( $is_restricted_post_type && ! $suppress_filter && ! yith_wcmbs_has_full_access() ) {

				if ( yith_wcmbs_settings()->is_alternative_content_enabled() ) {
					return; // I don't need to hide nothing.
				}

				if ( in_array( $post_type, array( 'post', 'product' ) ) ) {
					$plans_including_all_posts = yith_wcmbs_get_plans_including_all_posts( $post_type );

					if ( $plans_including_all_posts ) {
						if ( ! yith_wcmbs_user_has_membership( 0, $plans_including_all_posts ) ) {
							$post_in     = YITH_WCMBS_Manager()->get_allowed_post_ids_for_user( get_current_user_id() );
							$post_in     = ! ! $post_in ? $post_in : array( 0 );
							$old_post_in = $query->get( 'post__in' );
							if ( $old_post_in ) {
								$post_in = array_intersect( $post_in, $old_post_in );
								$post_in = ! ! $post_in ? $post_in : array( 0 );
							}
							$query->set( 'post__in', $post_in );
						}

						return;
					}
				}

				$post_not_in = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( get_current_user_id() );
				$post_not_in = ! ! $post_not_in ? $post_not_in : array();

				$old_post_not_in = $query->get( 'post__not_in' );
				if ( $old_post_not_in ) {
					$post_not_in = array_merge( (array) $post_not_in, (array) $old_post_not_in );
				}

				$query->set( 'post__not_in', (array) $post_not_in );

				// Remove non-allowed contents if any of them is set in 'post__in'.
				$old_post_in = $query->get( 'post__in' );
				if ( $old_post_in && $post_not_in ) {
					$post_in = array_diff( (array) $old_post_in, (array) $post_not_in );
					$post_in = ! ! $post_in ? $post_in : array( 0 );
					$query->set( 'post__in', $post_in );
				}
			}
		}

		/**
		 * Filter posts
		 *
		 * @param array $posts
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function filter_posts( $posts ) {
			$current_user_id = get_current_user_id();

			if ( 'the_posts' === current_action() && yith_wcmbs_has_full_access_to_all_posts( 'post', $current_user_id ) ) {
				return $posts;
			}

			if ( ! yith_wcmbs_settings()->is_alternative_content_enabled() && is_array( $posts ) ) {
				foreach ( $posts as $post_key => $post ) {
					if ( ! YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID, false ) ) {
						unset( $posts[ $post_key ] );
					}
				}
			}

			return array_slice( $posts, 0 );
		}


		/**
		 * If user doesn't have access to content, redirect to the link setted by admin
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function redirect_if_not_have_access() {
			global $post;
			if ( is_shop() ) {
				$post_id = get_option( 'woocommerce_shop_page_id' );
			} elseif ( yith_wcmbs_is_blog_page() ) {
				$post_id = get_option( 'page_for_posts' );
			} else {
				$post_id = $post ? $post->ID : 0;
			}

			$current_user_id = get_current_user_id();

			$user_has_no_access = ( is_single() || is_page() || yith_wcmbs_is_blog_page() ) && ! YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post_id );

			$user_has_no_access = $user_has_no_access || ( is_post_type_archive( 'product' ) && ! YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), wc_get_page_id( 'shop' ) ) );

			$user_has_no_access = apply_filters( 'yith_wcmbs_user_has_no_access', $user_has_no_access, $post );

			if ( $user_has_no_access ) {
				$redirect_link        = yith_wcmbs_settings()->get_option( 'yith-wcmbs-redirect-link' );
				$custom_redirect_link = apply_filters( 'yith_wcmbs_custom_redirect_link', get_post_meta( $post_id, '_yith_wcmbs_custom_redirect', true ), $post_id );

				if ( $custom_redirect_link ) {
					$redirect_link = $custom_redirect_link;
				}
				if ( ! empty( $redirect_link ) ) {
					if ( strpos( $redirect_link, 'http' ) !== 0 ) {
						$redirect_link = 'http://' . str_replace( 'http://', '', $redirect_link );
					}
				}
				wp_redirect( $redirect_link );
			}
		}

		/**
		 * Before add to cart a product check if user can buy it
		 * If user cannot buy the product, show a Error message
		 *
		 * @param        $passed_validation
		 * @param        $product_id
		 *
		 * @return bool
		 */
		public function validate_product_add_to_cart( $passed_validation, $product_id ) {
			if ( $passed_validation && ! YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), $product_id ) ) {
				$product       = wc_get_product( $product_id );
				$product_title = $product->get_title();

				$error_message     = sprintf( __( 'You cannot purchase "%s". To do it, you need a membership plan', 'yith-woocommerce-membership' ), $product_title );
				$error_message     = apply_filters( 'yith_wcmbs_validate_product_add_to_cart_needs_membership_error_message', $error_message, $product_title, $product_id );
				$passed_validation = false;
				wc_add_notice( $error_message, 'error' );
			}

			return $passed_validation;
		}


		/**
		 * Control the allowed access for products in shop
		 * If the user don't have access remove all WooCommerce actions that show contents in shop
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function control_product_access_in_shop() {
			global $post;
			$current_user_id = get_current_user_id();

			if ( YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID ) ) {
				$this->restore_woocommerce_product_shop_actions();
			} else {
				$this->remove_woocommerce_product_shop_actions();
			}
		}

		private function _init_woocommerce_hooks() {
			$this->_woocommerce_product_actions = apply_filters( 'yith_wcmbs_frontend_woocommerce_product_actions', array(
				'woocommerce_single_product_summary'       => array(
					'woocommerce_template_single_rating'      => 10,
					'woocommerce_template_single_price'       => 10,
					'woocommerce_template_single_excerpt'     => 20,
					'woocommerce_template_single_meta'        => 40,
					'woocommerce_template_single_sharing'     => 50,
					'woocommerce_template_single_add_to_cart' => 30,
				),
				'woocommerce_after_single_product_summary' => array(
					'woocommerce_output_product_data_tabs' => 10,
				),
				'woocommerce_simple_add_to_cart'           => array(
					'woocommerce_simple_add_to_cart' => 30,
				),
				'woocommerce_grouped_add_to_cart'          => array(
					'woocommerce_grouped_add_to_cart' => 30,
				),
				'woocommerce_variable_add_to_cart'         => array(
					'woocommerce_variable_add_to_cart' => 30,
				),
				'woocommerce_external_add_to_cart'         => array(
					'woocommerce_external_add_to_cart' => 30,
				),
				'woocommerce_single_variation'             => array(
					'woocommerce_single_variation'                    => 10,
					'woocommerce_single_variation_add_to_cart_button' => 20,
				),
			) );

			$this->_woocommerce_product_shop_actions = apply_filters( 'yith_wcmbs_frontend_woocommerce_product_shop_actions', array(
				'woocommerce_before_shop_loop_item_title'   => array(
					'woocommerce_show_product_loop_sale_flash' => 10,
				),
				'woocommerce_after_shop_loop_item_title'    => array(
					'woocommerce_template_loop_price'  => 10,
					'woocommerce_template_loop_rating' => 5,
				),
				'woocommerce_before_single_product_summary' => array(
					'woocommerce_show_product_sale_flash' => 10,
				),
				'woocommerce_after_shop_loop_item'          => array(
					'woocommerce_template_loop_add_to_cart' => 10,
				),
			) );
		}

		/**
		 * Control the allowed access for products in single product page
		 * If the user don't have access remove all WooCommerce actions that show contents in single product page
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function control_product_access_in_product_page() {
			global $post;
			$current_user_id = get_current_user_id();

			if ( is_single() ) {
				if ( YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID ) ) {
					$this->restore_woocommerce_product_actions();
				} else {
					$this->remove_woocommerce_product_actions();
				}
			}
		}


		/**
		 * Remove WooCommerce actions in Shop loop
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function remove_woocommerce_product_shop_actions() {
			$actions_to_remove = $this->_woocommerce_product_shop_actions;

			foreach ( $actions_to_remove as $hook => $functions ) {
				foreach ( $functions as $function => $default_priority ) {
					if ( $priority = has_action( $hook, $function ) ) {
						if ( ! $this->_woocommerce_product_shop_actions_priority_uptodate ) {
							$this->_woocommerce_product_shop_actions[ $hook ][ $function ] = $priority;
						}
						remove_action( $hook, $function, $priority );
					} else {
						if ( ! $this->_woocommerce_product_shop_actions_priority_uptodate ) {
							unset( $this->_woocommerce_product_shop_actions[ $hook ][ $function ] );
						}
					}
				}
			}

			$this->_woocommerce_product_shop_actions_priority_uptodate = true;

			do_action( 'yith_wcbms_remove_woocommerce_product_shop_actions' );

			$this->_woocommerce_product_shop_actions_removed = true;
		}

		/**
		 * Restore WooCommerce actions in Shop loop
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function restore_woocommerce_product_shop_actions() {
			if ( ! $this->_woocommerce_product_shop_actions_removed ) {
				return;
			}

			$actions_to_restore = $this->_woocommerce_product_shop_actions;

			foreach ( $actions_to_restore as $hook => $functions ) {
				foreach ( $functions as $function => $priority ) {
					if ( ! has_action( $hook, $function ) ) {
						add_action( $hook, $function, $priority );
					}
				}
			}

			do_action( 'yith_wcbms_restore_woocommerce_product_shop_actions' );

			$this->_woocommerce_product_shop_actions_removed = false;
		}

		/**
		 * Remove WooCommerce actions in Single Product Page
		 * and add alternative content
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function remove_woocommerce_product_actions() {
			$actions_to_remove = $this->_woocommerce_product_actions;

			foreach ( $actions_to_remove as $hook => $functions ) {
				foreach ( $functions as $function => $default_priority ) {
					if ( $priority = has_action( $hook, $function ) ) {
						if ( ! $this->_woocommerce_product_actions_priority_uptodate ) {
							$this->_woocommerce_product_actions[ $hook ][ $function ] = $priority;
						}
						remove_action( $hook, $function, $priority );
					} else {
						if ( ! $this->_woocommerce_product_actions_priority_uptodate ) {
							unset( $this->_woocommerce_product_actions[ $hook ][ $function ] );
						}
					}
				}
			}
			$this->_woocommerce_product_actions_priority_uptodate = true;

			add_action( 'woocommerce_single_product_summary', array( $this, 'get_the_alternative_content' ) );

			do_action( 'yith_wcbms_remove_woocommerce_product_actions' );

			$this->_woocommerce_product_actions_removed = true;

		}

		/**
		 * Restore WooCommerce actions in Single Product Page
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function restore_woocommerce_product_actions() {
			if ( ! $this->_woocommerce_product_actions_removed ) {
				return;
			}

			$actions_to_restore = $this->_woocommerce_product_actions;

			foreach ( $actions_to_restore as $hook => $functions ) {
				foreach ( $functions as $function => $priority ) {
					if ( ! has_action( $hook, $function ) ) {
						add_action( $hook, $function, $priority );
					}
				}
			}

			remove_action( 'woocommerce_single_product_summary', array( $this, 'get_the_alternative_content' ) );

			do_action( 'yith_wcbms_restore_woocommerce_product_actions' );

			$this->_woocommerce_product_actions_removed = false;
		}


		/**
		 * Print the alternative content for products
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function get_the_alternative_content() {
			global $post;
			$alternative_content = yith_wcmbs_get_alternative_content( $post->ID );

			echo yith_wcmbs_stylize_content( $alternative_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Filter the content in base of membership
		 * if the user don't have access, show the alternative content
		 *
		 * @param string $content the content of post, page
		 *
		 * @return string
		 * @access public
		 * @since  1.0.0
		 */
		public function filter_content_for_membership( $content ) {
			$post_id         = get_the_ID();
			$current_user_id = get_current_user_id();

			if ( ! $post_id || YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post_id ) ) {
				return $content;
			}

			$alternative_content = yith_wcmbs_get_alternative_content( $post_id, $content );

			return yith_wcmbs_stylize_content( $alternative_content );
		}

		/**
		 * Print Membership History in MyAccount
		 *
		 * @since      1.0.0
		 * @deprecated 1.4.0
		 */
		public function print_membership_history() {
		}

		/**
		 * Return the current page type (product, bundle_product, cart, checkout, ...).
		 *
		 * @return array
		 * @since 1.4.11
		 */
		protected function get_current_page_info() {
			static $info = null;
			global $post, $wp_query;
			if ( is_null( $info ) ) {

				if ( ! isset( $wp_query ) ) {
					return array();
				}

				$info = array(
					'singular'           => is_singular(),
					'product'            => is_product(),
					'my-account'         => is_account_page(),
					'membership'         => false,
					'membership-history' => false,
					'widget'             => ! ! is_active_widget( false, false, 'yith_wcmbs_messages_widget' ),
				);

				if ( is_singular() && is_a( $post, 'WP_Post' ) ) {
					if ( ! $info['product'] && has_shortcode( $post->post_content, 'product_page' ) ) {
						$info['product'] = true;
					}

					if ( has_shortcode( $post->post_content, 'membership_history' ) ) {
						$info['membership-history'] = true;
					}

					$shortcodes_with_assets = array(
						'membership_protected_links',
						'membership_items',
						'membership_download_product_links',
						'membership_history',
						'membership_downloaded_product_links',
					);

					foreach ( $shortcodes_with_assets as $shortcode ) {
						if ( has_shortcode( $post->post_content, $shortcode ) ) {
							$info['membership'] = true;
							break;
						}
					}
				}

				$info = apply_filters( 'yith_wcmbs_get_current_page_info', $info );
			}

			return $info;
		}

		/**
		 * Check if current page is one of the specified ones.
		 *
		 * @param string|array $pages The pages.
		 *
		 * @return bool
		 * @since 1.4.11
		 */
		protected function current_page_is( $pages = array() ) {
			$pages         = (array) $pages;
			$current_pages = array_keys( array_filter( $this->get_current_page_info() ) );

			return ! ! array_intersect( $current_pages, $pages );
		}

		/**
		 * Return assets to enqueue
		 *
		 * @return array
		 * @since 1.4.11
		 */
		protected function get_assets() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$assets = array(
				'styles'  => array(
					'yith-wcmbs-frontent-styles' => array(
						'path'  => YITH_WCMBS_ASSETS_URL . '/css/frontend.css',
						'where' => array(
							'singular', // Protected links in posts, pages, ...
							'product', // Protected links and download buttons in products.
							'my-account', // Membership history.
							'membership', // All membership pages.
							'membership-history', // Membership history (if set through shortcode in other pages).
							'widget', // Messages widget enabled.
						),
					),
					'dashicons'                  => array(
						'where' => array( 'my-account', 'membership-history' ),
					),
				),
				'scripts' => array(
					'yith_wcmbs_frontend_js' => array(
						'path'     => YITH_WCMBS_ASSETS_URL . '/js/frontend_premium' . $suffix . '.js',
						'deps'     => apply_filters( 'yith_wcmbs_frontend_js_deps', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-blockui' ) ),
						'where'    => false, // Register it only; it'll be manually enqueued.
						'localize' => array(
							'yith_wcmbs_frontend' => array(
								'ajax_url' => admin_url( 'admin-ajax.php' ),
								'user_id'  => get_current_user_id(),
							),
						),
					),
				),
			);

			return apply_filters( 'yith_wcpb_get_frontend_assets', $assets );
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			$assets  = $this->get_assets();
			$styles  = isset( $assets['styles'] ) ? $assets['styles'] : array();
			$scripts = isset( $assets['scripts'] ) ? $assets['scripts'] : array();

			foreach ( $styles as $handle => $style ) {
				$defaults = array(
					'version' => YITH_WCMBS_VERSION,
					'deps'    => array(),
					'path'    => false,
					'where'   => false,
				);
				$style    = wp_parse_args( $style, $defaults );

				if ( $style['path'] ) {
					wp_register_style( $handle, $style['path'], $style['deps'], $style['version'] );
				}

				if ( true === $style['where'] || ( ! ! $style['where'] && $this->current_page_is( $style['where'] ) ) ) {
					wp_enqueue_style( $handle );
				}
			}

			foreach ( $scripts as $handle => $script ) {
				$defaults = array(
					'version'  => YITH_WCMBS_VERSION,
					'deps'     => array(),
					'path'     => false,
					'where'    => false,
					'footer'   => true,
					'localize' => false,
				);
				$script   = wp_parse_args( $script, $defaults );

				if ( $script['path'] ) {
					wp_register_script( $handle, $script['path'], $script['deps'], $style['version'], $script['footer'] );
				}

				if ( $script['localize'] ) {
					foreach ( $script['localize'] as $object_name => $object ) {
						wp_localize_script( $handle, $object_name, $object );
					}
				}

				if ( true === $script['where'] || ( ! ! $script['where'] && $this->current_page_is( $script['where'] ) ) ) {
					wp_enqueue_script( $handle );
				}
			}
		}

		/**
		 * Late enqueue scripts and styles.
		 *
		 * @param array $types The types.
		 *
		 * @since 1.4.11
		 */
		public function late_enqueue_assets( $types = array() ) {
			$types = (array) $types;

			$assets = $this->get_assets();

			$styles  = isset( $assets['styles'] ) ? $assets['styles'] : array();
			$scripts = isset( $assets['scripts'] ) ? $assets['scripts'] : array();

			foreach ( $styles as $handle => $style ) {
				$where = isset( $style['where'] ) ? $style['where'] : false;

				if ( true === $style['where'] || ( ! ! $style['where'] && ! ! array_intersect( $where, $types ) ) ) {
					wp_enqueue_style( $handle );
				}
			}

			foreach ( $scripts as $handle => $script ) {
				$where = isset( $script['where'] ) ? $script['where'] : false;

				if ( true === $script['where'] || ( ! ! $script['where'] && ! ! array_intersect( $where, $types ) ) ) {
					wp_enqueue_script( $handle );
				}
			}
		}


		/**
		 * get the custom css for plans
		 *
		 * @access     public
		 * @since      1.0.0
		 * @deprecated 1.4.0 | Plan List Style is not useful
		 */
		public function get_inline_css_for_plans() {
			return '';
		}

		/**
		 * Discount price
		 *
		 * @param string $price The price
		 *
		 * @return float|int|string
		 */
		private function discount_price( $price ) {
			$discount = yith_wcmbs_get_user_membership_discount();
			if ( $discount ) {
				$price = (float) $price * ( 1 - ( absint( $discount ) / 100 ) );
			}

			return $price;
		}

		/**
		 * Discount product price
		 *
		 * @param string     $price
		 * @param WC_Product $product
		 *
		 * @return float|int
		 */
		public function discount_product_price( $price, $product ) {
			return $this->discount_price( $price );
		}

		/**
		 * Discount product price
		 *
		 * @param array      $prices  The prices.
		 * @param WC_Product $product The Variable Product.
		 *
		 * @return array
		 */
		public function discount_variation_prices( $prices, $product ) {
			foreach ( $prices as $key => &$_prices ) {
				if ( 'price' === $key ) {
					$_prices = array_map( array( $this, 'discount_price' ), $_prices );
				}
			}

			return $prices;
		}

		/**
		 * Set products on sale if the current member has a discount
		 *
		 * @param bool       $on_sale
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function set_on_sale_with_membership_discount( $on_sale, $product ) {
			$discount = yith_wcmbs_get_user_membership_discount();
			if ( $discount ) {
				$on_sale = true;
			}

			return $on_sale;
		}

		/**
		 * Change the icon in "My Account" page of Proteo theme.
		 *
		 * @param string $icon     Icon.
		 * @param string $endpoint Endpoint.
		 *
		 * @return string
		 * @since 1.4.0
		 */
		public function customize_my_account_proteo_icon( $icon, $endpoint ) {

			if ( $endpoint === YITH_WCMBS()->endpoints->get_endpoint( 'memberships' ) ) {
				ob_start();
				include YITH_WCMBS_ASSETS_PATH . '/icons/membership.svg';
				$icon = '<span class="yith-proteo-myaccount-icons yith-wcmbs-proteo-myaccount-icon">' . ob_get_clean() . '</span>';
			}

			return $icon;
		}

		/**
		 * Filter the "is_visible" property of the product.
		 * Useful, for example, to hide membership-products set as up-sells.
		 *
		 * @param bool $visible    True if the product is visible.
		 * @param int  $product_id The product ID.
		 *
		 * @since 1.4.10
		 */
		public function filter_product_is_visible( $visible, $product_id ) {
			if ( $visible && ! yith_wcmbs_has_full_access() && ! yith_wcmbs_settings()->is_alternative_content_enabled() ) {
				$visible = YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), $product_id, true );
			}

			return $visible;
		}
	}
}
/**
 * Unique access to instance of YITH_WCMBS_Frontend class
 *
 * @return YITH_WCMBS_Frontend
 * @since 1.0.0
 */
function YITH_WCMBS_Frontend() {
	return YITH_WCMBS_Frontend::get_instance();
}