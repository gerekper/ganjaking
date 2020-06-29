<?php 
	if ( ! defined( 'ABSPATH' ) ) { exit; }
		
	global $general_options,$post_type_options;
	
	$post_elements=array();
	$post_elements[]='tp_blog_list';
		$post_elements[]='tp_clients_list';
		$post_elements[]='tp_testimonial_slider';
		$post_elements[]='tp_tm_list';
		$post_elements[]='tp_portfolio_list';
	if(class_exists('woocommerce')) {
		$post_elements[]='tp_product_list';
	}
	if(function_exists('vc_add_param')){
		require_once(THEPLUS_PLUGIN_PATH.'vc_elements/vc_param/vc_param.php');
		$check_elements=pt_plus_get_option('general','check_elements');
		if(isset($check_elements) && !empty($check_elements)){
			foreach($check_elements as $value) {
				foreach(glob(THEPLUS_PLUGIN_PATH.'vc_elements/map_shortcodes/'.$value.'.php') as $shortcode) {
					require_once($shortcode);
				}
			}
		}else{
		foreach(glob(THEPLUS_PLUGIN_PATH.'vc_elements/map_shortcodes/*.php') as $shortcode) {
				require_once($shortcode);
			}
		}
		if(isset($post_elements) && !empty($post_elements)){
			foreach($post_elements as $value) {
				foreach(glob(THEPLUS_PLUGIN_PATH.'vc_elements/post_shortcodes/'.$value.'.php') as $shortcode) {
					require_once($shortcode);
				}
			}
		}
	}
add_image_size( 'tp-image-grid', 700, 700, true);

class Pt_plus_MetaBox {
	
	public static function get($name) {
		global $post;
		
		if (isset($post) && !empty($post->ID)) {
			return get_post_meta($post->ID, $name, true);
		}
		
		return false;
	}
}
function pt_plus_get_option($options_type,$field){
	$general_options=get_option( 'general_options' );
	$post_type_options=get_option( 'post_type_options' );
	$values='';
	if($options_type=='general'){
		if(isset($general_options[$field]) && !empty($general_options[$field])){
			$values=$general_options[$field];
		}
	}
	if($options_type=='post_type'){
		if(isset($post_type_options[$field]) && !empty($post_type_options[$field])){
			$values=$post_type_options[$field];
		}
	}
	return $values;
}
if(!function_exists('pt_plus_check_api_status')){
	function pt_plus_check_api_status() {
		$option_name = 'plus_key';
		if ( get_option( $option_name ) !== false ) {
			if(get_option( $option_name )=='1'){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
function pt_plus_testimonial_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$testi_post_type=$post_type_options['testimonial_post_type'];
	$post_name='theplus_testimonial';
	if(isset($testi_post_type) && !empty($testi_post_type)){
		if($testi_post_type=='themes'){
			$post_name=pt_plus_get_option('post_type','testimonial_theme_name');
		}elseif($testi_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','testimonial_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=pt_plus_get_option('post_type','testimonial_plugin_name');
			}
		}elseif($testi_post_type=='themes_pro'){
			$post_name='testimonial';
		}
	}else{
		$post_name='theplus_testimonial';
	}
	return $post_name;
}
function pt_plus_testimonial_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$testi_post_type=$post_type_options['testimonial_post_type'];
	$taxonomy_name='theplus_testimonial_cat';
	if(isset($testi_post_type) && !empty($testi_post_type)){
		if($testi_post_type=='themes'){
			$taxonomy_name=pt_plus_get_option('post_type','testimonial_category_name');
		}else if($testi_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','testimonial_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$taxonomy_name=pt_plus_get_option('post_type','testimonial_category_plugin_name');
			}
		}elseif($testi_post_type=='themes_pro'){
			$taxonomy_name='testimonial_category';
		}
	}else{
		$taxonomy_name='theplus_testimonial_cat';
	}
	return $taxonomy_name;
}
function pt_plus_team_member_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$team_post_type=$post_type_options['team_member_post_type'];
	$post_name='theplus_team_member';
	if(isset($team_post_type) && !empty($team_post_type)){
		if($team_post_type=='themes'){
			$post_name=pt_plus_get_option('post_type','team_member_theme_name');
		}elseif($team_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','team_member_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=pt_plus_get_option('post_type','team_member_plugin_name');
			}
		}elseif($team_post_type=='themes_pro'){
			$post_name='team_member';
		}
	}else{
		$post_name='theplus_team_member';
	}
	return $post_name;
}
function pt_plus_team_member_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$team_post_type=$post_type_options['team_member_post_type'];
	$taxonomy_name='theplus_team_member_cat';
	if(isset($team_post_type) && !empty($team_post_type)){
		if($team_post_type=='themes'){
			$taxonomy_name=pt_plus_get_option('post_type','team_member_category_name');
		}else if($team_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','team_member_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$taxonomy_name=pt_plus_get_option('post_type','team_member_category_plugin_name');
			}
		}elseif($team_post_type=='themes_pro'){
			$taxonomy_name='team_member_category';
		}
	}else{
		$taxonomy_name='theplus_team_member_cat';
	}
	return $taxonomy_name;
}
function pt_plus_client_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$client_post_type=$post_type_options['client_post_type'];
	$post_name='theplus_clients';
	if(isset($client_post_type) && !empty($client_post_type)){
		if($client_post_type=='themes'){
			$post_name=pt_plus_get_option('post_type','client_theme_name');
		}elseif($client_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','client_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=pt_plus_get_option('post_type','client_plugin_name');
			}
		}elseif($client_post_type=='themes_pro'){
			$post_name='clients';
		}
	}else{
		$post_name='theplus_clients';
	}
	return $post_name;
}
function pt_plus_client_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$client_post_type=$post_type_options['client_post_type'];
	$post_name='theplus_clients_cat';
	if(isset($client_post_type) && !empty($client_post_type)){
		if($client_post_type=='themes'){
			$post_name=pt_plus_get_option('post_type','client_category_name');
		}else if($client_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','client_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=pt_plus_get_option('post_type','client_category_plugin_name');
			}
		}elseif($client_post_type=='themes_pro'){
			$post_name='clients_category';
		}
	}else{
		$post_name='theplus_clients_cat';
	}
	return $post_name;
}

function pt_plus_portfolio_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$portfolio_post_type=$post_type_options['portfolio_post_type'];
	$post_name='theplus_portfolio';
	if(isset($portfolio_post_type) && !empty($portfolio_post_type)){
		if($portfolio_post_type=='themes'){
			$post_name=pt_plus_get_option('post_type','portfolio_theme_name');
		}elseif($portfolio_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','portfolio_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=pt_plus_get_option('post_type','portfolio_plugin_name');
			}
		}elseif($portfolio_post_type=='themes_pro'){
			$post_name='portfolio';
		}
	}else{
		$post_name='theplus_portfolio';
	}
	return $post_name;
}
function pt_plus_portfolio_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$portfolio_post_type=$post_type_options['portfolio_post_type'];
	$post_name='theplus_portfolio_category';
	if(isset($portfolio_post_type) && !empty($portfolio_post_type)){
		if($portfolio_post_type=='themes'){
			$post_name=pt_plus_get_option('post_type','portfolio_category_name');
		}else if($portfolio_post_type=='plugin'){
			$get_name=pt_plus_get_option('post_type','portfolio_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=pt_plus_get_option('post_type','portfolio_category_plugin_name');
			}
		}elseif($portfolio_post_type=='themes_pro'){
			$post_name='portfolio_category';
		}
	}else{
		$post_name='theplus_portfolio_category';
	}
	return $post_name;
}
function pt_plus_excerpt($limit) {
	 if(method_exists('WPBMap', 'addAllMappedShortcodes')) {
            WPBMap::addAllMappedShortcodes();
        }
	global $post;
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  // $excerpt['rendered'] = apply_filters( 'the_content', $post->post_content );
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}
function pt_plus_getFontsData( $fontsString ) {   
 
    $googleFontsParam = new Vc_Google_Fonts();      
    $fieldSettings = array();
    $fontsData = strlen( $fontsString ) > 0 ? $googleFontsParam->_vc_google_fonts_parse_attributes( $fieldSettings, $fontsString ) : '';
    return $fontsData;
     
}
 
 function pt_plus_googleFontsStyles( $fontsData ) {
     
    $fontFamily = explode( ':', $fontsData['values']['font_family'] );
    $styles[] = 'font-family:' . $fontFamily[0];
    $fontStyles = explode( ':', $fontsData['values']['font_style'] );
    $styles[] = 'font-weight:' . $fontStyles[1];
    $styles[] = 'font-style:' . $fontStyles[2];
     
    $inline_style = '';     
    foreach( $styles as $attribute ){           
        $inline_style .= $attribute.'; ';       
    }   
     
    return $inline_style;
     
}
 
function pt_plus_enqueueGoogleFonts( $fontsData ) {
     
    $settings = get_option( 'wpb_js_google_fonts_subsets' );
    if ( is_array( $settings ) && ! empty( $settings ) ) {
        $subsets = '&subset=' . implode( ',', $settings );
    } else {
        $subsets = '';
    }
	
    if ( isset( $fontsData['values']['font_family'] ) ) {
        wp_enqueue_style( 
            'vc_google_fonts_' . vc_build_safe_css_class( $fontsData['values']['font_family'] ), 
            '//fonts.googleapis.com/css?family=' . $fontsData['values']['font_family'] . $subsets
        );
    }
}
function pt_plus_loading_image_grid($postid='',$type=''){
	global $post;
	$content_image='';
	if($type!='background'){
		
		$image_url=THEPLUS_PLUGIN_URL.'vc_elements/images/placeholder-grid.jpg';
			$content_image='<img src="'.esc_url($image_url).'" alt="'.esc_attr(get_the_title()).'"/>';
			return $content_image;
	}elseif($type=='background'){
		$image_url=THEPLUS_PLUGIN_URL.'vc_elements/images/placeholder-grid.jpg';
		$data_src='style="background:url('.esc_url($image_url).') #f7f7f7;" ';
		return $data_src;
	}
}
function pt_plus_loading_bg_image($postid=''){
	global $post;
	$content_image='';
	if(!empty($postid)){
		$featured_image=get_the_post_thumbnail_url($postid,'full');
		$content_image='style="background:url('.esc_url($featured_image).') #f7f7f7;"';
		return $content_image;
	}else{
		return $content_image;
	}
}
if(!function_exists('pt_plus_gradient_color')){
	function pt_plus_gradient_color($overlay_color1,$overlay_color2,$overlay_gradient) {
$gradient_style='';
if($overlay_gradient=='horizontal'){
	$gradient_style ='background: -moz-linear-gradient(left, '.esc_attr($overlay_color1).' 0%, '.esc_attr($overlay_color2).' 100%);background: -webkit-gradient(linear, left top, right top, color-stop(0%,'.esc_attr($overlay_color1).'), color-stop(100%,'.esc_attr($overlay_color2).'));background: -webkit-linear-gradient(left, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -o-linear-gradient(left, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -ms-linear-gradient(left, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: linear-gradient(to right, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);';
}elseif($overlay_gradient=='vertical'){
 $gradient_style ='background: -moz-linear-gradient(top, '.esc_attr($overlay_color1).' 0%, '.esc_attr($overlay_color2).' 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'.esc_attr($overlay_color1).'), color-stop(100%,'.esc_attr($overlay_color2).'));background: -webkit-linear-gradient(top, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -o-linear-gradient(top, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -ms-linear-gradient(top, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: linear-gradient(to bottom, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);';
}elseif($overlay_gradient=='diagonal'){
$gradient_style ='background: -moz-linear-gradient(45deg, '.esc_attr($overlay_color1).' 0%, '.esc_attr($overlay_color2).' 100%);background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,'.esc_attr($overlay_color1).'), color-stop(100%,'.esc_attr($overlay_color2).'));background: -webkit-linear-gradient(45deg, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -o-linear-gradient(45deg, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -ms-linear-gradient(45deg, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: linear-gradient(45deg, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);';
}elseif($overlay_gradient=='radial'){
 $gradient_style ='background: -moz-radial-gradient(center, ellipse cover, '.esc_attr($overlay_color1).' 0%, '.esc_attr($overlay_color2).' 100%);background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,'.esc_attr($overlay_color1).'), color-stop(100%,'.esc_attr($overlay_color2).'));background: -webkit-radial-gradient(center, ellipse cover, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -o-radial-gradient(center, ellipse cover, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: -ms-radial-gradient(center, ellipse cover, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);background: radial-gradient(ellipse at center, '.esc_attr($overlay_color1).' 0%,'.esc_attr($overlay_color2).' 100%);';
}
	   return $gradient_style; 
	}
}
	/*----------------------------load more posts ---------------------------*/
function pt_plus_more_post_ajax(){
		global $post;
		ob_start();
		$post_type=$_POST["post_type"];
		$post_load=$_POST["post_load"];
		$texonomy_category=$_POST["texonomy_category"];
		$layout=$_POST["layout"];
		$offset = $_POST["offset"];
		$display_post = $_POST["display_post"];
		$category=$_POST["category"];
		$desktop_column=$_POST["desktop_column"];
		$tablet_column=$_POST["tablet_column"];
		$mobile_column=$_POST["mobile_column"];
		$style= $_POST["style"];
		$filter_category=$_POST["filter_category"];
		$order_by=$_POST["order_by"];
		$post_sort=$_POST["post_sort"];
		$animated_columns=$_POST["animated_columns"];
		$post_load_more=$_POST["post_load_more"];
		
		$desktop_class='vc_col-md-'.$desktop_column;
		$tablet_class='vc_col-sm-'.$tablet_column;
		$mobile_class='vc_col-xs-'.$mobile_column;
		$i=0;$j=1;
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => $post_load_more,
			$texonomy_category => $category,
			'offset' => $offset,
			'orderby'	=>$order_by,
			'post_status' =>'publish',
			'order'	=>$post_sort
		);
		
		if($layout=='metro'){
			$desktop_class=$tablet_class=$mobile_class='';
		}
		$i=$offset+1;
		$loop = new WP_Query($args);
			if ( $loop->have_posts() ) :
				while ($loop->have_posts()) {
					$loop->the_post();					
					if($post_load=='portfolios'){
						include THEPLUS_PLUGIN_PATH ."vc_elements/ajax-load-post/portfolio-style.php";
					}
					if($post_load=='blogs'){
						include THEPLUS_PLUGIN_PATH ."vc_elements/ajax-load-post/blog-style.php";
					}
					if($post_load=='clients'){
						include THEPLUS_PLUGIN_PATH ."vc_elements/ajax-load-post/client-style.php";
					}
					if($post_load=='product'){
						include THEPLUS_PLUGIN_PATH ."vc_elements/ajax-load-post/product-style.php";
					}
					$i++;
				}
				$content = ob_get_contents();
				ob_end_clean();
			endif;
		wp_reset_postdata();
		echo $content;
		exit;
		ob_end_clean();
	}
add_action('wp_ajax_pt_plus_more_post','pt_plus_more_post_ajax');
add_action('wp_ajax_nopriv_pt_plus_more_post', 'pt_plus_more_post_ajax');

function pt_plus_key_notice_ajax(){
	if ( get_option( 'theplus-notice-dismissed' ) !== false ) {
		update_option( 'theplus-notice-dismissed', '1' );
	} else {
		$deprecated = null;
		$autoload = 'no';
		add_option( 'theplus-notice-dismissed','1', $deprecated, $autoload );
	}
}
add_action('wp_ajax_plus_key_notice','pt_plus_key_notice_ajax');
add_action('wp_ajax_nopriv_plus_key_notice', 'pt_plus_key_notice_ajax');
function pt_plus_pagination($pages = '', $range = 4)
	{  
		$showitems = ($range * 2)+1;  
		
		global $paged;
		if(empty($paged)) $paged = 1;
		
		if($pages == '')
		{
			global $wp_query;
			if( $wp_query->max_num_pages <= 1 )
			return;
		
			$pages = $wp_query->max_num_pages;
			/*if(!$pages)
			{
				$pages = 1;
			}*/
			$pages = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		}   
		
		if(1 != $pages)
		{
			$paginate ="<div class=\"pt_theplus-pagination\">";
			if ( get_previous_posts_link() ){
				$paginate .= '<div class="paginate-prev">'.get_previous_posts_link('<<').'</div>';
			}
			
			for ($i=1; $i <= $pages; $i++)
			{
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				{
					$paginate .= ($paged == $i)? "<span class=\"current\">".esc_html($i)."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".esc_html($i)."</a>";
				}
			}
			if ( get_next_posts_link() ){
				$paginate .='<div class="paginate-next">'.get_next_posts_link('>>',1).'</div>';
			}
			$paginate .="</div>\n";
			return $paginate;
		}
	}
 function pt_plus_getImageSquareSize( $img_id, $img_size ) {
		if ( preg_match_all( '/(\d+)x(\d+)/', $img_size, $sizes ) ) {
			$exact_size = array(
				'width' => isset( $sizes[1][0] ) ? $sizes[1][0] : '0',
				'height' => isset( $sizes[2][0] ) ? $sizes[2][0] : '0',
			);
		} else {
			$image_downsize = image_downsize( $img_id, $img_size );
			$exact_size = array(
				'width' => $image_downsize[1],
				'height' => $image_downsize[2],
			);
		}
		$exact_size_int_w = (int) $exact_size['width'];
		$exact_size_int_h = (int) $exact_size['height'];
		if ( isset( $exact_size['width'] ) && $exact_size_int_w !== $exact_size_int_h ) {
			$img_size = $exact_size_int_w > $exact_size_int_h
				? $exact_size['height'] . 'x' . $exact_size['height']
				: $exact_size['width'] . 'x' . $exact_size['width'];
		}

		return $img_size;
	}
/*----------------------Header breadcurmbss------------------------------*/
	function pt_plus_breadcrumbs() {

    /* === OPTIONS === */
    $text['home']     = __('Home', 'pt_theplus'); 
    $text['category'] = __('Archive by "%s"', 'pt_theplus'); 
    $text['search']   = __('Search Results for "%s" Query', 'pt_theplus');
    $text['tag']      = __('Posts Tagged "%s"', 'pt_theplus');
    $text['author']   = __('Articles Posted by %s', 'pt_theplus');
    $text['404']      = __('Error 404', 'pt_theplus');

    $showCurrent = 1; 
    $showOnHome  = 1; 
    $delimiter   = ' <span class="del"></span> '; 
    $before      = '<span class="current">';
    $after       = '</span>';
    /* === END OF OPTIONS === */

    global $post;
    $homeLink = home_url() . '/';
    $linkBefore = '<span>';
    $linkAfter = '</span>';
    $link = $linkBefore . '<a href="%1$s">%2$s</a>' . $linkAfter;

    if (is_home() || is_front_page()) {

        if ($showOnHome == 1) $crumbs_output = '<nav id="crumbs"><a href="' . esc_url($homeLink) . '">' . esc_html($text['home']) . '</a></nav>';

    } else {

        $crumbs_output ='<nav id="crumbs">' . sprintf($link, $homeLink, $text['home']) . $delimiter;

        if ( is_category() ) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                $cats = str_replace('<a', $linkBefore . '<a', $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                $crumbs_output .= $cats;
            }
            $crumbs_output .= $before . sprintf($text['category'], single_cat_title('', false)) . $after;

        } elseif ( is_search() ) {
            $crumbs_output .= $before . sprintf($text['search'], get_search_query()) . $after;


        }
        elseif (is_singular('topic') ){
            $post_type = get_post_type_object(get_post_type());
            printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
        }
        /* in forum, add link to support forum page template */
        elseif (is_singular('forum')){
            $post_type = get_post_type_object(get_post_type());
            printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
        }
        elseif (is_tax('topic-tag')){
            $post_type = get_post_type_object(get_post_type());
            printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
        }
        elseif ( is_day() ) {
            $crumbs_output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            $crumbs_output .= sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
            $crumbs_output .= $before . get_the_time('d') . $after;

        } elseif ( is_month() ) {
            $crumbs_output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            $crumbs_output .= $before . get_the_time('F') . $after;

        } elseif ( is_year() ) {
            $crumbs_output .= $before . get_the_time('Y') . $after;

        } elseif ( is_single() && !is_attachment() ) {
            if ( get_post_type() != 'post' ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                 $crumbs_output .= $linkBefore . '<a href="'.$homeLink . '/' . $slug["slug"] . '/">'.$post_type->labels->singular_name.'</a>' . $linkAfter;
                if ($showCurrent == 1) $crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;
            } else {
                $cat = get_the_category();
				if(isset($cat[0])) {
					$cat =  $cat[0];
					$cats = get_category_parents($cat, TRUE, $delimiter);
					if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
					$cats = str_replace('<a', $linkBefore . '<a', $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					$crumbs_output .= $cats;
					if ($showCurrent == 1) $crumbs_output .= $before . esc_html(get_the_title()) . $after;
				}
            }

        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
            $post_type = get_post_type_object(get_post_type());
            $crumbs_output .= $before . $post_type->labels->singular_name . $after;

        } elseif ( is_attachment() ) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
			if($cat) {
				$cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, $delimiter);
				$cats = str_replace('<a', $linkBefore . '<a', $cats);
				$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
				$crumbs_output .= $cats;
				printf($link, get_permalink($parent), $parent->post_title);
				if ($showCurrent == 1) $crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;
			}
        } elseif ( is_page() && !$post->post_parent ) {
            if ($showCurrent == 1) $crumbs_output .= $before . get_the_title() . $after;

        } elseif ( is_page() && $post->post_parent ) {
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                $crumbs_output .= $breadcrumbs[$i];
                if ($i != count($breadcrumbs)-1) $crumbs_output .= $delimiter;
            }
            if ($showCurrent == 1) $crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;

        } elseif ( is_tag() ) {
            $crumbs_output .= $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

        } elseif ( is_author() ) {
            global $author;
            $userdata = get_userdata($author);
            $crumbs_output .= $before . sprintf($text['author'], $userdata->display_name) . $after;

        } elseif ( is_404() ) {
            $crumbs_output .= $before . $text['404'] . $after;
        }

        if ( get_query_var('paged') ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $crumbs_output .= ' (';
            $crumbs_output .= __('Page', 'pt_theplus') . ' ' . get_query_var('paged');
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $crumbs_output .= ')';
        }

        $crumbs_output .= '</nav>';

    }
return $crumbs_output;
}
/*----------------------Header breadcurmbss------------------------------*/
if(class_exists('woocommerce')) {
function pt_plus_out_of_stock() {
  global $post;
  $id = $post->ID;
  $status = get_post_meta($id, '_stock_status',true);
  
  if ($status == 'outofstock') {
  	return true;
  } else {
  	return false;
  }
}
function pt_plus_product_badge() {
 global $post, $product;
 	if (pt_plus_out_of_stock()) {
		echo '<span class="badge out-of-stock">' . __( 'Out of Stock', 'pt_theplus' ) . '</span>';
	} else if ( $product->is_on_sale() ) {
		if ('discount' == 'discount') {
			if ($product->get_type() == 'variable') {
				$available_variations = $product->get_available_variations();								
				$maximumper = 0;
				for ($i = 0; $i < count($available_variations); ++$i) {
					$variation_id=$available_variations[$i]['variation_id'];
					$variable_product1= new WC_Product_Variation( $variation_id );
					$regular_price = $variable_product1->get_regular_price();
					$sales_price = $variable_product1->get_sale_price();
					$percentage = $sales_price ? round( ( ( $regular_price - $sales_price ) / $regular_price ) * 100) : 0;
					if ($percentage > $maximumper) {
						$maximumper = $percentage;
					}
				}
				echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$maximumper.'%</span>', $post, $product);
			} else if ($product->get_type() == 'simple'){
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
				echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
			} else if ($product->get_type() == 'external'){
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
				echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
			}
		} else {
			echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale','pt_theplus' ).'</span>', $post, $product);
		}
	}
}
add_action( 'pt_plus_product_badge', 'pt_plus_product_badge',3 );
}