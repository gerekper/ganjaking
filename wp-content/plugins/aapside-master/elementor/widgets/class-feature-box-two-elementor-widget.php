<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Featrue_Box_Two_Widget extends Widget_Base {

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
		return 'appside-feature-box-two-widget';
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
		return esc_html__( 'Feature Box Two', 'aapside-master' );
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
		return 'eicon-icon-box';
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
		$this->add_control( 'color_theme', [

			'label'       => esc_html__( 'Color Theme', 'aapside-master' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				''      => esc_html__( 'Black', 'aapside-master' ),
				'white' => esc_html__( 'White', 'aapside-master' )
			),
			'default'     => '',
			'description' => esc_html__( 'set theme', 'aapside-master' )
		] );
		$this->add_control( 'theme', [
			'label'       => esc_html__( 'Icon Theme', 'aapside-master' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'gdbg-1' => esc_html__( 'Theme 01', 'aapside-master' ),
				'gdbg-2' => esc_html__( 'Theme 02', 'aapside-master' ),
				'gdbg-3' => esc_html__( 'Theme 03', 'aapside-master' ),
				'gdbg-4' => esc_html__( 'Theme 04', 'aapside-master' ),
			),
			'default'     => 'gdbg-1',
			'description' => esc_html__( 'select icon theme', 'aapside-master' )
		] );
		$this->add_control( 'icon', [
			'label'       => esc_html__( 'Icon', 'aapside-master' ),
			'type'        => Controls_Manager::ICON,
			'default'     => 'flaticon-settings-1',
			'description' => esc_html__( 'select icon', 'aapside-master' )
		] );
		$this->add_control( 'title', [
			'label'       => esc_html__( 'Title', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Easy Customize', 'aapside-master' ),
			'description' => esc_html__( 'enter title', 'aapside-master' )
		] );
		$this->add_control( 'description', [
			'label'       => esc_html__( 'Description', 'aapside-master' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => esc_html__( 'Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore', 'aapside-master' ),
			'description' => esc_html__( 'enter description', 'aapside-master' )
		] );

		$this->end_controls_section();

		$this->start_controls_section(
			'styling_section',
			[
				'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'       => esc_html__( 'Background', 'aapside-master' ),
			'name'        => 'why_us_background',
			'selector'    => "{{WRAPPER}} .single-why-us-item",
			'description' => esc_html__( 'background', 'aapside-master' )
		] );
		$this->add_control( 'divider', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'       => esc_html__( 'Icon Background', 'aapside-master' ),
			'name'        => 'feature_box_icon_hover_background',
			'selector'    => "{{WRAPPER}} .single-why-us-item .icon",
			'description' => esc_html__( 'icon background', 'aapside-master' )
		] );
		$this->add_control( 'icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-why-us-item .icon" => "color: {{VALUE}}"
			],
			'description' => esc_html__( 'icon color', 'aapside-master' )
		] );
		$this->add_control( 'divider_01', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-why-us-item .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-why-us-item .content p" => "color: {{VALUE}}"
			]
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __( 'Hover', 'plugin-name' ),
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'       => esc_html__( 'Background', 'aapside-master' ),
			'name'        => 'why_us_hover_background',
			'selector'    => "{{WRAPPER}} .single-why-us-item:hover",
			'description' => esc_html__( 'background', 'aapside-master' )
		] );
		$this->add_control( 'divider_02', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'label'       => esc_html__( 'Icon Background', 'aapside-master' ),
			'name'        => 'gd_two_hover_hover_background',
			'selector'    => "{{WRAPPER}} .single-why-us-item:hover .icon",
			'description' => esc_html__( 'icon background', 'aapside-master' )
		] );
		$this->add_control( 'divider_03', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_control( 'title_hover_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-why-us-item:hover .content .title" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'description_hover_color', [
			'label'     => esc_html__( 'Description Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-why-us-item:hover .content p" => "color: {{VALUE}}"
			]
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_control( 'divider_04', [
			'type' => Controls_Manager::DIVIDER
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-why-us-item .content .title"
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'label'    => esc_html__( 'Description Typography', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .single-why-us-item .content p"
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
        <div class="single-why-us-item <?php echo esc_attr( $settings['color_theme'] ); ?>">
            <div class="icon <?php echo esc_attr( $settings['theme'] ) ?>">
                <i class="<?php echo esc_attr( $settings['icon'] ); ?>"></i>
            </div>
            <div class="content">
                <h4 class="title"><?php echo esc_html( $settings['title'] ); ?></h4>
                <p><?php echo esc_html( $settings['description'] ) ?></p>
            </div>
        </div>

		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Featrue_Box_Two_Widget() );