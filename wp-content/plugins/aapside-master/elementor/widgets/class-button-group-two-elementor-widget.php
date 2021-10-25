<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Button_Group_Two extends Widget_Base {

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
		return 'appside-button-group-two-widget';
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
		return esc_html__( 'Button Group: 02', 'aapside-master' );
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
		return 'eicon-button';
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
		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignment', 'aapside-master' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => esc_html__( 'Left', 'aapside-master' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'aapside-master' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'aapside-master' ),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .appside-button-group' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control( 'button_items', [
			'label'       => esc_html__( 'Buttons', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'theme'    => 'gd-bg-1',
					'btn_text' => esc_html__( 'Learn More', 'aapside-master' ),
					'btn_link' => array(
						'url' => '#'
					)
				]
			],
			'fields'      => [
				[
					'name'        => 'theme',
					'label'       => esc_html__( 'Theme', 'aapside-master' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'select theme.', 'aapside-master' ),
					'options'     => array(
						'gd-bg-1' => esc_html__( 'Gradient One' ,'aapside-master'),
						'gd-bg-2' => esc_html__( 'Gradient Two','aapside-master' ),
						'gd-bg-3' => esc_html__( 'Gradient Three' ,'aapside-master'),
					),
					'default'     => 'gd-bg-1'
				],

				[
					'name'        => 'icon',
					'label'       => esc_html__( 'Button Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'enter button icon.', 'aapside-master' ),
					'default'     => 'flaticon-apple-1'
				],

				[
					'name'        => 'btn_text',
					'label'       => esc_html__( 'Button Text', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter button text.', 'aapside-master' ),
					'default'     => esc_html__( 'Learn More', 'aapside-master' )
				],

				[
					'name'        => 'btn_link',
					'label'       => esc_html__( 'Button Link', 'aapside-master' ),
					'type'        => Controls_Manager::URL,
					'default'     => array(
						'url' => '#'
					),
					'description' => esc_html__( 'enter button url.', 'aapside-master' ),
				],

				[
					'name'        => 'btn_rounded',
					'label'       => esc_html__( 'Button Rounded', 'aapside-master' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'enable button rounded.', 'aapside-master' ),
					'default'     => 'no'
				],

				[
					'name'        => 'btn_white_color',
					'label'       => esc_html__( 'Button White Color', 'aapside-master' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'enable button white color.', 'aapside-master' ),
					'default'     => 'no'
				]
			],
			'title_field' => "{{{ btn_text }}}"
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'button_spacing_settings_section',
			[
				'label' => esc_html__( 'Button Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'button_space_between', [
			'label'      => esc_html__( 'Button Space Between', 'aapside-master' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				]
			],
			'selectors'  => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn + .boxed-btn" => "margin-left: {{SIZE}}{{UNIT}}"
			]
		] );
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'button_typography',
			'label' => esc_html__('Button Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn"
		]);
		$this->end_controls_section();

		$this->start_controls_section(
			'button_gd_one_section',
			[
				'label' => esc_html__( 'Gradient One Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'button_background' );

		$this->start_controls_tab( 'normal_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_one_background',
			'selector'    => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1",
			'description' => esc_html__( 'gradient one background', 'aapside-master' )
		] );
		$this->add_control( 'gd_one_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_one_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1 i" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1"
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_one_hover_background',
			'selector'    => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1:hover",
			'description' => esc_html__( 'gradient one background', 'aapside-master' )
		] );
		$this->add_control( 'gd_one_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1:hover" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'gd_one_hover_icon_color', [
			'label'       => esc_html__( 'Button Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button icon color', 'aapside-master' ),
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1:hover i" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_hover_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-1:hover"
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'button_gd_two_section',
			[
				'label' => esc_html__( 'Gradient Two Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'button_two_background' );

		$this->start_controls_tab( 'normal_two_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_two_background',
			'selector'    => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2",
			'description' => esc_html__( 'gradient two background', 'aapside-master' )
		] );
		$this->add_control( 'gd_two_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_two_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_two_hover_background',
			'selector'    => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2:hover",
			'description' => esc_html__( 'gradient two background', 'aapside-master' )
		] );
		$this->add_control( 'gd_two_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-2:hover" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'button_gd_three_section',
			[
				'label' => esc_html__( 'Gradient Three Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'button_three_background' );

		$this->start_controls_tab( 'normal_three_style', [
			'label' => esc_html__( 'Normal', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_three_background',
			'selector'    => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-3",
			'description' => esc_html__( 'gradient two background', 'aapside-master' )
		] );
		$this->add_control( 'gd_three_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-3" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_three_style', [
			'label' => esc_html__( 'Hover', 'aapside-master' )
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'        => 'gd_three_hover_background',
			'selector'    => "{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-3:hover",
			'description' => esc_html__( 'gradient three background', 'aapside-master' )
		] );
		$this->add_control( 'gd_three_hover_text_color', [
			'label'       => esc_html__( 'Button Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change button text color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .btn-wrapper .boxed-btn.gd-bg-3:hover" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

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
		$settings     = $this->get_settings_for_display();
		$button_items = $settings['button_items'];
		?>
        <div class="btn-wrapper appside-button-group">
			<?php foreach ( $button_items as $item ):

				$btn_icon = $item['icon'] ? '<i class="' . esc_attr( $item['icon'] ) . '"></i>' : '';
				$button_class = 'boxed-btn '.$item['theme'];
				//button attr
				$target = $item['btn_link']['is_external'] ? ' target="_blank"' : '';
				$nofollow = $item['btn_link']['nofollow'] ? ' rel="nofollow"' : '';

                if ('yes' == $item['btn_rounded']){
					$button_class .= ' btn-rounded';
                }
				if ('yes' == $item['btn_white_color']){
					$button_class .= ' blank';
				}
				?>

                <a class="<?php echo esc_attr($button_class);?>" href="<?php echo esc_url($item['btn_link']['url'])?>" <?php echo $target?> <?php echo $nofollow?>>
					<?php echo wp_kses_post( $btn_icon ); ?>
					<?php echo esc_html( $item['btn_text'] ); ?>
                </a>

			<?php endforeach; ?>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Button_Group_Two() );