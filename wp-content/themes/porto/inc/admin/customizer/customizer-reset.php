<?php
/**
 * Porto Customizer Reset
 *
 * @author     Porto Themes
 * @category   Admin Functions
 * @since      4.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Porto_Customizer_Reset' ) ) :
	class Porto_Customizer_Reset {

		private $wp_customize;

		public function __construct() {
			add_action( 'customize_register', array( $this, 'customize_register' ) );

			add_action( 'wp_ajax_porto_customizer_reset_options', array( $this, 'reset_options' ) );
			add_action( 'wp_ajax_nopriv_porto_customizer_reset_options', array( $this, 'reset_options' ) );
		}

		public function customize_register( $wp_customize ) {
			$this->wp_customize = $wp_customize;
			$wp_customize->add_section(
				'porto_reset_all_options',
				array(
					'title'       => __( 'Reset Options', 'porto' ),
					'priority'    => 999,
					'description' => __( 'Click reset button to reset all options to default values.', 'porto' ),
				)
			);
			$wp_customize->add_control(
				'porto_reset_all_options_button',
				array(
					'type'        => 'button',
					'settings'    => array(),
					'priority'    => 10,
					'section'     => 'porto_reset_all_options',
					'input_attrs' => array(
						'value' => __( 'Reset Theme Options', 'porto' ),
						'class' => 'button button-primary porto_reset_all_options',
					),
				)
			);
		}

		public function reset_options() {
			if ( ! is_customize_preview() ) {
				wp_send_json_error( 'no_preview' );
			}
			if ( wp_verify_nonce( $_POST['nonce'], 'porto-customizer' ) ) {
				global $reduxPortoSettings;
				$options_defaults = $reduxPortoSettings->ReduxFramework->options_defaults;
				if ( empty( $options_defaults ) ) {
					$options_defaults = $reduxPortoSettings->ReduxFramework->_default_values();
				}
				if ( ! empty( $options_defaults ) ) {
					$reduxPortoSettings->ReduxFramework->set_options( $options_defaults );
				}
				wp_send_json_success();
			} else {
				wp_send_json_error( 'invalid_security' );
			}

		}
	}
endif;
new Porto_Customizer_Reset();
