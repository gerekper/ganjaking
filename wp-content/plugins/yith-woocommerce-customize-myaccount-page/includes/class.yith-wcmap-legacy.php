<?php
/**
 * Legacy class. Make the last version work with old options
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_Legacy' ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Legacy {

		/**
		 * Register activation hooks
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public static function register() {
			register_activation_hook( YITH_WCMAP_FILE, 'YITH_WCMAP_Legacy::update_old_items' );
			register_activation_hook( YITH_WCMAP_FILE, 'YITH_WCMAP_Legacy::update_old_options' );
		}

		/**
		 * Update old items
		 *
		 * @since  2.5.6
		 * @author Francesco Licandro
		 * @return void
		 */
		public static function update_old_items() {

			$fields = get_option( 'yith_wcmap_endpoint', array() );
			if ( empty( $fields ) ) {
				return;
			}

			$backup_option = 'yith_wcmap_endpoint_backup_pre_' . YITH_WCMAP_VERSION;
			if ( ! get_option( $backup_option, false ) ) {
				// Backup options.
				update_option( 'yith_wcmap_endpoint_backup_pre_' . YITH_WCMAP_VERSION, $fields, false );

				$fields     = json_decode( $fields, true );
				$new_fields = array();
				foreach ( $fields as $field ) {

					if ( ! isset( $field['id'] ) ) {
						continue;
					}

					if ( 'view-order' === $field['id'] ) {
						$field['id'] = 'orders';
					} elseif ( 'my-downloads' === $field['id'] ) {
						$field['id'] = 'downloads';
					}

					if ( isset( $field['children'] ) ) {
						$new_fields[ $field['id'] ] = array(
							'type'     => 'group',
							'children' => array(),
						);
						foreach ( $field['children'] as $child ) {

							if ( 'view-order' === $child['id'] ) {
								$child['id'] = 'orders';
							} elseif ( 'my-downloads' === $child['id'] ) {
								$child['id'] = 'downloads';
							}

							$new_fields[ $field['id'] ]['children'][ $child['id'] ] = array( 'type' => 'endpoint' );
						}
					} else {
						$new_fields[ $field['id'] ] = array( 'type' => 'endpoint' );
					}
				}

				if ( ! empty( $new_fields ) ) {
					update_option( 'yith_wcmap_endpoint', json_encode( $new_fields ) );
				}
			}
		}

		/**
		 * Update old option
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public static function update_old_options() {

			$options = array(
				'yith_wcmap_avatar[custom]'      => 'yith-wcmap-custom-avatar',
				'yith_wcmap_logout_button_color[text_normal]' => 'yith-wcmap-logout-color',
				'yith_wcmap_logout_button_color[text_hover]' => 'yith-wcmap-logout-color-hover',
				'yith_wcmap_logout_button_color[background_normal]' => 'yith-wcmap-logout-background',
				'yith_wcmap_logout_button_color[background_hover]' => 'yith-wcmap-logout-background-hover',
				'yith_wcmap_text_color[normal]'  => 'yith-wcmap-menu-item-color',
				'yith_wcmap_text_color[hover]'   => 'yith-wcmap-menu-item-color-hover',
				'yith_wcmap_users_avatar_ids'    => 'yith-wcmap-users-avatar-ids',
				'yith_wcmap_flush_rewrite_rules' => 'yith-wcmap-flush-rewrite-rules',
			);

			foreach ( $options as $new => $old ) {
				$value = get_option( $old, false );
				if ( false === $value ) {
					continue;
				}

				preg_match( '/(.*)\[(.*)\]/', $new, $new_as_array );
				if ( ! empty( $new_as_array ) ) {
					// Double check for array index.
					if ( empty( $new_as_array[2] ) ) {
						continue;
					}

					$new                           = $new_as_array[1];
					$new_value                     = get_option( $new, array() );
					$new_value[ $new_as_array[2] ] = $value;
					$value                         = $new_value;
				}

				// Update new option.
				update_option( $new, $value );
				// Delete old option.
				delete_option( $old );
			}
		}
	}
}
