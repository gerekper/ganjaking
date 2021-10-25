<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Quote_Box_One_Widget extends Widget_Base {

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
		return 'appside-quote-box-one-widget';
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
		return esc_html__( 'Quote Box: 01', 'aapside-master' );
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
		return 'eicon-blockquote';
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
		$this->add_control( 'quote', [
			'label'       => esc_html__( 'Quote', 'aapside-master' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default' => esc_html__('"oxo powers the FoundMyFitness members-only podcast. It made it simple to share private podcasts with our premium subscribers."','aapside-master'),
			'description' => esc_html__( 'enter Quote', 'aapside-master' )
		] );
		$this->add_control( 'image', [
			'label'       => esc_html__( 'Image', 'aapside-master' ),
			'type'        => Controls_Manager::MEDIA,
			'description' => esc_html__( 'select image', 'aapside-master' ),

		] );
		$this->add_control(
			'name',
			[
				'label'       => esc_html__( 'Name', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter name.', 'aapside-master' ),
				'default'     => esc_html__( 'Andrew Marker', 'aapside-master' )
			]
		);
		$this->add_control(
			'designation',
			[
				'label'       => esc_html__( 'Designation', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter designation.', 'aapside-master' ),
				'default'     => esc_html__( 'CEO, OXO Startup', 'aapside-master' )
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
		$this->add_control(
			'img_box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'aapside-master' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .quote-box-style-01 .author-meta .thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_right_gap',
			[
				'label' => esc_html__( 'Image Right Gap', 'aapside-master' ),
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
					'{{WRAPPER}} .quote-box-style-01 .author-meta .thumb' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'description_bottom_gap',
			[
				'label' => esc_html__( 'Quote Bottom Gap', 'aapside-master' ),
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
					'{{WRAPPER}} .quote-box-style-01 .author-meta' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Quote Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .quote-box-style-01 .description p" => "color: {{VALUE}}"
			]
		] );

		$this->add_control( 'name_color', [
			'label'     => esc_html__( 'Name Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .quote-box-style-01 .author-meta .content .name" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'designation_color', [
			'label'     => esc_html__( 'Designation Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .quote-box-style-01 .author-meta .content .designation" => "color: {{VALUE}}"
			]
		] );

		$this->end_controls_section();
		$this->start_controls_section(
			'styling_typogrpahy_section',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'quote_typography',
			'label'    => esc_html__( 'Quote Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .quote-box-style-01 .description p"
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .quote-box-style-01 .author-meta .content .name"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'designation_typography',
			'label'    => esc_html__( 'Designation Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .quote-box-style-01 .author-meta .content .designation"
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
		?>
        <div class="quote-box-style-01">
            <div class="description">
                <p><?php echo esc_html($settings['quote']);?></p>
            </div>
            <div class="author-meta">
                <div class="thumb">
                    <?php
                        $image_id = $settings['image']['id'];
                        $image_url = !empty($image_id) ? wp_get_attachment_image_src($image_id,'thumbnail')[0] : '';
                        $image_alt = !empty($image_id) ? get_post_meta($image_id,'_wp_attachment_image_alt',true) : '';
                        if (!empty($image_id)){
                            printf('<img src="%1$s" alt="%2$s">',esc_url($image_url),esc_attr($image_alt));
                        }
                    ?>
                </div>
                <div class="content">
                    <h4 class="name"><?php echo esc_html($settings['name']);?></h4>
                    <span class="designation"><?php echo esc_html($settings['designation']);?></span>
                </div>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Quote_Box_One_Widget() );