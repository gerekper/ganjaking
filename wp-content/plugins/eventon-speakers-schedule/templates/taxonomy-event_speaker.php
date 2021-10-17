<?php	
/*
 *	The template for displaying event categoroes - event speaker
 * 	In order to customize this archive page template
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 2.5
 */	
	
	global $eventon;

	get_header();

	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

	$term = get_term_by( 'slug', $term, $tax );

	do_action('eventon_before_main_content');

	$term_meta = evo_get_term_meta( $tax,$term->term_id );
	
	// IMAGE
		$img_url = false;
		if(!empty($term_meta['evo_spk_img'])){
			$img_url = wp_get_attachment_image_src($term_meta['evo_spk_img'],'full');
			$img_url = $img_url[0];
		}

?>

<div id="content" class='evotax_term_card evo_location_card <?php echo $tax;?>'>
	<div class="hentry">
		<div class='eventon entry-content'>
			<div class="evo_location_tax evotax_term_details" style='background-image:url(<?php echo $img_url;?>)'>
				<?php if($img_url):?><div class="location_circle" style='background-image:url(<?php echo $img_url;?>)'></div><?php endif;?>
				<h2 class="location_name"><span><?php echo $term->name;?></span></h2>
				
				<div class='location_description'><?php echo category_description();?></div>
			</div>
			
			<h3 class="location_subtitle evotax_term_subtitle"><?php evo_lang_e('Events by this speaker');?></h3>
		
		<?php 
			$shortcode = apply_filters('evo_tax_archieve_page_shortcode_'.$tax, 
				'[add_eventon_list number_of_months="5" '.$tax.'='.$term->term_id.' hide_mult_occur="no" hide_empty_months="yes"]', 
				$tax,
				$term->term_id
			);
			echo do_shortcode($shortcode);
		?>
		</div>
	</div>
</div>

<?php	do_action('eventon_after_main_content'); ?>

<?php //get_sidebar(); ?>
<?php get_footer(); ?>