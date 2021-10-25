<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Img_With_Video_Two_Widget extends Widget_Base {

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
		return 'appside-image-with-video-two-widget';
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
		return esc_html__( 'Img With Video Two', 'aapside-master' );
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
		return 'eicon-play';
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
			'label'   => esc_html__( 'Background Image', 'aapside-master' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => array(
				'url' => Utils::get_placeholder_image_src()
			)
		] );
		$this->add_control( 'url', [
			'label'   => esc_html__( 'URL', 'aapside-master' ),
			'type'    => Controls_Manager::URL,
			'default' => array(
				'url' => 'https://www.youtube.com/watch?v=w4Wlri_PQf4'
			)
		] );
		$this->add_control( 'title', [
			'label'   => esc_html__( 'Title', 'aapside-master' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( ' Intro & Demo Video', 'aapside-master' )
		] );

		$this->end_controls_section();
		$this->start_controls_section( 'styling_section', [
			'label' => esc_html__( 'Button Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_background',
			'label'    => esc_html__( 'Button Background', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .play-video-btn"
		] );
		$this->add_control( 'icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .img-full-width-video .hover .play-video-btn i" => "color:{{VALUE}}"
			]
		] );
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .img-full-width-video .hover .play-video-btn" => "color:{{VALUE}}"
			]
		] );
		$this->add_control( 'divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'name'     => 'title_typography',
			'selector' => "{{WRAPPER}} .img-full-width-video .hover .play-video-btn"
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
		$settings  = $this->get_settings_for_display();
		$image_id  = $settings['image']['id'];
		$video_url = $settings['url']['url'];
		$image_src = wp_get_attachment_image_src( $image_id, 'full' )[0];
		$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		?>
        <div class="full-width-video-area">
            <div class="img-full-width-video">
                <img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
                <div class="hover">
                    <a href="<?php echo esc_url( $video_url ); ?>" class="play-video-btn mfp-iframe"><i
                                class="fa fa-play"></i> <?php echo esc_html( $settings['title'] ) ?></a>
                </div>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Img_With_Video_Two_Widget() );