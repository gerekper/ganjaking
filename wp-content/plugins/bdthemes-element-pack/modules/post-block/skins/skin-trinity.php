<?php
namespace ElementPack\Modules\PostBlock\Skins;
use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Trinity extends Elementor_Skin_Base {

	public function get_id() {
		return 'trinity';
	}

	public function get_title() {
		return __( 'Trinity', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings = $this->parent->get_settings();
		$id       = uniqid('bdtpbm_');

		// TODO need to delete after v6.5
        if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['posts_limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }
		$this->parent->query_posts($limit);
		$wp_query = $this->parent->get_query();

		if( $wp_query->have_posts() ) :

			$this->parent->add_render_attribute(
				[
					'post-block' => [
						'id'    => esc_attr( $id ),
						'class' => [
							'bdt-post-block',
							'skin-trinity',
						]
					]
				]
			);
			
			?>
			<div <?php echo $this->parent->get_render_attribute_string( 'post-block' ); ?>>

		  		<div class="bdt-post-block-items bdt-child-width-1-<?php echo esc_attr($settings['featured_item']); ?>@m bdt-grid-<?php echo esc_attr($settings['trinity_column_gap']); ?>" data-bdt-grid>
					<?php
					while ( $wp_query->have_posts() ) : $wp_query->the_post();

						$placeholder_image_src = Utils::get_placeholder_image_src();
						$image_src             = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );

						if ( ! $image_src ) {
							$image_src = $placeholder_image_src;
						} else {
							$image_src = $image_src[0];
						}

						?>
			  			<div class="bdt-post-block-item featured-part">
				  			<div class="bdt-post-block-thumbnail-wrap bdt-position-relative">
				  				<div class="bdt-post-block-thumbnail">
				  					<a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
					  					<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					  				</a>
				  				</div>
				  				<div class="bdt-overlay-primary bdt-position-cover"></div>
						  		<div class="bdt-post-block-desc bdt-text-center bdt-position-center bdt-position-medium bdt-position-z-index">
									<?php if ('yes' == $settings['featured_show_tag']) : ?>
										<div class="bdt-post-block-tag-wrap">
					                		<?php
											$tags_list = get_the_tag_list( '<span class="bdt-background-primary">', '</span> <span class="bdt-background-primary">', '</span>');
						                		if ($tags_list) :
						                    		echo  wp_kses_post($tags_list);
						                		endif; ?>
					                	</div>
									<?php endif ?>

									<?php if ('yes' == $settings['featured_show_title']) : ?>
										<<?php echo Utils::get_valid_html_tag($settings['featured_title_size']); ?> class="bdt-post-block-title bdt-margin-small-top">
											<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-link" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()) ; ?></a>
										</<?php echo Utils::get_valid_html_tag($settings['featured_title_size']); ?>>
									<?php endif ?>

					            	<?php if ('yes' == $settings['featured_show_category'] or 'yes' == $settings['featured_show_date']) : ?>

										<div class="bdt-post-block-meta bdt-flex-center bdt-subnav bdt-flex-middle">
											<?php if ('yes' == $settings['featured_show_category']) : ?>
												<?php echo '<span>'.get_the_category_list(', ').'</span>'; ?>
											<?php endif ?>

											<?php $this->parent->render_featured_date(); ?>
										</div>

									<?php endif ?>
						  		</div>
							</div>
						</div>

					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>
 		<?php endif;
	}
}

