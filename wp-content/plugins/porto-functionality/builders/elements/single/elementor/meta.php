<?php
/**
 * Porto Elementor Single Post Meta Widget
 *
 * @author     P-THEMES
 * @since      2.3.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Typography;

class Porto_Elementor_Single_Meta_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_single_meta';
	}

	public function get_title() {
		return esc_html__( 'Meta', 'porto-functionality' );
	}

	public function get_icon() {
		return 'eicon-post-info';
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'custom', 'layout', 'post', 'meta', 'date', 'author', 'category', 'tags', 'comments', 'like' );
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/post-meta-single-builder/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_single_meta',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'hide_icon',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'description' => __( 'To hide icon of metas except the date.', 'porto-functionality' ),
					'label'       => __( 'Hide Icon', 'porto-functionality' ),
				)
			);
			$this->add_control(
				'hide_by',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'description' => __( 'To hide "by" of author meta.', 'porto-functionality' ),
					'label'       => __( 'Hide letter "by"', 'porto-functionality' ),
					'default'     => '',
				)
			);
			$this->add_control(
				'post-metas',
				array(
					'type'     => Controls_Manager::SELECT2,
					'label'    => __( 'Show Post Metas', 'porto-functionality' ),
					'options'  => array(
						'date'     => 'Date',
						'author'   => 'Author',
						'cats'     => 'Category',
						'tags'     => 'Tags',
						'comments' => 'Comments',
						'like'     => 'Like',
					),
					'multiple' => true,
					'default'  => '',
				)
			);
			$this->add_control(
				'meta_align',
				array(
					'label'       => __( 'Align', 'porto-functionality' ),
					'type'        => Controls_Manager::CHOOSE,
					'description' => __( 'Controls metas alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
					'options'     => array(
						'left'   => array(
							'title' => __( 'Left', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'separator'   => 'before',
					'selectors'   => array(
						'.elementor-element-{{ID}} .post-meta' => 'text-align: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'meta_space',
				array(
					'label'       => __( 'Meta Spacing', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'To control the space between post metas.', 'porto-functionality' ),
					'size_units'  => array(
						'px',
						'rem',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} .post-meta > span' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					),
					'qa_selector' => '.post-meta > span:nth-of-type(2)',
				)
			);

			$this->add_control(
				'icon_space',
				array(
					'label'       => __( 'Icon Spacing', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'To control the space between icon and text.', 'porto-functionality' ),
					'size_units'  => array(
						'px',
						'rem',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} .post-meta > span i' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					),
					'separator'   => 'after',
					'qa_selector' => '.post-meta > span:first-of-type i',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'meta_style',
					'selector' => '.elementor-element-{{ID}} .post-meta',
				)
			);

			$this->add_control(
				'link_color',
				array(
					'label'     => __( 'Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .post-meta,
						.elementor-element-{{ID}} .post-meta i, 
						.elementor-element-{{ID}} .post-meta a' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'link_hover_color',
				array(
					'label'     => __( 'Hover Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .post-meta a:hover,.elementor-element-{{ID}} .post-meta a:focus' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'show_divider',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'description' => __( 'To show divider between the post metas.', 'porto-functionality' ),
					'label'       => __( 'Show Divider', 'porto-functionality' ),
					'default'     => '',
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'divider_color',
				array(
					'label'     => __( 'Divider Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .post-meta > span:after' => 'color: {{VALUE}}',
					),
					'condition' => array(
						'show_divider' => 'yes',
					),
				)
			);

			$this->add_control(
				'divider_space',
				array(
					'label'       => __( 'Divider Spacing', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'To control the space between meta and divider.', 'porto-functionality' ),
					'size_units'  => array(
						'px',
						'rem',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} .post-meta > span:after' => 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ': {{SIZE}}{{UNIT}};',
					),
					'condition'   => array(
						'show_divider' => 'yes',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		$atts['elementor_id'] = $this->get_id();
		echo PortoBuildersSingle::get_instance()->shortcode_single_meta( $atts );
	}
}
