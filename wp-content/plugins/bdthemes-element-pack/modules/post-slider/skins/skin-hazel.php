<?php
namespace ElementPack\Modules\PostSlider\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Utils;
 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Hazel extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-hazel';
	}

	public function get_title() {
		return __( 'Hazel', 'bdthemes-element-pack' );
	}

	public function render_loop_item() {
		$settings              = $this->parent->get_settings();
		
		$thumbnail_size = $settings['thumbnail_size'];
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$slider_thumbnail      = wp_get_attachment_image_src( get_post_thumbnail_id(), $thumbnail_size );

		if ( ! $slider_thumbnail ) {
			$slider_thumbnail = $placeholder_image_src;
		} else {
			$slider_thumbnail = $slider_thumbnail[0];
		}

		$slider_max_height = $settings['slider_max_height']['size'] ? 'style="height:' . $settings['slider_max_height']['size'] . 'px"': '';

		?>
		<div class="bdt-post-slider-item">
			<div class="bdt-grid bdt-grid-collapse" data-bdt-grid>
				<div class="bdt-position-relative bdt-width-1-2 bdt-width-2-3@m bdt-post-slider-thumbnail">
					<div>
						<img src="<?php echo esc_url($slider_thumbnail); ?>" alt="<?php echo get_the_title(); ?>">						
					</div>
				</div>

				<div class="bdt-width-1-2 bdt-width-1-3@m">
					<div class="bdt-post-slider-content" <?php echo esc_attr($slider_max_height); ?>>

			            <?php if ($settings['show_tag']) : ?>
			        		<?php $tags_list = get_the_tag_list('<span class="bdt-background-primary">','</span> <span class="bdt-background-primary">','</span>'); ?>
			        		<?php if ($tags_list) : ?> 
			            		<div class="bdt-post-slider-tag-wrap"><?php  echo  wp_kses_post($tags_list); ?></div>
			            	<?php endif; ?>
			            <?php endif; ?>

						<?php $this->render_title(); ?>

						<?php if ($settings['show_meta']) : ?>
							<div class="bdt-post-slider-meta bdt-flex-inline bdt-flex-middle bdt-margin-small-top">
								<a class="bdt-flex bdt-flex-middle" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
								<div class="bdt-display-inline-block bdt-text-capitalize bdt-author"><?php echo esc_attr(get_the_author()); ?></div> 
								</a>
								<span class=""><?php esc_html_e('On', 'bdthemes-element-pack'); ?> <?php $this->parent->render_date(); ?></span>
							</div>
						<?php endif; ?>
						
						<?php $this->parent->render_excerpt(); ?>
						<?php $this->parent->render_read_more_button(); ?>

					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_header() {
		$settings = $this->parent->get_settings();
		$id       = 'bdt-post-slider-' . $this->parent->get_id();

		$ratio = ($settings['slider_size_ratio']['width'] && $settings['slider_size_ratio']['height']) ? $settings['slider_size_ratio']['width'].":".$settings['slider_size_ratio']['height'] : '';

	    $this->parent->add_render_attribute(
			[
				'slider-settings' => [
					'id'    => esc_attr($id),
					'class' => [
						'bdt-post-slider',
						'skin-hazel',
						'bdt-position-relative'
					],
					'data-bdt-slideshow' => [
						wp_json_encode(array_filter([
							"animation"         => $settings["slider_animations"],
							"min-height"        => $settings["slider_min_height"]["size"],
							"max-height"        => $settings["slider_max_height"]["size"],
							"ratio"             => $ratio,
							"autoplay"          => $settings["autoplay"],
							"autoplay-interval" => $settings["autoplay_interval"],
							"pause-on-hover"    => $settings["pause_on_hover"] == 'yes' ? 'true' : 'false',
						]))
					],
					'data-bdt-height-match' => '.bdt-post-slider-match-height'
				]
			]
		);
	    
		?>
		<div <?php echo ( $this->parent->get_render_attribute_string( 'slider-settings' ) ); ?>>
			<div class="bdt-slideshow-items bdt-child-width-1-1">
		<?php
	}

	public function render_title() {
		if ( ! $this->parent->get_settings( 'show_title' ) ) {
			return;
		}

		$tag = $this->parent->get_settings( 'title_tag' );

		?>
		<div class="bdt-post-slider-title-wrap">
			<a href="<?php echo get_permalink(); ?>">
				<<?php echo Utils::get_valid_html_tag($tag) ?> class="bdt-post-slider-title bdt-margin-remove-bottom">
					<?php the_title() ?>
				</<?php echo Utils::get_valid_html_tag($tag) ?>>
			</a>
		</div>
		<?php
	}

	public function render_footer() {
		?>

			</div>
			<?php $this->render_navigation(); ?>			
		</div>
		
		<?php
	}

	public function render_navigation() {
		$id     = $this->parent->get_id();
		$is_rtl = is_rtl() ? 'dir="ltr"' : '';
		$settings = $this->parent->get_settings_for_display();

		$prev_text =  (!empty($settings['hazel_prev_text']) ? $settings['hazel_prev_text'] : 'PREV');
		$next_text =  (!empty($settings['hazel_next_text']) ? $settings['hazel_next_text'] : 'NEXT');

		?>
		<div id="<?php echo esc_attr($id); ?>_nav"  class="bdt-post-slider-navigation bdt-position-bottom-right bdt-width-1-2 bdt-width-1-3@m">
			<div class="bdt-post-slider-navigation-inner bdt-grid bdt-grid-collapse" <?php echo esc_attr($is_rtl); ?>>
				<a class="bdt-hidden-hover bdt-width-1-2" href="#" data-bdt-slideshow-item="previous">
					<svg width="14" height="24" viewBox="0 0 14 24" xmlns="http://www.w3.org/2000/svg">
						<polyline fill="none" stroke="#000" stroke-width="1.4" points="12.775,1 1.225,12 12.775,23 "></polyline>
					</svg>
					<span class="bdt-slider-nav-text"><?php esc_html_e( $prev_text, 'bdthemes-element-pack' ) ?></span>
				</a>
				<a class="bdt-hidden-hover bdt-width-1-2" href="#" data-bdt-slideshow-item="next">
					<span class="bdt-slider-nav-text"><?php esc_html_e( $next_text, 'bdthemes-element-pack' ) ?></span>
					<svg width="14" height="24" viewBox="0 0 14 24" xmlns="http://www.w3.org/2000/svg">
						<polyline fill="none" stroke="#000" stroke-width="1.4" points="1.225,23 12.775,12 1.225,1 "></polyline>
					</svg>
				</a>
			</div>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();

		//$post_limit = ('bdt-hazel' == $settings['_skin'] or 'bdt-vast' == $settings['_skin']) ? $settings['item_limit']['size'] : 4;

		// TODO need to delete after v6.5
        if (isset($settings['item_limit']['size']) and $settings['posts_per_page'] == 6) {
            $limit = ('bdt-hazel' == $settings['_skin'] or 'bdt-vast' == $settings['_skin']) ? $settings['item_limit']['size'] : 4;
        } else {
            $limit = $settings['posts_per_page'];
        }

		$this->parent->query_posts($limit);

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->render_header();

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$this->render_loop_item();
		}

		$this->render_footer();

		wp_reset_postdata();
	}
}