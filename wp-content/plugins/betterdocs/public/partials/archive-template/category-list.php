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

?>
<div class="betterdocs-wraper betterdocs-main-wraper">
	<?php 
	$live_search = BetterDocs_DB::get_settings('live_search');
	if ( $live_search == 1 ) {
	?>
	<div class="betterdocs-search-form-wrap">
		<?php echo do_shortcode( '[betterdocs_search_form]' ); ?>
	</div><!-- .betterdocs-search-form-wrap -->
	<?php } ?>
	<div class="betterdocs-archive-wrap betterdocs-archive-main betterdocs-category-list">
		<?php

			$shortcode = do_shortcode( '[betterdocs_category_grid]' );

			echo apply_filters( 'betterdocs_category_list_shortcode', $shortcode );


		?>
	</div><!-- .betterdocs-archive-wrap -->

</div><!-- .betterdocs-wraper -->

<?php
get_footer();
