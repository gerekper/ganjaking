<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Button_Group extends Widget_Base {

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
		return 'appside-button-group-one-widget';
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
		return esc_html__( 'Button Group: 01', 'aapside-master' );
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
				'label'     => __( 'Alignment', 'aapside-master' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'aapside-master' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'aapside-master' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'aapside-master' ),
						'icon'  => 'eicon-text-align-right',
					],
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
					'theme'    => '',
					'btn_text' => esc_html__( 'Learn More', 'aapside-master' ),
					'btn_link' => array(
						'url' => '#'
					)
				]
			],
			'fields'      => [
				[
					'name'        => 'image_btn',
					'label'       => esc_html__( 'Image Button', 'aapside-master' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => [
						'no'  => esc_html__( 'Text Btn', 'aapside-master' ),
						'yes' => esc_html__( 'Image Btn', 'aapside-master' ),
					],
					'default'     => 'no',
					'description' => esc_html__( 'enable image button instead of text', 'aapside-master' )
				],
				[
					'name'        => 'btn_image',
					'label'       => esc_html__( 'Button Image', 'aapside-master' ),
					'type'        => Controls_Manager::MEDIA,
					'description' => esc_html__( 'upload iamge.', 'aapside-master' ),
					'default'     => array(
						'url' => Utils::get_placeholder_image_src()
					),
					'condition'   => array(
						'image_btn' => 'yes'
					)
				],
				[
					'name'        => 'theme',
					'label'       => esc_html__( 'Theme', 'aapside-master' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'select theme.', 'aapside-master' ),
					'options'     => array(
						''      => esc_html__( 'Default' ,'aapside-master'),
						'blank' => esc_html__( 'Blank' ,'aapside-master'),
					),
					'default'     => '',
					'condition'   => array(
						'image_btn' => 'no'
					)
				],
				[
					'name'        => 'primary_border_color',
					'label'       => esc_html__( 'Primary Border Color', 'aapside-master' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'enable primary border color.', 'aapside-master' ),
					'default'     => 'no',
					'condition'   => array(
						'theme'     => 'blank',
						'image_btn' => 'no'
					)
				],
				[
					'name'        => 'icon',
					'label'       => esc_html__( 'Button Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'enter button icon.', 'aapside-master' ),
					'default'     => 'flaticon-apple-1',
					'condition'   => array(
						'show_icon' => 'yes',
						'image_btn' => 'no'
					)
				],
				[
					'name'        => 'btn_text',
					'label'       => esc_html__( 'Button Text', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter button text.', 'aapside-master' ),
					'default'     => esc_html__( 'Learn More', 'aapside-master' ),
					'condition'   => array(
						'image_btn' => 'no'
					)
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
					'name'        => 'reverse_color',
					'label'       => esc_html__( 'Button Reverse Color', 'aapside-master' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'enable button reverse color.', 'aapside-master' ),
					'default'     => '',
					'condition'   => array(
						'image_btn' => 'no'
					)
				],
				[
					'name'        => 'show_icon',
					'label'       => esc_html__( 'Show Button Icon', 'aapside-master' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'show button icon.', 'aapside-master' ),
					'default'     => 'yes',
					'condition'   => array(
						'image_btn' => 'no'
					)
				]

			],
			'title_field' => "{{btn_text}}"
		] );
		$this->end_controls_section();

		/* typography settings start*/
		$this->start_controls_section(
			'button_typography_section',
			[
				'label' => esc_html__( 'Button Typography', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => esc_html__( 'Typography', 'aapside-master' ),
			'name'     => 'button_typography',
			'selector' => "{{WRAPPER}} .boxed-btn-02"
		] );
		$this->end_controls_section();
		/* typography settings end*/
		/*  button styling tabs start */
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
				'%' => [
					'min'  => 0,
					'max'  => 100,
				]
			],
			'selectors' => [
				"{{WRAPPER}} .appside-button-group .boxed-btn-02 + .boxed-btn-02" => "margin-left: {{SIZE}}{{UNIT}}"
			]
		] );
		$this->end_controls_section();
		/*  button styling tabs start */
		$this->start_controls_section(
			'button_settings_section',
			[
				'label' => esc_html__( 'Button Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'button_tabs'
		);

		$this->start_controls_tab(
			'button_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);

		$this->add_control( 'button_color', [
			'label'     => esc_html__( 'Text Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .boxed-btn-02' => "color:{{VALUE}}"
			]
		] );

		$this->add_control( 'button_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .boxed-btn-02 i' => "color:{{VALUE}}"
			]
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .boxed-btn-02"
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_background',
			'label'    => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .boxed-btn-02"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);

		$this->add_control( 'button_hover_color', [
			'label'     => esc_html__( 'Text Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .boxed-btn-02:hover' => "color:{{VALUE}}"
			]
		] );

		$this->add_control( 'button_hover_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .boxed-btn-02:hover i' => "color:{{VALUE}}"
			]
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'button_hover_border',
			'label'    => esc_html__( 'Border', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .boxed-btn-02:hover"
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_hover_background',
			'label'    => esc_html__( 'Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .boxed-btn-02:hover"
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		/*  button styling tabs end */
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

				$button_class = '';
                //button attributes
                if ('yes' == $item['image_btn']){
                    $button_class = 'boxed_img_btn'; 
                }else{
					$button_class = 'boxed-btn-02 '.$item['theme'];
                }

                if ('yes' == $item['reverse_color']){
                    $button_class .= ' reverse-color';
                }
				if ('yes' == $item['primary_border_color']){
					$button_class .= ' primary-border-color'; 
				}
				$target = $item['btn_link']['is_external'] ? ' target="_blank"' : '';
				$nofollow = $item['btn_link']['nofollow'] ? ' rel="nofollow"' : '';


				$btn_icon = 'yes' == $item['show_icon'] && $item['icon'] ? '<i class="' . esc_attr( $item['icon'] ) . '"></i>' : '';

				//btn img one
				$btn_img_id  = $item['btn_image']['id'];
				$btn_img     = wp_get_attachment_image_src( $btn_img_id, 'full' );
				$btn_img_alt = get_post_meta( $btn_img_id, '_wp_get_attachment_image_alt', true );

				?>

				<?php if ( 'yes' == $item['image_btn'] ): ?>
                <a class="<?php echo esc_attr($button_class);?>" href="<?php echo esc_url($item['btn_link']['url'])?>" <?php echo $target?> <?php echo $nofollow?> >
                    <img src="<?php echo esc_url( $btn_img[0] ) ?>" alt="<?php echo esc_attr( $btn_img_alt ) ?>">
                </a>
			<?php else: ?>
                <a class="<?php echo esc_attr($button_class);?>" href="<?php echo esc_url($item['btn_link']['url'])?>" <?php echo $target?> <?php echo $nofollow?>>
					<?php echo wp_kses_post( $btn_icon ); ?>
					<?php echo esc_html( $item['btn_text'] ); ?>
                </a>
			<?php endif; ?>

			<?php endforeach; ?>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Button_Group() );