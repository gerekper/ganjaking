<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Icon_List_Two_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Elementor widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'appside-icon-list-Two-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Elementor widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Icon List: 02', 'aapside-master' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Elementor widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-editor-list-ul';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Elementor widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
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

		$repeater = new Repeater();
		if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$repeater->add_control(
				'icon',
				[
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICONS,
					'description' => esc_html__( 'select Icon.', 'aapside-master' ),
				]
			);
		} else {
			$repeater->add_control(
				'icon',
				[
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select Icon.', 'aapside-master' ),
				]
			);
		}
		$repeater->add_control('title',[
		   'label' => esc_html__('Title','aapside-master'),
           'type' => Controls_Manager::TEXT,
            'description' => esc_html__('enter title','aapside-master')
        ]);
		$repeater->add_control('description',[
			'label' => esc_html__('Description','aapside-master'),
			'type' => Controls_Manager::TEXTAREA,
			'description' => esc_html__('enter description','aapside-master')
		]);
		$this->add_control(
			'icon_list_items',
			[
				'label' => __( 'Icon List Items', 'aapside-master' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		$this->add_control('spacing_divider',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_control(
			'gap_between_items',
			[
				'label' => esc_html__( 'Gap Between Items', 'aapside-master' ),
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
					'{{WRAPPER}} .icon-list-style-two li+li' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_gap',
			[
				'label' => esc_html__( 'Icon Right Gap', 'aapside-master' ),
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
					'{{WRAPPER}} .icon-list-style-two li .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'title_bottom_gap',
			[
				'label' => esc_html__( 'Title Bottom Gap', 'aapside-master' ),
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
					'{{WRAPPER}} .icon-list-style-two li .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'styling_section',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control('icon_color',[
		   'label' => esc_html__('Icon Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .icon-list-style-two li .icon" => "color: {{VALUE}}"
			]
        ]);
		$this->add_control('title_color',[
			'label' => esc_html__('Title Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .icon-list-style-two li .title" => "color: {{VALUE}}"
			]
		]);
		$this->add_control('description_color',[
		   'label' => esc_html__('Description Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .icon-list-style-two li .content p" => "color: {{VALUE}}"
			]
        ]);
		$this->add_control('divider',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .icon-list-style-two li .title"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'description_typography',
			'label' => esc_html__('Description Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .icon-list-style-two li .content p"
		]);
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
		?>
        <ul class="icon-list-style-two">
            <?php
                $all_list_items = $settings['icon_list_items'];

                foreach ($all_list_items as $item):

                    print '<li><div class="icon">';
	                if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
		               ! empty( $item['icon']['value'] ) ? Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ) : '';
	                } else {
		              ! empty( $item['icon'] ) ? sprintf( '<i class="%1$s"></i>', $item['icon'] ) : '';
	                }
	                print '</div><div class="content">';
	                printf('<h4 class="title">%1$s</h4>',esc_html($item['title']));
	                printf('<p>%1$s</p>',esc_html($item['description']));

	                print '</div></li>';

                endforeach;
            ?>
        </ul>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Icon_List_Two_Widget() );