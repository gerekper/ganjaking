<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Header_Area_Widget extends Widget_Base {

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
		return 'appside-header-area-widget';
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
		return esc_html__( 'Appside Header One', 'aapside-master' );
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
				'label' => esc_html__( 'General Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'theme',
			[
				'label'       => esc_html__( 'Theme', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'select theme', 'aapside-master' ),
				'options' => array(
				        'header-bg' => esc_html__('Style 01','aapside-master'),
				        'header-bg-2 style-two' => esc_html__('Style 02','aapside-master'),
				    ),
				'default'     => 'header-bg'
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter title.', 'aapside-master' ),
				'default'     => esc_html__( 'Make cool landing with appside', 'aapside-master' )
			]
		);
		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter description.', 'aapside-master' ),
				'default'     => esc_html__( 'Appside is the best app landing page which will help you showcase your business, lifestyle, social, or shopping app in the best possible manner.', 'aapside-master' )
			]
		);
		$this->add_control(
			'btn_1_text',
			[
				'label'       => esc_html__( 'Button One Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter button text.', 'aapside-master' ),
				'default'     => esc_html__( 'Download', 'aapside-master' )
			]
		);
		$this->add_control(
			'btn_1_link',
			[
				'label'       => esc_html__( 'Button One Link', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url' => '#'
				),
				'description' => esc_html__( 'enter button url.', 'aapside-master' ),
			]
		);
		$this->add_control(
			'btn_2_text',
			[
				'label'       => esc_html__( 'Button Two Text', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter button text.', 'aapside-master' ),
				'default'     => esc_html__( 'Learn More', 'aapside-master' )
			]
		);
		$this->add_control(
			'btn_2_link',
			[
				'label'       => esc_html__( 'Button Two Link', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url' => '#'
				),
				'description' => esc_html__( 'enter button url.', 'aapside-master' ),
			]
		);
		$this->add_control(
			'right_image',
			[
				'label'       => esc_html__( 'Right Image', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'description' => esc_html__( 'select image.', 'aapside-master' ),
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'images',
			[
				'label' => esc_html__('Images', 'aapside-master'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'bg_image',
			[
				'label'       => esc_html__( 'Background Image', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'description' => esc_html__( 'select image.', 'aapside-master' ),
			]
		);
		$this->add_control(
			'bg_color',
			[
				'label'       => esc_html__( 'Background Color', 'aapside-master' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => 'transparent',
				'description' => esc_html__( 'select background-color.', 'aapside-master' ),
				'selectors' => [
					"{{WRAPPER}} .header-area.appside-header-01" => "background-color:{{VALUE}}"
				]
			]
		);
		$this->add_control(
			'animation_image_one',
			[
				'label'       => esc_html__( 'Animation Image One', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'description' => esc_html__( 'select image.', 'aapside-master' ),
			]
		);
		$this->add_control(
			'animation_image_two',
			[
				'label'       => esc_html__( 'Animation Image Two', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'description' => esc_html__( 'select image.', 'aapside-master' ),
			]
		);
		$this->add_control(
			'animation_image_three',
			[
				'label'       => esc_html__( 'Animation Image Three', 'aapside-master' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src()
				),
				'description' => esc_html__( 'select image.', 'aapside-master' ),
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'paddings',
			[
				'label' => esc_html__('Padding', 'aapside-master'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control('padding',[
			'label' => esc_html__('Padding' ,'aapside-master'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px','em'],
			'allowed_dimensions' => ['top','bottom'],
			'selectors' => [
				'{{WRAPPER}} .appside-header-01' => 'padding-top: {{TOP}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};'
			],
			'description' => esc_html__('set padding for header area ','aapside-master')
		]);
		$this->end_controls_section();
		$this->start_controls_section(
			'css_selector',
			[
				'label' => esc_html__('CSS Selectors', 'aapside-master'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'section_id',
			[
				'label' => esc_html__('ID', 'aapside-master'),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__('enter section id.', 'aapside-master')
			]
		);
		$this->end_controls_section();

		$this->start_controls_section( 'styling_settings_section', [
			'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		]);
		$this->add_control('title_color',[
			'label' => esc_html__('Title Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .header-area .header-inner .title" => "color: {{VALUE}}"
			]
		]);
		$this->add_control('description_color',[
			'label' => esc_html__('Description Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .header-area .header-inner p" => "color: {{VALUE}}"
			]
		]);
		$this->add_control('divider',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .header-area .header-inner .title"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'description_typography',
			'label' => esc_html__('Description Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .header-area .header-inner p"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'btn_typography',
			'label' => esc_html__('Button Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .appside-header-01 .btn-wrapper .boxed-btn"
		]);
		$this->end_controls_section();

		/* button one styling area start */
		$this->start_controls_section( 'button_one_styling_section', [
			'label' => esc_html__( 'Button One Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		]);

        $this->start_controls_tabs('button_one_background');

        $this->start_controls_tab('button_one_normal_style',[
           'label' => esc_html__('Normal','aapside-master')
        ]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'button_one_normal_background',
			'label'  => esc_html__('Button Background ','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:first-child"
		]);
		$this->add_control('button_one_normal_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Button Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn:first-child" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'button_one_border',
			'label' => esc_html__('Border','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:first-child"
		]);

        $this->end_controls_tab();

		$this->start_controls_tab('button_one_hover_style',[
			'label' => esc_html__('Hover','aapside-master')
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'button_one_hover_normal_background',
			'label'  => esc_html__('Button Background ','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:first-child:hover"
		]);
		$this->add_control('button_one_hover_normal_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Button Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn:first-child:hover" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'button_one_hover_border',
			'label' => esc_html__('Border','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:first-child:hover"
		]);
		$this->end_controls_tab();

        $this->end_controls_tabs();
		$this->add_control(
			'button_one_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .btn-wrapper .boxed-btn:first-child' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/* button one styling area end */

		/* button two styling area start */
		$this->start_controls_section( 'button_two_styling_section', [
			'label' => esc_html__( 'Button Two Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		]);
		$this->start_controls_tabs('button_two_background');

		$this->start_controls_tab('button_two_normal_style',[
			'label' => esc_html__('Normal','aapside-master')
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'button_two_normal_background',
			'label'  => esc_html__('Button Background ','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:last-child"
		]);
		$this->add_control('button_two_normal_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Button Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn:last-child" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'button_two_border',
			'label' => esc_html__('Border','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:last-child"
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('button_two_hover_style',[
			'label' => esc_html__('Hover','aapside-master')
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'button_two_hover_normal_background',
			'label'  => esc_html__('Button Background ','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:last-child:hover"
		]);
		$this->add_control('button_two_hover_normal_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Button Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn:last-child:hover" => "color: {{VALUE}}"
			]
		]);
		$this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'button_two_hover_border',
			'label' => esc_html__('Border','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn:last-child:hover"
		]);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'button_two_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .btn-wrapper .boxed-btn:last-child' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/* button two styling area end */


		$this->start_controls_section( 'right_image_styling_section', [
			'label' => esc_html__( 'Right Image Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		]);
		$this->add_control(
			'right_image_right_position',
			[
				'label' => esc_html__( 'Right Position', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-area .header-right-image' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'right_image_bottom_position',
			[
				'label' => esc_html__( 'Top Position', 'aapside-master' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-area .header-right-image' => 'top: {{SIZE}}{{UNIT}};buttom:auto;',
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
		// background image
		$bg_image     = $settings['bg_image']['id'];
		$bg_image_url = wp_get_attachment_image_src( $bg_image, 'full' )[0];
		// right
		$right_image     = $settings['right_image']['id'];
		$right_image_url = wp_get_attachment_image_src( $right_image, 'full' )[0];
		$right_image_alt = get_post_meta( $right_image, '_wp_get_image_attachment_alt', true );
		// one
		$animation_image_one     = $settings['animation_image_one']['id'];
		$animation_image_one_url = wp_get_attachment_image_src( $animation_image_one, 'full' )[0];
		$animation_image_one_alt = get_post_meta( $animation_image_one, '_wp_get_image_attachment_alt', true );
		// two
		$animation_image_two     = $settings['animation_image_two']['id'];
		$animation_image_two_url = wp_get_attachment_image_src( $animation_image_two, 'full' )[0];
		$animation_image_two_alt = get_post_meta( $animation_image_two, '_wp_get_image_attachment_alt', true );
		// three
		$animation_image_three     = $settings['animation_image_three']['id'];
		$animation_image_three_url = wp_get_attachment_image_src( $animation_image_three, 'full' )[0];
		$animation_image_three_alt = get_post_meta( $animation_image_three, '_wp_get_image_attachment_alt', true );

		//button one
        $this->add_render_attribute('button_one','class','boxed-btn btn-rounded');
        if (!empty($settings['btn_1_link']['url'])){
            $this->add_link_attributes('button_one',$settings['btn_1_link']);
        }
        //button two
		$this->add_render_attribute('button_two','class','boxed-btn btn-rounded blank');
		if (!empty($settings['btn_2_link']['url'])){
			$this->add_link_attributes('button_two',$settings['btn_2_link']);
		}
		?>
        <div class="header-area appside-header-01  <?php echo esc_attr($settings['theme'])?>" id="<?php echo esc_attr($settings['section_id'])?>" style="background-image: url(<?php echo esc_url($bg_image_url)?>)">
            <div class="shape-1"><img src="<?php echo esc_url( $animation_image_one_url ); ?>" alt="<?php echo esc_attr( $animation_image_one_alt ); ?>"></div>
            <div class="shape-2"><img src="<?php echo esc_url( $animation_image_two_url ); ?>" alt="<?php echo esc_attr( $animation_image_two_alt ); ?>"></div>
            <div class="shape-3"><img src="<?php echo esc_url( $animation_image_three_url ); ?>" alt="<?php echo esc_attr( $animation_image_three_alt ); ?>"></div>
            <div class="header-right-image">
                <img src="<?php echo esc_url( $right_image_url ); ?>" alt="<?php echo esc_attr( $right_image_alt ); ?>">
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="header-inner">
                            <h1 class="title"><?php echo esc_html( $settings['title'] ); ?></h1>
                            <p><?php echo esc_html( $settings['description'] ); ?></p>
                            <div class="btn-wrapper margin-top-30">
								<?php if ( $settings['btn_1_text'] && $settings['btn_1_link']['url'] ): ?>
                                    <a <?php echo $this->get_render_attribute_string('button_one');?>><?php echo esc_html($settings['btn_1_text']);?></a>
								<?php endif; ?>
								<?php if ( $settings['btn_2_text'] && $settings['btn_2_link']['url'] ): ?>
                                    <a <?php echo $this->get_render_attribute_string('button_two');?>><?php echo esc_html($settings['btn_2_text']);?></a>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Header_Area_Widget() );