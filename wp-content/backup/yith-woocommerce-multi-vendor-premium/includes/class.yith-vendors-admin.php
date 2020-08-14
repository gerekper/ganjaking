<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendors_Admin
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Vendors_Admin' ) ) {

	class YITH_Vendors_Admin {

		/**
		 * @var string The taxonomy name
		 */
		protected $_taxonomy_name = '';

		/**
		 * @var YIT_Plugin_Panel_Woocommerce instance
		 */
		protected $_panel;

		/**
		 * @var YIT_Plugin_Panel_Woocommerce instance
		 */
		protected $_panel_page = 'yith_wpv_panel';

		/**
		 * @var string Official plugin documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-multi-vendor/';

		/**
		 * @var string Official plugin landing page
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-multi-vendor/';

		/**
		 * @var string Official plugin landing page
		 */
		protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-multi-vendor/vendor/medina-store/';

		/**
		 * @var string Vendor options role
		 */
		protected $_vendor_role = 'manage_vendor_store';

		/**
		 * Construct
		 */
		public function __construct() {
			$this->_taxonomy_name = YITH_Vendors()->get_taxonomy_name();

			/* Add Admin Body Class */
			add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

			/* Panel Settings */
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			/* Dashboard Menu */
			add_action( 'admin_menu', array( $this, 'add_new_link_check' ) );

			/* Plugin Information */
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WPV_PATH . '/' . basename( YITH_WPV_FILE ) ), array(
				$this,
				'action_links'
			) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_action( 'yith_wc_multi_vendor_premium_tab', array( $this, 'show_premium_tab' ) );

			/* Taxonomy management */
			add_action( $this->_taxonomy_name . '_add_form_fields', array( $this, 'add_taxonomy_fields' ), 1, 1 );
			add_action( $this->_taxonomy_name . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ), 10, 1 );
			add_filter( 'pre_insert_term', array( $this, 'check_duplicate_term_name' ), 10, 2 );
			add_filter( 'edit_terms', array( $this, 'check_duplicate_term_name' ), 10, 2 );
			add_action( 'edited_' . $this->_taxonomy_name, array( $this, 'save_taxonomy_fields' ), 10, 2 );
			add_action( 'created_' . $this->_taxonomy_name, array( $this, 'save_taxonomy_fields' ), 10, 2 );
			add_action( 'pre_delete_term', array( $this, 'remove_vendor_data' ), 10, 2 );

			/* Support to YITH Themes FW 2.0 */
			add_filter( 'yit_layouts_taxonomies_list', array( $this, 'add_taxonomy_to_layouts' ) );

			/* Taxonomy Table Management */
			add_filter( "bulk_actions-edit-{$this->_taxonomy_name}", '__return_empty_array' );

			/* Allow html in taxonomy descriptions */
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
			remove_filter( 'term_description', 'wp_kses_data' );

			/* WooCommerce */
			add_action( 'pre_user_query', array( $this, 'json_search_customer_name' ), 15 );
			add_filter( 'admin_url', array( $this, 'admin_url' ) );

			/* Vendor products management */
			add_filter( 'request', array( $this, 'filter_list' ) );
			add_filter( 'wp_count_posts', 'YITH_Vendors_Admin::vendor_count_posts', 10, 3 );
			add_action( 'save_post', array( $this, 'add_vendor_taxonomy_to_product' ), 10, 2 );
			add_action( 'current_screen', array( $this, 'disabled_manage_other_vendors_posts' ) );

			/* Add new attribute by Vendor */
			add_action( 'wp_ajax_woocommerce_add_new_attribute', 'YITH_Vendors_Admin::add_new_attribute', 5 );

			/* Grouped Products */
			add_action( 'pre_get_posts', array( $this, 'filter_vendor_linked_products' ), 10, 1 );

			/* Vendor media management */
			add_filter( 'ajax_query_attachments_args', array( $this, 'filter_vendor_media' ), 10, 1 );

			/* Vendor menu */
			add_action( 'admin_menu', array( $this, 'menu_items' ) );
			add_action( 'admin_menu', array( $this, 'remove_media_page' ) );
			add_action( 'admin_menu', array( $this, 'remove_dashboard_widgets' ) );
			add_action( 'admin_init', array( YITH_Vendors(), 'remove_wp_bar_admin_menu' ) );
			add_action( 'admin_bar_menu', array( $this, 'show_admin_bar_visit_store' ), 31 );
			add_filter( 'map_meta_cap', array( $this, 'remove_jetpack_menu_page' ), 10, 4 );
			add_action( 'wp_before_admin_bar_render', array( $this, 'remove_yoast_seo_ab_icon_admin_bar' ) );

			/* Vendor information management */
			add_action( 'admin_action_yith_admin_save_fields', array( $this, 'save_taxonomy_fields' ) );

			/* Prevent WooCommerce Access Admin */
			add_filter( 'woocommerce_prevent_admin_access', array( $this, 'prevent_admin_access' ) );

			/* Pending Product Notifier */
			add_action( 'admin_menu', array( $this, 'products_to_approve' ) );

			/* Order management */
			add_filter( 'request', array( $this, 'filter_order_list' ), 10, 1 );
			add_action( 'admin_head', array( $this, 'count_processing_order' ), 5 );

			/* Add Vendors Page Link in WP Dashboard */
			add_action( 'admin_menu', array( $this, 'admin_vendor_link' ) );

			/* Custom manage users columns. */
			add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );

			/* Manage Vendor Caps */
			add_action( 'yit_panel_wc_before_update', array( $this, 'manage_caps' ) );

			/* Regenerate Permalink after panel update */
			add_action( 'yit_panel_wc_before_update', array( $this, 'check_rewrite_rules' ) );
			add_action( 'yit_panel_wc_before_reset', array( $this, 'check_rewrite_rules' ) );

			/* Essential Grid Support */
			add_action( 'add_meta_boxes', array( $this, 'remove_ess_grid_metabox' ), 20 );

			/* WooCommerce Status Dashboard Widget */
			add_filter( 'woocommerce_dashboard_status_widget_top_seller_query', array(
				$this,
				'dashboard_status_widget_top_seller_query'
			) );

			/* Search Vendor admins for vendor */
			add_action( 'wp_ajax_woocommerce_json_search_customers', 'YITH_Vendors_Admin::json_search_admins', 5 );

			/* Tiny MCE Editor Hack */
			add_action( 'after_wp_tiny_mce', 'YITH_Vendors_Admin::remove_add_existing_content' );

			add_action( 'admin_notices', array( $this, 'add_woocommerce_admin_notice' ) );

			/* === Edit Product -> Remove 3rd-party metaboxes === */
			add_action( 'add_meta_boxes_product', array( $this, 'remove_meta_boxes' ), 99 );

			/* === Add Filter products by Vendor === */
			add_filter( 'woocommerce_products_admin_list_table_filters', array(
				$this,
				'products_admin_list_table_filters'
			) );

			/**
			 * Skip duplicate term name check in vendor name translation
			 */
			add_action( 'wp_ajax_wpml_save_term', array( $this, 'wpml_save_term_action' ), 5 );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters( 'yith_vendors_admin_tabs', array(
					'commissions'      => __( 'Commissions', 'yith-woocommerce-product-vendors' ),
					'vendors'          => YITH_Vendors()->get_plural_label( 'ucfirst' ),
					'accounts-privacy' => __( 'Accounts & Privacy', 'yith-woocommerce-product-vendors' ),
					'premium'          => __( 'Premium Version', 'yith-woocommerce-product-vendors' ),
				)
			);

			if ( ! YITH_Vendors()->is_wp_4_9_6_or_greater ) {
				unset( $admin_tabs['accounts-privacy'] );
			}
			$menu_title = $page_title = 'Multi Vendor';
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => $page_title,
				'menu_title'       => $menu_title,
				'capability'       => apply_filters( 'yit_wcmv_plugin_options_capability', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WPV_PATH . 'plugin-options',
				'links'            => $this->get_sidebar_link()
			);


			/* === Fixed: not updated theme/old plugin framework  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WPV_PATH . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Enqueue Style and Scripts
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function enqueue_scripts() {
			global $pagenow;
			$vendor = yith_get_vendor( 'current', 'user' );
			wp_register_script( 'yith-wpv-tax-menu', YITH_WPV_ASSETS_URL . 'js/tax-admin-menu.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'yith-wpv-admin', YITH_WPV_ASSETS_URL . 'js/admin.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_style( 'yith-wc-product-vendors-admin', YITH_WPV_ASSETS_URL . 'css/admin.css' );
			wp_localize_script( 'yith-wpv-admin', 'yith_vendors_caps', array( 'reviews' => get_option( 'yith_wpv_vendors_option_review_management', 'no' ) ) );

			if ( $this->is_vendor_dashboard() || $this->is_vendor_tax_page() ) {
				wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_enqueue_script( 'wc-enhanced-select' );
			}

			/* === Admin Menu Hack === */
			if ( $this->is_vendor_tax_page() ) {
				wp_enqueue_script( 'yith-wpv-tax-menu' );

				$css = '#adminmenu li#menu-posts-product > a:after{display:none;}
                        #adminmenu .wp-has-current-submenu .wp-submenu{display: none;}
                        #adminmenu li#menu-posts-product:not(.yith-wcmv-tax-menu) > a {color: #eee; background-color: transparent;}
                        #adminmenu #menu-posts-product:not(.yith-wcmv-tax-menu) .menu-icon-post div.wp-menu-image:before {color: #eee !important;}';
				wp_add_inline_style( 'yith-wc-product-vendors-admin', $css );

				if ( ! empty( $_GET['tag_ID'] ) ) {
					wp_enqueue_media();
				}
			}

			/* === Add Product Page === */
			if ( $vendor->is_valid() && $vendor->has_limited_access() && 'edit.php' === $pagenow && ! empty( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) {
				wp_add_inline_style( 'yith-wc-product-vendors-admin', '.vendor_limited_access .woocommerce-BlankState a.woocommerce-BlankState-cta.button:not(.button-primary){display:none !important;}' );
				wp_add_inline_script( 'yith-wpv-admin', "jQuery( '.woocommerce-BlankState' ).find( 'a.woocommerce-BlankState-cta.button' ).not( '.button-primary'  ).remove();" );
			}
		}

		public function filter_list( $request ) {
			//TODO: Move the upload part to premium class
			global $typenow, $pagenow;

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( is_admin() && ! $vendor->is_super_user() && $vendor->is_user_admin() ) {
				if ( 'product' == $typenow ) {
					$request = $this->filter_product_list( $request, $vendor );
				}

				elseif ( 'upload.php' == $pagenow ) {
					$request = $this->filter_vendor_media( $request, $vendor );
				}
			}

			return $request;
		}

		/**
		 * Only show vendor's products
		 *
		 * @param arr $request Current request
		 *
		 * @return arr          Modified request
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @since  1.0
		 */
		public function filter_product_list( $request, $vendor ) {
			global $typenow;

			$slug = $vendor->slug;
			/* WPML Support */
			global $sitepress;
			if ( isset( $sitepress ) ) {
				$vendor_id   = yit_wpml_object_id( $vendor->id, YITH_Vendors()->get_taxonomy_name(), true );
				$wpml_vendor = get_term_by( 'id', $vendor_id, $vendor->term->taxonomy );
				$slug        = $wpml_vendor->slug;
			}
			$request[ $vendor->term->taxonomy ] = $slug;

			return apply_filters( "yith_wcmv_product_request", $request );

		}

		/**
		 * Filter the post count for vendor
		 *
		 * @param $counts   The post count
		 * @param $type     Post type
		 * @param $perm     The read permission
		 *
		 * @return arr  Modified request
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @since    1.0
		 * @use      wp_post_count action
		 */
		public static function vendor_count_posts( $counts, $type, $perm ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( ! $vendor->is_valid() || ! in_array( $type, array(
					'product',
					'shop_order'
				) ) || $vendor->is_super_user() || ! $vendor->is_user_admin() ) {
				if ( 'shop_order' == $type && $vendor->is_super_user() ) {
					global $wpdb;

					if ( ! post_type_exists( $type ) ) {
						return new stdClass;
					}

					$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_parent = 0";
					if ( 'readable' == $perm && is_user_logged_in() ) {
						$post_type_object = get_post_type_object( $type );
						if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
							$query .= $wpdb->prepare( " AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
								get_current_user_id()
							);
						}
					}
					$query .= ' GROUP BY post_status';

					$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
					$counts  = array_fill_keys( get_post_stati(), 0 );

					foreach ( $results as $row ) {
						if ( array_key_exists( $row['post_status'], $counts ) ) {
							$counts[ $row['post_status'] ] = $row['num_posts'];
						}
					}

					$counts = (object) $counts;
				}

				return $counts;
			}

			/**
			 * Get a list of post statuses.
			 */
			$stati      = get_post_stati();
			$new_counts = new stdClass();

			// Update count object
			foreach ( $stati as $status ) {
				$posts = '';
				if ( 'product' == $type ) {
					$posts = $vendor->get_products( "post_status=$status" );
				} elseif ( 'shop_order' == $type ) {
					$posts = $vendor->get_orders( 'suborder', $status );
				}

				$new_counts->$status = count( $posts );
			}

			$counts = $new_counts;

			return $counts;
		}

		/**
		 * Add vendor to product
		 *
		 * @param int $post_id Product ID
		 *
		 * @return      void
		 * @author      Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since       1.0
		 * @use         save_post action
		 */
		public function add_vendor_taxonomy_to_product( $post_id, $post ) {
			$current_vendor = yith_get_vendor( 'current', 'user' );

			if ( apply_filters( 'yith_wcmv_add_vendor_taxonomy_to_product', $post, $current_vendor, 'product' == $post->post_type && current_user_can( 'edit_post', $post_id ) ) ) {
				if ( ! empty( $_REQUEST['bulk_edit'] ) ) {
					$vendor_taxonomy_name = YITH_Vendors()->get_taxonomy_name();
					if ( isset( $_REQUEST['tax_input'][ $vendor_taxonomy_name ] ) ) {
						if ( $current_vendor->is_super_user() ) {
							$vendor_slug = is_array( $_REQUEST['tax_input'][ $vendor_taxonomy_name ] ) ? array_shift( $_REQUEST['tax_input'][ $vendor_taxonomy_name ] ) : $_REQUEST['tax_input'][ $vendor_taxonomy_name ];
							$new_vendor  = yith_get_vendor( $vendor_slug, 'vendor' );
							$old_vendor  = yith_get_vendor( $post->ID, 'product' );
							if ( $old_vendor->is_valid() && $new_vendor->id != $old_vendor->id ) {
								wp_remove_object_terms( $post->ID, $old_vendor->term->slug, $vendor_taxonomy_name );
							}

							if ( $new_vendor->is_valid() ) {
								wp_set_object_terms( $post->ID, $new_vendor->term->slug, $vendor_taxonomy_name, false );
							}
						}
					}
				} else {
					$object_id = $terms = $taxonomy = '';
					if ( $current_vendor->has_limited_access() ) {
						$object_id = $post_id;
						$terms     = $current_vendor->term->slug;
						$taxonomy  = $current_vendor->term->taxonomy;
					} elseif ( $current_vendor->is_super_user() && ! empty( $_POST['tax_input'][ YITH_Vendors()->get_taxonomy_name() ] ) ) {
						$vendor_id = is_array( $_POST['tax_input'][ YITH_Vendors()->get_taxonomy_name() ] ) ? array_shift( $_POST['tax_input'][ YITH_Vendors()->get_taxonomy_name() ] ) : $_POST['tax_input'][ YITH_Vendors()->get_taxonomy_name() ];
						$vendor    = yith_get_vendor( $vendor_id, 'vendor' );
						if ( $vendor->is_valid() ) {
							$object_id = $post_id;
							$terms     = $vendor->term->slug;
							$taxonomy  = $vendor->term->taxonomy;
						}
					}

					wp_set_object_terms( $object_id, $terms, $taxonomy, false );
				}

				WC()->mailer();
				do_action( 'yith_wcmv_save_post_product', $post_id, $post, $current_vendor );
			}
		}

		/**
		 * Restrict vendors from editing other vendors' posts
		 *
		 * @return      void
		 * @author      Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since       1.0
		 * @use         current_screen filter
		 */
		public function disabled_manage_other_vendors_posts() {
			if ( isset( $_POST['post_ID'] ) || ! isset( $_GET['post'] ) ) {
				return;
			}

			$post_id = $product_vendor = 0;
			$vendor  = yith_get_vendor( 'current', 'user' );

			/* WPML Support */
			$default_language = function_exists( 'wpml_get_default_language' ) ? wpml_get_default_language() : null;
			$post             = get_post( $_GET['post'] );
			$is_seller        = $vendor->is_valid() && $vendor->has_limited_access();

			if ( $post && 'product' == $post->post_type ) {
				$post_id        = yit_wpml_object_id( $_GET['post'], 'product', true, $default_language );
				$product_vendor = yith_get_vendor( $post_id, 'product' ); // If false, the product hasn't any vendor set
				if ( $is_seller && false !== $product_vendor && $vendor->id != $product_vendor->id ) {
					wp_die( sprintf( __( 'You do not have permission to edit this product. %1$sClick here to view and edit your products%2$s.', 'yith-woocommerce-product-vendors' ), '<a href="' . esc_url( 'edit.php?post_type=product' ) . '">', '</a>' ) );
				}
			}

			if ( $post && 'shop_order' == $post->post_type ) {
				$vendor_orders = array_merge( $vendor->get_orders( 'suborder' ), $vendor->get_orders( 'all' ) );
				if ( $is_seller && ! in_array( $post->ID, $vendor_orders ) ) {
					wp_die( sprintf( __( 'You do not have permission to edit this order. %1$sClick here to view and edit your orders%2$s.', 'yith-woocommerce-product-vendors' ), '<a href="' . esc_url( 'edit.php?post_type=shop_order' ) . '">', '</a>' ) );
				}
			}
		}

		/**
		 * Filter the vendor media library
		 *
		 * @param array $query The Query
		 *
		 * @return array         Modified views
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0
		 */
		public function filter_vendor_media( $query = array(), $vendor = false ) {
			if ( ! $vendor ) {
				$vendor = yith_get_vendor( 'current', 'user' );
			}

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$vendor_admin_ids = $vendor->get_admins();

				if ( ! empty( $vendor_admin_ids ) ) {
					$query['author__in'] = $vendor_admin_ids;
				}

			}

			return apply_filters( "yith_wcmv_upload.php_request", $query );
		}

		/**
		 * Add items to dashboard menu
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function menu_items() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() || ! $vendor->is_owner() ) {
				return;
			}

			$menu_title = $page_title = sprintf(
				esc_html_x( '%s Profile', 'Menu item: Vendor Profile. %s means "Vendor"', 'yith-woocommerce-product-vendors' ),
                esc_html( YITH_Vendors()->get_singular_label( 'ucfirst' ) )
                );

			$menus = apply_filters( 'yith_wc_product_vendors_details_menu_items', array(
				'vendor_details' => array(
					'page_title' => $page_title,
					'menu_title' => $menu_title,
					'capability' => 'edit_products',
					'menu_slug'  => 'yith_vendor_details',
					'function'   => array( $this, 'admin_details_page' ),
					'icon'       => 'dashicons-id-alt',
					'position'   => 56
				),
			) );

			foreach ( $menus as $menu_args ) {
				extract( $menu_args );
				add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position );
			}
		}

		/**
		 * Remove upload.php page
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function remove_media_page() {
			/* Remove Media Library */
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				remove_menu_page( 'upload.php' );
			}
		}

		/**
		 * Get vendor admin template
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function admin_details_page() {
			$vendor = yith_get_vendor( 'current', 'user' );
			yith_wcpv_get_template( 'vendor-admin', array( 'vendor' => $vendor ), 'admin' );
		}

		/**
		 * Update vendor information
		 *
		 * @param $query object The query object
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 * @fire   product_vendors_details_fields_save action
		 */
		public function filter_vendor_linked_products( $query ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			$action = isset( $_GET['action'] ) ? $_GET['action'] : false;

			if ( $vendor->is_valid() && $vendor->has_limited_access() && ( 'woocommerce_json_search_products' == $action || 'woocommerce_json_search_grouped_products' == $action ) ) {
				$query_args = $vendor->get_query_products_args();
				$query->set( 'tax_query', $query_args['tax_query'] );
			}
		}

		/**
		 * Add fields to vendor taxonomy (add new vendor screen)
		 *
		 * @param str $taxonomy Current taxonomy name
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_taxonomy_fields( $taxonomy ) {
			$args = array(
				'step'            => apply_filters( 'yith_wcmv_commissions_step', '0.1' ),
				'commission'      => YITH_Vendors()->get_base_commission(),
				'tax_label'       => get_option( 'yith_vat_label', __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ) ),
				'shop_owner_args' => array(
					'class'            => 'wc-customer-search',
					'id'               => 'key_user',
					'name'             => 'yith_vendor_data[owner]',
					'data-placeholder' => esc_attr__( 'Search for a customer&hellip;', 'yith-woocommerce-product-vendors' ),
					'data-allow_clear' => true,
					'data-selected'    => '',
					'value'            => ''
				)
			);

			yith_wcpv_get_template( 'add-product-vendors-taxonomy', $args, 'admin' );
		}

		/**
		 * Edit fields to vendor taxonomy
		 *
		 * @param WP_Post $vendor Current vendor information
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function edit_taxonomy_fields( $vendor ) {

			//Get Vendor Owner & Admins
			$vendor        = yith_get_vendor( $vendor );
			$owner         = get_userdata( $vendor->get_owner() );
			$owner_display = $owner instanceof WP_User ? $owner->display_name . '(#' . $owner->ID . ' - ' . $owner->user_email . ')' : '';
			$vendor_owner  = ! empty( $owner_display ) ? esc_html( $owner->display_name ) . ' (#' . absint( $owner->ID ) . ' &ndash; ' . esc_html( $owner->user_email ) . ')' : '';
			$owner_id      = $owner instanceof WP_User ? $owner->ID : '';

			$args = apply_filters( 'yith_edit_taxonomy_args', array(
					'step'             => apply_filters( 'yith_wcmv_commissions_step', '0.1' ),
					'vendor'           => $vendor,
					'shop_owner_args'  => array(
						'class'            => 'wc-customer-search',
						'id'               => 'key_user',
						'name'             => 'yith_vendor_data[owner]',
						'data-placeholder' => esc_attr__( 'Search for a customer&hellip;', 'yith-woocommerce-product-vendors' ),
						'data-allow_clear' => true,
						'data-selected'    => YITH_Vendors()->is_wc_2_7_or_greather ? array( $owner_id => $vendor_owner ) : $vendor_owner,
						'value'            => $owner_id
					),
					'shop_admins_args' => array(
						'class'            => 'wc-customer-search',
						'id'               => 'yith_vendor_admins',
						'name'             => 'yith_vendor_data[admins]',
						'data-placeholder' => esc_attr__( 'Search for a shop admins&hellip;', 'yith-woocommerce-product-vendors' ),
						'data-allow_clear' => true,
						'data-selected'    => $this->format_vendor_admins_for_select2( $vendor ),
						'data-multiple'    => true,
						'value'            => implode( ',', array_diff( $vendor->get_admins(), array( $owner_id ) ) )
					),
				)
			);

			yith_wcpv_get_template( 'edit-product-vendors-taxonomy', $args, 'admin' );
		}

		/**
		 * Check for duplicate vendor name
		 *
		 * @param $term     string The term name
		 * @param $taxonomy string The taxonomy name
		 *
		 * @return mixed term object | WP_Error
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @since    1.0
		 */
		public function check_duplicate_term_name( $term, $taxonomy ) {

		    if( apply_filters( 'yith_wcmv_skip_check_duplicate_term_name', false ) ||  ! empty( $_REQUEST['icl_translation_of'] ) ){
			    return $term;
            }

			if ( $this->_taxonomy_name != $taxonomy ) {
				return $term;
			}

			if ( 'edit_terms' == current_action() && isset( $_POST['name'] ) ) {
				$duplicate = get_term_by( 'name', $_POST['name'], $taxonomy );

				/**
				 * If the vendor name exist -> check if is the edited item or not
				 */
				if ( $duplicate && $duplicate->term_id == $term ) {
					$duplicate = false;
				}

				$message   = __( 'A vendor with this name already exists.', 'yith-woocommerce-product-vendors' );
				$title     = __( 'Vendor name already exists', 'yith-woocommerce-product-vendors' );
				$back_link = admin_url( 'edit-tag.php' );

				$back_link = esc_url( add_query_arg( $back_link, array(
					'action'    => 'edit',
					'taxonomy'  => $_POST['taxonomy'],
					'tag_ID'    => $_POST['tag_ID'],
					'post_type' => 'product'
				) ) );

				$args = array( 'back_link' => $back_link );

				return ! $duplicate ? $term : wp_die( $message, $title, $args );

			} else {
				$to_return = $term;

				// If the user try to save a translation don't need to check for duplicated term name
				$duplicate = get_term_by( 'name', $term, $taxonomy );

				if ( $duplicate ) {
					$to_return = new WP_Error( 'term_exists', __( 'A vendor with this name already exists.', 'yith-woocommerce-product-vendors' ), $duplicate );
				}

				if ( ! empty( $_POST['yith_vendor_data']['owner'] ) ) {
					$vendor = yith_get_vendor( $_POST['yith_vendor_data']['owner'], 'user' );
					if ( $vendor->is_valid() ) {
						$to_return = new WP_Error( 'owner_exists', __( "You can't associate more vendor shops with the same shop owner.", 'yith-woocommerce-product-vendors' ), $duplicate );
					}
				}

				return $to_return;
			}
		}

		/**
		 * Save extra taxonomy fields for product vendors taxonomy
		 *
		 * @param $vendor_id string The vendor id
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @since  1.0
		 */
		public function save_taxonomy_fields( $vendor_id = 0 ) {

			if ( ! isset( $_POST['yith_vendor_data'] ) ) {
				return;
			}

			$is_new = strpos( current_action(), 'created_' ) !== false;

			// if not is set $vendor_id check if there is the update_vendor_id field inside the $_POST array
			if ( empty( $vendor_id ) && isset( $_POST['update_vendor_id'] ) ) {
				$vendor_id = $_POST['update_vendor_id'];
			}

			$vendor = yith_get_vendor( $vendor_id );

			if ( ! $vendor->is_valid() ) {
				return;
			}

			$vendor_name = '';

			if ( isset( $_POST['tag-name'] ) ) {
				$vendor_name = $_POST['tag-name'];
			} elseif ( isset( $_POST['name'] ) ) {
				$vendor_name = $_POST['name'];
			}

			$check = $this->check_duplicate_term_name( $vendor_name, YITH_Vendors()->get_taxonomy_name() );

			if ( is_wp_error( $check ) && ! empty( $check->error_data['term_exists'] ) && $check->error_data['term_exists']->term_id != $vendor->id ) {
				$show_admin_notices = 'name_exists';
			} else {
				$post_value     = $_POST['yith_vendor_data'];
				$usermeta_owner = YITH_Vendors()->get_user_meta_owner();
				$usermeta_admin = YITH_Vendors()->get_user_meta_key();

				if ( ! $vendor->has_limited_access() ) {
					foreach ( apply_filters( 'yith_wpv_save_checkboxes', array( 'enable_selling' ), $vendor->has_limited_access() ) as $key ) {
						! isset( $post_value[ $key ] ) && $post_value[ $key ] = 'no';
					}
				} else {
					foreach ( apply_filters( 'yith_wpv_save_checkboxes', array(), $vendor->has_limited_access() ) as $key ) {
						! isset( $post_value[ $key ] ) && $post_value[ $key ] = 'no';
					}
				}

				// set values
				$skip_wc_clean_for = apply_filters( 'yith_wcmv_skip_wc_clean_for_fields_array', array(
					'description',
					'shipping_policy',
					'shipping_refund_policy'
				) );
				foreach ( $post_value as $key => $value ) {
					if ( in_array( $key, $skip_wc_clean_for ) ) {
						$vendor->$key = $value;
					} else {
						$vendor->$key = ! is_array( $value ) ? wc_clean( $value ) : $value;
					}
				}

				// add vendor registrantion date
				if ( $is_new ) {
					$vendor->registration_date     = current_time( 'mysql' );
					$vendor->registration_date_gmt = current_time( 'mysql', 1 );
				}

				// Get current vendor admins and owner
				$admins = $vendor->get_admins();
				$owner  = $vendor->get_owner();

				if ( empty( $post_value['admins'] ) && 'admin_action_yith_admin_save_fields' == current_action() ) {
					//If the vendor save a tab different of vendor settings
					$post_value['admins'] = $admins;
				}

				// Remove all current admins (user meta)
				foreach ( $admins as $user_id ) {
					$user = get_user_by( 'id', $user_id );
					delete_user_meta( $user_id, $usermeta_admin );
					$user->remove_role( YITH_Vendors()->get_role_name() );
					$user->add_role( 'customer' );
				}

				// Remove current owner and update it
				if ( ! empty( $post_value['owner'] ) && $owner != $post_value['owner'] ) {
					delete_user_meta( $owner, $usermeta_owner );
					update_user_meta( intval( $post_value['owner'] ), $usermeta_owner, $vendor->id );
					$owner = intval( $post_value['owner'] );
				} // Vendor with noi owner

                elseif ( empty( $post_value['owner'] ) && 'admin_action_yith_admin_save_fields' != current_action() && $vendor->is_super_user() ) {
					delete_user_meta( $owner, $usermeta_owner );
					delete_user_meta( $owner, $usermeta_admin );
					$user = get_user_by( 'id', $owner );
					if ( $user instanceof WP_User ) {
						$user->remove_role( YITH_Vendors()->get_role_name() );
						$user->add_role( 'customer' );
					}
					$vendor->owner = $owner = '';
				}

				//Add Vendor Owner
				if ( ! isset( $post_value['admins'] ) ) {
					$post_value['admins'] = array( $owner );
				} else {
					$temp_admins          = $post_value['admins'];
					$post_value['admins'] = YITH_Vendors()->is_wc_2_7_or_greather ? maybe_unserialize( $temp_admins ) : explode( ',', $temp_admins );

					if ( ! empty( $owner ) ) {
						$post_value['admins'][] = $owner;
					}
				}

				// Only add selected admins
				if ( ! empty( $post_value['admins'] ) ) {
				    $role_to_remove = apply_filters( 'yith_wcmv_remove_role_for_vendor_admins', array( 'customer', 'subscriber' ) );
					foreach ( $post_value['admins'] as $user_id ) {
						update_user_meta( $user_id, $usermeta_admin, $vendor->id );
						$user = get_user_by( 'id', $user_id );
						if ( $user instanceof WP_User ) {
							$user->add_role( YITH_Vendors()->get_role_name() );
							foreach ( $role_to_remove as $role ) {
								$user->remove_role( $role );
							}
						}
					}
				}

				$show_admin_notices = 'success';

				do_action( 'yith_wpv_after_save_taxonomy', $vendor, $post_value );
			}

			if ( 'admin_action_yith_admin_save_fields' == current_action() ) {
				$url = esc_url_raw( $_REQUEST['_wp_http_referer'] );
				$url = add_query_arg( array( 'message' => $show_admin_notices ), $url );
				wp_redirect( $url );
			}
		}

		/**
		 * Add or Remove capabilities to vendor admins
		 *
		 * @param int $user_id User ID of vendor admin
		 * @param string $method The method to call: add to call add_cap method or remove to call remove_cap
		 * @param string $caps The capabilities to add or remove
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @access protected
		 */
		protected function _manage_vendor_caps( $method, $caps = '' ) {
			if ( 'remove' == $method || 'add' == $method ) {
				$role = get_role( YITH_Vendors()->get_role_name() );
				if ( $role instanceof WP_Role ) {
					if ( '' == $caps ) {
						$caps = array_keys( YITH_Vendors()->vendor_enabled_capabilities() );

					} elseif ( is_string( $caps ) ) {
						$caps = array( $caps );
					}

					foreach ( $caps as $cap ) {
						if ( 'add' == $method ) {
							$role->add_cap( $cap, true );
						} else {
							$role->remove_cap( $cap );
						}
					}
				}
			}
		}

		/**
		 * Remove admin usermeta info
		 *
		 * @param $term
		 * @param $taxonomy
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function remove_admin_usermeta_info( $term, $taxonomy ) {

			if ( $this->_taxonomy_name != $taxonomy ) {
				return;
			}

			global $wpdb;

			$sql     = $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_value=%d AND meta_key=%s", $term, YITH_Vendors()->get_user_meta_owner() );
			$user_id = $wpdb->get_var( $sql );

			if ( $user_id > 0 ) {
				/**
				 * Remove admin caps to user
				 */
				$user = get_user_by( 'id', $user_id );
				$user->remove_role( YITH_Vendors()->get_role_name() );
				$user->add_role( 'customer' );

				/**
				 * Remove vendor admin
				 */
				delete_user_meta( $user_id, YITH_Vendors()->get_user_meta_key() );
				delete_user_meta( $user_id, YITH_Vendors()->get_user_meta_owner() );
			}
		}

		/**
		 * Set vendor products to draft
		 *
		 * @param        $term
		 *
		 * @param string $post_status
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function set_product_to_orphan( $term, $post_status = 'draft' ) {

			$vendor      = yith_get_vendor( $term );
			$product_ids = $vendor->get_products();

			foreach ( $product_ids as $product_id ) {
				wp_update_post( array( 'ID' => $product_id, 'post_status' => $post_status ) );
			}
		}

		/**
		 * Remove all data linked to vendor
		 *
		 * When an admin delete a vendor call this method to delete all vendor data
		 *
		 * @param $term
		 * @param $taxonomy
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function remove_vendor_data( $term, $taxonomy ) {
			/**
			 * Remove vendor admin user meta
			 */
			$this->remove_admin_usermeta_info( $term, $taxonomy );

			/**
			 * Set vendor's products to draft
			 */
			$this->set_product_to_orphan( $term );
		}

		/**
		 * When searching using the WP_User_Query, search names (user meta) too
		 *
		 * @param object $query The current query object
		 *
		 * @return void
		 * @see WP_User_Query Class wp-includes/user.php
		 */
		public function json_search_customer_name( $query ) {

			if ( isset( $_GET['action'] ) && 'woocommerce_json_search_customers' == $_GET['action'] && isset( $_GET['plugin'] ) && YITH_WPV_SLUG == $_GET['plugin'] && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;

				$term = wc_clean( stripslashes( $_GET['term'] ) );
				$term = $wpdb->esc_like( $term );

				$where_old          = $wpdb->prepare( ") OR user_name.meta_value LIKE %s ", '%' . $term . '%' );
				$where_new          = $wpdb->prepare( " OR user_name.meta_value LIKE %s) ", '%' . $term . '%' );
				$query->query_where = str_replace( $where_old, $where_new, $query->query_where );
				$query->query_where .= $wpdb->prepare( "AND ID NOT IN (SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s)", YITH_Vendors()->get_user_meta_key() );
			}
		}

		/**
		 * Filter admin-ajax url
		 *
		 * @param object $query The current query object
		 *
		 * @return string   Filter admin url
		 * @since 1.8.4
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function admin_url( $url ) {
			if ( strpos( $url, 'admin-ajax.php' ) !== false && ( $this->is_vendor_tax_page() || $this->is_vendor_dashboard() ) ) {
				$url = add_query_arg( array( 'plugin' => YITH_WPV_SLUG ), $url );
			}

			return $url;
		}

		/**
		 * Print the Single Taxonomy Metabox
		 *
		 * @param $taxonomy     string Taxonomy Name
		 * @param $taxonomy_box string Taxonomy Box
		 *
		 * @return void
		 * @since  1.0.0
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function single_taxonomy_meta_box( $taxonomy = '', $taxonomy_box = '' ) {
			if ( empty( $taxonomy ) ) {
				$taxonomy = YITH_Vendors()->get_taxonomy_name();
			}

			if ( empty( $taxonomy_box ) ) {
				$taxonomy_box = 'yith_wcmv_vendor_metabox';
			}

			$taxonomy_label = YITH_Vendors()->get_vendors_taxonomy_label();
			$vendor         = yith_get_vendor( 'current', 'product' );
			$vendor_id      = 0;
			$wpml_vendor    = null;

			/* WPML Support */
			global $sitepress, $pagenow;
			if ( $vendor->is_valid() ) {
				if ( isset( $sitepress ) ) {
					$vendor_id   = yit_wpml_object_id( $vendor->id, YITH_Vendors()->get_taxonomy_name(), true );
					$wpml_vendor = get_term_by( 'id', $vendor_id, $vendor->term->taxonomy );
					$vendor_id   = $wpml_vendor->term_id;
				} else {
					$vendor_id = $vendor->id;
				}
			} elseif ( isset( $sitepress ) && $vendor->is_super_user() && 'post-new.php' == $pagenow && ! empty( $_GET['trid'] ) ) {
				$original_product_id = SitePress::get_original_element_id_by_trid( $_GET['trid'] );
				$original_vendor     = yith_get_vendor( $original_product_id, 'product' );
				if ( $original_vendor->is_valid() ) {
					$vendor_id   = yit_wpml_object_id( $original_vendor->id, YITH_Vendors()->get_taxonomy_name(), true );
					$wpml_vendor = get_term_by( 'id', $vendor_id, $original_vendor->term->taxonomy );
					$vendor_id   = $wpml_vendor->term_id;
				}
			}


			$args = array(
				'id'                => 'tax-input-yith_shop_vendor',
				'name'              => 'tax_input[yith_shop_vendor]',
				'taxonomy'          => YITH_Vendors()->get_taxonomy_name(),
				'show_option_none'  => ! $vendor->is_super_user() ? '' : sprintf( __( 'No %s' ), strtolower( $taxonomy_label['singular_name'] ) ),
				'hide_empty'        => ! $vendor->is_super_user(),
				'selected'          => $vendor_id,
				'walker'            => YITH_Walker_CategoryDropdown(),
				'option_none_value' => '', // Avoid to save -1 as new vendor when you create a new product
			);

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() && $vendor->is_user_admin() ) {
				echo is_null( $wpml_vendor ) ? $vendor->name : $wpml_vendor->name;
			} else {
				wp_dropdown_categories( $args );
			}
		}

		/**
		 * If an user is a vendor admin remove the woocommerce prevent admin access
		 *
		 * @Author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return bool
		 * @use    woocommerce_prevent_admin_access hooks
		 * @since  1.0.0
		 */
		public function prevent_admin_access( $prevent_access ) {
			global $current_user;
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( ! $vendor->is_super_user() ) {
				$is_valid = $vendor->is_valid();
				if ( $is_valid && $vendor->has_limited_access() && $vendor->is_user_admin() ) {
					$prevent_access = false;
				} elseif ( ! $is_valid && in_array( YITH_Vendors()->get_role_name(), $current_user->roles ) ) {
					$prevent_access = true;
				}
			}

			return $prevent_access;
		}

		/**
		 * Get vendors admin array for select2
		 *
		 * @Author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @param $vendor The vendor object
		 *
		 * @return string The admins
		 * @since  1.0.0
		 *
		 */
		public function format_vendor_admins_for_select2( $vendor = '' ) {
			if ( empty( $vendor ) ) {
				$vendor = yith_get_vendor( 'current', 'user' );
			}

			$admins = array();
			foreach ( $vendor->get_admins() as $user_id ) {
				if ( $vendor->owner != $user_id ) {
					$user               = get_userdata( $user_id );
					$user_display       = is_object( $user ) ? $user->display_name . '(#' . $user_id . ' - ' . $user->user_email . ')' : '';
					$admins[ $user_id ] = $user_display;
				}
			}

			return YITH_Vendors()->is_wc_2_7_or_greather ? $admins : json_encode( $admins );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, false );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WPV_FREE_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = 'yith-woocommerce-multi-vendor';

			}

			if ( defined( 'YITH_WPV_FREE_INIT' ) && YITH_WPV_FREE_INIT == $plugin_file ) {
				$new_row_meta_args['support'] = array(
					'url' => 'https://wordpress.org/support/plugin/yith-woocommerce-product-vendors'
				);
			}

			return $new_row_meta_args;
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}

		/**
		 * Remove Dashboard Widgets
		 *
		 * @return  string The premium landing link
		 * @return void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function remove_dashboard_widgets() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {

				add_filter( 'jetpack_just_in_time_msgs', '__return_false', 999 );
				add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );

				$to_removes = apply_filters( 'yith_wcmv_to_remove_dashboard_widgets', array(
						array(
							'id'      => 'woocommerce_dashboard_status',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						array(
							'id'      => 'dashboard_activity',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						array(
							'id'      => 'woocommerce_dashboard_recent_reviews',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						array(
							'id'      => 'dashboard_right_now',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						array(
							'id'      => 'dashboard_quick_press',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						array(
							'id'      => 'yith_wcmc_dashboard_widget',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						array(
							'id'      => 'jetpack_summary_widget',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
						//
						array(
							'id'      => 'wpseo-dashboard-overview',
							'screen'  => 'dashboard',
							'context' => 'normal'
						),
					)
				);

				foreach ( $to_removes as $widget ) {
					remove_meta_box( $widget['id'], $widget['screen'], $widget['context'] );
				}
			}
		}

		/**
		 * Show the premium tabs
		 *
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.0.0
		 */
		public function show_premium_tab() {
			yith_wcpv_get_template( 'premium-tab', array(), 'admin' );
		}


		/**
		 * Replace the Visit Store link from WooCommerce
		 * with the vendor store page link, if user is a vendor
		 * and is are logged in
		 *
		 * @param $wp_admin_bar The WP_Admin_Bar object
		 *
		 * @since  1.5.1
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function show_admin_bar_visit_store( $wp_admin_bar ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() && apply_filters( 'woocommerce_show_admin_bar_visit_store', true ) ) {
				// Add an option to visit the store
				$wp_admin_bar->add_node( array(
					'parent' => 'site-name',
					'id'     => 'view-store',
					'title'  => __( 'Visit Store', 'yith-woocommerce-product-vendors' ),
					'href'   => $vendor->get_url( 'frontend' )
				) );
			}
		}

		/**
		 * Add an extra body classes for vendors dashboard
		 *
		 * @param $admin_body_classes
		 *
		 * @return string
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.5.1
		 *
		 */
		public function admin_body_class( $admin_body_classes ) {
			global $post;
			$vendor            = yith_get_vendor( 'current', 'user' );
			$is_ajax           = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$is_order_details  = YITH_Vendors()->orders->is_vendor_order_details_page();
			$refund_management = 'yes' == get_option( 'yith_wpv_vendors_option_order_refund_synchronization', 'no' );
			$quote_management  = 'yes' == get_option( 'yith_wpv_vendors_enable_request_quote', 'no' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$admin_body_classes = $admin_body_classes . ' vendor_limited_access';
				if ( $is_order_details && $refund_management ) {
					$admin_body_classes = $admin_body_classes . ' vendor_refund_management';
				}

				if ( function_exists( 'YITH_Vendor_Request_Quote' ) && $quote_management && $post instanceof WP_Post && YITH_Vendor_Request_Quote()->has_valid_quote_status( $post->ID ) ) {
					$admin_body_classes = $admin_body_classes . ' vendor_quote_management';
				}
			} else if ( $vendor->is_super_user() ) {
				$admin_body_classes = $admin_body_classes . ' vendor_super_user';

				if ( $post && wp_get_post_parent_id( $post->ID ) && 'shop_order' == $post->post_type && $is_order_details ) {
					$admin_body_classes = $admin_body_classes . ' vendor_suborder_detail';
				}
			}

			return $admin_body_classes;
		}

		/**
		 * Add a bubble notification icon for pending products
		 *
		 * @return string
		 * @since    1.5.1
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function products_to_approve() {
			$vendor = yith_get_vendor( 'current', 'user' );
			/* Add the pending products bubble on Products -> Vendors menu */
			if ( $vendor->is_super_user() ) {
				global $menu, $submenu;
				$products             = get_posts( array(
					'post_type'      => 'product',
					'post_status'    => 'pending',
					'posts_per_page' => - 1
				) );
				$num_pending_products = count( $products );

				if ( $num_pending_products > 0 ) {
					$bubble       = " <span class='awaiting-mod count-{$num_pending_products}'><span class='pending-count'>{$num_pending_products}</span></span>";
					$products_uri = htmlspecialchars( add_query_arg( array( 'post_type' => 'product' ), 'edit.php' ) );

					foreach ( $menu as $key => $value ) {
						if ( $menu[ $key ][2] == $products_uri && $num_pending_products > 0 ) {
							$menu[ $key ][0] .= $bubble;
						}
					}

					foreach ( $submenu as $key => $value ) {
						$submenu_items = $submenu[ $key ];
						foreach ( $submenu_items as $position => $value ) {
							if ( $submenu[ $key ][ $position ][2] == $products_uri ) {
								$submenu[ $key ][ $position ][0] .= $bubble;

								return;
							}
						}
					}
				}
			}
		}

		//TODO: Move this code in order class

		/**
		 * Only show admins order
		 *
		 * @param $query query args
		 *
		 * @return arr          Modified request
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @since    1.6
		 */
		public function filter_order_list( $query ) {
			global $typenow;

			if ( 'shop_order' == $typenow ) {
				$vendor = yith_get_vendor( 'current', 'vendor' );
				if ( $vendor->is_super_user() && empty( $_REQUEST['s'] ) ) {
					$query['post_parent'] = 0;
				}

				return apply_filters( "yith_wcmv_{$typenow}_request", $query );
			}

			return $query;
		}

		/**
		 * Add items to dashboard menu
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function admin_vendor_link() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_super_user() ) {
				$url_args = array(
					'taxonomy'  => YITH_Vendors()->get_taxonomy_name(),
					'post_type' => 'product'
				);

				$menu_args = apply_filters( 'yith_wc_product_vendors_taxonomy_menu_items', array(
					'page_title' => YITH_Vendors()->get_vendors_taxonomy_label( 'menu_name' ),
					'menu_title' => YITH_Vendors()->get_vendors_taxonomy_label( 'menu_name' ),
					'capability' => 'manage_woocommerce',
					'menu_slug'  => htmlspecialchars( add_query_arg( $url_args, 'edit-tags.php' ) ),
					'function'   => '',
					'icon'       => 'dashicons-admin-multisite',
					'position'   => 56
				) );
				extract( $menu_args );
				add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position );
			}
		}

		/**
		 * Check if current page is the vendor taxonomy page (in admin)
		 *
		 * @return bool
		 * @since  1.7
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function is_vendor_tax_page() {
			global $pagenow;

			return ( 'edit-tags.php' == $pagenow || 'term.php' == $pagenow ) && ! empty( $_GET['taxonomy'] ) && YITH_Vendors()->get_taxonomy_name() == $_GET['taxonomy'] && ! empty( $_GET['post_type'] ) && 'product' == $_GET['post_type'];
		}

		/**
		 * Check if current page is the vendor dashboard page (in admin)
		 *
		 * @return bool
		 * @since  1.8.4
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function is_vendor_dashboard() {
			$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

			return is_admin() && is_object( $current_screen ) && 'toplevel_page_yith_vendor_settings' == $current_screen->id && ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && 'vendor-settings' == $_GET['tab'] ) );
		}

		/**
		 * Handles the output of the roles column on the `users.php` screen.
		 *
		 * @param string $output
		 * @param string $column
		 * @param int $user_id
		 *
		 * @return string
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function manage_users_custom_column( $output, $column, $user_id ) {
			if ( 'roles' === $column ) {
				global $wp_roles;

				$user       = new WP_User( $user_id );
				$user_roles = array();
				$output     = esc_html__( 'None', 'yith-woocommerce-product-vendors' );

				if ( is_array( $user->roles ) ) {
					foreach ( $user->roles as $role ) {
						$user_roles[] = translate_user_role( $wp_roles->role_names[ $role ] );
					}
					$output = join( ', ', $user_roles );
				}
			}

			return $output;
		}

		/**
		 * Adds custom columns to the `users.php` screen.
		 *
		 * @param array $columns
		 *
		 * @return array
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function manage_users_columns( $columns ) {

			// Unset the core WP `role` column.
			if ( isset( $columns['role'] ) ) {
				unset( $columns['role'] );
			}

			// Add our new roles column.
			$columns['roles'] = esc_html__( 'Roles', 'yith-woocommerce-product-vendors' );

			return $columns;
		}

		/**
		 * Check for order capabilities
		 *
		 * Add or remove vendor capabilities for coupon and review management
		 *
		 * @return void
		 * @since  1.3
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function manage_caps() {
			$is_vendor_panel = isset( $_GET['page'] ) && 'yith_wpv_panel' == $_GET['page'] ? true : false;
			$is_vendor_tab   = isset( $_GET['tab'] ) && 'vendors' == $_GET['tab'];
			$is_addons_tab   = isset( $_GET['tab'] ) && 'add-ons' == $_GET['tab'];
			$addons_caps_key = array();

			if ( class_exists( 'YITH_WCMV_Addons' ) && ! empty( YITH_Vendors()->addons ) ) {
				$addons_caps_key = array_keys( YITH_Vendors()->addons->plugins );
			}

			if ( $is_vendor_panel && ( $is_vendor_tab || $is_addons_tab ) ) {
				$caps = apply_filters( 'yith_wcmv_manage_role_caps', array(
						'order' => array(
							'edit_shop_orders'             => true,
							'read_shop_orders'             => true,
							'delete_shop_orders'           => true,
							'publish_shop_orders'          => true,
							'edit_published_shop_orders'   => true,
							'delete_published_shop_orders' => true,
						),
					)
				);

				foreach ( array_keys( $caps ) as $option ) {
					$option_slug = str_replace( '_', '-', $option );

					if ( $is_vendor_tab && in_array( $option_slug, $addons_caps_key ) ) {
						continue;
					} elseif ( $is_addons_tab && ! in_array( $option_slug, $addons_caps_key ) ) {
						continue;
					}

					$option_id = "yith_wpv_vendors_option_{$option}_management";
					$old_value = get_option( $option_id, 'no' );
					$new_value = isset( $_POST[ $option_id ] ) ? 'yes' : 'no';

					if ( $old_value != $new_value ) {
						foreach ( $caps[ $option ] as $key_cap => $key_val ) {
							$cap = $key_val === true ? $key_cap : $key_val;
							if ( 'no' == $old_value ) {
								$this->_manage_vendor_caps( 'add', $cap );

							} else {
								$this->_manage_vendor_caps( 'remove', $cap );

							}
						}
					}
				}
			}
		}

		/**
		 * Allow/Disable WooCommerce add new post type creation
		 *
		 * @return void
		 * @since    1.2.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_new_link_check() {
			global $typenow;

			//Check if post types is product or shop order
			if ( 'shop_order' != $typenow ) {
				return;
			}

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				global $submenu;
				foreach ( $submenu as $section => $menu ) {
					foreach ( $menu as $position => $args ) {
						$submenu_url = "post-new.php?post_type={$typenow}";
						if ( in_array( $submenu_url, $args ) ) {
							$submenu[ $section ][ $position ][4] = 'yith-wcpv-hide-submenu-item';
							break;
						}
					}
				}
				add_action( 'admin_enqueue_scripts', array( $this, 'remove_add_new_button' ), 20 );
			}
		}

		/**
		 * Remove add new post types button
		 *
		 * @return void
		 * @since    1.6.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function remove_add_new_button() {
			global $post_type, $wp_version;
			$rule = false;
			if ( version_compare( $wp_version, '4.3-RC1', '>=' ) ) {
				$rule = ".post-type-{$post_type} .wrap .page-title-action{ display: none; }";
			} else {
				$rule = ".post-type-{$post_type} .add-new-h2{ display: none; }";
			}

			wp_add_inline_style( 'yith-wc-product-vendors-admin', $rule );
		}

		/**
		 * Check if needs to refresh rewrite rules for frontpage
		 *
		 * @return void
		 * @since    1.9.7
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function check_rewrite_rules() {
			$current_action   = current_action();
			$is_reset         = 'yit_panel_wc_before_reset' == current_action();
			$is_update        = ! empty( $_POST['yith_wpv_vendor_taxonomy_rewrite'] ) && get_option( 'yith_wpv_vendor_taxonomy_rewrite' ) != $_POST['yith_wpv_vendor_taxonomy_rewrite'];
			$is_frontpage_tab = isset( $_GET['page'] ) && $this->_panel_page == $_GET['page'] && isset( $_GET['tab'] ) && 'frontpage' == $_GET['tab'];

			if ( ( $is_reset || $is_update ) && $is_frontpage_tab ) {
				update_option( 'yith_wcmv_flush_rewrite_rules', true );
			}
		}

		/**
		 * Add vendor taxonomy to YITH Theme fw 2.0 in layouts section
		 *
		 * @return   mixed Taxonomies array
		 * @since    1.8.1
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_taxonomy_to_layouts( $taxonomies ) {
			$taxonomies[ YITH_Vendors()->get_taxonomy_name() ] = get_taxonomy( YITH_Vendors()->get_taxonomy_name() );

			return $taxonomies;
		}

		/**
		 * Return the protected attribute capability for vendor menu
		 *
		 * @return string The vendor menu capabilty
		 * @since    1.8.4
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_special_cap() {
			return $this->_vendor_role;
		}

		/**
		 * Remove Essential Grid Metabox
		 *
		 * @return   void
		 * @since    1.9.5
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function remove_ess_grid_metabox() {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				remove_meta_box( 'eg-meta-box', 'product', 'normal' );
			}
		}

		/**
		 * Sidebar links
		 *
		 * @return   array The links
		 * @since    1.9.6
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_sidebar_link() {
			$links = array(
				array(
					'title' => __( 'Plugin documentation', 'yith-woocommerce-product-vendors' ),
					'url'   => $this->_official_documentation,
				),
			);

			if ( defined( 'YITH_WPV_FREE_INIT' ) ) {
				$links[] = array(
					'title' => __( 'Discover the premium version', 'yith-woocommerce-product-vendors' ),
					'url'   => $this->_premium_landing,
				);

				$links[] = array(
					'title' => __( 'Free Vs Premium', 'yith-woocommerce-product-vendors' ),
					'url'   => 'https://yithemes.com/themes/plugins/yith-woocommerce-multi-vendor/#tab-free_vs_premium_tab',
				);

				$links[] = array(
					'title' => __( 'Premium live demo', 'yith-woocommerce-product-vendors' ),
					'url'   => $this->_premium_live
				);

				$links[] = array(
					'title' => __( 'WordPress support forum', 'yith-woocommerce-product-vendors' ),
					'url'   => 'https://wordpress.org/support/plugin/yith-woocommerce-product-vendors',
				);

				$links[] = array(
					'title' => __( 'Changelog', 'yith-woocommerce-product-vendors' ),
					'url'   => 'http://yithemes.com/docs-plugins/yith-woocommerce-multi-vendor/14-changelog.html',
				);
			}

			if ( defined( 'YITH_WPV_PREMIUM' ) ) {
				$links[] = array(
					'title' => __( 'Support platform', 'yith-woocommerce-product-vendors' ),
					'url'   => 'https://yithemes.com/my-account/support/dashboard/',
				);

				$links[] = array(
					'title' => sprintf( '%s (%s %s)', __( 'Changelog', 'yith-woocommerce-product-vendors' ), __( 'current version', 'yith-woocommerce-product-vendors' ), YITH_WPV_VERSION ),
					'url'   => 'http://yithemes.com/docs-plugins/yith-woocommerce-multi-vendor/15-changelog-premium.html',
				);

				$links[] = array(
					'title' => __( 'PayPal sandbox test accounts', 'yith-woocommerce-product-vendors' ),
					'url'   => 'https://developer.paypal.com/developer/accounts/',
				);

				$links[] = array(
					'title' => __( 'Enable PayPal MassPay', 'yith-woocommerce-product-vendors' ),
					'url'   => 'https://www.paypal.com/selfhelp/home',
				);
			}

			return $links;
		}

		/**
		 * Filter TopSeller query for WooCommerce Dashboard Widget
		 *
		 * @param Array $query
		 *
		 * @return Array
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @since 1.9.16
		 */
		public function dashboard_status_widget_top_seller_query( $query ) {
			$query['where'] .= "AND posts.post_parent = 0";

			return $query;
		}

		/**
		 * Hakc WooCommerce order count
		 *
		 * @return false if cached value is set
		 * @since 1.9.16
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 */
		public function count_processing_order() {
			global $wpdb;

			$count          = 0;
			$status         = 'wc-processing';
			$order_statuses = array_keys( wc_get_order_statuses() );

			if ( ! in_array( $status, $order_statuses ) ) {
				return 0;
			}

			$cache_key   = WC_Cache_Helper::get_cache_prefix( 'orders' ) . $status;
			$cache_group = 'yith_wcmv';

			$cached = wp_cache_get( $cache_group . '_' . $cache_key, $cache_group );

			if ( $cached ) {
				return 0;
			}

			foreach ( wc_get_order_types( 'order-count' ) as $type ) {
				$query = "SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_parent = 0";
				$count += $wpdb->get_var( $wpdb->prepare( $query, $type, $status ) );
			}

			wp_cache_set( $cache_key, $count, 'counts' );
			wp_cache_set( $cache_group . '_' . $cache_key, true, $cache_group );
		}

		/**
		 * Get panel object
		 *
		 * @return object yith framework panel object
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 */
		public function get_panel() {
			return $this->_panel;
		}

		/**
		 * Search for customers and return json.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function json_search_admins() {
			ob_start();

			check_ajax_referer( 'search-customers', 'security' );

			$vendor          = yith_get_vendor( 'current', 'user' );
			$vendor_is_valid = $vendor->is_valid();
			if ( ! $vendor_is_valid || ( $vendor_is_valid && 'yes' == get_option( 'yith_wpv_vendors_option_order_management' ) ) ) {
				return;
			}

			$term    = wc_clean( stripslashes( $_GET['term'] ) );
			$exclude = array();

			if ( empty( $term ) ) {
				die();
			}

			if ( ! empty( $_GET['exclude'] ) ) {
				$exclude = array_map( 'intval', explode( ',', $_GET['exclude'] ) );
			}

			$found_customers = array();

			$is_callable_json_search_customer_name = is_callable( 'WC_AJAX::json_search_customer_name' );

			$is_callable_json_search_customer_name && add_action( 'pre_user_query', 'WC_AJAX::json_search_customer_name' );

			$customers_query = new WP_User_Query( apply_filters( 'woocommerce_json_search_customers_query', array(
				'fields'         => 'all',
				'orderby'        => 'display_name',
				'search'         => '*' . $term . '*',
				'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
			) ) );

			$is_callable_json_search_customer_name && remove_action( 'pre_user_query', 'WC_AJAX::json_search_customer_name' );

			$customers = $customers_query->get_results();

			if ( ! empty( $customers ) ) {
				foreach ( $customers as $customer ) {
					if ( ! in_array( $customer->ID, $exclude ) ) {
						$found_customers[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email( $customer->user_email ) . ')';
					}
				}
			}

			$found_customers = apply_filters( 'woocommerce_json_search_found_customers', $found_customers );

			wp_send_json( $found_customers );
		}

		/**
		 * Remove the add existing content from tiny mce editor
		 *
		 * @return void
		 * @author Andrea Grillo
		 */
		public static function remove_add_existing_content() {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				ob_start(); ?>
                <script type="text/javascript">
                    jQuery('#wplink-link-existing-content').add('#search-panel').remove();
                </script>
				<?php echo ob_get_clean();
			}
		}

		/**
		 * Print an admin notice
		 *
		 * @return void
		 * @use admin_notices hooks
		 * @since 2.1.1
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_woocommerce_admin_notice() {
			$message = $type = '';
			if ( isset( $_GET['message'] ) ) {
				switch ( $_GET['message'] ) {
					case 'success':
						$message = __( 'Option Saved', 'yith-woocommerce-product-vendors' );
						$type    = 'updated';
						break;

					case 'name_exists':
						$message = __( 'A vendor with this name already exists.', 'yith-woocommerce-product-vendors' );
						$type    = 'error';
						break;

					case 'owner_exist':
						$message = __( "You can't associate more vendor shops with the same shop owner.", 'yith-woocommerce-product-vendors' );
						$type    = 'error';
						break;
				}
			}

			if ( ! empty( $message ) ) :
				?>
                <div class="<?php echo $type; ?>">
                    <p><?php echo $message; ?></p>
                </div>
			<?php
			endif;
		}

		/**
		 * Add a new attribute via ajax function.
		 *
		 * @return void
		 * @use admin_notices hooks
		 * @since 2.3.2
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function add_new_attribute() {
			check_ajax_referer( 'add-attribute', 'security' );
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$taxonomy = esc_attr( $_POST['taxonomy'] );
				$term     = wc_clean( $_POST['term'] );

				if ( taxonomy_exists( $taxonomy ) ) {

					$result = wp_insert_term( $term, $taxonomy );

					if ( is_wp_error( $result ) ) {
						wp_send_json( array(
							'error' => $result->get_error_message(),
						) );
					} else {
						$term = get_term_by( 'id', $result['term_id'], $taxonomy );
						wp_send_json( array(
							'term_id' => $term->term_id,
							'name'    => $term->name,
							'slug'    => $term->slug,
						) );
					}
				}
				wp_die( - 1 );
			}
		}

		/**
		 * Remove 3rd-party plugin metaboxes
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @retur void
		 */
		public function remove_meta_boxes() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() ) {
				$default = array(
					'id'      => '',
					'screen'  => null,
					'context' => 'advanced'
				);

				$to_remove = apply_filters( 'yith_wcmv_remove_product_metaboxes', array(
					array(
						'id'      => 'ckwc',
						'screen'  => null,
						'context' => 'side'
					),
				) );

				foreach ( $to_remove as $r ) {
					$r = wp_parse_args( $r, $default );
					if ( ! empty( $r['id'] ) ) {
						remove_meta_box( $r['id'], $r['screen'], $r['context'] );
					}
				}
			}
		}

		/**
		 * Remove Jetpack pages for Vendor
		 *
		 * @param $caps
		 * @param $cap
		 * @param $user_id
		 * @param $args
		 *
		 * @return array
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 */
		public function remove_jetpack_menu_page( $caps, $cap, $user_id, $args ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && 'jetpack_admin_page' === $cap ) {
				$caps[] = 'manage_options';
			}

			return $caps;
		}

		/**
		 * Remove AB Icon added by Jetpack for vendor users
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @retur void
		 */
		public function remove_yoast_seo_ab_icon_admin_bar() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				global $wp_admin_bar;
				$wp_admin_bar->remove_menu( 'wpseo-menu' );
			}
		}

		/**
         * Add a filter by vendor in products list in admin area only if
         * current user can manager WooCommerce
         *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
         *
		 * @param $filters array of enabled filters
		 *
		 * @return array edited fiters
		 */
		public function products_admin_list_table_filters( $filters ) {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$tax_name             = YITH_Vendors()->get_taxonomy_name();
				$filters[ $tax_name ] = array( $this, 'render_products_category_filter' );
			}

			return $filters;
		}

		/**
		 * Filter by Vendor render on products page in admin
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
		 */
		public function render_products_category_filter() {
			global $wp_query;

			$taxonomy_label = YITH_Vendors()->get_vendors_taxonomy_label();
			$taxonomy_name = YITH_Vendors()->get_taxonomy_name();

			$args = array(
				'pad_counts'         => 1,
				'show_count'         => 1,
				'hierarchical'       => 1,
				'hide_empty'         => 1,
				'show_uncategorized' => 1,
				'orderby'            => 'name',
				'selected'           => isset( $wp_query->query_vars[ $taxonomy_name ] ) ? $wp_query->query_vars[ $taxonomy_name ] : '',
				'show_option_none'   => sprintf( __( 'No %s' ), strtolower( $taxonomy_label['singular_name'] ) ),
				'option_none_value'  => '',
				'value_field'        => 'slug',
				'taxonomy'           => $taxonomy_name,
				'name'               => $taxonomy_name,
				'class'              => 'dropdown_product_vendor',
			);

			if ( 'order' === $args['orderby'] ) {
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'order'; // phpcs:ignore
			}

			wp_dropdown_categories( $args );
		}

		/**
		 * Skip duplicated term name for vendor name translation
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function wpml_save_term_action(){
			add_filter( 'yith_wcmv_skip_check_duplicate_term_name', '__return_true' );
        }
	}
}
