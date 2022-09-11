<?php	
/*
 *	The template for displaying event categoroes 
 *
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@version: 4.0.3
 */
	
	
	global $eventon;

	get_header();

	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );
	$term = get_term_by( 'slug', $term, $tax );

	$lang = isset($_GET['lang']) ? sanitize_text_field( $_GET['lang'] ): 'L1';

	$tax_name = EVO()->frontend->get_localized_event_tax_names_by_slug($tax, $lang);
	$term_name = evo_lang_get('evolang_'. $tax .'_'. $term->term_id, $term->name, $lang);

	do_action('eventon_before_main_content');
?>

<div class='wrap evotax_term_card evotax_term_card container'>
	
	<div id='primary' class='content-area'>

		<header class="page-header ">
			<h1 class="page-title"><?php echo $tax_name.': '.$term_name; ?></h1>
			<?php if ( category_description() ) : // Show an optional category description ?>
			<div class="page-meta"><?php echo category_description(); ?></div>
			<?php endif; ?>
		</header><!-- .archive-header -->
		
		<div class='entry-content'>
			<div class='<?php echo apply_filters('evotax_template_content_class', 'eventon site-main');?>'>
			
				<div class="evotax_term_details endborder_curves" >						
					
					<h2 class="tax_term_name">
						<i><?php echo $tax_name;?></i>
						<span><?php echo $term_name;?></span>
					</h2>
					<div class='tax_term_description'><?php echo category_description();?></div>
				</div>

			
				<?php 
					$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';
					
					$shortcode = apply_filters('evo_tax_archieve_page_shortcode', 
						'[add_eventon_list number_of_months="5" '.$tax.'='.$term->term_id.' hide_mult_occur="no" hide_empty_months="yes" lang="'.$lang.'" eventtop_style="'. $eventtop_style.'"]', 
						$tax,
						$term->term_id
					);
					echo do_shortcode($shortcode);
				?>
			</div>
		</div>
	</div>
	
	<?php get_sidebar(); ?>
	
</div>

<?php	do_action('eventon_after_main_content'); ?>


<?php get_footer(); ?>