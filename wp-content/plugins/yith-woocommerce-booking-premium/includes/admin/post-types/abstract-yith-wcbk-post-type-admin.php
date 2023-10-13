<?php
/**
 * Class YITH_WCBK_Post_Type_Admin
 * Abstract class to handle post types on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Person_Type_Post_Type_Admin
	 */
	abstract class YITH_WCBK_Post_Type_Admin extends YITH_Post_Type_Admin {

		/**
		 * YITH_WCBK_Post_Type_Admin constructor.
		 */
		protected function __construct() {
			parent::__construct();

			if ( $this->post_type && $this->is_enabled() ) {
				$settings          = $this->get_post_type_settings();
				$title_placeholder = $settings['title_placeholder'] ?? '';
				$title_description = $settings['title_description'] ?? '';
				$updated_messages  = $settings['updated_messages'] ?? array();

				if ( $title_placeholder ) {
					add_filter( 'enter_title_here', array( $this, 'set_title_placeholder' ) );
				}

				if ( $title_description ) {
					add_action( 'edit_form_after_title', array( $this, 'add_title_description' ) );
				}

				if ( $updated_messages ) {
					add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
				}

				add_filter( 'yith_wcbk_booking_admin_screen_ids', array( $this, 'add_booking_admin_screen_ids' ), 10, 1 );
				add_filter( 'admin_body_class', array( $this, 'add_admin_body_classes' ), 10, 1 );
				add_action( 'dbx_post_sidebar', array( $this, 'print_save_button_in_edit_page' ), 10, 1 );
				add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );

				if ( $this->use_single_column_in_edit_page() ) {
					add_action( 'admin_head', array( $this, 'disable_screen_layout_columns' ) );
					add_filter( "get_user_option_screen_layout_{$this->post_type}", array( $this, 'force_single_column_screen_layout' ), 10, 1 );
				}
			}
		}

		/**
		 * Return true to use only one column in edit page.
		 *
		 * @return bool
		 */
		protected function use_single_column_in_edit_page() {
			return true;
		}

		/**
		 * Disable the screen layout columns, by setting it to 1 column.
		 */
		public function disable_screen_layout_columns() {
			if ( $this->is_post_type_edit() ) {
				get_current_screen()->add_option(
					'layout_columns',
					array(
						'max'     => 1,
						'default' => 1,
					)
				);
			}
		}

		/**
		 * Force using the single column layout.
		 *
		 * @return int
		 */
		public function force_single_column_screen_layout() {
			return 1;
		}

		/**
		 * Initialize the WP List handlers.
		 */
		public function init_wp_list_handlers() {
			parent::init_wp_list_handlers();
			if ( $this->should_wp_list_handlers_be_loaded() ) {
				$this->maybe_redirect_to_main_list();
			}
		}

		/**
		 * Return the post_type settings placeholder.
		 *
		 * @return array Array of settings: title_placeholder, title_description, updated_messages, hide_views.
		 */
		protected function get_post_type_settings() {
			return array();
		}

		/**
		 * Return true if you want to use the object. False otherwise.
		 *
		 * @return bool
		 */
		protected function use_object() {
			return false;
		}

		/**
		 * Has the months' dropdown enabled?
		 *
		 * @return bool
		 */
		protected function has_months_dropdown_enabled() {
			return false;
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_columns( $columns ) {
			if ( isset( $columns['date'] ) ) {
				unset( $columns['date'] );
			}

			$columns['actions'] = __( 'Actions', 'yith-booking-for-woocommerce' );

			return $columns;
		}

		/**
		 * Define bulk actions.
		 *
		 * @param array $actions Existing actions.
		 *
		 * @return array
		 */
		public function define_bulk_actions( $actions ) {
			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			if ( isset( $actions['trash'] ) ) {
				unset( $actions['trash'] );
			}
			$post_type_object = get_post_type_object( $this->post_type );

			if ( current_user_can( $post_type_object->cap->delete_posts ) ) {
				$actions['delete'] = __( 'Delete', 'yith-booking-for-woocommerce' );
			}

			return $actions;
		}

		/**
		 * Render Actions column
		 */
		protected function render_actions_column() {
			$actions = yith_plugin_fw_get_default_post_actions( $this->post_id, array( 'delete-directly' => true ) );

			yith_plugin_fw_get_action_buttons( $actions, true );
		}

		/**
		 * Show blank slate.
		 *
		 * @param string $which String which table-nav is being shown.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			if ( $this->get_blank_state_params() && $post_type === $this->post_type && 'bottom' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'] );
				unset( $counts['trash'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$this->render_blank_state();

				echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom > *, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; display: block } </style>';
			}
		}

		/**
		 * Set the "title" placeholder.
		 *
		 * @param string $placeholder Title placeholder.
		 *
		 * @return string
		 */
		public function set_title_placeholder( $placeholder ) {
			global $post_type;

			$settings          = $this->get_post_type_settings();
			$title_placeholder = $settings['title_placeholder'] ?? '';

			if ( $post_type === $this->post_type && $title_placeholder ) {
				$placeholder = $title_placeholder;
			}

			return $placeholder;
		}

		/**
		 * Add title description
		 */
		public function add_title_description() {
			global $post_type;

			$settings          = $this->get_post_type_settings();
			$title_description = $settings['title_description'] ?? '';

			if ( $post_type === $this->post_type && $title_description ) {
				?>
				<div id="yith-wcbk-cpt-title__wrapper">
					<div id="yith-wcbk-cpt-title__field"></div>
					<div id="yith-wcbk-cpt-title__description">
						<?php echo wp_kses_post( $title_description ); ?>
					</div>
				</div>

				<script type="text/javascript">
					( function () {
						document.getElementById( 'yith-wcbk-cpt-title__field' ).appendChild( document.getElementById( 'title' ) );
						document.getElementById( 'titlewrap' ).appendChild( document.getElementById( 'yith-wcbk-cpt-title__wrapper' ) );
					} )();
				</script>
				<?php
			}
		}

		/**
		 * Change messages when a post type is updated.
		 *
		 * @param array $messages Array of messages.
		 *
		 * @return array
		 */
		public function post_updated_messages( $messages ) {
			$settings         = $this->get_post_type_settings();
			$updated_messages = $settings['updated_messages'] ?? array();

			$messages[ $this->post_type ] = $updated_messages;

			return $messages;
		}

		/**
		 * Add booking admin screen IDs to allow including styles and scripts correctly.
		 *
		 * @param array $screen_ids The screen IDs.
		 *
		 * @return array
		 */
		public function add_booking_admin_screen_ids( $screen_ids ) {
			$screen_ids[] = $this->post_type;
			$screen_ids[] = 'edit-' . $this->post_type;

			return $screen_ids;
		}

		/**
		 * Is the post type list?
		 *
		 * @return bool
		 */
		public function is_post_type_list() {
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id = ! ! $screen ? $screen->id : false;

			return 'edit-' . $this->post_type === $screen_id;
		}

		/**
		 * Is the post type edit page?
		 *
		 * @return bool
		 */
		public function is_post_type_edit() {
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id = ! ! $screen ? $screen->id : false;

			return $screen_id === $this->post_type;
		}

		/**
		 * Add classes to body.
		 *
		 * @param string $classes The CSS classes.
		 *
		 * @return string
		 */
		public function add_admin_body_classes( $classes ) {
			$custom_classes               = array();
			$settings                     = $this->get_post_type_settings();
			$hide_views                   = $settings['hide_views'] ?? false;
			$hide_new_post_button_in_list = $settings['hide_new_post_button_in_list'] ?? false;

			if ( $this->is_post_type_list() ) {
				$custom_classes[] = 'yith-wcbk-post-type';
				$custom_classes[] = 'yith-wcbk-post-type-list';

				if ( $hide_views ) {
					$custom_classes[] = 'yith-wcbk-post-type-list--hide-views';
				}

				if ( $hide_new_post_button_in_list ) {
					$custom_classes[] = 'yith-wcbk-post-type-list--hide-new-post-button';
				}
			}

			if ( $this->is_post_type_edit() ) {
				$custom_classes[] = 'yith-wcbk-post-type';
				$custom_classes[] = 'yith-wcbk-post-type-edit';
			}

			if ( $custom_classes ) {
				$custom_classes = array_unique( $custom_classes );

				$classes .= ' ' . implode( ' ', $custom_classes ) . ' ';
			}

			return $classes;
		}

		/**
		 * Print save button in edit page.
		 *
		 * @param WP_Post $post The post.
		 */
		public function print_save_button_in_edit_page( $post ) {
			if ( ! ! $post && isset( $post->post_type ) && $post->post_type === $this->post_type ) {
				global $post_id;
				$is_updating      = ! ! $post_id;
				$save_text        = __( 'Save', 'yith-booking-for-woocommerce' );
				$post_type_object = get_post_type_object( $this->post_type );
				$single           = $post_type_object->labels->singular_name ?? '';

				if ( $single ) {
					// translators: %s is the post type name (eg: Booking, Resource, Person, etc.).
					$save_text = sprintf( __( 'Save %s', 'yith-booking-for-woocommerce' ), strtolower( $single ) );
				}
				?>
				<div class="yith-wcbk-post-type__actions yith-plugin-ui">
					<?php if ( $is_updating ) : ?>
						<button id="yith-wcbk-post-type__save" class="yith-plugin-fw__button--primary yith-plugin-fw__button--xl"><?php echo esc_html( $save_text ); ?></button>
					<?php else : ?>
						<input id="yith-wcbk-post-type__save" type="submit" class="yith-plugin-fw__button--primary yith-plugin-fw__button--xl" name="publish" value="<?php echo esc_html( $save_text ); ?>">
					<?php endif; ?>

					<a id="yith-wcbk-post-type__float-save" class="yith-plugin-fw__button--primary yith-plugin-fw__button--xl yith-plugin-fw-animate__appear-from-bottom"><?php echo esc_html( $save_text ); ?></a>
				</div>
				<?php
			}
		}

		/**
		 * Remove publish box from edit post page.
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		}

		/**
		 * Redirect to main list if the current view is 'trash' and there are no post.
		 */
		protected function maybe_redirect_to_main_list() {
			$post_status = wc_clean( wp_unslash( $_REQUEST['post_status'] ?? 'any' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'trash' === $post_status ) {
				$counts = (array) wp_count_posts( $this->post_type );
				unset( $counts['auto-draft'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$args = array(
					'post_type' => $this->post_type,
					'deleted'   => isset( $_GET['deleted'] ) ? wc_clean( wp_unslash( $_GET['deleted'] ) ) : null, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				);

				$list_url = add_query_arg( $args, admin_url( 'edit.php' ) );

				wp_safe_redirect( $list_url );
				exit();
			}
		}

	}
}
