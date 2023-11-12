<?php
/**
 * UAEL Marketing Button.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\MarketingButton\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Widget_Button;
use Elementor\Group_Control_Background;
// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Marketing Button.
 */
class Marketing_Button extends Common_Widget {

	/**
	 * Retrieve Marketing Button Widget name.
	 *
	 * @since 1.10.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Marketing_Button' );
	}

	/**
	 * Retrieve Marketing Button Widget title.
	 *
	 * @since 1.10.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Marketing_Button' );
	}

	/**
	 * Retrieve Marketing Button Widget icon.
	 *
	 * @since 1.10.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Marketing_Button' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.10.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Marketing_Button' );
	}

	/**
	 * Retrieve Marketing Button sizes.
	 *
	 * @since 1.10.0
	 * @access public
	 *
	 * @return array Marketing Button Sizes.
	 */
	public static function get_button_sizes() {
		return Widget_Button::get_button_sizes();
	}

	/**
	 * Register Marketing Button controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_presets_control( 'Marketing_Button', $this );

		// Content Tab.
		$this->register_buttons_content_controls();

		// Style Tab.
		$this->register_styling_style_controls();
		$this->register_color_content_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Marketing Button General Controls.
	 *
	 * @since 1.10.0
	 * @access protected
	 */
	protected function register_buttons_content_controls() {

		$this->start_controls_section(
			'section_buttons',
			array(
				'label' => __( 'General', 'uael' ),
			)
		);
			$this->add_control(
				'text',
				array(
					'label'   => __( 'Title', 'uael' ),
					'type'    => Controls_Manager::TEXTAREA,
					'rows'    => '2',
					'default' => __( 'Subscribe Now', 'uael' ),
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'desc_text',
				array(
					'label'   => __( 'Description', 'uael' ),
					'type'    => Controls_Manager::TEXTAREA,
					'rows'    => '3',
					'default' => __( 'Get access to Premium Features for FREE for a year!', 'uael' ),
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
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
			$this->add_control(
				'new_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default'          => array(
						'value'   => 'fa fa-arrow-right',
						'library' => 'fa-solid',
					),
					'separator'        => 'before',
				)
			);
		} else {
			$this->add_control(
				'icon',
				array(
					'label'     => __( 'Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'default'   => 'fa fa-arrow-right',
					'separator' => 'before',
				)
			);
		}

			$this->add_control(
				'icon_align',
				array(
					'label'      => __( 'Icon Position', 'uael' ),
					'type'       => Controls_Manager::SELECT,
					'default'    => 'left',
					'options'    => array(
						'left'      => __( 'Before Title', 'uael' ),
						'right'     => __( 'After Title', 'uael' ),
						'all_left'  => __( 'Before Title & Description', 'uael' ),
						'all_right' => __( 'After Title & Description', 'uael' ),
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

			$this->add_control(
				'icon_vertical_align',
				array(
					'label'       => __( 'Icon Vertical Alignment', 'uael' ),
					'type'        => Controls_Manager::CHOOSE,
					'label_block' => false,
					'default'     => 'center',
					'options'     => array(
						'flex-start' => array(
							'title' => __( 'Top', 'uael' ),
							'icon'  => 'eicon-v-align-top',
						),
						'center'     => array(
							'title' => __( 'Middle', 'uael' ),
							'icon'  => 'eicon-v-align-middle',
						),
					),
					'condition'   => array(
						'icon_align' => array( 'all_left', 'all_right' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} .uael-marketing-buttons-all_left.elementor-button .elementor-button-icon, {{WRAPPER}} .uael-marketing-buttons-all_right.elementor-button .elementor-button-icon' => 'align-self: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'icon_size',
				array(
					'label'              => __( 'Icon Size', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'max' => 50,
						),
					),
					'conditions'         => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'icon' ),
								'operator' => '!=',
								'value'    => '',
							),
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'icon_indent',
				array(
					'label'              => __( 'Icon Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'max' => 50,
						),
					),
					'conditions'         => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'icon' ),
								'operator' => '!=',
								'value'    => '',
							),
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-align-icon-right,
						{{WRAPPER}} .uael-marketing-buttons-all_right.elementor-button .elementor-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .elementor-align-icon-left,
						{{WRAPPER}} .uael-marketing-buttons-all_left.elementor-button .elementor-button-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'css_id',
				array(
					'label'   => __( 'CSS ID', 'uael' ),
					'type'    => Controls_Manager::TEXT,
					'default' => '',
					'title'   => __( 'Add your custom id WITHOUT the # key.', 'uael' ),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.10.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/marketing-button-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Register Marketing Button Colors Controls.
	 *
	 * @since 1.10.0
	 * @access protected
	 */
	protected function register_color_content_controls() {

		$this->start_controls_section(
			'general_colors',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'text_align',
				array(
					'label'        => __( 'Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'      => 'center',
					'toggle'       => false,
					'prefix_class' => 'uael-mbutton-text-',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'all_typography',
					'label'    => __( 'Title Typography', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector' => '{{WRAPPER}} .uael-marketing-button-title',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'desc_typography',
					'label'    => __( 'Description Typography', 'uael' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .uael-marketing-button .uael-marketing-button-desc',
				)
			);

			$this->add_responsive_control(
				'title_margin_bottom',
				array(
					'label'              => __( 'Space between Title & Description', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'size_units'         => array( 'px', 'em', 'rem' ),
					'range'              => array(
						'px' => array(
							'min' => 1,
							'max' => 50,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-marketing-button .elementor-button-content-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'frontend_available' => true,
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Marketing Button Styling Controls.
	 *
	 * @since 1.10.0
	 * @access protected
	 */
	protected function register_styling_style_controls() {

		$this->start_controls_section(
			'section_styling',
			array(
				'label' => __( 'Button', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'align',
				array(
					'label'              => __( 'Alignment', 'uael' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => array(
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
					'default'            => 'center',
					'toggle'             => false,
					'prefix_class'       => 'elementor%s-align-',
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'size',
				array(
					'label'   => __( 'Size', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'md',
					'options' => self::get_button_sizes(),
				)
			);

			$this->add_responsive_control(
				'padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', 'em', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'frontend_available' => true,
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
						'all_desc_color',
						array(
							'label'     => __( 'Description Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-marketing-button .uael-marketing-button-desc' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'           => 'all_background_color',
							'label'          => __( 'Background Color', 'uael' ),
							'types'          => array( 'classic', 'gradient' ),
							'selector'       => '{{WRAPPER}} a.elementor-button',
							'fields_options' => array(
								'color' => array(
									'global' => array(
										'default' => Global_Colors::COLOR_ACCENT,
									),
								),
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

					$this->add_control(
						'blink_effect',
						array(
							'label'   => __( 'Flare Animation', 'uael' ),
							'type'    => Controls_Manager::SWITCHER,
							'default' => 'no',
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
						'all_desc__hover_color',
						array(
							'label'     => __( 'Description Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-marketing-button a.elementor-button:hover .uael-marketing-button-desc' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'           => 'all_background_hover_color',
							'label'          => __( 'Background Color', 'uael' ),
							'types'          => array( 'classic', 'gradient' ),
							'selector'       => '{{WRAPPER}} a.elementor-button:hover',
							'fields_options' => array(
								'color' => array(
									'global' => array(
										'default' => Global_Colors::COLOR_ACCENT,
									),
								),
							),
						)
					);

					$this->add_control(
						'all_border_hover_color',
						array(
							'label'     => __( 'Border Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
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

					$this->add_control(
						'hover_animation',
						array(
							'label'       => __( 'Hover Animation', 'uael' ),
							'type'        => Controls_Manager::HOVER_ANIMATION,
							'label_block' => true,
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Marketing Button widget icon.
	 *
	 * @since 1.16.1
	 * @param array $settings settings.
	 * @access protected
	 */
	protected function render_button_icon( $settings ) {

		if ( UAEL_Helper::is_elementor_updated() ) {
			if ( ! isset( $settings['icon'] ) && ! \Elementor\Icons_Manager::is_migration_allowed() ) {
				// add old default.
				$settings['icon'] = 'fa fa-arrow-right';
			}

			$has_icon = ! empty( $settings['icon'] );

			if ( ! $has_icon && ! empty( $settings['new_icon']['value'] ) ) {
				$has_icon = true;
			}

			if ( $has_icon ) {
				$migrated = isset( $settings['__fa4_migrated']['new_icon'] );
				$is_new   = ! isset( $settings['icon'] ) && \Elementor\Icons_Manager::is_migration_allowed(); ?>

				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align' ) ); ?>>

					<?php
					if ( $is_new || $migrated ) {
						\Elementor\Icons_Manager::render_icon( $settings['new_icon'], array( 'aria-hidden' => 'true' ) );
					} elseif ( ! empty( $settings['icon'] ) ) {
						?>
						<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
					<?php } ?>

				</span>
			<?php } ?>
		<?php } elseif ( ! empty( $settings['icon'] ) ) { ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align' ) ); ?>>
				<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
			</span>
			<?php
		}
	}

	/**
	 * Render Marketing Button widget text.
	 *
	 * @since 1.10.0
	 * @access protected
	 */
	protected function render_button_text() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
		$this->add_render_attribute( 'content-wrapper', 'class', 'uael-buttons-icon-' . $settings['icon_align'] );

		$this->add_render_attribute( 'icon-align', 'class', 'elementor-align-icon-' . $settings['icon_align'] );
		$this->add_render_attribute( 'icon-align', 'class', 'elementor-button-icon' );

		$this->add_render_attribute( 'btn-text', 'class', 'elementor-button-text' );
		$this->add_render_attribute( 'btn-text', 'class', 'uael-marketing-button-title' );
		$this->add_render_attribute( 'btn-text', 'class', 'elementor-inline-editing' );

		?>
		<?php if ( 'all_left' === $settings['icon_align'] || 'all_right' === $settings['icon_align'] ) : ?>
			<?php $this->render_button_icon( $settings ); ?>
		<?php endif; ?>
		<span class="uael-marketing-buttons-wrap">
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-wrapper' ) ); ?>>
				<?php if ( 'left' === $settings['icon_align'] || 'right' === $settings['icon_align'] ) : ?>
					<?php $this->render_button_icon( $settings ); ?>
				<?php endif; ?>
				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'btn-text' ) ); ?> data-elementor-setting-key="text" data-elementor-inline-editing-toolbar="none"><?php echo wp_kses_post( $settings['text'] ); ?></span>
			</span>
			<?php if ( '' !== $settings['desc_text'] ) { ?>
				<span class="uael-marketing-button-desc elementor-inline-editing" data-elementor-setting-key="desc_text" data-elementor-inline-editing-toolbar="none"><?php echo wp_kses_post( $settings['desc_text'] ); ?></span>
			<?php } ?>
		</span>
		<?php
	}

	/**
	 * Render Marketing Buttons output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.10.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'uael-button-wrapper elementor-button-wrapper' );

		if ( ! empty( $settings['link']['url'] ) ) {

			$this->add_render_attribute( 'button', 'class', 'elementor-button-link' );

			$this->add_link_attributes( 'button', $settings['link'] );
		}

		if ( '' !== $settings['css_id'] ) {
			$this->add_render_attribute( 'button', 'id', $settings['css_id'] );
		}

		$this->add_render_attribute( 'button', 'class', 'elementor-button' );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
		}

		$this->add_render_attribute( 'button', 'class', 'uael-marketing-buttons-' . $settings['icon_align'] );

		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}
		?>
		<div class="uael-marketing-button">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
				<?php if ( 'yes' === $settings['blink_effect'] ) { ?>
					<span class="uael_btn__blink"></span>
				<?php	} ?>
					<?php $this->render_button_text(); ?>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Marketing Buttons widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		function render_icon() {
			icon_align = 'elementor-align-icon-' + settings.icon_align;

			var iconHTML = elementor.helpers.renderIcon( view, settings.new_icon, { 'aria-hidden': true }, 'i' , 'object' );

			var migrated = elementor.helpers.isIconMigrated( settings, 'new_icon' );
			#>
			<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
				<# if ( settings.icon || settings.new_icon ) { #>
					<span class="elementor-button-icon {{ icon_align }}">
						<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) {
						#>
							{{{ iconHTML.value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# } else { #>
							<i class="{{ settings.icon }}" aria-hidden="true"></i>
						<# } #>
					</span>
				<# } #>
			<?php } else { ?>
				<span class="elementor-button-icon {{ icon_align }}">
					<i class="{{ settings.icon }}" aria-hidden="true"></i>
				</span>
			<?php } ?>
		<# } #>
		<div class="uael-marketing-button">
			<#
			view.addRenderAttribute( 'wrapper', 'class', 'uael-button-wrapper elementor-button-wrapper' );
			var uael_mbutton_align = '';
			var new_icon_align = '';
			var icon_align = '';

			view.addRenderAttribute( 'button', 'class', 'elementor-button' );

			if ( '' != settings.link.url ) {
				view.addRenderAttribute( 'button', 'href', settings.link.url );
				view.addRenderAttribute( 'button', 'class', 'elementor-button-link' );
			}

			if ( '' != settings.size ) {
				view.addRenderAttribute( 'button', 'class', 'elementor-size-' + settings.size );
			}

			if ( '' !== settings.icon ) {
				uael_mbutton_align = 'uael-marketing-buttons-' + settings.icon_align;
				view.addRenderAttribute( 'button', 'class', uael_mbutton_align );
			}

			if ( settings.hover_animation ) {
				view.addRenderAttribute( 'button', 'class', 'elementor-animation-' + settings.hover_animation );
			}

			#>
			<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
				<a id="{{ settings.css_id }}" {{{ view.getRenderAttributeString( 'button' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
					<# if ( 'yes' === settings.blink_effect ) {
					#>
					<span class="uael_btn__blink"></span>
					<# } #>
					<# new_icon_align = ' uael-buttons-icon-' + settings.icon_align;

					if ( 'all_left' == settings.icon_align || 'all_right' == settings.icon_align ) {
						render_icon(); #>
					<# } #>
					<span class="uael-marketing-buttons-wrap">
						<span class="elementor-button-content-wrapper{{ new_icon_align }}">
							<# if ( 'left' == settings.icon_align || 'right' == settings.icon_align ) {
								render_icon(); #>
							<# } #>
							<span class="elementor-button-text elementor-inline-editing uael-marketing-button-title" data-elementor-setting-key="settings.buttons.text" data-elementor-inline-editing-toolbar="none">{{ settings.text }}</span>
						</span>
						<# if ( '' != settings.desc_text ) { #>
						<span class="uael-marketing-button-desc" data-elementor-setting-key="settings.buttons.desc_text" data-elementor-inline-editing-toolbar="none">{{ settings.desc_text }}</span>
						<# } #>
					</span>
				</a>
			</div>
		<?php
	}
}
