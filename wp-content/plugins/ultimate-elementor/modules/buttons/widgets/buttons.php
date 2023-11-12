<?php
/**
 * UAEL Buttons.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Buttons\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;
use Elementor\Widget_Button;
use Elementor\Group_Control_Background;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Buttons.
 */
class Buttons extends Common_Widget {

	/**
	 * Retrieve Buttons Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Buttons' );
	}

	/**
	 * Retrieve Buttons Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Buttons' );
	}

	/**
	 * Retrieve Buttons Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Buttons' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Buttons' );
	}

	/**
	 * Retrieve Button sizes.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Button Sizes.
	 */
	public static function get_button_sizes() {
		return Widget_Button::get_button_sizes();
	}

	/**
	 * Register Buttons controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_presets_control( 'Buttons', $this );

		// Content Tab.
		$this->register_buttons_content_controls();

		// Style Tab.
		$this->register_styling_style_controls();
		$this->register_color_content_controls();
		$this->register_spacing_content_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Buttons General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_buttons_content_controls() {

		$this->start_controls_section(
			'section_buttons',
			array(
				'label' => __( 'Button', 'uael' ),
			)
		);

			$repeater = new Repeater();

				$repeater->start_controls_tabs( 'button_repeater' );

					$repeater->start_controls_tab(
						'button_general',
						array(
							'label' => __( 'General', 'uael' ),
						)
					);

					$repeater->add_control(
						'text',
						array(
							'label'       => __( 'Text', 'uael' ),
							'type'        => Controls_Manager::TEXT,
							'default'     => __( 'Click Me', 'uael' ),
							'placeholder' => __( 'Click Me', 'uael' ),
							'dynamic'     => array(
								'active' => true,
							),
						)
					);

					$repeater->add_control(
						'link',
						array(
							'label'    => __( 'Link', 'uael' ),
							'type'     => Controls_Manager::URL,
							'default'  => array(
								'url'         => '#',
								'is_external' => '',
							),
							'dynamic'  => array(
								'active' => true,
							),
							'selector' => '',
						)
					);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$repeater->add_control(
				'new_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'label_block'      => true,
				)
			);

		} else {
			$repeater->add_control(
				'icon',
				array(
					'label'       => __( 'Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
				)
			);
		}

					$repeater->add_control(
						'icon_align',
						array(
							'label'      => __( 'Icon Position', 'uael' ),
							'type'       => Controls_Manager::SELECT,
							'default'    => 'left',
							'options'    => array(
								'left'  => __( 'Before', 'uael' ),
								'right' => __( 'After', 'uael' ),
							),
							'conditions' => array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'icon' ),
										'operator' => '!=',
										'value'    => '',
									),
								),
							),
						)
					);

					$repeater->add_control(
						'icon_indent',
						array(
							'label'      => __( 'Icon Spacing', 'uael' ),
							'type'       => Controls_Manager::SLIDER,
							'range'      => array(
								'px' => array(
									'max' => 50,
								),
							),
							'conditions' => array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'icon' ),
										'operator' => '!=',
										'value'    => '',
									),
								),
							),
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$repeater->add_control(
						'icon_size',
						array(
							'label'      => __( 'Icon size', 'uael' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', '%', 'em' ),
							'range'      => array(
								'px' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 1,
								),
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
								'em' => array(
									'min' => 0,
									'max' => 100,
								),
							),
							'default'    => array(
								'unit' => 'em',
								'size' => 1,
							),
							'conditions' => array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'icon' ),
										'operator' => '!=',
										'value'    => '',
									),
								),
							),
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button .elementor-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button .elementor-button-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$repeater->add_control(
						'css_id',
						array(
							'label'       => __( 'CSS ID', 'uael' ),
							'type'        => Controls_Manager::TEXT,
							'default'     => '',
							'label_block' => true,
							'title'       => __( 'Add your custom id WITHOUT the # key.', 'uael' ),
						)
					);

					$repeater->add_control(
						'css_classes',
						array(
							'label'       => __( 'CSS Classes', 'uael' ),
							'type'        => Controls_Manager::TEXT,
							'default'     => '',
							'label_block' => true,
							'title'       => __( 'Add space separated custom classes WITHOUT the dot.', 'uael' ),
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab(
					'button_design',
					array(
						'label' => __( 'Design', 'uael' ),
					)
				);

					$repeater->add_control(
						'html_message',
						array(
							'type'            => Controls_Manager::RAW_HTML,
							'raw'             => __( 'Set custom styles that will only affect this specific button.', 'uael' ),
							'content_classes' => 'elementor-control-field-description',
						)
					);

					$repeater->add_control(
						'color_options',
						array(
							'label'     => __( 'Normal', 'uael' ),
							'type'      => Controls_Manager::HEADING,
							'separator' => 'before',
						)
					);

					$repeater->add_control(
						'btn_text_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button' => 'color: {{VALUE}};',
							),
						)
					);

					$repeater->add_control(
						'btn_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button' => 'background-color: {{VALUE}};',
							),
						)
					);

					$repeater->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'      => 'btn_border',
							'label'     => __( 'Border', 'uael' ),
							'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button',
							'separator' => 'before',
						)
					);

					$repeater->add_control(
						'btn_border_radius',
						array(
							'label'      => __( 'Border Radius', 'uael' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%' ),
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$repeater->add_control(
						'hover_options',
						array(
							'label' => __( 'Hover', 'uael' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$repeater->add_control(
						'btn_text_hover_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button:hover' => 'color: {{VALUE}};',
							),
						)
					);

					$repeater->add_control(
						'btn_background_hover_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button:hover' => 'background-color: {{VALUE}};',
							),
						)
					);

					$repeater->add_control(
						'btn_border_hover_color',
						array(
							'label'     => __( 'Border Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-button:hover' => 'border-color: {{VALUE}};',
							),
						)
					);

					$repeater->add_group_control(
						Group_Control_Typography::get_type(),
						array(
							'name'     => 'btn_typography',
							'global'   => array(
								'default' => Global_Typography::TYPOGRAPHY_ACCENT,
							),
							'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} a.elementor-button .elementor-button-text',
						)
					);

				$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'buttons',
				array(
					'label'       => __( 'Buttons', 'uael' ),
					'type'        => Controls_Manager::REPEATER,
					'show_label'  => true,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{ text }}',
					'default'     => array(
						array(
							'text' => __( 'Enroll Today', 'uael' ),
						),
						array(
							'text' => __( 'Learn More', 'uael' ),
						),
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Buttons Spacing Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_spacing_content_controls() {
		$this->start_controls_section(
			'general_spacing',
			array(
				'label' => __( 'Spacing', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'gap',
				array(
					'label'      => __( 'Space between buttons', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 1,
							'max' => 1000,
						),
					),
					'default'    => array(
						'size' => 10,
						'unit' => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-dual-button-wrap .uael-button-wrapper' => 'margin-right: calc( {{SIZE}}{{UNIT}} / 2) ; margin-left: calc( {{SIZE}}{{UNIT}} / 2);',
						'{{WRAPPER}}.uael-button-stack-none .uael-dual-button-wrap' => 'margin-right: calc( -{{SIZE}}{{UNIT}} / 2) ; margin-left: calc( -{{SIZE}}{{UNIT}} / 2);',
						'(desktop){{WRAPPER}}.uael-button-stack-desktop .uael-dual-button-wrap .uael-button-wrapper'  => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 ); margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: 0; margin-left: 0;',
						'(desktop){{WRAPPER}}.uael-button-stack-desktop .uael-dual-button-wrap .uael-button-wrapper:last-child' => 'margin-bottom: 0;',
						'(desktop){{WRAPPER}}.uael-button-stack-desktop .uael-dual-button-wrap .uael-button-wrapper:first-child' => 'margin-top: 0;',
						'(tablet){{WRAPPER}}.uael-button-stack-tablet .uael-dual-button-wrap .uael-button-wrapper'        => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 ); margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: 0; margin-left: 0;',
						'(tablet){{WRAPPER}}.uael-button-stack-tablet .uael-dual-button-wrap .uael-button-wrapper:last-child' => 'margin-bottom: 0;',
						'(tablet){{WRAPPER}}.uael-button-stack-tablet .uael-dual-button-wrap .uael-button-wrapper:first-child' => 'margin-top: 0;',
						'(mobile){{WRAPPER}}.uael-button-stack-mobile .uael-dual-button-wrap .uael-button-wrapper'        => 'margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 ); margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: 0; margin-left: 0;',
						'(mobile){{WRAPPER}}.uael-button-stack-mobile .uael-dual-button-wrap .uael-button-wrapper:last-child' => 'margin-bottom: 0;',
						'(mobile){{WRAPPER}}.uael-button-stack-mobile .uael-dual-button-wrap .uael-button-wrapper:first-child' => 'margin-top: 0;',
					),
				)
			);

			$this->add_control(
				'stack_on',
				array(
					'label'        => __( 'Stack on', 'uael' ),
					'description'  => __( 'Choose on what breakpoint where the buttons will stack.', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'none',
					'options'      => array(
						'none'    => __( 'None', 'uael' ),
						'desktop' => __( 'Desktop', 'uael' ),
						'tablet'  => __( 'Tablet', 'uael' ),
						'mobile'  => __( 'Mobile', 'uael' ),
					),
					'prefix_class' => 'uael-button-stack-',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.1.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$link = UAEL_DOMAIN . 'docs/multi-buttons-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $link . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=Izbr-oO0VkU&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc&index=13" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Register Buttons Colors Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_color_content_controls() {

		$this->start_controls_section(
			'general_colors',
			array(
				'label' => __( 'Styling', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->start_controls_tabs( '_button_style' );

				$this->start_controls_tab(
					'_button_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'all_text_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'all_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'all_border',
							'label'    => __( 'Border', 'uael' ),
							'selector' => '{{WRAPPER}} .elementor-button',
						)
					);

					$this->add_control(
						'all_border_radius',
						array(
							'label'      => __( 'Border Radius', 'uael' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%' ),
							'selectors'  => array(
								'{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'     => 'all_button_box_shadow',
							'selector' => '{{WRAPPER}} .elementor-button',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'all_button_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'all_hover_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button:hover' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'all_background_hover_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button:hover' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'all_border_hover_color',
						array(
							'label'     => __( 'Border Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button:hover' => 'border-color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'      => 'all_button_hover_box_shadow',
							'selector'  => '{{WRAPPER}} .elementor-button:hover',
							'separator' => 'after',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Buttons Styling Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_styling_style_controls() {

		$this->start_controls_section(
			'section_styling',
			array(
				'label' => __( 'Design', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'size',
				array(
					'label'       => __( 'Size', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'sm',
					'label_block' => true,
					'options'     => self::get_button_sizes(),
				)
			);

			$this->add_responsive_control(
				'padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'all_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector' => '{{WRAPPER}} a.elementor-button,{{WRAPPER}} a.elementor-button svg',
				)
			);

			$this->add_responsive_control(
				'align',
				array(
					'label'        => __( 'Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'    => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
						'justify' => array(
							'title' => __( 'Justify', 'uael' ),
							'icon'  => 'fa fa-align-justify',
						),
					),
					'default'      => 'center',
					'toggle'       => false,
					'prefix_class' => 'uael%s-button-halign-',
				)
			);

			$this->add_control(
				'hover_animation',
				array(
					'label' => __( 'Hover Animation', 'uael' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render button widget text.
	 *
	 * @param array $button The item settings array.
	 * @param int   $i The index id.
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_button_text( $button, $i ) {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
		$this->add_render_attribute( 'content-wrapper', 'class', 'uael-buttons-icon-' . $button['icon_align'] );

		$this->add_render_attribute( 'icon-align_' . $i, 'class', 'elementor-align-icon-' . $button['icon_align'] );
		$this->add_render_attribute( 'icon-align_' . $i, 'class', 'elementor-button-icon' );

		$this->add_render_attribute( 'btn-text_' . $i, 'class', 'elementor-button-text' );
		$this->add_render_attribute( 'btn-text_' . $i, 'class', 'elementor-inline-editing' );

		$text_key = $this->get_repeater_setting_key( 'text', 'buttons', $i );
		$this->add_inline_editing_attributes( $text_key, 'none' );
		?>
		<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-wrapper' ) ); ?>>
			<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
				<?php if ( ! empty( $button['icon'] ) || ! empty( $button['new_icon'] ) ) : ?>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align_' . esc_attr( $i ) ) ); ?>>
						<?php
						$migrated = isset( $button['__fa4_migrated']['new_icon'] );
						$is_new   = ! isset( $button['icon'] ) && \Elementor\Icons_Manager::is_migration_allowed();
						if ( $is_new || $migrated ) {

							\Elementor\Icons_Manager::render_icon( $button['new_icon'], array( 'aria-hidden' => 'true' ) );
						} elseif ( ! empty( $button['icon'] ) ) {
							?>
							<i class="<?php echo esc_attr( $button['icon'] ); ?>" aria-hidden="true"></i>
						<?php } ?>
					</span>
				<?php endif; ?>
			<?php } elseif ( ! empty( $button['icon'] ) ) { ?>
				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align_' . esc_attr( $i ) ) ); ?>>
					<i class="<?php echo esc_attr( $button['icon'] ); ?>" aria-hidden="true"></i>
				</span>
			<?php } ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'btn-text_' . esc_attr( $i ) ) ); ?> data-elementor-setting-key="<?php echo esc_attr( $text_key ); ?>" data-elementor-inline-editing-toolbar="none"><?php echo wp_kses_post( $button['text'] ); ?></span>
		</span>
		<?php
	}

	/**
	 * Render Buttons output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$node_id  = $this->get_id();
		$count    = count( $settings['buttons'] );
		ob_start();
		?>
		<div class="uael-dual-button-outer-wrap">
			<div class="uael-dual-button-wrap">
				<?php
				for ( $i = 0; $i < $count; $i++ ) :
					if ( ! is_array( $settings['buttons'][ $i ] ) ) {
						continue;
					}
					$button = $settings['buttons'][ $i ];

					$this->add_render_attribute( 'button_wrap_' . $i, 'class', 'uael-button-wrapper elementor-button-wrapper uael-dual-button' );
					$this->add_render_attribute( 'button_wrap_' . $i, 'class', 'elementor-repeater-item-' . $button['_id'] );
					$this->add_render_attribute( 'button_wrap_' . $i, 'class', 'uael-dual-button-' . $i );

					$this->add_render_attribute( 'button_' . $i, 'class', 'elementor-button-link elementor-button' );

					if ( '' !== $button['css_classes'] ) {
						$this->add_render_attribute( 'button_' . $i, 'class', $button['css_classes'] );
					}

					if ( '' !== $settings['size'] ) {
						$this->add_render_attribute( 'button_' . $i, 'class', 'elementor-size-' . $settings['size'] );
					}

					if ( '' !== $button['css_id'] ) {
						$this->add_render_attribute( 'button_' . $i, 'id', $button['css_id'] );
					}

					if ( ! empty( $button['link']['url'] ) ) {

						$this->add_link_attributes( 'button_' . $i, $button['link'] );

						$this->add_render_attribute( 'button_' . $i, 'class', 'elementor-button-link' );
					}

					if ( $settings['hover_animation'] ) {
						$this->add_render_attribute( 'button_' . $i, 'class', 'elementor-animation-' . $settings['hover_animation'] );
					}
					?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'button_wrap_' . esc_attr( $i ) ) ); ?>>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button_' . esc_attr( $i ) ) ); ?>>
						<?php $this->render_button_text( $button, $i ); ?>
					</a>
				</div>
				<?php endfor; ?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render button widgets output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="uael-dual-button-outer-wrap">
			<div class="uael-dual-button-wrap">
				<#
					var iconsHTML = {};
					if ( settings.buttons ) {
						var counter = 1;
						_.each( settings.buttons, function( item, index ) {

							var button_wrap = 'elementor-repeater-item-' + item._id + ' uael-dual-button-' + counter;

							var button_class = 'elementor-size-' + settings.size + ' ' + item.css_classes;

						var buttonContentKey = view.getRepeaterSettingKey( 'text', 'buttons', index );
						view.addRenderAttribute(buttonContentKey, 'class', 'elementor-button-text' );
						view.addInlineEditingAttributes( buttonContentKey, 'advanced' );

							button_wrap += ' elementor-animation-' + settings.hover_animation;
							var new_icon_align = '';
							var icon_align = '';
				#>
				<div class="uael-button-wrapper elementor-button-wrapper uael-dual-button {{ button_wrap }}">
					<a id="{{ item.css_id }}" href="{{ item.link.url }}" class="elementor-button-link elementor-button {{ button_class }}">
						<# new_icon_align = ' uael-buttons-icon-' + item.icon_align; #>
						<span class="elementor-button-content-wrapper{{ new_icon_align }}">
							<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
								<#
								if ( item.icon || item.new_icon ) {
									icon_align = 'elementor-align-icon-' + item.icon_align;
								#>
									<span class="elementor-button-icon {{ icon_align }}">
										<#
											iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.new_icon, { 'aria-hidden': true }, 'i' , 'object' );
											migrated = elementor.helpers.isIconMigrated( item, 'new_icon' );
										#>
										<#
											if ( ( ! item.icon || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) {
										#>
												{{{ iconsHTML[ index ].value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
											<# } else { #>

												<i class="{{ item.icon }}" aria-hidden="true"></i>
											<# } #>
									</span>
								<# } #>
							<?php } else { ?>
								<# if ( item.icon ) {
									icon_align = 'elementor-align-icon-' + item.icon_align;
									#>
									<span class="elementor-button-icon {{ icon_align }}">
										<i class="{{ item.icon }}" aria-hidden="true"></i>
									</span>
								<# } #>
							<?php } ?>
							<span {{ view.getRenderAttributeString( buttonContentKey ) }} >{{ item.text }}</span>
						</span>
					</a>
				</div>
				<#
					counter++;
					});
				}
				#>
			</div>
		</div>
		<?php
	}

}
