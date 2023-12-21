<?php
/**
 * Link Hover widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons\Elementor\Traits\Link_Hover_Markup;

class Link_Hover extends Base {
	use Link_Hover_Markup;

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Animated Link', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/link-hover/';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-animated-link';
	}

	public function get_keywords() {
		return array('link', 'hover', 'animation');
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_title',
			array(
				'label' => __( 'Link Content', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'animation_style',
			array(
				'label'   => __( 'Animation Style', 'happy-elementor-addons' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'carpo',
				'options' => array(
					'carpo'   => __( 'Carpo', 'happy-elementor-addons' ),
					'carme'   => __( 'Carme', 'happy-elementor-addons' ),
					'dia'     => __( 'Dia', 'happy-elementor-addons' ),
					'eirene'  => __( 'Eirene', 'happy-elementor-addons' ),
					'elara'   => __( 'Elara', 'happy-elementor-addons' ),
					'ersa'    => __( 'Ersa', 'happy-elementor-addons' ),
					'helike'  => __( 'Helike', 'happy-elementor-addons' ),
					'herse'   => __( 'Herse', 'happy-elementor-addons' ),
					'io'      => __( 'Io', 'happy-elementor-addons' ),
					'iocaste' => __( 'Iocaste', 'happy-elementor-addons' ),
					'kale'    => __( 'Kale', 'happy-elementor-addons' ),
					'leda'    => __( 'Leda', 'happy-elementor-addons' ),
					'metis'   => __( 'Metis', 'happy-elementor-addons' ),
					'mneme'   => __( 'Mneme', 'happy-elementor-addons' ),
					'thebe'   => __( 'Thebe', 'happy-elementor-addons' ),
				),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'       => __( 'Title', 'happy-elementor-addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Animated Link', 'happy-elementor-addons' ),
				'placeholder' => __( 'Type Link Title', 'happy-elementor-addons' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_responsive_control(
            'link_align',
            [
                'label' => __( 'Alignment', 'happy-elementor-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'happy-elementor-addons' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'happy-elementor-addons' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'happy-elementor-addons' ),
                        'icon' => 'eicon-text-align-right',
                    ]
                ],
                'default' => 'left',
                'toggle' => true,
                // 'prefix_class' => 'ha-align-',
                'selectors_dictionary' => [
                    'left' => 'justify-content: flex-start',
                    'center' => 'justify-content: center',
                    'right' => 'justify-content: flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha_content__item' => '{{VALUE}}'
                ]
            ]
        );

		$this->add_control(
			'link_url',
			array(
				'label'         => __( 'Link', 'happy-elementor-addons' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'happy-elementor-addons' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => true,
				),
			)
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {

		$this->start_controls_section(
			'_section_media_style',
			array(
				'label' => __( 'Link Content', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Content Box Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .ha_content__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Link Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ha-link' => 'color: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Link Hover Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ha-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-link',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		self::{'render_' . $settings['animation_style'] . '_markup'}( $settings );
	}
}
