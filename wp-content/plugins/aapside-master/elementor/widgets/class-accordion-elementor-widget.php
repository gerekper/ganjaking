<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Accordion_One extends Widget_Base {

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
		return 'appside-accordion-one-widget';
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
		return esc_html__( 'Accordion: 01', 'aapside-master' );
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
		return 'eicon-accordion';
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
		$this->add_control( 'disable_open_first_item', [
			'type'  => Controls_Manager::SWITCHER,
			'label' => esc_html__( 'Disable Open First Item', 'aapside-master' ),
		] );
		$this->add_control( 'accordion_items', [
			'label'   => esc_html__( 'Accordion Item', 'aapside-master' ),
			'type'    => Controls_Manager::REPEATER,
			'default' => [
				[
					'title'       => esc_html__( 'How Appside help you?', 'aapside-master' ),
					'description' => esc_html__( 'Duis aute irure dolor reprehenderit in voluptate velit essle cillum dolore eu fugiat nulla pariatur. Excepteur sint ocaec at cupdatat proident suntin culpa qui officia deserunt mol anim id esa laborum perspiciat.', 'aapside-master' ),
				]
			],
			'fields'  => [
				[
					'name'        => 'title',
					'label'       => esc_html__( 'Title', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'Enter title', 'aapside-master' )
				],
				[
					'name'        => 'description',
					'label'       => esc_html__( 'Description', 'aapside-master' ),
					'type'        => Controls_Manager::TEXTAREA,
					'description' => esc_html__( 'Enter description', 'aapside-master' )
				]
			],
		] );
		$this->end_controls_section();
		$this->start_controls_section( 'styling_section', [
			'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .accordion-wrapper .card .card-header a"
		] );
        $this->add_group_control(
         Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => __( 'Box Shadow', 'aapside-master' ),
                'selector' => '{{WRAPPER}} .accordion-wrapper .card .card-header a',
            ]
        );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'label'    => esc_html__( 'Description Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .accordion-wrapper .card .card-body"
		] );
        $this->add_control( 'bg_color', [
            'label' => esc_html__( 'Background Color', 'aapside-master' ),
            'type' => Controls_Manager::COLOR,
            'description' => esc_html__('select color','aapside-master'),
            'selectors' => [
                "{{WRAPPER}} .accordion-wrapper .card .card-header a" => "background-color: {{VALUE}}"
            ]
        ] );
		$this->add_control( 'title_color', [
			'label' => esc_html__( 'Title Color', 'aapside-master' ),
            'type' => Controls_Manager::COLOR,
            'description' => esc_html__('select color','aapside-master'),
            'selectors' => [
                    "{{WRAPPER}} .accordion-wrapper .card .card-header a" => "color: {{VALUE}}"
            ]
		] );
		$this->add_control( 'description_color', [
			'label' => esc_html__( 'Description Color', 'aapside-master' ),
            'type' => Controls_Manager::COLOR,
            'description' => esc_html__('select color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .accordion-wrapper .card .card-body" => "color: {{VALUE}}"
			]
		] );
		$this->add_control(
			'faqs_margin',
			[
				'label' => esc_html__( 'Padding Top', 'plugin-domain' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-wrapper .card .card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$settings        = $this->get_settings_for_display();
		$accordion_items = $settings['accordion_items'];
		$random_number   = rand( 999, 999999 );
		?>
        <div class="accordion-wrapper">
            <div id="accordion-<?php echo esc_attr( $random_number ); ?>">
				<?php
				$a                 = 0;
				foreach ( $accordion_items as $item ):
					$collapse_class = ( 0 == $a && $settings['disable_open_first_item'] == 'yes') ? '' : 'collapsed';
					$show_class    = ( 0 == $a && $settings['disable_open_first_item'] == 'yes') ? 'show' : '';
					$aria_expanded = ( 0 == $a && $settings['disable_open_first_item'] == 'yes') ? 'true' : 'false';
					$a ++;
					$random__item_number = rand( 999, 999999 );
					?>
                    <div class="card">
                        <div class="card-header" id="headingOne_<?php echo esc_attr( $random__item_number ); ?>">
                            <h5 class="mb-0">
                                <a class="<?php echo esc_attr( $collapse_class ); ?>" data-toggle="collapse"
                                   role="button"
                                   data-target="#collapseOne_<?php echo esc_attr( $random__item_number ); ?>"
                                   aria-expanded="<?php echo esc_attr( $aria_expanded ); ?>"
                                   aria-controls="collapseOne_<?php echo esc_attr( $random__item_number ); ?>">
									<?php echo esc_html( $item['title'] ); ?>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseOne_<?php echo esc_attr( $random__item_number ); ?>"
                             class="collapse <?php echo esc_attr( $show_class ); ?>"
                             data-parent="#accordion-<?php echo esc_attr( $random_number ); ?>">
                            <div class="card-body">
								<?php echo esc_html( $item['description'] ); ?>
                            </div>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Accordion_One() );