<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Counterup_One_Widget extends Widget_Base {

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
		return 'appside-counterup-one-widget';
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
		return esc_html__( 'Counterup: 01', 'aapside-master' );
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
		return 'eicon-counter';
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
		$this->add_control('theme',[

			'label' => esc_html__('Theme' ,'aapside-master'),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'' => esc_html__('Black','aapside-master'),
				'white' => esc_html__('White','aapside-master')
			),
			'default' => '',
			'description' => esc_html__('set theme','aapside-master')
		]);
		$this->add_control( 'title', [
			'label'       => esc_html__( 'Title', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Positive Reviews', 'aapside-master' ),
			'description' => esc_html__( 'enter title', 'aapside-master' )
		] );
		$this->add_control( 'number', [
			'label'       => esc_html__( 'Number', 'aapside-master' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( '14,567', 'aapside-master' ),
			'description' => esc_html__( 'enter counterup number', 'aapside-master' )
		] );
		$this->add_control( 'icon', [
			'label'       => esc_html__( 'Icon', 'aapside-master' ),
			'type'        => Controls_Manager::ICON,
			'default'     => 'flaticon-rating',
			'description' => esc_html__( 'select icon', 'aapside-master' )
		] );

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
				"{{WRAPPER}} .single-counter-item .icon" => "color: {{VALUE}}"
			]
		]);
		$this->add_control('title_color',[
			'label' => esc_html__('Title Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-counter-item .content .title" => "color: {{VALUE}}"
			]
		]);
		$this->add_control('number_color',[
			'label' => esc_html__('Number Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .single-counter-item .content .count-num" => "color: {{VALUE}}"
			]
		]);
		$this->add_control('styling_divider',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'label' => esc_html__('Title Typography'),
			'name' => 'title_typography',
			'selector' => "{{WRAPPER}} .single-counter-item .content .title"
		]);
		$this->add_control('styling_divider_01',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'label' => esc_html__('Number Typography'),
			'name' => 'number_typography',
			'selector' => "{{WRAPPER}} .single-counter-item .content .count-num"
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
		$settings  = $this->get_settings_for_display();
        $icon = $settings['icon'];
        $title = $settings['title'];
        $number = $settings['number'];
		?>
        <div class="single-counter-item <?php echo esc_attr($settings['theme'])?>">
            <div class="icon">
                <i class="<?php echo esc_attr($icon);?>"></i>
            </div>
            <div class="content">
                <span class="count-num"><?php echo esc_html($number);?></span>
                <h4 class="title"><?php echo esc_html($title);?></h4>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Counterup_One_Widget() );