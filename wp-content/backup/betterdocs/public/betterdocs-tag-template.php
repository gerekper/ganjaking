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

$object = get_queried_object();
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
        <aside id="betterdocs-sidebar">
            <div class="betterdocs-sidebar-content">
                <?php
                echo do_shortcode( '[betterdocs_category_grid]' );
                ?>
			</div>
        </aside><!-- #sidebar -->
		<div id="main" class="docs-listing-main">
			<div class="docs-category-listing" >
				<div class="docs-cat-title">
					<?php printf( '<h3>%s </h3>', $object->name ); ?>
				</div>
				<div class="docs-list">
					<?php 
						$args = array(
							'post_type' => 'docs',
							'post_status' => 'publish',
							'posts_per_page' => -1,
							'tax_query' => array(
								array(
									'taxonomy' => 'doc_tag',
									'field'    => 'slug',
									'terms'    => $object->slug,
								),
							),
						);
						$post_query = new WP_Query( $args );

						if ( $post_query -> have_posts() ) :

							echo '<ul>';
							while ( $post_query -> have_posts() ) : $post_query -> the_post();
							echo '<li><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.86em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1536 1792"><path d="M1468 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28H96q-40 0-68-28t-28-68V96q0-40 28-68T96 0h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528V640H992q-40 0-68-28t-28-68V128H128v1536h1280zM384 800q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"/></svg><a href="'.get_the_permalink().'">'.get_the_title().'</a></li>';
							endwhile;
							
							echo '</ul>';

						else :

							echo '<p class="nothing-here">'.esc_html__( 'Sorry, no docs were found.', 'betterdocs' ).'</p>';
						
						endif;
						
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
