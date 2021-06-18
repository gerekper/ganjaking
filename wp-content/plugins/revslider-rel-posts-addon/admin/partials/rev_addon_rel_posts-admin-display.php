<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/admin/partials
 */

	$rev_slider_addon_values = array();
	parse_str(get_option('rev_slider_addon_rel_posts'), $rev_slider_addon_values);


	// Available Sliders
	$slider = new RevSlider();
	$arrSliders = $slider->getArrSliders();
	$defSlider = "";
	
	$rel_sliders = get_option( "rev_slider_addon_rel_posts");
	parse_str($rel_sliders, $rel_sliders_array);

	// Available Post Types
	$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

	$output = 'objects'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'

	$post_types = get_post_types( $args, $output, $operator ); 
	$post_types_slugs = array("post");
?>

<div id="rev_addon_rel_posts_settings_slideout" class="rs-sbs-slideout-wrapper" style="display:none">
	<div class="rs-sbs-header">
		<div class="rs-sbs-step"><i class="eg-icon-cog"></i></div>
		<div class="rs-sbs-title"><?php _e('Related Posts', 'rev_addon_rel_posts'); ?></div>
		<div class="rs-sbs-close"><i class="eg-icon-cancel"></i></div>
	</div>
	<div class="rs-submenu-wrapper">
		<ul class="rs-submenu-tabs-source rs-submenu-tabs" style="display:inline-block; ">		
			<li id="source_based_settings_li" class="selected" data-content="#subcat-source-post"><?php _e('Posts', 'rev_addon_rel_posts'); ?></li>					
			<?php // types will be a list of the post type names
			$post_types_labels[0] = __('Posts', 'rev_addon_rel_posts');
        	foreach ( $post_types  as $post_type ) {
        		$post_types_slugs[] = $post_type->rewrite["slug"];
        		$post_types_labels[] = $post_type->labels->name;
			?>
				<li data-content="#subcat-source-<?php echo $post_type->rewrite["slug"]; ?>"><?php echo $post_type->labels->name; ?></li>
			<?php 
			} 

			?>
		</ul>
	</div>
	<div class="tp-clearfix"></div>
	<div class="rs-sbs-slideout-inner">

	<!-- Start Settings -->
	<form id="rs-addon-rel_post-form">
		<div class=" rs-dash-widget-registered">
			<?php // types will be a list of the post type names
				$post_type_counter = 0;
	        	foreach ( $post_types_slugs  as $post_type_slug ) {
	        		$post_type_label = $post_types_labels[$post_type_counter++];
	        		$rev_addon_rel_posts_display = $post_type_slug == 'post' ? 'display: block' : 'display:none';
				?>
					<div id="subcat-source-<?php echo $post_type_slug; ?>" class="subcat-wrapper" style="<?php echo $rev_addon_rel_posts_display;?>">				
						<div class="rs-dash-strong-content"><?php _e("Slider","rev_addon_rel_posts"); ?></div>
						<div><?php _e('Select a Slider of the <strong>"Specific Posts" source type</strong>:','rev_addon_rel_posts'); ?></div>				
						<div class="rs-dash-content-space"></div>
						<?php 	
							$slider_select_options = "";
							foreach($arrSliders as $sliderony){
								if($sliderony->getParam('source_type')=="specific_posts" || ( $post_type_slug == 'product' && $sliderony->getParam('source_type')=="woocommerce" ) ){
									$slider_select_options .= '<option value="'.$sliderony->getAlias().'" '.selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-slider'], $sliderony->getAlias(), 0 ).'>'. $sliderony->getTitle() . '</option>';
								}
							} ?>

							<select name="rs-addon-rel-<?php echo $post_type_slug; ?>-slider" class="rs-addon-rel-slider-switch rs-addon-rel-<?php echo $post_type_slug; ?>-slider" data-type="<?php echo $post_type_slug; ?>">
							 	<option value=""><?php _e('No Related Post Slider','rev_addon_rel_posts'); ?></option>
							 	<?php
									echo $slider_select_options;
						        ?>
					      	</select>
							
							<?php if ($slider_select_options==""){
								_e('There is no Slider of the <strong>"Specific Posts" source type</strong> available, learn more in our <a target="_blank" href="https://www.themepunch.com/revslider-doc/post-based-slider/">documentation</a>.','rev_addon_rel_posts');
							}  ?>
						<div id="rs-addon-rel-<?php echo $post_type_slug; ?>-details">	
					      	<div class="rs-dash-content-space"></div>
					      	<div class="rs-dash-content-with-icon">
								<div class="rs-dash-strong-content"><?php _e("Number of posts","rev_addon_rel_posts"); ?></div>
								<div><?php _e('How many posts to display?','rev_addon_rel_posts'); ?></div>				
							</div>
							<div class="rs-dash-content-space"></div>
					      	<input name="rs-addon-rel-<?php echo $post_type_slug; ?>-number" type="number" value="<?php echo isset($rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-number']) ? $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-number'] : 4; ?>">

					      	<div class="rs-dash-content-space"></div>
					      	<div class="rs-dash-content-with-icon">
								<div class="rs-dash-strong-content"><?php _e("Start Search in","rev_addon_rel_posts"); ?></div>
								<div><?php _e('Look for related posts by this','rev_addon_rel_posts'); ?></div>				
							</div>
							<div class="rs-dash-content-space"></div>
						    <select name="rs-addon-rel-<?php echo $post_type_slug; ?>-start-with">
								<?php $taxonomy_objects = get_object_taxonomies( $post_type_slug , 'objects' );
								foreach ($taxonomy_objects as $taxonomy_object_key => $taxonomy_object) { ?>
									<option value="<?php echo $taxonomy_object_key; ?>" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-start-with'], $taxonomy_object_key, 1 ); ?> ><?php echo $taxonomy_object->labels->name; ?></option>
								<?php } ?>
						    </select>
					      	<div class="rs-dash-content-space"></div>
					      	<div class="rs-dash-content-with-icon">
								<div class="rs-dash-strong-content"><?php _e("Fill items with","rev_addon_rel_posts"); ?></div>
								<div><?php _e('If the start search returns not enough items','rev_addon_rel_posts'); ?></div>				
							</div>
							<div class="rs-dash-content-space"></div>
					      	<select name="rs-addon-rel-<?php echo $post_type_slug; ?>-fill-with">
					      		<?php $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with'] = empty($rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with']) ? 'category' : $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with']; ?>
					      			<option value=""><?php _e('Nothing','rev_addon_rel_posts'); ?></option>
							 		<?php 
								 		$taxonomy_objects = get_object_taxonomies( $post_type_slug , 'objects' );
		   								foreach ($taxonomy_objects as $taxonomy_object_key => $taxonomy_object) { ?>
		   									<option value="<?php echo $taxonomy_object_key; ?>" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with'], $taxonomy_object_key, 1 ); ?> ><?php echo $taxonomy_object->labels->name; ?></option>
	   								<?php } ?>
							 	<option value="random" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with'], 'random', 1 ); ?> ><?php _e('Random '.$post_type_label,'rev_addon_rel_posts') ?></option>
							 	<option value="recent" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with'], 'recent', 1 ); ?> ><?php _e('Recent '.$post_type_label,'rev_addon_rel_posts'); ?></option>
							 	<option value="popular" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-fill-with'], 'popular', 1 ); ?> ><?php _e('Most Commented '.$post_type_label,'rev_addon_rel_posts'); ?></option>
					      	</select>
					      	<div class="rs-dash-content-space"></div>
					      	<div class="rs-dash-content-with-icon">
								<div class="rs-dash-strong-content"><?php _e("Caching","rev_addon_rel_posts"); ?></div>
								<div><?php _e('Cache results for how many seconds?','rev_addon_rel_posts'); ?></div>				
							</div>
							<div class="rs-dash-content-space"></div>
					      	<input name="rs-addon-rel-<?php echo $post_type_slug; ?>-caching" type="number" value="<?php echo isset($rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-caching']) ? $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-caching'] : 0; ?>">
							<div style="clear:both;" class="rs-dash-content-space"></div>
							<div class="rs-dash-content-with-icon">
								<div class="rs-dash-strong-content"><?php _e("Slider Position","rev_addon_rel_posts"); ?></div>
								<div><?php _e('Display the slider above or below the normal content','rev_addon_rel_posts'); ?></div>				
							</div>
							<div class="rs-dash-content-space"></div>
							<select name="rs-addon-rel-<?php echo $post_type_slug; ?>-position" class="rs-addon-rel-<?php echo $post_type_slug; ?>-position">
								<?php $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-position'] = empty($rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-position']) ? 'bottom' : $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-position']; ?>
								<option value="top" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-position'], 'top', 1 ) ?> ><?php _e('Above'); ?> </option>
							 	<option value="bottom" <?php selected( $rev_slider_addon_values['rs-addon-rel-'.$post_type_slug.'-position'], 'bottom', 1 ) ?> ><?php _e('Below'); ?> </option>
					      	</select>
					    </div>
				      	<div class="rs-dash-content-space"></div>
					</div> <!-- End "<?php echo $post_type_slug; ?>" Settings -->		
				<?php 
				} // end foreach
			?>
		</div>
		<span id="ajax_rev_slider_addon_rel_posts_nonce" class="hidden"><?php echo wp_create_nonce( 'ajax_rev_slider_addon_rel_posts_nonce' ) ?></span>
		<div class="rs-dash-bottom-wrapper">
			<span style="display:none" id="rs_addon-rel_posts-wait" class="loader_round">Please Wait...</span>					
			<a href="javascript:void(0);" id="rs-addon-rel_posts-save" class="rs-dash-button">Save</a>
		</div>		
	</form>
<!-- End Settings -->

</div>	</div>