<?php

namespace ElementPack\Modules\EddProductReviewCarousel\Widgets;


use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

use ElementPack\Traits\Global_Swiper_Controls;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class EDD_Product_Review_Carousel extends Module_Base {
	use Global_Swiper_Controls;
	public function get_name() {
		return 'bdt-edd-product-review-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Product Review Carousel', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-product-review-carousel bdt-new';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['easy', 'digital', 'review', 'edd', 'eshop', 'estore', 'carousel', 'download'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-edd-product-review-carousel'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-edd-product-review-carousel'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/z6MSJtvbxPQ';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_woocommerce_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);
		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options' => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
			]
		);
		$this->add_responsive_control(
			'item_gap',
			[
				'label' => esc_html__('Item Gap', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'tablet_default' => [
					'size' => 20,
				],
				'mobile_default' => [
					'size' => 20,
				],
			]
		);
		$this->add_responsive_control(
			'alignment',
			[
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HIDDEN,
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
				'separator' => 'after'
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before'
			]
		);
		$this->add_control(
			'show_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_author',
			[
				'label' => esc_html__('Author', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_rating',
			[
				'label' => esc_html__('Rating', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_review_text',
			[
				'label'         => esc_html__('Review Text', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_query',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'items_limit',
			[
				'label'         => __('Limit', 'ultimae-store-kit'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 20,
						'step'  => 1,
					]
				],
				'default'       => [
					'unit'      => 'px',
					'size'      => 6,
				]
			]
		);
		$this->add_control(
			'offset',
			[
				'label'        => __('Offset', 'bdthemes-element-pack'),
				'description' => __(' The number of comments to pass over in the query.', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::TEXT,
			]
		);
		$this->add_control(
			'orderby',
			[
				'label'   => __('Order By', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'comment_date',
				'options' => [
					'comment_author'   => __('Author', 'bdthemes-element-pack'),
					'comment_approved' => __('Approved', 'bdthemes-element-pack'),
					'comment_date'     => __('Date', 'bdthemes-element-pack'),
					'comment_content'  => __('Content', 'bdthemes-element-pack'),
					'none'             => __('Random', 'bdthemes-element-pack'),
				],
			]
		);
		$this->add_control(
			'review_order',
			[
				'label'      => esc_html__('Order', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'DESC',
				'options'    => [
					'ASC'  => esc_html__('Ascending', 'bdthemes-element-pack'),
					'DESC' => esc_html__('Descending', 'bdthemes-element-pack'),
				],
			]
		);
		$this->add_control(
			'status',
			[
				'label'   => __('Status', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'approve',
				'options' => [
					'approve' => __('Approve', 'bdthemes-element-pack'),
					'hold'    => __('Hold', 'bdthemes-element-pack'),
					'all'     => __('All', 'bdthemes-element-pack'),
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__('Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item',
			]
		);
		$this->add_control(
			'item_padding',
			[
				'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'item_margin',
			[
				'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item'    => 'border: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'label'     => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item',
			]
		);
		$this->add_control(
			'item_border_radius',
			[
				'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'label'    => esc_html__('Image Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-info-wrap .ep-review-avatar-wrapper .ep-review-avatar-image img',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-info-wrap .ep-review-avatar-wrapper .ep-review-avatar-image img ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'exclude'  => [
					'shadow_position',
				],
				'selector' => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-info-wrap .ep-review-avatar-wrapper .ep-review-avatar-image img',
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_title_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-title',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'style_section_author',
			[
				'label' => esc_html__('Author', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'tabs_for_author'
		);
		$this->start_controls_tab(
			'author_meta',
			[
				'label' => esc_html__('Meta', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'author_meta_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-author-name span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'author_meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-author-name span:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'author_meta_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-author-name span',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'author_name',
			[
				'label' => esc_html__('Name', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'author_name_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-author-name a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'author_name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-author-name a:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'author_name_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-content .ep-review-author-name a',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_content_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-text:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-item .ep-review-text',
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_rating',
			[
				'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);
		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-rating span.dashicons-star-empty' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'rating_bg_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-rating span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'rating_margin',
			[
				'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-product-review-carousel .ep-review-rating span'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();
		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();





		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls('edd-product-review-carousel');

		$this->end_controls_section();
	}
	public function render_query() {
		$settings = $this->get_settings_for_display();
		$args = [
			'orderby'    => isset($settings['orderby']) ? $settings['orderby'] : 'name',
			'order'      => isset($settings['order']) ? $settings['order'] : 'ASC',
			'hide_empty' => isset($settings['hide_empty']) && ($settings['hide_empty'] == 'yes') ? 0 : 1,
		];


		switch ($settings['display_category']) {
			case 'all':
				if (isset($settings['cats_include_by_id']) && !empty($settings['cats_include_by_id'])) {
					$args['include'] = $settings['cats_include_by_id'];
				}
				if (isset($settings['cats_exclude_by_id']) && !empty($settings['cats_exclude_by_id'])) {
					$args['exclude'] = $settings['cats_exclude_by_id'];
				}
				break;
			case 'child':
				if ($settings['parent_cats'] != 'none' &&  !empty($settings['parent_cats'])) {
					$args['child_of'] = $settings['parent_cats'];
				}
				break;
			case 'parents':
				$args['parent'] = 0;
				break;
		}
		$categories = get_terms('download_category', $args);
		return $categories;
	}
	public function render_image() {
		$settings = $this->get_settings_for_display();
		$image_src = Utils::get_placeholder_image_src();
?>
		<div class="ep-edd-product-review-carousel-image">
			<?php if ($settings['is_use_image']) :
				$thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['category_image']['id'], 'category_thumbnail', $settings);
				if (!empty($thumb_url)) {
					$image_src = $settings['category_image']['url'];
				}
			?>
				<img src="<?php echo esc_url($image_src); ?>" alt="">
			<?php endif; ?>
		</div><?php
			}
			public function render_loop_item() {

				$settings = $this->get_settings_for_display();
				remove_action('pre_get_comments', array(edd_reviews(), 'hide_reviews'));
				$reviews = get_comments(
					apply_filters(
						'widget_edd_reviews_args',
						[
							'post_status' => 'publish',
							'post_type'   => 'download',
							'status'      => $settings['status'],
							'order'       => $settings['review_order'],
							'orderby'     => $settings['orderby'],
							'number'      => $settings['items_limit']['size'],
							'offset'      => $settings['offset'],
							'type'        => 'edd_review',
							'meta_query'  => [
								'relation' => 'AND',
								[
									'key'   => 'edd_review_approved',
									'value' => 1,
								],
								[
									'key'     => 'edd_review_reply',
									'compare' => 'NOT EXISTS',
								]
							]
						]
					)
				);
				add_action('pre_get_comments', array(edd_reviews(), 'hide_reviews'));
				if ($reviews) {
					foreach ($reviews as $review) {
				?>
				<div class="ep-review-item swiper-slide">
					<div class="ep-review-info-wrap">
						<div class="ep-review-avatar-wrapper">
							<?php if ($settings['show_image']) : ?>
								<div class="ep-review-avatar-image">
									<?php echo get_avatar($review->comment_author_email, $size = '50'); ?>
								</div>
							<?php endif; ?>
							<div class="ep-review-content">
								<?php
								if ($settings['show_title']) :
									printf('<%1$s class="ep-review-title">%2$s</%1$s>', $settings['title_tags'], get_comment_meta($review->comment_ID, 'edd_review_title', true));
								endif; ?>
								<?php if ($settings['show_author']) : ?>
									<div class="ep-review-author-name">
										<span><?php esc_html_e('purchase by', 'bdthemes-element-pack'); ?></span>
										<?php printf('<a href="%2$s">%1$s</a>', $review->comment_author, get_the_author_meta('url')); ?>
									</div>
								<?php endif; ?>

								<?php if ($settings['show_rating']) : ?>
									<div class="ep-review-rating">
										<?php edd_reviews()->render_star_rating(get_comment_meta($review->comment_ID, 'edd_rating', true)); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php if ($settings['show_review_text']) : ?>
						<div class="ep-review-text">
							<?php echo apply_filters('get_comment_text', $review->comment_content);  ?>
						</div>
					<?php endif; ?>
				</div>

		<?php
					}
				} else {
					element_pack_alert('Opps, Haven\'t found any reviews to display',);
				}
			}
			public function render_header() {
				$this->render_swiper_header_attribute('ep-edd-product-review-carousel');
				$this->add_render_attribute('carousel', 'class', ['ep-edd-product-review-carousel']); ?>
		<div <?php echo $this->get_render_attribute_string('carousel'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">
			<?php
			}

			public function render() {
				if (!class_exists('EDD_Reviews')) :
					element_pack_alert('Ops, EDD Reviews plugin is missing, please make sure EDD Reviews Plugin is install & activated first. <a href="https://easydigitaldownloads.com/">Easy Digital Download</a>');
					return;
				endif;
				$this->render_header();
				$this->render_loop_item();
				$this->render_footer();
			}
		}
