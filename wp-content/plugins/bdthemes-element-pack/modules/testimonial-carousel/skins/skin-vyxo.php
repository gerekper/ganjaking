<?php

namespace ElementPack\Modules\TestimonialCarousel\Skins;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Vyxo extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-vyxo';
	}

	public function get_title() {
		return __('Vyxo', 'bdthemes-element-pack');
	}

	public function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action('elementor/element/bdt-testimonial-carousel/section_style_text/after_section_start', [$this, 'register_vyxo_style_controls']);
	}

	public function register_vyxo_style_controls(Module_Base $widget) {
		$this->parent = $widget;

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'text_background_color',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text-wrap',
				// 'separator' => 'after',
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'        => 'text_border',
                'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-testimonial-carousel-text-wrap',
            ]
        );
        
        $this->add_responsive_control(
            'text_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-carousel-text-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'text_padding',
            [
                'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-carousel-text-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'text_margin',
            [
                'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-carousel-text-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'text_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel-text-wrap',
            ]
        );

		$this->add_control(
            'text_divider',
            [
                'label'     => esc_html__('Divider', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIVIDER,
            ]
        );
	}

	public function render() {
		$settings = $this->parent->get_settings();

		// TODO need to delete after v6.5
		if (isset($settings['posts']) and $settings['posts_per_page'] == 10) {
			$limit = $settings['posts'];
		} else {
			$limit = $settings['posts_per_page'];
		}
		$wp_query = $this->parent->render_query($limit);

		if ($wp_query->have_posts()) : ?>

			<?php $this->parent->render_header('vyxo'); ?>

			<?php while ($wp_query->have_posts()) : $wp_query->the_post(); 
			$platform = get_post_meta(get_the_ID(), 'bdthemes_tm_platform', true);
			?>
				<div class="swiper-slide bdt-testimonial-carousel-item bdt-review-<?php echo strtolower($platform); ?>">
					<div class="bdt-testimonial-carousel-text-wrap bdt-padding bdt-background-primary">

						<?php if ($settings['rating_position'] == 'top') : ?>
							<?php if ($settings['show_rating']) : ?>
								<div class="bdt-testimonial-carousel-rating bdt-display-inline-block">
									<?php $this->parent->render_rating(get_the_ID()); ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<?php $this->parent->render_excerpt(); ?>
					</div>
					<div class="bdt-testimonial-carousel-item-wrapper">
						<div class="testimonial-item-header bdt-position-top-center">
							<?php $this->parent->render_image(get_the_ID()); ?>
						</div>

						<div class="bdt-testimonial-meta <?php echo ($settings['meta_multi_line']) ? '' : 'bdt-meta-multi-line'; ?>">
							<?php
							$this->parent->render_title(get_the_ID());
							$this->parent->render_address(get_the_ID()); ?>
						</div>

						<?php if ($settings['rating_position'] == 'bottom') : ?>
						<?php if ($settings['show_rating']) : ?>
							<div class="bdt-testimonial-carousel-rating bdt-display-inline-block">
								<?php $this->parent->render_rating(get_the_ID()); ?>
							</div>
						<?php endif; ?>
						<?php endif; ?>

					</div>
				</div>
			<?php endwhile;
			wp_reset_postdata(); ?>

<?php $this->parent->render_footer();

		endif;
	}
}
