<?php
namespace ElementPack\Modules\PostSlider\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Utils;
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Vast extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-vast';
	}

	public function get_title() {
		return __( 'Vast', 'bdthemes-element-pack' );
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

		?>
		<div class="bdt-post-slider-item">
			<div class="bdt-position-relative bdt-post-slider-thumbnail">
				<img src="<?php echo esc_url($slider_thumbnail); ?>" alt="<?php echo get_the_title(); ?>">
				<?php //$this->render_navigation(); ?>
			</div>

			<div class="bdt-post-slider-content bdt-padding-large bdt-background-muted">

	            <?php if ($settings['show_tag']) : ?>
	        		<?php $tags_list = get_the_tag_list('<span class="bdt-background-primary">','</span> <span class="bdt-background-primary">','</span>'); ?>
	        		<?php if ($tags_list) : ?> 
	            		<div class="bdt-post-slider-tag-wrap" data-bdt-slider-parallax="y: -200,200">
	            			<?php  echo  wp_kses_post($tags_list); ?>
            			</div>
	            	<?php endif; ?>
	            <?php endif; ?>

				<?php $this->render_title(); ?>

				<?php if ($settings['show_meta']) : ?>
					<div class="bdt-post-slider-meta bdt-flex-inline bdt-flex-middle" data-bdt-slider-parallax="x: 250,-250">
						<a class="bdt-flex bdt-flex-middle" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
						<div class="bdt-author bdt-border-circle bdt-overflow-hidden bdt-visible@m"><?php echo get_avatar( get_the_author_meta( 'ID' ) , 28 ); ?></div>
						</a>
						<div class="">
							<span class="">
								<a class="" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
								<?php echo esc_attr(get_the_author()); ?>
								</a>
								<span class="bdt-display-inline-block bdt-margin-remove">
									<?php echo esc_html_x('On', 'Frontend', 'bdthemes-element-pack'); ?> <?php $this->parent->render_date(); ?>
								</span>
							</span>
							
							<span>
                            
                                <?php
	                                echo element_pack_get_category_list( $settings[ 'posts_source' ] );
                                ?>
                            </span>
						</div>
						

					</div>
				<?php endif; ?>
				
				<?php $this->parent->render_excerpt(); ?>
				<?php $this->parent->render_read_more_button(); ?>

			</div>
		</div>
		<?php
	}

	public function render_header() {
		$settings = $this->parent->get_settings();
		$id       = 'bdt-post-slider-' . $this->parent->get_id();

	    $this->parent->add_render_attribute(
			[
				'slider-settings' => [
					'id'    => esc_attr($id),
					'class' => [
						'bdt-post-slider',
						'skin-vast',
						'bdt-position-relative'
					],
					'data-bdt-slider' => [
						wp_json_encode(array_filter([
							"animation"         => $settings["slider_animations"],
							"autoplay"          => $settings["autoplay"],
							"autoplay-interval" => $settings["autoplay_interval"],
							"pause-on-hover"    => $settings["pause_on_hover"] == 'yes' ? 'true' : 'false',
						]))
					]
				]
			]
		);
	    
		?>
		<div <?php echo ( $this->parent->get_render_attribute_string( 'slider-settings' ) ); ?>>
			<div class="bdt-slider-items bdt-child-width-1-1">
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
				<<?php echo Utils::get_valid_html_tag($tag); ?> class="bdt-post-slider-title bdt-margin-remove-bottom" data-bdt-slider-parallax="x: 200,-200">
					<?php the_title() ?>
				</<?php echo Utils::get_valid_html_tag($tag); ?>>
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
		$settings = $this->parent->get_settings();
		$id       = $this->parent->get_id();

		?>
		<div  class="bdt-post-slider-navigation">
			<a class="bdt-position-center-left bdt-position-small bdt-hidden-hover" href="#" data-bdt-slidenav-previous data-bdt-slider-item="previous"></a>
			<a class="bdt-position-center-right bdt-position-small bdt-hidden-hover" href="#" data-bdt-slidenav-next data-bdt-slider-item="next"></a>
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