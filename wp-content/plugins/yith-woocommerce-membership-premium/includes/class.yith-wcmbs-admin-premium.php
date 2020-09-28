<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since    1.0.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Admin_Premium extends YITH_WCMBS_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $_instance;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {
			parent::__construct();

			// Add Capabilities to Administrator and Shop Manager
			add_action( 'admin_init', array( $this, 'add_plans_capabilities' ) );

			// Bulk Edit for adding Membership Restrict Access
			add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_render' ), 10, 2 );
			//add_action( 'quick_edit_custom_box', array( $this, 'bulk_edit_render' ), 10, 2 );
			add_action( 'wp_ajax_yith_wcmbs_save_bulk_edit', array( $this, 'save_bulk_edit' ) );

			// Ajax Action for removing plan for post or product
			add_action( 'wp_ajax_yith_wcmbs_remove_plan_for_post', array( $this, 'remove_plan_for_post' ) );

			// Register Post Type
			add_action( 'init', array( $this, 'post_type_register' ), 16 );

			// Actions for membership
			add_action( 'add_meta_boxes', array( $this, 'show_info_membership' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_actions_in_membership' ) );
			add_action( 'admin_menu', array( $this, 'remove_publish_box_for_membership' ) );
			add_action( 'save_post', array( $this, 'save_membership' ) );

			// Free to Premium
			add_action( 'init', array( $this, 'free_to_premium' ), 15 );

			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'custom_type_in_metabox' ) );

			// Add custom types of chosen for yit metabox
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'custom_types_for_yit_metabox' ) );
			add_action( 'save_post', array( $this, 'add_items_in_plan' ) );

			add_action( 'admin_action_duplicate_membership', array( $this, 'admin_action_duplicate_membership' ) );
			add_filter( 'post_row_actions', array( $this, 'add_duplicate_action_on_plans' ), 10, 2 );

			// Filter settings for premium options
			add_filter( 'yith_wcmbs_panel_settings_options', array( $this, 'premium_settings' ) );

			// Add metabox for alternative content
			add_action( 'init', array( $this, 'add_metabox_for_alternative_content' ) );

			YITH_WCMBS_Messages_Manager_Admin();

			add_action( 'edit_attachment', array( $this, 'save_metaboxes' ) );
			add_filter( 'manage_media_columns', array( $this, 'add_columns' ) );
			add_action( 'manage_media_custom_column', array( $this, 'custom_columns' ), 10, 2 );

			/* Save plan item order metabox for Plans */
			add_action( 'save_post', array( $this, 'save_plan_item_order_metabox' ) );

			/* Add Membership Items shortcodes in Membership Plan List */
			add_filter( 'manage_yith-wcmbs-plan_posts_columns', array( $this, 'add_shortcode_columns_in_plan' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'render_shortcode_columns_in_plan' ), 10, 2 );

			/* Manage Membership List columns */
			add_filter( 'manage_' . YITH_WCMBS_Membership_Helper()->post_type_name . '_posts_columns', array( $this, 'manage_membership_list_columns' ) );
			add_action( 'manage_' . YITH_WCMBS_Membership_Helper()->post_type_name . '_posts_custom_column', array( $this, 'render_membership_list_columns' ), 10, 2 );
			add_filter( 'manage_edit-' . YITH_WCMBS_Membership_Helper()->post_type_name . '_sortable_columns', array( $this, 'manage_membership_sortable_columns' ), 10, 1 );
			add_action( 'pre_get_posts', array( $this, 'membership_orderby' ) );
			add_action( 'parse_query', array( $this, 'membership_search' ) );
			add_filter( 'get_search_query', array( $this, 'membership_search_label' ) );

			// Remove Actions for Membership
			add_filter( 'post_row_actions', array( $this, 'membership_row_actions' ), 10, 2 );
			// Remove bulk actions for Membership List
			add_filter( 'bulk_actions-edit-' . YITH_WCMBS_Membership_Helper()->post_type_name, '__return_empty_array' );
			// Insert Membership Status Filters in Membership WP LIST
			add_filter( 'views_edit-' . YITH_WCMBS_Membership_Helper()->post_type_name, array( $this, 'insert_membership_status_filters' ) );
			add_action( 'pre_get_posts', array( $this, 'filter_memberships' ) );

			// prevent delete or trash plans
			add_action( 'wp_trash_post', array( $this, 'prevent_delete_or_trash_plans' ) );
			add_action( 'before_delete_post', array( $this, 'prevent_delete_or_trash_plans' ) );
			// remove restricted access for all items in plan
			add_action( 'before_delete_post', array( $this, 'remove_restricted_access_on_delete_plan' ), 11 );

			// Delete Transients
			add_action( 'save_post', array( YITH_WCMBS_Manager(), 'delete_transients' ) );
			add_action( 'yit_panel_wc_after_update', array( $this, 'delete_transient_after_update_options' ) );

			YITH_WCMBS_Advanced_Administration();

			add_filter( 'woocommerce_json_search_found_customers', array( $this, 'json_search_found_customers_return_username_only' ), 10, 1 );
		}

		function json_search_found_customers_return_username_only( $found_customers ) {
			if ( ! ! $found_customers && isset( $_GET['yith_wcmbs_show_username_only'] ) ) {
				foreach ( $found_customers as $user_id => $user_data ) {
					$user = get_user_by( 'id', $user_id );
					if ( $user ) {
						$customer = new WC_Customer( intval( $user_id ) );
						$data     = $user->nickname . ' (#' . $user_id . ')';
						if ( 0 !== $customer->get_id() ) {
							$data = sprintf(
								esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
								$user->nickname,
								$customer->get_id(),
								$customer->get_email()
							);
						}
						$found_customers[ $user_id ] = $data;
					}
				}
			}

			return $found_customers;
		}


		/**
		 * filter Memberships by status [in Membership WP List page]
		 *
		 * @param WP_Query $query
		 */
		public function filter_memberships( $query ) {
			$is_membership               = isset( $query->query['post_type'] ) && $query->query['post_type'] == YITH_WCMBS_Membership_Helper()->post_type_name;
			$is_membership_status_filter = isset( $_REQUEST['membership_status'] ) && in_array( $_REQUEST['membership_status'], array_keys( YITH_WCMBS_Manager()->status_list ) );

			if ( is_admin() && $is_membership && $is_membership_status_filter ) {
				$status = $_REQUEST['membership_status'];

				$query->set( 'meta_query', array(
					array(
						'key'   => '_status',
						'value' => $status,
					),
				) );
			}
		}

		/**
		 * Insert Membership Status Filters in WP List Table for Memberships
		 *
		 * @param $views
		 *
		 * @return array
		 */
		public function insert_membership_status_filters( $views ) {
			$new_views = isset( $views['all'] ) ? array( 'all' => $views['all'] ) : array();

			if ( isset( $views['trash'] ) ) {
				$new_views['trash'] = $views['trash'];
			}

			$membership_statuses = YITH_WCMBS_Manager()->status_list;

			$link = admin_url( 'edit.php?post_type=ywcmbs-membership' );

			$current_status = isset( $_REQUEST['membership_status'] ) ? $_REQUEST['membership_status'] : '';

			foreach ( $membership_statuses as $status_key => $status_name ) {
				$this_link  = add_query_arg( array( 'membership_status' => $status_key ), $link );
				$class_html = $current_status == $status_key ? ' class="current" ' : '';
				$number     = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( $status_key );

				if ( $status_key == 'expiring' ) {
					$this_link = add_query_arg( array( 'orderby' => 'end_date', 'order' => 'asc' ), $this_link );
				}

				if ( $number > 0 ) {
					$new_views[ $status_key ] = "<a href='{$this_link}'{$class_html}>{$status_name} <span class='count'>({$number})</span></a>";
				}
			}

			return $new_views;
		}

		/**
		 * Delete transient after update options of Membership
		 */
		public function delete_transient_after_update_options() {
			if ( isset( $_POST['yith-wcmbs-hide-contents'] ) ) {
				do_action( 'yith_wcmbs_delete_transients' );
			}
		}

		/**
		 * Before deleting or trashing a membership plan
		 * remove restricted access for all items in plan
		 **
		 *
		 * @param int $post_id the id of the plan
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function remove_restricted_access_on_delete_plan( $post_id ) {
			$post_type = get_post_type( $post_id );

			if ( $post_type == 'yith-wcmbs-plan' ) {
				$restricted_post_types = YITH_WCMBS_Manager()->post_types;
				$plan_items            = array();
				foreach ( $restricted_post_types as $post_type ) {
					$meta_query = array(
						'relation' => 'OR',
						array(
							'key'     => '_yith_wcmbs_restrict_access_plan',
							'value'   => $post_id,
							'compare' => 'LIKE',
						),
					);

					$args       = array(
						'post_type'                  => $post_type,
						'posts_per_page'             => - 1,
						'post_status'                => $post_type == 'attachment' ? 'any' : 'publish',
						'yith_wcmbs_suppress_filter' => true,
						'meta_query'                 => $meta_query,
						'fields'                     => 'ids',
					);
					$plan_items = array_merge( $plan_items, get_posts( $args ) );
				}
				if ( ! empty( $plan_items ) ) {
					foreach ( $plan_items as $item_id ) {
						$restrict_access_plan = get_post_meta( $item_id, '_yith_wcmbs_restrict_access_plan', true );
						$restrict_access_plan = array_diff( $restrict_access_plan, array( $post_id ) );
						update_post_meta( $item_id, '_yith_wcmbs_restrict_access_plan', $restrict_access_plan );
					}
				}
			}
		}


		/**
		 * Before deleting or trashing a membership plan
		 * control if one or more memberships of this plan are not 'cancelled' or 'expired'.
		 * If there are at least one, block delete and trash actions for plan!
		 *
		 * @param int $post_id the id of the plan
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function prevent_delete_or_trash_plans( $post_id ) {
			$post_type = get_post_type( $post_id );

			if ( $post_type == 'yith-wcmbs-plan' ) {
				$meta_query = array(
					'relation' => 'AND',
					array(
						'key'   => '_plan_id',
						'value' => $post_id,
					),
					array(
						'key'     => '_status',
						'value'   => array( 'cancelled', 'expired' ),
						'compare' => 'NOT IN',
					),
				);

				$memberships = YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query );

				if ( ! empty( $memberships ) ) {
					$link = admin_url( 'edit.php' );
					$link = add_query_arg( array( 'post_type' => 'yith-wcmbs-plan' ), $link );

					$text = __( 'This membership plan cannot be deleted or trashed because it is currently active for one or more users! To delete it, all memberships linked to this plan must expire or be cancelled.' );
					$text .= "<br /><br /><a href='{$link}'>";
					$text .= __( 'Return to membership plans page' );
					$text .= '</a>';

					wp_die( $text, __( 'Error', 'yith-woocommerce-membership' ) );
				}

				do_action( 'yith_wcmbs_delete_transients' );
			}
		}


		/**
		 * Remove plan restriction from a post or a product
		 * [AJAX]
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function remove_plan_for_post() {
			if ( isset( $_POST['post_id'] ) && isset( $_POST['plan_id'] ) ) {
				$post_id                    = $_POST['post_id'];
				$plan_id                    = $_POST['plan_id'];
				$old_plan_ids               = get_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', true );
				$restrict_access_plan_delay = get_post_meta( $post_id, '_yith_wcmbs_plan_delay', true );

				$new_plans = array_diff( $old_plan_ids, array( $plan_id ) );
				if ( isset( $restrict_access_plan_delay[ $post_id ] ) ) {
					unset( $restrict_access_plan_delay[ $post_id ] );
				}

				update_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', $new_plans );
				update_post_meta( $post_id, '_yith_wcmbs_plan_delay', $restrict_access_plan_delay );

				do_action( 'yith_wcmbs_delete_transients' );
			}
			die();
		}

		/**
		 * Add bulk edit
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function bulk_edit_render( $column_name, $post_type ) {
			if ( in_array( $post_type, YITH_WCMBS_Manager()->post_types ) && $column_name == 'yith_wcmbs_restrict_access' ) {
				switch ( $column_name ) {
					case 'yith_wcmbs_restrict_access':
						wc_get_template( '/bulk/bulk-edit-memberhsip-access.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );

						break;
				}
			}
		}

		/**
		 * Save bulk edit [AJAX]
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function save_bulk_edit() {
			$post_ids = ( ! empty( $_POST['post_ids'] ) ) ? $_POST['post_ids'] : array();
			$plans    = ( ! empty( $_POST['yith_wcmbs_restrict_access_plan'] ) ) ? $_POST['yith_wcmbs_restrict_access_plan'] : null;

			if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					if ( ! empty( $plans ) ) {
						$old_plans = get_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', true );
						$old_plans = ! empty( $old_plans ) ? $old_plans : array();
						$new_plans = array_merge( $old_plans, array_diff( $plans, $old_plans ) );
						update_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', $new_plans );
					}
				}
			}
			die();
		}


		/**
		 * Save Membership
		 *
		 * @param int $post_id the id of the membership
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com
		 */
		public function save_membership( $post_id ) {
			if ( ! empty( $_POST['yith_wcmbs_membership_actions'] ) ) {
				$action = $_POST['yith_wcmbs_membership_actions'];

				if ( in_array( $action, array_keys( yith_wcmbs_get_membership_statuses() ) ) ) {
					$membership = new YITH_WCMBS_Membership( $post_id );
					$membership->update_status( $action );
				}
			}

			if ( ! empty( $_POST['_yith_wcmbs_membership_user_id'] ) ) {
				$user_id = $_POST['_yith_wcmbs_membership_user_id'];

				$membership = new YITH_WCMBS_Membership( $post_id );
				$membership->set( 'user_id', $user_id );
			}
		}

		/**
		 * Add Metabox Actions in membership
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com
		 */
		public function add_actions_in_membership() {
			add_meta_box( 'yith-wcmbs-membership-actions', __( 'Memebership Actions', 'yith-woocommerce-membership' ), array(
				$this,
				'show_membership_actions_metabox',
			), YITH_WCMBS_Membership_Helper()->post_type_name, 'side', 'high' );
		}

		/**
		 * Metabox Actions in membership
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com
		 */
		public function show_membership_actions_metabox( $post ) {

			$membership = new YITH_WCMBS_Membership( $post->ID );
			$args       = array(
				'membership' => $membership,
			);
			wc_get_template( '/metaboxes/membership_actions.php', $args, YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
		}

		/**
		 * Add Metabox to show the info of membership
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com
		 */
		public function show_info_membership() {
			add_meta_box( 'yith-wcmbs-info-membership', __( 'Membership Info', 'yith-woocommerce-membership' ), array(
				$this,
				'show_membership_info_metabox',
			), YITH_WCMBS_Membership_Helper()->post_type_name, 'normal', 'default' );
		}

		/**
		 * Remove publish metabox for membership
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com
		 */
		public function remove_publish_box_for_membership() {
			remove_meta_box( 'submitdiv', YITH_WCMBS_Membership_Helper()->post_type_name, 'side' );
		}

		/**
		 * Metabox to show the info of membership
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com
		 */
		public function show_membership_info_metabox( $post ) {

			$membership = new YITH_WCMBS_Membership( $post->ID );
			$args       = array(
				'membership' => $membership,
			);
			wc_get_template( '/metaboxes/membership_info_content.php', $args, YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
		}


		/**
		 * Add content in custom column in product table list
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function custom_columns( $column, $post_id ) {
			if ( $column == 'yith_wcmbs_restrict_access' ) {
				$restrict_access_plan = get_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', true );
				$html                 = '';

				if ( ! empty( $restrict_access_plan ) ) {
					$plans_html = '<br />';
					foreach ( $restrict_access_plan as $plan_id ) {
						$plan_name  = get_the_title( $plan_id );
						$plans_html .= $plan_name . '<br />';
					}

					$html = '<span class="dashicons dashicons-groups tips" data-tip="' . __( 'Included in memberships', 'yith-woocommerce-membership' ) . ': ' . $plans_html . '"></span>';
				}

				echo $html;
			}
		}

		/**
		 * Free to Premium
		 * create a plan in base of free plugin setting
		 * edit user with free membership and assign it to created plan
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function free_to_premium() {
			$free_membership_product_id = get_option( 'yith-wcmbs-membership-product', false );
			if ( $free_membership_product_id ) {
				$free_membership_name = get_option( 'yith-wcmbs-membership-name', false );

				$new_post = array(
					'post_status' => 'publish',
					'post_type'   => 'yith-wcmbs-plan',
					'post_title'  => $free_membership_name,
				);

				$new_post_id = wp_insert_post( $new_post );
				if ( $new_post_id ) {
					update_post_meta( $new_post_id, '_membership-product', $free_membership_product_id );


					/* Update USERS to Premium */
					$memberships = YITH_WCMBS_Membership_Helper()->get_all_memberships_by_plan_id( 0 );

					if ( ! empty( $memberships ) ) {
						foreach ( $memberships as $membership ) {
							$membership->set( 'plan_id', $new_post_id );
						}
					}

					/* Update POSTS, PAGES, PRODUCTS to Premium */
					$post_types = array( 'post', 'page', 'product' );
					foreach ( $post_types as $post_type ) {
						$args  = array(
							'posts_per_page' => - 1,
							'post_type'      => $post_type,
						);
						$posts = get_posts( $args );
						if ( ! empty( $posts ) ) {
							foreach ( $posts as $post ) {
								$restrict_access = get_post_meta( $post->ID, '_yith_wcmbs_restrict_access', true );
								if ( $restrict_access == 'all_members' ) {
									update_post_meta( $post->ID, '_yith_wcmbs_restrict_access_plan', array( $new_post_id ) );
									/* delete post meta restrict access for this post */
									delete_post_meta( $post->ID, '_yith_wcmbs_restrict_access' );
								}
							}
						}
					}

					/* delete free options */
					delete_option( 'yith-wcmbs-membership-name' );
					delete_option( 'yith-wcmbs-membership-product' );
				}
			}
		}


		/**
		 * Add plans management capabilities to Admin and Shop Manager
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_plans_capabilities() {

			$capability_type = 'plan';
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

			foreach ( $caps as $key => $cap ) {
				$admin->add_cap( $cap );
				if ( $shop_manager ) {
					$shop_manager->add_cap( $cap );
				}
			}
		}


		/**
		 * filter settings for premium options
		 *
		 * @param array $tab
		 *
		 * @return array
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function premium_settings( $tab ) {
			$new_tab = include YITH_WCMBS_DIR . 'plugin-options/settings-premium-options.php';

			return $new_tab;
		}


		/*
		 * set the custom type ajax product
		 *
		 * @param array $args
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function custom_type_in_metabox( $args ) {
			if ( isset( $args['type'] ) ) {
				switch ( $args['type'] ) {
					case 'yith-wcmbs-ajax-products':
						$new_args = array(
							'basename' => YITH_WCMBS_DIR,
							'path'     => 'premium/metaboxes/types/',
							'type'     => 'yith-wcmbs-ajax-products',
							'args'     => $args['args'],
						);

						$args = $new_args;
						break;
					case 'yith-wcmbs-ajax-search':
						$new_args = array(
							'basename' => YITH_WCMBS_DIR,
							'path'     => 'premium/metaboxes/types/',
							'type'     => 'yith-wcmbs-ajax-search',
							'args'     => $args['args'],
						);

						$args = $new_args;
						break;

				}
			}

			return $args;
		}

		/*
		 * parse custom types for YIT Metabox
		 *
		 * @param array $args
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function custom_types_for_yit_metabox( $args ) {
			if ( isset( $args['type'] ) ) {
				$items_array = array();
				switch ( $args['type'] ) {
					case 'fl-product-cats':
						//$items_array = get_terms( 'product_cat', array( 'fields' => 'id=>name' ) );
						$items_array = yith_wcmbs_get_hierarchicaly_terms( 'product_cat' );
						break;
					case 'fl-post-cats':
						//$items_array = get_terms( 'category', array( 'fields' => 'id=>name' ) );
						$items_array = yith_wcmbs_get_hierarchicaly_terms( 'category' );
						break;
					default:
						return $args;
				}

				if ( in_array( $args['type'], array( 'fl-product-cats', 'fl-post-cats' ) ) ) {
					$args['type']            = 'chosen-and-buttons';
					$args['basename']        = YITH_WCMBS_DIR;
					$args['path']            = 'premium/metaboxes/types/';
					$args['args']['buttons'] = array(
						array(
							'title' => __( 'Select All', 'yith-woocommerce-membership' ),
							'class' => 'button yith-wcmbs-select2-select-all',
						),
						array(
							'title' => __( 'Deselect All', 'yith-woocommerce-membership' ),
							'class' => 'button yith-wcmbs-select2-deselect-all',
						),
					);
				}

				$args['args']['options'] = $items_array;
			}

			return $args;
		}

		/**
		 * Add items in plan
		 *
		 * @param int $post_id the id of the plan
		 *
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_items_in_plan( $post_id ) {
			$post_type = get_post_type( $post_id );
			if ( $post_type == 'yith-wcmbs-plan' ) {
				if ( isset( $_POST['yit_metaboxes'] ) ) {
					$metaboxes = $_POST['yit_metaboxes'];

					$post_ids = array();
					if ( ! empty( $metaboxes['_add-products'] ) ) {
						$products_to_add = is_array( $metaboxes['_add-products'] ) ? $metaboxes['_add-products'] : explode( ',', $metaboxes['_add-products'] );
						$post_ids        = array_merge( $post_ids, $products_to_add );
					}

					if ( ! empty( $metaboxes['_add-posts'] ) ) {
						$posts_to_add = is_array( $metaboxes['_add-posts'] ) ? $metaboxes['_add-posts'] : explode( ',', $metaboxes['_add-posts'] );
						$post_ids     = array_merge( $post_ids, $posts_to_add );
					}
					if ( ! empty( $metaboxes['_add-pages'] ) ) {
						$pages_to_add = is_array( $metaboxes['_add-pages'] ) ? $metaboxes['_add-pages'] : explode( ',', $metaboxes['_add-pages'] );
						$post_ids     = array_merge( $post_ids, $pages_to_add );
					}

					if ( ! empty( $post_ids ) ) {
						foreach ( $post_ids as $id ) {
							$plans = get_post_meta( $id, '_yith_wcmbs_restrict_access_plan', true );
							$plans = ! empty( $plans ) ? $plans : array();
							$plans = array_unique( array_merge( $plans, array( $post_id ) ) );
							update_post_meta( $id, '_yith_wcmbs_restrict_access_plan', $plans );
						}
					}

				}
			}
		}


		/**
		 * Add Metaboxes
		 *
		 * @param string $post_type
		 *
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function register_metaboxes( $post_type ) {
			if ( in_array( $post_type, YITH_WCMBS_Manager()->post_types ) ) {
				add_meta_box( 'yith-wcmbs-restrict-access-metabox', __( 'Set access', 'yith-woocommerce-membership' ),
							  array( $this, 'restrict_access_metabox_render' ), null, 'side', 'high' );
			}

			/**
			 * this hook will be removed, since its name is different than what it does
			 *
			 * @deprecated hook yith_wcmbs_show_restrict_access_metabox
			 */
			$deprecated_check = apply_filters( 'yith_wcmbs_show_restrict_access_metabox', true );

			if ( ( $post_type == 'yith-wcmbs-plan' ) && $deprecated_check ) {
				if ( apply_filters( 'yith_wcmbs_show_plan_item_order_metabox', true ) ) {
					add_meta_box( 'yith-wcmbs-plan-item-order', __( 'Plan Item Order', 'yith-woocommerce-membership' ),
								  array( $this, 'plan_item_order_render' ), null, 'normal', 'default' );
				}

				if ( apply_filters( 'yith_wcmbs_show_plan_item_style_metabox', true ) ) {
					add_meta_box( 'yith-wcmbs-shortcode-plan-item-style', __( 'Item list style of the shortcode', 'yith-woocommerce-membership' ),
								  array( $this, 'shortcode_plan_item_list_style_render' ), null, 'normal', 'default' );
				}
			}
		}

		/**
		 * Render Plan Item Order Metabox for Plans
		 *
		 * @param WP_Post $post the post
		 *
		 * @return void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function plan_item_order_render( $post ) {

			$allowed_in_plan = YITH_WCMBS_Manager()->get_allowed_posts_in_plan( $post->ID );

			$sorted_items = get_post_meta( $post->ID, '_yith_wcmbs_plan_items', true );
			$sorted_items = apply_filters( 'yith_wcmbs_sorted_plan_items', $sorted_items, $post->ID );
			$sorted_items = ! empty( $sorted_items ) ? $sorted_items : array();

			$plan_list_styles = get_post_meta( $post->ID, '_yith_wcmbs_plan_list_styles', true );

			foreach ( $sorted_items as $key => $item ) {
				if ( is_numeric( $item ) ) {
					if ( ! in_array( $item, $allowed_in_plan ) ) {
						unset( $sorted_items[ $key ] );
					}
				}
			}

			if ( ! empty( $allowed_in_plan ) ) {
				foreach ( $allowed_in_plan as $item_id ) {
					if ( ! in_array( $item_id, $sorted_items ) ) {
						$sorted_items[] = $item_id;
					}
				}
			}

			$t_args = array(
				'posts'   => $sorted_items,
				'plan_id' => $post->ID,
			);

			wc_get_template( '/metaboxes/plan_item_order.php', $t_args, '', YITH_WCMBS_TEMPLATE_PATH );
		}

		/**
		 * Render Shortcode Item List style Metabox for Plans
		 *
		 * @param WP_Post $post the post
		 *
		 * @return void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function shortcode_plan_item_list_style_render( $post ) {
			$plan_list_styles = get_post_meta( $post->ID, '_yith_wcmbs_plan_list_styles', true );

			$default_plan_list_styles = array(
				'list_style'           => 'none',
				'title_color'          => '#333333',
				'title_background'     => 'transparent',
				'title_font_size'      => '15',
				'title_margin_top'     => '0',
				'title_margin_right'   => '0',
				'title_margin_bottom'  => '0',
				'title_margin_left'    => '0',
				'title_padding_top'    => '0',
				'title_padding_right'  => '0',
				'title_padding_bottom' => '0',
				'title_padding_left'   => '0',
				'item_background'      => 'transparent',
				'item_color'           => '#333333',
				'item_font_size'       => '15',
				'item_margin_top'      => '0',
				'item_margin_right'    => '0',
				'item_margin_bottom'   => '0',
				'item_margin_left'     => '20',
				'item_padding_top'     => '0',
				'item_padding_right'   => '0',
				'item_padding_bottom'  => '0',
				'item_padding_left'    => '0',
				'show_icons'           => 'yes',
			);

			$plan_list_styles = wp_parse_args( $plan_list_styles, $default_plan_list_styles );

			$t_args = array(
				'default_plan_list_styles' => $default_plan_list_styles,
				'plan_list_styles'         => $plan_list_styles,
			);

			wc_get_template( '/metaboxes/shortcode_plan_item_list_style.php', $t_args, YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
		}

		/**
		 * Save Plan item order metabox
		 *
		 * @param int $post_id the id of the post
		 *
		 * @return void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function save_plan_item_order_metabox( $post_id ) {
			if ( 'yith-wcmbs-plan' === get_post_type( $post_id ) ) {
				/* Save Ordered items */
				if ( ! empty( $_POST['_yith_wcmbs_plan_items'] ) ) {
					update_post_meta( $post_id, '_yith_wcmbs_plan_items', $_POST['_yith_wcmbs_plan_items'] );
				}

				/* Save hided items */
				if ( ! empty( $_POST['_yith_wcmbs_hidden_item_ids'] ) ) {
					$hidden = $_POST['_yith_wcmbs_hidden_item_ids'];
					$hidden = array_map( 'intval', $hidden );
					update_post_meta( $post_id, '_yith_wcmbs_hidden_item_ids', $hidden );
				} else {
					update_post_meta( $post_id, '_yith_wcmbs_hidden_item_ids', array() );
				}

				/* Save List Style */
				if ( ! empty( $_POST['_yith_wcmbs_plan_list_styles'] ) ) {
					$meta = $_POST['_yith_wcmbs_plan_list_styles'];

					if ( ! isset( $meta['show_icons'] ) ) {
						$meta['show_icons'] = 'no';
					} else {
						$meta['show_icons'] = 'yes';
					}

					update_post_meta( $post_id, '_yith_wcmbs_plan_list_styles', $meta );
				}
			}
		}


		/**
		 * Register Plans custom post type with options metabox
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public
		function post_type_register() {

			$labels = array(
				'menu_name'          => _x( 'Membership', 'plugin name in admin WP menu', 'yith-woocommerce-membership' ),
				'all_items'          => __( 'Plans', 'yith-woocommerce-membership' ),
				'name'               => __( 'Plans', 'yith-woocommerce-membership' ),
				'singular_name'      => __( 'Plan', 'yith-woocommerce-membership' ),
				'add_new'            => __( 'Add Plan', 'yith-woocommerce-membership' ),
				'add_new_item'       => __( 'New Plan', 'yith-woocommerce-membership' ),
				'edit_item'          => __( 'Edit Plan', 'yith-woocommerce-membership' ),
				'view_item'          => __( 'View Plan', 'yith-woocommerce-membership' ),
				'not_found'          => __( 'Plan not found', 'yith-woocommerce-membership' ),
				'not_found_in_trash' => __( 'Plan not found in trash', 'yith-woocommerce-membership' ),
			);

			$capability_type = 'plan';
			$caps            = array(
				'edit_post'              => "edit_{$capability_type}",
				'read_post'              => "read_{$capability_type}",
				'delete_post'            => "delete_{$capability_type}",
				'edit_posts'             => "edit_{$capability_type}s",
				'edit_others_posts'      => "edit_others_{$capability_type}s",
				'publish_posts'          => "publish_{$capability_type}s",
				'read_private_posts'     => "read_private_{$capability_type}s",
				'read'                   => "read",
				'delete_posts'           => "delete_{$capability_type}s",
				'delete_private_posts'   => "delete_private_{$capability_type}s",
				'delete_published_posts' => "delete_published_{$capability_type}s",
				'delete_others_posts'    => "delete_others_{$capability_type}s",
				'edit_private_posts'     => "edit_private_{$capability_type}s",
				'edit_published_posts'   => "edit_published_{$capability_type}s",
				'create_posts'           => "edit_{$capability_type}s",
				'manage_posts'           => "manage_{$capability_type}s",
			);

			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => 'plan',
				'capabilities'        => $caps,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'menu_icon'           => 'dashicons-groups',
				'supports'            => array( 'title' ),
			);

			register_post_type( 'yith-wcmbs-plan', $args );

			$args = array(
				'label'    => __( 'Membership Plan Options', 'yith-woocommerce-membership' ),
				'pages'    => 'yith-wcmbs-plan',
				'class'    => yith_set_wrapper_class(),
				'context'  => 'normal',
				'priority' => 'high',
				'tabs'     => apply_filters( 'yith_wcmbs_plan_tabs_metabox_settings', array(
					'items-in-plan' => array(
						'label'  => __( 'Planned Items', 'yith-woocommerce-membership' ),
						'fields' => array(
							'product-cats' => array(
								'label'    => __( 'Allowed Product Categories', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select which product categories members will have access to.', 'yith-woocommerce-membership' ),
								'type'     => 'fl-product-cats',
								'multiple' => true,
								'std'      => array(),
							),
							'post-cats'    => array(
								'label'    => __( 'Allowed Post Categories', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select which post categories members will have access to.', 'yith-woocommerce-membership' ),
								'type'     => 'fl-post-cats',
								'multiple' => true,
								'std'      => array(),
							),
							'product-tags' => array(
								'label'    => __( 'Allowed Product Tags', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select which product tags members will have access to.', 'yith-woocommerce-membership' ),
								'type'     => 'ajax-terms',
								'data'     => array(
									'taxonomy'    => 'product_tag',
									'placeholder' => __( 'Search Product Tags', 'yith-woocommerce-membership' ),
									'allow-clear' => true,
								),
								'multiple' => true,
								'std'      => array(),
							),
							'post-tags'    => array(
								'label'    => __( 'Allowed Post Tags', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select which post tags members will have access to.', 'yith-woocommerce-membership' ),
								'type'     => 'ajax-terms',
								'data'     => array(
									'taxonomy'    => 'post_tag',
									'placeholder' => __( 'Search Post Tags', 'yith-woocommerce-membership' ),
									'allow-clear' => true,
								),
								'multiple' => true,
								'std'      => array(),
							),
							'add-products' => array(
								'label'    => __( 'Include Products', 'yith-woocommerce-membership' ),
								'desc'     => apply_filters( 'yith_wcmbs_add_products_in_plan_description', __( 'Select the products to include in the membership.', 'yith-woocommerce-membership' ) ),
								'type'     => 'ajax-products',
								'multiple' => true,
								'value'    => '',
								'no_value' => true,
							),
							'add-posts'    => array(
								'label'    => __( 'Include Posts', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select the posts to include in the membership.', 'yith-woocommerce-membership' ),
								'type'     => 'ajax-posts',
								'multiple' => true,
								'std'      => '',
								'value'    => '',
								'no_value' => true,

							),
							'add-pages'    => array(
								'label'    => __( 'Include Pages', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select the pages to include in the membership.', 'yith-woocommerce-membership' ),
								'type'     => 'ajax-posts',
								'multiple' => true,
								'data'     => array(
									'placeholder' => __( 'Search Pages', 'yith-woocommerce-membership' ),
									'post_type'   => 'page',
								),
								'value'    => '',
								'no_value' => true,

							),
						),
					),
					'settings'      => array(
						'label'  => __( 'Settings', 'yith-woocommerce-membership' ),
						'fields' => array(
							'membership-product'          => array(
								'label'    => __( 'Select membership product', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select the product that users have to purchase to get a membership access', 'yith-woocommerce-membership' ),
								'type'     => 'ajax-products',
								'data'     => array(
									'action'                                    => 'woocommerce_json_search_products_and_variations',
									'security'                                  => wp_create_nonce( 'search-products' ),
									'yith_wcmbs_search_for_membership_products' => true,
								),
								'multiple' => true,
							),
							'membership-duration'         => array(
								'label' => __( 'Duration', 'yith-woocommerce-membership' ),
								'desc'  => __( 'Select the duration of the membership plan (in days). Set 0 for unlimited duration.', 'yith-woocommerce-membership' ),
								'type'  => 'number',
								'min'   => 0,
								'std'   => 0,
							),
							'linked-plans'                => array(
								'label'    => __( 'Linked Plans', 'yith-woocommerce-membership' ),
								'desc'     => __( 'Select the plans you want to assign to this plan.', 'yith-woocommerce-membership' ),
								'type'     => 'ajax-posts',
								'multiple' => true,
								'data'     => array(
									'placeholder' => __( 'Search Plans', 'yith-woocommerce-membership' ),
									'post_type'   => 'yith-wcmbs-plan',
								),
								'std'      => array(),
							),
							'show-contents-in-my-account' => array(
								'label'       => __( 'Show contents in Membership History', 'yith-woocommerce-membership' ),
								'desc-inline' => __( 'Select this option if you want to show contents in Membership History.', 'yith-woocommerce-membership' ),
								'type'        => 'checkbox',
								'std'         => 'no',
							),
						),
					),
				) ),
			);
			if ( YITH_WCMBS_Products_Manager()->is_allowed_download() ) {
				$extra_fields = array(
					'initial-download-limit'     => array(
						'label' => __( 'Download number for first term', 'yith-woocommerce-membership' ),
						'desc'  => __( 'Select the initial limit for downloads (for the first term). Set to -1 if you want it will be equal to Download number for every period.', 'yith-woocommerce-membership' ),
						'type'  => 'number',
						'min'   => - 1,
						'std'   => - 1,
					),
					'download-limit'             => array(
						'label' => __( 'Download number for every term', 'yith-woocommerce-membership' ),
						'desc'  => __( 'Select the limit for downloads for every term. Set 0 for unlimited. Please note: Download number will not be applied to current memberships that are not provided with it!', 'yith-woocommerce-membership' ),
						'type'  => 'number',
						'min'   => 0,
						'std'   => 0,
					),
					'download-limit-period'      => array(
						'label' => __( 'Define Download term', 'yith-woocommerce-membership' ),
						'desc'  => __( 'Select the limit term for downloads.', 'yith-woocommerce-membership' ),
						'type'  => 'number',
						'min'   => 1,
						'std'   => 1,
					),
					'download-limit-period-unit' => array(
						'label'   => __( 'Download term unit', 'yith-woocommerce-membership' ),
						'desc'    => __( 'Select the limit term unit for downloads.', 'yith-woocommerce-membership' ),
						'type'    => 'select',
						'options' => array(
							'days'   => __( 'days', 'yith-woocommerce-membership' ),
							'weeks'  => __( 'weeks', 'yith-woocommerce-membership' ),
							'months' => __( 'months', 'yith-woocommerce-membership' ),
							'years'  => __( 'years', 'yith-woocommerce-membership' ),
						),
						'std'     => 'days',
					),
					'can-be-accumulated'         => array(
						'label'       => __( 'Can be accumulated', 'yith-woocommerce-membership' ),
						'desc-inline' => __( 'Select this option if you want that downloads can be accumulated.', 'yith-woocommerce-membership' ),
						'type'        => 'checkbox',
						'std'         => 'no',
					),
				);

				$args['tabs']['settings']['fields'] = array_merge( $args['tabs']['settings']['fields'], $extra_fields );
			}

			$metabox = YIT_Metabox( 'yith-wcmbs-metabox-settings' );
			$metabox->init( $args );
		}

		public function add_metabox_for_alternative_content() {
			$post_types = array_diff( YITH_WCMBS_Manager()->post_types, array( 'attachment' ) );

			$args    = array(
				'label'    => __( 'Alternative Content', 'yith-woocommerce-membership' ),
				'pages'    => $post_types,
				'context'  => 'normal',
				'priority' => 'default',
				'tabs'     => apply_filters( 'yith_wcmbs_alternative_content_metabox', array(
					'settings' => array( //tab
										 'label'  => __( 'Alternative Content', 'yith-woocommerce-membership' ),
										 'fields' => array(
											 'alternative-content' => array(
												 'label' => __( 'Alternative Content', 'yith-woocommerce-membership' ),
												 'desc'  => __( 'Select the plans you want to assign to this plan.', 'yith-woocommerce-membership' ),
												 'type'  => 'textarea-editor',
												 'std'   => '',
											 ),
										 ),
					),
				) ),
			);
			$metabox = YIT_Metabox( 'yith-wcmbs-alternative-content-metabox' );
			$metabox->init( $args );
		}

		/**
		 * get links for yit panel sidebar
		 *
		 * @return array
		 */
		public function get_panel_sidebar_links() {
			return array(
				array(
					'url'   => 'http://www.yithemes.com',
					'title' => __( 'Your Inspiration Themes', 'yith-woocommerce-membership' ),
				),
				array(
					'url'   => $this->doc_url,
					'title' => __( 'Plugin Documentation', 'yith-woocommerce-membership' ),
				),
				array(
					'url'   => $this->doc_url . 'help-faq',
					'title' => __( 'FAQ', 'yith-woocommerce-membership' ),
				),
				array(
					'url'   => 'http://plugins.yithemes.com/yith-woocommerce-membership/product/membership/?preview',
					'title' => __( 'Live Demo', 'yith-woocommerce-membership' ),
				),
				array(
					'url'   => 'https://yithemes.com/my-account/support/dashboard/',
					'title' => __( 'Support Desk', 'yith-woocommerce-membership' ),
				),
			);
		}

		/**
		 * Renders the Restrict Access Metabox for all post types
		 *
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function restrict_access_metabox_render( $post ) {
			$restrict_access_plan       = get_post_meta( $post->ID, '_yith_wcmbs_restrict_access_plan', true );
			$restrict_access_plan_delay = get_post_meta( $post->ID, '_yith_wcmbs_plan_delay', true );

			$t_args = array(
				'post'                 => $post,
				'restrict_access_plan' => $restrict_access_plan,
				'plan_delay'           => $restrict_access_plan_delay,
			);

			wc_get_template( '/metaboxes/restrict_access.php', $t_args, YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
		}

		/**
		 * Save meta for the metabox containing the chart table
		 *
		 * @param       $post_id
		 *
		 * @since       1.0.0
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function save_metaboxes( $post_id ) {
			if ( isset( $_POST['_yith_wcmbs_restrict_access_edit_post'] ) ) {
				if ( ! empty( $_POST['_yith_wcmbs_restrict_access_plan'] ) ) {
					$restrict_access_plan_meta  = ! empty( $_POST['_yith_wcmbs_restrict_access_plan'] ) ? $_POST['_yith_wcmbs_restrict_access_plan'] : array();
					$restrict_access_plan_delay = ! empty( $_POST['_yith_wcmbs_plan_delay'] ) ? $_POST['_yith_wcmbs_plan_delay'] : array();

					/*if ( !empty( $restrict_access_plan_delay ) ) {
						foreach ( $restrict_access_plan_delay as $key => $value ) {
							if ( empty( $value ) )
								unset( $restrict_access_plan_delay[ $key ] );
						}
					}*/

					update_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan', $restrict_access_plan_meta );
					update_post_meta( $post_id, '_yith_wcmbs_plan_delay', $restrict_access_plan_delay );
				} else {
					delete_post_meta( $post_id, '_yith_wcmbs_restrict_access_plan' );
					delete_post_meta( $post_id, '_yith_wcmbs_plan_delay' );
				}
			}
		}

		/**
		 * Do actions duplicate_membership
		 *
		 * @since       1.0.0
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function admin_action_duplicate_membership() {
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] = 'duplicate_membership' ) {
				if ( isset( $_REQUEST['plan_id'] ) ) {
					$id_membership = absint( $_REQUEST['plan_id'] );
					$this->duplicate_membership( $id_membership );

					$admin_edit_url = admin_url( 'edit.php?post_type=yith-wcmbs-plan' );
					wp_redirect( $admin_edit_url );
				}
			}
		}

		/**
		 * Add Duplicate action link in Membership Plans LIST
		 *
		 * @param array   $actions An array of row action links. Defaults are
		 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
		 *                         'Delete Permanently', 'Preview', and 'View'.
		 * @param WP_Post $post    The post object.
		 *
		 * @return array
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since       1.0.0
		 */
		public function add_duplicate_action_on_plans( $actions, $post ) {
			if ( $post->post_type == 'yith-wcmbs-plan' && $post->post_status == 'publish' ) {
				$admin_edit_url            = admin_url();
				$link                      = add_query_arg( array(
																'action'  => 'duplicate_membership',
																'plan_id' => $post->ID,
															), $admin_edit_url );
				$action_name               = __( 'Duplicate', 'yith-woocommerce-membership' );
				$actions['duplicate_plan'] = "<a href='{$link}'>{$action_name}</a>";
			}

			return $actions;
		}

		/**
		 * Duplicate a membership plan
		 *
		 * @param int $post_id the id of the membership plan
		 *
		 * @since       1.0.0
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function duplicate_membership( $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post || $post->post_type != 'yith-wcmbs-plan' ) {
				return;
			}

			$new_post = array(
				'post_status' => $post->post_status,
				'post_type'   => 'yith-wcmbs-plan',
				'post_title'  => $post->post_title . ' - ' . __( 'Copy', 'yith-woocommerce-membership' ),
			);

			$meta_to_save = array(
				'_membership-product',
				'_membership-duration',
				'_product-cats',
				'_post-cats',
				'_product-tags',
				'_post-tags',
				'_linked-plans',
				'_show-contents-in-my-account',
				'_initial-download-limit',
				'_download-limit',
				'_download-limit-period',
				'_download-limit-period-unit',
				'_can-be-accumulated',
				'_yith_wcmbs_plan_items',
				'_yith_wcmbs_plan_list_styles',
				'_yith_wcmbs_hidden_item_ids',
			);

			$new_post_id = wp_insert_post( $new_post );

			foreach ( $meta_to_save as $key ) {
				$value = get_post_meta( $post_id, $key, true );
				update_post_meta( $new_post_id, $key, $value );
			}
		}

		/**
		 * Add Shortcode column in Membership Plan List
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_shortcode_columns_in_plan( $columns ) {
			$columns['yith_wcmbs_shortcode'] = __( 'Membership Item Shortcode', 'yith-woocommerce-membership' );

			if ( isset( $columns['date'] ) ) {
				$date_text = $columns['date'];
				unset( $columns['date'] );
				$columns['date'] = $date_text;
			}

			return $columns;
		}

		/**
		 * Render Shortcode column in Membership Plan List
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function render_shortcode_columns_in_plan( $column, $post_id ) {
			if ( $column == 'yith_wcmbs_shortcode' ) {
				$to_copy_id = 'yith-wcmbs-copy-to-clipboard-' . $post_id;
				$copy_text  = __( 'Copy to clipboard', 'yith-woocommerce-booking' );

				echo "<code id='{$to_copy_id}'>[membership_items plan={$post_id}]</code>";
				echo "<span class='dashicons dashicons-admin-page yith-wcmbs-copy-to-clipboard tips' data-selector-to-copy='#{$to_copy_id}' data-tip='{$copy_text}'></span>";
			}
		}

		/**
		 * Manage columns column in Membership List
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function manage_membership_list_columns( $columns ) {
			$date_text = $columns['date'];
			unset( $columns['cb'] );
			unset( $columns['date'] );
			unset( $columns['title'] );

			$columns['ID']         = __( 'ID', 'yith-woocommerce-membership' );
			$columns['status']     = __( 'Status', 'yith-woocommerce-membership' );
			$columns['user']       = __( 'User', 'yith-woocommerce-membership' );
			$columns['order']      = __( 'Order', 'yith-woocommerce-membership' );
			$columns['start_date'] = __( 'Starting Date', 'yith-woocommerce-membership' );
			$columns['end_date']   = __( 'Expiration Date', 'yith-woocommerce-membership' );

			$columns = array_merge( $columns, apply_filters( 'yith_wcmbs_membership_custom_columns', array() ) );

			$columns['date'] = $date_text;

			return $columns;
		}

		/**
		 * Manage sortable columns in Membership List
		 *
		 * @param $sortable_columns
		 *
		 * @return array
		 * @since  1.2.1
		 */
		public function manage_membership_sortable_columns( $sortable_columns ) {
			$sortable_columns['start_date'] = 'start_date';
			$sortable_columns['end_date']   = 'end_date';

			return $sortable_columns;
		}

		/**
		 * Membership Orderby for sorting in WP List
		 *
		 * @param $query
		 */
		public function membership_orderby( $query ) {
			if ( ! is_admin() ) {
				return;
			}

			$orderby = $query->get( 'orderby' );

			switch ( $orderby ) {
				case 'start_date':
					$query->set( 'meta_key', '_start_date' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
				case 'end_date':
					$query->set( 'meta_key', '_end_date' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
			}
		}

		/**
		 * Membership Search
		 *
		 * @param WP_Query $wp
		 *
		 * @since 1.3.13
		 */
		public function membership_search( $wp ) {
			global $pagenow;

			if ( 'edit.php' != $pagenow || empty( $wp->query_vars['s'] ) || $wp->query_vars['post_type'] !== YITH_WCMBS_Membership_Helper()->post_type_name ) {
				return;
			}

			if ( ! is_numeric( $_GET['s'] ) && function_exists( 'wc_order_search' ) ) {
				$order_ids = wc_order_search( wc_clean( wp_unslash( $_GET['s'] ) ) );
				$user_ids  = get_users( array( 'search' => wc_clean( wp_unslash( $_GET['s'] ) ), 'fields' => 'ids' ) );

				if ( $order_ids || $user_ids ) {
					// Remove "s" - we don't want to search membership name.
					unset( $wp->query_vars['s'] );

					// so we know we're doing this.
					$wp->query_vars['membership_search'] = true;

					// let's search for order ids or user ids
					$wp->query_vars['meta_query']['relation'] = 'OR';
					if ( $order_ids ) {
						$wp->query_vars['meta_query'][] = array( 'key' => '_order_id', 'value' => $order_ids, 'compare' => 'IN' );
					}
					if ( $user_ids ) {
						$wp->query_vars['meta_query'][] = array( 'key' => '_user_id', 'value' => $user_ids, 'compare' => 'IN' );
					}
				}

			}
		}

		/**
		 * Change the label when searching for memberships.
		 *
		 * @param mixed $query
		 *
		 * @return string
		 * @since 1.3.15
		 */
		public function membership_search_label( $query ) {
			global $pagenow, $typenow;

			if ( 'edit.php' != $pagenow ) {
				return $query;
			}

			if ( $typenow != YITH_WCMBS_Membership_Helper()->post_type_name ) {
				return $query;
			}

			if ( ! get_query_var( 'membership_search' ) ) {
				return $query;
			}

			return wp_unslash( $_GET['s'] );
		}

		/**
		 * Render columns in Membership List
		 *
		 * @access public
		 *
		 * @param $column
		 * @param $post_id
		 *
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function render_membership_list_columns( $column, $post_id ) {
			$membership = new YITH_WCMBS_Membership( $post_id );
			if ( $membership ) {
				switch ( $column ) {
					case 'ID':
						$title = $membership->get_plan_title();
						echo "<strong>#{$post_id}</strong> $title";
						break;
					case 'status':
						$status_text = $membership->get_status_text();
						echo "<span class='yith-wcmbs-membership-status {$membership->status}'>$status_text</span>";
						break;
					case 'user':
						$user_id = $membership->user_id;
						$user    = get_user_by( 'id', $user_id );
						if ( ! $user ) {
							break;
						}
						$edit_link = get_edit_user_link( $user_id );
						echo "<a href='{$edit_link}'>{$user->user_login}</a>";
						break;
					case 'order':
						$order_id = $membership->order_id;
						if ( $order_id > 0 ) {
							$the_order = wc_get_order( $order_id );
							if ( $the_order && $user_id = yit_get_prop( $the_order, 'user_id', true ) ) {
								$user_info = get_userdata( $user_id );
							}

							if ( ! empty( $user_info ) ) {
								$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

								if ( $user_info->first_name || $user_info->last_name ) {
									$username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
								} else {
									$username .= esc_html( ucfirst( $user_info->display_name ) );
								}

								$username .= '</a>';

								$username = apply_filters( 'yith_wcmbs_username_anchor_membership_list_table', $username, $user_info );

							} else {
								$billing_first_name = yit_get_prop( $the_order, 'billing_first_name' );
								$billing_last_name  = yit_get_prop( $the_order, 'billing_last_name' );
								if ( $the_order && ( $billing_first_name || $billing_last_name ) ) {
									$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), $billing_first_name, $billing_last_name ) );
								} else {
									$username = __( 'Guest', 'woocommerce' );
								}
							}
							if ( $the_order ) {
								printf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username );
							} else {
								printf( __( '%s [Order not found]', 'yith-woocommerce-membership' ), '<strong>#' . absint( $order_id ) . '</strong>' );
							}

							$billing_email = yit_get_prop( $the_order, 'billing_email' );
							if ( $the_order && $billing_email ) {
								echo '&ensp;'; // added space to prevent issues on copy and paste
								echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
							}

						} else {
							_e( 'created by Admin', 'yith-woocommerce-membership' );
						}
						break;
					case 'start_date':
						echo $membership->get_formatted_date( 'start_date' );
						break;
					case 'end_date':
						echo $membership->get_formatted_date( 'end_date' );
						break;
				}

				do_action( 'yith_wcmbs_membership_render_custom_columns', $column, $post_id, $membership );
			}
		}


		/**
		 * Remove Membership Actions
		 *
		 * @param array   $actions An array of row action links. Defaults are
		 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
		 *                         'Delete Permanently', 'Preview', and 'View'.
		 * @param WP_Post $post    The post object.
		 *
		 * @return array
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since       1.0.0
		 */
		public function membership_row_actions( $actions, $post ) {
			if ( $post->post_type == YITH_WCMBS_Membership_Helper()->post_type_name ) {
				$membership = new YITH_WCMBS_Membership( $post->ID );
				if ( $membership->user_id != 0 && ! apply_filters( 'yith_wcmbs_enable_membership_trash', false ) ) {
					unset( $actions['trash'] );
				}

				unset( $actions['inline hide-if-no-js'] );
				unset( $actions['view'] );
			}

			return $actions;
		}
	}
}