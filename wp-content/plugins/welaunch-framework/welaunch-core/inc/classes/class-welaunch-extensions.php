<?php
/**
 * Register Extensions for use
 *
 * @package weLaunch Framework/Classes
 * @since       3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Extensions', false ) ) {

	/**
	 * Class weLaunch_Extensions
	 */
	class weLaunch_Extensions extends weLaunch_Class {

		/**
		 * weLaunch_Extensions constructor.
		 *
		 * @param object $parent weLaunchFramework object pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			$this->load();
		}

		/**
		 * Class load functions.
		 *
		 * @throws ReflectionException For fallback.
		 */
		private function load() {
			$core = $this->core();

			$max = 1;

			if ( weLaunch_Core::$pro_loaded ) {
				$max = 2;
			}

			for ( $i = 1; $i <= $max; $i ++ ) {
				$path = weLaunch_Core::$dir . 'inc/extensions/';

				if ( 2 === $i ) {
					$path = weLaunch_Pro::$dir . 'core/inc/extensions/';
				}

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$path = apply_filters( 'welaunch/' . $core->args['opt_name'] . '/extensions/dir', $path );

				/**
				 * Action 'welaunch/extensions/before'
				 *
				 * @param object $this weLaunchFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( 'welaunch/extensions/before', $core );

				/**
				 * Action 'welaunch/extensions/{opt_name}/before'
				 *
				 * @param object $this weLaunchFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( "welaunch/extensions/{$core->args['opt_name']}/before", $core );

				if ( isset( $core->old_opt_name ) && null !== $core->old_opt_name ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'welaunch/extensions/' . $core->old_opt_name . '/before', $core );
				}

				require_once weLaunch_Core::$dir . 'inc/classes/class-welaunch-extension-abstract.php';

				$path = untrailingslashit( $path );

				// Backwards compatibility for extensions.
				$instance_extensions = weLaunch::get_extensions( $core->args['opt_name'], '' );
				if ( ! empty( $instance_extensions ) ) {
					foreach ( $instance_extensions as $name => $extension ) {
						if ( ! isset( $core->extensions[ $name ] ) ) {
							if ( class_exists( 'weLaunchFramework_Extension_' . $name ) ) {
								$a = new ReflectionClass( 'weLaunchFramework_Extension_' . $name );
								weLaunch::set_extensions( $core->args['opt_name'], dirname( $a->getFileName() ), true );
							}
						}
						if ( ! isset( $core->extensions[ $name ] ) ) {
							/* translators: %s is the name of an extension */
							$msg  = '<strong>' . sprintf( esc_html__( 'The `%s` extension was not located properly', 'welaunch-framework' ), $name ) . '</strong>';
							$data = array(
								'parent'  => $this->parent,
								'type'    => 'error',
								'msg'     => $msg,
								'id'      => $name . '_notice_',
								'dismiss' => false,
							);
							if ( method_exists( 'weLaunch_Admin_Notices', 'set_notice' ) ) {
								weLaunch_Admin_Notices::set_notice( $data );
							}
							continue;
						}
						if ( ! is_subclass_of( $core->extensions[ $name ], 'weLaunch_Extension_Abstract' ) ) {
							$ext_class                      = get_class( $core->extensions[ $name ] );
							$new_class_name                 = $ext_class . '_extended';
							weLaunch::$extension_compatibility = true;
							$core->extensions[ $name ]      = weLaunch_Functions_Ex::extension_compatibility( $core, $extension['path'], $ext_class, $new_class_name, $name );
						}
					}
				}

				weLaunch::set_extensions( $core->args['opt_name'], $path, true );

				/**
				 * Action 'welaunch/extensions/{opt_name}'
				 *
				 * @param object $this weLaunchFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( "welaunch/extensions/{$core->args['opt_name']}", $core );

				if ( isset( $core->old_opt_name ) && null !== $core->old_opt_name ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'welaunch/extensions/' . $core->old_opt_name, $core );
				}
			}
		}
	}
}
