<?php
/**
 * Abstract Render Block Class.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Render_Block' ) ) {
	/**
	 * Abstract Render Block Class.
	 *
	 * @since 3.1.0
	 */
	abstract class YITH_WCBK_Render_Block {

		/**
		 * Block attributes
		 *
		 * @var array
		 */
		protected $attributes = array();

		/**
		 * Object data.
		 *
		 * @var array
		 */
		protected $data = array(
			'allow_blank_state' => 'no',
		);

		/**
		 * YITH_WCBK_Render_Block constructor.
		 *
		 * @param array $attributes Block attributes.
		 */
		public function __construct( $attributes = array() ) {
			$this->attributes = $this->parse_attributes( $attributes );
		}

		/**
		 * Parse attributes.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return array
		 */
		protected function parse_attributes( $attributes ) {
			return wp_parse_args( $attributes, $this->attributes );
		}

		/**
		 * Get allow_blank_state value.
		 *
		 * @return string
		 */
		public function get_allow_blank_state() {
			return $this->data['allow_blank_state'];
		}

		/**
		 * Is the empty state allowed?
		 *
		 * @return bool
		 */
		public function is_blank_state_allowed() {
			return 'yes' === $this->get_allow_blank_state();
		}

		/**
		 * Set the allow_blank_state value.
		 *
		 * @param bool|string $value The value to be set.
		 */
		public function set_allow_blank_state( $value ) {
			$this->data['allow_blank_state'] = wc_bool_to_string( $value );
		}

		/**
		 * Render
		 */
		abstract public function render();

		/**
		 * Retrieve blank state params.
		 *
		 * @return array
		 */
		public function get_blank_state_params() {
			return array();
		}

		/**
		 * Render an empty state.
		 */
		public function render_blank_state() {
			if ( ! wp_style_is( 'yith-plugin-ui', 'registered' ) ) {
				$plugin_fw_assets = class_exists( 'YIT_Assets' ) && is_callable( 'YIT_Assets::instance' ) ? YIT_Assets::instance() : false;

				if ( $plugin_fw_assets && is_callable( array( $plugin_fw_assets, 'register_styles_and_scripts' ) ) ) {
					$plugin_fw_assets->register_styles_and_scripts();
				}
			}

			wp_enqueue_style( 'yith-plugin-ui' );

			$blank_state_params         = $this->get_blank_state_params();
			$blank_state_params['type'] = 'list-table-blank-state';

			yith_plugin_fw_get_component( $blank_state_params, true );
		}

		/**
		 * Is this a block preview request?
		 */
		protected function is_block_preview() {
			return defined( 'YITH_WCBK_BLOCK_PREVIEW' ) && YITH_WCBK_BLOCK_PREVIEW;
		}
	}
}
