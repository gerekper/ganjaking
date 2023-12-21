<?php
/**
 * Toggle
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons\Elementor\Controls\Group_Control_Foreground;

defined( 'ABSPATH' ) || die();

class Toggle extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Advanced Toggle', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-drawer';
	}

	public function get_keywords() {
		return [ 'accordion', 'toggle', 'collapsible', 'tabs', 'switch' ];
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->__toggle_content_controls();
		$this->__options_content_controls();
	}

	protected function __toggle_content_controls() {

		$this->start_controls_section(
			'_section_toggle',
			[
				'label' => __( 'Toggle', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Title', 'happy-addons-pro' ),
				'default'     => __( 'Toggle Title', 'happy-addons-pro' ),
				'placeholder' => __( 'Type Toggle Title', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'icon',
			[
				'type'       => Controls_Manager::ICONS,
				'label'      => __( 'Icon', 'happy-addons-pro' ),
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'source',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Content Source', 'happy-addons-pro' ),
				'default'   => 'editor',
				'separator' => 'before',
				'options'   => [
					'editor'   => __( 'Editor', 'happy-addons-pro' ),
					'template' => __( 'Template', 'happy-addons-pro' ),
				],
			]
		);

		$repeater->add_control(
			'editor',
			[
				'label'      => __( 'Content Editor', 'happy-addons-pro' ),
				'show_label' => false,
				'type'       => Controls_Manager::WYSIWYG,
				'condition'  => [
					'source' => 'editor',
				],
				'dynamic'    => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'template',
			[
				'label'       => __( 'Section Template', 'happy-addons-pro' ),
				'placeholder' => __( 'Select a section template for as tab content', 'happy-addons-pro' ),
				'description' => sprintf(
					__( 'Wondering what is section template or need to create one? Please click %1$shere%2$s ', 'happy-addons-pro' ),
					'<a target="_blank" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section' ) ) . '">',
					'</a>'
				),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => hapro_get_section_templates(),
				'condition'   => [
					'source' => 'template',
				],
			]
		);

		$this->add_control(
			'tabs',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{title}}',
				'default'     => [
					[
						'title'  => 'Toggle Item 1',
						'source' => 'editor',
						'editor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore <br><br>et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
					],
					[
						'title'  => 'Toggle Item 2',
						'source' => 'editor',
						'editor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore <br><br>et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __options_content_controls() {

		$this->start_controls_section(
			'_section_options',
			[
				'label' => __( 'Options', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'closed_icon',
			[
				'type'    => Controls_Manager::ICONS,
				'label'   => __( 'Closed Icon', 'happy-addons-pro' ),
				'default' => [
					'library' => 'solid',
					'value'   => 'fas fa-plus',
				],
			]
		);

		$this->add_control(
			'opened_icon',
			[
				'type'    => Controls_Manager::ICONS,
				'label'   => __( 'Opened Icon', 'happy-addons-pro' ),
				'default' => [
					'library' => 'solid',
					'value'   => 'fas fa-minus',
				],
			]
		);

		$this->add_control(
			'icon_position',
			[
				'type'           => Controls_Manager::CHOOSE,
				'label'          => __( 'Position', 'happy-addons-pro' ),
				'default'        => 'left',
				'toggle'         => false,
				'options'        => [
					'left'  => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class'   => 'ha-toggle--icon-',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__item_style_controls();
		$this->__title_style_controls();
		$this->__title_icon_style_controls();
		$this->__content_style_controls();
		$this->__open_close_style_controls();
	}

	protected function __item_style_controls() {

		$this->start_controls_section(
			'_section_item',
			[
				'label' => __( 'Item', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_spacing',
			[
				'label'     => __( 'Vertical Spacing (px)', 'happy-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'step'      => 'any',
				'default'   => -1,
				'selectors' => [
					'{{WRAPPER}} .ha-toggle__item:not(:first-child)' => 'margin-top: {{VALUE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'label'    => __( 'Box Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-toggle__item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-toggle__item',
			]
		);

		$this->end_controls_section();
	}

	protected function __title_style_controls() {

		$this->start_controls_section(
			'_section_title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ha-toggle__item-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'label'    => __( 'Text Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-toggle__item-title',
			]
		);

		$this->start_controls_tabs( '_tab_tab_status' );
		$this->start_controls_tab(
			'_tab_tab_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'title_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name'     => 'title_text_gradient',
				'selector' => '{{WRAPPER}} .ha-toggle__item-title-text, {{WRAPPER}} .ha-toggle__item-title-icon i:before, {{WRAPPER}} .ha-toggle__item-title-icon svg, {{WRAPPER}} .ha-toggle__icon i:before, {{WRAPPER}} .ha-toggle__icon svg',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'title_bg',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-toggle__item-title',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_tab_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'title_active_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item-title.ha-toggle__item--active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name'     => 'title_active_text_gradient',
				'selector' => '{{WRAPPER}} .ha-toggle__item-title.ha-toggle__item--active .ha-toggle__item-title-text, {{WRAPPER}} .ha-toggle__item-title.ha-toggle__item--active .ha-toggle__item-title-icon i:before, {{WRAPPER}} .ha-toggle__item-title.ha-toggle__item--active .ha-toggle__icon i:before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'title_active_bg',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-toggle__item-title.ha-toggle__item--active',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __title_icon_style_controls() {

		$this->start_controls_section(
			'_section_title_icon',
			[
				'label' => __( 'Title Icon', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_icon_spacing',
			[
				'label'      => __( 'Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item-title-icon' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_content',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'content_border',
				'label'    => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-toggle__item-content',
			]
		);

		$this->add_control(
			'content_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-toggle__item-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .ha-toggle__item-content',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-toggle__item-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-toggle__item-content',
			]
		);

		$this->end_controls_section();
	}

	protected function __open_close_style_controls() {

		$this->start_controls_section(
			'_section_icon',
			[
				'label' => __( 'Open / Close Icon', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_icon_spacing',
			[
				'label'      => __( 'Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}}.ha-toggle--icon-left .ha-toggle__icon > span' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}}.ha-toggle--icon-right .ha-toggle__icon > span' => 'margin-left: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! is_array( $settings['tabs'] ) || empty( $settings['tabs'] ) ) {
			return;
		}

		$has_closed_icon = ( ! empty( $settings['closed_icon'] ) && ! empty( $settings['closed_icon']['value'] ) );
		$has_opened_icon = ( ! empty( $settings['opened_icon'] ) && ! empty( $settings['opened_icon']['value'] ) );

		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div class="ha-toggle__wrapper" role="tablist">
			<?php
			foreach ( $settings['tabs'] as $index => $item ) :
				$count = $index + 1;

				$title_setting_key = $this->get_repeater_setting_key( 'title', 'tabs', $index );
				$has_title_icon    = ( ! empty( $item['icon'] ) && ! empty( $item['icon']['value'] ) );

				if ( $item['source'] === 'editor' ) {
					$content_setting_key = $this->get_repeater_setting_key( 'editor', 'tabs', $index );
					// $this->add_inline_editing_attributes( $content_setting_key, 'advanced' );
				} else {
					$content_setting_key = $this->get_repeater_setting_key( 'section', 'tabs', $index );
				}

				$this->add_render_attribute(
					$title_setting_key,
					[
						'id'            => 'ha-toggle__item-title-' . $id_int . $count,
						'class'         => [ 'ha-toggle__item-title' ],
						'data-tab'      => $count,
						'role'          => 'tab',
						'aria-controls' => 'ha-toggle__item-content-' . $id_int . $count,
					]
				);

				$this->add_render_attribute(
					$content_setting_key,
					[
						'id'              => 'ha-toggle__item-content-' . $id_int . $count,
						'class'           => [ 'ha-toggle__item-content' ],
						'data-tab'        => $count,
						'role'            => 'tabpanel',
						'aria-labelledby' => 'ha-toggle__item-title-' . $id_int . $count,
					]
				);

				?>
				<div class="ha-toggle__item">
					<div <?php echo $this->get_render_attribute_string( $title_setting_key ); ?>>
						<?php if ( $has_opened_icon || $has_closed_icon ) : ?>
							<span class="ha-toggle__item-icon ha-toggle__icon" aria-hidden="true">
								<?php if ( $has_opened_icon ) : ?>
									<span class="ha-toggle__icon--closed"><?php ha_render_icon( $settings, false, 'closed_icon' ); ?></span>
								<?php endif; ?>
								<?php if ( $has_opened_icon ) : ?>
									<span class="ha-toggle__icon--opened"><?php ha_render_icon( $settings, false, 'opened_icon' ); ?></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
						<div class="ha-toggle__item-title-inner">
							<?php if ( $has_title_icon ) : ?>
								<span class="ha-toggle__item-title-icon"><?php ha_render_icon( $item, false, 'icon' ); ?></span>
							<?php endif; ?>
							<span class="ha-toggle__item-title-text"><?php echo ha_kses_basic( $item['title'] ); ?></span>
						</div>
					</div>
					<div <?php echo $this->get_render_attribute_string( $content_setting_key ); ?>>
						<?php
						if ( $item['source'] === 'editor' ) :
							echo $this->parse_text_editor( $item['editor'] );
						elseif ( $item['source'] === 'template' && $item['template'] ) :
							echo ha_elementor()->frontend->get_builder_content_for_display( $item['template'] );
						endif;
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
