<?php
/**
 * UAEL Before After Slider.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BaSlider\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Before After.
 */
class BaSlider extends Common_Widget {

	/**
	 * Retrieve Before After Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'BaSlider' );
	}

	/**
	 * Retrieve Before After Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'BaSlider' );
	}

	/**
	 * Retrieve Before After Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'BaSlider' );
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
		return parent::get_widget_keywords( 'BaSlider' );
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script', 'uael-twenty-twenty', 'uael-move', 'imagesloaded' );
	}

	/**
	 * Register Before After controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_general_content_controls();
		$this->register_helpful_information();
	}
	/**
	 * Register Before After General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {

		$this->start_controls_section(
			'section_before',
			array(
				'label' => __( 'Before', 'uael' ),
			)
		);

		$this->add_control(
			'before_src',
			array(
				'label'       => __( 'Before Image Source', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'media',
				'label_block' => true,
				'options'     => array(
					'media' => __( 'Media', 'uael' ),
					'url'   => __( 'URL', 'uael' ),
				),
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'     => __( 'Before Photo', 'uael' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'before_src' => 'media',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'before_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `before_image_size` and `before_image_custom_dimension` phpcs:ignore Squiz.PHP.CommentedOutCode.Found.
				'default'   => 'large',
				'separator' => 'none',
				'condition' => array(
					'before_src' => 'media',
				),
			)
		);

		$this->add_control(
			'before_img_url',
			array(
				'label'       => __( 'Before Photo URL', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => array(
					'before_src' => 'url',
				),
			)
		);

		$this->add_control(
			'before_text',
			array(
				'label'     => __( 'Before Label', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'selector'  => '{{WRAPPER}} .uael-infobox-title-prefix',
				'default'   => __( 'Before', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'selectors' => array(
					'{{WRAPPER}} .twentytwenty-before-label:before' => 'content: "{{VALUE}}";',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_after',
			array(
				'label' => __( 'After', 'uael' ),
			)
		);

		$this->add_control(
			'after_src',
			array(
				'label'       => __( 'After Image Source', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'media',
				'label_block' => true,
				'options'     => array(
					'media' => __( 'Media', 'uael' ),
					'url'   => __( 'URL', 'uael' ),
				),
			)
		);

		$this->add_control(
			'after_image',
			array(
				'label'     => __( 'After Photo', 'uael' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'after_src' => 'media',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'after_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `after_image_size` and `after_image_custom_dimension`. phpcs:ignore Squiz.PHP.CommentedOutCode.Found.
				'default'   => 'large',
				'separator' => 'none',
				'condition' => array(
					'after_src' => 'media',
				),
			)
		);

		$this->add_control(
			'after_img_url',
			array(
				'label'       => __( 'After Photo URL', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => array(
					'after_src' => 'url',
				),
			)
		);

		$this->add_control(
			'after_text',
			array(
				'label'     => __( 'After Label', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'selector'  => '{{WRAPPER}} .uael-infobox-title-prefix',
				'default'   => __( 'After', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'selectors' => array(
					'{{WRAPPER}} .twentytwenty-after-label:before' => 'content: "{{VALUE}}";',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Orientation', 'uael' ),
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'   => __( 'Before After Slider Orientation', 'uael' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'vertical'   => array(
						'title' => __( 'Vertical', 'uael' ),
						'icon'  => 'eicon-section',
					),
					'horizontal' => array(
						'title' => __( 'Horizontal', 'uael' ),
						'icon'  => 'fa fa-columns',
					),
				),
				'default' => 'horizontal',
				'toggle'  => false,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => __( 'Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'-right' => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					' '      => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'-left'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => '-right',
				'selectors' => array(
					'{{WRAPPER}}' => 'margin{{VALUE}}:auto;',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'move_on_hover',
			array(
				'label'        => __( 'Move on Hover', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.5)',
				'selectors' => array(
					'{{WRAPPER}} .twentytwenty-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_handle',
			array(
				'label' => __( 'Comparison Handle', 'uael' ),
			)
		);

		$this->add_control(
			'initial_offset',
			array(
				'label'       => __( 'Handle Initial Offset', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'default'     => array(
					'size' => 50,
				),
				'range'       => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'label_block' => true,
				'options'     => array(
					'0.0' => __( '0.0', 'uael' ),
					'0.1' => __( '0.1', 'uael' ),
					'0.2' => __( '0.2', 'uael' ),
					'0.3' => __( '0.3', 'uael' ),
					'0.4' => __( '0.4', 'uael' ),
					'0.5' => __( '0.5', 'uael' ),
					'0.6' => __( '0.6', 'uael' ),
					'0.7' => __( '0.7', 'uael' ),
					'0.8' => __( '0.8', 'uael' ),
					'0.9' => __( '0.9', 'uael' ),
				),
			)
		);

		$this->add_control(
			'handle_color',
			array(
				'label'     => __( 'Handle Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .twentytwenty-handle' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .twentytwenty-handle::before' => 'background:  {{VALUE}};',
					'{{WRAPPER}} .twentytwenty-handle::after' => 'background: {{VALUE}};',
					'body:not(.rtl) {{WRAPPER}} .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-color:  {{VALUE}};',
					'body:not(.rtl) {{WRAPPER}} .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}};',
					'.rtl {{WRAPPER}} .twentytwenty-handle .twentytwenty-right-arrow' => 'border-right-color: {{VALUE}};',
					'.rtl {{WRAPPER}} .twentytwenty-handle .twentytwenty-left-arrow' => 'border-left-color:  {{VALUE}};',
					'{{WRAPPER}} .twentytwenty-handle .twentytwenty-up-arrow' => 'border-bottom-color:  {{VALUE}};',
					'{{WRAPPER}} .twentytwenty-handle .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thickness',
			array(
				'label'      => __( 'Handle Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 5,
				),
				'range'      => array(
					'px' => array(
						'max' => 15,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle::before' => 'width: {{SIZE}}{{UNIT}}; margin-left:calc( -{{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle::after' => 'width: {{SIZE}}{{UNIT}}; margin-left:calc( -{{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .twentytwenty-handle' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle::before' => 'height: {{SIZE}}{{UNIT}}; margin-top:calc( -{{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle::after' => 'height: {{SIZE}}{{UNIT}}; margin-top:calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->add_control(
			'circle_width',
			array(
				'label'      => __( 'Circle Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'max' => 150,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .twentytwenty-handle' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}; margin-left:calc( -{{SIZE}}{{UNIT}}/2 - {{thickness.size}}{{thickness.unit}} ); margin-top:calc( -{{SIZE}}{{UNIT}}/2 - {{thickness.size}}{{thickness.unit}} );',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before' => 'margin-bottom: calc( ( {{SIZE}}{{UNIT}} + ( {{thickness.size}}{{thickness.unit}} * 2 ) ) / 2 );',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after' => 'margin-top: calc( ( {{SIZE}}{{UNIT}} + ( {{thickness.size}}{{thickness.unit}} * 2 ) ) / 2 );',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before' => 'margin-left: calc( ( {{SIZE}}{{UNIT}} + ( {{thickness.size}}{{thickness.unit}} * 2 ) ) / 2 );',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'margin-right: calc( ( {{SIZE}}{{UNIT}} + ( {{thickness.size}}{{thickness.unit}} * 2 ) ) / 2 );',
				),
			)
		);

		$this->add_control(
			'circle_radius',
			array(
				'label'      => __( 'Circle Radius', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'range'      => array(
					'%' => array(
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .twentytwenty-handle' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'triangle_size',
			array(
				'label'      => __( 'Triangle Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 6,
				),
				'range'      => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors'  => array(
					'body:not(.rtl) {{WRAPPER}} .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .twentytwenty-handle .twentytwenty-left-arrow' => 'border-left-width: {{SIZE}}{{UNIT}};',
					'body:not(.rtl) {{WRAPPER}} .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-width: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .twentytwenty-handle .twentytwenty-right-arrow' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-left-arrow, {{WRAPPER}} .twentytwenty-right-arrow, {{WRAPPER}} .twentytwenty-up-arrow, {{WRAPPER}} .twentytwenty-down-arrow' => 'border-width: {{SIZE}}{{UNIT}};',
					'body:not(.rtl) {{WRAPPER}} .twentytwenty-handle .twentytwenty-left-arrow' => 'margin-right: calc({{SIZE}}{{UNIT}}/2);',
					'.rtl {{WRAPPER}} .twentytwenty-handle .twentytwenty-left-arrow' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'body:not(.rtl) {{WRAPPER}} .twentytwenty-handle .twentytwenty-right-arrow' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'.rtl {{WRAPPER}} .twentytwenty-handle .twentytwenty-right-arrow' => 'margin-right: calc({{SIZE}}{{UNIT}}/2);',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Before/After Label', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'typography',
			array(
				'label' => __( 'Before/After Label', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'show_on',
			array(
				'label'        => __( 'Show Label On', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'hover',
				'label_block'  => true,
				'options'      => array(
					'hover'  => __( 'Hover Only', 'uael' ),
					'normal' => __( 'Normal Only', 'uael' ),
					'both'   => __( 'Hover & Normal', 'uael' ),
				),
				'prefix_class' => 'uael-ba-label-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'vertical_alignment',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'flex-start' => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'      => 'flex-start',
				'selectors'    => array(
					'{{WRAPPER}} .twentytwenty-before-label, {{WRAPPER}} .twentytwenty-after-label' => 'justify-content: {{VALUE}};',
				),
				'toggle'       => false,
				'condition'    => array(
					'orientation' => 'vertical',
				),
				'prefix_class' => 'uael%s-ba-valign-',
			)
		);

		$this->add_responsive_control(
			'horizontal_alignment',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'flex-start' => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-arrows-v',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'uael' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'      => 'flex-start',
				'selectors'    => array(
					'{{WRAPPER}} .twentytwenty-before-label, {{WRAPPER}} .twentytwenty-after-label' => 'align-items: {{VALUE}};',
				),
				'prefix_class' => 'uael%s-ba-halign-',
				'toggle'       => false,
				'condition'    => array(
					'orientation' => 'horizontal',
				),
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

		$help_link_2 = UAEL_DOMAIN . 'docs/before-after-slider-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=7m6FD8Yk3N0&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc&index=3" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render the Image URL as per source
	 *
	 * @param string $position The before/after position.
	 * @since 0.0.1
	 */
	protected function get_image_src( $position ) {
		if ( '' === $position ) {
			return;
		}

		$url      = '';
		$settings = $this->get_settings_for_display();

		if ( 'media' === $settings[ $position . '_src' ] ) {

			if ( '' !== $settings[ $position . '_image' ]['id'] ) {

				$url = Group_Control_Image_Size::get_attachment_image_src( $settings[ $position . '_image' ]['id'], $position . '_image', $settings );
			} else {
				$url = $settings[ $position . '_image' ]['url'];
			}
		} else {

			$url = $settings[ $position . '_img_url' ];
		}

		return $url;
	}

	/**
	 * Render Before After output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();
		$node_id  = $this->get_id();
		ob_start();
		$before_img = $this->get_image_src( 'before' );
		$after_img  = $this->get_image_src( 'after' );
		?>
		<div class="uael-before-after-slider">
			<div class="uael-ba-container" data-move-on-hover="<?php echo esc_attr( $settings['move_on_hover'] ); ?>" data-orientation="<?php echo esc_attr( $settings['orientation'] ); ?>" data-offset="<?php echo esc_attr( ( $settings['initial_offset']['size'] / 100 ) ); ?>">
				<img class="uael-before-img" style="position: absolute;" src="<?php echo esc_url( $before_img ); ?>" alt="<?php echo esc_attr( $settings['before_text'] ); ?>"/>
				<img class="uael-after-img" src="<?php echo esc_url( $after_img ); ?>" alt="<?php echo esc_attr( $settings['after_text'] ); ?>"/>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render Before After Slider widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		var before_img = '';
		var after_img = '';

		if( 'media' == settings.before_src ) {

			var before_image = {
				id: settings.before_image.id,
				url: settings.before_image.url,
				size: settings.before_image_size,
				dimension: settings.before_image_custom_dimension,
				model: view.getEditModel()
			};
			before_img = elementor.imagesManager.getImageUrl( before_image );
		} else {
			before_img = settings.before_img_url;
		}

		if( 'media' == settings.after_src ) {
			var after_image = {
				id: settings.after_image.id,
				url: settings.after_image.url,
				size: settings.after_image_size,
				dimension: settings.after_image_custom_dimension,
				model: view.getEditModel()
			};
			after_img = elementor.imagesManager.getImageUrl( after_image );
		} else {
			after_img = settings.after_img_url;
		}

		if ( ! before_img || ! after_img ) {
			return;
		}

		#>
		<div class="uael-before-after-slider">
			<div class="uael-ba-container" data-move-on-hover="{{settings.move_on_hover}}" data-orientation="{{settings.orientation}}" data-offset="{{settings.initial_offset.size/100}}">
				<img class="uael-before-img" style="position: absolute;" src="{{before_img}}" alt="{{settings.before_text}}"/>
				<img class="uael-after-img" src="{{after_img}}" alt="{{settings.after_text}}"/>
			</div>
		</div>
		<# elementorFrontend.hooks.doAction( 'frontend/element_ready/uael-ba-slider.default' ); #>
		<?php
	}

}

