<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Brand_Carousel_One_Widget extends Widget_Base {

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
		return 'appside-brand-carousel-one-widget';
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
		return esc_html__( 'Brand Carousel One', 'aapside-master' );
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
				],[
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
				'type'        => Controls_Manager::TEXT,
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
		?>
        <div class="brand-carousel-wrapper appside-rtl-slider">
            <div class="brands-carousel owl-carousel"
                 id="brand-one-carousel-<?php echo esc_attr( $rand_numb ); ?>"
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
                <div class="single-brand-item">
                    <img src="<?php echo esc_url($image_url[0]);?>" alt="<?php echo esc_attr($image_alt);?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Brand_Carousel_One_Widget() );
