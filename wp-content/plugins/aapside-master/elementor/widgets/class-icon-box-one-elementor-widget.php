<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Icon_Box_One_Widget extends Widget_Base {

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
		return 'appside-icon-box-one-widget';
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
		return esc_html__( 'Icon Box: 01', 'aapside-master' );
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
		$this->add_control(
			'theme',
			[
				'label'       => esc_html__( 'Theme', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'theme-01' => esc_html__( 'Theme 01', 'aapside-master' ),
					'theme-02' => esc_html__( 'Theme 02', 'aapside-master' ),
					'theme-03' => esc_html__( 'Theme 03', 'aapside-master' ),
					'theme-04' => esc_html__( 'Theme 04', 'aapside-master' ),
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
				'default'     => esc_html__( 'Clean Code', 'aapside-master' )
			]
		);
		$this->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icon', 'aapside-master' ),
				'type'        => Controls_Manager::ICON,
				'description' => esc_html__( 'select Icon.', 'aapside-master' ),
				'default'     => 'fa fa-github'
			]
		);
		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'aapside-master' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'enter text.', 'aapside-master' ),
				'default'     => esc_html__( 'Messenger communication consectetur adipiscing elit, sed do eiusmod tempor labore.', 'aapside-master' )
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'styling_section', [
			'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_control( 'icon_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .single-icon-box-03 .icon" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'icon_border_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Icon Border Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .single-icon-box-03 .icon" => "border-color: {{VALUE}}"
			]
		] );
		$this->add_control( 'title_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .single-icon-box-03 .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'description_color', [
			'type'      => Controls_Manager::COLOR,
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'selectors' => [
				"{{WRAPPER}} .single-icon-box-03 .content p" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'  => 'title_typography',
			'label' => esc_html__( 'Title Typography', 'aapside-master' ),
            'selector' => "{{WRAPPER}} .single-icon-box-03 .content .title"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'  => 'description_typography',
			'label' => esc_html__( 'Description Typography', 'aapside-master' ),
            'selector' => "{{WRAPPER}} .single-icon-box-03 .content p"
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
        <div class="single-icon-box-03 <?php echo esc_attr( $settings['theme'] ) ?>">
            <div class="icon">
                <i class="<?php echo esc_attr( $settings['icon'] ) ?>"></i>
            </div>
            <div class="content">
                <h4 class="title"><?php echo esc_html__( $settings['title'] ) ?></h4>
                <p><?php echo esc_html__( $settings['description'] ) ?></p>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Icon_Box_One_Widget() );