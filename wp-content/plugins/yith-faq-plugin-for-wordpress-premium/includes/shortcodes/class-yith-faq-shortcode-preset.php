<?php
/**
 * Preset Shortcode class
 *
 * @package YITH\FAQPluginForWordPress\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Shortcode_Preset' ) ) {

	/**
	 * Implements shortcode for FAQ plugin
	 *
	 * @class   YITH_FAQ_Shortcode_Preset
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress\Shortcodes
	 */
	class YITH_FAQ_Shortcode_Preset extends YITH_FAQ_Shortcode {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_shortcode( 'yith_faq_preset', array( $this, 'print_shortcode_from_preset' ) );

		}

		/**
		 * Outputs shortcode from a preset
		 *
		 * @param array $args Shortcode arguments.
		 *
		 * @return false|string
		 * @since  2.0.0
		 */
		public function print_shortcode_from_preset( $args ) {
			if ( isset( $args['id'] ) && is_numeric( $args['id'] ) ) {
				return do_shortcode( yfwp_create_shortcode( $args['id'] ) );
			}

			return false;
		}

		/**
		 * Set shortcode on Gutenberg and Elementor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function gutenberg_block() {

			$blocks = array(
				'yith-faq-preset' => array(
					'style'          => 'yith-faq-shortcode-frontend',
					'title'          => esc_html__( 'FAQ preset', 'yith-faq-plugin-for-wordpress' ),
					'description'    => esc_html__( 'Add the FAQ preset shortcode.', 'yith-faq-plugin-for-wordpress' ),
					'shortcode_name' => 'yith_faq_preset',
					'do_shortcode'   => true,
					'keywords'       => array(
						esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
						esc_html__( 'Frequently Asked Questions', 'yith-faq-plugin-for-wordpress' ),
					),
					'attributes'     => array(
						'id' => array(
							'type'     => 'select',
							'label'    => esc_html__( 'FAQ preset', 'yith-faq-plugin-for-wordpress' ),
							'options'  => yfwp_get_presets(),
							'multiple' => false,
							'default'  => '',
						),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );
			yith_plugin_fw_register_elementor_widgets( $blocks, true );

		}

	}

	new YITH_FAQ_Shortcode_Preset();

}
