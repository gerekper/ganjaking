<?php
namespace ElementPack\Modules\PostBlock\Skins;
 
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Icons_Manager;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Genesis extends Elementor_Skin_Base {

	public function get_id() {
		return 'genesis';
	}

	public function get_title() {
		return __( 'Genesis', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings = $this->parent->get_settings();
		$id       = uniqid('bdtpbm_');

		$animation        = ($settings['read_more_hover_animation']) ? ' elementor-animation-'.$settings['read_more_hover_animation'] : '';
		$bdt_list_divider = ( $settings['show_list_divider'] ) ? ' bdt-has-divider' : '';

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
							'bdt-grid',
							'bdt-grid-match',
							'skin-genesis',
						],
						'data-bdt-grid' => ''
					]
				]
			);

			if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default
				$settings['icon'] = 'fas fa-arrow-right';
			}

			$migrated  = isset( $settings['__fa4_migrated']['post_block_icon'] );
			$is_new    = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

			?> 
			<div <?php echo $this->parent->get_render_attribute_string( 'post-block' ); ?>>

				<?php $bdt_count = 0;
			
				while ( $wp_query->have_posts() ) : $wp_query->the_post();

					$bdt_count++;

					$placeholder_image_src = Utils::get_placeholder_image_src();
					$image_src             = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );
 
					if ( ! $image_src ) {
						$image_src = $placeholder_image_src;
					} else {
						$image_src = $image_src[0];
					}

					 

					if( $bdt_count <= $settings['featured_item']) : ?>

				  		<div class="bdt-width-1-<?php echo esc_attr($settings['featured_item']); ?>@m">
				  			<div class="bdt-post-block-item featured-part">
								<div class="bdt-post-block-img-wrapper bdt-margin-bottom">
									<a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
					  					<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					  				</a>
								</div>
						  		
						  		<div class="bdt-post-block-desc">

									<?php if ($settings['featured_show_title']) : ?>
										<<?php echo Utils::get_valid_html_tag($settings['featured_title_size']); ?>  class="bdt-post-block-title">
											<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-link" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()) ; ?></a>
										</<?php echo Utils::get_valid_html_tag($settings['featured_title_size']); ?> >
									<?php endif ?>

	            	            	<?php if ($settings['featured_show_category'] or $settings['featured_show_date']) : ?>

	            						<div class="bdt-post-block-meta bdt-subnav bdt-flex-middle">
											<?php $this->parent->render_featured_date(); ?>

	            							<?php if ($settings['featured_show_category']) : ?>
	            								<?php echo '<span>'.get_the_category_list(', ').'</span>'; ?>
	            							<?php endif ?>
	            							
	            						</div>

	            					<?php endif ?>

									<?php $this->parent->render_excerpt(); ?>

									<?php if ($settings['featured_show_read_more']) : ?>
										<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-read-more bdt-link-reset<?php echo esc_attr($animation); ?>"><?php echo esc_html($settings['read_more_text']); ?>
											
											<?php if ($settings['post_block_icon']['value']) : ?>
												<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

													<?php if ( $is_new || $migrated ) :
														Icons_Manager::render_icon( $settings['post_block_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
													else : ?>
														<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
													<?php endif; ?>

												</span>
											<?php endif; ?>

										</a>
									<?php endif ?>

						  		</div>

							</div>
							
						</div>
					
					<?php if ($bdt_count == $settings['featured_item']) : ?>

			  		<div class="bdt-post-block-item list-part bdt-width-1-1@m bdt-margin-medium-top">
			  			<ul class="bdt-child-width-1-<?php echo esc_attr($settings['featured_item']); ?>@m<?php echo esc_attr($bdt_list_divider); ?>" data-bdt-grid data-bdt-scrollspy="cls: bdt-animation-fade; target: > .bdt-post-block-item; delay: 300;">
			  		<?php endif; ?>

					<?php else : ?>
						<?php 
						$post_thumbnail  = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' ); 

 						 if(empty($post_thumbnail[0])){
 						 	$image_src_thumbnail = $placeholder_image_src;
 						 }else{
 						 	$image_src_thumbnail =$post_thumbnail[0];
 						 }
 						 
						?>
					  			<li>
						  			<div class="bdt-flex">
						  				<div class="bdt-post-block-thumbnail bdt-width-auto">
						  					<a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
							  					<img src="<?php echo esc_url($image_src_thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
							  				</a>
						  				</div>
								  		<div class="bdt-post-block-desc bdt-width-expand bdt-margin-small-left">
											<?php if ($settings['list_show_title']) : ?>
												<h4 class="bdt-post-block-title">
													<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-link" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()) ; ?></a>
												</h4>
											<?php endif ?>

							            	<?php if ($settings['list_show_category'] or $settings['list_show_date']) : ?>

												<div class="bdt-post-block-meta bdt-subnav bdt-flex-middle">
													<?php $this->parent->render_list_date(); ?>

													<?php if ($settings['list_show_category']) : ?>
														<?php echo '<span>'.get_the_category_list(', ').'</span>'; ?>
													<?php endif ?>
													
												</div>

											<?php endif ?>
								  		</div>
									</div>
								</li>
							<?php endif; endwhile; ?>
						</ul>
					</div>
				</div>
		
		 	<?php 
			wp_reset_postdata(); 
		endif;
	}
}

