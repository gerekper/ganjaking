<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Icon_Box_List extends Widget_Base {

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
		return 'appside-icon-box-list-one-widget';
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
		return esc_html__( 'Icon Box List', 'aapside-master' );
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
		return 'eicon-editor-list-ul';
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
		$this->add_control( 'icon_list_items', [
			'label'       => esc_html__( 'Icon List', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'icon'  => 'xg-icon-save',
					'title' => esc_html__( 'Easily manage', 'aapside-master' ),
				]
			],
			'fields'      => [
				[
					'name'        => 'icon',
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select icon.', 'aapside-master' ),
					'default'     => 'xg-icon-save'
				],
				[
					'name'        => 'title',
					'label'       => esc_html__( 'Title', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter title.', 'aapside-master' ),
					'default'     => esc_html__( 'Easily manage', 'aapside-master' )
				],
			],
			'title_field' => "{{title}}"
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'styling_settings', [
			'label' => esc_html__( 'Styling Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_control( 'icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .icon-box-list li .icon" => "color: {{COLOR}}"
			]
		] );
		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Title Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .icon-box-list li .content .title" => "color: {{COLOR}}"
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
		$settings       = $this->get_settings_for_display();
		$icon_box_items = $settings['icon_list_items'];
		?>
        <ul class="icon-box-list">
			<?php foreach ( $icon_box_items as $item ):
				?>
                <li>
                    <div class="icon"><i class="<?php echo esc_attr( $item['icon'] ) ?>"></i></div>
                    <div class="content"><h4 class="title"><?php echo esc_html( $item['title'] ) ?></h4></div>
                </li>
			<?php endforeach; ?>
        </ul>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Icon_Box_List() );