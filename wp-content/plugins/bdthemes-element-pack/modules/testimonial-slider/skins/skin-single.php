<?php

namespace ElementPack\Modules\TestimonialSlider\Skins;


use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Single extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-single';
	}

	public function get_title() {
		return __('Single', 'bdthemes-element-pack');
	}

	public function render_image() {

		$testimonial_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');

		if (!$testimonial_thumb) {
			$testimonial_thumb = BDTEP_ASSETS_URL . 'images/member.svg';
		} else {
			$testimonial_thumb = $testimonial_thumb[0];
		}

?>
		<div class="bdt-testimonial-thumb-wrap bdt-flex bdt-position-relative">
			<div class="bdt-testimonial-thumb bdt-position-relative">
				<img src="<?php echo esc_url($testimonial_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
			</div>
			<?php $this->parent->render_review_platform(get_the_ID()); ?>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();
		$index = 1;

		$rating_align = ($settings['thumb']) ? '' : ' bdt-flex-center';


		// TODO need to delete after v6.5
		if (isset($settings['posts']) and $settings['posts_per_page'] == 6) {
			$limit = $settings['posts'];
		} else {
			$limit = $settings['posts_per_page'];
		}

		$wp_query = $this->parent->render_query($limit);

		if (!$wp_query->found_posts) {
			return;
		}

		$this->parent->render_header('single', $id, $settings);

		while ($wp_query->have_posts()) : $wp_query->the_post(); 
		$platform = get_post_meta(get_the_ID(), 'bdthemes_tm_platform', true);
		?>

			<div class="swiper-slide bdt-review-<?php echo strtolower($platform); ?>">

				<div class="bdt-grid bdt-flex-middle" data-bdt-grid data-bdt-height-match="target: > div > div">

					<?php if ('right' !== $settings['alignment']) : ?>
						<div class="bdt-testimonial-image-part bdt-width-auto@m">
							<?php $this->render_image(); ?>
						</div>
					<?php endif; ?>

					<div class="bdt-testimonial-desc-part bdt-width-expand@m">

						<div class="bdt-slider-item-inner bdt-grid-item-match">

							<?php if ('after' == $settings['meta_position']) : ?>
								<div class="bdt-testimonial-text bdt-text-<?php echo esc_attr($settings['alignment']); ?>">
									<?php $this->parent->render_excerpt(); ?>

								</div>
							<?php endif; ?>

							<div class="bdt-flex bdt-flex-<?php echo esc_attr($settings['alignment']); ?> bdt-flex-middle">

								<?php $this->parent->render_meta('testmonial-meta-' . $index); ?>

							</div>

							<?php if ('before' == $settings['meta_position']) : ?>
								<div class="bdt-testimonial-text bdt-text-<?php echo esc_attr($settings['alignment']); ?>">
									<?php $this->parent->render_excerpt(); ?>

								</div>
							<?php endif; ?>
						</div>
					</div>

					<?php if ('right' == $settings['alignment']) : ?>
						<div class="bdt-testimonial-image-part bdt-width-auto@m">
							<?php $this->render_image(get_the_ID()); ?>
						</div>
					<?php endif; ?>

				</div>
			</div>

<?php
			$index++;
		endwhile;

		wp_reset_postdata();

		$this->parent->render_footer($settings);
	}
}
