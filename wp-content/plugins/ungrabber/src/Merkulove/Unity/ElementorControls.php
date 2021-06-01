<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.3
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

use Elementor\Controls_Manager;
use Merkulove\Ungrabber\ungrabber_elementor;
use Merkulove\Ungrabber\Unity\Plugin as UnityPlugin;

/**
 * Class contains custom Elementor Controls Groups
 *
 * @since 1.0.0
 **/

class ElementorControls extends ungrabber_elementor {

	/**
	 * The one true Elementor Controls.
	 *
	 * @var ElementorControls
	 **/
	private static $instance;

	/**
	 * Group of Elementor Controls for transforming element by CSS selector
	 *
	 * @param string $section - The unique name of the controls_section containing the group of controls
	 * @param string $prefix - Unique prefix of the controls group
	 * @param string $selector - CSS selector to which the control settings will be applied
	 * @param array $defaults - Array of default values [ rotate, offset_x, offset_y, transform_origin, transform_origin_x, transform_origin_y ]
	 * @param array $props - Array of controls properties [ devices, range_deg, range_px, range_percent, step, separator ]
	 * @param array $condition - Array with conditions for the entire group of controls
	 *
	 * Example of use:
	 *
	 * ElementorControls::get_instance()->Group_Controls_Transform(
	 *   'general_settings',
	 *   'rectangle_transform',
	 *   '.mdp-rectangle span',
	 *   [
	 *     'devices' => [ 'desktop', 'tablet' ],
	 *     'step' => 2
	 *   ],
	 *   [
	 *     'rotate' => 17
	 *   ],
	 *   [
	 *     'show_transform' => 'yes'
	 *   ]
	 * );
	 *
	 */
	public function Group_Controls_Transform( $section, $prefix, $selector, $defaults = [], $props = [], $condition = [] ) {

		# Properties
		$devices = $props[ 'devices' ] ? $props[ 'devices' ] : [ 'desktop', 'tablet', 'mobile' ]; // Default devices for responsive controls desktop, tablet, mobile
		$range_deg = $props[ 'range_deg' ] ? $props[ 'range_deg' ] : 360; // Default range is 360deg
		$range_px = $props[ 'range_px' ] ? $props[ 'range_px' ] : 500; // Default range is 500px
		$range_percent = $props[ 'range_percent' ] ? $props[ 'range_percent' ] : 100; // Default range is 100%
		$step = $props[ 'step' ] ? $props[ 'step' ] : 1; // Default step is equal 1

		# Separator
		$separator_before = [];
		$separator_after = [];
		if ( $props[ 'separator' ] ) {

			if ( 'before' === $props[ 'separator' ] ) {

				$separator_before = [ 'separator' => 'before' ];

			}

			if ( 'after' === $props[ 'separator' ] ) {

				$separator_after = [ 'separator' => 'after' ];

			}

		}

		# Defaults
		$rotate = $defaults[ 'rotate' ] ? $defaults[ 'rotate' ] : 0; // Default rotate is 0
		$offset_x = $defaults[ 'offset_x' ] ? $defaults[ 'offset_x' ] : 0; // Default Offset X is 0
		$offset_y = $defaults[ 'offset_y' ] ? $defaults[ 'offset_y' ] : 0; // Default Offset Y is 0
		$transform_origin = $defaults[ 'transform_origin' ] ? $defaults[ 'transform_origin' ] : 'center'; // Default Transform Origin is Center
		$transform_origin_x = $defaults[ 'transform_origin_x' ] ? $defaults[ 'transform_origin_x' ] : 0; // Default Transform Origin X for Custom Transform Origin is 0
		$transform_origin_y = $defaults[ 'transform_origin_y' ] ? $defaults[ 'transform_origin_y' ] : 0; // Default Transform Origin Y for Custom Transform Origin is 0
		$text_domain = UnityPlugin::get_slug() . '-elementor';

		# Start of section
		$this->start_controls_section(
			$section,
			[ 'condition' => $condition ]
		);

		# Transform Origin
		$this->add_responsive_control(
			$prefix . '_transform_origin',
			array_merge ( $separator_before,
				[
					'label' => __( 'Transform Origin', $text_domain ),
					'type' => Controls_Manager::SELECT,
					'devices' => $devices,
					'default' => $transform_origin,
					'options' => [
						'center'  => __( 'Default', $text_domain ),
						'top left' => __( 'Top Left', $text_domain ),
						'top center' => __( 'Top Center', $text_domain ),
						'top right' => __( 'Top Right', $text_domain ),
						'center left' => __( 'Middle Left', $text_domain ),
						'center center' => __( 'Middle Center', $text_domain ),
						'center right' => __( 'Middle Right', $text_domain ),
						'bottom left' => __( 'Bottom Left', $text_domain ),
						'bottom center' => __( 'Bottom Center', $text_domain ),
						'bottom right' => __( 'Bottom Right', $text_domain ),
						'custom' => __( 'Custom', $text_domain ),
					],
					'condition' => $condition
				]
			)
		);

		# Transform origin X
		$this->add_responsive_control(
			$prefix . '_transform_origin_x',
			[
				'label' => __( 'Origin-X', $text_domain ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0 - $range_px,
						'max' => $range_px,
						'step' => $step,
					],
					'%' => [
						'min' => 0 - $range_percent,
						'max' => $range_percent,
						'step' => $step,
					],
				],
				'devices' => $devices,
				'default' => [
					'unit' => 'px',
					'size' => $transform_origin_x,
				],
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'transform-origin: {{' . $prefix . '_transform_origin_x.SIZE}}{{' . $prefix . '_transform_origin_x.UNIT}} {{' . $prefix . '_transform_origin_y.SIZE}}{{' . $prefix . '_transform_origin_y.UNIT}}',
				],
				'condition' => array_merge( [ $prefix . '_transform_origin' => 'custom' ] , $condition )
			]
		);

		# Transform origin Y
		$this->add_responsive_control(
			$prefix . '_transform_origin_y',
			[
				'label' => __( 'Origin-Y', $text_domain ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0 - $range_px,
						'max' => $range_px,
						'step' => $step,
					],
					'%' => [
						'min' => 0 - $range_percent,
						'max' => $range_percent,
						'step' => $step,
					],
				],
				'devices' => $devices,
				'default' => [
					'unit' => 'px',
					'size' => $transform_origin_y,
				],
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'transform-origin: {{' . $prefix . '_transform_origin_x.SIZE}}{{' . $prefix . '_transform_origin_x.UNIT}} {{' . $prefix . '_transform_origin_y.SIZE}}{{' . $prefix . '_transform_origin_y.UNIT}}',
				],
				'condition' => array_merge( [ $prefix . '_transform_origin' => 'custom' ] , $condition )
			]
		);

		# Rotate
		$this->add_responsive_control(
			$prefix . '_rotate',
			[
				'label' => __( 'Rotate', $text_domain ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'range' => [
					'deg' => [
						'min' => 0 - $range_deg,
						'max' => $range_deg,
						'step' => $step,
					]
				],
				'devices' => $devices,
				'default' => [
					'unit' => 'deg',
					'size' => $rotate,
				],
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'transform: translate({{' . $prefix . '_offset_x.SIZE}}{{' . $prefix . '_offset_x.UNIT}}, {{' . $prefix . '_offset_y.SIZE}}{{' . $prefix . '_offset_y.UNIT}}) rotate({{' . $prefix . '_rotate.SIZE}}deg);',
				],
				'condition' => $condition
			]
		);

		# Offset-X
		$this->add_responsive_control(
			$prefix . '_offset_x',
			[
				'label' => __( 'Offset-X', $text_domain ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0 - $range_px,
						'max' => $range_px,
						'step' => $step,
					],
					'%' => [
						'min' => 0 - $range_percent,
						'max' => $range_percent,
						'step' => $step,
					],
				],
				'devices' => $devices,
				'default' => [
					'unit' => 'px',
					'size' => $offset_x,
				],
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'transform: translate({{' . $prefix . '_offset_x.SIZE}}{{' . $prefix . '_offset_x.UNIT}}, {{' . $prefix . '_offset_y.SIZE}}{{' . $prefix . '_offset_y.UNIT}}) rotate({{' . $prefix . '_rotate.SIZE}}deg);',
				],
				'condition' => $condition
			]
		);

		# Offset-Y
		$this->add_responsive_control(
			$prefix . '_offset_y',
			array_merge ( $separator_after,
				[
					'label' => __( 'Offset-Y', $text_domain ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0 - $range_px,
							'max' => $range_px,
							'step' => $step,
						],
						'%' => [
							'min' => 0 - $range_percent,
							'max' => $range_percent,
							'step' => $step,
						],
					],
					'devices' => $devices,
					'default' => [
						'unit' => 'px',
						'size' => $offset_y,
					],
					'selectors' => [
						'{{WRAPPER}} ' . $selector => 'transform: translate({{' . $prefix . '_offset_x.SIZE}}{{' . $prefix . '_offset_x.UNIT}}, {{' . $prefix . '_offset_y.SIZE}}{{' . $prefix . '_offset_y.UNIT}}) rotate({{' . $prefix . '_rotate.SIZE}}deg);',
					],
					'condition' => $condition
				]
			)
		);

		# End of section
		$this->end_controls_section();

	}

	/**
	 * Main Elementor Controls Instance.
	 *
	 * Insures that only one instance of Elementor exists in memory at any one time.
	 *
	 * @static
     *
	 * @return ElementorControls
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}