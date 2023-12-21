<?php
namespace Happy_Addons\Elementor\Extension;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Shapes;

defined( 'ABSPATH' ) || die();

class Shape_Divider {

	public static function init() {
		add_filter( 'elementor/shapes/additional_shapes', [__CLASS__, 'additional_shape_divider'] );
		add_action( 'elementor/element/section/section_shape_divider/before_section_end', [__CLASS__, 'update_shape_list'] );
		add_action( 'elementor/element/container/section_shape_divider/before_section_end', [__CLASS__, 'update_shape_list'] );
	}

	public static function update_shape_list( Element_Base $element ) {
		$default_shapes = [];
		$happy_shapes_top = [];
		$happy_shapes_bottom = [];

		foreach ( Shapes::get_shapes() as $shape_name => $shape_props ) {
			if ( ! isset( $shape_props['ha_shape'] ) ) {
				$default_shapes[ $shape_name ] = $shape_props['title'];
			} elseif ( ! isset( $shape_props['ha_shape_bottom'] ) ){
				$happy_shapes_top[ $shape_name ] = $shape_props['title'];
			} else {
				$happy_shapes_bottom[ $shape_name ] = $shape_props['title'];
			}
		}

		$element->update_control(
			'shape_divider_top',
			[
				'type' => Controls_Manager::SELECT,
				'groups' => [
					[
						'label' => __( 'Disable', 'happy-elementor-addons' ),
						'options' => [
							'' => __( 'None', 'happy-elementor-addons' ),
						],
					],
					[
						'label' => __( 'Default Shapes', 'happy-elementor-addons' ),
						'options' => $default_shapes,
					],
					[
						'label' => __( 'Happy Shapes', 'happy-elementor-addons' ),
						'options' => $happy_shapes_top,
					],
				],
			]
		);

		$element->update_control(
			'shape_divider_bottom',
			[
				'type' => Controls_Manager::SELECT,
				'groups' => [
					[
						'label' => __( 'Disable', 'happy-elementor-addons' ),
						'options' => [
							'' => __( 'None', 'happy-elementor-addons' ),
						],
					],
					[
						'label' => __( 'Default Shapes', 'happy-elementor-addons' ),
						'options' => $default_shapes,
					],
					[
						'label' => __( 'Happy Shapes', 'happy-elementor-addons' ),
						'options' => array_merge( $happy_shapes_top, $happy_shapes_bottom ),
					],
				],
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param array $shape_list
	 * @return void
	 */
	public static function additional_shape_divider( $shape_list ) {
		$happy_shapes = [
			'abstract-web' => [
				'title' => _x( 'Abstract Web', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/abstract-web.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/abstract-web.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'crossline' => [
				'title' => _x( 'Crossline', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/crossline.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/crossline.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'droplet' => [
				'title' => _x( 'Droplet', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/droplet.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/droplet.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'flame' => [
				'title' => _x( 'Flame', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/flame.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/flame.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'frame' => [
				'title' => _x( 'Frame', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/frame.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/frame.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'half-circle' => [
				'title' => _x( 'Half Circle', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/half-circle.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/half-circle.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'multi-cloud' => [
				'title' => _x( 'Multi Cloud', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/multi-cloud.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/multi-cloud.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'multi-wave' => [
				'title' => _x( 'Multi Wave', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/multi-wave.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/multi-wave.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'smooth-zigzag' => [
				'title' => _x( 'Smooth Zigzag', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/smooth-zigzag.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/smooth-zigzag.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'splash' => [
				'title' => _x( 'Splash', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/splash.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/splash.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'splash2' => [
				'title' => _x( 'Splash 2', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/splash2.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/splash2.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'torn-paper' => [
				'title' => _x( 'Torn Paper', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/torn-paper.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/torn-paper.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'brush' => [
				'title' => _x( 'Brush', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/brush.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/brush.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'sports' => [
				'title' => _x( 'Sports', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/sports.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/sports.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
				'ha_shape_bottom' => true,
			],
			'landscape' => [
				'title' => _x( 'Landscape', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/landscape.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/landscape.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
				'ha_shape_bottom' => true,
			],
			'nature' => [
				'title' => _x( 'Nature', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/nature.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/nature.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
				'ha_shape_bottom' => true,
			],
			'desert' => [
				'title' => _x( 'Desert', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/desert.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/desert.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
				'ha_shape_bottom' => true,
			],
			'under-water' => [
				'title' => _x( 'Under Water', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/under-water.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/under-water.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
				'ha_shape_bottom' => true,
			],
			'cityscape-layer' => [
				'title' => _x( 'Cityscape Layer', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/cityscape-layer.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/cityscape-layer.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
				'ha_shape_bottom' => true,
			],
			'drop' => [
				'title' => _x( 'Drop', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/drop.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/drop.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'mosque' => [
				'title' => _x( 'Mosque', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/mosque.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/mosque.svg',
				'has_flip' => true,
				'has_negative' => false,
				'ha_shape' => true,
			],
			'christmas' => [
				'title' => _x( 'Christmas', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/christmas.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/christmas.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			],
			'christmas2' => [
				'title' => _x( 'Christmas 2', 'Shapes', 'happy-elementor-addons' ),
				'path' => HAPPY_ADDONS_DIR_PATH . 'assets/imgs/shape-divider/christmas2.svg',
				'url' => HAPPY_ADDONS_ASSETS . 'imgs/shape-divider/christmas2.svg',
				'has_flip' => true,
				'has_negative' => true,
				'ha_shape' => true,
			]
		];

		/*
		 * svg path should contain elementor class to show in editor mode
		*/
		return array_merge( $happy_shapes, $shape_list );
	}
}

Shape_Divider::init();
