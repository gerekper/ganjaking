<?php
/**
 * Badge Class Premium
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPrmeium\Objects
 *
 * @since   2.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Badge_Premium' ) ) {
	/**
	 * Badge Class
	 */
	class YITH_WCBM_Badge_Premium extends YITH_WCBM_Badge {


		/**
		 * Stores Badge data.
		 *
		 * @var array
		 */
		protected $data = array(
			'title'                => '',
			'enabled'              => 'yes',
			'type'                 => '',
			'uploaded_image_id'    => '',
			'uploaded_image_width' => 0,
			'image'                => '1.svg',
			'css'                  => '1.svg',
			'advanced'             => '1.svg',
			'advanced_display'     => 'amount',
			'text'                 => 'Text',
			'background_color'     => '#2470FF',
			'text_color'           => '#FFFFFF',
			'size'                 => array(
				'dimensions' => array(
					'width'  => 150,
					'height' => 50,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'padding'              => array(
				'dimensions' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'border_radius'        => array(
				'dimensions' => array(
					'top-left'     => 0,
					'top-right'    => 0,
					'bottom-right' => 0,
					'bottom-left'  => 0,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'margin'               => array(
				'dimensions' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'opacity'              => '100',
			'rotation'             => array(
				'x' => 0,
				'y' => 0,
				'z' => 0,
			),
			'use_flip_text'        => 'no',
			'flip_text'            => 'vertical',
			'position_type'        => 'fixed',
			'anchor_point'         => 'top-left',
			'position_values'      => array(
				'dimensions' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'position'             => 'top',
			'alignment'            => 'left',
			'scale_on_mobile'      => '1',
		);

		/**
		 * Meta to props.
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'              => 'enabled',
			'_type'                 => 'type',
			'_image'                => 'image',
			'_uploaded__image_id'   => 'uploaded_image_id',
			'_uploaded_image_width' => 'uploaded_image_width',
			'_css'                  => 'css',
			'_advanced'             => 'advanced',
			'_advanced_display'     => 'advanced_display',
			'_text'                 => 'text',
			'_background_color'     => 'background_color',
			'_text_color'           => 'text_color',
			'_size'                 => 'size',
			'_padding'              => 'padding',
			'_border_radius'        => 'border_radius',
			'_margin'               => 'margin',
			'_opacity'              => 'opacity',
			'_rotation'             => 'rotation',
			'_use_flip_text'        => 'use_flip_text',
			'_flip_text'            => 'flip_text',
			'_position_type'        => 'position_type',
			'_anchor_point'         => 'anchor_point',
			'_position_values'      => 'position_values',
			'_position'             => 'position',
			'_alignment'            => 'alignment',
			'_scale_on_mobile'      => 'scale_on_mobile',
		);

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $object_type = 'badge';

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $data_store_object_type = 'badge-premium';

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		|
		*/

		/**
		 * Get uploaded_image_id property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_uploaded_image_id( $context = 'view' ) {
			return $this->get_prop( 'uploaded_image_id', $context );
		}

		/**
		 * Get uploaded image width property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_uploaded_image_width( $context = 'view' ) {
			return $this->get_prop( 'uploaded_image_width', $context );
		}

		/**
		 * Get css property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_css( $context = 'view' ) {
			return $this->get_prop( 'css', $context );
		}

		/**
		 * Get advanced property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_advanced( $context = 'view' ) {
			return $this->get_prop( 'advanced', $context );
		}

		/**
		 * Get advanced_display property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_advanced_display( $context = 'view' ) {
			return $this->get_prop( 'advanced_display', $context );
		}

		/**
		 * Get text_color property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_text_color( $context = 'view' ) {
			return $this->get_prop( 'text_color', $context );
		}

		/**
		 * Get margin property
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_margin( $context = 'view' ) {
			return $this->get_prop( 'margin', $context );
		}

		/**
		 * Get opacity property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_opacity( $context = 'view' ) {
			return $this->get_prop( 'opacity', $context );
		}

		/**
		 * Get rotation property
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_rotation( $context = 'view' ) {
			return $this->get_prop( 'rotation', $context );
		}

		/**
		 * Get use_flip_text property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_use_flip_text( $context = 'view' ) {
			return $this->get_prop( 'use_flip_text', $context );
		}

		/**
		 * Get flip_text property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_flip_text( $context = 'view' ) {
			return $this->get_prop( 'flip_text', $context );
		}

		/**
		 * Get position_type property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_position_type( $context = 'view' ) {
			return $this->get_prop( 'position_type', $context );
		}

		/**
		 * Get anchor_point property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_anchor_point( $context = 'view' ) {
			return $this->get_prop( 'anchor_point', $context );
		}

		/**
		 * Get position_values property
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_position_values( $context = 'view' ) {
			return $this->get_prop( 'position_values', $context );
		}

		/**
		 * Get scale_on_mobile property
		 *
		 * @param string $context The context.
		 *
		 * @return float
		 */
		public function get_scale_on_mobile( $context = 'view' ) {
			return $this->get_prop( 'scale_on_mobile', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Methods for setting data from object.
		|
		*/

		/**
		 * Set uploaded_image_id property
		 *
		 * @param string $value The uploaded image id.
		 */
		public function set_uploaded_image_id( $value ) {
			$this->set_prop( 'uploaded_image_id', $value );
		}

		/**
		 * Set uploaded image width property
		 *
		 * @param string $value The uploaded image width.
		 */
		public function set_uploaded_image_width( $value ) {
			$this->set_prop( 'uploaded_image_width', absint( $value ) );
		}

		/**
		 * Set css property
		 *
		 * @param string $value The css.
		 */
		public function set_css( $value ) {
			$this->set_prop( 'css', sanitize_text_field( $value ) );
		}

		/**
		 * Set advanced property
		 *
		 * @param string $value The advanced.
		 */
		public function set_advanced( $value ) {
			$this->set_prop( 'advanced', sanitize_text_field( $value ) );
		}

		/**
		 * Set advanced_display property
		 *
		 * @param string $value The advanced_display.
		 */
		public function set_advanced_display( $value ) {
			$this->set_prop( 'advanced_display', in_array( $value, array( 'amount', 'percentage' ), true ) ? $value : 'amount' );
		}

		/**
		 * Set text_color property
		 *
		 * @param string $value The text_color.
		 */
		public function set_text_color( $value ) {
			$this->set_prop( 'text_color', $value );
		}

		/**
		 * Set margin property
		 *
		 * @param array $value The margin.
		 */
		public function set_margin( $value ) {
			if ( isset( $value['dimensions'], $value['unit'] ) ) {
				$value['dimensions'] = array_map( 'intval', $value['dimensions'] );
				$this->set_prop( 'margin', $value );
			}
		}

		/**
		 * Set opacity property
		 *
		 * @param int $value The opacity.
		 */
		public function set_opacity( $value ) {
			$this->set_prop( 'opacity', absint( $value ) );
		}

		/**
		 * Set rotation property
		 *
		 * @param array $value The rotation.
		 */
		public function set_rotation( $value ) {
			if ( isset( $value['x'], $value['y'], $value['z'] ) ) {
				$this->set_prop( 'rotation', $value );
			}
		}

		/**
		 * Set use flip text property
		 *
		 * @param string $value The use flip text value.
		 */
		public function set_use_flip_text( $value ) {
			$this->set_prop( 'use_flip_text', wc_bool_to_string( 'yes' === $value ) );
		}

		/**
		 * Set flip text property
		 *
		 * @param string $value The flip text.
		 */
		public function set_flip_text( $value ) {
			$this->set_prop( 'flip_text', in_array( $value, array( 'vertical', 'horizontal', 'both' ), true ) ? $value : 'vertical' );
		}

		/**
		 * Set position type property
		 *
		 * @param string $value The position type.
		 */
		public function set_position_type( $value ) {
			$this->set_prop( 'position_type', in_array( $value, array( 'fixed', 'values' ), true ) ? $value : 'fixed' );
		}

		/**
		 * Set anchor point property
		 *
		 * @param string $value The anchor point.
		 */
		public function set_anchor_point( $value ) {
			$this->set_prop( 'anchor_point', in_array( $value, array( 'top-left', 'top-right', 'bottom-left', 'bottom-right' ), true ) ? $value : 'top-left' );
		}

		/**
		 * Set position values property
		 *
		 * @param array $value The position values.
		 */
		public function set_position_values( $value ) {
			if ( isset( $value['dimensions'], $value['unit'] ) ) {
				$this->set_prop( 'position_values', $value );
			}
		}

		/**
		 * Set scale on mobile property
		 *
		 * @param float $value The scale on mobile value.
		 */
		public function set_scale_on_mobile( $value ) {
			$this->set_prop( 'scale_on_mobile', floatval( $value ) );
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		| Checks if a condition is true or false.
		|
		*/

		/**
		 * Has flipped Text?
		 *
		 * @return bool
		 */
		public function has_flipped_text() {
			return wc_string_to_bool( $this->get_use_flip_text() ) && $this->get_type() !== 'image';
		}

		/**
		 * Check if the badge is an upload image
		 *
		 * @return bool
		 */
		public function is_uploaded_image() {
			return $this->is_type( 'image' ) && 'upload' === $this->get_image();
		}

		/**
		 * Check if the badge is an upload image
		 *
		 * @return bool
		 */
		public function is_printable() {
			$return     = true;
			$badge_list = yith_wcbm_get_badges_list();
			switch ( $this->get_type() ) {
				case 'image':
					if ( $this->is_uploaded_image() ) {
						$return = yith_wcbm_has_active_license();
						break;
					} // Different behavior if the badge is uploaded.
				case 'advanced':
				case 'css':
					$getter = 'get_' . $this->get_type();
					if ( is_callable( array( $this, $getter ) ) ) {
						$return = in_array( $this->{$getter}(), $badge_list[ $this->get_type() ], true );
					}
					break;
			}

			return $return;
		}

		/*
		|--------------------------------------------------------------------------
		| Non-CRUD Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get badge positions
		 *
		 * @return string[]
		 */
		public function get_positions() {
			$position_type = $this->get_position_type();
			$positions     = array(
				'top'    => 'auto',
				'right'  => 'auto',
				'bottom' => 'auto',
				'left'   => 'auto',
			);

			if ( 'fixed' === $position_type ) {
				if ( 'center' === $this->get_position() ) {
					$positions['top'] = '50%';
				} else {
					$positions[ $this->get_position() ] = '0';
				}
				if ( 'middle' === $this->get_alignment() ) {
					$positions['left'] = '50%';
				} else {
					$positions[ $this->get_alignment() ] = '0';
				}
			} else {
				$position_values = $this->get_position_values();
				$unit            = $position_values['unit'] ?? 'px';
				if ( isset( $position_values['dimensions'] ) ) {
					foreach ( $position_values['dimensions'] as $side => $value ) {
						$positions[ $side ] = absint( $value ) . $unit;
					}
				}
			}

			return $positions;
		}

		/**
		 * Get Position top
		 *
		 * @return string
		 */
		public function get_position_top() {
			return $this->get_positions()['top'];
		}

		/**
		 * Get Position right
		 *
		 * @return string
		 */
		public function get_position_right() {
			return $this->get_positions()['right'];
		}

		/**
		 * Get Position bottom
		 *
		 * @return string
		 */
		public function get_position_bottom() {
			return $this->get_positions()['bottom'];
		}

		/**
		 * Get Position left
		 *
		 * @return string
		 */
		public function get_position_left() {
			return $this->get_positions()['left'];
		}

		/**
		 * Get Position left
		 *
		 * @return string
		 */
		public function get_image_url() {
			$image_url = '';
			if ( 'image' === $this->get_type() ) {
				if ( in_array( $this->get_image(), yith_wcbm_get_imported_badge_list( 'image' ), true ) ) {
					$image_url = yith_wcbm_get_badge_library_dir_url( 'image' ) . $this->get_image();
				} elseif ( 'upload' === $this->get_image() ) {
					$image_url = wp_get_attachment_image_url( $this->get_uploaded_image_id(), 'full' );
				} else {
					$image_url = YITH_WCBM_ASSETS_URL . 'images/image-badges/' . $this->get_image();
					$image_url = str_replace( array( 'http://', 'https://' ), '//', $image_url );
					$image_url = apply_filters( 'yith_wcbm_image_badge_url', $image_url, $this->get_data(), $this );
				}
			}

			return $image_url;
		}

		/**
		 * Get badge classes
		 *
		 * @param WC_Product $product The product in which is the badge.
		 *
		 * @return string
		 */
		public function get_classes( $product = false ) {
			$classes = array(
				'yith-wcbm-badge',
				'yith-wcbm-badge-' . $this->get_id(),
				'yith-wcbm-badge-' . $this->get_type(),
			);

			if ( $this->has_flipped_text() ) {
				$classes[] = 'yith-wcbm-badge-' . $this->get_type() . '--flip-' . $this->get_flip_text();
			}
			if ( $product ) {
				$classes[] = 'yith-wcbm-badge--on-product-' . $product->get_id();

				if ( $product->is_type( 'variation' ) ) {
					$classes[] = 'yith-wcbm-badge-show-if-variation';
					$classes[] = 'yith-wcbm-badge-show-if-variation--' . $product->get_id();
				}
			}

			switch ( $this->get_type() ) {
				case 'image':
					if ( $this->is_uploaded_image() ) {
						$classes[] = 'yith-wcbm-badge-image-uploaded';
					}
					break;
				case 'advanced':
					$classes[] = 'yith-wcbm-advanced-display-' . $this->get_advanced_display();
					break;
			}

			return implode( ' ', apply_filters( 'yith_wcbm_badge_classes', $classes, $this ) );
		}

		/**
		 * Get internal props from $_REQUEST array
		 *
		 * @return array
		 */
		public function get_internal_props_from_request() {
			$props = array();
			if ( isset( $_REQUEST['yith_wcbm_badge_security'], $_REQUEST['yith_wcbm_badge'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith_wcbm_badge_security'] ) ), 'yith_wcbm_save_badge' ) ) {
				$changes            = $this->changes;
				$this->changes      = array();
				$defaults           = array(
					'_use_flip_text' => 'no',
				);
				$props_from_request = wp_parse_args( wp_unslash( $_REQUEST['yith_wcbm_badge'] ), $defaults ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$meta_to_props      = $this->meta_key_to_props;
				foreach ( $props_from_request as $prop_name => $value ) {
					if ( array_key_exists( $prop_name, $meta_to_props ) ) {
						$setter = 'set_' . $meta_to_props[ $prop_name ];
						if ( method_exists( $this, $setter ) ) {
							$this->$setter( $value );
						}
					}
				}
				$props         = $this->get_changes();
				$this->changes = $changes;
			}

			return $props;
		}

		/**
		 * Get css ID
		 *
		 * @return int
		 */
		public function get_css_id() {
			return absint( str_replace( '.svg', '', $this->get_css() ) );
		}

		/**
		 * Get advanced ID
		 *
		 * @return int
		 */
		public function get_advanced_id() {
			return absint( str_replace( '.svg', '', $this->get_advanced() ) );
		}

		/**
		 * Get selected style ID
		 *
		 * @return int
		 */
		public function get_selected_style_id() {
			$style = 1;
			if ( 'text' !== $this->get_type() ) {
				$getter = 'get_' . $this->get_type() . '_id';
				if ( is_callable( array( $this, $getter ) ) ) {
					$style = $this->{$getter}();
				}
			}

			return $style;
		}

		/**
		 * Get advanced Id
		 *
		 * @param bool $whit_rule_name True if you want even the rule name (for all browsers).
		 *
		 * @return int
		 */
		public function get_transform_css( $whit_rule_name = false ) {
			$transform_css = '';
			if ( 'fixed' === $this->get_position_type() ) {
				if ( 'middle' === $this->get_position() ) {
					$transform_css = 'translateY(-50%)';
				}
				if ( 'center' === $this->get_alignment() ) {
					$transform_css = 'middle' === $this->get_position() ? 'translate(-50% , -50%)' : 'translateX(-50%)';
				}
			}
			$rotation = array_intersect_key( array_filter( array_map( 'absint', $this->get_rotation() ) ), array_flip( array( 'x', 'y', 'z' ) ) );
			foreach ( $rotation as $axis => $value ) {
				$transform_css .= ' rotate' . $axis . '( ' . $value . 'deg )';
			}
			if ( $transform_css && $whit_rule_name ) {
				$transform_css = "
				-ms-transform: $transform_css; 
				-webkit-transform: $transform_css; 
				transform: $transform_css;";
			}

			return $transform_css;
		}

		/**
		 * Get badge style
		 */
		public function get_style() {
			$additional_style = $this->get_additional_style();
			$style            = ".yith-wcbm-badge.yith-wcbm-badge-{$this->get_type()}.yith-wcbm-badge-{$this->get_id()} {
				{$this->get_position_css()}
				{$this->get_opacity_css()}
				{$this->get_transform_css(true)}
				{$this->get_margin_css()}
				{$this->get_padding_css()}
				{$additional_style}
			}";

			$style .= $this->get_scale_on_mobile_css();

			return esc_html( $style );
		}

		/**
		 * Get badge additional style
		 *
		 * @return string
		 */
		public function get_additional_style() {
			$css = '';
			switch ( $this->get_type() ) {
				case 'text':
					$css .= 'background-color:' . $this->get_background_color() . ';';
					$css .= ' ' . $this->get_border_radius_css();
					$css .= ' ' . $this->get_width_css();
					$css .= ' ' . $this->get_height_css();
					break;
				case 'image':
					if ( $this->is_uploaded_image() ) {
						$width = $this->get_uploaded_image_width();

						$css .= ' width:' . ( $width ? $width . 'px;' : 'auto' );
					}
					break;
			}

			return apply_filters( 'yith_wcbm_badge_additional_style', $css, $this );
		}

		/**
		 * Get background color CSS
		 *
		 * @return string
		 */
		public function get_background_color_css() {
			return 'background-color:' . $this->get_background_color() . ';';
		}

		/**
		 * Get margin CSS
		 *
		 * @return string
		 */
		public function get_margin_css() {
			$unit = $this->get_margin_unit();

			return 'margin: ' . implode( $unit . ' ', array_merge( array_flip( array( 'top', 'right', 'bottom', 'left' ) ), $this->get_margin()['dimensions'] ) ) . $unit . ';';
		}

		/**
		 * Get opacity CSS
		 *
		 * @return string
		 */
		public function get_opacity_css() {
			return 'opacity: ' . $this->get_opacity() . '%;';
		}

		/**
		 * Get scale on mobile CSS
		 *
		 * @return string
		 */
		public function get_scale_on_mobile_css() {
			$scale_on_mobile = $this->get_scale_on_mobile();
			$css             = '';

			if ( 1.0 !== floatval( $scale_on_mobile ) ) {
				$mobile_breakpoint = get_option( 'yith-wcbm-mobile-breakpoint', 768 ) . 'px';

				$transform = $this->get_transform_css() . " scale({$scale_on_mobile})";

				$transform_css = "-ms-transform: $transform; -webkit-transform: $transform; transform: $transform;";

				$css = "@media only screen and (max-width: {$mobile_breakpoint}) {
							.yith-wcbm-badge.yith-wcbm-badge-{$this->get_type()}.yith-wcbm-badge-{$this->get_id()}{ 
							{$transform_css}
							}
						}
				";
			}

			return $css;
		}

		/**
		 * Get transform origin css
		 *
		 * @return string
		 */
		public function get_transform_origin_css() {
			$transform_origin_css = 'transform-origin: ';
			if ( 'values' === $this->get_position_type() ) {
				$transform_origin_css .= str_replace( '-', ' ', $this->get_anchor_point() ) . ';';
			} else {
				$transform_origin_css .= ( 'middle' !== $this->get_position() ? $this->get_position() : 'center' ) . ' ' . $this->get_alignment() . ';';
			}

			return apply_filters( 'yith_wcbm_badge_get_transform_origin_css', $transform_origin_css, $this );
		}

		/**
		 * Get dimensions prop unit
		 *
		 * @param string $prop The property for which you want the unit.
		 *
		 * @return string
		 */
		public function get_dimensions_prop_unit( $prop ) {
			$values = $this->get_prop( $prop );
			$units  = array(
				'px'         => 'px',
				'percentage' => '%',
			);

			return isset( $values['unit'] ) && array_key_exists( $values['unit'], $units ) ? $units[ $values['unit'] ] : 'px';
		}

		/**
		 * Get position values unit
		 *
		 * @return string
		 */
		public function get_position_values_unit() {
			return $this->get_dimensions_prop_unit( 'position_values' );
		}

		/**
		 * Get margin unit
		 *
		 * @return string
		 */
		public function get_margin_unit() {
			return $this->get_dimensions_prop_unit( 'margin' );
		}

		/**
		 * Get border radius unit
		 *
		 * @return string
		 */
		public function get_border_radius_unit() {
			return $this->get_dimensions_prop_unit( 'border_radius' );
		}

		/**
		 * Get padding unit
		 *
		 * @return string
		 */
		public function get_padding_unit() {
			return $this->get_dimensions_prop_unit( 'padding' );
		}

		/**
		 * Get width
		 *
		 * @return string
		 */
		public function get_width() {
			$unit  = $this->get_dimensions_prop_unit( 'size' );
			$size  = $this->get_size();
			$width = isset( $size['dimensions']['width'] ) ? intval( $size['dimensions']['width'] ) : 150;

			return 0 !== $width ? $width . $unit : 'auto';
		}

		/**
		 * Get height
		 *
		 * @return string
		 */
		public function get_height() {
			$unit   = $this->get_dimensions_prop_unit( 'size' );
			$size   = $this->get_size();
			$height = isset( $size['dimensions']['height'] ) ? intval( $size['dimensions']['height'] ) : 50;

			return 0 !== $height ? $height . $unit : 'auto';
		}

		/**
		 * Get position CSS
		 *
		 * @return string
		 */
		public function get_position_css() {
			if ( 'fixed' === $this->get_position_type() ) {
				switch ( $this->get_position() ) {
					case 'middle':
						$position_css = 'top: 50%;';
						break;
					case 'bottom':
						$position_css = 'bottom: 0;';
						break;
					default:
						$position_css = 'top: 0;';
						break;
				}
				switch ( $this->get_alignment() ) {
					case 'center':
						$alignment_css = 'left: 50%;';
						break;
					case 'right':
						$alignment_css = 'right: 0;';
						break;
					default:
						$alignment_css = 'left: 0;';
						break;
				}
				$css = $position_css . ' ' . $alignment_css . ' ';
			} else {
				$anchor_point    = $this->get_anchor_point();
				$positions       = array(
					'top'    => 'top: auto;',
					'right'  => 'right: auto;',
					'bottom' => 'bottom: auto;',
					'left'   => 'left: auto;',
				);
				$unit            = $this->get_position_values_unit();
				$position_values = $this->get_position_values();
				foreach ( explode( '-', $anchor_point ) as $side ) {
					$positions[ $side ] = $side . ': ' . ( isset( $position_values['dimensions'][ $side ] ) ? intval( $position_values['dimensions'][ $side ] ) : 0 ) . $unit . ';';
				}

				$css = implode( ' ', $positions );
			}

			return $css;
		}

		/*
		|--------------------------------------------------------------------------
		| Colors Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get text color from text
		 *
		 * @return string
		 */
		public function get_text_color_from_text() {
			preg_match( '/#[a-f0-9]{6}(?!.*#[a-f0-9]{6})/', $this->get_text(), $matches );

			return $matches ? current( $matches ) : '#000000';
		}

		/**
		 * Get secondary background color
		 *
		 * @return string
		 */
		public function get_secondary_background_color() {
			return yith_wcbm_color_with_factor( substr( $this->get_background_color(), 1 ), 0.6 );
		}

		/**
		 * Get secondary light background color
		 *
		 * @return string
		 */
		public function get_secondary_light_background_color() {
			return yith_wcbm_color_with_factor( substr( $this->get_background_color(), 1 ), 0.7 );
		}

		/**
		 * Get secondary dark background color
		 *
		 * @return string
		 */
		public function get_secondary_dark_background_color() {
			return yith_wcbm_color_with_factor( substr( $this->get_background_color(), 1 ), 0.5 );
		}

		/**
		 * Get tertiary background color
		 *
		 * @return string
		 */
		public function get_tertiary_background_color() {
			return yith_wcbm_color_with_factor( substr( $this->get_background_color(), 1 ), 0.4 );
		}

		/**
		 * Get triadic positive background color
		 *
		 * @return string
		 */
		public function get_triadic_positive_background_color() {
			return yith_wcbm_get_hue_rotated_color( $this->get_background_color(), 90 );
		}

		/**
		 * Get triadic negative background color
		 *
		 * @return string
		 */
		public function get_triadic_negative_background_color() {
			return yith_wcbm_get_hue_rotated_color( $this->get_background_color(), -90 );
		}

		/**
		 * Get analogous positive background color
		 *
		 * @return string
		 */
		public function get_analogous_positive_background_color() {
			return yith_wcbm_get_hue_rotated_color( $this->get_background_color(), 30 );
		}

		/**
		 * Get analogous negative background color
		 *
		 * @return string
		 */
		public function get_analogous_negative_background_color() {
			return yith_wcbm_get_hue_rotated_color( $this->get_background_color(), -30 );
		}

		/**
		 * Get complementary background color
		 *
		 * @return string
		 */
		public function get_complementary_background_color() {
			return yith_wcbm_get_hue_rotated_color( $this->get_background_color(), 180 );
		}

		/**
		 * Display badge
		 *
		 * @param int $product_id The product ID.
		 */
		public function display( $product_id = 0 ) {
			$args = array(
				'badge'      => $this,
				'product_id' => $product_id,
			);

			do_action( 'yith_wcbm_badge_display', $this, $product_id );
			if ( $this->is_printable() ) {
				yith_wcbm_get_view( 'badge-content-premium.php', $args );
			}
		}
	}
}
