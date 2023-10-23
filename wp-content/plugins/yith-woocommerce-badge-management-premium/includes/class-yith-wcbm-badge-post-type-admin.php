<?php
/**
 * Class YITH_WCBM_Badge_Post_Type_Admin
 *
 * Handles the "Badge" post type on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Classes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Badge_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBM_Badge_Post_Type_Admin
	 */
	class YITH_WCBM_Badge_Post_Type_Admin extends YITH_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = 'yith-wcbm-badge';

		/**
		 * Return false since I don't want to use the object.
		 *
		 * @return bool
		 */
		protected function use_object() {
			return false;
		}

		/**
		 * Retrieve an array of parameters for blank state.
		 *
		 * @return array{
		 * @type string $icon       The icon. (uses YITH Font)
		 * @type string $icon_class The icon Class.
		 * @type string $icon_url   The icon URL.
		 * @type string $message    The message to be shown.
		 * @type array  $cta        The call-to-action button args.
		 * }
		 */
		protected function get_blank_state_params() {
			$badges = (array) wp_count_posts( YITH_WCBM_Post_Types::$badge );
			if ( isset( $badges['publish'] ) && ! $badges['publish'] ) {
				echo '<style> .edit-php.post-type-yith-wcbm-badge .wp-heading-inline + a{ display: none; } #posts-filter{ max-width: 100% !important; }</style>';
			}

			return array(
				'icon_url' => YITH_WCBM_ASSETS_URL . 'images/icons/empty-badges.svg',
				'message'  => __( 'You have no badges created yet.<br>Build now your first one!', 'yith-woocommerce-badges-management' ),
				'cta'      => array(
					'title' => _x( 'Create Badge', 'Empty state CTA button text', 'yith-woocommerce-badges-management' ),
				),
			);
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

			if ( isset( $columns['title'] ) ) {
				$columns['title'] = _x( 'Name', '[ADMIN] Badge Rules table column name', 'yith-woocommerce-badges-management' );
			}
			$columns['yith_wcbm_preview'] = _x( 'Preview', '[ADMIN] Badges table column name', 'yith-woocommerce-badges-management' );
			$columns['yith_wcbm_actions'] = _x( 'Enabled', '[ADMIN] Badges table column name', 'yith-woocommerce-badges-management' );

			return $columns;
		}

		/**
		 * Define Bulk actions for badges
		 *
		 * @param array $actions The Badge bulk Actions.
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
			$actions['enable']  = _x( 'Enable', 'Badge action', 'yith-woocommerce-badges-management' );
			$actions['disable'] = _x( 'Disable', 'Badge action', 'yith-woocommerce-badges-management' );
			$actions['delete']  = _x( 'Delete', 'Badge action', 'yith-woocommerce-badges-management' );

			return $actions;
		}

		/**
		 * Handle bulk actions
		 *
		 * @param string $redirect_to The redirect url.
		 * @param string $action      The Action.
		 * @param array  $ids         The badge ids.
		 */
		public function handle_bulk_actions( $redirect_to, $action, $ids ) {
			switch ( $action ) {
				case 'enable':
					foreach ( $ids as $id ) {
						$badge = yith_wcbm_get_badge_object( $id );
						if ( $badge && ! $badge->is_enabled() ) {
							$badge->set_enabled( 'yes' );
							$badge->save();
						}
					}
					break;
				case 'disable':
					foreach ( $ids as $id ) {
						$badge = yith_wcbm_get_badge_object( $id );
						if ( $badge && $badge->is_enabled() ) {
							$badge->set_enabled( 'no' );
							$badge->save();
						}
					}
					break;
			}

			return $redirect_to;
		}

		/**
		 * Render Actions column
		 */
		protected function render_yith_wcbm_actions_column() {
			$post_id = $this->post_id;
			$actions = array();

			if ( current_user_can( 'edit_post', $post_id ) ) {
				$enable_field = array(
					'type'  => 'onoff',
					'class' => ' yith-wcbm-enable-badge',
					'id'    => 'yith-wcbm-enable-badge-' . $post_id,
					'data'  => array( 'badge-id' => $post_id ),
					'value' => wc_bool_to_string( yith_wcbm_is_badge_enabled( $post_id ) ),
				);
				yith_plugin_fw_get_field( $enable_field, true );

				$actions['edit']  = array(
					'type'   => 'action-button',
					'title'  => _x( 'Edit', 'Action button title', 'yith-woocommerce-badges-management' ),
					'action' => 'edit',
					'url'    => get_edit_post_link( $post_id ),
				);
				$actions['clone'] = array(
					'type'   => 'action-button',
					'title'  => _x( 'Clone', 'Action button title', 'yith-woocommerce-badges-management' ),
					'action' => 'view',
					'icon'   => 'clone',
					'url'    => add_query_arg(
						array(
							'action'   => 'yith_wcbm_clone_badge',
							'post'     => $post_id,
							'security' => wp_create_nonce( 'yith_wcbm_clone_badge' ),
						),
						admin_url()
					),
				);
			}
			if ( current_user_can( 'delete_post', $post_id ) ) {
				$actions['delete'] = array(
					'type'   => 'action-button',
					'title'  => _x( 'Delete', 'Action button title', 'yith-woocommerce-badges-management' ),
					'action' => 'delete',
					'icon'   => 'trash',
					'url'    => get_delete_post_link( $post_id, '', true ),
				);
			}

			yith_plugin_fw_get_action_buttons( $actions, true );
		}

		/**
		 * Render Actions column
		 */
		protected function render_yith_wcbm_preview_column() {
			$badge = yith_wcbm_get_badge_object( $this->post_id );
			if ( $badge ) {
				echo '<div class="yith-wcbm-preview-column-container">';
				$badge->display( 'preview' );
				echo '</div>';
			}
		}

	}
}

return YITH_WCBM_Badge_Post_Type_Admin::instance();
