<?php
/**
 * Porto Elementor Single Post Comments
 *
 * @author     P-THEMES
 * @since      2.3.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;

class Porto_Elementor_Single_Comments_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_single_comments';
	}

	public function get_title() {
		return esc_html__( 'Post Comments', 'porto-functionality' );
	}

	public function get_icon() {
		return 'eicon-comments';
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'custom', 'layout', 'post', 'comments', 'discussion' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/post-comments-single-builder/';
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
			'comment_author_icon',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Hide Author Icon', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'comments_spacing',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Space between Comments', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space between the comments. To perform this, the post has more than 2 comments.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} ul.comments>li + li' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.comment:nth-child(2)',
			)
		);

		$this->add_control(
			'comments_reply',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Space between Comment and Reply', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space between the comment and reply object.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-respond' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'comments_form',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Space between Reply and Form', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space between the reply title and reply form.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-respond .comment-reply-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.comment-form',
			)
		);

		$this->add_control(
			'heading_image_style',
			array(
				'label' => esc_html__( 'Comments Image', 'porto-functionality' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'comment_image_between',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Image Spacing (px)', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space the avatar and the comment body.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} ul.comments>li .img-thumbnail' => 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ': -{{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.comment:first-child .img-thumbnail',
			)
		);

		$this->add_control(
			'comment_image_width',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Max Width of Comment Image', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the max width of avatar.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} ul.comments>li img.avatar' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'comment_image_radius',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Border Radius of Comment Image', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'%',
				),
				'description' => __( 'To control the border radius of avatar.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} ul.comments>li img.avatar' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => esc_html__( 'Commenter Title', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_title',
				'selector' => '.elementor-element-{{ID}} .post-comments > h3, .elementor-element-{{ID}} .post-comments > h4',
			)
		);

		$this->add_control(
			'comment_title_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of the commente title.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-comments h4 , .elementor-element-{{ID}} .post-comments h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_name_style',
			array(
				'label'     => esc_html__( 'Commenter Name', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_name',
				'selector' => '.elementor-element-{{ID}} .comment-by strong a',
			)
		);

		$this->add_control(
			'comment_name_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of the commenter.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-by strong,.elementor-element-{{ID}} .comment-by strong a' => 'color: {{VALUE}}',
				),
				'qa_selector' => '.comment:first-child .comment-by strong',
			)
		);

		$this->add_control(
			'heading_set_style',
			array(
				'label'     => esc_html__( 'Commenter Settings', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_setting',
				'selector' => '.elementor-element-{{ID}} .comment-by span a',
			)
		);

		$this->add_control(
			'comment_setting_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of commenter options.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-by span a' => 'color: {{VALUE}}',
				),
				'qa_selector' => '.comment:first-child .comment-by>span',
			)
		);

		$this->add_control(
			'comment_setting_h_color',
			array(
				'label'       => esc_html__( 'Hover Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the hover color of commenter options.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-by span a:hover,.elementor-element-{{ID}} .comment-by span a:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'heading_date_style',
			array(
				'label'     => esc_html__( 'Comment Date', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_date',
				'selector' => '.elementor-element-{{ID}} ul.comments .comment-block .date',
			)
		);

		$this->add_control(
			'comment_date_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of comment date.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} ul.comments .comment-block .date' => 'color: {{VALUE}}',
				),
				'qa_selector' => '.comment:first-child .comment-block .date',
			)
		);

		$this->add_control(
			'heading_text_style',
			array(
				'label'     => esc_html__( 'Comment Text', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_text',
				'selector' => '.elementor-element-{{ID}} .comment-block > div p',
			)
		);

		$this->add_control(
			'comment_text_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of comment content.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-block > div p' => 'color: {{VALUE}}',
				),
				'qa_selector' => '.comment:first-child .comment-block > div p',
			)
		);

		$this->add_control(
			'form_heading_style',
			array(
				'label'     => esc_html__( 'Comment Form Heading', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'form_heading',
				'selector' => '.elementor-element-{{ID}} .comment-respond .comment-reply-title',
			)
		);

		$this->add_control(
			'comment_form_heading_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of reply heading.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-respond .comment-reply-title' => 'color: {{VALUE}}',
				),
				'qa_selector' => '.comment-respond .comment-reply-title',
			)
		);

		$this->add_control(
			'form_reply_style',
			array(
				'label'       => esc_html__( 'Comment Form Reply', 'porto-functionality' ),
				'description' => __( 'These options are shown if edit or reply the comments. Try on the real page.', 'porto-functionality' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_reply',
				'selector' => '.elementor-element-{{ID}} .comment-respond #cancel-comment-reply-link',
			)
		);

		$this->add_control(
			'comment_form_reply_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of reply heading.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-respond #cancel-comment-reply-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'comment_form_reply_color_hover',
			array(
				'label'       => esc_html__( 'Hover Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the hover color of reply heading.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-respond #cancel-comment-reply-link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'comment_form_reply_space',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Space between Reply title and button', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'%',
				),
				'description' => __( 'To control the color of reply heading.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-respond #cancel-comment-reply-link' => 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_label_style',
			array(
				'label'     => esc_html__( 'Comment Form Label', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'comment_label',
				'selector' => '.elementor-element-{{ID}} .comment-form label',
			)
		);

		$this->add_control(
			'comment_form_label_color',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of reply form lable.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .comment-form label' => 'color: {{VALUE}}',
				),
				'qa_selector' => '.comment-form label',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		echo PortoBuildersSingle::get_instance()->shortcode_single_comments( $atts );
	}
}
