<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

! defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since    1.0.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Admin
		 */
		protected static $instance;

		/**
		 * @var $_panel YIT_Plugin_Panel_WooCommerce Object
		 */
		protected $_panel;

		/**
		 * @var string The panel page.
		 */
		protected $_panel_page = 'yith_wcmbs_panel';

		/**
		 * @var YITH_WCMBS_Admin_Assets
		 */
		public $assets;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Admin
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
			$this->assets = YITH_WCMBS_Admin_Assets::get_instance();
			YITH_WCMBS_Admin_Profile();
			YITH_WCMBS_Advanced_Administration();
			YITH_WCMBS_Messages_Manager_Admin();
			YITH_WCMBS_Legacy_Elements::get_instance();
			YITH_WCMBS_Admin_Meta_Boxes::get_instance();

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			foreach ( YITH_WCMBS_Manager()->post_types as $post_type ) {
				add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_columns' ) );
			}
			add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
			add_action( 'manage_pages_custom_column', array( $this, 'custom_columns' ), 10, 2 );

			// Bulk Edit for adding Membership Restrict Access
			add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_render' ), 10, 2 );
			add_action( 'wp_ajax_yith_wcmbs_save_bulk_edit', array( $this, 'save_bulk_edit' ) );

			add_action( 'admin_action_duplicate_membership', array( $this, 'admin_action_duplicate_membership' ) );
			add_filter( 'post_row_actions', array( $this, 'add_duplicate_action_on_plans' ), 10, 2 );

			add_filter( 'manage_media_columns', array( $this, 'add_columns' ) );
			add_action( 'manage_media_custom_column', array( $this, 'custom_columns' ), 10, 2 );

			/* Add Membership Items shortcodes in Membership Plan List */
			add_filter( 'manage_yith-wcmbs-plan_posts_columns', array( $this, 'add_shortcode_columns_in_plan' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'render_shortcode_columns_in_plan' ), 10, 2 );
			add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ), 10, 2 );

			/* Manage Membership List columns */
			add_filter( 'manage_' . YITH_WCMBS_Post_Types::$membership . '_posts_columns', array( $this, 'manage_membership_list_columns' ) );
			add_action( 'manage_' . YITH_WCMBS_Post_Types::$membership . '_posts_custom_column', array( $this, 'render_membership_list_columns' ), 10, 2 );
			add_filter( 'manage_edit-' . YITH_WCMBS_Post_Types::$membership . '_sortable_columns', array( $this, 'manage_membership_sortable_columns' ), 10, 1 );
			add_action( 'pre_get_posts', array( $this, 'membership_orderby' ) );
			add_action( 'parse_query', array( $this, 'membership_search' ) );
			add_filter( 'get_search_query', array( $this, 'membership_search_label' ) );
			add_action( 'edit_form_top', array( $this, 'print_back_to_wp_list_button' ) );

			// Remove Actions for Membership
			add_filter( 'post_row_actions', array( $this, 'membership_row_actions' ), 10, 2 );
			// Remove bulk actions for Membership List
			add_filter( 'bulk_actions-edit-' . YITH_WCMBS_Post_Types::$membership, '__return_empty_array' );
			// Insert Membership Status Filters in Membership WP LIST
			add_filter( 'views_edit-' . YITH_WCMBS_Post_Types::$membership, array( $this, 'insert_membership_status_filters' ) );
			add_action( 'pre_get_posts', array( $this, 'filter_memberships' ) );

			// prevent delete or trash plans
			add_action( 'wp_trash_post', array( $this, 'prevent_delete_or_trash_plans' ) );
			add_action( 'before_delete_post', array( $this, 'prevent_delete_or_trash_plans' ) );
			// remove restricted access for all items in plan
			add_action( 'before_delete_post', array( $this, 'remove_restricted_access_on_delete_plan' ), 11 );

			// Delete Transients
			add_action( 'save_post', array( YITH_WCMBS_Manager(), 'delete_transients' ) );
			add_action( 'yit_panel_wc_after_update', array( $this, 'delete_transient_after_update_options' ) );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMBS_DIR . '/' . basename( YITH_WCMBS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

			add_action( 'yith_plugin_fw_get_field_after', array( $this, 'customize_plugin_fw_field_after_text' ), 10, 1 );

			// Render blank state for Memberships, Plans and Messages CPT
			add_action( 'manage_posts_extra_tablenav', array( $this, 'maybe_render_blank_state' ) );
		}

		/**
		 * Print the "Back to WP List" button in Edit Post pages
		 *
		 * @since 1.4.0
		 */
		public function print_back_to_wp_list_button() {
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id = $screen ? $screen->id : false;
			global $post_type_object;

			if ( $screen_id && $post_type_object && in_array( $screen_id, array( YITH_WCMBS_Post_Types::$membership, YITH_WCMBS_Post_Types::$plan, YITH_WCMBS_Post_Types::$thread ) ) ) {
				$url  = add_query_arg( array( 'post_type' => $screen_id ), admin_url( 'edit.php' ) );
				$name = get_post_type_labels( $post_type_object )->name;
				// translators: %s is the name of the menu to back to (example Back to Membership Plans)
				$back_to = sprintf( __( 'Back to %s', 'yith-woocommerce-membership' ), $name );
				?>
				<div id='yith-wcmbs-back-to-wp-list__wrapper'>
					<a id='yith-wcmbs-back-to-wp-list' class='yith-wcmbs-back-to-wp-list' href='<?php echo esc_url( $url ) ?>'><?php echo esc_html( $back_to ) ?></a>
				</div>
				<script type="text/javascript">
					( function () {
						var wrap   = document.querySelector( '.wrap' ),
							backTo = document.querySelector( '#yith-wcmbs-back-to-wp-list__wrapper' );

						wrap.insertBefore( backTo, wrap.childNodes[ 0 ] );
					} )();
				</script>
				<?php

			}
		}

		/**
		 * Add column in CPT table list
		 *
		 * @param array $columns The columns
		 *
		 * @return mixed
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_columns( $columns ) {
			$columns['yith_wcmbs_restrict_access'] = '<span class="dashicons dashicons-lock"></span>';

			return $columns;
		}

		/**
		 * Add content in custom column in product table list
		 *
		 * @param string $column  The column name.
		 * @param int    $post_id The post ID.
		 *
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function custom_columns( $column, $post_id ) {
			if ( 'yith_wcmbs_restrict_access' === $column ) {
				$plans = yith_wcmbs_get_plans_meta_for_post( $post_id );

				if ( $plans ) {
					$plan_titles = array_filter( array_map( 'get_the_title', $plans ) );
					$plan_titles = array_map( 'esc_html', $plan_titles );
					$plans_html  = "\n" . implode( "\n", $plan_titles );
					$tip         = esc_attr__( 'Included in memberships', 'yith-woocommerce-membership' ) . ': ' . $plans_html;
					echo '<span class="dashicons dashicons-groups tips" data-tip="' . nl2br( esc_attr( $tip ) ) . '"></span>';
				}
			}
		}


		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCMBS_PREMIUM' ), YITH_WCMBS_SLUG );
		}

		/**
		 * plugin_row_meta
		 * add the action links to plugin admin page
		 *
		 * @param $row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 *
		 * @return   array
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			$init = defined( 'YITH_WCMBS_FREE_INIT' ) ? YITH_WCMBS_FREE_INIT : YITH_WCMBS_INIT;

			if ( $init === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WCMBS_SLUG;
				$row_meta_args['is_premium'] = defined( 'YITH_WCMBS_PREMIUM' );
			}

			return $row_meta_args;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$tabs = array(
				'membership' => _x( 'Membership', 'Tab title in plugin settings panel', 'yith-woocommerce-membership' ),
				'settings'   => _x( 'General Options', 'Tab title in plugin settings panel', 'yith-woocommerce-membership' ),
			);
			$tabs = apply_filters( 'yith_wcmbs_settings_admin_tabs', $tabs );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'YITH WooCommerce Membership',
				'menu_title'       => 'Membership',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YITH_WCMBS_DIR . '/plugin-options',
				'plugin_slug'      => YITH_WCMBS_SLUG,
				'help_tab'         => array(
					'main_video' => array(
						'desc' => _x( 'Check this video to learn how to <b>create a site with content reserved to members:</b>', 'Help tab - Video title', 'yith-woocommerce-membership' ),
						'url'  => array(
							'en' => 'https://www.youtube.com/embed/EuXQ15H36D8',
							'it' => 'https://www.youtube.com/embed/fIKf8I3UavI',
							'es' => 'https://www.youtube.com/embed/MG73C_HwfP4',
						),
					),
					'playlists'  => array(
						'en' => 'https://www.youtube.com/watch?v=EuXQ15H36D8&list=PLDriKG-6905l61xx-y9SjBS95kreJB7Z2&ab_channel=YITH',
						'it' => 'https://www.youtube.com/watch?v=an2b_4VN110&list=PL9c19edGMs0-MbWRgu2hetaG2VokWedn1&ab_channel=YITHITALIA',
						'es' => 'https://www.youtube.com/watch?v=MG73C_HwfP4&list=PL9Ka3j92PYJPZXUoG-_V_AkY7ooLtQ_3k&ab_channel=YITHESPA%C3%91A',
					),
					'hc_url'    => 'https://support.yithemes.com/hc/en-us/categories/360003469197-YITH-WOOCOMMERCE-MEMBERSHIP',
				),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCMBS_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * filter Memberships by status [in Membership WP List page]
		 *
		 * @param WP_Query $query
		 */
		public function filter_memberships( $query ) {
			$is_membership               = isset( $query->query['post_type'] ) && $query->query['post_type'] == YITH_WCMBS_Post_Types::$membership;
			$is_membership_status_filter = isset( $_REQUEST['membership_status'] ) && in_array( $_REQUEST['membership_status'], array_keys( yith_wcmbs_get_membership_statuses() ) );

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

			$membership_statuses = yith_wcmbs_get_membership_statuses();

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
						array(
							'key'     => '_yith_wcmbs_restrict_access_plan',
							'value'   => serialize( (string) $post_id ),
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
						$restrict_access_plan = yith_wcmbs_get_plans_meta_for_post( $item_id );
						$restrict_access_plan = array_diff( $restrict_access_plan, array( $post_id ) );
						yith_wcmbs_update_plans_meta_for_post( $item_id, $restrict_access_plan );
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
						$old_plans = yith_wcmbs_get_plans_meta_for_post( $post_id );
						$new_plans = array_merge( $old_plans, array_diff( $plans, $old_plans ) );
						yith_wcmbs_update_plans_meta_for_post( $post_id, $new_plans );
					}
				}
			}
			die();
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
				'_post-cats',
				'_product-cats',
				'_post-tags',
				'_product-tags',
				'_enable_purchasing',
				'_membership-product',
				'_membership-duration-enabled',
				'_membership-duration',
				'_linked-plans',
				'_show-contents-in-my-account',
				'_initial-download-limit',
				'_initial-download-limit-enabled',
				'_download-limit',
				'_download-limit-period',
				'_download-limit-period-unit',
				'_can-be-accumulated',
				'_download_limit_type',
				'_default_credits_for_product',
				'_yith_wcmbs_plan_items',
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
			if ( 'yith_wcmbs_shortcode' === $column ) {
				$to_copy_id = 'yith-wcmbs-copy-to-clipboard-' . $post_id;

				echo '<code id="' . esc_attr( $to_copy_id ) . '">[membership_items plan=' . absint( $post_id ) . ']</code>';
				echo '<span class="dashicons dashicons-admin-page yith-wcmbs-copy-to-clipboard tips" data-selector-to-copy="#' . esc_attr( $to_copy_id ) . '" data-tip="' . esc_attr__( 'Copy to clipboard', 'yith-woocommerce-membership' ) . '"></span>';
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

			if ( 'edit.php' != $pagenow || empty( $wp->query_vars['s'] ) || $wp->query_vars['post_type'] !== YITH_WCMBS_Post_Types::$membership ) {
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

			if ( $typenow != YITH_WCMBS_Post_Types::$membership ) {
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
			$membership = yith_wcmbs_get_membership( $post_id );
			if ( $membership ) {
				switch ( $column ) {
					case 'ID':
						$title = $membership->get_plan_title();
						echo sprintf( '<strong>%s</strong> %s', absint( $post_id ), esc_html( $title ) );
						break;
					case 'status':
						echo '<span class="yith-wcmbs-membership-status ' . esc_attr( $membership->status ) . '">' . esc_html( $membership->get_status_text() ) . '</span>';
						break;
					case 'user':
						$user_id = $membership->user_id;
						$user    = get_user_by( 'id', $user_id );
						if ( ! $user ) {
							break;
						}
						$edit_link = get_edit_user_link( $user_id );
						echo '<a href="' . esc_url( $edit_link ) . '">' . esc_html( $user->user_login ) . '</a>';
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
								echo wp_kses_post( sprintf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username ) );
							} else {
								echo wp_kses_post( sprintf( __( '%s [Order not found]', 'yith-woocommerce-membership' ), '<strong>#' . absint( $order_id ) . '</strong>' ) );
							}

							$billing_email = yit_get_prop( $the_order, 'billing_email' );
							if ( $the_order && $billing_email ) {
								echo '&ensp;'; // added space to prevent issues on copy and paste.
								echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
							}

						} else {
							esc_html_e( 'created by Admin', 'yith-woocommerce-membership' );
						}
						break;
					case 'start_date':
						echo esc_html( $membership->get_formatted_date( 'start_date' ) );
						break;
					case 'end_date':
						echo esc_html( $membership->get_formatted_date( 'end_date' ) );
						break;
				}

				do_action( 'yith_wcmbs_membership_render_custom_columns', $column, $post_id, $membership );
			}
		}

		/**
		 * Customize Plugin FW fields to display a text after the field
		 *
		 * @since 1.4.0
		 */
		public function customize_plugin_fw_field_after_text( $field ) {
			if ( isset( $field['yith-wcmbs-after-text'] ) ) {
				echo $field['yith-wcmbs-after-text']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Set Default hidden columns in WP Lists
		 *
		 * @param array     $hidden
		 * @param WP_Screen $screen
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function default_hidden_columns( $hidden, $screen ) {
			if ( 'edit-' . YITH_WCMBS_Post_Types::$plan === $screen->id ) {
				$hidden[] = 'date';
			}

			return $hidden;
		}

		/**
		 * Render blank state for Memberships, Plans and Messages CPT
		 *
		 * @param string $which String which tablenav is being shown.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			$attrs = array(
				YITH_WCMBS_Post_Types::$membership           => array(
					'icon'    => YITH_WCMBS_ASSETS_PATH . '/icons/membership.svg',
					'message' => __( 'You have no membership yet!', 'yith-woocommerce-membership' ),
					'cta'     => '',
					'cta_url' => '',
				),
				YITH_WCMBS_Post_Types::$plan                 => array(
					'icon'    => YITH_WCMBS_ASSETS_PATH . '/icons/plans.svg',
					'message' => __( 'You have no membership plan yet!', 'yith-woocommerce-membership' ),
					'cta'     => __( 'Create a new membership plan', 'yith-woocommerce-membership' ),
					'cta_url' => add_query_arg( array( 'post_type' => YITH_WCMBS_Post_Types::$plan ), admin_url( 'post-new.php' ) ),
				),
				YITH_WCMBS_Post_Types::$thread               => array(
					'icon'    => YITH_WCMBS_ASSETS_PATH . '/icons/messages.svg',
					'message' => __( 'You have no message yet!', 'yith-woocommerce-membership' ) . '<br />' . __( 'Here you will see messages sent by members', 'yith-woocommerce-membership' ),
					'cta'     => '',
					'cta_url' => '',
				),
				YITH_WCMBS_Post_Types::$alternative_contents => array(
					'icon'    => YITH_WCMBS_ASSETS_PATH . '/icons/alternative-content.svg',
					'message' => __( 'You have no alternative content block yet!', 'yith-woocommerce-membership' ),
					'cta'     => __( 'Create new alternative content block', 'yith-woocommerce-membership' ),
					'cta_url' => add_query_arg( array( 'post_type' => YITH_WCMBS_Post_Types::$alternative_contents ), admin_url( 'post-new.php' ) ),
				),
			);

			$post_types = array_keys( $attrs );

			if ( in_array( $post_type, $post_types ) && 'bottom' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}


				yith_wcmbs_get_view( 'list-table-blank-state.php', $attrs[ $post_type ] );

				echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; } </style>';
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
			if ( $post->post_type == YITH_WCMBS_Post_Types::$membership ) {
				$membership = yith_wcmbs_get_membership( $post->ID );
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

/**
 * Unique access to instance of YITH_WCMBS_Admin class
 *
 * @return YITH_WCMBS_Admin
 */
function YITH_WCMBS_Admin() {
	return YITH_WCMBS_Admin::get_instance();
}