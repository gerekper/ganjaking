<?php
/**
 * Post Tiles widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Happy_Addons\Elementor\Controls\Group_Control_Foreground;
use Happy_Addons_Pro\Controls\Image_Selector;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use WP_Query;

defined('ABSPATH') || die();

class Post_Tiles extends Base {

	use Lazy_Query_Builder;

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Post Tiles', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-article';
	}

	public function get_keywords() {
		return ['post', 'posts', 'portfolio', 'grid', 'tiles', 'query', 'blog'];
	}

	protected static function get_tiles_layout_options() {
		$dir = HAPPY_ADDONS_PRO_ASSETS . 'imgs/tiles-preview/';
		$options = [];

		for ( $i = 1; $i <= 12; ++$i ) {
			$options[$i] = [
				'title' => sprintf( esc_attr__( 'Layout %s', 'happy-addons-pro' ), $i ),
				'url' => $dir . "tiles{$i}.svg",
			];
		}

		return $options;
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__layout_content_controls();
		$this->__query_content_controls();
	}

	protected function __layout_content_controls() {

		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'tiles_layout',
			[
				'label'       => __( 'Layout', 'happy-addons-pro' ),
				'label_block' => true,
				'type'        => Image_Selector::TYPE,
				'default'     => '1',
				'options'     => self::get_tiles_layout_options()
			]
		);

		$this->add_control(
			'_heading_meta',
			[
				'label' => __( 'Meta Data', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'active_meta',
			[
				'type' => Controls_Manager::SELECT2,
				'label' => __( 'Active', 'happy-addons-pro' ),
				'description' => __( 'Select to show and unselect to hide', 'happy-addons-pro' ),
				'label_block' => true,
				'multiple' => true,
				'default' => ['author', 'date'],
				'options' => [
					'author'   => __( 'Author', 'happy-addons-pro' ),
					'date'     => __( 'Date', 'happy-addons-pro' ),
					'comments' => __( 'Comments', 'happy-addons-pro' ),
				]
			]
		);

		$this->add_control(
			'meta_has_icon',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Icon', 'happy-addons-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Separator', 'happy-addons-pro' ),
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-meta span:not(:first-child):before' => 'content:"{{VALUE}}";',
				]
			]
		);

		$this->add_control(
			'_heading_excerpt_label',
			[
				'label' => __( 'Excerpt & Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'type' => Controls_Manager::NUMBER,
				'label' => __( 'Excerpt Length', 'happy-addons-pro' ),
				'default' => 15
			]
		);

		$this->add_control(
			'show_taxonomy_badge',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Badge', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->end_controls_section();
	}

	protected function __query_content_controls() {

		$this->start_controls_section(
			'_section_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_controls();

		$this->add_control(
			'query_id',
			[
				'label'   => __( 'Query ID', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [ 'active' => true ],
				'description' => __( 'Give your Query a custom unique id to allow server side filtering.', 'happy-addons-pro' ),
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__tiles_style_controls();
		$this->__content_style_controls();
		$this->__badge_style_controls();
		$this->__meta_style_controls();
		$this->__title_excerpt_style_controls();
	}

	protected function __tiles_style_controls() {

		$this->start_controls_section(
			'_section_style_tiles',
			[
				'label' => __( 'Tiles', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tiles_gap',
			[
				'label' => __( 'Tiles Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles' => '--tiles-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tiles_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tiles__tile',
			]
		);

		$this->add_control(
			'tiles_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tiles_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tiles__tile',
			]
		);

		$this->add_responsive_control(
			'tiles_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_style_overlay',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Overlay (On hover)', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tiles_overlay',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-tiles__tile:after',
			]
		);

		$this->end_controls_section();
	}

	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_style_content_wrap',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_bg',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-tiles__tile-content',
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-justify',
					]
				],
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-content' => '{{VALUE}}'
				],
				'selectors_dictionary' => [
					'left' => '
						-webkit-box-align: start;
						-ms-flex-align: start;
						align-items: flex-start;
						-webkit-box-pack: start;
						-ms-flex-pack: start;
						justify-content: flex-start;
						text-align: left;',
					'center' => '
						-webkit-box-align: center;
						-ms-flex-align: center;
						align-items: center;
						-webkit-box-pack: center;
      					-ms-flex-pack: center;
          				justify-content: center;
						text-align: center;',
					'right' => '
						-webkit-box-align: end;
						-ms-flex-align: end;
						align-items: flex-end;
						-webkit-box-pack: end;
      					-ms-flex-pack: end;
         				justify-content: flex-end;
						text-align: right;',
					'justify' => 'text-align: justify;',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __badge_style_controls() {

		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_position_toggle',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_position_x',
			[
				'label' => __( 'Position Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'condition' => [
					'badge_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-tag' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_position_y',
			[
				'label' => __( 'Position Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'badge_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-tag' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_popover();

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-tag' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-tag' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-tiles__tile-tag',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-tiles__tile-tag',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					'line_height',
					'font_size',
				],
				'selector' => '{{WRAPPER}} .ha-tiles__tile-tag',
			]
		);

		$this->end_controls_section();
	}

	protected function __meta_style_controls() {

		$this->start_controls_section(
			'_section_style_meta',
			[
				'label' => __( 'Meta Data', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'meta_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					// 'line_height',
					// 'font_size',
				],
				'selector' => '{{WRAPPER}} .ha-tiles__tile-meta',
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-meta' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-tiles__tile-meta svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __title_excerpt_style_controls() {

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Title & Excerpt', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'title_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					// 'line_height',
					'font_size',
				],
				'selector' => '{{WRAPPER}} .ha-tiles__tile-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __( 'Text Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tiles__tile-title',
			]
		);

		$this->start_controls_tabs( '_tabs_title_stat' );

		$this->start_controls_tab(
			'_tab_title_stat_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'title_color',
				'selector' => '{{WRAPPER}} .ha-tiles__tile-title a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_title_stat_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'title_color_hover',
				'selector' => '{{WRAPPER}} .ha-tiles__tile-title a:hover, {{WRAPPER}} .ha-tiles__tile-title a:focus',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_control(
			'_heading_excerpt',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Excerpt', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'excerpt_bottom_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'exclude' => [
					// 'line_height',
					// 'font_size',
				],
				'selector' => '{{WRAPPER}} .ha-tiles__tile-excerpt',
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tiles__tile-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}


	protected static function get_tiles_grid_by_layout( $layout = 1 ) {
		$grid = [
			1 => ['lg', 'sm', 'sm', 'sm', 'sm'],
			2 => ['lg', 'md', 'sm', 'sm'],
			3 => ['lg', 'sm', 'md', 'sm'],
			4 => ['lg', 'md', 'sm', 'sm'],
			5 => ['lg', 'sm', 'sm', 'md'],
			6 => ['lg', 'md', 'md'],
			7 => ['md', 'md', 'md', 'md'],
			8 => ['sm', 'lg', 'sm', 'sm', 'sm'],
			9 => ['md', 'sm', 'md', 'md', 'sm'],
			10 => ['sm', 'lg', 'sm', 'sm', 'sm'],
			11 => ['lg', 'sm', 'sm', 'sm', 'sm'],
			12 => ['sm', 'md', 'sm', 'md', 'md'],
		];

		return ( isset( $grid[ $layout ] ) ? $grid[ $layout ] : [] );
	}

	protected static function get_tiles_column_by_row( $layout = 1 ) {
		if ( in_array( $layout, [1,2,3,4,5,6,7,8,9] ) ) {
			return '4by2';
		}

		if ( in_array( $layout, [10,11] ) ) {
			return '4by5';
		}

		if ( in_array( $layout, [12] ) ) {
			return '7by2';
		}
	}

	protected static function get_number_of_tiles( $layout = 1 ) {
		if ( in_array( $layout, [1,8,9,10,11,12] ) ) {
			return 5;
		}

		if ( in_array( $layout, [2,3,4,5,7] ) ) {
			return 4;
		}

		if ( in_array( $layout, [6,7,8,9] ) ) {
			return 3;
		}
	}

	protected static function render_meta( $fields = [], $has_icon = false ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}
		?>
		<div class="ha-tiles__tile-meta">
		<?php
			if ( in_array( 'author', $fields ) ) {
				self::render_author( $has_icon );
			}

			if ( in_array( 'date', $fields ) ) {
				self::render_date( $has_icon );
			}

			if ( in_array( 'comments', $fields ) ) {
				self::render_comments( $has_icon );
			}
		?>
		</div>
		<?php
	}

	protected static function render_author( $has_icon = false ) {
		?>
		<span class="ha-tiles__tile-author">
			<?php if ( $has_icon ) : ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M30 26.4V29c0 1.7-1.3 3-3 3H5c-1.7 0-3-1.3-3-3v-2.6c0-4.6 3.8-8.4 8.4-8.4h1c1.4 0.6 2.9 1 4.6 1s3.2-0.4 4.6-1h1C26.2 18 30 21.8 30 26.4zM8 8c0-4.4 3.6-8 8-8s8 3.6 8 8 -3.6 8-8 8S8 12.4 8 8z"/></svg>
			<?php endif; ?>
			<?php the_author(); ?>
		</span>
		<?php
	}

	protected static function render_date( $has_icon = false ) {
		?>
		<span class="ha-tiles__tile-date">
			<?php if ( $has_icon ) : ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M32 16c0 8.8-7.2 16-16 16S0 24.8 0 16 7.2 0 16 0 32 7.2 32 16zM22.2 19.5c0-0.3-0.2-0.6-0.4-0.8L18.1 16V6.7c0-0.6-0.5-1-1-1H15c-0.6 0-1 0.5-1 1v10 0c0 0.7 0.4 1.6 1 2l4.3 3.2c0.2 0.1 0.4 0.2 0.6 0.2 0.3 0 0.6-0.2 0.8-0.4l1.3-1.6C22.1 20 22.2 19.7 22.2 19.5z"/></svg>
			<?php endif; ?>
			<?php the_time( get_option( 'date_format' ) ); ?>
		</span>
		<?php
	}

	protected static function render_comments( $has_icon = false ) {
		?>
		<span class="ha-tiles__tile-comment">
			<?php if ( $has_icon ) : ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M32 4v18c0 2.2-1.8 4-4 4h-9l-7.8 5.9c-0.5 0.4-1.2 0-1.2-0.6V26H4c-2.2 0-4-1.8-4-4V4c0-2.2 1.8-4 4-4h24C30.2 0 32 1.8 32 4z"/></svg>
			<?php endif; ?>
			<?php comments_number(); ?>
		</span>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout = $settings['tiles_layout'];
		$excerpt_length = (int) $settings['excerpt_length'];
		$has_icon = (bool) $settings['meta_has_icon'];
		$active_meta = isset( $settings['active_meta'] ) ? $settings['active_meta'] : [];
		$grid = self::get_tiles_grid_by_layout( $layout );

		$this->add_render_attribute(
			'tiles-grid',
			'class',
			[
				'ha-tiles',
				'ha-tiles--' . $layout,
				'ha-tiles--' . self::get_tiles_column_by_row( $layout ),
			]
		);

		$args = $this->get_query_args();

		$args['posts_per_page'] = self::get_number_of_tiles( $layout );

		//define ha tiles post custom query filter hook
		if ( !empty( $settings['query_id'] ) ) {
			$args = apply_filters( "happyaddons/tiles-post/{$settings['query_id']}", $args );
		}

		$_query = new WP_Query( $args );

		if ( $_query->have_posts() ) : ?>

			<div <?php $this->print_render_attribute_string( 'tiles-grid' ); ?>>

				<?php while ( $_query->have_posts() ) :
					$_query->the_post();

					$_key = 'post-tile-' . $_query->current_post;
					$_grid_tile_size = ( isset( $grid[ $_query->current_post ] ) ? $grid[ $_query->current_post ] : 'sm' );
					$this->add_render_attribute(
						$_key,
						'class',
						[
							'ha-tiles__tile',
							'ha-tiles__tile--' . $_grid_tile_size,
						]
					);

					?>
					<article <?php $this->print_render_attribute_string( $_key ); ?>>
						<div class="ha-tiles__tile-content">
							<?php self::render_meta( $active_meta, $has_icon ); ?>
							<<?php echo ha_escape_tags( $settings['title_tag'], 'h2' ).' class="ha-tiles__tile-title"';?>><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></<?php echo ha_escape_tags( $settings['title_tag'], 'h2' );?>>
							<p class="ha-tiles__tile-excerpt"><?php echo ha_pro_get_excerpt( null, $excerpt_length ); ?></p>
						</div>

						<?php

						if ( ! empty( $settings['show_taxonomy_badge'] ) && $settings['show_taxonomy_badge'] === 'yes' ) {
							hapro_the_first_category();
						}

						the_post_thumbnail( 'large', [ 'class' => 'ha-tiles__tile-img' ] );

						?>
					</article>

				<?php endwhile; ?>

			</div>

			<?php

			wp_reset_postdata();
		endif;
	}
}
