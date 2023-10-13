<?php
/**
 * Abstract Render Block with Style Class.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Render_Block_With_Style' ) ) {
	/**
	 * Booking form block class
	 *
	 * @since 4.0.0
	 */
	abstract class YITH_WCBK_Render_Block_With_Style extends YITH_WCBK_Render_Block {

		/**
		 * Block style attributes
		 *
		 * @var array
		 */
		protected $style_attributes = array(
			'textColor'       => '#000000',
			'backgroundColor' => '#ffffff',
			'padding'         => 20,
			'border'          => array(
				'color' => '#cccccc',
				'style' => 'solid',
				'width' => 0,
			),
			'borderRadius'    => 0,
		);

		/**
		 * The constructor.
		 *
		 * @param array $attributes Block attributes.
		 */
		public function __construct( $attributes = array() ) {
			$this->attributes = array_merge( $this->style_attributes, $this->attributes );
			parent::__construct( $attributes );
		}

		/**
		 * Get the text_color.
		 *
		 * @return string
		 */
		public function get_text_color() {
			return $this->attributes['textColor'];
		}

		/**
		 * Get the background_color.
		 *
		 * @return string
		 */
		public function get_background_color() {
			return $this->attributes['backgroundColor'];
		}

		/**
		 * Get the padding.
		 *
		 * @return string
		 */
		public function get_padding() {
			return $this->attributes['padding'];
		}

		/**
		 * Get the border.
		 *
		 * @return array
		 */
		public function get_border() {
			$border = $this->attributes['border'];
			$border = is_array( $border ) ? $border : array();

			$border['color'] = $border['color'] ?? '#cccccc';
			$border['style'] = $border['style'] ?? 'solid';
			$border['width'] = absint( $border['width'] ?? 0 );

			return $border;
		}

		/**
		 * Get the border_radius.
		 *
		 * @return int
		 */
		public function get_border_radius() {
			return absint( $this->attributes['borderRadius'] );
		}

		/**
		 * Get the CSS style of the wrapper.
		 *
		 * @return string
		 */
		protected function get_css_style() {
			$border        = $this->get_border();
			$border_radius = $this->get_border_radius();
			$padding       = $this->get_padding();

			$style = array(
				'background'    => $this->get_background_color(),
				'color'         => $this->get_text_color(),
				'padding'       => $padding > 0 ? ( $padding . 'px' ) : false,
				'border'        => $border['width'] > 0 ? sprintf( '%spx %s %s', $border['width'], $border['style'], $border['color'] ) : false,
				'border-radius' => $border_radius > 0 ? ( $border_radius . 'px' ) : false,
			);

			$style     = array_filter( $style );
			$css_style = array();

			foreach ( $style as $prop => $value ) {
				$css_style[] = esc_attr( $prop ) . ': ' . esc_attr( $value );
			}

			return implode( '; ', $css_style );
		}
	}
}
