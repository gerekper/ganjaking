<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Team_Member_One_Widget extends Widget_Base {

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
		return 'appside-team-member-one-widget';
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
		return esc_html__( 'Team Member One', 'aapside-master' );
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
		return 'eicon-person';
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
		$this->add_control( 'theme', [

			'label'       => esc_html__( 'Theme', 'aapside-master' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				''      => esc_html__( 'Black', 'aapside-master' ),
				'white' => esc_html__( 'White', 'aapside-master' )
			),
			'default'     => '',
			'description' => esc_html__( 'set theme', 'aapside-master' )
		] );
		$this->add_control( 'team_member_items', [
			'label'       => esc_html__( 'Team Member Item', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'name'        => esc_html__( 'Maria Hexa', 'aapside-master' ),
					'image'       => array(
						'url' => Utils::get_placeholder_image_src()
					),
					'designation' => esc_html__( 'Creative Designer', 'aapside-master' )
				],
				[
					'name'        => esc_html__( 'Maria Hexa', 'aapside-master' ),
					'image'       => array(
						'url' => Utils::get_placeholder_image_src()
					),
					'designation' => esc_html__( 'Creative Designer', 'aapside-master' )
				],
			],
			'fields'      => [
				[
					'name'        => 'image',
					'label'       => esc_html__( 'Image', 'aapside-master' ),
					'type'        => Controls_Manager::MEDIA,
					'description' => esc_html__( 'enter title.', 'aapside-master' ),
					'default'     => array(
						'url' => Utils::get_placeholder_image_src()
					)
				],
				[
					'name'        => 'name',
					'label'       => esc_html__( 'Name', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter name', 'aapside-master' ),
					'default'     => esc_html__( 'Lara Croft', 'aapside-master' )
				],
				[
					'name'        => 'designation',
					'label'       => esc_html__( 'Designation', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter designation', 'aapside-master' ),
					'default'     => esc_html__( 'CEO, Appside', 'aapside-master' )
				],
				[
					'name'        => 'icon_1',
					'label'       => esc_html__( 'Icon one', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select icon', 'aapside-master' ),
					'default'     => 'fa fa-facebook'
				],
				[
					'name'        => 'icon_1_url',
					'label'       => esc_html__( 'Icon 1 Url', 'aapside-master' ),
					'type'        => Controls_Manager::URL,
					'description' => esc_html__( 'enter url', 'aapside-master' ),
					'default'     => array(
						'url' => '#'
					)
				],
				[
					'name'        => 'icon_2',
					'label'       => esc_html__( 'Icon Two', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select icon', 'aapside-master' ),
					'default'     => 'fa fa-twitter'
				],
				[
					'name'        => 'icon_2_url',
					'label'       => esc_html__( 'Icon 2 Url', 'aapside-master' ),
					'type'        => Controls_Manager::URL,
					'description' => esc_html__( 'enter url', 'aapside-master' ),
					'default'     => array(
						'url' => '#'
					)
				],
				[
					'name'        => 'icon_3',
					'label'       => esc_html__( 'Icon Three', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select icon', 'aapside-master' ),
					'default'     => 'fa fa-instagram'
				],
				[
					'name'        => 'icon_3_url',
					'label'       => esc_html__( 'Icon 3 Url', 'aapside-master' ),
					'type'        => Controls_Manager::URL,
					'description' => esc_html__( 'enter url', 'aapside-master' ),
					'default'     => array(
						'url' => '#'
					)
				],

			],
			'title_field' => '{{name}}'
		] );
		$this->end_controls_section();

		$this->start_controls_section(
			'slider_settings_section',
			[
				'label' => esc_html__( 'Slider Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Items', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'you can set how many item show in slider', 'aapside-master' ),
				'default'     => '4'
			]
		);
		$this->add_control(
			'margin',
			[
				'label'       => esc_html__( 'Margin', 'aapside-master' ),
				'description' => esc_html__( 'you can set margin for slider', 'aapside-master' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					]
				],
				'default'     => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units'  => [ 'px' ]
			]
		);
		$this->add_control(
			'loop',
			[
				'label'       => esc_html__( 'Loop', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'you can set yes/no to enable/disable', 'aapside-master' ),
				'default'     => 'yes'
			]
		);
		$this->add_control(
			'autoplay',
			[
				'label'       => esc_html__( 'Autoplay', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'you can set yes/no to enable/disable', 'aapside-master' ),
				'default'     => 'yes'
			]
		);
		$this->add_control(
			'autoplaytimeout',
			[
				'label'      => esc_html__( 'Autoplay Timeout', 'aapside-master' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 2,
					]
				],
				'default'    => [
					'unit' => 'px',
					'size' => 5000,
				],
				'size_units' => [ 'px' ],
				'condition'  => array(
					'autoplay' => 'yes'
				)
			]

		);
		$this->end_controls_section();
		$this->start_controls_section(
			'styling_settings_section',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-team-member .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'designation_color', [
			'label'     => esc_html__( 'Designation Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-team-member .content .post" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'    => esc_html__( 'Overlay Background', 'aapside-master' ),
			'name'     => 'overlay_background',
			'selector' => "{{WRAPPER}} .single-team-member .thumb .hover"
		] );
		$this->add_control( 'thumb_border_color', [
			'label'     => esc_html__( 'Border Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-team-member .thumb" => "border-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'thumb_icon_color', [
			'label'     => esc_html__( 'Social Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-team-member .thumb .hover .social-icon li a" => "color: {{VALUE}}"
			]
		] );
		$this->end_controls_section();
		$this->start_controls_section(
			'typography_settings_section',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-team-member .content .title"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'designation_typography',
			'label' => esc_html__('Designation Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-team-member .content .post"
		]);
		$this->end_controls_section();

	}

	/**
	 * Render Elementor widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings              = $this->get_settings_for_display();
		$all_team_member_items = $settings['team_member_items'];
		$rand_numb             = rand( 333, 999999999 );

		//slider settings
		$loop            = $settings['loop'] ? 'true' : 'false';
		$items           = $settings['items'] ? $settings['items'] : 4;
		$autoplay        = $settings['autoplay'] ? 'true' : 'false';
		$autoplaytimeout = $settings['autoplaytimeout']['size'];
		?>
        <div class="team-member-carousel-wrapper appside-rtl-slider">
            <div class="team-carousel owl-carousel"
                 id="team-one-carousel-<?php echo esc_attr( $rand_numb ); ?>"
                 data-loop="<?php echo esc_attr( $loop ); ?>"
                 data-margin="<?php echo esc_attr( $settings['margin']['size'] ); ?>"
                 data-items="<?php echo esc_attr( $items ); ?>"
                 data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
                 data-autoplaytimeout="<?php echo esc_attr( $autoplaytimeout ); ?>"
            >
				<?php
				foreach ( $all_team_member_items as $item ):
					$image_id = $item['image']['id'];
					$image_url = wp_get_attachment_image_src( $image_id, 'full', false );
					$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					?>

                    <div class="single-team-member  <?php echo esc_attr( $settings['theme'] ); ?>">
                        <div class="thumb">
                            <img src="<?php echo esc_url( $image_url[0] ); ?>"
                                 alt="<?php echo esc_attr( $image_alt ); ?>">
                            <div class="hover">
                                <ul class="social-icon">
									<?php
									if ( ! empty( $item['icon_1'] ) && ! empty( $item['icon_1_url'] ) ) {
										printf( ' <li><a href="%2$s"><i class="%1$s"></i></a></li>', esc_attr( $item['icon_1'] ), esc_url( $item['icon_1_url']['url'] ) );
									}
									if ( ! empty( $item['icon_2'] ) && ! empty( $item['icon_2_url'] ) ) {
										printf( ' <li><a href="%2$s"><i class="%1$s"></i></a></li>', esc_attr( $item['icon_2'] ), esc_url( $item['icon_2_url']['url'] ) );
									}
									if ( ! empty( $item['icon_3'] ) && ! empty( $item['icon_3_url'] ) ) {
										printf( ' <li><a href="%2$s"><i class="%1$s"></i></a></li>', esc_attr( $item['icon_3'] ), esc_url( $item['icon_3_url']['url'] ) );
									}
									?>
                                </ul>
                            </div>
                        </div>
                        <div class="content">
                            <h4 class="title"><?php echo esc_html( $item['name'] ); ?></h4>
                            <span class="post"><?php echo esc_html( $item['designation'] ); ?></span>
                        </div>
                    </div>

				<?php endforeach; ?>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Team_Member_One_Widget() );