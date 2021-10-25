<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Tab_One_Widget extends Widget_Base {

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
		return 'appside-tab-one-widget';
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
		return esc_html__( 'Tabs: 01', 'aapside-master' );
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
		return 'eicon-tabs';
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
		$this->add_control( 'tabs_items', [
			'label'       => esc_html__( 'Tabs Item', 'aapside-master' ),
			'type'        => Controls_Manager::REPEATER,
			'default'     => [
				[
					'title'       => esc_html__( 'Log In Account', 'aapside-master' ),
					'icon'        => 'flaticon-checked',
					'number'      => esc_html__( '1', 'aapside-master' ),
					'description' => esc_html__( 'Innovative solutions with the best. Incididunt dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor incididunt ut labore et dolore', 'apprise-master' )

				],
				[
					'title'       => esc_html__( 'Open Settings', 'aapside-master' ),
					'icon'        => 'flaticon-settings-1',
					'number'      => esc_html__( '2', 'aapside-master' ),
					'description' => esc_html__( 'Innovative solutions with the best. Incididunt dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor tempor incididunt ut labore et dolore', 'apprise-master' )
				]
			],
			'fields'      => [
				[
					'name'        => 'title',
					'label'       => esc_html__( 'Title', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter title.', 'aapside-master' ),
					'default'     => esc_html__( 'Log In Account', 'aapside-master' )
				],
				[
					'name'        => 'icon',
					'label'       => esc_html__( 'Icon', 'aapside-master' ),
					'type'        => Controls_Manager::ICON,
					'description' => esc_html__( 'select icon.', 'aapside-master' ),
					'default'     => 'flaticon-checked'
				],
				[
					'name'        => 'number',
					'label'       => esc_html__( 'Number', 'aapside-master' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'enter number.', 'aapside-master' ),
					'default'     => esc_html__( '1', 'apprise-master' )
				],
				[
					'name'        => 'description',
					'label'       => esc_html__( 'Content', 'aapside-master' ),
					'type'        => Controls_Manager::WYSIWYG,
					'description' => esc_html__( 'enter tab content.', 'aapside-master' ),
				],

			],
			'title_field' => "{{title}}"
		] );


		$this->end_controls_section();
		/* button styling start */
		$this->start_controls_section( 'buttton_styling', [
			'label' => esc_html__( 'Tabs Button Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->start_controls_tabs(
			'button_style_tabs'
		);

		$this->start_controls_tab(
			'button_style_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_background',
			'label'    => esc_html__( 'Button Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link"
		] );

		$this->add_control( 'button_color', [
			'label'     => esc_html__( 'Button Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'icon_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link i" => "color: {{VALUE}}"
			]
		] );
		$this->add_control('divider',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_control( 'number_color', [
				'label'     => esc_html__( 'Number Color', 'aapside-master' ),
				'type'      => Controls_Manager::COLOR,
				"selectors" => [
					"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link .number" => "color: {{VALUE}}"
				]
			]
		);
		$this->add_control( 'number_border_color', [
				'label'     => esc_html__( 'Number Border Color', 'aapside-master' ),
				'type'      => Controls_Manager::COLOR,
				"selectors" => [
					"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link .number" => "border-color: {{VALUE}}"
				]
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'number_background',
			'label'    => esc_html__( 'Button Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link .number"
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover_tab',
			[
				'label' => __( 'Active', 'aapside-master' ),
			]
		);

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'button_hover_background',
			'label'    => esc_html__( 'Button Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link.active"
		] );

		$this->add_control( 'button_hover_color', [
			'label'     => esc_html__( 'Button Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link.active" => "color: {{VALUE}}"
			]
		] );
		$this->add_control( 'icon_hover_color', [
			'label'     => esc_html__( 'Icon Color', 'aapside-master' ),
			'type'      => Controls_Manager::COLOR,
			"selectors" => [
				"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link.active i" => "color: {{VALUE}}"
			]
		] );
		$this->add_control('divider_01',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_control( 'number_hover_color', [
				'label'     => esc_html__( 'Number Color', 'aapside-master' ),
				'type'      => Controls_Manager::COLOR,
				"selectors" => [
					"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link.active .number" => "color: {{VALUE}}"
				]
			]
		);
		$this->add_control( 'number_hover_border_color', [
				'label'     => esc_html__( 'Number Border Color', 'aapside-master' ),
				'type'      => Controls_Manager::COLOR,
				"selectors" => [
					"{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link.active .number" => "border-color: {{VALUE}}"
				]
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'number_hover_background',
			'label'    => esc_html__( 'Button Background ', 'aapside-master' ),
			'selector' => "{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link.active .number"
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/* button styling end */

		$this->start_controls_section( 'typography_settings', [
			'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'button_typography',
			'label' => esc_html__('Button Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .how-it-work-tab-nav .nav-tabs .nav-item .nav-link"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'description_typography',
			'label' => esc_html__('Description Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .how-it-works-tab-content *"
		]);

		$this->end_controls_section();

		/* tabs description start */
		$this->start_controls_section( 'tab_description_styling', [
			'label' => esc_html__( 'Tabs Description Styling', 'aapside-master' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );
		$this->add_control( 'tabs_description_color', [
				'label'     => esc_html__( 'Text Color', 'aapside-master' ),
				'type'      => Controls_Manager::COLOR,
				"selectors" => [
					"{{WRAPPER}} .how-it-works-tab-content *" => "color: {{VALUE}}",
					"{{WRAPPER}} .how-it-works-tab-content p" => "color: {{VALUE}}"
				]
			]
		);
		$this->end_controls_section();
		/* tabs description end */
	}

	/**
	 * Render Elementor widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$all_tab_item = $settings['tabs_items'];
		$a            = 0;
		$b            = 0;
		$tab_id       = array();
		?>
        <div class="how-it-work-tab-nav">
            <ul class="nav nav-tabs" role="tablist">
				<?php
				foreach ( $all_tab_item as $tab ):
					$active_class = ( 0 == $a ) ? 'active show' : '';
					$tab__id = 'appside_tab_' . rand( 999, 9999999 );
					array_push( $tab_id, $tab__id );
					$a ++;
					?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo esc_attr( $active_class ); ?>" data-toggle="tab"
                           href="#<?php echo esc_attr( $tab__id ); ?>" role="tab"><i
                                    class="<?php echo esc_attr( $tab['icon'] ); ?>"></i> <?php echo esc_html( $tab['title'] ); ?>
                            <span class="number"><?php echo esc_html( $tab['number'] ) ?></span></a>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>
        <div class="tab-content">
			<?php foreach ( $all_tab_item as $key => $tab ):
				$tab_content_active_class = ( 0 == $b ) ? 'active show' : '';
				$b ++;
				?>
                <div class="tab-pane fade <?php echo esc_attr( $tab_content_active_class ); ?>"
                     id="<?php echo esc_attr( $tab_id[ $key ] ); ?>" role="tabpanel">
                    <div class="how-it-works-tab-content">
						<?php
                        if (Plugin::instance()->editor->is_edit_mode(get_the_ID())){
                            print $tab['description'];
                        }else{
                            echo apply_filters( 'the_content', $tab['description'] );
                        }
                        ?>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Tab_One_Widget() );