<?php
/**
 * Porto Elementor Single Builder Author Box Widget
 *
 * @author     P-THEMES
 * @since      2.3.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Porto_Elementor_Single_Author_Box_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_single_author_box';
	}

	public function get_title() {
		return esc_html__( 'Post Author Box', 'porto-functionality' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'custom', 'layout', 'post', 'image', 'thumbnail', 'gallery' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/post-author-box-single-builder/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_single_author',
			array(
				'label' => esc_html__( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => esc_html__( 'Author Title', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_title',
				'selector' => '.elementor-element-{{ID}} .post-author h3',
			)
		);

		$this->add_control(
			'author_title_color',
			array(
				'label'     => esc_html__( 'Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}}  .post-author h3' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'author_icon',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'To hide the icon of author title.', 'porto-functionality' ),
				'label'       => __( 'Hide author icon', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'author_icon_space',
			array(
				'label'       => esc_html__( 'Icon Spacing', 'porto-functionality' ),
				'description' => esc_html__( 'Set custom space of author icon.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-author h3 i' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'author_icon' => '',
				),
				'qa_selector' => '.post-author h3 i',
			)
		);

		$this->add_control(
			'author_space',
			array(
				'label'       => esc_html__( 'Author Spacing', 'porto-functionality' ),
				'description' => esc_html__( 'Set custom space of author.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-author h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_image_style',
			array(
				'label'     => esc_html__( 'Author Image', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'author_image_size',
			array(
				'label'       => esc_html__( 'Author Image Max Width', 'porto-functionality' ),
				'description' => esc_html__( 'Set max width of author image.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-author .img-thumbnail img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.post-author .img-thumbnail img',
			)
		);

		$this->add_control(
			'author_image_radius',
			array(
				'label'       => esc_html__( 'Author Image Border Radius', 'porto-functionality' ),
				'description' => esc_html__( 'Set border radius of author image.', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'%',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-author .img-thumbnail img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

			$this->add_control(
				'heading_name_style',
				array(
					'label'     => esc_html__( 'Author Name', 'porto-functionality' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'author_name',
					'selector' => '.elementor-element-{{ID}} .post-author .name a',
				)
			);

			$this->add_control(
				'author_name_color',
				array(
					'label'       => esc_html__( 'Color', 'porto-functionality' ),
					'type'        => Controls_Manager::COLOR,
					'selectors'   => array(
						'.elementor-element-{{ID}} .post-author .name a' => 'color: {{VALUE}}',
					),
					'qa_selector' => '.name a',
				)
			);

			$this->add_control(
				'heading_desc_style',
				array(
					'label'     => esc_html__( 'Author Description', 'porto-functionality' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'author_desc',
					'selector' => '.elementor-element-{{ID}} .post-author .author-content',
				)
			);

			$this->add_control(
				'author_desc_color',
				array(
					'label'       => esc_html__( 'Color', 'porto-functionality' ),
					'type'        => Controls_Manager::COLOR,
					'selectors'   => array(
						'.elementor-element-{{ID}} .post-author .author-content' => 'color: {{VALUE}}',
					),
					'qa_selector' => '.author-content',
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		echo PortoBuildersSingle::get_instance()->shortcode_single_author_box( $atts );

	}
}
