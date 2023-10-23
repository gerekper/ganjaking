<?php
/**
 * Badge Class
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagement\Objects
 *
 * @since   2.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Badge' ) ) {
	/**
	 * Badge Class
	 */
	class YITH_WCBM_Badge extends WC_Data {

		/**
		 * Stores Badge data.
		 *
		 * @var array
		 */
		protected $data = array(
			'title'            => '',
			'enabled'          => 'yes',
			'type'             => '',
			'image'            => '1.svg',
			'text'             => 'Text',
			'background_color' => '#2470FF',
			'size'             => array(
				'dimensions' => array(
					'width'  => 150,
					'height' => 50,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'padding'          => array(
				'dimensions' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'border_radius'    => array(
				'dimensions' => array(
					'top-left'     => 0,
					'top-right'    => 0,
					'bottom-right' => 0,
					'bottom-left'  => 0,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			),
			'position'         => 'top',
			'alignment'        => 'left',
		);

		/**
		 * Meta to props.
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'          => 'enabled',
			'_type'             => 'type',
			'_image'            => 'image',
			'_text'             => 'text',
			'_background_color' => 'background_color',
			'_size'             => 'size',
			'_padding'          => 'padding',
			'_border_radius'    => 'border_radius',
			'_position'         => 'position',
			'_alignment'        => 'alignment',
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
		protected $data_store_object_type = 'badge';

		/**
		 * YITH_WCBM_Badge constructor
		 *
		 * @param int|YITH_WCBM_Badge|WP_Post $badge Badge Object, Badge ID or Badge Post.
		 */
		public function __construct( $badge = 0 ) {
			parent::__construct();

			if ( $badge instanceof WP_Post ) {
				$this->set_id( $badge->ID );
			} elseif ( is_numeric( $badge ) && $badge > 0 && get_post_type( $badge ) === YITH_WCBM_Post_Types::$badge ) {
				$this->set_id( $badge );
			} elseif ( $badge instanceof self ) {
				$this->set_id( $badge->get_id() );
			} else {
				$this->set_object_read( true );
			}

			$this->load_data_store();

			if ( $this->get_id() > 0 && $this->data_store ) {
				$this->data_store->read( $this );
			}
		}

		/**
		 * Load Data Store
		 */
		protected function load_data_store() {
			try {
				$this->data_store = WC_Data_Store::load( $this->data_store_object_type );
			} catch ( Exception $e ) {
				$this->data_store = false;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		|
		*/

		/**
		 * Get title
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_title( $context = 'view' ) {
			return $this->get_prop( 'title', $context );
		}

		/**
		 * Get type
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_type( $context = 'view' ) {
			return $this->get_prop( 'type', $context );
		}

		/**
		 * Get enabled property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_enabled( $context = 'view' ) {
			return $this->get_prop( 'enabled', $context );
		}

		/**
		 * Get image property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_image( $context = 'view' ) {
			return $this->get_prop( 'image', $context );
		}

		/**
		 * Get text property
		 *
		 * @param string                        $context The context.
		 * @param int|string|WC_Product|WP_Post $product The product ['template' if the badge is showing just for a preview reason].
		 *
		 * @return string
		 */
		public function get_text( $context = 'view', $product = false ) {
			$placeholders = yith_wcbm_get_badges_placeholders_values( $product );
			$text         = $this->get_prop( 'text', $context );
			if ( 'edit' === $context ) {
				return $text;
			}

			foreach ( $placeholders as $placeholder => $value ) {
				$text = str_replace( '{{' . $placeholder . '}}', $value, $text );
			}

			return yith_wcbm_wpml_string_translate( 'yith-woocommerce-badges-management', $text, $text );
		}

		/**
		 * Get background_color property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_background_color( $context = 'view' ) {
			return $this->get_prop( 'background_color', $context );
		}

		/**
		 * Get size property
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_size( $context = 'view' ) {
			return $this->get_prop( 'size', $context );
		}

		/**
		 * Get padding property
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_padding( $context = 'view' ) {
			return $this->get_prop( 'padding', $context );
		}

		/**
		 * Get border_radius property
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_border_radius( $context = 'view' ) {
			return $this->get_prop( 'border_radius', $context );
		}

		/**
		 * Get position property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_position( $context = 'view' ) {
			return $this->get_prop( 'position', $context );
		}

		/**
		 * Get alignment property
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_alignment( $context = 'view' ) {
			return $this->get_prop( 'alignment', $context );
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
		 * Set the title property value
		 *
		 * @param string $value title value.
		 */
		public function set_title( $value ) {
			$this->set_prop( 'title', sanitize_text_field( $value ) );
		}

		/**
		 * Set the type property value
		 *
		 * @param string $value type value.
		 */
		public function set_type( $value ) {
			$this->set_prop( 'type', sanitize_text_field( $value ) );
		}

		/**
		 * Set enabled property
		 *
		 * @param string $value The enabled.
		 */
		public function set_enabled( $value ) {
			$this->set_prop( 'enabled', wc_bool_to_string( 'yes' === $value ) );
		}

		/**
		 * Set image property
		 *
		 * @param string $value The image.
		 */
		public function set_image( $value ) {
			$this->set_prop( 'image', sanitize_text_field( $value ) );
		}

		/**
		 * Set text property
		 *
		 * @param string $value The text.
		 */
		public function set_text( $value ) {
			$this->set_prop( 'text', $value );
		}

		/**
		 * Set background_color property
		 *
		 * @param string $value The background_color.
		 */
		public function set_background_color( $value ) {
			$this->set_prop( 'background_color', $value );
		}

		/**
		 * Set size property
		 *
		 * @param array $value The size.
		 */
		public function set_size( $value ) {
			if ( isset( $value['dimensions'], $value['unit'] ) ) {
				$this->set_prop( 'size', $value );
			}
		}

		/**
		 * Set padding property
		 *
		 * @param array $value The padding.
		 */
		public function set_padding( $value ) {
			if ( isset( $value['dimensions'], $value['unit'] ) ) {
				$this->set_prop( 'padding', $value );
			}
		}

		/**
		 * Set border radius property
		 *
		 * @param array $value The border radius.
		 */
		public function set_border_radius( $value ) {
			if ( isset( $value['dimensions'], $value['unit'] ) ) {
				$this->set_prop( 'border_radius', $value );
			}
		}

		/**
		 * Set position property
		 *
		 * @param string $value The position.
		 */
		public function set_position( $value ) {
			$this->set_prop( 'position', in_array( $value, array( 'top', 'middle', 'bottom' ), true ) ? $value : 'top' );
		}

		/**
		 * Set alignment property
		 *
		 * @param string $value The alignment.
		 */
		public function set_alignment( $value ) {
			$this->set_prop( 'alignment', in_array( $value, array( 'left', 'center', 'right' ), true ) ? $value : 'left' );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Methods
		|--------------------------------------------------------------------------
		|
		| Methods that interacts with the CRUD.
		|
		*/

		/**
		 * Save data to the database.
		 *
		 * @return int Badge Rule ID
		 */
		public function save() {
			if ( $this->data_store ) {
				do_action( 'yith_wcbm_before_' . $this->object_type . '_object_save', $this, $this->data_store );
				if ( $this->get_id() ) {
					$this->data_store->update( $this );
				} else {
					$this->data_store->create( $this );
				}
				do_action( 'yith_wcbm_' . $this->object_type . '_object_save', $this, $this->data_store );
			}

			return $this->get_id();
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
		 * Check the badge type
		 *
		 * @param string $type The type.
		 *
		 * @return bool
		 */
		public function is_type( $type ) {
			return $type === $this->get_type();
		}

		/**
		 * Check if the badge is enabled
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return 'yes' === $this->get_enabled();
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
			$positions = array(
				'top'    => 'auto',
				'right'  => 'auto',
				'bottom' => 'auto',
				'left'   => 'auto',
			);

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
				$image_url = YITH_WCBM_ASSETS_URL . 'images/image-badges/' . $this->get_image();
				$image_url = str_replace( array( 'http://', 'https://' ), '//', $image_url );
				$image_url = apply_filters( 'yith_wcbm_image_badge_url', $image_url, $this->get_data(), $this );
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

			if ( $product ) {
				$classes[] = 'yith-wcbm-badge--on-product-' . $product->get_id();
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
				$props_from_request = wp_unslash( $_REQUEST['yith_wcbm_badge'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
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
		 * Get image ID
		 *
		 * @return int
		 */
		public function get_image_id() {
			return absint( str_replace( '.svg', '', $this->get_image() ) );
		}

		/**
		 * Get selected style ID
		 *
		 * @return int
		 */
		public function get_selected_style_id() {
			$style = 1;
			if ( ! $this->is_type( 'text' ) ) {
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
			if ( 'middle' === $this->get_position() ) {
				$transform_css = 'translateY(-50%)';
			}
			if ( 'center' === $this->get_alignment() ) {
				$transform_css = 'middle' === $this->get_position() ? 'translate(-50% , -50%)' : 'translateX(-50%)';
			}

			if ( $whit_rule_name ) {
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
				{$this->get_transform_css(true)}
				{$this->get_padding_css()}
				{$additional_style}
			}";

			return esc_html( $style );
		}

		/**
		 * Get badge additional style
		 *
		 * @return string
		 */
		public function get_additional_style() {
			$css = '';
			if ( $this->is_type( 'text' ) ) {
				$css .= 'background-color:' . $this->get_background_color() . ';';
				$css .= ' ' . $this->get_border_radius_css();
				$css .= ' ' . $this->get_width_css();
				$css .= ' ' . $this->get_height_css();
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
		 * Get height CSS
		 *
		 * @return string
		 */
		public function get_height_css() {
			return 'height:' . $this->get_height() . ';';
		}

		/**
		 * Get width CSS
		 *
		 * @return string
		 */
		public function get_width_css() {
			return 'width:' . $this->get_width() . ';';
		}

		/**
		 * Get border radius CSS
		 *
		 * @return string
		 */
		public function get_border_radius_css() {
			$unit = $this->get_border_radius_unit();

			return 'border-radius: ' . implode( $unit . ' ', $this->get_border_radius()['dimensions'] ) . $unit . ';';
		}

		/**
		 * Get padding CSS
		 *
		 * @return string
		 */
		public function get_padding_css() {
			$unit = $this->get_padding_unit();

			return 'padding: ' . implode( $unit . ' ', array_merge( array_flip( array( 'top', 'right', 'bottom', 'left' ) ), $this->get_padding()['dimensions'] ) ) . $unit . ';';
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

			return $position_css . ' ' . $alignment_css . ' ';
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
			yith_wcbm_get_view( 'badge-content.php', $args );
		}
	}
}
