<?php
/**
 * UAEL RetinaImage.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\RetinaImage\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Plugin;


// UltimateElementor Classes.
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Retina Image widget.
 *
 * Widget that displays an Retina image into the retina devices.
 *
 * @since 1.17.0
 */
class Retina_Image extends Common_Widget {

	/**
	 * Retina Image class var.
	 *
	 * @var $settings array.
	 */
	public $settings = array();

	/**
	 * Retrieve RetinaImage Widget name.
	 *
	 * @since 1.17.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Retina_Image' );
	}

	/**
	 * Retrieve RetinaImage Widget title.
	 *
	 * @since 1.17.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Retina_Image' );
	}

	/**
	 * Retrieve RetinaImage Widget icon.
	 *
	 * @since 1.17.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Retina_Image' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.17.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Retina_Image' );
	}

	/**
	 * Register RetinaImage controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_content_retina_image_controls();
		$this->register_retina_image_styling_controls();
		$this->register_retina_caption_styling_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register RetinaImage Controls.
	 *
	 * @since 1.17.0
	 * @access protected
	 */
	protected function register_content_retina_image_controls() {
		$this->start_controls_section(
			'section_retina_image',
			array(
				'label' => __( 'Retina Image', 'uael' ),
			)
		);
		$this->add_control(
			'retina_image',
			array(
				'label'   => __( 'Choose Default Image', 'uael' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->add_control(
			'real_retina',
			array(
				'label'   => __( 'Choose Retina Image', 'uael' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'retina_image',
				'label'   => __( 'Image Size', 'uael' ),
				'default' => 'medium',
			)
		);
		$this->add_responsive_control(
			'align',
			array(
				'label'              => __( 'Alignment', 'uael' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
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
				'default'            => 'center',
				'selectors'          => array(
					'{{WRAPPER}} .uael-retina-image-container, {{WRAPPER}} .uael-caption-width' => 'text-align: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'caption_source',
			array(
				'label'   => __( 'Caption', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'   => __( 'None', 'uael' ),
					'custom' => __( 'Custom Caption', 'uael' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'caption',
			array(
				'label'       => __( 'Custom Caption', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Enter your image caption', 'uael' ),
				'condition'   => array(
					'caption_source' => 'custom',
				),
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label'   => __( 'Link', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'uael' ),
					'custom' => __( 'Custom URL', 'uael' ),
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'uael' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'uael' ),
				'condition'   => array(
					'link_to' => 'custom',
				),
				'show_label'  => false,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Retina Image Style Controls.
	 *
	 * @since 1.17.0
	 * @access protected
	 */
	protected function register_retina_image_styling_controls() {
		$this->start_controls_section(
			'section_style_retina_image',
			array(
				'label' => __( 'Retina Image', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label'              => __( 'Width', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'unit' => '%',
				),
				'tablet_default'     => array(
					'unit' => '%',
				),
				'mobile_default'     => array(
					'unit' => '%',
				),
				'size_units'         => array( '%', 'px', 'vw' ),
				'range'              => array(
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-retina-image img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-retina-image .wp-caption .widget-image-caption' => 'width: {{SIZE}}{{UNIT}}; display: inline-block;',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'space',
			array(
				'label'              => __( 'Max Width', 'uael' ) . ' (%)',
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'unit' => '%',
				),
				'tablet_default'     => array(
					'unit' => '%',
				),
				'mobile_default'     => array(
					'unit' => '%',
				),
				'size_units'         => array( '%' ),
				'range'              => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-retina-image img' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wp-caption-text'       => 'max-width: {{SIZE}}{{UNIT}}; display: inline-block; width: 100%;',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'separator_panel_style',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'retina_image_border',
			array(
				'label'       => __( 'Border Style', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'label_block' => false,
				'options'     => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-retina-image-container .uael-retina-img' => 'border-style: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'retina_image_border_size',
			array(
				'label'      => __( 'Border Width', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'    => '1',
					'bottom' => '1',
					'left'   => '1',
					'right'  => '1',
					'unit'   => 'px',
				),
				'condition'  => array(
					'retina_image_border!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-retina-image-container .uael-retina-img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'retina_image_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'retina_image_border!' => 'none',
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-retina-image-container .uael-retina-img' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'              => __( 'Border Radius', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-retina-image-container .uael-retina-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_box_shadow',
				'exclude'  => array(
					'box_shadow_position',
				),
				'selector' => '{{WRAPPER}} .uael-retina-image img',
			)
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab(
			'normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'     => __( 'Opacity', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-retina-image img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .uael-retina-image img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);
		$this->add_control(
			'opacity_hover',
			array(
				'label'     => __( 'Opacity', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-retina-image:hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .uael-retina-image:hover img',
			)
		);

		$this->add_control(
			'hover_animation',
			array(
				'label' => __( 'Hover Animation', 'uael' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);
		$this->add_control(
			'background_hover_transition',
			array(
				'label'     => __( 'Transition Duration', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-retina-image img' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register Caption style Controls.
	 *
	 * @since 1.17.0
	 * @access protected
	 */
	protected function register_retina_caption_styling_controls() {

		$this->start_controls_section(
			'section_style_caption',
			array(
				'label'     => __( 'Caption', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'caption_source!' => 'none',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
			)
		);

		$this->add_control(
			'caption_background_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .widget-image-caption',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'caption_text_shadow',
				'selector' => '{{WRAPPER}} .widget-image-caption',
			)
		);

		$this->add_responsive_control(
			'caption_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .widget-image-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'caption_space',
			array(
				'label'              => __( 'Caption Top Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'            => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0px;',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.17.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/introducing-retina-image-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

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
	 * Check if the current widget has caption
	 *
	 * @access private
	 * @since 1.17.0
	 *
	 * @param array $settings returns settings.
	 *
	 * @return boolean
	 */
	private function has_caption( $settings ) {
		return ( ! empty( $settings['caption_source'] ) && 'none' !== $settings['caption_source'] );
	}

	/**
	 * Get the caption for current widget.
	 *
	 * @access private
	 * @since 1.17.0
	 * @param array $settings returns the caption.
	 *
	 * @return string
	 */
	private function get_caption( $settings ) {
		$caption = '';
		if ( ! empty( $settings['caption_source'] ) ) {

			if ( 'custom' === $settings['caption_source'] ) {
				$caption = ! empty( $settings['caption'] ) ? $settings['caption'] : '';
			}
		}
		return $caption;
	}

	/**
	 * Render Retina Image output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.17.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['retina_image']['url'] ) ) {
			return;
		}

		$has_caption = $this->has_caption( $settings );
		$this->add_render_attribute( 'wrapper', 'class', 'uael-retina-image' );
		$link = $this->get_link_url( $settings );

		if ( $link ) {

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$this->add_render_attribute(
					'link',
					array(
						'class' => 'elementor-clickable',
					)
				);
			}
			$this->add_link_attributes( 'link', $settings['link'] );

		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<?php if ( $has_caption ) : ?>
				<figure class="wp-caption">
			<?php endif; ?>
			<?php if ( $link ) : ?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
			<?php endif; ?>
			<?php
			$size = $settings['retina_image_size'];
			$demo = '';

			if ( 'custom' !== $size ) {
				$image_size = $size;
			} else {
				require_once ELEMENTOR_PATH . 'includes/libraries/bfi-thumb/bfi-thumb.php';

				$image_dimension = $settings['retina_image_custom_dimension'];

				$image_size = array(
					// Defaults sizes.
					0           => null, // Width.
					1           => null, // Height.

					'bfi_thumb' => true,
					'crop'      => true,
				);

				$has_custom_size = false;
				if ( ! empty( $image_dimension['width'] ) ) {
					$has_custom_size = true;
					$image_size[0]   = $image_dimension['width'];
				}

				if ( ! empty( $image_dimension['height'] ) ) {
					$has_custom_size = true;
					$image_size[1]   = $image_dimension['height'];
				}

				if ( ! $has_custom_size ) {
					$image_size = 'full';
				}
			}
			$retina_image_url = $settings['real_retina']['url'];

			$image_url = $settings['retina_image']['url'];

			$image_data = wp_get_attachment_image_src( $settings['retina_image']['id'], $image_size, true );

			$retina_data = wp_get_attachment_image_src( $settings['real_retina']['id'], $image_size, true );

			$retina_image_class = 'elementor-animation-';

			if ( ! empty( $settings['hover_animation'] ) ) {
				$demo = $settings['hover_animation'];
			}
			if ( ! empty( $image_data ) ) {
				$image_url = $image_data[0];
			}
			if ( ! empty( $retina_data ) ) {
				$retina_image_url = $retina_data[0];
			}
			$class_animation = $retina_image_class . $demo;

			$image_unset         = site_url() . '/wp-includes/images/media/default.png';
			$placeholder_img_url = Utils::get_placeholder_image_src();

			if ( $image_unset === $retina_image_url ) {
				if ( $image_unset !== $image_url ) {
					$retina_image_url = $image_url;
				} else {
					$retina_image_url = $placeholder_img_url;
				}
			}

			if ( $image_unset === $image_url ) {
				$image_url = $placeholder_img_url;
			}

			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'Chrome' ) !== false ) { // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__

				$date             = new \DateTime();
				$timestam         = $date->getTimestamp();
				$image_url        = $image_url . '?' . $timestam;
				$retina_image_url = $retina_image_url . '?' . $timestam;
			}

			?>
				<div class="uael-retina-image-set">
					<div class="uael-retina-image-container">
						<img class="uael-retina-img <?php echo esc_attr( $class_animation ); ?>" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( Control_Media::get_image_alt( $settings['retina_image'] ) ); ?>"srcset="<?php echo esc_url( $image_url ) . ' 1x,' . esc_url( $retina_image_url ) . ' 2x'; ?>"/>
					</div>
				</div>
			<?php if ( $link ) : ?>
					</a>
			<?php endif; ?>
			<?php if ( $has_caption ) : ?>
				<?php $retina_caption = $this->get_caption( $settings ); ?>
					<?php if ( ! empty( $retina_caption ) ) : ?>
						<div class="uael-caption-width">
							<figcaption class="widget-image-caption wp-caption-text"><?php echo wp_kses_post( $this->get_caption( $settings ) ); ?></figcaption>
						</div>
				<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Retrieve Retina image widget link URL.
	 *
	 * @since 1.17.0
	 * @access private
	 *
	 * @param array $settings returns settings.
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_link_url( $settings ) {
		if ( 'none' === $settings['link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_to'] ) {
			if ( empty( $settings['link']['url'] ) ) {
				return false;
			}
			return $settings['link'];
		}
	}
}
