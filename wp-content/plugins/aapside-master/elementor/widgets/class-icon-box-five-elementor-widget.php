<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Icon_Box_Five_Widget extends Widget_Base {

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
		return 'appside-icon-box-five-widget';
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
		return esc_html__( 'Icon Box: 05', 'aapside-master' );
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
				    'icon-bg-1' => esc_html__('Theme 01'),
				    'icon-bg-2' => esc_html__('Theme 02'),
				    'icon-bg-3' => esc_html__('Theme 03'),
				    'icon-bg-4' => esc_html__('Theme 04'),
                ),
				'description' => esc_html__( 'select theme', 'aapside-master' ),
				'default'     => 'icon-bg-1'
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter  title.', 'aapside-master' ),
				'default'     => esc_html__('Full Management','aapside-master')
			]
		);
		$this->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icon', 'aapside-master' ),
				'type'        => Controls_Manager::ICON,
				'description' => esc_html__( 'select Icon.', 'aapside-master' ),
				'default'     => 'flaticon-vector'
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
				"{{WRAPPER}} .single-icon-item-05 .icon" => "color: {{VALUE}}"
			]
        ]);
		$this->add_control('title_color',[
		   'label' => esc_html__('Title Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-icon-item-05 .content .title" => "color: {{VALUE}}"
			]
        ]);
		$this->add_control('description_color',[
		   'label' => esc_html__('Description Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-icon-item-05 .content p" => "color: {{VALUE}}"
			]
        ]);
		$this->add_control('divider',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-icon-item-05 .content .title"
		]);
		$this->add_control('divider_02',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'description_typography',
			'label' => esc_html__('Description Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-icon-item-05 .content p"
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
        <div class="single-icon-item-05">
            <div class="icon <?php echo esc_attr($settings['theme'])?>">
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

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Icon_Box_Five_Widget() );