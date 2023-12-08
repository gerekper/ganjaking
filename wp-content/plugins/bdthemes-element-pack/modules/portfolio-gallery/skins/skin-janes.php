<?php
namespace ElementPack\Modules\PortfolioGallery\Skins;

use Elementor\Utils;
use Elementor\Controls_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Janes extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-janes';
	}

	public function get_title() {
		return __( 'Janes', 'bdthemes-element-pack' );
	}

	public function render_overlay() {
		$settings = $this->parent->get_settings_for_display();

		$this->parent->add_render_attribute(
			[
				'content-position' => [
					'class' => [
						'bdt-position-cover',
					]
				]
			], '', '', true
		);

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'content-position' ); ?>>
			<div class="bdt-portfolio-content">
				<div class="bdt-gallery-content-inner">
					<?php 

					$placeholder_img_src = Utils::get_placeholder_image_src();

					$img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

					if ( ! $img_url ) {
						$img_url = $placeholder_img_src;
					} else {
						$img_url = $img_url[0];
					}

					$this->parent->add_render_attribute(
						[
							'lightbox-settings' => [
								'class' => [
									'bdt-gallery-item-link',
									'bdt-gallery-lightbox-item',
									('icon' == $settings['link_type']) ? 'bdt-link-icon' : 'bdt-link-text'
								],
								'data-elementor-open-lightbox' => 'no',
								'data-caption'                 => get_the_title(),
								'href'                         => esc_url($img_url)
							]
						], '', '', true
					);
					
					if ( 'none' !== $settings['show_link'])  : ?>
						<div class="bdt-flex-inline bdt-gallery-item-link-wrapper">
							<?php if (( 'lightbox' == $settings['show_link'] ) || ( 'both' == $settings['show_link'] )) : ?>
								<a <?php echo $this->parent->get_render_attribute_string( 'lightbox-settings' ); ?>>
									<?php if ( 'icon' == $settings['link_type'] ) : ?>
										<i class="ep-icon-plus" aria-hidden="true"></i>
									<?php elseif ( 'text' == $settings['link_type'] ) : ?>
										<span><?php esc_html_e( 'ZOOM', 'bdthemes-element-pack' ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
							
							<?php if (( 'post' == $settings['show_link'] ) || ( 'both' == $settings['show_link'] )) : ?>
								<?php 
									$link_type_class =  ( 'icon' == $settings['link_type'] ) ? ' bdt-link-icon' : ' bdt-link-text'; 
									$target =  ( $settings['external_link'] ) ? 'target="_blank"' : ''; 

									?>
								<a class="bdt-gallery-item-link<?php echo esc_attr($link_type_class); ?>" href="<?php echo esc_attr(get_permalink()); ?>" <?php echo $target; ?>>
									<?php if ( 'icon' == $settings['link_type'] ) : ?>
										<i class="ep-icon-link" aria-hidden="true"></i>
									<?php elseif ( 'text' == $settings['link_type'] ) : ?>
										<span><?php esc_html_e( 'VIEW', 'bdthemes-element-pack' ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_desc() {
		?>
			<div class="bdt-portfolio-desc">
				<div>
					<?php $this->parent->render_title(); ?>
					<?php $this->parent->render_categories_names(); ?>
				</div>
				<?php $this->parent->render_excerpt(); ?>

			</div>
		<?php
	}
	public function render_post() {
		$settings = $this->parent->get_settings_for_display();
		global $post;

		$element_key = 'portfolio-item-' . $post->ID;
		$item_filters = get_the_terms( $post->ID, 'portfolio_filter' );

		if ($settings['tilt_show']) {
			$this->parent->add_render_attribute('portfolio-item-inner', 'data-tilt', '', true);
			if ($settings['tilt_scale']) {
				$this->parent->add_render_attribute('portfolio-item-inner', 'data-tilt-scale', '1.2', true);
			}
		}

		$this->parent->add_render_attribute('portfolio-item-inner', 'class', 'bdt-portfolio-inner', true);

		$this->parent->add_render_attribute('portfolio-item', 'class', 'bdt-gallery-item bdt-transition-toggle', true);

		if( $settings['show_filter_bar'] and is_array($item_filters) ) {
			foreach ($item_filters as $item_filter) {
				$this->parent->add_render_attribute($element_key, 'data-filter', 'bdtp-' . $item_filter->slug);
			}
		}

		?>
		<div <?php echo $this->parent->get_render_attribute_string( $element_key ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( 'portfolio-item' ); ?>>
				<div <?php echo $this->parent->get_render_attribute_string( 'portfolio-item-inner' ); ?>>
					<?php
					$this->parent->render_thumbnail();
					$this->render_overlay();
					?>
				<?php $this->render_desc(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();

		// TODO need to delete after v6.5
        if (isset($settings['limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }
		$this->parent->query_posts($limit);

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->parent->render_header('janes');

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->parent->render_footer();

		if ($settings['show_pagination']) {
			element_pack_post_pagination($wp_query);
		}
		
		wp_reset_postdata();

	}

}

