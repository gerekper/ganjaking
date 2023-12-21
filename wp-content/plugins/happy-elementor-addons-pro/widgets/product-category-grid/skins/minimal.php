<?php
namespace Happy_Addons_Pro\Widget\Skins\Product_Category_Grid;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Minimal extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'minimal';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Minimal', 'happy-addons-pro' );
	}
	
	/**
	 * content area style controls
	 */
	protected function content_area_style_tab_controls() {

		$this->add_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'prefix_class' => 'ha-product-cat-grid-content-align-',
			]
		);

		parent::content_area_style_tab_controls();

	}

	/**
	 * count style controls
	 */
	protected function count_style_tab_controls() {

		$this->add_control(
            '_heading_count',
            [
                'label' => __( 'Count', 'happy-addons-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
		);

        $this->add_responsive_control(
            'count_space',
            [
                'label' => __( 'Top Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
				],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-count' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-count',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-count' => 'color: {{VALUE}};'
                ],
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
        );

	}

}
