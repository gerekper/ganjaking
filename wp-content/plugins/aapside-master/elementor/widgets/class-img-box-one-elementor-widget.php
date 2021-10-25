<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Image_Box_One_Widget extends Widget_Base {

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
		return 'appside-img-box-one-widget';
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
		return esc_html__( 'Img Box: 01', 'aapside-master' );
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
		$this->add_control( 'image', [
			'label'       => esc_html__( 'Image', 'aapside-master' ),
			'type'        => Controls_Manager::MEDIA,
			'description' => esc_html__( 'Select Image', 'aapside-master' )
		] );
		$this->add_control(
			'price_status',
			[
				'label'       => esc_html__( 'Price Show/Hide', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'show/hide price', 'aapside-master' ),
			]
		);
		$this->add_control(
			'price',
			[
				'label'       => esc_html__( 'Price', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter price.', 'aapside-master' ),
				'default'     => esc_html__( '$200', 'aapside-master' ),
				'condition'   => [ 'price_status' => 'yes' ]
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter title.', 'aapside-master' ),
				'default'     => esc_html__( 'Thailand', 'aapside-master' )
			]
		);
		$this->add_control(
			'img_box_url',
			[
				'label'       => esc_html__( 'URL', 'aapside-master' ),
				'type'        => Controls_Manager::URL,
				'description' => esc_html__( 'enter uel.', 'aapside-master' )
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
					'{{WRAPPER}} .image-box-style-01 img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'price_color', [
			'label'     => esc_html__( 'Price Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .image-box-style-01 .hover .price" => "color: {{VALUE}}"
			]
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'    => esc_html__( 'Price Background', 'aapside-master' ),
			'name'     => 'price-background',
			'selector' => "{{WRAPPER}} .image-box-style-01 .hover .price"
		] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .image-box-style-01 .hover .title" => "color: {{VALUE}}"
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
			'name'     => 'price_typography',
			'label'    => esc_html__( 'Price Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .image-box-style-01 .hover .price"
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .image-box-style-01 .hover .title"
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
		$this->add_render_attribute('link','class','');
		if (!empty($settings['img_box_url']['url'])){
		    $this->add_link_attributes('link',$settings['img_box_url']);
        }
		?>
        <div class="image-box-style-01">

			<div class="img-wrap">
                <a <?php echo $this->get_render_attribute_string('link');?>>
                <?php
                    $image_id = $settings['image']['id'];
                    $image_src = !empty($image_id) ? wp_get_attachment_image_src($image_id,'full')[0] : '';
                    $image_alt = !empty($image_id) ? get_post_meta($image_id,'_wp_attachment_image_alt',true) : '';
                    printf('<img src="%1$s" alt="%2$s"/>',esc_url($image_src),esc_attr($image_alt));
                ?>
                </a>
                <div class="hover">
                        <?php
                            if (!empty($settings['price_status'])){
                                printf('<span class="price">%1$s</span>', esc_html($settings['price']));
                            }
                        ?>
                    <a <?php echo $this->get_render_attribute_string('link');?>><h4 class="title"><?php echo esc_html($settings['title']);?></h4> </a>
                </div>
            </div>

        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Image_Box_One_Widget() );