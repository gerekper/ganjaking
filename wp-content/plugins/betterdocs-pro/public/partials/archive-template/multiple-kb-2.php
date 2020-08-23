<?php
/**
 * Template multiple docs
 *
 * @link       https://wpdeveloper.net
 * @since      
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

get_header(); 

?>
<div class="betterdocs-wraper betterdocs-mkb-wraper">

	<?php 

	$live_search = BetterDocs_DB::get_settings('live_search');

	if($live_search == 1){
	
	?>

	<div class="betterdocs-search-form-wrap">

		<?php echo do_shortcode( '[betterdocs_search_form]' ); ?>
	
	</div><!-- .betterdocs-search-form-wrap -->
	
	<?php } ?>
	
	<div class="betterdocs-archive-wrap betterdocs-archive-mkb">
		
		<?php
			
			// Display category list.
			echo do_shortcode( '[betterdocs_multiple_kb_2]' );

		?>
		
	</div><!-- .betterdocs-archive-wrap -->

</div><!-- .betterdocs-wraper -->

<?php
get_footer();
