<?php
/**
 * Template archive docs
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

get_header(); 

$alphabetically_order_post = BetterDocs_DB::get_settings('alphabetically_order_post');
$nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');

global $wp_query;
$term_slug = $wp_query->query['doc_category'];
$term = get_term_by( 'slug', $wp_query->query['doc_category'], 'doc_category' );
?>

<div class="betterdocs-category-wraper betterdocs-single-wraper">
	<?php 
		$live_search = BetterDocs_DB::get_settings('live_search');
		if($live_search == 1){
	?>
	<div class="betterdocs-search-form-wrap">
		<?php echo do_shortcode( '[betterdocs_search_form]' ); ?>
	</div>
	<?php } ?>
	<div class="betterdocs-content-area">
		
		<?php 

		$enable_archive_sidebar = BetterDocs_DB::get_settings('enable_archive_sidebar');
		
		if ( $enable_archive_sidebar == 1 ) {

		?>

        <aside id="betterdocs-sidebar">
            <div class="betterdocs-sidebar-content">

				<?php

				$shortcode = do_shortcode( '[betterdocs_category_grid sidebar_list="true" posts_per_grid="-1"]' );

				echo apply_filters( 'betterdocs_sidebar_category_shortcode', $shortcode );
                
				?>
				
			</div>
        </aside><!-- #sidebar -->

		<?php } ?>

		<div id="main" class="docs-listing-main">
			<div class="docs-category-listing" >
				<?php 

				$enable_breadcrumb = BetterDocs_DB::get_settings('enable_breadcrumb');
				
				if ( $enable_breadcrumb == 1 ) {
					betterdocs_breadcrumbs();
				}

				?>
				<div class="docs-cat-title">
					<?php printf( '<h3>%s </h3>', $term->name ); ?>
					<?php printf( '<p>%s </p>', $term->description ); ?>
				</div>
				<div class="docs-list">
					<?php 

						$multikb = apply_filters( 'betterdocs_cat_template_multikb', false );

						$args = BetterDocs_Helper::list_query_arg( 'docs', $multikb, $term->slug, -1, $alphabetically_order_post );

						$args = apply_filters( 'betterdocs_articles_args', $args, $term->term_id );

						$post_query = new WP_Query( $args );

						if ( $post_query -> have_posts() ) :

							echo '<ul>';
							while ( $post_query -> have_posts() ) : $post_query -> the_post();
								echo '<li>'. BetterDocs_Helper::list_svg() .'<a href="'.get_the_permalink().'">'.get_the_title().'</a></li>';
							endwhile;
							
							echo '</ul>';
						
						endif;
						wp_reset_query();

						// Sub category query
						if( $nested_subcategory == 1 ) {
							$sub_categories = BetterDocs_Helper::child_taxonomy_terms( $term->term_id, $multikb );
							
							if( $sub_categories ){
								foreach($sub_categories as $sub_category) {
									echo '<span class="docs-sub-cat-title">
									' . BetterDocs_Helper::arrow_right_svg() . '
										' . BetterDocs_Helper::arrow_down_svg() . '
									<a href="#">'.$sub_category->name.'</a></span>';
									echo '<ul class="docs-sub-cat">';
									
									$sub_args = BetterDocs_Helper::list_query_arg( 'docs', $multikb, $sub_category->slug, -1, $alphabetically_order_post );
									$sub_post_query = new WP_Query( $sub_args );
									
									if ( $sub_post_query->have_posts() ) :
										while ( $sub_post_query->have_posts() ) : $sub_post_query->the_post();
											$sub_attr = ['href="'.get_the_permalink().'"'];
											echo '<li class="sub-list">'. BetterDocs_Helper::list_svg() .'<a '.implode(' ',$sub_attr).'>'.get_the_title().'</a></li>';
										endwhile;
									endif;

									wp_reset_query();
									echo '</ul>';
								}
								
							}
						}
						
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
