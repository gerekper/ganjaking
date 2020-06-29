<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_POS_Store_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_POS_Store_Post_Type_Admin
	 * Main Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Store_Post_Type_Admin {

		/** @var string post type */
		public $post_type;

		/** @var YITH_POS_Store_Post_Type_Admin */
		protected static $_instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Store_Post_Type_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS_Store_Post_Type_Admin constructor
		 */
		public function __construct() {
			$this->post_type = YITH_POS_Post_Types::$store;

			add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
			add_action( 'admin_init', array( $this, 'add_meta_boxes' ), 1 );


			add_filter( 'get_user_option_screen_layout_' . $this->post_type, '__return_true' );

			add_filter( 'admin_body_class', array( $this, 'add_wizard_body_class' ) );
			add_filter( 'yit_before_metaboxes_tab', array( $this, 'print_wizard_nav' ) );
			add_filter( 'yit_after_metaboxes_tab', array( $this, 'print_wizard_pagination' ) );

			add_action( 'yith_pos_store_metabox_registers_list', array( $this, 'print_registers_list_in_metabox' ) );

			// List Table
			add_action( 'all_admin_notices', array( $this, 'create_your_first_store_page' ), 15 );
			add_filter( 'manage_' . YITH_POS_Post_Types::$store . '_posts_columns', array(
				$this,
				'manage_list_columns'
			) );
			add_action( 'manage_' . YITH_POS_Post_Types::$store . '_posts_custom_column', array(
				$this,
				'render_list_columns'
			), 10, 2 );
			add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ), 10, 2 );
			add_filter( 'bulk_actions-edit-' . YITH_POS_Post_Types::$store, array(
				$this,
				'manage_bulk_actions'
			), 10, 1 );
			add_filter( 'post_row_actions', array( $this, 'manage_row_actions' ), 10, 2 );

			// delete registers when deleting a Store
			add_action( 'delete_post', array( $this, 'delete_registers_when_deleting_the_store' ), 10, 1 );

			add_action( 'save_post', array( $this, 'remove_user_roles_before_saving_the_store' ), 5, 1 );
			add_action( 'save_post', array( $this, 'add_user_roles_after_saving_the_store' ), 100, 1 );

			// Filters
			add_action( 'restrict_manage_posts', array( $this, 'render_filters' ), 10, 1 );
			add_action( 'pre_get_posts', array( $this, 'filter_stores' ), 10, 1 );
		}


		/**
		 * Filter stores
		 *
		 * @param   WP_Query  $query
		 */
		public function filter_stores( $query ) {
			if ( $query->is_main_query() && isset( $query->query['post_type'] ) && YITH_POS_Post_Types::$store === $query->query['post_type'] ) {
				$meta_query = ! ! $query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array();

				if ( ! empty( $_REQUEST['enabled'] ) ) {
					$enabled = 'yes' === $_REQUEST['enabled'];
					if ( $enabled ) {
						$meta_query[] = array(
							'relation' => 'OR',
							array( 'key' => '_enabled', 'value' => 'yes' ),
							array( 'key' => '_enabled', 'compare' => 'NOT EXISTS' ),
						);
					} else {
						$meta_query[] = array( 'key' => '_enabled', 'value' => 'no' );
					}
					$query->set( 'meta_query', $meta_query );
				}
			}
		}

		/**
		 * render filters
		 *
		 * @param $post_type
		 */
		public function render_filters( $post_type ) {
			if ( YITH_POS_Post_Types::$store === $post_type ) {
				$selected_enabled = isset( $_REQUEST['enabled'] ) ? $_REQUEST['enabled'] : '';

				$enabled_statuses = array(
					'yes' => __( 'Yes', 'yith-point-of-sale-for-woocommerce' ),
					'no'  => __( 'No', 'yith-point-of-sale-for-woocommerce' ),
				);

				echo "<select name='enabled'>";
				echo "<option value=''>" . __( 'Filter by enabled status', 'yith-point-of-sale-for-woocommerce' ) . "</option>";
				foreach ( $enabled_statuses as $id => $name ) {
					echo "<option value='{$id}' " . selected( $id, $selected_enabled, false ) . ">$name</option>";
				}
				echo "</select>";
			}
		}

		/**
		 * Add Cashier and Manager roles to users after saving the store
		 *
		 * @param $post_id
		 */
		public function add_user_roles_after_saving_the_store( $post_id ) {
			if ( YITH_POS_Post_Types::$store === get_post_type( $post_id ) ) {
				$store = yith_pos_get_store( $post_id );
				if ( $store ) {
					foreach ( $store->get_managers() as $manager_id ) {
						yith_pos_maybe_add_user_role( $manager_id, 'yith_pos_manager' );
					}
					foreach ( $store->get_cashiers() as $cashier_id ) {
						yith_pos_maybe_add_user_role( $cashier_id, 'yith_pos_cashier' );
					}
				}
			}
		}

		public function remove_user_roles_before_saving_the_store( $post_id ) {
			if ( YITH_POS_Post_Types::$store === get_post_type( $post_id ) ) {

				$store = yith_pos_get_store( $post_id );

				if ( isset( $_REQUEST['yit_metaboxes']['_managers'] ) ) {
					$old_managers = $store->get_managers();
					$new_managers = $_REQUEST['yit_metaboxes']['_managers'];
					$diff         = array_diff( $old_managers, $new_managers );
					if ( $diff ) {
						foreach ( $diff as $manager_id ) {
							yith_pos_maybe_remove_user_role( $manager_id, 'manager', $post_id );
						}
					}
				}

				if ( isset( $_REQUEST['yit_metaboxes']['_cashiers'] ) ) {
					$old_cashiers = $store->get_cashiers();
					$new_cashiers = $_REQUEST['yit_metaboxes']['_cashiers'];
					$diff         = array_diff( $old_cashiers, $new_cashiers );

					if ( $diff ) {
						foreach ( $diff as $cashier_id ) {
							yith_pos_maybe_remove_user_role( $cashier_id, 'cashier', $post_id );
						}
					}
				}
			}
		}

		/**
		 * Delete all registers of the store if the store is deleted
		 *
		 * @param $post_id
		 */
		public function delete_registers_when_deleting_the_store( $post_id ) {
			if ( YITH_POS_Post_Types::$store === get_post_type( $post_id ) ) {
				$store = yith_pos_get_store( $post_id );
				if ( $store ) {
					$store->delete_all_registers();
				}
			}
		}

		/**
		 * Print the "create your first store" page if there aren't any store.
		 */
		public function create_your_first_store_page() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( $screen && 'edit-' . YITH_POS_Post_Types::$store === $screen->id ) {
				$statuses  = array( 'publish', 'draft', 'trash' );
				$one_store = yith_pos_get_stores( array( 'posts_per_page' => 1, 'post_status' => $statuses ) );
				if ( ! $one_store ) {
					yith_pos_get_view( 'panel/create-your-first-store.php' );
				}
			}
		}

		/**
		 * return the current page of the wizard stored in the Store
		 *
		 * @return int|mixed
		 */
		private function _get_wizard_current_page() {
			static $current_page;
			if ( ! isset( $current_page ) ) {
				global $post_id;
				$current_page = get_post_meta( $post_id, '_wizard_current_page', true );
				$current_page = ! ! $current_page ? absint( $current_page ) : 1;
			}

			return $current_page;
		}

		/**
		 * Add the wizard class to body if you're creating a new store or editing a draft one
		 *
		 * @param   string  $class
		 *
		 * @return string
		 */
		public function add_wizard_body_class( $class ) {
			if ( yith_pos_is_store_wizard() ) {
				$class .= ' yith-pos-store-wizard ';
			}

			return $class;
		}

		/**
		 * Print the wizard nav
		 */
		public function print_wizard_nav() {
			if ( yith_pos_is_store_wizard() ) {
				$args = array(
					'current_page' => $this->_get_wizard_current_page(),
				);
				yith_pos_get_view( 'panel/store-wizard-nav.php', $args );
			}
		}

		/**
		 * Print the wizard pagination
		 */
		public function print_wizard_pagination() {
			if ( yith_pos_is_store_wizard() ) {
				$args = array(
					'current_page' => $this->_get_wizard_current_page(),
				);
				yith_pos_get_view( 'panel/store-wizard-pagination.php', $args );
			}
		}


		/**
		 * Remove publish box from edit booking
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		}

		/**
		 * Add meta boxes to edit the store
		 */
		public function add_meta_boxes() {
			$args             = require_once( YITH_POS_DIR . '/plugin-options/metabox/store-options.php' );
			$metabox_template = YIT_Metabox( 'yith-pos-store' );
			$metabox_template->init( $args );
		}

		/**
		 * Return the title of section
		 *
		 * @param   int   $step
		 * @param   bool  $publish
		 *
		 * @return mixed|void
		 */
		public function get_section_title( $step = 1, $publish = true ) {
			$titles = array(
				'1' => array(
					'title' => $publish ? __( 'Info', 'yith-point-of-sale-for-woocommerce' ) : __( 'Step 1: Store Info', 'yith-point-of-sale-for-woocommerce' ),
					'desc'  => $publish ? __( 'General store info', 'yith-point-of-sale-for-woocommerce' ) : __( 'Enter the store details including the address, contact information and social accounts', 'yith-point-of-sale-for-woocommerce' )
				),
				'2' => array(
					'title' => $publish ? __( 'Employees', 'yith-point-of-sale-for-woocommerce' ) : __( 'Step 2: Employees', 'yith-point-of-sale-for-woocommerce' ),
					'desc'  => $publish ? __( 'Set the employees (managers and cashiers) for this store<br/>You can choose your cashiers from the users already registered in this site or create new cashiers', 'yith-point-of-sale-for-woocommerce' ) : __( 'Assign a manager to manage all registers and add cashiers to each of them', 'yith-point-of-sale-for-woocommerce' )
				),
				'3' => array(
					'title' => $publish ? __( 'Registers', 'yith-point-of-sale-for-woocommerce' ) : __( 'Step 3: Registers', 'yith-point-of-sale-for-woocommerce' ),
					'desc'  => $publish ? __( 'General store info', 'yith-point-of-sale-for-woocommerce' ) : __( 'Create at least one register for this store.<br/>You can set the visibility of each register to hide/show to some users', 'yith-point-of-sale-for-woocommerce' )
				),
				'4' => array(
					'title' => __( 'Resume', 'yith-point-of-sale-for-woocommerce' ),
					'desc'  => __( 'Just one more step! Click the "Save Store" button to save your store. To make changes you can go back now or edit these from "Stores" tab.', 'yith-point-of-sale-for-woocommerce' )
				)
			);

			$title = isset( $titles[ $step ] ) ? sprintf( '<div class="yith-pos-store-metabox-title">%s</div><div class="yith-pos-store-metabox-subtitle">%s</div>', $titles[ $step ]['title'], $titles[ $step ]['desc'] ) : '';

			return apply_filters( 'yith_post_admin_section_title', $title, $step, $publish );
		}

		public function print_registers_list_in_metabox() {
			global $post_id;
			$args = array(
				'store_id'  => $post_id,
				'registers' => yith_pos_get_registers_by_store( $post_id )
			);
			yith_pos_get_view( 'metabox/store-registers-list.php', $args );
		}

		/**
		 * Manage the columns of the Store List
		 *
		 * @param   array  $columns
		 *
		 * @return array
		 */
		public function manage_list_columns( $columns ) {
			$date_text = $columns['date'];
			unset( $columns['date'] );
			unset( $columns['title'] );

			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );

			$new_columns['title']     = __( 'Store Name', 'yith-point-of-sale-for-woocommerce' );
			$new_columns['employees'] = __( 'Employees', 'yith-point-of-sale-for-woocommerce' );
			$new_columns['registers'] = __( 'Registers', 'yith-point-of-sale-for-woocommerce' );
			$new_columns['enabled']   = __( 'Enabled', 'yith-point-of-sale-for-woocommerce' );

			$new_columns = array_merge( $new_columns, $columns );

			$new_columns['date'] = $date_text;

			return $new_columns;
		}

		/**
		 * Render the columns of the Store List
		 *
		 * @param   array  $column
		 * @param   int    $post_id
		 */
		public function render_list_columns( $column, $post_id ) {
			$store = yith_pos_get_store( $post_id );
			switch ( $column ) {
				case 'employees':
					$managers = $store->get_managers();
					$cashiers = $store->get_cashiers();

					if ( $managers ) {
						echo "<strong>" . __( 'Managers', 'yith-point-of-sale-for-woocommerce' ) . ":</strong>";
						yith_pos_compact_list( array_map( 'yith_pos_get_employee_name', $managers ) );
					}

					if ( $cashiers ) {
						echo "<strong>" . __( 'Cashiers', 'yith-point-of-sale-for-woocommerce' ) . ":</strong>";
						yith_pos_compact_list( array_map( 'yith_pos_get_employee_name', $cashiers ) );
					}
					break;

				case 'registers':
					$registers = $store->get_register_ids();

					if ( $registers ) {
						yith_pos_compact_list( array_map( 'yith_pos_get_register_name', $registers ) );
					}
					break;
				case 'enabled':
					if ( $store->is_published() ) {
						echo "<div class='yith-plugin-ui'>";
						echo yith_plugin_fw_get_field( array(
							'type'  => 'onoff',
							'id'    => 'yith-pos-store-toggle-enabled-' . $store->get_id(),
							'class' => 'yith-pos-store-toggle-enabled',
							'value' => $store->is_enabled() ? 'yes' : 'no',
							'data'  => array(
								'store-id' => $store->get_id(),
								'security' => wp_create_nonce( 'store-toggle-enabled' )
							)
						) );
						echo "</div>";
					} else {
						$post_status     = $store->get_post_status();
						$post_status_obj = get_post_status_object( $post_status );
						echo "<div class='yith-pos-post-status yith-pos-post-status--{$post_status}'>{$post_status_obj->label}</div>";
					}
					break;
			}
		}

		/**
		 * Set the default hidden columns of the Store List
		 *
		 * @param   array      $hidden
		 * @param   WP_Screen  $screen
		 *
		 * @return array
		 */
		public function default_hidden_columns( $hidden, $screen ) {
			if ( 'edit-' . YITH_POS_Post_Types::$store === $screen->id ) {
				$hidden[] = 'date';
			}

			return $hidden;
		}

		/**
		 * Manage the bulk actions in the Store List
		 *
		 * @param   array  $actions
		 *
		 * @return array
		 */
		public function manage_bulk_actions( $actions ) {
			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			return $actions;
		}

		/**
		 * Manage the row actions in the Store List
		 *
		 * @param   array    $actions
		 * @param   WP_Post  $post
		 *
		 * @return array
		 */
		public function manage_row_actions( $actions, $post ) {
			if ( YITH_POS_Post_Types::$store === get_post_type( $post ) ) {
				if ( isset( $actions['inline hide-if-no-js'] ) ) {
					unset( $actions['inline hide-if-no-js'] );
				}

				$show_registers_link = add_query_arg( array(
					'post_type' => YITH_POS_Post_Types::$register,
					'store'     => $post->ID
				), admin_url( 'edit.php' ) );

				$actions['show-registers'] = "<a href='$show_registers_link'>" . __( 'Show Registers', 'yith-point-of-sale-for-woocommerce' ) . "</a>";

			}

			return $actions;
		}
	}

}
