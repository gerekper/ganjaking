<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Header_Area_One_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Elementor widget name.
	 *
	 * @return string Widget name.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'appside-header-area-two-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Elementor widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return esc_html__( 'Appside Header Two', 'aapside-master' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Elementor widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-archive-title';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Elementor widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [ 'appside_widgets' ];
	}

	/**
	 * Register Elementor widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__( 'Section Contents', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'A better way to connect with your customers', 'aapside-master' ),
				'description' => esc_html__( 'enter title text', 'aapside-master' )
			]
		);
		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'To increase sales by skyrocketing communication with All messages in one simple dashboard it now takes seconds.', 'aapside-master' ),
				'description' => esc_html__( 'enter description', 'aapside-master' )
			]
		);
		$this->add_control(
			'image',
			[
				'label'       => esc_html__( 'Image', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'upload iamge.', 'aapside-master' ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				)
			]
		);
		$this->add_control(
			'image_btn',
			[
				'label'       => esc_html__( 'Image Button', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'enable image button insted of text', 'aapside-master' )
			]
		);
		$this->add_control(
			'btn_text',
			[
				'label'       => esc_html__( 'Button Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter button text.', 'aapside-master' ),
				'default'     => esc_html__( 'Learn More', 'aapside-master' ),
				'condition'   => array(
					'image_btn' => ''
				)
			]
		);
		$this->add_control(
			'btn_one_image',
			[
				'label'       => esc_html__( 'Button One Image', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'upload iamge.', 'aapside-master' ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'condition'   => array(
					'image_btn' => 'yes'
				)
			]
		);

		$this->add_control(
			'btn_link',
			[
				'label'       => esc_html__( 'Button Link', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url' => '#'
				),
				'description' => esc_html__( 'enter button url.', 'aapside-master' ),
			]
		);
		$this->add_control(
			'btn_text_two',
			[
				'label'       => esc_html__( 'Button Two Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter button text.', 'aapside-master' ),
				'default'     => esc_html__( 'Learn More', 'aapside-master' ),
				'condition'   => array(
					'image_btn' => ''
				)
			]
		);
		$this->add_control(
			'btn_two_image',
			[
				'label'       => esc_html__( 'Button Two Image', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'upload iamge.', 'aapside-master' ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'condition'   => array(
					'image_btn' => 'yes'
				)
			]
		);
		$this->add_control(
			'btn_link_two',
			[
				'label'       => esc_html__( 'Button Two Link', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url' => '#'
				),
				'description' => esc_html__( 'enter button url.', 'aapside-master' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'background_image',
			[
				'label' => esc_html__( 'Background Image', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'background_image',
				'label'    => esc_html__( 'Background Image', 'aapside-master' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .appside-header-09'
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'background_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'background_overlay',
				'label'    => esc_html__( 'Background Overlay', 'aapside-master' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .appside-header-09-overlay'
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'css_styles',
			[
				'label' => esc_html__( 'Padding', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control( 'padding', [
			'label'              => esc_html__( 'Padding', 'aapside-master' ),
			'type'               => Controls_Manager::DIMENSIONS,
			'size_units'         => [ 'px', 'em' ],
			'allowed_dimensions' => [ 'top', 'bottom' ],
			'selectors'          => [
				'{{WRAPPER}} .appside-header-09' => 'padding-top: {{TOP}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};'
			],
			'description'        => esc_html__( 'set padding for header area ', 'aapside-master' )
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'styling_settings',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'title_color',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'select title.', 'aapside-master' ),
				'selectors'   => [
					"{{WRAPPER}} .appside-header-09 .header-inner .title" => "color:{{VALUE}}"
				]
			]
		);
		$this->add_control(
			'description_color',
			[
				'label'       => esc_html__( 'Description', 'aapside-master' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'select description.', 'aapside-master' ),
				'selectors'   => [
					"{{WRAPPER}} .appside-header-09 .header-inner p" => "color:{{VALUE}}"
				]
			]
		);
		$this->end_controls_section();
		/* Typography Settings start */
		$this->start_controls_section(
			'typography_settings',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			"selector" => "{{WRAPPER}} .appside-header-09 .header-inner .title"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'label'    => esc_html__( 'Description Typography', 'aapside-master' ),
			"selector" => "{{WRAPPER}} .appside-header-09 .header-inner p"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'label'    => esc_html__( 'Button Typography', 'aapside-master' ),
			"selector" => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02"
		] );

		$this->end_controls_section();
		/* Typography Settings end */

		$this->start_controls_section(
			'button_one_styling_section',
			[
				'label' => esc_html__( 'Button One Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'button_one_background' );

		$this->start_controls_tab( 'normal_one_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_one_background',
			'selector'    => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child",
			'description' => esc_html__( 'button background', 'aapside-master' )
		] );
		$this->add_control( 'gd_one_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_one_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child i" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_one_normal_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child"
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_one_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_one_hover_background',
			'selector'    => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child:hover",
			'description' => esc_html__( 'button hover background', 'aapside-master' )
		] );
		$this->add_control( 'gd_one_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_one_hover_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child:hover i" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_one_hover_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:first-child:hover"
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'button_two_styling_section',
			[
				'label' => esc_html__( 'Button Two Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'button_two_background' );

		$this->start_controls_tab( 'normal_two_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_two_background',
			'selector'    => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child",
			'description' => esc_html__( 'button background', 'aapside-master' )
		] );
		$this->add_control( 'gd_two_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_two_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child i" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_two_normal_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child"
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_two_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_two_hover_background',
			'selector'    => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child:hover",
			'description' => esc_html__( 'button hover background', 'aapside-master' )
		] );
		$this->add_control( 'gd_two_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_two_hover_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child:hover i" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_two_hover_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .appside-header-09 .header-inner .btn-wrapper .boxed-btn-02:last-child:hover"
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'css_selector',
			[
				'label' => esc_html__( 'CSS Selectors', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'section_id',
			[
				'label'       => esc_html__( 'ID', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter section id.', 'aapside-master' )
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'right_image_selector',
			[
				'label' => esc_html__( 'Right Image Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'padding_top_right_image',
			[
				'label' => esc_html__( 'Padding Top', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .appside-header-09 .right-img' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Render Elementor widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$img_id   = $settings['image']['id'];
		$img_url  = wp_get_attachment_image_src( $img_id, 'full' );
		$img_alt  = get_post_meta( $img_id, '_wp_get_attachment_image_alt', true );

		//btn img one
		$btn_img_one_id  = $settings['btn_one_image']['id'];
		$btn_img_one     = wp_get_attachment_image_src( $btn_img_one_id, 'full' );
		$btn_img_one_alt = get_post_meta( $btn_img_one_id, '_wp_get_attachment_image_alt', true );
		//btn img two
		$btn_img_two_id  = $settings['btn_two_image']['id'];
		$btn_img_two     = wp_get_attachment_image_src( $btn_img_two_id, 'full' );
		$btn_img_two_alt = get_post_meta( $btn_img_two_id, '_wp_get_attachment_image_alt', true );

		//btn one
        if ('yes' == $settings['image_btn']){
	        $this->add_render_attribute('btn_one','class','boxed_img_btn');
        }else{
            $this->add_render_attribute('btn_one','class','boxed-btn-02 reverse-color');
        }
        if (!empty($settings['btn_link']['url'])){
            $this->add_link_attributes('btn_one',$settings['btn_link']);
        }
        //btn two
		if ('yes' == $settings['image_btn']){ 
			$this->add_render_attribute('btn_two','class','boxed_img_btn');
		}else{
			$this->add_render_attribute('btn_two','class','boxed-btn-02 blank');
		}
		if (!empty($settings['btn_link_two']['url'])){
			$this->add_link_attributes('btn_two',$settings['btn_link_two']);
		}

		?>
        <header class="appside-header-09" id="<?php echo esc_attr( $settings['section_id'] ) ?>">
            <div class="appside-header-09-overlay"></div>
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-6">
                        <div class="header-inner">
                            <h1 class="title wow fadeInDown"><?php echo esc_html( $settings['title'] ) ?></h1>
                            <p><?php echo esc_html( $settings['description'] ) ?></p>
                            <div class="btn-wrapper wow fadeInUp">
								<?php

								if ( 'yes' == $settings['image_btn'] ): ?>

                                    <a <?php echo $this->get_render_attribute_string('btn_one');?>>
                                        <img src="<?php echo esc_url( $btn_img_one[0] ) ?>"
                                             alt="<?php echo esc_attr( $btn_img_one_alt ) ?>">
                                    </a>
                                    <a <?php echo $this->get_render_attribute_string('btn_two');?>>
                                        <img src="<?php echo esc_url( $btn_img_two[0] ) ?>"
                                             alt="<?php echo esc_attr( $btn_img_two_alt ) ?>">
                                    </a>

								<?php else: ?>

                                    <a <?php echo $this->get_render_attribute_string('btn_one');?>>
                                        <i class="flaticon-apple-1"></i> <?php echo esc_html( $settings['btn_text'] ) ?>
                                    </a>
                                    <a <?php echo $this->get_render_attribute_string('btn_two');?>>
                                        <i class="flaticon-android-logo"></i> <?php echo esc_html( $settings['btn_text_two'] ) ?>
                                    </a>

								<?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="right-img">
                            <div class="img-wrapper">
                                <img src="<?php echo esc_url( $img_url[0] ) ?>"
                                     alt="<?php echo esc_attr( $img_alt ) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Header_Area_One_Widget() );