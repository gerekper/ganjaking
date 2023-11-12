<?php
/**
 * UAEL Section Divider feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\SectionDivider;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Shapes;
use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Get Module Name.
	 *
	 * @return string Module name.
	 * @since 1.35.0
	 * @access public
	 */
	public function get_name() {
		return 'uael-section-divider';
	}

	/**
	 * Module should load or not.
	 *
	 * @return bool true|false.
	 * @since 1.35.0
	 * @access public
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Check if this is a widget.
	 *
	 * @return bool true|false.
	 * @since 1.35.0
	 * @access public
	 */
	public function is_widget() {
		return false;
	}

	/**
	 * Get Widgets.
	 *
	 * @return array Widgets.
	 * @since 1.35.0
	 * @access public
	 */
	public function get_widgets() {
		return array(
			'SectionDivider',
		);
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		parent::__construct();

		if ( UAEL_Helper::is_widget_active( 'SectionDivider' ) ) {
			$this->add_actions();

		}
	}

	/**
	 * Add actions required to run the widget.
	 *
	 * @since 1.35.0
	 * @access private
	 */
	private function add_actions() {
		add_filter( 'elementor/shapes/additional_shapes', array( __CLASS__, 'uael_divider_list' ) );
		add_action( 'elementor/element/section/section_shape_divider/before_section_end', array( __CLASS__, 'uael_divider_control' ) );
	}

	/**
	 * Updates elementor's control to show UAE section dividers.
	 *
	 * @param Element_Base $element returns controls array.
	 */
	public static function uael_divider_control( Element_Base $element ) {
		$default_shapes     = array();
		$uael_shapes_top    = array();
		$uael_shapes_bottom = array();

		foreach ( Shapes::get_shapes() as $shape_name => $shape_props ) {
			if ( ! isset( $shape_props['uael_shape'] ) ) {
				$default_shapes[ $shape_name ] = $shape_props['title'];
			} elseif ( ! isset( $shape_props['uael_shape_bottom'] ) ) {
				$uael_shapes_top[ $shape_name ] = $shape_props['title'];
			} else {
				$uael_shapes_bottom[ $shape_name ] = $shape_props['title'];
			}
		}

		$element->update_control(
			'shape_divider_top',
			array(
				'type'   => Controls_Manager::SELECT,
				'groups' => array(
					array(
						'label'   => __( 'Disable', 'uael' ),
						'options' => array(
							'' => __( 'None', 'uael' ),
						),
					),
					array(
						'label'   => __( 'Elementor Shapes', 'uael' ),
						'options' => $default_shapes,
					),
					array(
						'label'   => __( 'UAE Shapes', 'uael' ),
						'options' => $uael_shapes_top,
					),
				),
			)
		);

		$element->update_control(
			'shape_divider_bottom',
			array(
				'type'   => Controls_Manager::SELECT,
				'groups' => array(
					array(
						'label'   => __( 'Disable', 'uael' ),
						'options' => array(
							'' => __( 'None', 'uael' ),
						),
					),
					array(
						'label'   => __( 'Elementor Shapes', 'uael' ),
						'options' => $default_shapes,
					),
					array(
						'label'   => __( 'UAE Shapes', 'uael' ),
						'options' => $uael_shapes_bottom,
					),
				),
			)
		);

		$element->update_control(
			'shape_divider_top_color',
			array(
				'condition' => array(
					'shape_divider_top!' => array( '', 'xmas-lights' ),
				),
			)
		);
	}

	/**
	 * Add UAE section dividers to existing list.
	 *
	 * @param array $elementor_dividers Dividers list.
	 *
	 * @return array
	 */
	public static function uael_divider_list( $elementor_dividers ) {
		$uael_dividers = array(
			'xmas-trees'  => array(
				'title'             => _x( 'Xmas Trees', 'Shapes', 'uael' ),
				'path'              => UAEL_DIR . 'assets/img/section-divider/xmas-trees.svg',
				'url'               => UAEL_URL . 'assets/img/section-divider/xmas-trees.svg',
				'has_flip'          => true,
				'has_negative'      => false,
				'uael_shape'        => true,
				'uael_shape_bottom' => true,
			),
			'xmas-lights' => array(
				'title'        => _x( 'Xmas Lights', 'Shapes', 'uael' ),
				'path'         => UAEL_DIR . 'assets/img/section-divider/xmas-lights.svg',
				'url'          => UAEL_URL . 'assets/img/section-divider/xmas-lights.svg',
				'has_flip'     => true,
				'has_negative' => true,
				'uael_shape'   => true,
			),
			'halloween'   => array(
				'title'             => _x( 'Halloween', 'Shapes', 'uael' ),
				'path'              => UAEL_DIR . 'assets/img/section-divider/halloween.svg',
				'url'               => UAEL_URL . 'assets/img/section-divider/halloween.svg',
				'has_flip'          => true,
				'has_negative'      => false,
				'uael_shape'        => true,
				'uael_shape_bottom' => true,
			),
		);

		return array_merge( $uael_dividers, $elementor_dividers );
	}
}
