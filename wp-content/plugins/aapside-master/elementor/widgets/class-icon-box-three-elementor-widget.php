<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Icon_Box_Three_Widget extends Widget_Base {

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
		return 'appside-icon-box-three-widget';
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
		return esc_html__( 'Icon Box: 03', 'aapside-master' );
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
		return 'eicon-alert';
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
		$this->add_control(
			'theme',
			[
				'label'       => esc_html__( 'Theme', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'options' => array(
                    'theme-01' => esc_html__('Theme 01','aapside-master'),
                    'theme-02' => esc_html__('Theme 02','aapside-master'),
                    'theme-03' => esc_html__('Theme 03','aapside-master'),
                ),
				'description' => esc_html__( 'select theme.', 'aapside-master' ),
				'default'     => 'theme-01'
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter  title.', 'aapside-master' ),
				'default'     => esc_html__('Full Mangement','aapside-master')
			]
		);
		$this->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icon', 'aapside-master' ),
				'type'        => Controls_Manager::ICON,
				'description' => esc_html__( 'select Icon.', 'aapside-master' ),
				'default'     => 'flaticon-layers-2'
			]
		);
		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter text.', 'aapside-master' ),
				'default'     => esc_html__('Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor','aapside-master')
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'settings_styling',
			[
				'label' => esc_html__( 'Section Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'featured_background',
			'selector' => "{{WRAPPER}} .single-connect-you-item",
			'description' => esc_html__('Icon Box background','aapside-master')
		]);
		$this->add_control( 'icon_box_icon_color', [
			'label'       => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change icon box icon color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .single-connect-you-item .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'icon_box_title_color', [
			'label'       => esc_html__( 'Title Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change icon box title color', 'aapside-master' ),
			'default'     => '#fff',
			'selectors'   => [
				"{{WRAPPER}} .single-connect-you-item .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'icon_box_text_color', [
			'label'       => esc_html__( 'Text Color', 'aapside-master' ),
			'type'        => Controls_Manager::COLOR,
			'description' => esc_html__( 'change icon box text color', 'aapside-master' ),
			'default'     => 'rgba(255, 255, 255, 0.9)',
			'selectors'   => [
				"{{WRAPPER}} .single-connect-you-item .content p" => "color: {{VALUE}}"
			]
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
        <div class="single-connect-you-item <?php echo esc_attr($settings['theme'])?>">
            <div class="icon">
                <i class="<?php echo esc_attr($settings['icon'])?>"></i>
            </div>
            <div class="content">
                <h4 class="title"><?php echo esc_html__($settings['title'])?></h4>
                <p><?php echo esc_html__($settings['description'])?></p>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Icon_Box_Three_Widget() );