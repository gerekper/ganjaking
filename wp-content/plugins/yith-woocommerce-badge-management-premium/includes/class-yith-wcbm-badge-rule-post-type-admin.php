<?php
/**
 * Class YITH_WCBM_Badge_Rule_Post_Type_Admin
 *
 * Handles the "Badge" post type on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Classes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBM_Badge_Rule_Post_Type_Admin
	 */
	class YITH_WCBM_Badge_Rule_Post_Type_Admin extends YITH_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = 'ywcbm-badge-rule';

		/**
		 * Return true since I want to use badge rule object.
		 *
		 * @return bool
		 */
		protected function use_object() {
			return true;
		}

		/**
		 * Initialize Badge Rule Object.
		 *
		 * @param int $post_id The post ID.
		 */
		protected function prepare_row_data( $post_id ) {
			if ( empty( $this->object ) || $this->object->get_id() !== $post_id ) {
				$this->object = yith_wcbm_get_badge_rule( $post_id );
			}
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
			$badge_rules = (array) wp_count_posts( YITH_WCBM_Post_Types_Premium::$badge_rule );
			if ( isset( $badge_rules['publish'] ) && ! $badge_rules['publish'] ) {
				echo '<style> .edit-php.post-type-ywcbm-badge-rule .wp-heading-inline + a{ display: none; } #posts-filter{ max-width: 100% !important; }</style>';
			}

			$params = array(
				'icon_url' => YITH_WCBM_ASSETS_URL . 'images/icons/empty-rules.svg',
				'message'  => __( 'You have no badge rules yet.<br>Set now the first one!', 'yith-woocommerce-badges-management' ),
				'cta'      => array(
					'title' => _x( 'Set Rule', 'Empty state CTA button text', 'yith-woocommerce-badges-management' ),
				),
			);

			if ( yith_wcbm_update_is_running() ) {
				$params['icon_url'] = YITH_WCBM_ASSETS_URL . 'images/spinner.gif';
				$params['message']  = __( 'We are generating the badge rules.<br>Wait a few minutes to see the rules, or create a new one.', 'yith-woocommerce-badges-management' );
				echo '<style> .edit-php.post-type-ywcbm-badge-rule .yith-plugin-fw__list-table-blank-state img.yith-plugin-fw__list-table-blank-state__icon{width: 60px;}</style>';
			}

			return $params;
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
			$bulk_actions_to_add = array(
				'enable'  => __( 'Enable', 'yith-woocommerce-badges-management' ),
				'disable' => __( 'Disable', 'yith-woocommerce-badges-management' ),
				'delete'  => __( 'Delete', 'yith-woocommerce-badges-management' ),
			);

			return array_merge( $actions, $bulk_actions_to_add );
		}

		/**
		 * Handle bulk actions.
		 *
		 * @param string $redirect_to URL to redirect to.
		 * @param string $action      Action name.
		 * @param array  $ids         List of ids.
		 *
		 * @return string
		 */
		public function handle_bulk_actions( $redirect_to, $action, $ids ) {
			if ( in_array( $action, array( 'enable', 'disable' ), true ) ) {
				foreach ( $ids as $id ) {
					$rule = yith_wcbm_get_badge_rule( $id );
					if ( $rule ) {
						$rule->set_enabled( wc_bool_to_string( 'enable' === $action ) );
						$rule->save();
					}
				}
			}

			return esc_url_raw( $redirect_to );
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

			$columns['yith_wcbm_type']    = _x( 'Type', '[ADMIN] Badge Rules table column name', 'yith-woocommerce-badges-management' );
			$columns['yith_wcbm_actions'] = _x( 'Enabled', '[ADMIN] Badge Rules table column name', 'yith-woocommerce-badges-management' );

			return $columns;
		}

		/**
		 * Render Type Column
		 */
		protected function render_yith_wcbm_type_column() {
			$rule_types = yith_wcbm_get_badge_rules_types();
			$rule       = $this->object;
			$rule_type  = $rule->get_type( 'edit' );
			if ( array_key_exists( $rule_type, $rule_types ) && isset( $rule_types[ $rule_type ]['title'] ) ) {
				$rule_type = $rule_types[ $rule_type ]['title'];
			} else {
				$integration_rules_type = array(
					'auction'         => 'YITH Auctions for WooCommerce Premium',
					'dynamic-pricing' => 'YITH WooCommerce Dynamic Pricing and Discounts Premium',
				);
				if ( array_key_exists( $rule_type, $integration_rules_type ) ) {
					// translators: %s is the name of the plugin to activate to use that badge rule.
					$rule_type = '<span class="yith-wcbm-invalid-badge-rule-type">' . sprintf( __( '%s is required', 'yith-woocommerce-badges-management' ), $integration_rules_type[ $rule_type ] ) . '</span>';
				} else {
					$rule_type = '';
				}
			}
			echo wp_kses_post( $rule_type );
		}

		/**
		 * Render Actions column
		 */
		protected function render_yith_wcbm_actions_column() {
			$rule_types = yith_wcbm_get_badge_rules_types();
			$rule       = $this->object;
			$actions    = array();
			if ( $rule ) {
				if ( array_key_exists( $rule->get_type( 'edit' ), $rule_types ) ) {
					$post_id = $this->post_id;
					$actions = array();

					if ( current_user_can( 'edit_post', $post_id ) ) {
						$enable_field = array(
							'type'  => 'onoff',
							'class' => 'yith-wcbm-enable-badge-rule',
							'id'    => 'yith-wcbm-enable-badge-rule-' . $post_id,
							'data'  => array( 'badge-rule-id' => $post_id ),
							'value' => wc_bool_to_string( ! metadata_exists( 'post', $post_id, '_enabled' ) || 'yes' === get_post_meta( $post_id, '_enabled', true ) ),
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
									'action'   => 'yith_wcbm_clone_badge_rule',
									'post'     => $post_id,
									'security' => wp_create_nonce( 'yith_wcbm_clone_badge_rule' ),
								),
								admin_url()
							),
						);
					}
				}
				if ( current_user_can( 'delete_post', $rule->get_id() ) ) {
					$actions['delete'] = array(
						'type'   => 'action-button',
						'title'  => _x( 'Delete', 'Action button title', 'yith-woocommerce-badges-management' ),
						'action' => 'delete',
						'icon'   => 'trash',
						'class'  => array_key_exists( $rule->get_type( 'edit' ), $rule_types ) ? '' : 'yith-wcbm-just-delete-action',
						'url'    => get_delete_post_link( $rule->get_id(), '', true ),
					);
				}
				yith_plugin_fw_get_action_buttons( $actions );
			}
		}

	}
}

return YITH_WCBM_Badge_Rule_Post_Type_Admin::instance();
