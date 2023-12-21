<?php
/**
 * Justified gallery widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Justified_Gallery extends Base {

	/**
	 * Default filter is the global filter
	 * and can be overriden from settings
	 *
	 * @var string
	 */
	protected $_default_filter = '*';

	public function get_title() {
		return __( 'Justified Grid', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/justified-grid/';
	}

	public function get_icon() {
		return 'hm hm-brick-wall';
	}

	public function get_keywords() {
		return [ 'gallery', 'image', 'justified', 'filter', 'lightbox' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__gallery_content_controls();
		$this->__advance_content_controls();
	}

	protected function __gallery_content_controls() {

		$this->start_controls_section(
			'_section_gallery',
			[
				'label' => __( 'Gallery', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'filter',
			[
				'label' => __( 'Filter Name', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Type gallery filter name', 'happy-elementor-addons' ),
				'description' => __( 'Filter name will be used in filter menu.', 'happy-elementor-addons' ),
				'default' => __( 'Filter Name', 'happy-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'is_default_filter',
			[
				'label' => __( 'Is Default Filter?', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description' => __( 'Set this as default active filter. Make sure filter menu is active and visible. Last active will get priority.', 'happy-elementor-addons' ),
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'images',
			[
				'type' => Controls_Manager::GALLERY,
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'gallery',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'show_label' => false,
				/** translators: 1: Filter name */
				'title_field' => sprintf( __( 'Filter Group: %1$s', 'happy-elementor-addons' ), '{{filter}}' ),
				'default' => [
					[
						'filter' => __( 'Happy', 'happy-elementor-addons' ),
					]
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'medium_large',
				'separator' => 'before',
				'exclude' => [
					'custom'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __advance_content_controls() {

		$this->start_controls_section(
			'_section_advance',
			[
				'label' => __( 'Advance', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_filter',
			[
				'label' => __( 'Show Filter Menu', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-elementor-addons' ),
				'label_off' => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'description' => __( 'Enable to display filter menu.', 'happy-elementor-addons' ),
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_all_filter',
			[
				'label' => __( 'Show "All" Filter', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => __( 'Enable to display "All" filter in filter menu.', 'happy-elementor-addons' ),
				'condition' => [
					'show_filter' => 'yes'
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'all_filter_label',
			[
				'label' => __( 'Filter Label', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'All', 'happy-elementor-addons' ),
				'placeholder' => __( 'Type filter label', 'happy-elementor-addons' ),
				'description' => __( 'Type "All" filter label.', 'happy-elementor-addons' ),
				'condition' => [
					'show_all_filter' => 'yes',
					'show_filter' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'show_caption',
			[
				'label' => __( 'Show Caption', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator' => 'before',
				'description' => __( 'Make sure to add image caption.', 'happy-elementor-addons' ),
				'style_transfer' => true,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'row_height',
			[
				'label' => __( 'Height', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 150,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'max_row_height',
			[
				'label' => __( 'Max row height enable?', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-elementor-addons' ),
				'label_off' => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
				// 'description' => __( 'Enable to display filter menu.', 'happy-elementor-addons' ),
				'style_transfer' => true,
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'margins',
			[
				'label' => __( 'Margins', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__wrap' => '--justified-grid-pull: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'last_row',
			[
				'label' => __( 'Last Row', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'justify',
				'options' => [
					'nojustify' => __( 'No Justify', 'happy-elementor-addons' ),
					'justify' => __( 'Justify', 'happy-elementor-addons' ),
					'hide' => __( 'Hide', 'happy-elementor-addons' ),
				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'enable_popup',
			[
				'label' => __( 'Enable Lightbox?', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'return_value' => 'yes',
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'disable_lightbox_on_tablet',
			[
				'label' => __( 'Disable On Tablet', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'enable_popup' => 'yes',
				]
			]
		);

		$this->add_control(
			'disable_lightbox_on_mobile',
			[
				'label' => __( 'Disable On Mobile', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => [
					'enable_popup' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'popup_image',
				'default' => 'large',
				'exclude' => [
					'custom'
				],
				'condition' => [
					'enable_popup' => 'yes',
				]
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__image_style_controls();
		$this->__caption_style_controls();
		$this->__filter_menu_style_controls();
	}

	protected function __image_style_controls() {

		$this->start_controls_section(
			'_section_style_image',
			[
				'label' => __( 'Image', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__item' => 'background-color: {{VALUE}};'
				 ]
			]
		);

		$this->start_controls_tabs(
			'_tabs_image_effects',
			[
				'separator' => 'before'
			]
		);

		$this->start_controls_tab(
			'_tab_image_effects_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => __( 'Opacity', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__item img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_filters',
				'selector' => '{{WRAPPER}} .ha-justified-grid__item img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => __( 'Opacity', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__item:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css_filters_hover',
				'selector' => '{{WRAPPER}} .ha-justified-grid__item:hover img',
			]
		);

		$this->add_control(
			'image_background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__item img' => 'transition-duration: {{SIZE}}s;',
				],
			]
		);

		$this->add_control(
			'image_hover_animation',
			[
				'label' => __( 'Hover Animation', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'default' => 'grow',
				'label_block' => true,
			]
		);

		$this->add_control(
			'image_hover_cursor',
			[
				'label' => __( 'Hover Cursor', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => ha_get_css_cursors(),
				'default' => 'default',
				'selectors' => [
					'{{WRAPPER}} .ha-justified-grid__item:hover img' => 'cursor: {{VALUE}};'
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function __caption_style_controls() {

		$this->start_controls_section(
			'_section_style_caption',
			[
				'label' => __( 'Caption', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .justified-gallery > .ha-justified-grid__item > .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'caption_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .justified-gallery > .ha-justified-grid__item > .caption' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .justified-gallery > .ha-justified-grid__item > .caption' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .justified-gallery > .ha-justified-grid__item > .caption',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __filter_menu_style_controls() {

		$this->start_controls_section(
			'_section_style_menu',
			[
				'label' => __( 'Filter Menu', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_menu',
			[
				'label' => __( 'Menu', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'menu_margin',
			[
				'label' => __( 'Margin', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_buttons',
			[
				'label' => __( 'Filter Buttons', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label' => __( 'Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .ha-filter__item'
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-filter__item'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .ha-filter__item',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label' => __( 'Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'desktop_default' => 'left',
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .ha-filter' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->start_controls_tabs( '_tabs_style_button' );

		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item:hover, {{WRAPPER}} .ha-filter__item:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item:hover, {{WRAPPER}} .ha-filter__item:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item:hover, {{WRAPPER}} .ha-filter__item:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_active',
			[
				'label' => __( 'Active', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_active_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item--active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_active_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item--active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_active_border_color',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-filter__item--active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function get_gallery_data() {
		$gallery = $this->get_settings_for_display( 'gallery' );

		if ( ! is_array( $gallery ) || empty( $gallery ) ) {
			return [];
		}

		$menu = [];
		$items = [];

		foreach ( $gallery as $key => $item ) {
			if ( empty( $item['images'] ) ) {
				continue;
			}

			$images = $item['images'];
			$filter = '__fltr-' . ( $key + 1 );

			if ( ! empty( $item['is_default_filter'] ) ) {
				$this->_default_filter = '.' . $filter;
			}

			if ( $filter && ! isset( $data[ $filter ] ) ) {
				$menu[ $filter ] = $item['filter'];
			}

			foreach ( $images as $image ) {
				if ( ! isset( $items[ $image['id'] ] ) ) {
					$items[ $image['id'] ] = $filter;
				} else {
					$items[ $image['id'] ] .= ' ' . $filter;
				}
			}
		}

		return compact( 'menu', 'items' );
	}

	protected function image_missing_alert() {
		if( ha_elementor()->editor ){
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'Please select an image first to render the grid properly', 'happy-elementor-addons' )
			);
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$gallery = $this->get_gallery_data();

		if ( empty( $gallery ) ) {
			return;
		}

		if ( count( $gallery['items'] ) <= 0 ) {
			$this->image_missing_alert();
			return;
		}

		$this->add_render_attribute( 'grid_wrap', 'class', [
			'ha-justified-grid__wrap',
			'hajs-justified-grid',
		] );

		$has_popup = $settings['enable_popup'];
		$item_html_tag = 'div';

		if ( $has_popup ) {
			$item_html_tag = 'a';
		}

		if ( $settings['show_filter'] === 'yes' ) : ?>
			<div class="ha-filter hajs-filter" data-default-filter="<?php echo $this->_default_filter; ?>" role="navigation" aria-label="<?php echo esc_attr_x( 'Gallery filter', 'Gallery filter aria label', 'happy-elementor-addons' ); ?>">
				<?php if ( $settings['show_all_filter'] ) : ?>
					<button class="ha-filter__item" type="button" data-filter="*"><?php echo esc_html( $settings['all_filter_label'] ); ?></button></li>
				<?php endif; ?>
				<?php foreach ( $gallery['menu'] as $key => $val ) : ?>
					<button class="ha-filter__item" type="button" data-filter=".<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $val ); ?></button></li>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div <?php echo $this->get_render_attribute_string( 'grid_wrap' ); ?>>
			<?php foreach ( $gallery['items'] as $id => $filter_str ) :
				$caption = $settings['show_caption'] ? esc_attr( wp_get_attachment_caption( $id ) )  : '';
				$popup = $has_popup ? sprintf( 'href="#" data-mfp-src="%s"', esc_url( wp_get_attachment_image_url( $id, $settings['popup_image_size'] ) ) ) : '';
				?>
				<<?php echo $item_html_tag; ?> <?php echo $popup; ?> class="ha-justified-grid__item ha-js-lightbox <?php echo esc_attr( $filter_str ); ?>" title="<?php echo $caption; ?>">
					<?php echo wp_get_attachment_image( $id, $settings['thumbnail_size'], false, ['class' => 'elementor-animation-' . esc_attr( $settings['image_hover_animation'] ) ] ); ?>
				</<?php echo $item_html_tag; ?>>
			<?php endforeach; ?>
		</div>

		<?php
	}
}
