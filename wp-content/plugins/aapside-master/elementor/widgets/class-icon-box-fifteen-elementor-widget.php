<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Icon_Box_Fifteen_Widget extends Widget_Base {

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
		return 'appside-icon-box-fifteen-widget';
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
		return esc_html__( 'Icon Box: 15', 'aapside-master' );
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
		return 'eicon-alert';
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
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter  title.', 'aapside-master' ),
				'default'     => esc_html__( 'Free Install', 'aapside-master' )
			]
		);
		$this->add_control( 'icon_type', [
			'label'       => esc_html__( 'Icon Type', 'aapside-master' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => [
				'icon'  => esc_html__( 'Icon', 'aapside-master' ),
				'image' => esc_html__( 'Image', 'aapside-master' )
			],
			'default'     => 'icon',
			'description' => esc_html__( 'select theme', 'aapside-master' )
		] );

		$this->add_control( 'image', [
			'label' => esc_html__( 'Image Icon', 'aapside-master' ),
			'type'  => Controls_Manager::MEDIA,
            'description' => esc_html__('upload image icon','aapside-master'),
            'condition' => ['icon_type' => 'image']
		] );

		if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$this->add_control(
				'icon',
				[
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICONS,
					'description' => esc_html__( 'select Icon.', 'aapside-master' ),
					'default'     => [
						'value'   => 'fas fa-star',
						'library' => 'solid',
					],
					'condition'   => [ 'icon_type' => 'icon' ]
				]
			);
		} else {
			$this->add_control(
				'icon',
				[
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select Icon.', 'aapside-master' ),
					'default'     => 'flaticon-vector',
					'condition'   => [ 'icon_type' => 'icon' ]
				]
			);
		}

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter text.', 'aapside-master' ),
				'default'     => esc_html__( 'Inspiration comes in many ways and you like to save', 'aapside-master' )
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icon_styling_section',
			[
				'label' => esc_html__( 'Icon Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_group_control(Group_Control_Background::get_type(),[
           'label' => esc_html__('Background','aapside-master'),
           'name' => 'icon_box_background',
           'selector' => "{{WRAPPER}} .single-icon-item-15 .icon"
        ]);
		$this->add_control(
			'icon_border_radius',
			[
				'label' => esc_html__( 'Icon Border Radius', 'aapside-master' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-icon-item-15 .icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_bottom_space',
			[
				'label' => esc_html__( 'Icon Right Space', 'aapside-master' ),
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
					'{{WRAPPER}} .single-icon-item-15 .img-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .single-icon-item-15 .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control( 'icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-icon-item-15 .icon"          => "color: {{VALUE}}",
				"{{WRAPPER}} .single-icon-item-15 .icon svg path" => "fill: {{VALUE}}",
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'styling_section',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'title_bottom_space',
			[
				'label' => esc_html__( 'Title Bottom Space', 'aapside-master' ),
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
					'{{WRAPPER}} .single-icon-item-15 .content .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-icon-item-15 .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-icon-item-15 .content p" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'typography_styling_section',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-icon-item-15 .content .title"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'label'    => esc_html__( 'Description Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-icon-item-15 .content p"
		] );
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
		$this->add_render_attribute( 'icon_box_wrapper', 'class', 'single-icon-item-15' );

		?>
        <div <?php echo $this->get_render_attribute_string( 'icon_box_wrapper' ); ?>>
            <?php if ($settings['icon_type'] == 'icon'): ?>
            <div class="icon">
				<?php
                    if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
                        ! empty( $settings['icon']['value'] ) ? Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ) : '';
                    } else {
                        ! empty( $settings['icon'] ) ? printf( '<i class="%1$s"></i>', esc_attr( $settings['icon'] ) ) : '';
                    }
				?>
            </div>
            <?php else:?>
                <div class="img-icon">
                    <?php
                    $img_icon_id = $settings['image']['id'];
                    $img_icon_url = !empty($img_icon_id) ? wp_get_attachment_image_src($img_icon_id,'full')[0] : '';
                    $img_icon_alt = !empty($img_icon_id) ? get_post_meta($img_icon_id,'_wp_image_attachment_alt',true) : '';
                    printf('<img src="%1$s" alt="%2$s"/>',esc_url($img_icon_url),esc_attr($img_icon_alt));
                    ?>
                </div>
            <?php endif;?>
            <div class="content">
                <h4 class="title"><?php echo esc_html__( $settings['title'] ) ?></h4>
                <p><?php echo esc_html__( $settings['description'] ) ?></p>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Icon_Box_Fifteen_Widget() );