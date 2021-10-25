<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Screenshort_Two_Widget extends Widget_Base {

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
		return 'appside-screenshort-two-widget';
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
		return esc_html__( 'Screenshort Two', 'aapside-master' );
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
		return 'eicon-slider-push';
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
		$this->add_control( 'screenshort_items', [
			'label'       => esc_html__( 'Screenshort Item', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'image'       =>  array(
						'url' => Utils::get_placeholder_image_src()
					)
				],
                [
					'image'       =>  array(
						'url' => Utils::get_placeholder_image_src()
					)
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
				]
			],
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
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					]
				],
				'default'    => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units' => [ 'px' ],
				'condition'  => array(
					'autoplay' => 'yes'
				)
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
			'mobile_mockup_settings_section',
			[
				'label' => esc_html__( 'Mobile Mockup Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control('mobile_mockup',[
		   'label' => esc_html__('Mobile Mockup','aapside-master'),
            'type' => Controls_Manager::MEDIA,
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
		$settings = $this->get_settings_for_display();
        $all_screenshort_item = $settings['screenshort_items'];
		$rand_numb         = rand( 333, 999999999 );

		//slider settings
		$loop            = $settings['loop'] ? 'true' : 'false';
		$items           = $settings['items'] ? $settings['items'] : 4;
		$autoplay        = $settings['autoplay'] ? 'true' : 'false';
		$autoplaytimeout = $settings['autoplaytimeout']['size'];

		//mobile mockup
		$mockup_image_id = $settings['mobile_mockup']['id'];
		$mockup_image_url = wp_get_attachment_image_src( $mockup_image_id, 'full', false );
		$mockup_image_alt = get_post_meta( $mockup_image_id, '_wp_attachment_image_alt', true );
        $default_mockup = "data:image/png;base64,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";
		$mockup_image_url_render = !empty($mockup_image_id) ? esc_url($mockup_image_url[0]) :  $default_mockup;
		$mockup_image_alt_render = !empty($mockup_image_id) ? $mockup_image_alt : '';
        ?>
        <div class="screeshort-carousel-wrap-02 appside-rtl-slider">
            <div class="mobile-cover">
                <img src="<?php echo $mockup_image_url_render;?>" alt="<?php echo esc_attr($mockup_image_alt_render);?>">
            </div>
            <div class="screenshort-carousel-02 owl-carousel"
                 id="screenshort-two-carousel-<?php echo esc_attr( $rand_numb ); ?>"
                 data-loop="<?php echo esc_attr( $loop ); ?>"
                 data-margin="<?php echo esc_attr( $settings['margin']['size'] ); ?>"
                 data-items="<?php echo esc_attr( $items ); ?>"
                 data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
                 data-autoplaytimeout="<?php echo esc_attr( $autoplaytimeout ); ?>"
            >
                <?php
                foreach ( $all_screenshort_item as $item ):
                    $image_id = $item['image']['id'];
                    $image_url = wp_get_attachment_image_src( $image_id, 'full', false );
                    $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
                ?>
                <div class="single-screenshort-item">
                    <img src="<?php echo esc_url($image_url[0]);?>" alt="<?php echo esc_attr($image_alt);?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Screenshort_Two_Widget() );