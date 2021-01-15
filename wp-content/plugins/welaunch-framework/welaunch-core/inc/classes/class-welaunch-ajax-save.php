<?php
/**
 * weLaunch AJAX Save Class
 *
 * @class weLaunch_Core
 * @version 4.0.0
 * @package weLaunch Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_AJAX_Save', false ) ) {

	/**
	 * Class weLaunch_AJAX_Save
	 */
	class weLaunch_AJAX_Save extends weLaunch_Class {

		/**
		 * weLaunch_AJAX_Save constructor.
		 * array_merge_recursive_distinct
		 *
		 * @param object $parent weLaunchFrameword object.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			add_action( 'wp_ajax_' . $this->args['opt_name'] . '_ajax_save', array( $this, 'save' ) );
		}

		/**
		 * AJAX callback to save the option panel values.
		 */
		public function save() {
			$core = $this->core();

			if ( ! isset( $_REQUEST['nonce'] ) || ( isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['nonce'] ) ), 'welaunch_ajax_nonce' . $this->args['opt_name'] ) ) ) {
				echo wp_json_encode(
					array(
						'status' => esc_html__( 'Invalid security credential.  Please reload the page and try again.', 'welaunch-framework' ),
						'action' => '',
					)
				);
				die();
			}

			if ( ! weLaunch_Helpers::current_user_can( $core->args['page_permissions'] ) ) {
				echo wp_json_encode(
					array(
						'status' => esc_html__( 'Invalid user capability.  Please reload the page and try again.', 'welaunch-framework' ),
						'action' => '',
					)
				);
				die();
			}

			if ( isset( $_POST['opt_name'] ) && ! empty( $_POST['opt_name'] ) && isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
				$welaunch = weLaunch::instance( sanitize_text_field( wp_unslash( $_POST['opt_name'] ) ) );

				if ( ! empty( $welaunch->args['opt_name'] ) ) {

					$post_data = wp_unslash( $_POST['data'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

					// New method to avoid input_var nonsense.  Thanks @harunbasic.
					$values = weLaunch_Functions_Ex::parse_str( $post_data );
					$values = $values[ $welaunch->args['opt_name'] ];

					if ( ! empty( $values ) ) {
						try {
							if ( isset( $welaunch->validation_ran ) ) {
								unset( $welaunch->validation_ran );
							}

							$welaunch->options_class->set( $welaunch->options_class->validate_options( $values ) );

							$do_reload = false;
							if ( isset( $core->required_class->reload_fields ) && ! empty( $core->required_class->reload_fields ) ) {
								if ( ! empty( $core->transients['changed_values'] ) ) {
									foreach ( $core->required_class->reload_fields as $idx => $val ) {
										if ( array_key_exists( $val, $core->transients['changed_values'] ) ) {
											$do_reload = true;
										}
									}
								}
							}

							if ( $do_reload || ( isset( $values['defaults'] ) && ! empty( $values['defaults'] ) ) || ( isset( $values['defaults-section'] ) && ! empty( $values['defaults-section'] ) ) || ( isset( $values['import_code'] ) && ! empty( $values['import_code'] ) ) || ( isset( $values['import_link'] ) && ! empty( $values['import_link'] ) ) ) {
								echo wp_json_encode(
									array(
										'status' => 'success',
										'action' => 'reload',
									)
								);
								die();
							}

							$welaunch->enqueue_class->get_warnings_and_errors_array();

							$return_array = array(
								'status'   => 'success',
								'options'  => $welaunch->options,
								'errors'   => isset( $welaunch->enqueue_class->localize_data['errors'] ) ? $welaunch->enqueue_class->localize_data['errors'] : null,
								'warnings' => isset( $welaunch->enqueue_class->localize_data['warnings'] ) ? $welaunch->enqueue_class->localize_data['warnings'] : null,
								'sanitize' => isset( $welaunch->enqueue_class->localize_data['sanitize'] ) ? $welaunch->enqueue_class->localize_data['sanitize'] : null,
							);
						} catch ( Exception $e ) {
							$return_array = array( 'status' => $e->getMessage() );
						}
					} else {
						echo wp_json_encode(
							array(
								'status' => esc_html__( 'Your panel has no fields. Nothing to save.', 'welaunch-framework' ),
							)
						);
						die();
					}
				}
			}

			if ( isset( $core->transients['run_compiler'] ) && $core->transients['run_compiler'] ) {
				$core->no_output = true;
				$temp            = $core->args['output_variables_prefix'];
				// Allow the override of variables prefix for use by SCSS or LESS.
				if ( isset( $core->args['compiler_output_variables_prefix'] ) ) {
					$core->args['output_variables_prefix'] = $core->args['compiler_output_variables_prefix'];
				}
				$core->output_class->enqueue();
				$core->args['output_variables_prefix'] = $temp;

				try {

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName
					$compiler_css = $core->compilerCSS;  // Backward compatibility variable.

					/**
					 * Action 'welaunch/options/{opt_name}/compiler'
					 *
					 * @param array  options
					 * @param string CSS that get sent to the compiler hook
					 */

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'welaunch/options/' . $core->args['opt_name'] . '/compiler', $core->options, $compiler_css, $core->transients['changed_values'], $core->output_variables );

					/**
					 * Action 'welaunch/options/{opt_name}/compiler/advanced'
					 *
					 * @param array  options
					 * @param string CSS that get sent to the compiler hook, which sends the full weLaunch object
					 */

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'welaunch/options/' . $core->args['opt_name'] . '/compiler/advanced', $core );
				} catch ( Exception $e ) {
					$return_array = array( 'status' => $e->getMessage() );
				}

				unset( $core->transients['run_compiler'] );
				$core->transient_class->set();
			}

			if ( isset( $return_array ) ) {
				if ( 'success' === $return_array['status'] ) {
					$panel = new weLaunch_Panel( $welaunch );
					ob_start();
					$panel->notification_bar();
					$notification_bar = ob_get_contents();
					ob_end_clean();
					$return_array['notification_bar'] = $notification_bar;
				}

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				echo wp_json_encode( apply_filters( 'welaunch/options/' . $core->args['opt_name'] . '/ajax_save/response', $return_array ) );
			}

			die();
		}
	}
}
