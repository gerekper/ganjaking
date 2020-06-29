<?php 
/**
 * Article Reactions Shortcode
 * *
 * @since      1.0.2
 * 
 */

add_shortcode( 'betterdocs_article_reactions', 'betterdocs_article_reactions' );
function betterdocs_article_reactions( $atts, $content = null ) {
	$reactions_text = get_theme_mod('betterdocs_post_reactions_text', esc_html__('What are your Feelings', 'betterdocs-pro'));
?>
	<div class="betterdocs-article-reactions">
		<div class="betterdocs-article-reactions-heading">
			<?php if($reactions_text){
				echo '<h5>'.esc_html($reactions_text).'</h5>';
			} ?>
		</div>
		<ul class="betterdocs-article-reaction-links">
			<li><a class="betterdocs-feelings" data-feelings="happy" href="#">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
				<path class="st0" d="M10,0.1c-5.4,0-9.9,4.4-9.9,9.8c0,5.4,4.4,9.9,9.8,9.9c5.4,0,9.9-4.4,9.9-9.8C19.9,4.5,15.4,0.1,10,0.1z
					M13.3,6.4c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C11.8,7.1,12.5,6.4,13.3,6.4z M6.7,6.4
					c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C5.2,7.1,5.9,6.4,6.7,6.4z M10,16.1c-2.6,0-4.9-1.6-5.8-4
					l1.2-0.4c0.7,1.9,2.5,3.2,4.6,3.2s3.9-1.3,4.6-3.2l1.2,0.4C14.9,14.5,12.6,16.1,10,16.1z"/>
				<path class="st1" d="M-6.6-119.7c-7.1,0-12.9,5.8-12.9,12.9s5.8,12.9,12.9,12.9s12.9-5.8,12.9-12.9S0.6-119.7-6.6-119.7z
					M-2.3-111.4c1.1,0,2,0.9,2,2c0,1.1-0.9,2-2,2c-1.1,0-2-0.9-2-2C-4.3-110.5-3.4-111.4-2.3-111.4z M-10.9-111.4c1.1,0,2,0.9,2,2
					c0,1.1-0.9,2-2,2c-1.1,0-2-0.9-2-2C-12.9-110.5-12-111.4-10.9-111.4z M-6.6-98.7c-3.4,0-6.4-2.1-7.6-5.3l1.6-0.6
					c0.9,2.5,3.3,4.2,6,4.2s5.1-1.7,6-4.2L1-104C-0.1-100.8-3.2-98.7-6.6-98.7z"/>
				</svg>
			</a></li>
			<li><a class="betterdocs-feelings" data-feelings="normal"  href="#">
			<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
				viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
			<path class="st0" d="M10,0.2c-5.4,0-9.8,4.4-9.8,9.8s4.4,9.8,9.8,9.8s9.8-4.4,9.8-9.8S15.4,0.2,10,0.2z M6.7,6.5
				c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5C5.9,9.5,5.2,8.9,5.2,8C5.2,7.2,5.9,6.5,6.7,6.5z M14.2,14.3H5.9
				c-0.3,0-0.6-0.3-0.6-0.6c0-0.3,0.3-0.6,0.6-0.6h8.3c0.3,0,0.6,0.3,0.6,0.6C14.8,14,14.5,14.3,14.2,14.3z M13.3,9.5
				c-0.8,0-1.5-0.7-1.5-1.5c0-0.8,0.7-1.5,1.5-1.5c0.8,0,1.5,0.7,1.5,1.5C14.8,8.9,14.1,9.5,13.3,9.5z"/>
			</svg>
			</a></li>
			<li><a class="betterdocs-feelings" data-feelings="sad"  href="#">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
				<circle class="st0" cx="27.5" cy="0.6" r="1.9"/>
				<circle class="st0" cx="36" cy="0.6" r="1.9"/>
				<path class="st1" d="M10,0.3c-5.4,0-9.8,4.4-9.8,9.8s4.4,9.8,9.8,9.8s9.8-4.4,9.8-9.8S15.4,0.3,10,0.3z M13.3,6.6
					c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C11.8,7.3,12.4,6.6,13.3,6.6z M6.7,6.6c0.8,0,1.5,0.7,1.5,1.5
					c0,0.8-0.7,1.5-1.5,1.5C5.9,9.6,5.2,9,5.2,8.1C5.2,7.3,5.9,6.6,6.7,6.6z M14.1,15L14.1,15c-0.2,0-0.4-0.1-0.5-0.2
					c-0.9-1-2.2-1.7-3.7-1.7s-2.8,0.6-3.7,1.7C6.2,14.9,6,15,5.9,15h0c-0.6,0-0.8-0.6-0.5-1.1c1.1-1.3,2.8-2.1,4.6-2.1
					c1.8,0,3.5,0.8,4.6,2.1C15,14.3,14.7,15,14.1,15z"/>
				</svg>
			</a></li>
		</ul>
	</div> <!-- Social Share end-->
<?php }


/**
 * Category box layout 3
 * *
 * @since      1.0.2
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode( 'betterdocs_category_box_l3', 'betterdocs_category_box_l3' );
function betterdocs_category_box_l3( $atts, $content = null ) {
	ob_start();
	$column_number = BetterDocs_DB::get_settings('column_number');
	$nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
	$get_args = shortcode_atts(
		array(
            'post_type' => 'docs',
			'category' => 'doc_category',
			'column' => '',
			'nested_subcategory' => '',
			'terms' => ''
		),
		$atts
	);

	$terms_object = array(
		'taxonomy' => $get_args['category'],
		'orderby' => 'name',
		'hide_empty' => true,
		'parent' => 0
	);

	if ( $nested_subcategory == 1 || $get_args['nested_subcategory'] === "true" || $get_args['terms'] ) {
		unset($terms_object['parent']);
	}

	if ( $get_args['terms'] ) {
		$terms_object['include'] = explode(',', $get_args['terms']);
		$terms_object['orderby'] = 'include';
	}

	$taxonomy_objects = get_terms($terms_object);

	if ( $taxonomy_objects && ! is_wp_error( $taxonomy_objects ) ) :
		$class = ['betterdocs-categories-wrap betterdocs-category-box betterdocs-category-box-pro pro-layout-3 ash-bg layout-flex'];
		if(isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
			$class[] = 'docs-col-'.$get_args['column'];
		} else {
			$class[] = 'docs-col-'.$column_number;
		}
		

	?>
	<div class="<?php echo implode(' ',$class) ?>">
		<?php
		// display category grid by order
		foreach ( $taxonomy_objects as $term ) {
			$term_id = $term->term_id;
			if ( $term->count != 0 ) {

				// set active category class in single page	
				$wrap_class = 'docs-single-cat-wrap';
			?>
				<a href="<?php echo get_term_link( $term->slug, $get_args['category'] ) ?>" class="<?php echo esc_attr($wrap_class) ?>">
					<?php
					$cat_icon_id = get_term_meta( $term_id, 'doc_category_image-id', true);
					if($cat_icon_id){
						echo wp_get_attachment_image ( $cat_icon_id, 'thumbnail' );
					} else {
						echo '<img class="docs-cat-icon" src="'.BETTERDOCS_ADMIN_URL.'assets/img/betterdocs-cat-icon.svg" alt="">';
					}
					echo '<div class="title-count">';
					echo '<h3 class="docs-cat-title">'.$term->name.'</h3>';
					echo wp_sprintf('<span>%s ' . __('articles', 'betterdocs-pro').'</span>', $term->count);
					echo '</div>';
					?>	
				</a>
			<?php
			}
		}
		?>
	</div>
	<?php
	endif;
	return ob_get_clean();
}


/**
 * Get the category grid with docs list.
 * *
 * @since      1.0.2
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode( 'betterdocs_category_grid_2', 'betterdocs_category_grid_2' );
function betterdocs_category_grid_2( $atts, $content = null ) {
	ob_start();
	$column_number = BetterDocs_DB::get_settings('column_number');
	$posts_number = BetterDocs_DB::get_settings('posts_number');
	$alphabetically_order_post = BetterDocs_DB::get_settings('alphabetically_order_post');
	$nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
	$exploremore_btn = BetterDocs_DB::get_settings('exploremore_btn');
	$exploremore_btn_txt = BetterDocs_DB::get_settings('exploremore_btn_txt');
	$get_args = shortcode_atts (
		array(
            'sidebar_list' => false,
            'post_type' => 'docs',
			'category' => 'doc_category',
			'count' => true,
			'icon' => true,
			'masonry' => '',
			'column' => '',
			'posts' => '',
			'nested_subcategory' => '',
			'terms' => ''
		),
		$atts
	);

	$terms_object = array(
		'taxonomy' => $get_args['category'],
		'orderby' => 'name', 
		'hide_empty' => true,
		'parent' => 0
	);

	if ( $get_args['terms'] ) {
		unset($terms_object['parent']);
		$terms_object['include'] = explode(',', $get_args['terms']);
		$terms_object['orderby'] = 'include';
	}

	$taxonomy_objects = get_terms($terms_object);

	if ( $taxonomy_objects && ! is_wp_error( $taxonomy_objects ) ) :
		$class = ['betterdocs-categories-wrap category-grid white-bg'];
		if(!is_singular('docs') && !is_tax('doc_category') && !is_tax('doc_tag')){
			$class[] = 'layout-flex';
			if(isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])){
				$class[] = 'docs-col-'.$get_args['column'];
			}else{
				$class[] = 'docs-col-'.$column_number;
			}
		}

	?>
	<div class="<?php echo implode(' ',$class) ?>">
		<?php
		// get single page category id
		if(is_single()) {
			$term_list = wp_get_post_terms(get_the_ID(), 'doc_category', array("fields" => "all"));
			$category_id = array_column($term_list, 'term_id');
			$page_cat = get_the_ID();
		} else {
			$category_id = '';
			$page_cat = '';
		}

		$taxonomy_first_row = array_slice($taxonomy_objects, 0, $column_number);
		$taxonomy_all = array_slice($taxonomy_objects, $column_number);

		echo '<div class="betterdocs-categories-wrap wrap-top-row layout-flex">';
		foreach ( $taxonomy_first_row as $term ) {
			if ( '0' == ( $term->count && $term->parent ) ) {
				echo '<a href="'.get_term_link( $term->slug, $get_args['category'] ).'" class="docs-single-cat-wrap docs-cat-list-2 docs-cat-list-2-box">';
				echo '<div class="docs-cat-list-2-box-content">';
					$term_id = $term->term_id;	
					$cat_icon_id = get_term_meta( $term_id, 'doc_category_image-id', true);
					if($cat_icon_id){
						echo wp_get_attachment_image ( $cat_icon_id, 'thumbnail' );
					} else {
						echo '<img class="docs-cat-icon" src="'.BETTERDOCS_ADMIN_URL.'assets/img/betterdocs-cat-icon.svg" alt="">';
					}
					echo '<div class="title-count">';
					echo '<h3 class="docs-cat-title">'.$term->name.'</h3>';
					echo wp_sprintf('<span>%s ' . __('articles', 'betterdocs-pro').'</span>', $term->count);
					echo '</div>';
				echo '</div>';
				echo '</a>';
			}
		}
		echo '</div>';

		// display category grid by order
		foreach ( $taxonomy_all as $term ) {
			if ( '0' == ( $term->count && $term->parent ) ) {
				$term_id = $term->term_id;
			?>
				<div class="docs-single-cat-wrap docs-cat-list-2 docs-cat-list-2-items">
					<div class="docs-cat-title-wrap">
						<a href="<?php echo get_term_link( $term->slug, $get_args['category'] ) ?>"><h3 class="docs-cat-title"><?php echo $term->name ?></h3></a>
					</div>
					<div class="docs-item-container">
						<?php 
							$args = array(
								'post_type'   => $get_args['post_type'],
								'post_status' => 'publish',
								'tax_query' => array(
									array(
										'taxonomy' => $get_args['category'],
										'field'    => 'slug',
										'terms'    => $term->slug,
										'operator'          => 'AND',
            							'include_children'  => false
									),
								),
							);
							if(isset($get_args['posts']) && $get_args['posts'] == true && is_numeric($get_args['posts'])){
								$posts_number = $get_args['posts'];
							}
							$args['posts_per_page'] = $posts_number;

							if($alphabetically_order_post == 1) {
								$args['orderby'] = 'title';
								$args['order'] = 'ASC';
							}

							$args = apply_filters( 'betterdocs_articles_args', $args, $term->term_id );
						
							$post_query = new WP_Query( $args );
							if ( $post_query->have_posts() ) :

								echo '<ul>';
								while ( $post_query->have_posts() ) : $post_query->the_post();
									$attr = ['href="'.get_the_permalink().'"'];
									if($page_cat === get_the_ID()){
										$attr[] = 'class="active"';
									}
									echo '<li><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.86em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1536 1792"><path d="M1468 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28H96q-40 0-68-28t-28-68V96q0-40 28-68T96 0h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528V640H992q-40 0-68-28t-28-68V128H128v1536h1280zM384 800q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z" fill="#626262"/></svg><a '.implode(' ',$attr).'>'.get_the_title().'</a></li>';
								endwhile;
								
								echo '</ul>';
							
							endif;
							wp_reset_query();

							// Sub category query
							if($nested_subcategory == 1 || $get_args['nested_subcategory'] === "true") {

								if ( $get_args['nested_subcategory'] === "false" ) {
									return;
								}

								$taxonomies = array('doc_category');
								$sub_taxonomies = array('parent' => $term->term_id,'orderby' => 'name');
								$sub_categories = get_terms($taxonomies, $sub_taxonomies);
								if($sub_categories){
									
									foreach($sub_categories as $sub_category) {
										echo '<span class="docs-sub-cat-title">
										<svg class="toggle-arrow arrow-right" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.48em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 608 1280"><g transform="translate(608 0) scale(-1 1)"><path d="M595 288q0 13-10 23L192 704l393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10L23 727q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></g></svg>
										<svg class="toggle-arrow arrow-down" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.8em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 1280"><path d="M1011 480q0 13-10 23L535 969q-10 10-23 10t-23-10L23 503q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393l393-393q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
										<a href="#">'.$sub_category->name.'</a></span>';
										echo '<ul class="docs-sub-cat">';
										$sub_args = array(
											'post_type'   => $get_args['post_type'],
											'post_status' => 'publish',
											'tax_query' => array(
												array(
													'taxonomy' => $get_args['category'],
													'field'    => 'slug',
													'terms'    => $sub_category->slug,
													'operator'          => 'AND',
            										'include_children'  => false
												),
											),
										);
										if($alphabetically_order_post == 1) {
											$sub_args['orderby'] = 'title';
											$sub_args['order'] = 'ASC';
										}
										$sub_args['posts_per_page'] = -1;
										$sub_args = apply_filters( 'betterdocs_sub_cat_articles_args', $sub_args, $sub_category->term_id );
										$sub_post_query = new WP_Query( $sub_args );
										if ( $sub_post_query->have_posts() ) :
											while ( $sub_post_query->have_posts() ) : $sub_post_query->the_post();
												$sub_attr = ['href="'.get_the_permalink().'"'];
												echo '<li class="sub-list"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="0.86em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1536 1792"><path d="M1468 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28H96q-40 0-68-28t-28-68V96q0-40 28-68T96 0h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528V640H992q-40 0-68-28t-28-68V128H128v1536h1280zM384 800q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9H416q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"/></svg><a '.implode(' ',$sub_attr).'>'.get_the_title().'</a></li>';
											endwhile;
										endif;
										wp_reset_query();
										echo '</ul>';
									}
									
								}
							}

							if($exploremore_btn == 1 && !is_singular('docs') && !is_tax('doc_category') && !is_tax('doc_tag')){
								echo '<a class="docs-cat-link-btn" href="'.get_term_link( $term->slug, $get_args['category'] ).'">'.esc_html($exploremore_btn_txt).' </a>';
							}
						?>
					</div>
				</div>
			<?php
			}
		}
		?>
	</div>
	<?php
	endif;
	return ob_get_clean();
}
/* Describe what the code snippet does so you can remember later on */
//add_action('wp_head', 'betterdocs_helpshelf_integration');
function betterdocs_helpshelf_integration(){
?>
<script type="text/javascript" id="hs-loader">
    window.helpShelfSettings = {
        "siteKey": "GQ2xBVCe",
        "userId": "",
        "userEmail": "",
        "userFullName": "",
        "userAttributes": {
            // Add custom user attributes here
        },
        "companyAttributes": {
            // Add custom company attributes here
        }
    };

    (function() {var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://s3.amazonaws.com/helpshelf-production/gen/loader/GQ2xBVCe.min.js";
    po.onload = po.onreadystatechange = function() {var rs = this.readyState; if (rs && rs != "complete" && rs != "loaded") return;HelpShelfLoader = new HelpShelfLoaderClass(); HelpShelfLoader.identify(window.helpShelfSettings);};
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);})();
</script>
<?php
};
?>