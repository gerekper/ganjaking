<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Powerpack Setting class.
 *
 * @package  SP_Designer_CSS_Setting/Setting
 * @category Class
 * @author   Tiago Noronha
 */
class SP_Designer_CSS_Setting extends WP_Customize_Setting {
	const ID_PATTERN = '/^sp_designer_css_data\[(?P<id>-?\d+)\]$/';

	const TYPE = 'sp_designer_css_data';

	/**
	 * Setting type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'theme_mod';

	/**
	 * Default transport.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Storage of pre-setup menu item to prevent wasted calls to wp_setup_nav_menu_item().
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $value;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Bootstrap Customizer instance.
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Optional. Setting arguments.
	 *
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		$args['type'] = $this->type;

		parent::__construct( $manager, $id, $args );
	}

	public function sanitize( $value ) {
		// Menu is marked for deletion.
		if ( false === $value ) {
			return $value;
		}

		// Invalid.
		if ( ! is_array( $value ) ) {
			return null;
		}

		// Selector
		$sanitized[ 'selector' ] = '';
		if ( array_key_exists( 'selector', $value ) ) {
			$sanitized['selector'] = sanitize_text_field( $value['selector'] );
		}

		// Positive value properties
		$positive = array(
			'fontSize', 'lineHeight', 'borderWidth', 'borderRadius', 'paddingTop', 'paddingLeft', 'paddingRight', 'paddingBottom'
		);

		foreach ( $positive as $property ) {
			$sanitized[ $property ] = 0;

			if ( array_key_exists( $property, $value ) && is_numeric( $value[ $property ] ) ) {
				$sanitized[ $property ] = abs( floatval( $value[ $property ] ) );
			}
		}

		// Other Numeric properties (can be negative)
		$numeric = array( 'letterSpacing', 'marginTop', 'marginLeft', 'marginRight', 'marginBottom' );

		foreach ( $numeric as $property ) {
			$sanitized[ $property ] = 0;

			if ( array_key_exists( $property, $value ) && is_numeric( $value[ $property ] ) ) {
				$sanitized[ $property ] = floatval( $value[ $property ] );
			}
		}

		// Measurement Units
		$units = array(
			'fontSizeUnit', 'letterSpacingUnit', 'borderRadiusUnit', 'borderWidthUnit', 'marginTopUnit', 'marginLeftUnit',
			'marginRightUnit', 'marginBottomUnit', 'paddingTopUnit', 'paddingLeftUnit', 'paddingRightUnit', 'paddingBottomUnit'
		);

		foreach ( $units as $property ) {
			$sanitized[ $property ] = 'px';

			if ( array_key_exists( $property, $value ) && in_array( $value[ $property ], array( 'px', 'em' ) ) ) {
				$sanitized[ $property ] = sanitize_text_field( $value[ $property ] );
			}
		}

		// Colors
		$colors = array( 'color', 'borderColor', 'backgroundColor' );
		foreach ( $colors as $property ) {
			$sanitized[ $property ] = '';

			if ( array_key_exists( $property, $value ) ) {
				$hex = sanitize_hex_color( $value[ $property ] );
				if ( ! empty( $hex ) ) {
					$sanitized[ $property ] = $hex;
				}
			}
		}

		// Font Family
		$sanitized[ 'fontFamily' ] = 'Default';
		if ( array_key_exists( 'fontFamily', $value ) && '' !== $value['fontFamily'] ) {
			$sanitized['fontFamily'] = sanitize_text_field( $value['fontFamily'] );
		}

		// Font Variant
		$sanitized[ 'fontVariant' ] = 'Regular';
		if ( array_key_exists( 'fontVariant', $value ) && '' !== $value['fontVariant'] ) {
			$sanitized['fontVariant'] = sanitize_text_field( $value['fontVariant'] );
		}

		// Font Style
		$sanitized[ 'fontStyle' ] = '';
		if ( array_key_exists( 'fontStyle', $value ) && 'italic' === $value['fontStyle'] ) {
			$sanitized['fontStyle'] = 'italic';
		}

		// Font Weight
		$sanitized[ 'fontWeight' ] = '';
		if ( array_key_exists( 'fontWeight', $value ) && in_array( absint( $value['fontWeight'] ), array( 100, 200, 300, 400, 500, 600, 700, 800, 900 ) ) ) {
			$sanitized['fontWeight'] = absint( $value['fontWeight'] );
		}

		// Text Underline
		$sanitized[ 'textUnderline' ] = '';
		if ( array_key_exists( 'textUnderline', $value ) && 'underline' === $value['textUnderline'] ) {
			$sanitized['textUnderline'] = 'underline';
		}

		// Line Through
		$sanitized[ 'textLineThrough' ] = '';
		if ( array_key_exists( 'textLineThrough', $value ) && 'line-through' === $value['textLineThrough'] ) {
			$sanitized['textLineThrough'] = 'line-through';
		}

		// Border Style
		$sanitized['borderStyle'] = 'none';
		if ( array_key_exists( 'borderStyle', $value ) && in_array( $value['borderStyle'], array( 'none', 'dashed', 'double', 'dotted', 'solid' ) ) ) {
			$sanitized['borderStyle'] = sanitize_text_field( $value['borderStyle'] );
		}

		// Background Image
		if ( array_key_exists( 'backgroundImage', $value ) ) {
			$sanitized['backgroundImage']        = $value['backgroundImage'];
			$sanitized['backgroundImage']['id']  = absint( $sanitized['backgroundImage']['id'] );
			$sanitized['backgroundImage']['url'] = esc_url_raw( $sanitized['backgroundImage']['url'] );

			if ( false === wp_get_attachment_image_src( $sanitized['backgroundImage']['id'] ) ) {
				$sanitized['backgroundImage'] = array(
					'id'    => (int) 0,
					'sizes' => array(),
					'url'   => '',
				);
			}
		} else {
			$sanitized['backgroundImage'] = array(
				'id'    => (int) 0,
				'sizes' => array(),
				'url'   => '',
			);
		}

		// Background Repeat
		$sanitized['backgroundRepeat'] = 'no-repeat';
		if ( array_key_exists( 'backgroundRepeat', $value ) && in_array( $value['backgroundRepeat'], array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ) ) ) {
			$sanitized['backgroundRepeat'] = sanitize_text_field( $value['backgroundRepeat'] );
		}

		// Background Position
		$sanitized['backgroundPosition'] = 'left';
		if ( array_key_exists( 'backgroundPosition', $value ) && in_array( $value['backgroundPosition'], array( 'left', 'center', 'right' ) ) ) {
			$sanitized['backgroundPosition'] = sanitize_text_field( $value['backgroundPosition'] );
		}

		// Background Style
		$sanitized['backgroundAttachment'] = 'scroll';
		if ( array_key_exists( 'backgroundAttachment', $value ) && in_array( $value['backgroundAttachment'], array( 'scroll', 'fixed' ) ) ) {
			$sanitized['backgroundAttachment'] = sanitize_text_field( $value['backgroundAttachment'] );
		}

		// Display
		$sanitized['updateDisplay'] = 'inline';
		if ( array_key_exists( 'updateDisplay', $value ) && in_array( $value['updateDisplay'], array( 'inline', 'none' ) ) ) {
			$sanitized['updateDisplay'] = sanitize_text_field( $value['updateDisplay'] );
		}

		/** This filter is documented in wp-includes/class-wp-customize-setting.php */
		return apply_filters( "customize_sanitize_{$this->id}", $sanitized, $this );
	}
}