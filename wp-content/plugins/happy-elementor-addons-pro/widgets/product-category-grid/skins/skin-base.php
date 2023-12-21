<?php
namespace Happy_Addons_Pro\Widget\Skins\Product_Category_Grid;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * register controls hooks
	 */
	protected function _register_controls_actions() {

		add_action( 'elementor/element/ha-product-category-grid/_section_layout/before_section_end', [ $this, 'register_controls' ] );

		add_action( 'elementor/element/ha-product-category-grid/_section_layout_style/after_section_end', [ $this, 'register_style_sections' ] );
	}

	/**
	 * register controls
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->layout_content_tab_controls();
	}

	/**
	 * Layout content controls
	 */
	protected function layout_content_tab_controls() {

		$this->add_control(
            'cat_per_page',
            [
                'label' => __( 'Category Per Page', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 1,
				'max' => 1000,
				'default' => '3',
            ]
		);

		$this->add_control(
			'cat_image_show',
			[
				'label' => __( 'Featured Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'cat_image',
				'default' => 'thumbnail',
				'exclude' => [
					'custom'
				],
				'condition' => [
					$this->get_control_id( 'cat_image_show' ) => 'yes',
				]
			]
		);

		$this->add_control(
			'show_cats_count',
			[
				'label' => __( 'Count Number', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => __( 'Image Overlay', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'prefix_class' => 'ha-image-overlay-',
				'selectors_dictionary' => [
                    'yes' => 'content:\'\';',
                ],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-thumbnail:before' => '{{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'cat_image_show' ) => 'yes',
				]
			]
		);

	}

	/**
	 * Register Style controls
	 */
	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		//Box Style Start
		$this->box_style_tab_controls();

		//Feature Image Style Start
		$this->image_style_tab_controls();

		//Content Style Start
		$this->content_style_tab_controls();
	}


	/**
	 * Box Style controls
	 */
	protected function box_style_tab_controls() {

		$this->start_controls_section(
			'_section_item_box_style',
			[
				'label' => __( 'Item Box', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
            'item_heght',
            [
                'label' => __( 'Height', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-item' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
		);

		$this->add_responsive_control(
            'item_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
				'name' => 'item_border',
                'selector' => '{{WRAPPER}} .ha-product-cat-grid-item-inner',
            ]
		);

		$this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .ha-product-cat-grid-item-inner'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .ha-product-cat-grid-item-inner',
            ]
		);

		$this->end_controls_section();
	}

	/**
	 * Image Style controls
	 */
	protected function image_style_tab_controls() {

		$this->start_controls_section(
			'_section_image_style',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'cat_image_show' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
            'feature_image_width',
            [
                'label' => __( 'Width', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-thumbnail img' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

		$this->add_responsive_control(
            'feature_image_height',
            [
                'label' => __( 'Height', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-thumbnail img' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_image_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'image_overlay_color',
			[
				'label' => __( 'Overlay Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					$this->get_control_id( 'image_overlay' ) => 'yes',
				],
				'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-thumbnail:before' => 'background-color: {{VALUE}};',
				],
			]
        );

		$this->end_controls_section();
	}

	/**
	 * Content Style controls
	 */
	protected function content_style_tab_controls() {

		$this->start_controls_section(
			'_section_content_style',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->content_area_style_tab_controls();

		$this->title_style_tab_controls();

		$this->count_style_tab_controls();

		$this->end_controls_section();

	}

	/**
	 * Content area Style controls
	 */
	protected function content_area_style_tab_controls() {

		$this->add_responsive_control(
            'content_margin',
            [
                'label' => __( 'Margin', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-content-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .ha-product-cat-grid-content-inner',
            ]
        );

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
				'name' => 'content_item_border',
                'selector' => '{{WRAPPER}} .ha-product-cat-grid-content-inner',
            ]
		);

		$this->add_responsive_control(
            'content_item_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-content-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

	}

	/**
	 * Title area Style controls
	 */
	protected function title_style_tab_controls() {

        $this->add_control(
            '_heading_title',
            [
                'label' => __( 'Title', 'happy-addons-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-title a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-title a' => 'color: {{VALUE}};'
                ],
            ]
		);

		$this->add_control(
            'title_hover_color',
            [
                'label' => __( 'Hover Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-title a:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

	}

	/**
	 * Count area Style controls
	 */
	protected function count_style_tab_controls() {

		$this->add_control(
            '_heading_count',
            [
                'label' => __( 'Count', 'happy-addons-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
		);

        $this->add_responsive_control(
            'count_space',
            [
                'label' => __( 'Left Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                    ],
				],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-cat-grid-count' => 'margin-left: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-count',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-count' => 'color: {{VALUE}};'
                ],
                'condition' => [
					$this->get_control_id( 'show_cats_count' ) => 'yes',
                ],
            ]
        );

	}

	/**
	 * Render content
	 */
	public function render() {
        if ( ! class_exists( 'WooCommerce' ) ) {
			printf( '<div class="ha-product-cat-grid-error">%s</div>', __( 'Please Install/Activate Woocommerce Plugin.', 'happy-addons-pro' ) );

			return;
		}

		$settings = $this->parent->get_settings_for_display();
		$cat_per_page = $this->get_instance_value( 'cat_per_page' );
		$product_cats = $this->parent->get_query($cat_per_page);

		if ( empty( $product_cats ) ) {
			if ( is_admin() ) {
				return printf( '<div class="ha-product-cat-grid-error">%s</div>', __( 'Nothing Found. Please Add Category.', 'happy-addons-pro' ) );
			}
		}

		$this->parent->add_render_attribute(
			'wrapper',
			'class',
			[
				'ha-product-cat-grid-wrapper',
				'ha-product-cat-grid-'. $this->get_id(),
			]
		);

		?>

		<div <?php $this->parent->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			foreach ( $product_cats as $product_cat ) :

				$image_src = Utils::get_placeholder_image_src();
				$thumbnail_id = get_term_meta( $product_cat->term_id, 'thumbnail_id', true );
				$image = wp_get_attachment_image_src( $thumbnail_id, $this->get_instance_value( 'cat_image_size' ), false );

				if ( $image ) {
					$image_src = $image[0];
				}

				$has_image = '';
				if ( 'yes' == $this->get_instance_value( 'cat_image_show' ) ) {
					$has_image = esc_attr( ' ha-product-cat-grid-has-image' );
				}

				?>
				<article class="ha-product-cat-grid-item<?php echo esc_attr( ' ' . $has_image ); ?>">
					<div class="ha-product-cat-grid-item-inner">

						<?php if ( $image_src && 'yes' == $this->get_instance_value( 'cat_image_show' ) ) : ?>
							<div class="ha-product-cat-grid-thumbnail">
								<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $product_cat->name ); ?>">
							</div>
						<?php endif; ?>

						<div class="ha-product-cat-grid-content">
							<div class="ha-product-cat-grid-content-inner">
								<h2 class="ha-product-cat-grid-title">
									<a href="<?php echo esc_url( get_term_link( $product_cat->term_id, 'product_cat' ) ); ?>">
										<?php echo esc_html( $product_cat->name ); ?>
									</a>
								</h2>

								<?php if ( $this->get_instance_value( 'show_cats_count' ) == 'yes' ) : ?>
									<?php
										$count_text = '';
										if( 'classic' == $this->get_id() ){
											$count_text = '('. $product_cat->count . ')';
										}
										if( 'minimal' == $this->get_id() ){
											$count_text = $product_cat->count > 1 ? $product_cat->count . __( ' Items', 'happy-addons-pro' ) : $product_cat->count . __( ' Item', 'happy-addons-pro' );
										}
									?>
									<div class="ha-product-cat-grid-count">
										<?php esc_html_e( $count_text ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</article>
				<?php
			endforeach;
			?>
		</div>
		<?php

		$this->parent->get_load_more_button();
	}

}
