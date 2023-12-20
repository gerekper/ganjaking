<?php 
	if ( ! defined( 'ABSPATH' ) ) { exit; }
		
	global $theplus_options,$post_type_options;
		
add_image_size( 'tp-image-grid', 700, 700, true);

/*quick view start*/
$getoptwidget = get_option( 'theplus_options');
function tp_get_product_info(){
	if(class_exists('woocommerce')) {
		$nonce = (isset($_POST["security"])) ? wp_unslash( $_POST["security"] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'theplus-addons' ) ){
			die ( 'Security checked!');
		}
		
		global $woocommerce,$post;
		$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : '';
		$template_id = !empty($_POST['template_id']) ? $_POST['template_id'] : '';
		$status = !empty($_POST['status']) ? $_POST['status'] : '';
		$custom_template = !empty($_POST['custom_template']) ? $_POST['custom_template'] : '';
		
		if(ctype_digit($product_id) && $status === 'publish'){
			wp( 'p=' . $product_id . '&post_status=publish&post_type=product' );		


			ob_start();
			while ( have_posts() ) : the_post();
				?>
				<div class="tp-quickview-wrapper"> 
				<?php
				if(!empty($template_id) && !empty($custom_template) && $custom_template=='yes'){
					global $tp_render_loop, $wp_query,$tp_index;
					$tp_index++;

					$tp_old_query=$wp_query;				
					$new_query=new \WP_Query( array( 'p' => get_the_ID() ) );
					$wp_query = $new_query;
					$pid=get_the_ID();
					$template_id = get_current_ID($template_id);
					$tp_render_loop=get_the_ID().",".$template_id;
					if (!$template_id) return;
					$return = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
					$tp_render_loop=false;
					$wp_query = $tp_old_query;		
					echo $return;
				}else{
					echo '<div class="tp-qv-left">';
						echo get_the_post_thumbnail();
					echo '</div>';
					echo '<div class="tp-qv-right">';					
							echo '<div class="tp-qv-title">'.get_the_title().'</div>';
								$excerpt = explode(' ', get_the_excerpt(), 50);							
								if (count($excerpt)>= 50) {
									array_pop($excerpt);
									$excerpt = implode(" ",$excerpt).'...';								
								} else {
									$excerpt = implode(" ",$excerpt);								
								}	
								$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
							echo '<div class="tp-qv-excerpt">'.$excerpt.'</div>';							
							echo '<div class="tp-qv-button"><a href="'.get_permalink().'">Read More</a></div>';
					echo '</div>';				
				}

				echo '</div>';

			endwhile;
			echo  ob_get_clean(); 	
			exit();
		}
	}	
}

add_action( 'wp_ajax_tp_get_product_ajax', 'tp_get_product_info' );
add_action( 'wp_ajax_nopriv_tp_get_product_ajax','tp_get_product_info' );

/*quick view end*/

/*dynamic listing quickview*/
function tp_get_dl_post_info(){	
	$nonce = (isset($_POST["security"])) ? wp_unslash( $_POST["security"] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'theplus-addons' ) ){
		die ( 'Security checked!');
	}
	
	global $woocommerce,$post;
	$template_id = !empty($_POST['template_id']) ? $_POST['template_id'] : '';
	$custom_template = !empty($_POST['custom_template']) ? $_POST['custom_template'] : '';
	
	$args = array();
	if(isset($_POST['product_id'], $_POST['qvquery']) && ctype_digit($_POST['product_id'])){
		$args = array(
			'post_type' => !empty($_POST['qvquery']) ? $_POST['qvquery'] : 'post',
			'post_status' => 'publish',
			'p' => !empty($_POST['product_id']) ? $_POST['product_id'] : '',			
		);
	}else{
		exit();
	}
	$loop = new WP_Query($args);
	
	if($loop->have_posts()){
		 if(ctype_digit($loop->query['p']) && $loop->query['post_status'] === 'publish' && !empty($loop->query['post_type'])){
		
			ob_start();
			while ( $loop->have_posts() ) : $loop->the_post();
				?>
				<div class="tp-quickview-wrapper"> 
				<?php
				if(!empty($template_id) && !empty($custom_template) && $custom_template=='yes'){
					global $tp_render_loop, $wp_query,$tp_index;
					$tp_index++;

					$tp_old_query=$wp_query;				
					$new_query=new \WP_Query( array( 'p' => get_the_ID() ) );
					$wp_query = $new_query;
					$pid=get_the_ID();
					$template_id = get_current_ID($template_id);
					$tp_render_loop=get_the_ID().",".$template_id;
					if (!$template_id) return;
					$return = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
					$tp_render_loop=false;
					$wp_query = $tp_old_query;		
					echo $return;
				}else{
					echo '<div class="tp-qv-left">';
						echo get_the_post_thumbnail();
					echo '</div>';
					echo '<div class="tp-qv-right">';					
							echo '<div class="tp-qv-title">'.get_the_title().'</div>';
								$excerpt = explode(' ', get_the_excerpt(), 50);							
								if (count($excerpt)>= 50) {
									array_pop($excerpt);
									$excerpt = implode(" ",$excerpt).'...';								
								} else {
									$excerpt = implode(" ",$excerpt);								
								}	
								$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
							echo '<div class="tp-qv-excerpt">'.$excerpt.'</div>';							
							echo '<div class="tp-qv-button"><a href="'.get_permalink().'">Read More</a></div>';
					echo '</div>';				
				}

				echo '</div>';

			endwhile;
			echo  ob_get_clean(); 	
			exit();
		}
	}	
}

add_action( 'wp_ajax_tp_get_dl_post_info_ajax', 'tp_get_dl_post_info' );
add_action( 'wp_ajax_nopriv_tp_get_dl_post_info_ajax','tp_get_dl_post_info' );

/*dynamic listing quickview*/

if (! function_exists('tp_get_image_rander') && version_compare( L_THEPLUS_VERSION, '5.0.2', '<' ) ) {
	function tp_get_image_rander( $id ='', $size = 'full', $attr =[], $posttype = 'attachment' ) {
		if( empty($id) ){
			return '';
		}
		
		if(!empty($posttype) && $posttype=='post' ){
			$get_post = get_post( $id );
	 
			if ( ! $get_post ) {
				return '';
			}
			$id = get_post_thumbnail_id( $get_post );
		}
		
		if( ! wp_get_attachment_image_src( $id ) ){
			return '';
		}
		
		$output = '';	
		
		$get_image = wp_get_attachment_image( $id, $size, false, $attr );
		
		$check_srcset = strpos( $get_image, 'srcset' ) !== false;
				
		$output = $get_image . $output;

		return $output;
	}
}

// Check Html Tag
function theplus_html_tag_check(){
	return [ 'div',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'a',
		'span',
		'p',
		'header',
		'footer',
		'article',
		'aside',
		'main',
		'nav',		
		'section',		
	];
}		

function theplus_validate_html_tag( $check_tag ) {
	return in_array( strtolower( $check_tag ), theplus_html_tag_check() ) ? $check_tag : 'div';
}

/*pre loader body class*/
$theplus_optionsget = get_option( 'theplus_options');
if(!empty($theplus_optionsget['check_elements']) && isset($theplus_optionsget['check_elements'])){
	if (in_array("tp_pre_loader", $theplus_optionsget['check_elements'])){
		function theplus_body_class($classes) {
			$classes[]="theplus-preloader";
			return $classes;
		}
	add_filter('body_class', 'theplus_body_class');
	}	
}


/*woo multi step*/
function woo_checkout_update_order_review() {
	ob_start();
	Woo_Checkout::checkout_order_review_default();
	$woo_checkout_order_review = ob_get_clean();

	wp_send_json(
		array(
			'order_review' => $woo_checkout_order_review,
		)
	);
}
add_action('wp_ajax_woo_checkout_update_order_review', 'woo_checkout_update_order_review',10);
add_action('wp_ajax_nopriv_woo_checkout_update_order_review','woo_checkout_update_order_review',10);


//user profile social
function theplus_user_social_links( $user_contact ) {   
   $user_contact['tp_phone_number'] = __('Phone Number', 'theplus');
   $user_contact['tp_profile_facebook'] = __('Facebook Link', 'theplus');
   $user_contact['tp_profile_twitter'] = __('Twitter Link', 'theplus');
   $user_contact['tp_profile_instagram'] = __('Instagram', 'theplus');

   return $user_contact;
}
add_filter('user_contactmethods', 'theplus_user_social_links',10);

/* WOOCOMMERCE Mini Cart */
function theplus_woocomerce_ajax_cart_update($fragments) {
	if(class_exists('woocommerce')) {		
		ob_start();
		?>			
			
			<div class="cart-wrap"><span><?php echo WC()->cart->get_cart_contents_count(); ?></span></div>
		<?php
		$fragments['.cart-wrap'] = ob_get_clean();
		return $fragments;
	}
}
add_filter('woocommerce_add_to_cart_fragments', 'theplus_woocomerce_ajax_cart_update', 10, 3);

/*3rd party WC_Product_Subtitle*/
if(!function_exists('product_subtitle_after_title')){
	function product_subtitle_after_title() {
		echo do_shortcode("[product_subtitle]");
	}
}
add_action("theplus_after_product_title","product_subtitle_after_title");
/*3rd party WC_Product_Subtitle*/

/*defer script*/
function tp_defer_scripts( $tag, $handle, $src ) {
			$defer = array( 
	'google_platform_js'
  );
  if ( in_array( $handle, $defer ) ) {
	 return '<script src="' . $src . '" async defer type="text/javascript"></script>' . "\n";
  }
	
	return $tag;
} 

add_filter( 'script_loader_tag', 'tp_defer_scripts', 10, 3 );
/*defer script*/

function theplus_get_thumb_url(){
	return THEPLUS_ASSETS_URL .'images/placeholder-grid.jpg';
}

/* Custom Link url attachment Media */
function plus_attachment_field_media( $form_fields, $post ) {
    $form_fields['plus-gallery-url'] = array(
        'label' => esc_html__('Custom URL','theplus'),
        'input' => 'url',
        'value' => get_post_meta( $post->ID, 'plus_gallery_url', true ),
        'helps' => esc_html__('Gallery Listing Widget Used Custom Url Media','theplus'),
    );
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'plus_attachment_field_media', 10, 2 );
function plus_attachment_field_save( $post, $attachment ) {    
    if( isset( $attachment['plus-gallery-url'] ) )
		update_post_meta( $post['ID'], 'plus_gallery_url', esc_url( $attachment['plus-gallery-url'] ) ); 
    
	return $post;	
}
add_filter( 'attachment_fields_to_save', 'plus_attachment_field_save', 10, 2 );
/* Custom Link url attachment Media */

class Theplus_MetaBox {
	
	public static function get($name) {
		global $post;
		
		if (isset($post) && !empty($post->ID)) {
			return get_post_meta($post->ID, $name, true);
		}
		
		return false;
	}
}
function theplus_get_option($options_type,$field){
	$theplus_options=get_option( 'theplus_options' );
	$post_type_options=get_option( 'post_type_options' );
	$values='';
	if($options_type=='general'){
		if(isset($theplus_options[$field]) && !empty($theplus_options[$field])){
			$values=$theplus_options[$field];
		}
	}
	if($options_type=='post_type'){
		if(isset($post_type_options[$field]) && !empty($post_type_options[$field])){
			$values=$post_type_options[$field];
		}
	}
	return $values;
}

function theplus_white_label_option($field){
	$label_options=get_option( 'theplus_white_label' );	
		$values='';
		if(isset($label_options[$field]) && !empty($label_options[$field])){
			$values=$label_options[$field];
		}	
	return $values;
}

function theplus_testimonial_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$testi_post_type=!empty($post_type_options['testimonial_post_type']) ? $post_type_options['testimonial_post_type'] : '';
	$post_name='theplus_testimonial';
	if(isset($testi_post_type) && !empty($testi_post_type)){
		if($testi_post_type=='themes'){
			$post_name=theplus_get_option('post_type','testimonial_theme_name');
		}elseif($testi_post_type=='plugin'){
			$get_name=theplus_get_option('post_type','testimonial_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=theplus_get_option('post_type','testimonial_plugin_name');
			}
		}elseif($testi_post_type=='themes_pro'){
			$post_name='testimonial';
		}
	}else{
		$post_name='theplus_testimonial';
	}
	return $post_name;
}
function theplus_testimonial_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$testi_post_type=!empty($post_type_options['testimonial_post_type']) ? $post_type_options['testimonial_post_type'] : '';
	$taxonomy_name='theplus_testimonial_cat';
	if(isset($testi_post_type) && !empty($testi_post_type)){
		if($testi_post_type=='themes'){
			$taxonomy_name=theplus_get_option('post_type','testimonial_category_name');
		}else if($testi_post_type=='plugin'){
			$get_name=theplus_get_option('post_type','testimonial_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$taxonomy_name=theplus_get_option('post_type','testimonial_category_plugin_name');
			}
		}elseif($testi_post_type=='themes_pro'){
			$taxonomy_name='testimonial_category';
		}
	}else{
		$taxonomy_name='theplus_testimonial_cat';
	}
	return $taxonomy_name;
}
function theplus_client_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$client_post_type=!empty($post_type_options['client_post_type']) ? $post_type_options['client_post_type'] : '';
	$post_name='theplus_clients';
	if(isset($client_post_type) && !empty($client_post_type)){
		if($client_post_type=='themes'){
			$post_name=theplus_get_option('post_type','client_theme_name');
		}elseif($client_post_type=='plugin'){
			$get_name=theplus_get_option('post_type','client_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=theplus_get_option('post_type','client_plugin_name');
			}
		}elseif($client_post_type=='themes_pro'){
			$post_name='clients';
		}
	}else{
		$post_name='theplus_clients';
	}
	return $post_name;
}
function theplus_client_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$client_post_type=!empty($post_type_options['client_post_type']) ? $post_type_options['client_post_type'] : '';
	$post_name='theplus_clients_cat';
	if(isset($client_post_type) && !empty($client_post_type)){
		if($client_post_type=='themes'){
			$post_name=theplus_get_option('post_type','client_category_name');
		}else if($client_post_type=='plugin'){
			$get_name=theplus_get_option('post_type','client_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=theplus_get_option('post_type','client_category_plugin_name');
			}
		}elseif($client_post_type=='themes_pro'){
			$post_name='clients_category';
		}
	}else{
		$post_name='theplus_clients_cat';
	}
	return $post_name;
}
function theplus_team_member_post_name(){
	$post_type_options=get_option( 'post_type_options' );
	$team_post_type=!empty($post_type_options['team_member_post_type']) ? $post_type_options['team_member_post_type'] : '';
	$post_name='theplus_team_member';
	if(isset($team_post_type) && !empty($team_post_type)){
		if($team_post_type=='themes'){
			$post_name=theplus_get_option('post_type','team_member_theme_name');
		}elseif($team_post_type=='plugin'){
			$get_name=theplus_get_option('post_type','team_member_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$post_name=theplus_get_option('post_type','team_member_plugin_name');
			}
		}elseif($team_post_type=='themes_pro'){
			$post_name='team_member';
		}
	}else{
		$post_name='theplus_team_member';
	}
	return $post_name;
}
function theplus_team_member_post_category(){
	$post_type_options=get_option( 'post_type_options' );
	$team_post_type=!empty($post_type_options['team_member_post_type']) ? $post_type_options['team_member_post_type'] : '';
	$taxonomy_name='theplus_team_member_cat';
	if(isset($team_post_type) && !empty($team_post_type)){
		if($team_post_type=='themes'){
			$taxonomy_name=theplus_get_option('post_type','team_member_category_name');
		}else if($team_post_type=='plugin'){
			$get_name=theplus_get_option('post_type','team_member_category_plugin_name');
			if(isset($get_name) && !empty($get_name)){
				$taxonomy_name=theplus_get_option('post_type','team_member_category_plugin_name');
			}
		}elseif($team_post_type=='themes_pro'){
			$taxonomy_name='team_member_category';
		}
	}else{
		$taxonomy_name='theplus_team_member_cat';
	}
	return $taxonomy_name;
}

/* woo swatches
 * @since 4.1.8
 * Woocommerce Custom Field Type
 */
function tp_product_attributes_types($selector){
		$type = tp_product_attr();
		
		foreach ( $type as $key => $options ) {
			$selector[ $key ] = $options['title'];
		}

		return $selector;
}

//Woocommerce Custom Attributes
function tp_product_attr($type = false){

	$types['color'] = array(
		'title'   => esc_html__( 'Color', 'theplus' ),
	);
	$types['image'] = array(
		'title'   => esc_html__( 'Image', 'theplus' ),
	);
	$types['button'] = array(
		'title'   => esc_html__( 'Button', 'theplus' ),
	);

	if ( $type ) {
		return isset( $types[ $type ] ) ? $types[ $type ] : array();
	}
	
	return $types;
}

$theplus_data=get_option( 'theplus_api_connection_data' );
if(isset($theplus_optionsget['check_elements']) && !empty($theplus_optionsget['check_elements']) && (in_array("tp_woo_single_basic", $theplus_optionsget['check_elements']) || in_array("tp_search_filter", $theplus_optionsget['check_elements']))  && !empty($theplus_data['theplus_woo_swatches_switch'])){
	add_filter( 'product_attributes_type_selector', 'tp_product_attributes_types') ;
}
/*woo swatches*/

function theplus_scroll_animation(){
	
	$theplus_data=get_option( 'theplus_api_connection_data' );
		
	if(isset($theplus_data['scroll_animation_offset']) && !empty($theplus_data['scroll_animation_offset']) && $theplus_data['scroll_animation_offset']!=0){
		$value= $theplus_data['scroll_animation_offset'].'%';
	}else if(isset($theplus_data['scroll_animation_offset']) && !empty($theplus_data['scroll_animation_offset']) && $theplus_data['scroll_animation_offset']==0){
		$value= '85%';
	}else{
		$value= '85%';
	}
	
	return $value;
}
function theplus_excerpt($limit) {
	if(method_exists('WPBMap', 'addAllMappedShortcodes')) {
		WPBMap::addAllMappedShortcodes();
	}
		global $post;
		$excerpt = explode(' ', get_the_excerpt(), $limit);
		$content = explode(' ', get_the_content(), $limit);
		
		if(!empty($excerpt)){
			if (count($excerpt)>=$limit) {
				array_pop($excerpt);			
				$excerpt = implode(" ",$excerpt).'...';			
			}else{
				$excerpt = implode(" ",$excerpt);
			}
		}else if(count($content)>=$limit){
			array_pop($content);			
			$excerpt = implode(" ",$content).'...';			
		}else {
			$excerpt = implode(" ",$excerpt);			
		}	
		$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
	
	return $excerpt;
}
function limit_words($string, $word_limit){
	$words = explode(" ",$string);
	return implode(" ",array_splice($words,0,$word_limit));
}	
function theplus_get_title($limit) {
	if(method_exists('WPBMap', 'addAllMappedShortcodes')) {
		WPBMap::addAllMappedShortcodes();
	}
		global $post;
		$title = explode(' ', get_the_title(), $limit);
		if (count($title)>=$limit) {
			array_pop($title);
			$title = implode(" ",$title).'...';
		} else {
			$title = implode(" ",$title);
		}	
		$title = preg_replace('`[[^]]*]`','',$title);
	
	return $title;
}
function theplus_loading_image_grid($postid='',$type=''){
	global $post;
	$content_image='';
	if($type!='background'){		
		$image_url=THEPLUS_ASSETS_URL .'images/placeholder-grid.jpg';
		$content_image='<img src="'.esc_url($image_url).'" alt="'.esc_attr(get_the_title()).'"/>';
		
		return $content_image;
	
	}elseif($type=='background'){
	
		$image_url=THEPLUS_ASSETS_URL .'images/placeholder-grid.jpg';
		$data_src='style="background:url('.esc_url($image_url).') #f7f7f7;" ';
		
		return $data_src;
		
	}
}
function theplus_loading_bg_image($postid=''){
	global $post;
	$content_image='';
	if(!empty($postid)){
		$featured_image=get_the_post_thumbnail_url($postid,'full');
		if(empty($featured_image)){
			$featured_image=theplus_get_thumb_url();
		}
		$content_image='style="background:url('.esc_url($featured_image).') #f7f7f7;"';
		return $content_image;
	}else{
	return $content_image;
	}
}
function theplus_array_flatten($array) {
	  if (!is_array($array)) { 
		return FALSE; 
	  } 
	  $result = array(); 
	  foreach ($array as $key => $value) { 
		if (is_array($value)) { 
		  $result = array_merge($result, theplus_array_flatten($value)); 
		} 
		else { 
		  $result[$key] = $value; 
		} 
	  } 
	  return $result; 
}
function theplus_createSlug($str, $delimiter = '-'){
	
	$slug=preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	return $slug;
	
} 
/*----------------------------load more posts ---------------------------*/
function theplus_more_post_ajax(){
	global $post;
	ob_start();
	$load_attr = isset($_POST["loadattr"]) ? wp_unslash( $_POST["loadattr"] ) : '';
	if(empty($load_attr)){
		ob_get_contents();
		exit;
		ob_end_clean();
	}
	$load_attr = tp_check_decrypt_key($load_attr);
	$load_attr = json_decode($load_attr,true);
	if(!is_array($load_attr)){
		ob_get_contents();
		exit;
		ob_end_clean();
	}
	
	$nonce = (isset($load_attr["theplus_nonce"])) ? wp_unslash( $load_attr["theplus_nonce"] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'theplus-addons' ) ){
		die ( 'Security checked!');
	}
	
	$paged= (isset($_POST["paged"]) && intval($_POST["paged"]) ) ? wp_unslash( $_POST["paged"] ) : '';
	$offset= (isset($_POST["offset"]) && intval($_POST["offset"]) ) ? wp_unslash( $_POST["offset"] ) : '';
	
	$post_type = isset( $load_attr["post_type"] ) ? sanitize_text_field( wp_unslash($load_attr["post_type"]) ) : '';
	$post_load = isset( $load_attr["load"] ) ? sanitize_text_field( wp_unslash($load_attr["load"]) ) : '';
	$texonomy_category = isset( $load_attr["texonomy_category"] ) ? sanitize_text_field( wp_unslash($load_attr["texonomy_category"]) ) : '';
	$include_posts = isset( $load_attr["include_posts"] ) ? sanitize_text_field( wp_unslash($load_attr["include_posts"]) ) : '';
	$exclude_posts = isset( $load_attr["exclude_posts"] ) ? sanitize_text_field( wp_unslash($load_attr["exclude_posts"]) ) : '';
	$layout =  isset( $load_attr["layout"] ) ? sanitize_text_field( wp_unslash($load_attr["layout"]) ) : '';
	$b_dis_badge_switch = isset( $load_attr["badge"] ) ? sanitize_text_field( wp_unslash($load_attr["badge"]) ) : '';
	$out_of_stock = isset( $load_attr["out_of_stock"] ) ? sanitize_text_field( wp_unslash($load_attr["out_of_stock"]) ) : '';
	$variation_price_on = isset( $load_attr["variationprice"] ) ? sanitize_text_field( wp_unslash($load_attr["variationprice"]) ) : '';
	$hover_image_on_off = isset( $load_attr["hoverimagepro"] ) ? sanitize_text_field( wp_unslash($load_attr["hoverimagepro"]) ) : '';
	
	$display_post = (isset( $load_attr["display_post"] ) && intval($load_attr["display_post"]) ) ? wp_unslash($load_attr["display_post"]) : 4;
	$category = isset( $load_attr["category"] ) ? wp_unslash($load_attr["category"]) : '';
	$post_tags = isset( $load_attr["post_tags"] ) ? wp_unslash($load_attr["post_tags"]) : '';
	$ex_cat = isset( $load_attr["ex_cat"] ) ? wp_unslash($load_attr["ex_cat"]) : '';
	$ex_tag = isset( $load_attr["ex_tag"] ) ? wp_unslash($load_attr["ex_tag"]) : '';
	$post_authors = isset( $load_attr["post_authors"] ) ? wp_unslash($load_attr["post_authors"]) : '';
	$desktop_column = (isset( $load_attr["desktop-column"] )  && intval($load_attr["desktop-column"]) ) ? wp_unslash($load_attr["desktop-column"]) : '';
	$tablet_column = (isset( $load_attr["tablet-column"] )  && intval($load_attr["tablet-column"]) ) ? wp_unslash($load_attr["tablet-column"]) : '';
	$mobile_column = (isset( $load_attr["mobile-column"] )  && intval($load_attr["mobile-column"]) ) ? wp_unslash($load_attr["mobile-column"]) : '';
	$style = isset( $load_attr["style"] ) ? sanitize_text_field( wp_unslash($load_attr["style"]) ) : '';
	$style_layout = isset( $load_attr["style_layout"] ) ? sanitize_text_field( wp_unslash($load_attr["style_layout"]) ) : '';
	$filter_category = isset( $load_attr["filter_category"] ) ? wp_unslash($load_attr["filter_category"]) : '';
	$order_by = isset( $load_attr["order_by"] ) ? sanitize_text_field( wp_unslash($load_attr["order_by"]) ) : '';
	$post_order = isset( $load_attr["post_order"] ) ? sanitize_text_field( wp_unslash($load_attr["post_order"]) ) : '';
	$animated_columns = isset( $load_attr["animated_columns"] ) ? sanitize_text_field( wp_unslash($load_attr["animated_columns"]) ) : '';
	$post_load_more = (isset( $load_attr["post_load_more"] ) && intval($load_attr["post_load_more"]) ) ? wp_unslash($load_attr["post_load_more"]) : '';
	$display_cart_button = isset( $load_attr["cart_button"] ) ? wp_unslash($load_attr["cart_button"]) : '';
	
	$metro_column = isset( $load_attr["metro_column"] ) ? wp_unslash($load_attr["metro_column"]) : '';
	$metro_style = isset( $load_attr["metro_style"] ) ? wp_unslash($load_attr["metro_style"]) : '';
	$responsive_tablet_metro = isset( $load_attr["responsive_tablet_metro"] ) ? wp_unslash($load_attr["responsive_tablet_metro"]) : '';
	$tablet_metro_column = isset( $load_attr["tablet_metro_column"] ) ? wp_unslash($load_attr["tablet_metro_column"]) : '';
	$tablet_metro_style = isset( $load_attr["tablet_metro_style"] ) ? wp_unslash($load_attr["tablet_metro_style"]) : '';
	
	$display_post_title = isset( $load_attr["display_post_title"] ) ? wp_unslash($load_attr["display_post_title"]) : '';
	$post_title_tag = isset( $load_attr["post_title_tag"] ) ? wp_unslash($load_attr["post_title_tag"]) : '';

	$author_prefix = isset( $load_attr["author_prefix"] ) ? wp_unslash($load_attr["author_prefix"]) : '';

	$title_desc_word_break = isset( $load_attr["title_desc_word_break"] ) ? wp_unslash($load_attr["title_desc_word_break"]) : '';
	
	$display_title_limit = isset( $load_attr["display_title_limit"] ) ? wp_unslash($load_attr["display_title_limit"]) : '';
	$display_title_by = isset( $load_attr["display_title_by"] ) ? wp_unslash($load_attr["display_title_by"]) : '';
	$display_title_input = isset( $load_attr["display_title_input"] ) ? wp_unslash($load_attr["display_title_input"]) : '';
	$display_title_3_dots = isset( $load_attr["display_title_3_dots"] ) ? wp_unslash($load_attr["display_title_3_dots"]) : '';
	
	$feature_image = isset( $load_attr["feature_image"] ) ? wp_unslash($load_attr["feature_image"]) : '';
	
	$display_post_meta = isset( $load_attr["display_post_meta"] ) ? wp_unslash($load_attr["display_post_meta"]) : '';
	$post_meta_tag_style = isset( $load_attr["post_meta_tag_style"] ) ? wp_unslash($load_attr["post_meta_tag_style"]) : '';
	$display_post_meta_date = isset( $load_attr["display_post_meta_date"] ) ? wp_unslash($load_attr["display_post_meta_date"]) : '';
	$display_post_meta_author = isset( $load_attr["display_post_meta_author"] ) ? wp_unslash($load_attr["display_post_meta_author"]) : '';
	$display_post_meta_author_pic = isset( $load_attr["display_post_meta_author_pic"] ) ? wp_unslash($load_attr["display_post_meta_author_pic"]) : '';
	$display_excerpt = isset( $load_attr["display_excerpt"] ) ? wp_unslash($load_attr["display_excerpt"]) : '';
	$post_excerpt_count = isset( $load_attr["post_excerpt_count"] ) ? wp_unslash($load_attr["post_excerpt_count"]) : '';
	$display_post_category = isset( $load_attr["display_post_category"] ) ? wp_unslash($load_attr["display_post_category"]) : '';
	$post_category_style = isset( $load_attr["post_category_style"] ) ? wp_unslash($load_attr["post_category_style"]) : '';
	$dpc_all = isset( $load_attr["dpc_all"] ) ? wp_unslash($load_attr["dpc_all"]) : '';
	$featured_image_type = isset( $load_attr["featured_image_type"] ) ? wp_unslash($load_attr["featured_image_type"]) : '';
	
	$display_thumbnail = isset( $load_attr["display_thumbnail"] ) ? wp_unslash($load_attr["display_thumbnail"]) : '';
	$thumbnail = isset( $load_attr["thumbnail"] ) ? wp_unslash($load_attr["thumbnail"]) : '';
	$thumbnail_car = isset( $load_attr["thumbnail_car"] ) ? wp_unslash($load_attr["thumbnail_car"]) : '';
	
	$display_button = isset( $load_attr['display_button'] ) ? wp_unslash($load_attr['display_button']) : '';
	$button_style = isset( $load_attr['button_style'] ) ? sanitize_text_field( wp_unslash($load_attr['button_style']) ) : '';
	$before_after = isset( $load_attr['before_after'] ) ? sanitize_text_field( wp_unslash($load_attr['before_after']) ) : '';
	$button_text = isset( $load_attr['button_text'] ) ? sanitize_text_field( wp_unslash($load_attr['button_text']) ) : '';
	$button_icon_style = isset( $load_attr['button_icon_style'] ) ? sanitize_text_field( wp_unslash($load_attr['button_icon_style']) ) : '';
	$button_icon = isset( $load_attr['button_icon'] ) ? $load_attr['button_icon'] : '';
	$button_icons_mind = isset( $load_attr['button_icons_mind'] ) ? $load_attr['button_icons_mind'] : '';
	
	$skin_template = isset( $load_attr['skin_template'] ) ? $load_attr['skin_template'] : '';

	$dynamic_template = $skin_template;
	$display_product = isset( $load_attr["display_product"] ) ? wp_unslash($load_attr["display_product"]) : '';
	$display_catagory = isset( $load_attr["display_catagory"] ) ? wp_unslash($load_attr["display_catagory"]) : '';
	$display_rating = isset( $load_attr["display_rating"] ) ? wp_unslash($load_attr["display_rating"]) : '';
	
	$display_yith_list = isset( $load_attr["display_yith_list"] ) ? wp_unslash($load_attr["display_yith_list"]) : '';
	$display_yith_compare = isset( $load_attr["display_yith_compare"] ) ? wp_unslash($load_attr["display_yith_compare"]) : '';
	$display_yith_wishlist = isset( $load_attr["display_yith_wishlist"] ) ? wp_unslash($load_attr["display_yith_wishlist"]) : '';
	$display_yith_quickview = isset( $load_attr["display_yith_quickview"] ) ? wp_unslash($load_attr["display_yith_quickview"]) : '';
	
	$dcb_single_product = isset( $load_attr["dcb_single_product"] ) ? wp_unslash($load_attr["dcb_single_product"]) : '';
	$dcb_variation_product = isset( $load_attr["dcb_variation_product"] ) ? wp_unslash($load_attr["dcb_variation_product"]) : '';
	
	$display_theplus_quickview = isset($load_attr["display_theplus_quickview"]) ? wp_unslash($load_attr["display_theplus_quickview"]) : '';
	
	$desktop_class=$tablet_class=$mobile_class='';
	if($layout!='carousel' && $layout!='metro'){
		if($desktop_column=='5'){
			$desktop_class='theplus-col-5';
		}else{
			$desktop_class='tp-col-lg-'.esc_attr($desktop_column);
		}
		
		$tablet_class='tp-col-md-'.esc_attr($tablet_column);
		$mobile_class='tp-col-sm-'.esc_attr($mobile_column);
		$mobile_class .=' tp-col-'.esc_attr($mobile_column);
	}

	$clientContentFrom="";
	if($post_load=='clients'){
		$clientContentFrom = isset( $load_attr['SourceType'] ) ? $load_attr['SourceType'] : '';
		$disable_link = isset( $load_attr['disable_link'] ) ? $load_attr['disable_link'] : '';
	}

	$j=1;
	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $post_load_more,
		$texonomy_category => $category,
		'offset' => $offset,
		'orderby'	=>$order_by,
		'post_status' =>'publish',
		'order'	=>$post_order
	);
	
	if('' !== $ex_tag){
		$ex_tag =explode(",",$ex_tag);
		$args['tag__not_in'] = $ex_tag;
	}
	if('' !== $ex_cat){
		$ex_cat =explode(",",$ex_cat);
		$args['category__not_in'] = $ex_cat;
	}
	
	if('' !== $exclude_posts){
		$exclude_posts =explode(",",$exclude_posts);
		$args['post__not_in'] = $exclude_posts;
	}
	if('' !== $include_posts){
		$include_posts =explode(",",$include_posts);
		$args['post__in'] = $include_posts;
	}
	
	if((!empty($post_type) && $post_type =='product')){			
		$args['tax_query'] = [
			'relation' => 'AND',
			[
				'taxonomy' => 'product_visibility',
				'field' => 'name',
				'terms' => ['exclude-from-search', 'exclude-from-catalog'],
				'operator' => 'NOT IN',
			],
		];
	}
	
	if(!empty($display_product) && $display_product=='featured'){
		$args['tax_query']     = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
			),
		);
	}
	
	if(!empty($display_product) && $display_product=='on_sale'){
		$args['meta_query']     = array(
			'relation' => 'OR',
			array( // Simple products type
				'key'           => '_sale_price',
				'value'         => 0,
				'compare'       => '>',
				'type'          => 'numeric'
			),
			array( // Variable products type
				'key'           => '_min_variation_sale_price',
				'value'         => 0,
				'compare'       => '>',
				'type'          => 'numeric'
			)
		);
	}
	
	if(!empty($display_product) && $display_product=='top_sales'){
		$args['meta_query']     = array(
			array(
				'key' 		=> 'total_sales',
				'value' 	=> 0,
				'compare' 	=> '>',
				)
		);
	}
	
	if(!empty($display_product) && $display_product=='instock'){
		$args['meta_query']     = array(
			array(
				'key' 		=> '_stock_status',
				'value' 	=> 'instock',												
			)
		);
	}
	
	if(!empty($display_product) && $display_product=='outofstock'){
		$args['meta_query']     = array(
			array(
				'key' 		=> '_stock_status',
				'value' 	=> 'outofstock',												
			)
		);
	}
	
	if ( '' !== $post_tags && $post_type=='post') {
		$post_tags =explode(",",$post_tags);
		$args['tax_query'] = array(
		'relation' => 'AND',
			array(
				'taxonomy'         => 'post_tag',
				'terms'            => $post_tags,
				'field'            => 'term_id',
				'operator'         => 'IN',
				'include_children' => true,
			),
		);
	}
	
	if (!empty($post_type) && ($post_type !='post' && $post_type !='product')) {
		if ( !empty($texonomy_category) && $texonomy_category=='categories' && !empty($category)) {
			$category =explode(",",$category);
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'categories',
					'field' => 'slug',
					'terms' => $category,
				),
			);
		}
	}
	
	if('' !== $post_authors && $post_type=='post'){
		$args['author'] = $post_authors;
	}
	
	$ji=($post_load_more*$paged)-$post_load_more+$display_post+1;
	$ij='';
	$tablet_metro_class=$tablet_ij='';
	$loop = new WP_Query($args);		
		if ( $loop->have_posts() ) :
			while ($loop->have_posts()) {
				$loop->the_post();
				
				//read more button
				$the_button='';
				if($display_button == 'yes'){
					
					$btn_uid=uniqid('btn');
					$data_class= $btn_uid;
					$data_class .=' button-'.$button_style.' ';
					
					$the_button ='<div class="pt-plus-button-wrapper">';
						$the_button .='<div class="button_parallax">';
							$the_button .='<div class="ts-button">';
								$the_button .='<div class="pt_plus_button '.$data_class.'">';
									$the_button .= '<div class="animted-content-inner">';
										$the_button .='<a href="'.esc_url(get_the_permalink()).'" class="button-link-wrap" role="button" rel="nofollow">';
										$the_button .= include THEPLUS_PATH. 'includes/blog/post-button.php'; 
										$the_button .='</a>';
									$the_button .='</div>';
								$the_button .='</div>';
							$the_button .='</div>';
						$the_button .='</div>';
					$the_button .='</div>';	
				}
				
				if($post_load=='blogs'){
					include THEPLUS_PATH ."includes/ajax-load-post/blog-style.php";
				}
				if($post_load=='clients'){
					include THEPLUS_PATH ."includes/ajax-load-post/client-style.php";
				}
				if($post_load=='portfolios'){
					include THEPLUS_PATH ."includes/ajax-load-post/portfolio-style.php";
				}
				if($post_load=='products' || $post_load=='dynamiclisting'){
					$template_id='';
					if(!empty($dynamic_template)){
						$count=count($dynamic_template);
						$value = $offset%$count;
						$template_id=$dynamic_template[$value];	
					}
					if($post_load=='dynamiclisting'){
						include THEPLUS_PATH ."includes/ajax-load-post/dynamic-listing-style.php";
					}
					
					if($post_load=='products'){
						include THEPLUS_PATH ."includes/ajax-load-post/product-style.php";
					}						
					
					$offset++;
				}					
				$ji++;
			}
			$content = ob_get_contents();
			ob_end_clean();
		endif;
	wp_reset_postdata();
	echo $content;
	exit;
	ob_end_clean();
}
add_action('wp_ajax_theplus_more_post','theplus_more_post_ajax');
add_action('wp_ajax_nopriv_theplus_more_post', 'theplus_more_post_ajax');

function tp_feed_load(){
	ob_start();
	$result = [];
	$load_attr = isset($_POST["loadattr"]) ? wp_unslash( $_POST["loadattr"] ) : '';

	if(empty($load_attr)){
		ob_get_contents();
		exit;
		ob_end_clean();
	}

	$load_attr = tp_check_decrypt_key($load_attr);
	$load_attr = json_decode($load_attr, true);
	if(!is_array($load_attr)){
		ob_get_contents();
		exit;
		ob_end_clean();
	}

	$nonce = (isset($load_attr["theplus_nonce"])) ? wp_unslash( $load_attr["theplus_nonce"] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'theplus-addons' ) ){
		die ( 'Security checked!');
	}

	$load_class = isset($load_attr["load_class"]) ? sanitize_text_field( wp_unslash($load_attr["load_class"]) ) : uniqid("tp-sfeed");
	$style = isset($load_attr["style"]) ? sanitize_text_field( wp_unslash($load_attr["style"]) ) : 'style-1';
	$layout = isset($load_attr["layout"]) ? sanitize_text_field( wp_unslash($load_attr["layout"]) ) : 'grid';

	$desktop_column = (isset($load_attr["desktop_column"])  && intval($load_attr["desktop_column"]) ) ? wp_unslash($load_attr["desktop_column"]) : '';
	$tablet_column = (isset($load_attr["tablet_column"])  && intval($load_attr["tablet_column"]) ) ? wp_unslash($load_attr["tablet_column"]) : '';
	$mobile_column = (isset($load_attr["mobile_column"])  && intval($load_attr["mobile_column"]) ) ? wp_unslash($load_attr["mobile_column"]) : '';
	$DesktopClass = isset($load_attr["DesktopClass"]) ? sanitize_text_field( wp_unslash($load_attr["DesktopClass"]) ) : '';
	$TabletClass = isset($load_attr["TabletClass"]) ? sanitize_text_field( wp_unslash($load_attr["TabletClass"]) ) : '';
	$MobileClass = isset($load_attr["MobileClass"]) ? sanitize_text_field( wp_unslash($load_attr["MobileClass"]) ) : '';
	$postview = (isset($load_attr["postview"]) && intval($load_attr["postview"]) ) ? wp_unslash($load_attr["postview"]) : '';
	$display = (isset($load_attr["display"]) && intval($load_attr["display"]) ) ? wp_unslash($load_attr["display"]) : '';
	$txtLimt = isset($load_attr["TextLimit"]) ? wp_unslash($load_attr["TextLimit"]) : '';
	$TextCount = isset($load_attr["TextCount"]) ? wp_unslash($load_attr["TextCount"]) : '';
	$TextType = isset($load_attr["TextType"]) ? wp_unslash($load_attr["TextType"]) : '';
	$TextMore = isset($load_attr["TextMore"]) ? wp_unslash($load_attr["TextMore"]) : '';
	$TextDots = isset($load_attr["TextDots"]) ? wp_unslash($load_attr["TextDots"]) : '';
	$FancyStyle = isset($load_attr["FancyStyle"]) ? wp_unslash($load_attr["FancyStyle"]) : 'default';
	$DescripBTM = isset($load_attr["DescripBTM"]) ? wp_unslash($load_attr["DescripBTM"]) : '';
	$MediaFilter = isset($load_attr["MediaFilter"]) ? wp_unslash($load_attr["MediaFilter"]) : 'default';
	$CategoryWF = isset($load_attr["categorytext"]) ? wp_unslash($load_attr["categorytext"]) : '';
	$TotalPost = (isset($load_attr["TotalPost"]) && intval($load_attr["TotalPost"]) ) ? wp_unslash($load_attr["TotalPost"]) : '';
	$PopupOption = isset($load_attr["PopupOption"]) ? wp_unslash($load_attr["PopupOption"]) : 'OnFancyBox';

	$uid_sfeed=$load_class;
	$FinalData = get_transient("SF-Loadmore-".$load_class);
	$view = isset($_POST["view"]) ? intval($_POST["view"]) : [];	
	$feedshow = isset($_POST["feedshow"]) ? intval($_POST["feedshow"]) : [];

	$FancyBoxJS='';
	if($PopupOption == 'OnFancyBox'){
		$FancyBoxJS = "data-fancybox=".esc_attr($load_class);
	}

	$desktop_class=$tablet_class=$mobile_class='';
	if($layout != 'carousel'){
		$desktop_class = 'tp-col-lg-'.esc_attr($desktop_column);
		$tablet_class = 'tp-col-md-'.esc_attr($tablet_column);
		$mobile_class = 'tp-col-sm-'.esc_attr($mobile_column);
		$mobile_class .= ' tp-col-'.esc_attr($mobile_column);
	}	

	$FinalDataa=[];
	if( is_array($FinalData) ){
		$FinalDataa = array_slice($FinalData, $view , $feedshow);
	}

	if(!empty($FinalDataa)){
		foreach ($FinalDataa as $F_index => $loadData) {
			$PopupTarget=$PopupLink='';
			$uniqEach = uniqid();
			$PopupSylNum = "{$uid_sfeed}-{$F_index}-{$uniqEach}";
			$RKey = !empty($loadData['RKey']) ? $loadData['RKey'] : '';
			$PostId = !empty($loadData['PostId']) ? $loadData['PostId'] : '';
			$selectFeed = !empty($loadData['selectFeed']) ? $loadData['selectFeed'] : '';
			$Massage = !empty($loadData['Massage']) ? $loadData['Massage'] : '';
			$Description = !empty($loadData['Description']) ? $loadData['Description'] : '';
			$Type = !empty($loadData['Type']) ? $loadData['Type'] : '';
			$PostLink = !empty($loadData['PostLink']) ? $loadData['PostLink'] : '';
			$CreatedTime = !empty($loadData['CreatedTime']) ? $loadData['CreatedTime'] : '';
			$PostImage = !empty($loadData['PostImage']) ? $loadData['PostImage'] : '';
			$UserName = !empty($loadData['UserName']) ? $loadData['UserName'] : '';
			$UserImage = !empty($loadData['UserImage']) ? $loadData['UserImage'] : '';
			$UserLink = !empty($loadData['UserLink']) ? $loadData['UserLink'] : '';
			$socialIcon = !empty($loadData['socialIcon']) ? $loadData['socialIcon'] : '';
			$CategoryText = !empty($loadData['FilterCategory']) ? $loadData['FilterCategory'] : '';
			$ErrorClass = !empty($loadData['ErrorClass']) ? $loadData['ErrorClass'] : '';
			$EmbedURL = !empty($loadData['Embed']) ? $loadData['Embed'] : '';
			$EmbedType = !empty($loadData['EmbedType']) ? $loadData['EmbedType'] : '';
			$FbAlbum = !empty($loadData['FbAlbum']) ? $loadData['FbAlbum'] : '';

			$category_filter = $loop_category = '';
			if( !empty($CategoryWF=='yes') && !empty($CategoryText)  && $layout !='carousel' ){
				$loop_category = explode(',', $CategoryText);
				foreach( $loop_category as $category ) {
					$category = preg_replace('/[^A-Za-z0-9-]+/', '-', $category);
					$category_filter .=' '.esc_attr($category).' ';
				}
			}

			if($selectFeed == 'Facebook'){
				$Fblikes = !empty($loadData['FbLikes']) ? $loadData['FbLikes'] : 0;
				$comment = !empty($loadData['comment']) ? $loadData['comment'] : 0;
				$share = !empty($loadData['share']) ? $loadData['share'] : 0;
				$likeImg = THEPLUS_ASSETS_URL.'images/social-feed/like.png';
				$ReactionImg = THEPLUS_ASSETS_URL.'images/social-feed/love.png';
			}
			if($selectFeed == 'Twitter'){
				$TwRT = !empty($loadData['TWRetweet']) ? $loadData['TWRetweet'] : 0;
				$TWLike = !empty($loadData['TWLike']) ? $loadData['TWLike'] : 0;
				$TwReplyURL = !empty($loadData['TwReplyURL']) ? $loadData['TwReplyURL'] : '';
				$TwRetweetURL = !empty($loadData['TwRetweetURL']) ? $loadData['TwRetweetURL'] : '';
				$TwlikeURL = !empty($loadData['TwlikeURL']) ? $loadData['TwlikeURL'] : '';
				$TwtweetURL = !empty($loadData['TwtweetURL']) ? $loadData['TwtweetURL'] : '';
			}
			if($selectFeed == 'Vimeo'){
				$share = !empty($loadData['share']) ? $loadData['share'] : 0;
				$likes = !empty($loadData['likes']) ? $loadData['likes'] : 0;
				$comment = !empty($loadData['comment']) ? $loadData['comment'] : 0;
			}
			if($selectFeed == 'Youtube'){
				$view = !empty($loadData['view']) ? $loadData['view'] : 0;
				$likes = !empty($loadData['likes']) ? $loadData['likes'] : 0;
				$comment = !empty($loadData['comment']) ? $loadData['comment'] : 0;
				$Dislike = !empty($loadData['Dislike']) ? $loadData['Dislike'] : 0;
			}
			if( $Type == 'video' || $Type == 'photo' && $selectFeed != 'Instagram'){
				$videoURL = $PostLink;
				$ImageURL = $PostImage;
			}

			$IGGP_Icon='';
			if($selectFeed == 'Instagram'){
				$IGGP_Type = !empty($loadData['IG_Type']) ? $loadData['IG_Type'] : 'Instagram_Basic';

				if($IGGP_Type == 'Instagram_Graph'){
					$IGGP_Icon = !empty($loadData['IGGP_Icon']) ? $loadData['IGGP_Icon'] : '';
					$likes = !empty($loadData['likes']) ? $loadData['likes']: 0;
					$comment = !empty($loadData['comment']) ? $loadData['comment'] : 0;
					$videoURL = $PostLink;
					$PostLink = !empty($loadData['IGGP_PostLink']) ? $loadData['IGGP_PostLink'] : '';
					$ImageURL = $PostImage;

					$IGGP_CAROUSEL = !empty($loadData['IGGP_CAROUSEL']) ? $loadData['IGGP_CAROUSEL'] : '';
					if( $Type == "CAROUSEL_ALBUM" && $FancyStyle == 'default' ){
						$FancyBoxJS = "data-fancybox=".esc_attr("IGGP-CAROUSEL-{$F_index}-{$uniqEach}");
					}else{
						$FancyBoxJS = "data-fancybox=".esc_attr($uid_sfeed);
					}
				}else if($IGGP_Type == 'Instagram_Basic'){
					$videoURL = $PostLink;
					$ImageURL = $PostImage;
				}
			}
			if(!empty($FbAlbum)){
				$PostLink = !empty($PostLink[0]['link']) ? $PostLink[0]['link'] : 0;
				$FancyBoxJS = "data-fancybox=".esc_attr("album-Facebook{$F_index}-{$uid_sfeed}");
			}

			if( ($F_index < $TotalPost) && ( ($MediaFilter == 'default') || ($MediaFilter == 'ompost' && !empty($PostLink) && !empty($PostImage)) || ($MediaFilter == 'hmcontent' &&  empty($PostLink) && empty($PostImage) )) ){
				echo '<div class="grid-item '.esc_attr('feed-'.$selectFeed.' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$RKey.' '.$category_filter).'" data-index="'.esc_attr($selectFeed.$F_index).'">';				
					if(!empty($style)){
						include THEPLUS_PATH. 'includes/social-feed/social-feed-'.$style.'.php';
					}
				echo '</div>';
			}				
		}
	}
	$GridData = ob_get_clean();

	$result['success'] = 1;
	$result['totalFeed'] = isset($load_attr['totalFeed']) ? wp_unslash($load_attr['totalFeed']) : '';
	$result['FilterStyle'] = isset($load_attr['FilterStyle']) ? wp_unslash($load_attr['FilterStyle']) : '';
	$result['allposttext'] = isset($load_attr['allposttext']) ? wp_unslash($load_attr['allposttext']) : '';
	$result['HTMLContent'] = $GridData;
	$result['maximumposts'] = (int)$TotalPost;

	wp_send_json( $result );
}
add_action('wp_ajax_tp_feed_load','tp_feed_load');
add_action('wp_ajax_nopriv_tp_feed_load', 'tp_feed_load');

function tp_number_short( $n, $precision = 1 ) {
    if ($n < 900) {
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
	}
	
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}

/**
 * It is used for Remove transist for social feed, social remove, table widget
 *
 * @version 5.2.5
 */
function Tp_delete_transient() {
	$result = [];
	$delete_transient_nonce = isset($_POST['delete_transient_nonce']) ? $_POST['delete_transient_nonce'] : '';

	if( wp_verify_nonce($delete_transient_nonce, 'delete_transient_nonce') ) {
		global $wpdb;
			$table_name = $wpdb->prefix . "options";
			$DataBash = $wpdb->get_results( "SELECT * FROM $table_name" );
			$blockName = !empty($_POST['blockName']) ? $_POST['blockName'] : '';

			if($blockName == 'SocialFeed'){
				$transient = array(
					// facebook
						'Fb-Url-',
						'Fb-Time-',
						'Data-Fb-',
					// vimeo
						'Vm-Url-',
						'Vm-Time-',
						'Data-Vm-',
					// Instagram basic
						'IG-Url-',
						'IG-Profile-',
						'IG-Time-',
						'Data-IG-',	
					// Instagram Graph
						'IG-GP-Url-',
						'IG-GP-Time-',
						'IG-GP-Data-',
						'IG-GP-UserFeed-Url-',
						'IG-GP-UserFeed-Data-',
						'IG-GP-Hashtag-Url-',
						'IG-GP-HashtagID-data-',
						'IG-GP-HashtagData-Url-',
						'IG-GP-Hashtag-Data-',
						'IG-GP-story-Url-',
						'IG-GP-story-Data-',
						'IG-GP-Tag-Url-',
						'IG-GP-Tag-Data-',
					// Tweeter
						'Tw-BaseUrl-',
						'Tw-Url-',
						'Tw-Time-',
						'Data-tw-',
					// Youtube
						'Yt-user-',
						'Yt-user-Time-',
						'Data-Yt-user-',
						'Yt-Url-',
						'Yt-Time-',
						'Data-Yt-',
						'Yt-C-Url-',
						'Yt-c-Time-',
						'Data-c-Yt-',
					// loadmore
						'SF-Loadmore-',
					// Performance
						'SF-Performance-'
				);
			}else if($blockName == 'SocialReviews'){
				$transient = array(
					// Facebook
						'Fb-R-Url-',
						'Fb-R-Time-',
						'Fb-R-Data-',
					// Google
						'G-R-Url-',
						'G-R-Time-',
						'G-R-Data-',
					// loadmore
						'SR-LoadMore-',
					// Performance
						'SR-Performance-',
					// Beach
						'Beach-Url-',
						'Beach-Time-',
						'Beach-Data-',
				);
			}else if($blockName == 'Table'){
				// Google Sheet
				$transient = array(
					'tp-gs-table-url-',
					'tp-gs-table-time-',
					'tp-gs-table-Data-',
				);
			}

			foreach ($DataBash as $First) {
				foreach ($transient as $second) {
					$Find_Transient = !empty($First->option_name) ? strpos( $First->option_name, $second ) : '';
					if(!empty($Find_Transient)){
						$wpdb->delete( $table_name, array( 'option_name' => $First->option_name ) );
					}
				}
			}
			
		$result['success'] = 1;
		$result['blockName'] = $blockName;
	}else{
		$result['success'] = 0;
	}

	// echo json_encode($result);
	echo wp_send_json($result);
	// exit();
}

if(current_user_can("manage_options")){
	add_action( 'wp_ajax_Tp_delete_transient', 'Tp_delete_transient' );
	add_action( 'wp_ajax_nopriv_Tp_delete_transient', 'Tp_delete_transient' );
}

function Tp_socialreview_Gettoken() {
	$result = [];
	$delete_transient_nonce = isset($_POST['GetNonce']) ? $_POST['GetNonce'] : '';
	if( wp_verify_nonce($delete_transient_nonce, 'SocialReview_nonce') ) {

		$get_json = wp_remote_get("https://theplusaddons.com/wp-json/template_socialreview_api/v2/socialreviewAPI?time=".time());
		
		if ( is_wp_error( $get_json ) ) {
			wp_send_json_error( array( 'messages' => 'something wrong in API' ) );
		}else{
			$URL_StatusCode = wp_remote_retrieve_response_code($get_json);
			if($URL_StatusCode == 200){
				$getdata = wp_remote_retrieve_body($get_json);
				$result['SocialReview'] = json_decode($getdata, true);
				$result['success'] = 1;
				wp_send_json($result);
			}
		}
	}else{
		$result['success'] = 0;
	}

	exit();
}
add_action( 'wp_ajax_theplus_socialreview_Gettoken', 'Tp_socialreview_Gettoken' );
add_action( 'wp_ajax_nopriv_theplus_socialreview_Gettoken', 'Tp_socialreview_Gettoken' );

/*load more reviews*/
function tp_reviews_load(){
	ob_start();
	$result = [];
	$load_attr = isset($_POST["loadattr"]) ? wp_unslash( $_POST["loadattr"] ) : '';
	if(empty($load_attr)){
		ob_get_contents();
		exit;
		ob_end_clean();
	}
	$load_attr = tp_check_decrypt_key($load_attr);
	$load_attr = json_decode($load_attr,true);
	if(!is_array($load_attr)){
		ob_get_contents();
		exit;
		ob_end_clean();
	}
	$nonce = (isset($load_attr["theplus_nonce"])) ? wp_unslash( $load_attr["theplus_nonce"] ) : '';
	if (!wp_verify_nonce($nonce, 'theplus-addons')){
		die ( 'Security checked!');
	}

	$load_class = isset($load_attr["load_class"]) ? sanitize_text_field( wp_unslash($load_attr["load_class"]) ) : '';
	$style = isset($load_attr["style"]) ? sanitize_text_field( wp_unslash($load_attr["style"]) ) : '';
	$layout = isset($load_attr["layout"]) ? sanitize_text_field( wp_unslash($load_attr["layout"]) ) : '';
	$desktop_column = (isset($load_attr["desktop_column"])  && intval($load_attr["desktop_column"]) ) ? wp_unslash($load_attr["desktop_column"]) : '';
	$tablet_column = (isset($load_attr["tablet_column"])  && intval($load_attr["tablet_column"]) ) ? wp_unslash($load_attr["tablet_column"]) : '';
	$mobile_column = (isset($load_attr["mobile_column"])  && intval($load_attr["mobile_column"]) ) ? wp_unslash($load_attr["mobile_column"]) : '';
	$DesktopClass = isset($load_attr["DesktopClass"]) ? sanitize_text_field( wp_unslash($load_attr["DesktopClass"]) ) : '';
	$TabletClass = isset($load_attr["TabletClass"]) ? sanitize_text_field( wp_unslash($load_attr["TabletClass"]) ) : '';
	$MobileClass = isset($load_attr["MobileClass"]) ? sanitize_text_field( wp_unslash($load_attr["MobileClass"]) ) : '';
	$CategoryWF = isset($load_attr["categorytext"]) ? sanitize_text_field( wp_unslash($load_attr["categorytext"]) ) : '';
	$FeedId = (!empty($_POST["FeedId"]) && isset( $load_attr["FeedId"] ) ) ? wp_unslash( preg_split("/\,/", $load_attr["FeedId"]) ) : '';
	$txtLimt = isset($load_attr["TextLimit"]) ? wp_unslash($load_attr["TextLimit"]) : '';
	$TextCount = isset( $load_attr["TextCount"]) ? wp_unslash($load_attr["TextCount"]) : '';
	$TextType = isset($load_attr["TextType"]) ? wp_unslash($load_attr["TextType"]) : '';
	$TextMore = isset($load_attr["TextMore"]) ? wp_unslash($load_attr["TextMore"]) : '';
	$TextDots = isset($load_attr["TextDots"]) ? wp_unslash($load_attr["TextDots"]) : '';
	$postview = (isset($load_attr["postview"]) && intval($load_attr["postview"]) ) ? wp_unslash($load_attr["postview"]) : '';
	$display = (isset( $load_attr["display"]) && intval($load_attr["display"]) ) ? wp_unslash($load_attr["display"]) : '';
	$view = isset($_POST["view"]) ? intval($_POST["view"]) : [];	
	$feedshow = isset($_POST["feedshow"]) ? intval($_POST["feedshow"]) : [];
	$FinalData = get_transient("SR-LoadMore-".$load_class);
	$FinalDataa = array_slice($FinalData, $view , $feedshow);

	$desktop_class=$tablet_class=$mobile_class='';
	if($layout != 'carousel'){
		$desktop_class .= ' tp-col-12';
		$desktop_class = 'tp-col-lg-'.esc_attr($desktop_column);
		$tablet_class = 'tp-col-md-'.esc_attr($tablet_column);
		$mobile_class = 'tp-col-sm-'.esc_attr($mobile_column);
		$mobile_class .= ' tp-col-'.esc_attr($mobile_column);	
	}

	foreach ($FinalDataa as $F_index => $Review) {
		$RKey = !empty($Review['RKey']) ? $Review['RKey'] : '';
		$RIndex = !empty($Review['Reviews_Index']) ? $Review['Reviews_Index'] : '';
		$PostId = !empty($Review['PostId']) ? $Review['PostId'] : '';
		$Type = !empty($Review['Type']) ? $Review['Type'] : '';
		$Time = !empty($Review['CreatedTime']) ? $Review['CreatedTime'] : '';
		$UName = !empty($Review['UserName']) ? $Review['UserName'] : '';
		$UImage = !empty($Review['UserImage']) ? $Review['UserImage'] : '';
		$ULink = !empty($Review['UserLink']) ? $Review['UserLink'] : '';
		$PageLink = !empty($Review['PageLink']) ? $Review['PageLink'] : '';
		$Massage = !empty($Review['Massage']) ? $Review['Massage'] : '';
		$Icon = !empty($Review['Icon']['value']) ? $Review['Icon']['value'] : 'fas fa-star';
		$Logo = !empty($Review['Logo']) ? $Review['Logo'] : '';
		$rating = !empty($Review['rating']) ? $Review['rating'] : '';
		$CategoryText = !empty($Review['FilterCategory']) ? $Review['FilterCategory'] : '';
		$ReviewClass = !empty($Review['selectType']) ? ' '.$Review['selectType'] : '';
		$ErrClass = !empty($Review['ErrorClass']) ? $Review['ErrorClass'] : '';
		$PlatformName = !empty($Review['selectType']) ? ucwords(str_replace('custom', '', $Review['selectType'])) : '';	

		$category_filter=$loop_category='';
		if( !empty($CategoryWF == 'yes') && !empty($CategoryText)  && $layout != 'carousel' ){
			$loop_category = explode(',', $CategoryText);
			foreach( $loop_category as $category ) {
				$category = preg_replace('/[^A-Za-z0-9-]+/', '-', $category);
				$category_filter .=' '.esc_attr($category).' ';
			}
		}
		if(!empty($style)){
		  include THEPLUS_PATH. 'includes/social-reviews/social-review-' . sanitize_file_name($style) . '.php';
	    }
	}

	$GridData = ob_get_clean();
	$result['success'] = 1;
	$result['TotalReview'] = isset($load_attr['TotalReview']) ? wp_unslash($load_attr['TotalReview']) : '';
	$result['FilterStyle'] = isset($load_attr['FilterStyle']) ? wp_unslash($load_attr['FilterStyle']) : '';
	$result['allposttext'] = isset($load_attr['allposttext']) ? wp_unslash($load_attr['allposttext']) : '';
	$result['HTMLContent'] = $GridData;

	echo json_encode($result);
	exit();
}
add_action('wp_ajax_tp_reviews_load','tp_reviews_load');
add_action('wp_ajax_nopriv_tp_reviews_load', 'tp_reviews_load');

/*Search filter*/
function theplus_filter_post(){
	global $post,$wpdb;	
	if(!isset($_POST['nonce']) || empty($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'theplus-searchfilter' )){	
		die ('Security checked!');
	}

	$optiondata = isset($_POST["option"]) ? wp_unslash( $_POST["option"] ) : '';
	if(empty($optiondata) || !is_array($optiondata)){
		ob_start();
			ob_get_contents();
			exit;
		ob_end_clean();
	}
	
	$Maplocation=[];
	$tablet_metro_class=$kij=0;$ji=1;$ij='';$offset=0;
	foreach ($optiondata as $key => $postdata) {
		$FilterType = (!empty($postdata['filtertype']) && $postdata['filtertype'] == 'search_list') ? $postdata['filtertype'] : '';
		$widgetName = isset($postdata["load"]) ? sanitize_text_field(wp_unslash($postdata["load"])) : '';
		$post_load = $widgetName;
		$desktop_class=$tablet_class=$mobile_class='';
		
		if($widgetName == 'dynamiclisting' || $widgetName == 'products'){
			$post_type = isset($postdata["post_type"]) ? sanitize_text_field( wp_unslash($postdata["post_type"]) ) : '';
			$layout = isset($postdata["layout"]) ? sanitize_text_field(wp_unslash($postdata["layout"])) : '';
			$texonomy_category = isset($postdata["texonomy_category"]) ? sanitize_text_field( wp_unslash($postdata["texonomy_category"]) ) : '';

			$DisplayPost = (isset($postdata["display_post"]) && intval($postdata["display_post"])) ? sanitize_text_field($postdata["display_post"]) : 4;
			$display_post = $DisplayPost;		// Not used

			$post_load_more = (isset($postdata["post_load_more"]) && intval($postdata["post_load_more"])) ?  wp_unslash($postdata["post_load_more"]) : 1;
			$post_title_tag = isset($postdata["post_title_tag"]) ? wp_unslash($postdata["post_title_tag"]) : '';

			$style = isset($postdata["style"]) ? sanitize_text_field(wp_unslash($postdata["style"])) : 'style-1';
			$desktop_column = (isset($postdata["desktop-column"])  && intval($postdata["desktop-column"]) ) ? wp_unslash($postdata["desktop-column"]) : 3;
			$tablet_column = (isset($postdata["tablet-column"])  && intval($postdata["tablet-column"]) ) ? wp_unslash($postdata["tablet-column"]) : 4;
			$mobile_column = (isset($postdata["mobile-column"])  && intval($postdata["mobile-column"]) ) ? wp_unslash($postdata["mobile-column"]) : 6;	
			$metro_column = isset($postdata["metro_column"]) ? wp_unslash($postdata["metro_column"]) : '';
			$metro_style = isset($postdata["metro_style"]) ? wp_unslash($postdata["metro_style"]) : '';
			$responsive_tablet_metro = isset($postdata["responsive_tablet_metro"]) ? wp_unslash($postdata["responsive_tablet_metro"]) : '';
			$tablet_metro_column = isset($postdata["tablet_metro_column"]) ? wp_unslash($postdata["tablet_metro_column"]) : '';
			$tablet_metro_style = isset($postdata["tablet_metro_style"]) ? wp_unslash($postdata["tablet_metro_style"]) : '';
			$category_type = isset($postdata["category_type"]) ? $postdata["category_type"] : 'false';
			$category = isset($postdata["category"]) ? wp_unslash($postdata["category"])  : '';
			$order_by = isset($postdata["order_by"]) ? sanitize_text_field( wp_unslash($postdata["order_by"]) ) : '';
			$post_order = isset($postdata["post_order"]) ? sanitize_text_field( wp_unslash($postdata["post_order"]) ) : '';
			$filter_category = isset($postdata["filter_category"]) ? sanitize_text_field(wp_unslash($postdata["filter_category"])) : '';
			$animated_columns = isset($postdata["animated_columns"]) ? sanitize_text_field(wp_unslash($postdata["animated_columns"])) : '';
			$featured_image_type = isset($postdata["featured_image_type"]) ? wp_unslash($postdata["featured_image_type"]) : '';
			$display_thumbnail = isset($postdata["display_thumbnail"]) ? wp_unslash($postdata["display_thumbnail"]) : '';
			$thumbnail = isset($postdata["thumbnail"]) ? wp_unslash($postdata["thumbnail"]) : '';
			$thumbnail_car = isset($postdata["thumbnail_car"]) ? wp_unslash($postdata["thumbnail_car"]) : '';
			$display_theplus_quickview = isset($postdata["display_theplus_quickview"]) ? wp_unslash($postdata["display_theplus_quickview"]) : '';
			$includePosts = (isset($postdata["include_posts"])  && intval($postdata["include_posts"]) ) ? wp_unslash($postdata["include_posts"]) : '';
			$excludePosts = (isset($postdata["exclude_posts"])  && intval($postdata["exclude_posts"]) ) ? wp_unslash($postdata["exclude_posts"]): '';		

			$dynamic_template = isset($postdata['skin_template'] ) ? $postdata['skin_template'] : '';
			$paged = (isset( $postdata["page"] ) && intval($postdata["page"]) ) ?  wp_unslash($postdata["page"]) : ''; // Not used

			$is_archivePage = isset($postdata['is_archive']) ? $postdata['is_archive'] : 0;
			$Archivepage = isset($postdata['archive_page']) ? $postdata['archive_page'] : '';
			$ArchivepageType = ( !empty($Archivepage) && !empty($Archivepage['archive_Type']) ) ? sanitize_text_field($Archivepage['archive_Type']) : '';
			$ArchivepageID = ( !empty($Archivepage) && !empty($Archivepage['archive_Id']) ) ? $Archivepage['archive_Id'] : '';
			$ArchivepageName = ( !empty($Archivepage) && !empty($Archivepage['archive_Name']) ) ? $Archivepage['archive_Name'] : '';

			$is_searchPage = isset($postdata['is_search']) ? $postdata['is_search'] : 0;
			$SearchPage = isset($postdata['is_search_page']) ? $postdata['is_search_page'] : '';
			$SearchPageval = ( !empty($SearchPage) && !empty($SearchPage['is_search_value']) ) ? sanitize_text_field($SearchPage['is_search_value']) : '';
			$CustonQuery = !empty($postdata['custon_query']) ? $postdata['custon_query'] : '';

			$enable_archive_search = (!empty($postdata['enablearchive']) && $postdata['enablearchive'] === 'true') ? 'true' : 'false';

			if( $layout != 'carousel' && $layout != 'metro' ){
				$desktop_class = 'tp-col-lg-'.esc_attr($desktop_column);
				$tablet_class = 'tp-col-md-'.esc_attr($tablet_column);
				$mobile_class = 'tp-col-sm-'.esc_attr($mobile_column);
				$mobile_class .= ' tp-col-'.esc_attr($mobile_column);	
			}
		}

		if($widgetName == 'dynamiclisting'){
			$title_desc_word_break = isset($postdata["title_desc_word_break"]) ? wp_unslash($postdata["title_desc_word_break"]) : '';
			$style_layout = isset($postdata["style_layout"]) ? sanitize_text_field( wp_unslash($postdata["style_layout"]) ) : '';
			$post_tags = isset($postdata["post_tags"]) ? wp_unslash($postdata["post_tags"]) : '';
			$post_authors = isset($postdata["post_authors"]) ? wp_unslash($postdata["post_authors"]) : '';
			$display_post_meta = isset($postdata["display_post_meta"]) ? sanitize_text_field( wp_unslash($postdata["display_post_meta"]) ) : '';
			$post_meta_tag_style = isset($postdata["post_meta_tag_style"]) ? wp_unslash($postdata["post_meta_tag_style"]) : '';
			$display_post_meta_date = isset($postdata["display_post_meta_date"]) ? wp_unslash($postdata["display_post_meta_date"]) : '';
			$display_post_meta_author = isset($postdata["display_post_meta_author"]) ? wp_unslash($postdata["display_post_meta_author"]) : '';
			$display_post_meta_author_pic = isset($postdata["display_post_meta_author_pic"]) ? wp_unslash($postdata["display_post_meta_author_pic"]) : '';
			$display_excerpt = isset($postdata["display_excerpt"]) ? sanitize_text_field(wp_unslash($postdata["display_excerpt"])) : '';
			$post_excerpt_count = isset($postdata["post_excerpt_count"]) ? wp_unslash($postdata["post_excerpt_count"]) : '';
			$display_post_category = isset($postdata["display_post_category"]) ? wp_unslash($postdata["display_post_category"]) : '';
			$dpc_all = isset($postdata["dpc_all"]) ? wp_unslash($postdata["dpc_all"]) : '';
			$post_category_style = isset($postdata["post_category_style"]) ? sanitize_text_field( wp_unslash($postdata["post_category_style"]) ) : '';
			$display_title_limit = isset($postdata["display_title_limit"]) ? wp_unslash($postdata["display_title_limit"]) : '';
			$display_title_by = isset($postdata["display_title_by"]) ? wp_unslash($postdata["display_title_by"]) : '';
			$display_title_input = isset($postdata["display_title_input"]) ? wp_unslash($postdata["display_title_input"]) : '';
			$display_title_3_dots = isset($postdata["display_title_3_dots"]) ? wp_unslash($postdata["display_title_3_dots"]) : '';
			$feature_image = isset($postdata["feature_image"]) ? wp_unslash($postdata["feature_image"]) : '';
			$full_image_size = !empty($postdata['full_image_size']) ? $postdata['full_image_size'] : 'yes';
			$author_prefix = isset($postdata["author_prefix"]) ? wp_unslash($postdata["author_prefix"]) : 'By';
		}else if($widgetName == 'products'){
			$b_dis_badge_switch = isset( $postdata["badge"] ) ? sanitize_text_field( wp_unslash($postdata["badge"]) ) : '';
			$out_of_stock = isset( $postdata["out_of_stock"] ) ? sanitize_text_field( wp_unslash($postdata["out_of_stock"]) ) : '';
			$variation_price_on = isset( $postdata["variationprice"] ) ? sanitize_text_field( wp_unslash($postdata["variationprice"]) ) : '';
			$hover_image_on_off = isset( $postdata["hoverimagepro"] ) ? sanitize_text_field( wp_unslash($postdata["hoverimagepro"]) ) : '';
			$display_product = isset($postdata["display_product"]) ? wp_unslash($postdata["display_product"]) : '';
			$display_rating = isset($postdata["display_rating"] ) ? wp_unslash($postdata["display_rating"]) : '';
			$display_catagory = isset($postdata["display_catagory"] ) ? wp_unslash($postdata["display_catagory"]) : '';
			$display_yith_list = isset($postdata["display_yith_list"] ) ? wp_unslash($postdata["display_yith_list"]) : '';
			$display_yith_compare = isset($postdata["display_yith_compare"] ) ? wp_unslash($postdata["display_yith_compare"]) : '';
			$display_yith_wishlist = isset($postdata["display_yith_wishlist"] ) ? wp_unslash($postdata["display_yith_wishlist"]) : '';
			$display_yith_quickview = isset($postdata["display_yith_quickview"] ) ? wp_unslash($postdata["display_yith_quickview"]) : '';
			$display_cart_button = isset($postdata["cart_button"]) ? wp_unslash($postdata["cart_button"]) : '';
			$dcb_single_product = isset( $postdata["dcb_single_product"] ) ? wp_unslash($postdata["dcb_single_product"]) : '';
			$dcb_variation_product = isset( $postdata["dcb_variation_product"] ) ? wp_unslash($postdata["dcb_variation_product"]) : '';
		}else if($widgetName == 'googlemap'){
			$Places = isset($postdata["places"]) ? $postdata["places"] : '';
			$Options = isset($postdata["options"]) ? $postdata["options"] : '';
			$listing_type = isset($postdata["listing_type"]) ? $postdata["listing_type"] : '';
			$PostId = isset($postdata["PostId"]) ? $postdata["PostId"] : '';
			$MapWidgetId = isset($postdata["MapWidgetId"]) ? $postdata["MapWidgetId"] : '';
		}

		$loadmore_SF = !empty($postdata['loadMore_sf']) ? $postdata['loadMore_sf'] : 0;
		if(!empty($loadmore_SF)){
			$new_offset = !empty($postdata['new_offset']) ? $postdata['new_offset'] : 0;
			$offset = $new_offset;
			$DisplayPost = $post_load_more;
		}

		$Lazyload_SF = !empty($postdata['lazyload_sf']) ? $postdata['lazyload_sf'] : 0;
		if(!empty($Lazyload_SF)){
			$new_offset = !empty($postdata['new_offset']) ? $postdata['new_offset'] : 0;
			$offset = $new_offset;
			$DisplayPost = $post_load_more;
		}

		$Paginate_sf = !empty($postdata['Paginate_sf']) ? $postdata['Paginate_sf'] : 0;
		if(!empty($Paginate_sf)){
			$new_offset = !empty($postdata['new_offset']) ? $postdata['new_offset'] : "";
			$offset = $new_offset;
		}

		if( !empty($CustonQuery) ){
			$args=[];
			if (has_filter($CustonQuery) ){
				$args = apply_filters($CustonQuery, $args);
			}
		}else{
			if($widgetName == 'dynamiclisting' || $widgetName == 'products'){
				$args = array(
					'post_type' => $post_type,
					'post_status' => 'publish',
					'posts_per_page' => $DisplayPost,
					'offset' => $offset,
					'orderby' => $order_by,
					'order'	=> $post_order,
				);
			}
		}

		if(!empty($excludePosts)){
			$args['post__not_in'] = explode(',',$excludePosts);
		}

		if(!empty($includePosts)){
			
			$args['post__in'] = explode(',',$includePosts);
		}

		if(!empty($post_authors) && $post_type == 'post' && $widgetName == 'dynamiclisting'){
			$args['author'] = $post_authors;
		}

		if ($FilterType == 'search_list'){
			$meta_keyArr=$meta_keyArr1=$attr_tax=$TmpPostID=[];

			if( !empty($postdata['seapara']) ){
				foreach($postdata['seapara'] as $item => $val) {
					$FieldValue = (!empty($val) && !empty($val['field'])) ? $val['field'] : '';
					$TypeValue = (!empty($val) && !empty($val['type'])) ? $val['type'] : '';
					$DataValue = (!empty($val) && !empty($val['value'])) ? $val['value'] : '';
					$NameValue = (!empty($val) && !empty($val['name'])) ? $val['name'] : '';
					$keyEnable=$WooSortEnable=0;
					$PubliStatus = 'publish';

					if($TypeValue == 'taxonomy'){
						if($FieldValue == 'rating' && $post_type == 'product' && !empty($DataValue)){
							$RatingQ = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->comments}.comment_post_ID FROM {$wpdb->commentmeta}, {$wpdb->comments} WHERE {$wpdb->commentmeta}.meta_key='rating' AND {$wpdb->commentmeta}.meta_value = %d AND {$wpdb->comments}.comment_type='review' AND {$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID", $DataValue ) );

							if(!empty($RatingQ)){
								foreach ($RatingQ as $value) {
									if(!empty($value->comment_post_ID)){
										$TmpPostID[] = $value->comment_post_ID;
									}
								}
							}else{
								$TmpPostID[] = [];
							}
						}else if($FieldValue == 'search' && strlen($DataValue) > 1){
							$Generic = !empty($val) ? $val['Generic'] : [];
							$AllData=$GTitle=$Gexcerpt=$Gcontent=$Gname=$PCat=$PTag=[];

							if( !empty($Generic['GFEnable']) ){
								$Result = ($Generic['GFSType'] == 'fullMatch') ? "{$wpdb->esc_like($DataValue)}%" : "%{$wpdb->esc_like($DataValue)}%";
								
								$PType = $wpdb->prepare( "AND {$wpdb->posts}.post_type = %s", $post_type );
								
								if( !empty($Generic['GFTitle']) ){
									$PTitle = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s ", $Result );
									$GTitle = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE {$PTitle} AND {$wpdb->posts}.post_status = %s {$PType}", $PubliStatus ) );
								}

								if( !empty($Generic['GFContent']) ){
									$Pcontent = $wpdb->prepare( "{$wpdb->posts}.post_content LIKE %s ", $Result );
									$Gcontent = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE {$Pcontent} AND {$wpdb->posts}.post_status = %s {$PType}", $PubliStatus ) );
								}
								if( !empty($Generic['GFExcerpt']) ){
									$Pexcerpt = $wpdb->prepare( "{$wpdb->posts}.post_excerpt LIKE %s ", $Result );
									$Gexcerpt = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE {$Pexcerpt} AND {$wpdb->posts}.post_status = %s {$PType}", $PubliStatus ) );
								}

								if( !empty($Generic['GFName']) ){
									$Pname = $wpdb->prepare( "{$wpdb->posts}.post_name LIKE %s ", $Result);
									$Gname = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE {$Pname} AND {$wpdb->posts}.post_status = %s {$PType}", $PubliStatus ) );
								}

								if( !empty($Generic['GFCategory']) ){
									$CatType='category_name';
									if($post_type == 'post'){
										$CatTaxonomy = 'category';
									}else if($post_type == 'product'){
										$CatTaxonomy=$CatType='product_cat';
									}else{
										$CatTaxonomy = 'any';
									}

									$PCat = query_posts( array(
										'taxonomy' 		=> $CatTaxonomy,
										'post_type'		=> $post_type,
										$CatType	 	=> $DataValue,
										'post_status' 	=> 'publish',
										'posts_per_page' => -1,
										'orderby' 		=> 'name',
										'order'			=> 'ASC',
										'hide_empty'	=> 0,				
									) );
								}

								if( !empty($Generic['GFTags']) ){
									if($post_type == 'post'){
										$TagTaxonomy = 'post_tag';
										$TagType = 'tag_slug__in';
									}else if($post_type == 'product'){
										$TagTaxonomy = 'product_tag';
										$TagType = 'product_tag';
									}else{
										/**static tag Taxonomy*/
										$TagTaxonomy = 'post_tag';
										$TagType = 'tag';
									}

									$PTag = query_posts( array(
										'taxonomy' 		=> $TagTaxonomy,
										'post_type'		=> $post_type,
										$TagType		=> $DataValue,
										'post_status' 	=> 'publish',
										'posts_per_page' => -1,
										'orderby' 		=> 'name',
										'order'			=> 'ASC',
										'hide_empty' 	=> 0,
									) );
								}

								if( !empty($GTitle) ){
									array_push( $AllData, $GTitle );
								}

								if( !empty($Gcontent) ){
									array_push( $AllData, $Gcontent );
								}

								if( !empty($Gexcerpt) ){
									array_push( $AllData, $Gexcerpt );
								}

								if( !empty($Gname) ){
									array_push( $AllData, $Gname );
								}

								if( !empty($PTag) ){
									array_push( $AllData, $PTag );
								}

								if( !empty($PCat) ){
									array_push( $AllData, $PCat );
								}

								// array_push( $AllData, $GTitle, $Gcontent, $Gexcerpt, $Gname, $PTag, $PCat );

								if( !empty($AllData) ){
									foreach($AllData as $value) {
										if(!empty($value)){
											foreach($value as $vall){
												if(!empty($vall->ID)){
													$TmpPostID[] = $vall->ID;
												}
											}
										}else{
											$TmpPostID[] = array();
										}
									}
								}
							}else{
								$args['s'] = $DataValue;
							}	
						}else if($FieldValue == 'alphabet'){
							if( !empty($DataValue) ){
								foreach ($DataValue as $one) {
									$PTitle = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s ", $wpdb->esc_like($one).'%' );
									$PType = $wpdb->prepare( "AND {$wpdb->posts}.post_type=%s", $post_type);
									$AlphaQ = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$PTitle} AND {$wpdb->posts}.post_status=%s {$PType}", $PubliStatus ) );

									if(!empty($AlphaQ)){
										foreach ($AlphaQ as $two) {
											if(!empty($two->ID)){
												$TmpPostID[] = $two->ID;
											}
										}
									}else{
										$TmpPostID[] = 0;
									}
						 		}
							}
						}else if($FieldValue == 'date'){
							$args['date_query'] = array(
								array(
									'after' => (!empty($DataValue) && !empty($DataValue[0])) ? $DataValue[0] : '',
									'before' => (!empty($DataValue) && !empty($DataValue[1])) ? $DataValue[1] : '',
									'inclusive' => true,
								),
							);
						}else if($FieldValue == 'range'){
							$Range_Q[] = array(
								'key' => '_price',
								'value' => $DataValue,
								'compare' => 'BETWEEN',
								'type' => 'NUMERIC' 
							);

							if(!empty($Range_Q)){
								$meta_keyArr[] = $Range_Q;
							}
						}else if(($FieldValue == 'color' || $FieldValue == 'image' || $FieldValue == 'button') && $post_type == 'product'){
							if( !empty($DataValue) && !empty($NameValue) ){
								$attr_tax[] = array(
									'taxonomy' => $NameValue,
									'field' => 'id',
									'terms' => $DataValue,
									'operator' => 'IN',
								);
							}
						}else if($FieldValue == 'tabbing' || $FieldValue == 'checkBox' || $FieldValue == 'DropDown' || $FieldValue == 'radio'){
							if(!empty($DataValue)){
								$keyEnable = 1;
							}
						}else if($FieldValue == 'woo_SgDropDown' || $FieldValue == 'woo_SgTabbing'){
							$keyEnable=$WooSortEnable=1;
						}else if($FieldValue == 'autocomplete'){
							$Maplocation = theplus_searchfilter_autocomplete($NameValue, $DataValue, $TypeValue, $val ,$MapWidgetId, $PostId);
						}
					}else if($TypeValue == 'acf_conne'){
						if(class_exists('ACF')){
							$ACF_Key = acf_get_field($NameValue)['key'];
							if(!empty($ACF_Key)){
								if($FieldValue == 'rating'){
									$Rating_Q[] = array(
										'key' => $NameValue,
										'value' => $DataValue,
										'compare' => '=',
										'type' => 'text',
									);
									if(!empty($Rating_Q)){
										$meta_keyArr[] = $Rating_Q;
									}
								}else if($FieldValue == 'search'){
									if(strlen($DataValue) > 1){
										$data=[];
										$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID AND {$wpdb->posts}.post_status = %s", $PubliStatus ) );
										if( !empty($DB_Result) ){
											foreach ($DB_Result as $value) {
												$PostID = !empty($value->ID) ? $value->ID : '';
												$ACFdata = get_field($ACF_Key, $PostID);
											
												if(!empty($ACFdata)){
													$array2 = explode("|", $ACFdata);
													foreach ($array2 as $val) {
														if(trim( strtolower($val) ) == strtolower($DataValue) ){
															$data[] = $ACFdata;
														}
													}
												}
											}
											if(!empty($data)){
												$meta_keyArr[] = array(
													'key' => $NameValue,
													'value'	=> $data,
													'compare' => 'IN'
												);
											}else{
												$meta_keyArr[] = array(
													'key' => $NameValue,
													'value' => '',
													'compare' => '==',
												);
											}
										}
									}
								}else if($FieldValue == 'date'){
									if( !empty($DataValue) && !empty($NameValue) ){
										$Date_Q[] = array(
											'key' => $NameValue,
											'value' => $DataValue,
											'compare' => 'BETWEEN',
											'type' => 'DATE',
										);

										if(!empty($Date_Q)){
											$meta_keyArr[] = $Date_Q;
										}
									}
								}else if($FieldValue == 'range'){
									if( !empty($DataValue) && !empty($NameValue) ){
										$Range_Q[] = array(
											'key'		=> $NameValue,
											'value'		=> $DataValue,
											'compare'   => 'BETWEEN',
											'type'      => 'NUMERIC',
										);

										if(!empty($Range_Q)){
											$meta_keyArr[] = $Range_Q;
										}
									}
								}else if($FieldValue == 'color' || $FieldValue == 'image' || $FieldValue == 'button'){
									$Rangee_Q[] = array(
										'key'	 => $NameValue,
										'value'	 => $DataValue,
										'compare'=> 'IN',
									);
									if(!empty($Rangee_Q)){
										$meta_keyArr[] = $Rangee_Q;
									}
								}else if($FieldValue == 'tabbing' && !empty($DataValue)){
									$data=[];
									$DB_Result = $wpdb->get_results(  $wpdb->prepare(  "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID AND {$wpdb->posts}.post_status=%s", $PubliStatus ) );
									
									foreach ($DB_Result as $value) {
										$PostID = !empty($value->ID) ? $value->ID : '';
										$ACFdata = get_field($ACF_Key, $PostID);
										
										if(!empty($ACFdata)){
											$array2 = explode("|", $ACFdata);
											foreach ($array2 as $val) {
												$fvalue = str_replace( ' ', '-', ltrim(rtrim($val)) );
												if (in_array($fvalue, $DataValue) ){
													$data[] = $ACFdata;
												}
											}
										}
									}

									$tabbing_Q[] = array(
										'key'	 => $NameValue,
										'value'	 => $data,
										'compare'=> 'IN',
									);
									if(!empty($tabbing_Q)){
										$meta_keyArr[] = $tabbing_Q;
									}
								}else if($FieldValue == 'radio'){
									if( !empty($DataValue) && !empty($NameValue) ){
										$Rangee_Q[] = array(
											'key'	 => $NameValue,
											'value'	 => $DataValue,
											'compare'=> '=',
										);

										if(!empty($Rangee_Q)){
											$meta_keyArr[] = $Rangee_Q;
										}
									}
								}else if($FieldValue == 'checkBox'){
									if( !empty($DataValue) && !empty($NameValue) ){
										foreach($DataValue as $metadata){
											$CheckBox_Q[] = array(
												'key' => $NameValue,
												'value'	=> $metadata,
												'compare' => 'LIKE'
											);
										}

										if(!empty($CheckBox_Q)){
											$meta_keyArr[] = $CheckBox_Q;
										}
									}
								}else if($FieldValue == 'DropDown'){
									if( !empty($DataValue) ){
										$DropDown_Q[] = array(
											'key' => $NameValue,
											'value'	=> $DataValue,
											'compare' => 'LIKE'
										);
										if(!empty($DropDown_Q)){
											$meta_keyArr[] = $DropDown_Q;
										}
									}
								}else{
									$Rangee_Q[] = array(
										'key' => $item,
										'value' => $val,
										'compare' => '=',
									);
									if(!empty($Rangee_Q)){
										$meta_keyArr[] = $Rangee_Q;
									}
								}
							}
						}
					}else if($TypeValue == 'toolset_conne' || $TypeValue == 'pods_conne' || $TypeValue == 'metabox_conne'){	/******* Connection Toolset - PODs *******/
						if( $TypeValue == 'toolset_conne' && !is_plugin_active('types/wpcf.php') ){
							return;
						}else if( $TypeValue == 'pods_conne' && !class_exists('PodsInit') ){
							return;
						}else if( $TypeValue == 'metabox_conne' && !class_exists('RWMB_Field') ){
							return;
						}else{
							if( !empty($DataValue) ){
								$Connnection_Q = [];
								if($FieldValue == 'rating'){
									$Connnection_Q = theplus_searchfilster_rating($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'search'){
									$Connnection_Q = theplus_searchfilter_input($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'date'){
									$Connnection_Q = theplus_searchfilter_DateFilter($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'tabbing'){
									$Connnection_Q = theplus_searchfilter_Tabbing($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'range'){
									$Connnection_Q = theplus_searchfilter_range($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'radio'){
									$Connnection_Q = theplus_searchfilster_radiobtn($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'checkBox'){
									if($TypeValue == 'toolset_conne'){
										$TmpPostID = theplus_searchfilster_checkBox($NameValue, $DataValue, $PubliStatus, $TypeValue);
									}else if($TypeValue == 'pods_conne' || $TypeValue == 'metabox_conne'){
										$Connnection_Q = theplus_searchfilster_checkBox($NameValue, $DataValue, $PubliStatus, $TypeValue);
									}
								}else if($FieldValue == 'DropDown'){
									$Connnection_Q = theplus_searchfilster_dropdown($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'image'){
									$Connnection_Q = array(
										'key'	 => $NameValue,
										'value'	 => $DataValue,
										'compare'=> 'IN',
									);
								}else if($FieldValue == 'button'){
									$Connnection_Q = theplus_searchfilter_button($NameValue, $DataValue, $PubliStatus, $TypeValue);
								}else if($FieldValue == 'color'){
									$Connnection_Q = theplus_searchfilster_color($NameValue, $DataValue, $PubliStatus, $TypeValue);				
								}else if($FieldValue == 'autocomplete'){
									$Maplocation = theplus_searchfilter_autocomplete($NameValue, $DataValue, $TypeValue, $val ,$MapWidgetId, $PostId);
									$Marks = !empty($Maplocation['marks']) ? $Maplocation['marks'] : '';
									
									if( !empty($Marks) ){
										$MapsMarks=[];
										foreach ($Maplocation['marks'] as $mapvalue) {
											if( !empty($mapvalue[0]) && !empty($mapvalue[1]) ){
												$MapsMarks[] = $mapvalue[0].','.$mapvalue[1];	
											}
										}

										$Connnection_Q = array( 
											'key' => $NameValue,
											'value' => $MapsMarks,
											'compare'=> 'IN'
										);
									}
								}else{
									$Connnection_Q = array(
										'key' => $item,
										'value' => $val,
										'compare' => '=',
									);
								}

								if(!empty($Connnection_Q)){
									$meta_keyArr[] = $Connnection_Q;
								}
							}
						}	
					}

					if(!empty($keyEnable)){
						if($post_type == 'post'){
							if($NameValue == 'category' && !empty($DataValue)){
								$args['category__in'] = $DataValue;
							}else if($NameValue == 'post_tag' && !empty($DataValue) ){
								$args['tag__in'] = $DataValue;
							}else{
								if( !empty($texonomy_category) && !empty($NameValue) && !empty($DataValue) ){
									$attr_tax[] = array(
										array(
											'taxonomy'=>$NameValue,
											'field'=>'term_id',
											'terms'=>$DataValue,
										),
									);
								}
							}
						}else if($post_type == 'product') {
							$attr_tax[] = array(
								'taxonomy' => 'product_visibility',
								'field' => 'name',
								'terms' => ['exclude-from-search', 'exclude-from-catalog'],
								'operator' => 'NOT IN',
							);
	
							if( empty($WooSortEnable) ){
								if( !empty($DataValue) && !empty($NameValue) ){
									$attr_tax[] = array(
										'taxonomy' => $NameValue,
										'field' => 'id',
										'terms' => $DataValue
									);
								}
							}

							if( !empty($DataValue) && !empty($WooSortEnable) ){
								/*Woo Sorting*/
								foreach ($DataValue as $val) {
									if($val == 'featured'){
										$attr_tax[] = array(
											array(
												'taxonomy'=>'product_visibility',
												'field'=>'name',
												'terms'=>'featured',
											),
										);
									}
									if($val == 'on_sale'){
										$meta_keyArr[] = array( 'relation' => 'OR',
											array( // Simple products type
												'key' => '_sale_price',
												'value' => 0,
												'compare' => '>',
												'type' => 'numeric'
											),
											array( // Variable products type
												'key' => '_min_variation_sale_price',
												'value' => 0,
												'compare' => '>',
												'type' => 'numeric'
											)
										);
									}
									if($val == 'top_sales'){
										$meta_keyArr[] = array(
											array(
												'key' => 'total_sales',
												'value' => 0,
												'compare' => '>',
												)
										);
									}
									if($val == 'instock'){
										$meta_keyArr[] = array(
											array(
												'key' => '_stock_status',
												'value' => 'instock',
											)
										);
									}
									if($val == 'outofstock'){
										$meta_keyArr[] = array(
											array(
												'key' 		=> '_stock_status',
												'value' 	=> 'outofstock',												
											)
										);
									}
								}
							}

						}else{
							if( !empty($NameValue) && !empty($DataValue) ){
								$attr_tax[] = array(
									'taxonomy' => $NameValue,
									'field' => 'id',
									'terms' => $DataValue
								);
							}
						}
					}
				}
			}

			/*Search Page*/
			if( !empty($is_searchPage) ){
				$args['s'] = $SearchPageval;
				$args['exact'] = false;
			}
			
			/*Select Category widget*/
			if( $enable_archive_search == 'false' ){
				if( !empty($category_type) || $category_type == 'true' ){
					if( !empty($category) && $post_type == 'post' ){
						
						if( $ArchivepageName == 'cat' ){
							$args['category__in'] = explode(',', $category);
						}else if( $ArchivepageName == 'post_tag' ){

						}else{
							if( !empty($ArchivepageName) ){
								$attr_tax[] = array(
									'taxonomy'=>$NameValue,
									'field'=>'term_id',
									'terms'=> explode(',', $category),
								);
							}else{	
								$NameTexo = "";
								if( $texonomy_category == "cat" ){
									$NameTexo = "category";
								}

								$attr_tax[] = array(
									'taxonomy'=> $NameTexo,
									'field'=>'term_id',
									'terms'=> explode(',', $category),
								);
							}
						}
					}else if( !empty($category) && $post_type == 'product' ){
						$attr_tax[] = array(
							'taxonomy' => 'product_cat',
							'field' => 'slug',
							'terms' => explode(',', $category),
						);
					}else if( !empty($category)  ){
						if( !empty($texonomy_category) ){
							$attr_tax[] = array(
								'taxonomy' => $texonomy_category,
								'field' => 'slug',
								'terms' => explode(',', $category),
							);
						}
					}
				}
			}

			/**Display Product Option ListingWidget*/
			if( $post_type == 'product' && $display_product != 'all' ){
				$wooProductType = theplus_woo_product_type($display_product);

				if( !empty($wooProductType) ){
					if( $display_product == 'featured' ){
						$attr_tax[] = $wooProductType;
					}
	
					if( $display_product != 'featured' ){
						$meta_keyArr[] = $wooProductType;
					}
				}
			}

			if( !empty($attr_tax) ){
				$args['tax_query'] = array('relation'=>'AND', $attr_tax);
			}
			if( !empty($TmpPostID) ){
				$args['post__in'] = $TmpPostID;
			}
			if( !empty($meta_keyArr) ){
				$args['meta_query'] = array('relation'=>'AND', $meta_keyArr);
			}
		}
		
		$result[$key]=[];
		if($widgetName == 'googlemap'){
			$result[$key]['HtmlData'] = '';
			$result[$key]['totalrecord'] = '';
			$result[$key]['Maplocation'] = $Maplocation;
			$result[$key]['widgetName'] = $widgetName;
			$result[$key]['places'] = $Places;
            $result[$key]['options'] = $Options;
		}else{
			$totalcount='';
			ob_start();
				$loop = new WP_Query($args);
				$totalcount = $loop->found_posts;
			
				if ($loop->have_posts()) {
					while ($loop->have_posts()) {
						$loop->the_post();		

						$template_id='';
						if(!empty($dynamic_template)){
							$count=count($dynamic_template);
							$value = (int)$offset % (int)$count;
							$template_id=$dynamic_template[$value];	
						}

						if( $widgetName == "products" && $FilterType == 'search_list' && $post_type == 'product' ){
							include THEPLUS_PATH . "includes/ajax-load-post/product-style.php";
						}else if($widgetName == "dynamiclisting" && $FilterType == 'search_list'){
							include THEPLUS_PATH . "includes/ajax-load-post/dynamic-listing-style.php";
						}
						$ji++;$kij++;

					}
				}
			$Alldata = ob_get_contents();
			ob_end_clean();

			if(!empty($Alldata)){
				$result[$key]['HtmlData'] = $Alldata;
				$result[$key]['totalrecord'] = $totalcount;
				$result[$key]['widgetName'] = $widgetName;
				$result[$key]['Maplocation'] = '';
			}
		}
	}

	wp_reset_postdata();
	wp_send_json($result);
}
add_action('wp_ajax_theplus_filter_post','theplus_filter_post');
add_action('wp_ajax_nopriv_theplus_filter_post','theplus_filter_post');

/*Search Filter Search input (Toolset - PODs - MetaBox) */
function theplus_searchfilter_input($NameValue, $DataValue, $PubliStatus, $TypeValue){
	global $post,$wpdb;	
	
	if( $TypeValue == 'pods_conne' || $TypeValue == 'toolset_conne' ){
		$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID AND {$wpdb->posts}.post_status = %s", $PubliStatus ) );
		if( !empty($DB_Result) ){
			$Data=[];

			foreach ($DB_Result as $value) {
				$ConData='';
				$PostID = !empty($value->ID) ? $value->ID : '';
				if($TypeValue == 'toolset_conne'){
					$ConData = get_post_field( $NameValue , $PostID );
				}else if($TypeValue == 'pods_conne'){
					$Pods = pods( 'post', $PostID, false );
					$ConData = $Pods->display( $NameValue );
				}
		
				if( !empty($ConData) ){
					$PODs_Array = explode( "|", $ConData );
					foreach ($PODs_Array as $val) {
						if( trim(strtolower($val)) == trim(strtolower($DataValue)) ){
							$Data[] = $ConData;
						}
					}
				}
			}

			if(!empty($Data)){
				return array(
					'key'	  => $NameValue,
					'value'	  => $Data,
					'compare' => 'IN'
				);
			}
		}
	}else if( $TypeValue == 'metabox_conne' ){
		$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_key, {$wpdb->postmeta}.meta_value FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID And {$wpdb->postmeta}.meta_key = %s AND {$wpdb->posts}.post_status = %s ", $NameValue, "publish" ) );
		
		if( !empty($DB_Result) ){
			foreach ($DB_Result as $value) {
				$array2 = explode("|", $value->meta_value);

				foreach ($array2 as $two) {
					if( trim(strtolower($two)) == trim(strtolower($DataValue)) ){
						return array(
							'key'	  => $NameValue,
							'value'	  => $value->meta_value,
							'compare' => '='
						);
					}
				}
			}
		}

	}

}

/* Search Filter Tabbing (Toolset - PODs - MetaBox) */
function theplus_searchfilter_Tabbing($NameValue, $DataValue, $PubliStatus, $TypeValue){
	global $post,$wpdb;	
	$Data=[];

	if( $TypeValue == 'toolset_conne' ){
		$DB_Result = $wpdb->get_results( $wpdb->prepare(  "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID AND {$wpdb->posts}.post_status = %s", $PubliStatus ) );
	
		if( !empty($DB_Result) && is_array($DB_Result) ){
			foreach ($DB_Result as $value) {
				$PostID = !empty($value->ID) ? $value->ID : '';
				$ACFdata = get_post_field( $NameValue , $PostID );

				if(!empty($ACFdata)){
					$array2 = explode("|", $ACFdata);

					foreach ($array2 as $val) {
						$fvalue = str_replace( ' ', '-', ltrim(rtrim($val)) );
						if ( in_array($fvalue, $DataValue) ){
							$Data[] = $ACFdata;
						}
					}
				}
			}

			if(!empty($Data)){
				return array(
					'key'	 => $NameValue,
					'value'	 => $Data,
					'compare'=> 'IN',
				);
			}
		}
	}else if( $TypeValue == 'pods_conne' ){
		$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_value FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.post_name = %s AND {$wpdb->posts}.post_status = %s AND {$wpdb->posts}.post_type = %s AND {$wpdb->postmeta}.meta_key = %s ", $NameValue, $PubliStatus, "_pods_field", $NameValue ) );
		
		if( !empty($DB_Result) && is_array($DB_Result) ){
			foreach ($DB_Result as $value) {
				$array2 = explode("|", $value->meta_value);

				foreach ($array2 as $two) {
					if ( in_array( trim($two), $DataValue ) ){
						$Data[] = $value->meta_value;
					}
				}
			}
			if( !empty($Data) ){	
				return array( 
					'key' => $NameValue,
					'value' => $Data, 
					'compare'=> 'IN' 
				);
			}
		}
	}else if( $TypeValue == 'metabox_conne' ){
		$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_key, {$wpdb->postmeta}.meta_value FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID And {$wpdb->postmeta}.meta_key = %s AND {$wpdb->posts}.post_status = %s ", $NameValue, "publish" ) );
		
		if( !empty($DB_Result) ){
			foreach ($DB_Result as $key => $value) {
				$array2 = explode("|", $value->meta_value);

				foreach ($array2 as $two) {
					$fvalue = trim($two);
					if ( in_array($fvalue, $DataValue) ){
						$Data[] = $value->meta_value;
					}
				}
			}
		}
		
		if( !empty($Data) ){	
			return array( 
				'key' => $NameValue,
				'value' => $Data, 
				'compare'=> 'IN' 
			);
		}	
	}
}

/* Search Filter Date (Toolset - PODs - MetaBox) */
function theplus_searchfilter_DateFilter($NameValue, $DataValue, $PubliStatus, $TypeValue){
	global $post,$wpdb;	
	
	if($TypeValue == 'pods_conne'){
		if( !empty($NameValue) && !empty($DataValue) ){
			return array(
				'key' => $NameValue,
				'value' => $DataValue,
				'compare' => 'BETWEEN',
				'type' => 'DATE',
			);
		}else{
			return false;
		}
	}else if($TypeValue == 'toolset_conne'){
		$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->posts}.ID, {$wpdb->postmeta}.meta_value, {$wpdb->postmeta}.post_id
		FROM {$wpdb->posts}, {$wpdb->postmeta}
		WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
		AND {$wpdb->posts}.post_status = %s
		AND {$wpdb->postmeta}.meta_key = %s ", $PubliStatus, $NameValue ) );

		if(!empty($DB_Result) && is_array($DB_Result)){
			$Data=[];
			$DateOne = (!empty($DataValue) && !empty($DataValue[0]) ? $DataValue[0] : date("Y-m-d"));
			$DateTwo = (!empty($DataValue) && !empty($DataValue[1]) ? $DataValue[1] : date("Y-m-d"));

			foreach ($DB_Result as $value) {
				$PostID = (!empty($value) && !empty($value->ID)) ? $value->ID : '';
				$MetaValue = (!empty($value) && !empty($value->meta_value)) ? date_i18n( 'Y-m-d', $value->meta_value ) : '';

				if( strtotime($MetaValue) >= strtotime($DateOne) && strtotime($MetaValue) <= strtotime($DateTwo) ){
					$Data[] = (!empty($value) && !empty($value->meta_value) ) ? $value->meta_value : '';
				}
			}

			if(!empty($Data)){
				return array(
					'key'	  => $NameValue,
					'value'	  => $Data,
					'compare' => 'IN'
				);
			}
		}else{
			return false;
		}
	}else if( $TypeValue == 'metabox_conne' ){
		if( !empty($NameValue) && !empty($DataValue) ){
			return array(
				'key' => $NameValue,
				'value' => $DataValue,
				'compare' => 'BETWEEN',
				'type' => 'DATE',
			);
		}
	}
}

/* Search Filter checkbox (Toolset - PODs -MetaBox) */
function theplus_searchfilster_checkBox($NameValue, $DataValue, $PubliStatus, $TypeValue){
	global $post,$wpdb;
	$Data = [];
	if($TypeValue == 'toolset_conne'){
		foreach($DataValue as $val1){
			$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->posts}.post_status = %s AND {$wpdb->postmeta}.meta_key = %s AND {$wpdb->postmeta}.meta_value Like %s", $PubliStatus, $NameValue, "%{$wpdb->esc_like($val1)}%" ) );
			if( !empty($DB_Result) ){
				foreach( $DB_Result as $val2 ){
					if( !empty($val2) && !empty($val2->post_id) ){
						$Data[] = $val2->post_id;
					}
				}
			}
		}

		if( !empty($Data) ){
			return $Data;
		}
	}else if($TypeValue == 'pods_conne'){
		$DB_Result = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_key, {$wpdb->postmeta}.meta_value FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID And {$wpdb->postmeta}.meta_key = %s AND {$wpdb->posts}.post_status = %s ", $NameValue, $PubliStatus ) );

		if( !empty($DB_Result) ){
			foreach ($DB_Result as $value) {
				$array2 = explode("|", $value->meta_value);

				foreach ($array2 as $two) {
					$fvalue = trim($two);
					if ( in_array( $fvalue, $DataValue ) ){
						$Data[] = $value->meta_value;
					}
				}
			}

			if( !empty($Data) ){
				return array(
					'key'	 => $NameValue,
					'value'	 => $Data,
					'compare' => 'IN',
				);
			}
		}
	}else if($TypeValue == 'metabox_conne'){
		if( !empty($NameValue) && !empty($DataValue) ){
			return array(
				'key'	 => $NameValue,
				'value'	 => $DataValue,
				'compare'=> 'IN',
			);	
		}
	}
}

/* Search Filter Radio (Toolset - PODs - MetaBox)  */
function theplus_searchfilster_radiobtn($NameValue, $DataValue, $PubliStatus, $TypeValue){
	if( $TypeValue == 'toolset_conne' || $TypeValue == 'metabox_conne' ){
		return array(
					'key'	 => $NameValue,
					'value'	 => $DataValue,
					'compare'=> '=',
				);
	}else if($TypeValue == 'pods_conne'){
		$Data=[];
		global $post,$wpdb;
		$GetResult = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_key, {$wpdb->postmeta}.meta_value FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID And {$wpdb->postmeta}.meta_key = %s AND {$wpdb->posts}.post_status = %s ", $NameValue, "publish" ) );

		if( !empty($GetResult) ){
			foreach ($GetResult as $key => $value) {
				$array2 = explode("|", $value->meta_value);

				foreach ($array2 as $two) {
					$fvalue = trim($two);
					if ( trim($two) == $DataValue ){
						$Data[] = $value->meta_value;
					}
				}
			}
			
			if( !empty($Data) ){
				return array(
					'key'	 => $NameValue,
					'value'	 => $Data,
					'compare' => 'IN',
				);
			}
		}
	}
}

/* Search Filter DropDown (Toolset - PODs - MetaBox)  */
function theplus_searchfilster_dropdown($NameValue, $DataValue, $PubliStatus, $TypeValue){
	if( $TypeValue == 'toolset_conne' || $TypeValue == 'metabox_conne' ){
		return array(
			'key'	 => $NameValue,
			'value'	 => $DataValue,
			'compare'=> '=',
		);
	}else if( $TypeValue == 'pods_conne' ){
		$Data=[];
		global $post,$wpdb;
		$GetResult = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_key, {$wpdb->postmeta}.meta_value FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID And {$wpdb->postmeta}.meta_key = %s AND {$wpdb->posts}.post_status = %s ", $NameValue, "publish" ) );

		if( !empty($GetResult) ){
			foreach ($GetResult as $value) {
				$array2 = explode( "|", $value->meta_value );

				foreach ($array2 as $two) {
					if ( trim($two) == $DataValue ){
						$Data[] = $value->meta_value;
					}
				}
			}

			if( !empty($Data) ){
				return array(
					'key'	 => $NameValue,
					'value'	 => $Data,
					'compare' => 'IN',
				);
			}
		}
	}
}

/* Search Filter Button (Toolset - PODs - MetaBox) */
function theplus_searchfilter_button($NameValue, $DataValue, $PubliStatus, $TypeValue){
	global $post,$wpdb;
	$Data=$GetResult=[];

	if( $TypeValue == 'toolset_conne' ){
		$GetResult = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_value, {$wpdb->postmeta}.meta_key FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->posts}.post_status = %s AND {$wpdb->postmeta}.meta_key = %s ", $PubliStatus, $NameValue ) );
	}else if( $TypeValue == 'pods_conne' ){
		$GetResult = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_value {$wpdb->postmeta}.meta_key FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.meta_key = %s And {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID AND {$wpdb->posts}.post_status = %s ", $NameValue, $PubliStatus ) );
	}else if( $TypeValue == 'metabox_conne' ){
		$GetResult = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->postmeta}.post_id, {$wpdb->postmeta}.meta_value, {$wpdb->postmeta}.meta_key FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID And {$wpdb->postmeta}.meta_key = %s AND {$wpdb->posts}.post_status = %s ", $NameValue, $PubliStatus ) );
	}

	if( !empty($GetResult) && is_array($GetResult) ){    
		foreach ($GetResult as $value) {
			$array2 = explode("|", $value->meta_value);

			foreach ($array2 as $value1) {
				if ( trim($value1) == $DataValue[0] ) {
					$Data[] = $value->meta_value;
				}
			}
		}

		if( !empty($Data) ){
			return array(
				'key'	 => $NameValue,
				'value'	 => $Data,
				'compare' => 'IN',
			);
		}
	}
}

/* Search Filter Rating (Toolset - PODs - MetaBox) */
function theplus_searchfilster_rating($NameValue, $DataValue, $PubliStatus, $TypeValue){
	if( $TypeValue == 'toolset_conne' || $TypeValue == 'pods_conne' || $TypeValue == 'metabox_conne'){
		return array(
			'key'	 => $NameValue,
			'value'	 => $DataValue,
			'compare' => 'IN',
		);
	}
}

/* Search Filter Color (Toolset - PODs - MetaBox) */
function theplus_searchfilster_color($NameValue, $DataValue, $PubliStatus, $TypeValue){
	if( $TypeValue == 'pods_conne' || $TypeValue == 'toolset_conne' || $TypeValue == 'metabox_conne' ){
		return array(
			'key'	 => $NameValue,
			'value'	 => $DataValue,
			'compare'=> 'IN',
		);
	}
}

/* Search Filter Range (Toolset - PODs - Metabox) */ 
function theplus_searchfilter_range($NameValue, $DataValue, $PubliStatus, $TypeValue){
	if( $TypeValue == 'pods_conne' || $TypeValue == 'toolset_conne' || $TypeValue == 'metabox_conne' ){
		return array(
			'key'		=> $NameValue,
			'value'		=> $DataValue,
			'compare'   => 'BETWEEN',
			'type'      => 'NUMERIC',
		);
	}
}

/* Search Filter Autocomplete ( - PODs - Metabox) */ 
function theplus_searchfilter_autocomplete($NameValue, $DataValue, $TypeValue, $val ,$MapWidgetId, $PostId){
	$Maplocation = [];
	$GetAddress = get_post_meta( $PostId, 'tp-gmap-address-'.$MapWidgetId, true );
	$GeoValue = (!empty($val) && !empty($val['locationdata'])) ? $val['locationdata'] : '';
	
	if( !empty($GeoValue) && !empty($GetAddress) ){
		$Country = !empty($GeoValue['country']) ? trim(strtolower( $GeoValue['country'] ) ) : '';
		$State = !empty($GeoValue['state']) ? trim(strtolower( $GeoValue['state']) ) : '';
		$city = !empty($GeoValue['city']) ? trim(strtolower( $GeoValue['city']) ) : '';
		$PostalCode = !empty($GeoValue['postalCode']) ? trim(strtolower( $GeoValue['postalCode']) ) : '';
		$geo = !empty($GeoValue['geo']) ? $GeoValue['geo'] : '';

		if( !empty($geo) ){
			$Maplocation['letlong'] = $GeoValue['geo'];
		}

		foreach ($GetAddress as $Gplace1) {
			$address_components = !empty($Gplace1['address_components']) ? $Gplace1['address_components'] : [];
			$latitude = !empty($Gplace1['latitude']) ? $Gplace1['latitude'] : '';
			$longitude = !empty($Gplace1['longitude']) ? $Gplace1['longitude'] : '';
			$Address = !empty($Gplace1['address']) ? $Gplace1['address'] : '';
			$letlong = array( $latitude, $longitude );

			if( !empty($address_components) ){
				foreach ($address_components as $Gplace2) {
					$long_name = !empty($Gplace2['long_name']) ? trim(strtolower($Gplace2['long_name'])) : '';
					if( !empty($PostalCode) ){
						if( $long_name == $PostalCode ){
							$Maplocation['marks'][] = array( $latitude, $longitude );
							$Maplocation['address'][] = $Gplace1['address'];
						}
					}else if( !empty($city) ){
						if( $long_name == $city ){
							$Maplocation['marks'][] = array( $latitude, $longitude );
							$Maplocation['address'][] = $Gplace1['address'];
						}
					}else if( !empty($State) ){
						if( $long_name == $State ){
							$Maplocation['marks'][] = array( $latitude, $longitude );
							$Maplocation['address'][] = $Gplace1['address'];
						}													
					}else if( !empty($Country) ){
						if( $long_name == $Country ){
							$Maplocation['marks'][] = array( $latitude, $longitude );
							$Maplocation['address'][] = $Gplace1['address'];
						}
					}
				}
			}
		}
	}else{

		$Maplocation['letlong'] = array( 37.0902, 95.7129 );

		foreach ($GetAddress as $Gplace1) {
			$latitude = !empty($Gplace1['latitude']) ? $Gplace1['latitude'] : '';
			$longitude = !empty($Gplace1['longitude']) ? $Gplace1['longitude'] : '';
			$Address = !empty($Gplace1['address']) ? $Gplace1['address'] : '';

			$Maplocation['marks'][] = array( $latitude, $longitude );
			$Maplocation['address'][] = $Gplace1['address'];
		}

	}

	return $Maplocation;
}

function theplus_woo_product_type($display_product){
	if( $display_product == 'featured' ){
		return array(
			'taxonomy'=>'product_visibility',
			'field'=>'name',
			'terms'=>'featured',
		);
	}

	if( $display_product == 'on_sale' ){
		return array( 'relation' => 'OR',
			array( // Simple products type
				'key' => '_sale_price',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			),
			array( // Variable products type
				'key' => '_min_variation_sale_price',
				'value' => 0,
				'compare' => '>',
				'type' => 'numeric'
			)
		);
	}

	if( $display_product == 'top_sales' ){
		return array(
			'key' => 'total_sales',
			'value' => 0,
			'compare' => '>',
		);
	}

	if( $display_product == 'instock' ){
		return array(
			'key' => '_stock_status',
			'value' => 'instock',
		);
	}

	if( $display_product == 'outofstock' ){
		return array(
			'key' => '_stock_status',
			'value' => 'outofstock',
		);
	}

	if( $display_product == 'all' ){
		return;
	}
	
}

/*Search bar*/
function tp_search_bar(){	
	if(!isset($_POST['nonce']) || empty($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'tp-searchbar' )){	
		die ('Security checked!');
	}
	
	$searchData=[];	
	parse_str($_POST['searchData'], $searchData);
	
	$DefaultData = !empty($_POST['DefaultData']) ? $_POST['DefaultData'] : '';
	$SpecialCTP = (!empty($DefaultData) && !empty($DefaultData['SpecialCTP'])) ? 1 : 0;
	if(!empty($DefaultData) && !empty($DefaultData['Def_Post']) ){
		$Def_post = $DefaultData['Def_Post'];
	}else if(!empty($DefaultData) && !empty($SpecialCTP) ){
		$Def_post = (!empty($DefaultData) && !empty($DefaultData['SpecialCTPType'])) ? $DefaultData['SpecialCTPType'] : 'post';
	}else{
		$Def_post = 'any';
	}

	$GetTaxonomy = !empty($searchData['taxonomy']) ? $searchData['taxonomy'] : '';

	$Enable_DefaultStxt=0;
	$PostType='';
	if(!empty($searchData) && !empty($searchData['post_type'])){
		$PostType = sanitize_text_field($searchData['post_type']);
	}else{
		$Enable_DefaultStxt=1;
		$PostType = $Def_post;
	}

	$PostType = (!empty($searchData) && !empty($searchData['post_type'])) ? sanitize_text_field($searchData['post_type']) : $Def_post;
	$postper = !empty($_POST['postper']) ? intval($_POST['postper']) : 3;
	$GFilter = !empty($_POST['GFilter']) ? $_POST['GFilter'] : [];
	$GFSType = !empty($GFilter['GFSType']) ? sanitize_text_field($GFilter['GFSType']) : 'otheroption';
	$ACFEnable = !empty($_POST['ACFilter']['ACFEnable']) ? $_POST['ACFilter']['ACFEnable'] : 0;
	$ACF_Key = !empty($_POST['ACFilter']['ACFkey']) ? $_POST['ACFilter']['ACFkey'] : '';

	if($PostType == 'product' && !class_exists('woocommerce')){
		$response['error'] = 1;
		$response['message'] = 'woocommerce checked!';
		wp_send_json_success($response);
		die();
	}

	$ResultData = !empty($_POST['ResultData']) ? $_POST['ResultData'] : [];
	$Pagestyle = !empty($ResultData['Pagestyle']) ? $ResultData['Pagestyle'] : 'none';
	
	$response = array(
		'error' => false,
		'post_count' => 0,
		'message' => '',
		'posts' => null,
	);

	$query_args = array(
		'post_type' => $PostType,
		'suppress_filters' => false,
		'ignore_sticky_posts' => true,
		'orderby' => 'relevance',
		'posts_per_page' => -1,
		'post_status' => 'publish',
	);

	$seaposts=[];
	if(!empty($_POST['text'])){
		global $wpdb;
		$sqlContent = $_POST['text'];
		if( !empty($ACFEnable) || (!empty($GFilter['GFEnable']) )){
			$AllData=$GTitle=$GExcerpt=$Gcontent=$GName=$PCat=$PTag=$ACFData=[];

			$Result='';
			if($GFSType == 'fullMatch'){
				$Result = "{$wpdb->esc_like($sqlContent)}";
			}else if($GFSType == 'wordmatch'){
				$Result = "{$wpdb->esc_like($sqlContent)}%";
			}else{
				$Result = "%{$wpdb->esc_like($sqlContent)}%";
			}

			$Publish = $wpdb->prepare(" AND {$wpdb->posts}.post_status = %s ", 'publish');
			
			$DType='';
			if(!empty($PostType)){
				if(!empty($Enable_DefaultStxt)){
					$DType='';
				}else{
					$DType = $wpdb->prepare(" AND post_type = %s", $PostType);
				}
			}else{
				$DType = " AND post_type IN ('post','page','product')";
			}
			
			if(!empty($GFilter['GFEnable'])){
				if(!empty($GFilter['GFTitle'])){ 
					$GTitle = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_title LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFExcerpt'])){
					$GExcerpt = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_excerpt LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFContent'])){
					$Gcontent = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_content LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFName'])){
					$GName = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_name LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFCategory']) && $PostType != 'page'){
					$CatTaxonomy='';
					$CatPT=$PostType;
					$CatType='category_name';
					if($PostType == 'post'){
						$CatTaxonomy = 'category';
					}else if($PostType == 'product'){
						$CatTaxonomy=$CatType='product_cat';
					}else{
						$CatTaxonomy = 'any';
						$CatPT = 'post';
					}

					$PCat = query_posts( array(
						'taxonomy' 		=> $CatTaxonomy,
						'post_type'		=> $CatPT,
						$CatType	 	=> $sqlContent,
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'orderby' 		=> 'name',
						'order'			=> 'ASC',
						'hide_empty'	=> 0,				
					) );
				}
				if(!empty($GFilter['GFTags']) && $PostType != 'page') { 
					$TagTaxonomy=$TagType='';
					$TagPT=$PostType;
					$ArrayData=[];
					if( is_array($PostType) ){
						foreach ($PostType as $key => $value) {
							if( $value == 'post' ){
								$TagTaxonomy = 'post_tag';
								$TagType = 'tag';
							}else if( $value == 'product' ){
								$TagTaxonomy = 'product_tag';
								$TagType = 'product_tag';
							}else{
								/**static tag Taxonomy*/
								$TagTaxonomy = 'post_tag';
								$TagType = 'tag';
							}
							
							$ArrayData = array(
								'taxonomy' 		=> $TagTaxonomy,
								'post_type'		=> $value,
								$TagType		=> $sqlContent,
								'post_status' 	=> 'publish',
								'posts_per_page' => -1,
								'orderby' 		=> 'name',
								'order'			=> 'ASC',
								'hide_empty' 	=> 0,
							);

							$Gettags = query_posts( $ArrayData );

							$PTag = array_merge( $PTag, $Gettags );
						}
					}else{
						if($PostType == 'post'){
							$TagTaxonomy = 'post_tag';
							$TagType = 'tag';
						}else if($PostType == 'product'){
							$TagTaxonomy = 'product_tag';
							$TagType = 'product_tag';
						}else{
							/**static tag Taxonomy*/
							$TagTaxonomy = 'post_tag';
							$TagType = 'tag';
						}
	
						$PTag = query_posts( array(
							'taxonomy' 		=> $TagTaxonomy,
							'post_type'		=> $TagPT,
							$TagType		=> $sqlContent,
							'post_status' 	=> 'publish',
							'posts_per_page' => -1,
							'orderby' 		=> 'name',
							'order'			=> 'ASC',
							'hide_empty' 	=> 0,
						) );
					}
				}
			}
			
			if( class_exists('acf') && !empty($ACFEnable) && !empty($ACF_Key) ){
				$ACFPrepare = $wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID {$Publish}");
				$AcfPost = $wpdb->get_results($ACFPrepare);
				foreach ($AcfPost as $key => $one) {
					$PostID = !empty($one->ID) ? $one->ID : '';
					$GetData = acf_get_field($ACF_Key)['key'];
					$ACFone = get_field($GetData, $PostID);
					if(!empty($ACFone)){
						$ACFArray = explode("|", $ACFone);
						foreach ($ACFArray as $two) {
							$ACFtxt = ltrim(rtrim($two));
							if( ($GFSType == 'otheroption') && str_contains(strtolower($ACFtxt), strtolower($sqlContent)) ){
								$ACFData[] = $one->ID;
							}else if( ($GFSType == 'fullMatch') && (strtolower($ACFtxt) == strtolower($sqlContent)) ){
								$ACFData[] = $one->ID;
							}
						}
					}
				}
			}

			array_push( $AllData, $GTitle, $GExcerpt, $Gcontent, $GName, $PCat, $PTag, $ACFData );
			$TmpPostID=[];
			if(!empty($AllData)){
				foreach($AllData as $one) {
					if(!empty($one)){
						foreach($one as $two){
							if( !empty($GFilter['GFEnable']) && !empty($two->ID)){
								$TmpPostID[] = $two->ID;
							}else if( !empty($ACFEnable) && !empty($two) ){
								$TmpPostID[] = $two;
							}
						}
					}
				}
			}
			
			if( !empty($TmpPostID) ){
				$query_args['post__in'] = $TmpPostID;
			}else{
				$query_args['s'] = $sqlContent;
				// $query_args['post__in'] = [0];
			}
		}else{
			$query_args['s'] = $sqlContent;
		}
	}

	if($PostType == 'product'){
		$tax_query = ['relation' => 'AND',
			[
				'taxonomy' => 'product_visibility',
				'field' => 'name',
				'terms' => ['exclude-from-search', 'exclude-from-catalog'],
				'operator' => 'NOT IN',
			],
		];
	}
	if(!empty($searchData['taxonomy']) && !empty($searchData['cat']) ){
		$tax_query = [
			[
				'taxonomy' => $searchData['taxonomy'],
				'field' => 'term_id',
				'terms' => $searchData['cat'] 
			]
		];
	}

	if(!empty($tax_query) ){
		$query_args['tax_query'] = [ 'relation' => 'AND', $tax_query ];
	}

	if($Pagestyle !== 'none'){
		$offset = !empty($_POST['offset']) ? $_POST['offset'] : '';
		$loadmore_Post = !empty($_POST['loadNumpost']) ? $_POST['loadNumpost'] : $postper;

		$query_args['offset'] = $offset;
		if($Pagestyle == 'pagination'){
			$query_args['posts_per_page'] = $postper;
		}else if($Pagestyle == 'load_more'){
			$query_args['posts_per_page'] = $loadmore_Post;
		}else if($Pagestyle == 'lazy_load'){
			$query_args['posts_per_page'] = $loadmore_Post;
		}
	}else{
		$query_args['posts_per_page'] = $postper;
	}
	
	$seaposts = new WP_Query($query_args);
	$response['posts']  = array();
	$response['limit_query'] = $postper;
	$response['columns']  = ceil($seaposts->found_posts / $postper);
	$response['post_count']  = $seaposts->found_posts;
	$response['total_count']  = $seaposts->found_posts;

	if($Pagestyle == 'pagination' && $response['limit_query'] < $response['post_count']){
		$response['pagination'] = '';
		$Pcounter = !empty($ResultData['Pcounter']) ? $ResultData['Pcounter'] : 0;
		$PClimit = !empty($ResultData['PClimit']) ? $ResultData['PClimit'] : 5;
		$PNavigation = !empty($ResultData['PNavigation']) ? $ResultData['PNavigation'] : 0;
		$PNxttxt = !empty($ResultData['PNxttxt']) ? $ResultData['PNxttxt'] : '';
		$PPrevtxt = !empty($ResultData['PPrevtxt']) ? $ResultData['PPrevtxt'] : '';
		$PNxticon = !empty($ResultData['PNxticon']) ? $ResultData['PNxticon'] : '';
		$PPrevicon = !empty($ResultData['PPrevicon']) ? $ResultData['PPrevicon'] : '';
		$Pstyle = !empty($ResultData['Pstyle']) ? $ResultData['Pstyle'] : 'center';

		$next=$prev=$BtnNum='';
		if(!empty($PNavigation)){
			$next .= '<button class="tp-pagelink prev" data-prev="1" >';
				$next .= (!empty($PPrevtxt)) ? '<span class="tp-prev-txt">'.esc_html($PPrevtxt).'</span>' :'';
				$next .= (!empty($PPrevicon)) ? '<span class="tp-prev-icon"> <i class="'.esc_attr($PPrevicon).' tp-title-icon"></i> </span>' :'';
			$next .= '</button>';
		}

		if(!empty($Pcounter)){
			if($response['columns'] <= $PClimit){
				for ($i=0; $i<$PClimit; $i++){
					if($i < $response['columns']){
						$active = (($i+1) == 1) ? 'active' : '';
						$BtnNum .= '<button class="tp-pagelink tp-ajax-page '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
					}
				}
			}else{
				for ($i=0; $i<$response['columns']; $i++){
					if($i < $PClimit){
						$active = (($i+1) == 1) ? 'active' : '';
						$BtnNum .= '<button class="tp-pagelink tp-ajax-page '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
					}else{
						$active = (($i+1) == 1) ? 'active' : '';
						$BtnNum .= '<button class="tp-pagelink tp-ajax-page tp-hide '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
					}
				}
			}
		}else{
			for ($i=0; $i<$response['columns']; $i++){
				$active = (($i+1) == 1) ? 'active' : '';
				$BtnNum .= '<button class="tp-pagelink tp-ajax-page tp-hide '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
			}
		}

		if(!empty($PNavigation)){
			$prev .= '<button class="tp-pagelink next" data-next="1">';
				$prev .= !empty($PNxttxt) ? '<span class="tp-next-txt">'.esc_html($PNxttxt).'</span>' : '';
				$prev .= !empty($PNxticon) ? '<span class="tp-next-icon"> <i class="'.esc_attr($PNxticon).' tp-title-icon"></i> </span>' : '';
				$prev .= '</button>';
		}

		if($Pstyle == 'after'){
			$response['pagination'] .= $next . $prev . $BtnNum;
		}else if($Pstyle == 'center'){
			$response['pagination'] .= $next . $BtnNum . $prev;
		}else if($Pstyle == 'before'){
			$response['pagination'] .= $BtnNum . $next . $prev;
		}
	}else if($Pagestyle == 'load_more'){		
		$BtnTxt = !empty($ResultData['loadbtntxt']) ? $ResultData['loadbtntxt'] : 0;
		$response['loadmore'] = '<a class="post-load-more" data-page="1" >'.esc_html($BtnTxt).'</a>';
		$LoadPage = !empty($ResultData['loadpage']) ? $ResultData['loadpage'] : 0;
		if(!empty($LoadPage)){
			$PageHtml = '';
			$Pagetxt = !empty($ResultData['loadPagetxt']) ? $ResultData['loadPagetxt'] : '';
			$loadnumber = !empty($ResultData['loadnumber']) ? $ResultData['loadnumber'] : $postper;
			//$Numbcount = ceil($seaposts->found_posts / $loadnumber);
			$Numbcount = ceil( ($seaposts->found_posts - $postper) / $loadnumber ) + 1;

			$PageHtml .= '<span class="tp-page-link" >'.esc_html($Pagetxt).'</span>';
			$PageHtml .= '<button class="tp-pagelink tp-load-page" data-page="1" ><span class="tp-load-number" > 1 </span> / '.esc_html($Numbcount).' </button>';
			
			$response['loadmore_page'] = $PageHtml;
		}
	}else if($Pagestyle == 'lazy_load'){		
		$response['lazymore'] = '<a class="post-lazy-load" data-page="1"><div class="tp-spin-ring"><div></div><div></div><div></div><div></div></div></a>';
	}

	foreach ($seaposts->posts as $key => $post){
		$product='';
		if($PostType == 'product' || $GetTaxonomy == "product_cat" || $GetTaxonomy == 'product_tag' ){
			$product = wc_get_product($post->ID);
		}

		$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');		
		$response['posts'][$key] = array(
			'title'       => !empty($post) ? $post->post_title : '',
			'content'     => !empty($post) ? $post->post_title : '',
			'link'        => !empty($post) ? get_permalink($post) : '',
			'content'     => !empty($post) ? $post->post_excerpt : '',
			'thumb'		  => $url,
			'PostType'	  => $PostType,
			'Wo_Price'	  => !empty($product) ? $product->get_price_html() : '',
			'Wo_shortDesc'=> !empty($product) ? $product->get_short_description() : '',
		);
	}

	wp_reset_postdata();
	wp_send_json_success($response);

}
add_action('wp_ajax_tp_search_bar','tp_search_bar');
add_action('wp_ajax_nopriv_tp_search_bar','tp_search_bar');

/*search URL*/
function theplus_search_bar_query( $query ) {
    if ($query->is_search() && ! is_admin() && $query->is_main_query()) {
		if(isset($_GET['taxonomy']) && !empty($_GET['taxonomy']) && $_GET['taxonomy'] != 'category' && isset($_GET['cat']) && !empty($_GET['cat'])){			
			$emag = get_term_by('id', $_GET['cat'], $_GET['taxonomy']);
			
			if(!empty($emag->count) && $emag->count >=1){
				unset( $query->query['cat'] );
				unset( $query->query_vars['cat'] );
				$query->query[$_GET['taxonomy']] =$emag->slug;
				$query->query_vars[$_GET['taxonomy']] =$emag->slug;
			}			
		}
    }
}
add_filter( 'pre_get_posts','theplus_search_bar_query' );

function get_current_ID($id){
	$newid = apply_filters( 'wpml_object_id', $id, 'elementor_library', TRUE );

	return $newid ? $newid : $id;
}

function plus_acf_repeater_field_ajax(){
	if(!isset($_POST['security']) || empty($_POST['security']) || ! wp_verify_nonce( $_POST['security'], 'theplus-addons' )){
		die('Invalid Nonce Security checked!');
	} 
	
	$data = [];	
	if(!empty($_POST['post_id']) && isset($_POST['post_id']) && absint($_POST['post_id'])){
	$acf_fields = get_field_objects($_POST['post_id']);
	
		if( $acf_fields ){
			foreach( $acf_fields as $field_name => $field ){
				if($field['type'] == 'repeater'){
					$data[] = [
					  'meta_id' => $field['name'],
					  'text' => $field['label']
					] ;
				}
			}
		}
	}
	wp_send_json_success($data);
}

/**
 * Create template dynamic
 *
 * @since 5.3.0
 */
function theplus_template_create() {

	/**Security checked wp nonce*/
	$nonce = ! empty( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
	if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'live_editor' ) ) {
		die( 'Security checked!' );
	}

	/**Security checked user login*/
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'theplus' ) ) );
	}

	$uniq       = uniqid();
	$rand_num   = wp_rand( 1, 1000 );
	$post_name  = 'tp-create-template-' . sanitize_text_field( wp_unslash( $uniq ) );
	$post_title = '';
	$args       = array(
		'post_type'              => 'elementor_library',
		'post_status'            => 'publish',
		'name'                   => $post_name,
		'posts_per_page'         => 1,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	);
	$post       = get_posts( $args );

	if ( empty( $post ) ) {
		$post_title = 'TP Template ' . $rand_num;

		$params = array(
			'post_content' => '',
			'post_type'    => 'elementor_library',
			'post_title'   => $post_title,
			'post_name'    => $post_name,
			'post_status'  => 'publish',
			'meta_input'   => array(
				'_elementor_edit_mode'     => 'builder',
				'_elementor_template_type' => 'page',
				'_wp_page_template'        => 'elementor_canvas',
			),
		);

		$post_id = wp_insert_post( $params );

	}

	$temp_url = get_admin_url() . '/post.php?post=' . $post_id . '&action=elementor';

	$result = array(
		'url'   => $temp_url,
		'id'    => $post_id,
		'title' => $post_title,
	);

	wp_send_json_success( $result );
}
add_action( 'wp_ajax_theplus_template_create', 'theplus_template_create' );
add_action( 'wp_ajax_nopriv_theplus_template_create', 'theplus_template_create' );

/**
 *
 * Create - Edit New Template
 *
 * @since 5.3.0
 */
function change_new_template_title() {

	/**Security checked wp nonce*/
	$nonce = ! empty( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
	if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'live_editor' ) ) {
		die( 'Security checked!' );
	}

	/**Security checked user login*/
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'theplus' ) ) );
	}

	$id            = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
	$updated_title = ! empty( $_POST['updated_title'] ) ? sanitize_text_field( wp_unslash( $_POST['updated_title'] ) ) : '';

	$res = wp_update_post(
		array(
			'ID'         => $id,
			'post_title' => $updated_title,
		)
	);

	wp_send_json_success( $res );
}
add_action( 'wp_ajax_change_new_template_title', 'change_new_template_title' );
add_action( 'wp_ajax_nopriv_change_new_template_title', 'change_new_template_title' );

/**
 *
 * Edit Template
 *
 * @since 5.3.0
 */
function change_current_template_title() {

	/**Security checked wp nonce*/
	$nonce = ! empty( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
	if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'live_editor' ) ) {
		die( 'Security checked!' );
	}

	/**Security checked user login*/
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'theplus' ) ) );
	}

	$id            = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
	$updated_title = ! empty( $_POST['updated_title'] ) ? sanitize_text_field( wp_unslash( $_POST['updated_title'] ) ) : '';

	$res = wp_update_post(
		array(
			'ID'         => $id,
			'post_title' => $updated_title,
		)
	);

	$dev = array(
		'ID'         => $id,
		'post_title' => $updated_title,
	);

	wp_send_json_success( $dev );
}
add_action( 'wp_ajax_change_current_template_title', 'change_current_template_title' );
add_action( 'wp_ajax_nopriv_change_current_template_title', 'change_current_template_title' );

if( is_admin() &&  current_user_can("manage_options") && class_exists('acf')){	
	add_action('wp_ajax_plus_acf_repeater_field','plus_acf_repeater_field_ajax');
}


function get_acf_repeater_field(){
	
	$data= [];	
	if(class_exists('acf') && isset($_GET['post']) && absint($_GET['post'])){
		$post_id = get_field('tp_preview_post',$_GET['post']);
		$acf_fields = get_field_objects($post_id);
		if( $acf_fields ){
			foreach( $acf_fields as $field_name => $field ){
				if($field['type'] == 'repeater'){
					$data[$field['name']] = $field['label'];
				}
			}
		}
	}
	return $data;
}

/**
 *
 * Ajax Pagination
 *
 * @since 5.3.0
 */
function theplus_ajax_based_pagination() {

	/**Security checked wp nonce */
	$nonce = ! empty( $_POST['nonce'] ) ? wp_unslash( $_POST['nonce'] ) : '';
	if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'theplus-addons' ) ) {
		die( 'Security checked!' );
	}

	/**Security checked wp nonce */
	check_ajax_referer( 'theplus-addons', 'nonce' );

	$postdata = isset( $_POST['option'] ) ? wp_unslash( $_POST['option'] ) : '';
	if ( empty( $postdata ) || ! is_array( $postdata ) ) {
		ob_start();
			ob_get_contents();
		ob_end_clean();
		exit;
	}

	/** $FilterType = ( !empty($postdata['filtertype']) && 'search_list' === $postdata['filtertype'] ) ? $postdata['filtertype'] : ''; */
	$widgetname         = isset( $postdata['load'] ) ? sanitize_text_field( wp_unslash( $postdata['load'] ) ) : '';
	$post_load          = $widgetname;
	$desktop_class      = '';
	$tablet_class       = '';
	$mobile_class       = '';
	$tablet_metro_class = 0;
	$kij                = 0;
	$ji                 = 1;
	$ij                 = '';
	$offset             = 0;

	$paginationType = ! empty( $postdata['paginationType'] ) ? $postdata['paginationType'] : 'standard';

	if ( 'dynamiclisting' === $widgetname || 'products' === $widgetname ) {
		$post_type         = isset( $postdata['post_type'] ) ? sanitize_text_field( wp_unslash( $postdata['post_type'] ) ) : '';
		$layout            = isset( $postdata['layout'] ) ? sanitize_text_field( wp_unslash( $postdata['layout'] ) ) : '';
		$texonomy_category = isset( $postdata['texonomy_category'] ) ? sanitize_text_field( wp_unslash( $postdata['texonomy_category'] ) ) : '';

		$DisplayPost  = ( isset( $postdata['display_post'] ) && intval( $postdata['display_post'] ) ) ? sanitize_text_field( $postdata['display_post'] ) : 4;
		$display_post = $DisplayPost;       // Not used

		$post_load_more = ( isset( $postdata['post_load_more'] ) && intval( $postdata['post_load_more'] ) ) ? wp_unslash( $postdata['post_load_more'] ) : 1;
		$post_title_tag = isset( $postdata['post_title_tag'] ) ? wp_unslash( $postdata['post_title_tag'] ) : '';

		$style                     = isset( $postdata['style'] ) ? sanitize_text_field( wp_unslash( $postdata['style'] ) ) : 'style-1';
		$desktop_column            = ( isset( $postdata['desktop-column'] ) && intval( $postdata['desktop-column'] ) ) ? wp_unslash( $postdata['desktop-column'] ) : 3;
		$tablet_column             = ( isset( $postdata['tablet-column'] ) && intval( $postdata['tablet-column'] ) ) ? wp_unslash( $postdata['tablet-column'] ) : 4;
		$mobile_column             = ( isset( $postdata['mobile-column'] ) && intval( $postdata['mobile-column'] ) ) ? wp_unslash( $postdata['mobile-column'] ) : 6;
		$metro_column              = isset( $postdata['metro_column'] ) ? wp_unslash( $postdata['metro_column'] ) : '';
		$metro_style               = isset( $postdata['metro_style'] ) ? wp_unslash( $postdata['metro_style'] ) : '';
		$responsive_tablet_metro   = isset( $postdata['responsive_tablet_metro'] ) ? wp_unslash( $postdata['responsive_tablet_metro'] ) : '';
		$tablet_metro_column       = isset( $postdata['tablet_metro_column'] ) ? wp_unslash( $postdata['tablet_metro_column'] ) : '';
		$tablet_metro_style        = isset( $postdata['tablet_metro_style'] ) ? wp_unslash( $postdata['tablet_metro_style'] ) : '';
		$category_type             = isset( $postdata['category_type'] ) ? $postdata['category_type'] : 'false';
		$category                  = isset( $postdata['category'] ) ? wp_unslash( $postdata['category'] ) : '';
		$order_by                  = isset( $postdata['order_by'] ) ? sanitize_text_field( wp_unslash( $postdata['order_by'] ) ) : '';
		$post_order                = isset( $postdata['post_order'] ) ? sanitize_text_field( wp_unslash( $postdata['post_order'] ) ) : '';
		$filter_category           = isset( $postdata['filter_category'] ) ? sanitize_text_field( wp_unslash( $postdata['filter_category'] ) ) : '';
		$animated_columns          = isset( $postdata['animated_columns'] ) ? sanitize_text_field( wp_unslash( $postdata['animated_columns'] ) ) : '';
		$featured_image_type       = isset( $postdata['featured_image_type'] ) ? wp_unslash( $postdata['featured_image_type'] ) : '';
		$display_thumbnail         = isset( $postdata['display_thumbnail'] ) ? wp_unslash( $postdata['display_thumbnail'] ) : '';
		$thumbnail                 = isset( $postdata['thumbnail'] ) ? wp_unslash( $postdata['thumbnail'] ) : '';
		$thumbnail_car             = isset( $postdata['thumbnail_car'] ) ? wp_unslash( $postdata['thumbnail_car'] ) : '';
		$display_theplus_quickview = isset( $postdata['display_theplus_quickview'] ) ? wp_unslash( $postdata['display_theplus_quickview'] ) : '';
		$includePosts              = ( isset( $postdata['include_posts'] ) && intval( $postdata['include_posts'] ) ) ? wp_unslash( $postdata['include_posts'] ) : '';
		$excludePosts              = ( isset( $postdata['exclude_posts'] ) && intval( $postdata['exclude_posts'] ) ) ? wp_unslash( $postdata['exclude_posts'] ) : '';

		$dynamic_template = isset( $postdata['skin_template'] ) ? $postdata['skin_template'] : '';
		$paged            = ( isset( $postdata['page'] ) && intval( $postdata['page'] ) ) ? wp_unslash( $postdata['page'] ) : ''; // Not used

		$is_archivePage  = isset( $postdata['is_archive'] ) ? $postdata['is_archive'] : 0;
		$Archivepage     = isset( $postdata['archive_page'] ) ? $postdata['archive_page'] : '';
		$ArchivepageType = ( ! empty( $Archivepage ) && ! empty( $Archivepage['archive_Type'] ) ) ? sanitize_text_field( $Archivepage['archive_Type'] ) : '';
		$ArchivepageID   = ( ! empty( $Archivepage ) && ! empty( $Archivepage['archive_Id'] ) ) ? $Archivepage['archive_Id'] : '';
		$ArchivepageName = ( ! empty( $Archivepage ) && ! empty( $Archivepage['archive_Name'] ) ) ? $Archivepage['archive_Name'] : '';

		$is_searchPage = isset( $postdata['is_search'] ) ? $postdata['is_search'] : 0;
		$SearchPage    = isset( $postdata['is_search_page'] ) ? $postdata['is_search_page'] : '';
		$SearchPageval = ( ! empty( $SearchPage ) && ! empty( $SearchPage['is_search_value'] ) ) ? sanitize_text_field( $SearchPage['is_search_value'] ) : '';
		$CustonQuery   = ! empty( $postdata['custon_query'] ) ? $postdata['custon_query'] : '';
		$author_prefix = isset( $load_attr["author_prefix"] ) ? wp_unslash($load_attr["author_prefix"]) : '';

		$enable_archive_search = ! empty( $postdata['enable_archive_search'] ) ? 'true' : 'false';
		$listing_type          = isset( $postdata['listing_type'] ) ? $postdata['listing_type'] : '';


		if ( 'carousel' !== $layout && 'metro' !== $layout ) {
			$desktop_class = 'tp-col-lg-' . esc_attr( $desktop_column );
			$tablet_class  = 'tp-col-md-' . esc_attr( $tablet_column );
			$mobile_class  = 'tp-col-sm-' . esc_attr( $mobile_column );
			$mobile_class .= ' tp-col-' . esc_attr( $mobile_column );
		}
	}

	if ( 'dynamiclisting' === $widgetname ) {
		$title_desc_word_break        = isset( $postdata['title_desc_word_break'] ) ? wp_unslash( $postdata['title_desc_word_break'] ) : '';
		$style_layout                 = isset( $postdata['style_layout'] ) ? sanitize_text_field( wp_unslash( $postdata['style_layout'] ) ) : '';
		$post_tags                    = isset( $postdata['post_tags'] ) ? wp_unslash( $postdata['post_tags'] ) : '';
		$post_authors                 = isset( $postdata['post_authors'] ) ? wp_unslash( $postdata['post_authors'] ) : '';
		$display_post_meta            = isset( $postdata['display_post_meta'] ) ? sanitize_text_field( wp_unslash( $postdata['display_post_meta'] ) ) : '';
		$post_meta_tag_style          = isset( $postdata['post_meta_tag_style'] ) ? wp_unslash( $postdata['post_meta_tag_style'] ) : '';
		$display_post_meta_date       = isset( $postdata['display_post_meta_date'] ) ? wp_unslash( $postdata['display_post_meta_date'] ) : '';
		$display_post_meta_author     = isset( $postdata['display_post_meta_author'] ) ? wp_unslash( $postdata['display_post_meta_author'] ) : '';
		$display_post_meta_author_pic = isset( $postdata['display_post_meta_author_pic'] ) ? wp_unslash( $postdata['display_post_meta_author_pic'] ) : '';
		$display_excerpt              = isset( $postdata['display_excerpt'] ) ? sanitize_text_field( wp_unslash( $postdata['display_excerpt'] ) ) : '';
		$post_excerpt_count           = isset( $postdata['post_excerpt_count'] ) ? wp_unslash( $postdata['post_excerpt_count'] ) : '';
		$display_post_category        = isset( $postdata['display_post_category'] ) ? wp_unslash( $postdata['display_post_category'] ) : '';
		$dpc_all                      = isset( $postdata['dpc_all'] ) ? wp_unslash( $postdata['dpc_all'] ) : '';
		$post_category_style          = isset( $postdata['post_category_style'] ) ? sanitize_text_field( wp_unslash( $postdata['post_category_style'] ) ) : '';
		$display_title_limit          = isset( $postdata['display_title_limit'] ) ? wp_unslash( $postdata['display_title_limit'] ) : '';
		$display_title_by             = isset( $postdata['display_title_by'] ) ? wp_unslash( $postdata['display_title_by'] ) : '';
		$display_title_input          = isset( $postdata['display_title_input'] ) ? wp_unslash( $postdata['display_title_input'] ) : '';
		$display_title_3_dots         = isset( $postdata['display_title_3_dots'] ) ? wp_unslash( $postdata['display_title_3_dots'] ) : '';
		$feature_image                = isset( $postdata['feature_image'] ) ? wp_unslash( $postdata['feature_image'] ) : '';
		$full_image_size              = ! empty( $postdata['full_image_size'] ) ? $postdata['full_image_size'] : 'yes';

		$texo_category        = ! empty( $postdata['texo_category'] ) ? $postdata['texo_category'] : '';
		$texo_post_tag        = ! empty( $postdata['texo_post_tag'] ) ? $postdata['texo_post_tag'] : '';
		$texo_post_taxonomies = ! empty( $postdata['texo_post_taxonomies'] ) ? $postdata['texo_post_taxonomies'] : '';
		$texo_include_slug    = ! empty( $postdata['texo_include_slug'] ) ? $postdata['texo_include_slug'] : '';
		$author_prefix        = isset($postdata["author_prefix"]) ? wp_unslash($postdata["author_prefix"]) : 'By';

	} elseif ( 'products' === $widgetname ) {
		$b_dis_badge_switch     = isset( $postdata['badge'] ) ? sanitize_text_field( wp_unslash( $postdata['badge'] ) ) : '';
		$out_of_stock           = isset( $postdata['out_of_stock'] ) ? sanitize_text_field( wp_unslash( $postdata['out_of_stock'] ) ) : '';
		$variation_price_on     = isset( $postdata['variationprice'] ) ? sanitize_text_field( wp_unslash( $postdata['variationprice'] ) ) : '';
		$hover_image_on_off     = isset( $postdata['hoverimagepro'] ) ? sanitize_text_field( wp_unslash( $postdata['hoverimagepro'] ) ) : '';
		$display_product        = isset( $postdata['display_product'] ) ? wp_unslash( $postdata['display_product'] ) : '';
		$display_rating         = isset( $postdata['display_rating'] ) ? wp_unslash( $postdata['display_rating'] ) : '';
		$display_catagory       = isset( $postdata['display_catagory'] ) ? wp_unslash( $postdata['display_catagory'] ) : '';
		$display_yith_list      = isset( $postdata['display_yith_list'] ) ? wp_unslash( $postdata['display_yith_list'] ) : '';
		$display_yith_compare   = isset( $postdata['display_yith_compare'] ) ? wp_unslash( $postdata['display_yith_compare'] ) : '';
		$display_yith_wishlist  = isset( $postdata['display_yith_wishlist'] ) ? wp_unslash( $postdata['display_yith_wishlist'] ) : '';
		$display_yith_quickview = isset( $postdata['display_yith_quickview'] ) ? wp_unslash( $postdata['display_yith_quickview'] ) : '';
		$display_cart_button    = isset( $postdata['cart_button'] ) ? wp_unslash( $postdata['cart_button'] ) : '';
		$dcb_single_product     = isset( $postdata['dcb_single_product'] ) ? wp_unslash( $postdata['dcb_single_product'] ) : '';
		$dcb_variation_product  = isset( $postdata['dcb_variation_product'] ) ? wp_unslash( $postdata['dcb_variation_product'] ) : '';
	}

	$Paginate_sf = ! empty( $postdata['Paginate_sf'] ) ? $postdata['Paginate_sf'] : 0;
	$new_offset  = ! empty( $postdata['new_offset'] ) ? $postdata['new_offset'] : 0;
	$offset      = (int) $new_offset;

	if ( ! empty( $CustonQuery ) ) {
		$args = array();
		if ( has_filter( $CustonQuery ) ) {
			$args = apply_filters( $CustonQuery, $args );
		}
	} elseif ( 'dynamiclisting' === $widgetname || 'products' === $widgetname ) {
			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => $DisplayPost,
				'offset'         => $offset,
				'orderby'        => $order_by,
				'order'          => $post_order,
			);
	}

	/**Display Product Option ListingWidget*/
	if ( 'products' === $widgetname ) {

		if ( ! empty( $texonomy_category ) && ! empty( $category ) && empty( $is_archivePage ) ) {
			$attr_tax[] = array(
				'taxonomy' => $texonomy_category,
				'field'    => 'slug',
				'terms'    => explode( ',', $category ),
			);
		}

		if ( 'all' !== $display_product ) {
			$wooProductType = theplus_woo_product_type( $display_product );

			if ( ! empty( $wooProductType ) ) {
				if ( 'featured' === $display_product ) {
					$attr_tax[] = $wooProductType;
				}

				if ( 'featured' !== $display_product ) {
					$meta_keyArr[] = $wooProductType;
				}
			}
		}

		if ( 'all' === $display_product ) {
			$attr_tax[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'exclude-from-search', 'exclude-from-catalog' ),
				'operator' => 'NOT IN',
			);
		}
	}

	if ( 'dynamiclisting' === $widgetname && empty( $is_archivePage ) ) {
		if ( 'post' === $post_type ) {
			if ( ! empty( $texo_category ) ) {
				$attr_tax[] = array(
					'taxonomy' => $texo_category,
					'field'    => 'id',
					'terms'    => explode( ',', $category ),
				);
			}

			if ( ! empty( $texo_post_tag ) ) {
				$attr_tax[] = array(
					'taxonomy' => $texo_post_tag,
					'field'    => 'id',
					'terms'    => explode( ',', $post_tags ),
				);
			}
		} elseif ( ! empty( $texo_include_slug ) && ! empty( $texo_post_taxonomies ) ) {
				$attr_tax[] = array(
					'taxonomy' => $texo_post_taxonomies,
					'field'    => 'slug',
					'terms'    => explode( ',', $texo_include_slug ),
				);
		}
	}

	/**Archive Page*/
	if ( 'products' === $widgetname && 'archive_listing' === $listing_type && ! empty( $is_archivePage ) && $ArchivepageType && $ArchivepageID ) {
		$attr_tax[] = array(
			'taxonomy' => $ArchivepageType,
			'field'    => 'id',
			'terms'    => $ArchivepageID,
		);
	} elseif ( 'dynamiclisting' === $widgetname && 'archive_listing' === $listing_type && ! empty( $is_archivePage ) ) {
		if ( 'post' === $post_type ) {
			$attr_tax[] = array(
				'taxonomy' => $ArchivepageName,
				'field'    => 'id',
				'terms'    => $ArchivepageID,
			);
		} else {
			$attr_tax[] = array(
				'taxonomy' => $texo_post_taxonomies,
				'field'    => 'slug',
				'terms'    => $category,
			);
		}
	}

	/**Search Page*/
	if ( ! empty( $is_searchPage ) ) {
		$args['s']     = $SearchPageval;
		$args['exact'] = false;
	}

	if ( ! empty( $excludePosts ) ) {
		$args['post__not_in'] = explode( ',', $excludePosts );
	}

	if ( ! empty( $includePosts ) ) {
		$args['post__in'] = explode( ',', $includePosts );
	}

	if ( ! empty( $attr_tax ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			$attr_tax,
		);
	}

	if ( ! empty( $meta_keyArr ) ) {
		$args['meta_query'] = array(
			'relation' => 'AND',
			$meta_keyArr,
		);
	}

	$totalcount = '';
	ob_start();
		$loop       = new WP_Query( $args );
		$totalcount = $loop->found_posts;

	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();

			$template_id = '';
			if ( ! empty( $dynamic_template ) ) {
				$count       = count( $dynamic_template );
				$value       = (int) $offset % (int) $count;
				$template_id = $dynamic_template[ $value ];
			}

			if ( 'products' === $widgetname && 'product' === $post_type ) {
				include THEPLUS_PATH . 'includes/ajax-load-post/product-style.php';
			} elseif ( 'dynamiclisting' === $widgetname ) {
				include THEPLUS_PATH . 'includes/ajax-load-post/dynamic-listing-style.php';
			}

			++$ji;
			++$kij;
		}
	}
	$Alldata = ob_get_contents();
	ob_end_clean();

	if ( ! empty( $Alldata ) ) {
		$result['HtmlData']    = $Alldata;
		$result['totalrecord'] = $totalcount;
		$result['widgetName']  = $widgetname;
	}

	wp_send_json( $result );
}
add_action( 'wp_ajax_theplus_ajax_based_pagination', 'theplus_ajax_based_pagination' );
add_action( 'wp_ajax_nopriv_theplus_ajax_based_pagination', 'theplus_ajax_based_pagination' );

/*Wp login ajax*/
function theplus_ajax_login() {
	
	if( (!isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'ajax-login-nonce' ) )  ){		
		echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'theplus' )] );
		exit;
	}
	
	$access_info = [];		
	$access_info['user_login']    = !empty($_POST['username']) ? sanitize_user($_POST['username']) : "";
	$access_info['user_password'] = !empty($_POST['password']) ? $_POST['password'] : "";
	$access_info['rememberme']    = true;
	
	$user_signon = wp_signon( $access_info );
	
	if ( !is_wp_error($user_signon) ){
		
		$userID = $user_signon->ID;
		wp_set_current_user( $userID, $access_info['user_login'] );
		wp_set_auth_cookie( $userID, true, true );
		
		echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'theplus')] );
		
	} else {
		if ( isset( $user_signon->errors['invalid_email'][0] ) ) {
			
			echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Ops! Invalid Email..!', 'theplus')] );
		} elseif ( isset( $user_signon->errors['invalid_username'][0] ) ) {

			echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Ops! Invalid Username..!', 'theplus')] );
		} elseif ( isset( $user_signon->errors['incorrect_password'][0] ) ) {
			
			echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Ops! Incorrent Password..!', 'theplus')] );
		}
	}
	die();
}
add_action( 'wp_ajax_nopriv_theplus_ajax_login', 'theplus_ajax_login' );
/*Wp login ajax*/

/* login social application facebook/google */
function tp_login_social_app( $name, $email, $type = ''){
	$response	= [];
	$user_data	= get_user_by( 'email', $email ); 

	if ( ! empty( $user_data ) && $user_data !== false ) {
		$user_ID = $user_data->ID;
		wp_set_auth_cookie( $user_ID );
		wp_set_current_user( $user_ID, $name );
		do_action( 'wp_login', $user_data->user_login, $user_data );
		echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'theplus')] );
	} else {
		
		$password = wp_generate_password( 12, true, false );
		
		$args = [
			'user_login' => $name,
			'user_pass'  => $password,
			'user_email' => $email,
			'first_name' => $name,
		];
		
		if ( username_exists( $name ) ) {
			$suffix_id = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
			$name  .= $suffix_id;

			$args['user_login'] = strtolower( preg_replace( '/\s+/', '', $name ) );
		}

		$result = wp_insert_user( $args );

		$user_data = get_user_by( 'email', $email );

		if ( $user_data ) {
			$user_ID    = $user_data->ID;
			$user_email = $user_data->user_email;

			$user_meta = array(
				'login_source' => $type,
			);

			update_user_meta( $user_ID, 'theplus_login_form', $user_meta );
						
			if ( wp_check_password( $password, $user_data->user_pass, $user_data->ID ) ) {
				wp_set_auth_cookie( $user_ID );
				wp_set_current_user( $user_ID, $name );
				do_action( 'wp_login', $user_data->user_login, $user_data );
				echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'theplus')] );
			}
		}
	}
	
	die();
}
/* login social application facebook/google */

/*facebook verify data*/
function tp_facebook_verify_data_user( $fb_token, $fb_id, $fb_secret ) {
	$fb_api = 'https://graph.facebook.com/oauth/access_token';
	$fb_api = add_query_arg( [
		'client_id'     => $fb_id,
		'client_secret' => $fb_secret,
		'grant_type'    => 'client_credentials',
	], $fb_api );

	$fb_res = wp_remote_get( $fb_api );

	if ( is_wp_error( $fb_res ) ) {
		wp_send_json_error();
	}

	$fb_response = json_decode( wp_remote_retrieve_body( $fb_res ), true );

	$app_token = $fb_response['access_token'];

	$debug_token = 'https://graph.facebook.com/debug_token';
	$debug_token = add_query_arg( [
		'input_token'  => $fb_token,
		'access_token' => $app_token,
	], $debug_token );

	$response = wp_remote_get( $debug_token );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return json_decode( wp_remote_retrieve_body( $response ), true );
}

function tp_facebook_get_user_email( $user_id, $access_token ){
	$fb_url = 'https://graph.facebook.com/' . $user_id;
	$fb_url = add_query_arg( [
		'fields'       => 'email',
		'access_token' => $access_token,
	], $fb_url );

	$response = wp_remote_get( $fb_url );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return json_decode( wp_remote_retrieve_body( $response ), true );
}
/*facebook verify data*/

/*Wp facebook social login ajax*/
function theplus_ajax_facebook_login() {
	
	if(!get_option('users_can_register')){
		echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Registration option not enbled in your general settings.', 'theplus' )] );
		die();
	}
	
	if( (!isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'ajax-login-nonce' ) )  ){		
		echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'theplus' )] );
		die();
	}
	
	$access_token = (!empty( $_POST['accessToken'] )) ? sanitize_text_field( $_POST['accessToken'] ) : '';
	$user_id = (!empty( $_POST['id'] )) ? sanitize_text_field( $_POST['id'] ) : 0;
	$email	=	(isset($_POST['email'])) ? sanitize_email($_POST['email']) : '';
	$name	=	(isset($_POST['name'])) ? sanitize_user( $_POST['name'] ) : '';
	
	$fb_data= get_option( 'theplus_api_connection_data' );
	$fb_app_id = (!empty($fb_data['theplus_facebook_app_id'])) ? $fb_data['theplus_facebook_app_id'] : '';
	$fb_secret_id = (!empty($fb_data['theplus_facebook_app_secret'])) ? $fb_data['theplus_facebook_app_secret'] : '';
				
	$verify_data = tp_facebook_verify_data_user( $access_token, $fb_app_id, $fb_secret_id );
	
	if ( empty( $user_id ) || ( $user_id !== $verify_data['data']['user_id'] ) || empty( $verify_data ) || empty( $fb_app_id ) || empty( $fb_secret_id ) || ( $fb_app_id !== $verify_data['data']['app_id'] ) || ( ! $verify_data['data']['is_valid'] ) ) {
		echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Invalid Authorization', 'theplus')] );
		die();
	}
	
	$email_res = tp_facebook_get_user_email( $verify_data['data']['user_id'], $access_token );
	
	if ( !empty( $email ) && ( empty( $email_res['email'] ) || $email_res['email'] !== $email ) ) {
		echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Facebook email validation failed', 'theplus')] );
		die();
	}

	$verify_email = !empty( $email ) && !empty( $email_res['email'] ) ? sanitize_email( $email_res['email'] ) : $verify_data['user_id'] . '@facebook.com';
	
	tp_login_social_app( $name, $verify_email, 'facebook' );
	
	die();
}

add_action( 'wp_ajax_nopriv_theplus_ajax_facebook_login', 'theplus_ajax_facebook_login' );
/*Wp facebook social login ajax*/

/*Forgot Password*/
function theplus_ajax_forgot_password_ajax() {
	global $wpdb, $wp_hasher;
	$tpforgotdata = isset($_POST['tpforgotdata']) ? $_POST['tpforgotdata'] : '';
	
	$forgotdata = tp_check_decrypt_key($tpforgotdata);
	$forgotdata = json_decode($forgotdata,true);
	
	$nonce = isset($forgotdata['noncesecure']) ? $forgotdata['noncesecure'] : '';
	
	if ( ! wp_verify_nonce( $nonce, 'tp_user_lost_password_action' ) ){
		die ( 'Security checked!');
	}        
		
	$user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';
	
	$errors = new WP_Error();
 
    if ( empty( $user_login ) || ! is_string( $user_login ) ) {        
		echo wp_json_encode( [ 'lost_pass'=>'empty_username', 'message'=> sprintf(__( '<strong>ERROR</strong>: Enter a username or email address.','theplus' )) ] );
		exit;
    } elseif ( strpos( $user_login, '@' ) ) {
        $user_data = get_user_by( 'email', trim( wp_unslash( $user_login ) ) );
        if ( empty( $user_data ) ) {          
			echo wp_json_encode( [ 'lost_pass'=>'invalid_email', 'message'=> sprintf(__( '<strong>ERROR</strong>: There is no account with that username or email address.','theplus' )) ] );
			exit;
        }
    } else {
        $login     = trim( $user_login );
        $user_data = get_user_by( 'login', $login );
		if ( ! $user_data ) {			
			echo wp_json_encode( [ 'lost_pass'=>'invalidcombo', 'message'=> sprintf(__( '<strong>ERROR</strong>: There is no account with that username or email address.','theplus' )) ] );
			exit;
		}
    }
 
    do_action( 'lostpassword_post', $errors );

    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    $key        = get_password_reset_key( $user_data );

    if ( is_wp_error( $key ) ) {
		return $key;
    }

    if ( is_multisite() ) {
		$site_name = get_network()->site_name;
    } else {
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
	
	$reset_url = isset($forgotdata['reset_url']) ? $forgotdata['reset_url'] : '';
	$forgot_url = isset($forgotdata['forgot_url']) ? $forgotdata['forgot_url'] : '';
	
	//$forgotdatatceol = json_decode($forgotdata['tceol']);
	
	/*forgot password mail*/
	$message='';
	if(!empty($forgotdata['tceol']) && (!empty($forgotdata['tceol']['tp_cst_email_lost_opt']) && $forgotdata['tceol']['tp_cst_email_lost_opt']=='yes')){
					
		$elsub =  html_entity_decode($forgotdata['tceol']['tp_cst_email_lost_subject']);
		$elmsg =  html_entity_decode($forgotdata['tceol']['tp_cst_email_lost_message']);
		
		if(!empty($forgotdata["f_p_opt"]) && $forgotdata["f_p_opt"]=='default'){		
			$tplr_link_get = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );		
		}else if(!empty($forgotdata["f_p_opt"]) && $forgotdata["f_p_opt"]=='f_p_frontend'){
			$data_fp_frontdata = [];
			$data_fp_frontdata['key'] = $key;
			$data_fp_frontdata['redirecturl'] = $reset_url;
			$data_fp_frontdata['forgoturl'] = $forgot_url;
			$data_fp_frontdata['login'] = rawurlencode( $user_login );
			
			$frontdata_key= tp_plus_simple_decrypt( json_encode($data_fp_frontdata), 'ey' );
			
			$tplr_link_get = network_site_url( "wp-login.php?action=theplusrp&datakey=$frontdata_key", 'login' );
		}
		
		$elfind = array( '/\[tplr_sitename\]/', '/\[tplr_username\]/', '/\[tplr_link\]/' );
		$lrreplacement = array( $site_name,$user_login,$tplr_link_get);		
		$clrmessage = preg_replace( $elfind,$lrreplacement,$elmsg );
		
		$lrheaders = array( 'Content-Type: text/html; charset=UTF-8' );
		 
		wp_mail( $user_email, $elsub, $clrmessage, $lrheaders );
		
	}else{ 
		$message = esc_html__( 'Someone has requested a password reset for the following account:','theplus' ) . "\r\n\r\n";

		$message .= sprintf( esc_html__( 'Site Name: %s','theplus' ), $site_name ) . "\r\n\r\n";

		$message .= sprintf( esc_html__( 'Username: %s','theplus' ), $user_login ) . "\r\n\r\n";
		$message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.','theplus' ) . "\r\n\r\n";
		$message .= esc_html__( 'To reset your password, visit the following address:','theplus' ) . "\r\n\r\n";
		
		if(!empty($forgotdata["f_p_opt"]) && $forgotdata["f_p_opt"]=='default'){		
			$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";		
		}else if(!empty($forgotdata["f_p_opt"]) && $forgotdata["f_p_opt"]=='f_p_frontend'){
			$data_fp_frontdata = [];
			$data_fp_frontdata['key'] = $key;
			$data_fp_frontdata['redirecturl'] = $reset_url;
			$data_fp_frontdata['forgoturl'] = $forgot_url;
			$data_fp_frontdata['login'] = rawurlencode( $user_login );
			
			$frontdata_key= tp_plus_simple_decrypt( json_encode($data_fp_frontdata), 'ey' );
			$message .= '<' . network_site_url( "wp-login.php?action=theplusrp&datakey=$frontdata_key", 'login' ) . ">\r\n";
		}
	}
	

	$title = sprintf( esc_html__( '[%s] Password Reset','theplus' ), $site_name );

	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	
	$fp_correct_email_text = !empty($forgotdata['fp_correct_email']) ? $forgotdata['fp_correct_email'] : 'Check your e-mail for the reset password link.';
	
	if(!empty($forgotdata['tceol']) && (!empty($forgotdata['tceol']['tp_cst_email_lost_opt']) && $forgotdata['tceol']['tp_cst_email_lost_opt']=='yes')){		
		echo wp_json_encode( [ 'lost_pass'=>'confirm', 'message'=> $fp_correct_email_text ] );
	}else{
		if ( wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
		echo wp_json_encode( [ 'lost_pass'=>'confirm', 'message'=> $fp_correct_email_text ] );
	else
		echo wp_json_encode( [ 'lost_pass'=>'could_not_sent', 'message'=> esc_html__('The e-mail could not be sent.','theplus') . "<br />\n" . esc_html__('Possible reason: your host may have disabled the mail() function.','theplus') ] );
	}	

	exit;
}
add_action( 'wp_ajax_nopriv_theplus_ajax_forgot_password', 'theplus_ajax_forgot_password_ajax' );
add_action( 'wp_ajax_theplus_ajax_forgot_password', 'theplus_ajax_forgot_password_ajax' );
/*Forgot Password*/

/*ENCRYPT DECRIPT*/
function tp_check_decrypt_key($key){   	 
	$decrypted = tp_plus_simple_decrypt( $key, 'dy' );
	return $decrypted;
}
function tp_plus_simple_decrypt( $string, $action = 'dy' ) {
	// you may change these values to your own
	$tppk=get_option( 'theplus_purchase_code' );
	$generated = !empty(get_option( 'tp_key_random_generate' )) ? get_option( 'tp_key_random_generate' ) : 'PO$_key';
	
	$secret_key = ( isset($tppk['tp_api_key']) && !empty($tppk['tp_api_key']) ) ? $tppk['tp_api_key'] : $generated;
	$secret_iv = 'PO$_iv';

	$output = false;
	$encrypt_method = "AES-128-CBC";
	$key = hash( 'sha256', $secret_key );
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

	if( $action == 'ey' ) {
		$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	}
	else if( $action == 'dy' ){
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}

	return $output;
}

/*reset password start*/
add_action( 'wp_ajax_nopriv_theplus_ajax_reset_password', 'theplus_ajax_reset_password_ajax' );
add_action( 'wp_ajax_theplus_ajax_reset_password', 'theplus_ajax_reset_password_ajax' );
function theplus_ajax_reset_password_ajax() {
	$tpresetdata = isset($_POST['tpresetdata']) ? $_POST['tpresetdata'] : '';
	
	$resetdata = tp_check_decrypt_key($tpresetdata);
	$resetdata = json_decode($resetdata,true);
	$user_login = isset($resetdata['login']) ? $resetdata['login'] : '';	
	$user_login = urldecode($user_login);
    $user_key = isset($resetdata['key']) ? $resetdata['key'] : '';
	$nonce = isset($resetdata['noncesecure']) ? $resetdata['noncesecure'] : '';
	
	if ( ! wp_verify_nonce( $nonce, 'tp_reset_action' ) ){
		die ( 'Security checked!');
	}
	
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $user = check_password_reset_key( $user_key, $user_login );
 
        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' ) {
			   echo wp_json_encode( [ 'reset_pass'=>'expire', 'message'=> esc_html__('The entered key has expired. Please start reset process again.','theplus') ] );
            } else {
				echo wp_json_encode( [ 'reset_pass'=>'invalid', 'message'=> esc_html__('The entered key is invalid. Please start reset process again.','theplus') ] );
            }
            exit;
        }
 
        if ( isset( $_POST['user_pass'] ) ) {
            if ( $_POST['user_pass'] != $_POST['user_pass_conf'] ) {                
				echo wp_json_encode( [ 'reset_pass'=>'mismatch', 'message'=> esc_html__('Password does not match. Please try again.','theplus') ] );
				exit;
            }
 
            if ( empty( $_POST['user_pass'] ) ) {                
                echo wp_json_encode( [ 'reset_pass'=>'empty', 'message'=> esc_html__('Password Field is Empty. Enter Password.','theplus') ] );                
                exit;
            }
			
            reset_password( $user, $_POST['user_pass'] );
			
           echo wp_json_encode( [ 'reset_pass'=>'success', 'message'=> esc_html__('Your password has been changed. Use your new password to sign in.','theplus') ] );
		   
        } else {
            echo "Invalid request.";
        }
 
        exit;
    }
}

add_action( 'login_form_theplusrp','redirect_to_tp_custom_password_reset');
if(!empty($_GET['action']) && $_GET['action']=='theplusrp'){
	add_action( 'login_form_resetpass','redirect_to_tp_custom_password_reset' );	
}

function redirect_to_tp_custom_password_reset() {
	
    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
        // Verify key / login combo
		
		if(!empty($_GET['action']) && $_GET['action']=='theplusrp'){
			$datakey = isset($_GET['datakey']) ? $_GET['datakey'] : '';
			$forgotdata = tp_check_decrypt_key($datakey);
			$forgotdata = json_decode(stripslashes($forgotdata),true);
			$user = check_password_reset_key( $forgotdata['key'], rawurldecode($forgotdata['login']) );
			$forgoturl = $forgotdata['forgoturl'];
			$redirecturl = $forgotdata['redirecturl'];
			$login = $forgotdata['login'];
			$key = $forgotdata['key'];
		}else{
			$forgoturl = isset($_GET['forgoturl']) ? wp_http_validate_url($_GET['forgoturl']) : '';
			$redirecturl ='';
			$login = isset($_GET['login']) ? $_GET['login'] : '';
			$key = isset($_GET['key']) ? $_GET['key'] : '';
			
			$user = check_password_reset_key( $key, $login );			
		}
        	
        if ( ! $user || is_wp_error( $user ) ) {
			
            if ( $user && $user->get_error_code() === 'expired_key' ) {
				$redirect_url = $forgoturl;
				$redirect_url = add_query_arg( 'expired', 'expired', $redirect_url );
				wp_safe_redirect($redirect_url);
            } else {
				$redirect_url = $forgoturl;
				$redirect_url = add_query_arg( 'invalid', 'invalid', $redirect_url );
				wp_safe_redirect($redirect_url);
            }
            exit;
        }
		if(!empty($redirecturl)){	
			$data_res = [];
			$data_res['login'] =  $login;
			$data_res['forgoturl'] = $forgoturl;
			$data_res['key'] = $key;
			
			$data_reskey= tp_plus_simple_decrypt( json_encode($data_res), 'ey' );
			
			$redirect_url = $redirecturl;
			$redirect_url = add_query_arg( 'action', 'theplusrpf', $redirect_url );
			$redirect_url = add_query_arg( 'datakey', $data_reskey, $redirect_url );
			wp_safe_redirect($redirect_url);
		}else{
			wp_safe_redirect(home_url());
		}
        exit;
    }
}
/*reset password end*/

function theplus_ajax_register_user( $email='', $first_name='', $last_name='',$tp_user_role='' ) {
	    $errors = new \WP_Error();
		$result    = '';
	    if ( ! is_email( $email ) ) {
	        $errors->add( 'email', esc_html__( 'The email address you entered is not valid.', 'theplus' ) );
	        return $errors;
	    }
	 
	    if ( username_exists( $email ) || email_exists( $email ) ) {
	        $errors->add( 'email_exists', esc_html__( 'An account exists with this email address.', 'theplus' ) );
	        return $errors;
	    }
		
	    if(!empty($_POST["dis_password"]) && $_POST["dis_password"]=='yes'){
			if(!empty($_POST["dis_password_conf"]) && $_POST["dis_password_conf"]!='yes' && $_POST['password']){
				$password = $_POST['password'];
			}else{
				if($_POST['password'] == $_POST['conf_password']){	
					$password = $_POST['password'];
				}else{
					$errors->add( 'pass_mismatch', esc_html__( 'Password & Confirm Password Not Match!', 'theplus' ) );
					return $errors;
				}
			}			
		}else{
			$password = wp_generate_password( 12, false );
		}
		
		if(!empty($_POST["user_login"])){
			$user_login = !empty($_POST['user_login']) ? sanitize_user($_POST['user_login']) : '';
		}else{
			$user_login = $email;
		}
		
	    $user_data = array(
	        'user_login'    => $user_login,
	        'user_email'    => $email,
	        'user_pass'     => $password,
	        'first_name'    => $first_name,
	        'last_name'     => $last_name,
	        'nickname'      => $first_name,
	    );
		$user_id_get = username_exists( $user_login );
		
		$user_id='';
		if ( ! $user_id_get ) {
			$user_id = wp_insert_user( $user_data );
			if(empty($_POST['tceo'])){
				if(!empty($_POST["dis_password"]) && $_POST["dis_password"]=='no'){
					wp_new_user_notification( $user_id, null, 'both' );
				}else{
					wp_new_user_notification( $user_id, null, 'both' );
				}
			}			
			
			wp_update_user( array ('ID' => $user_id) ) ;
		}
		
	    return $user_id;
}
if(get_option('users_can_register')){
	add_action( 'wp_ajax_nopriv_theplus_ajax_register', 'theplus_ajax_register' );
}

function get_element_widget_data( $elements, $id ) {

	foreach ( $elements as $element ) {
		if ( $id === $element['id'] ) {
			return $element;
		}

		if ( ! empty( $element['elements'] ) ) {
			$element = get_element_widget_data( $element['elements'], $id );

			if ( $element ) {
				return $element;
			}
		}
	}

	return false;
}

function theplus_ajax_register() {
	
	 if( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'ajax-login-nonce' ) ){		
		echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'theplus' )] );
		exit;
	 }
	 
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) { 
		if ( ! get_option( 'users_can_register' ) ) {
			echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Registering new users is currently not allowed.', 'theplus' )] );
		} else {
			$email      = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
			$first_name = isset($_POST['first_name']) ? sanitize_text_field( $_POST['first_name'] ) : '';
			$last_name  = isset($_POST['last_name']) ? sanitize_text_field( $_POST['last_name'] ) : '';
			$user_login  = isset($_POST['user_login']) ? sanitize_text_field( $_POST['user_login'] ) : '';
			$passwordemc  = isset($_POST['password']) ? $_POST['password'] : '';
		
			$captcha = isset($_POST["token"]) ? $_POST["token"] : '';
			$dis_cap = $_POST["dis_cap"];
			$dis_mail_chimp = $_POST["dis_mail_chimp"];
			$mail_chimp_check = $_POST["mail_chimp_check"];
			$auto_loggedin = $_POST["auto_loggedin"];
			
			if(!empty($dis_cap) && $dis_cap=='yes'){
				if(!$captcha){
					$message = sprintf(__( 'Please check the the captcha form.', 'theplus' ), get_bloginfo( 'name' ) );
					echo wp_json_encode( ['registered' => false, 'message'=> $message] );					
					exit;
				}
			}
			$check_recaptcha= get_option( 'theplus_api_connection_data' );
			$resscore='';
			$check_captcha = false;
			if( !empty($dis_cap) && $dis_cap=='yes' && !empty($check_recaptcha['theplus_secret_key_recaptcha']) && !empty($captcha) ){
				$secretKey = $check_recaptcha['theplus_secret_key_recaptcha'];
				$ip = $_SERVER['REMOTE_ADDR'];
				
				$url = 'https://www.google.com/recaptcha/api/siteverify';
				$data = array('secret' => $secretKey, 'response' => $captcha);
				
				$options = array(
					'http' => array(
					  'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					  'method'  => 'POST',
					  'content' => http_build_query($data)
					)
				  );
				  
				  
				$recaptcha_secret = isset($data['secret']) ? $data['secret'] : '';
				$recaptcha_respo = isset($data['response']) ? $data['response'] : '';					
				$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $recaptcha_respo);
				$responseKeys = json_decode($response["body"], true);
				
				$resscore=$responseKeys["score"];
				$check_captcha = true;
				if(!$responseKeys['success']){
					$message = sprintf(__( 'Please check the the reCaptcha form.', 'theplus' ), get_bloginfo( 'name' ) );
					echo wp_json_encode( ['registered' => false, 'message'=> $message, 'recaptcha' => false ] );
					exit;
				}
				
			}
			
			$result     = theplus_ajax_register_user( $email, $first_name, $last_name );
			if(empty($result)){				
				echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Username Already Exists.', 'theplus' )] );				
			}else if ( is_wp_error( $result ) ) {
				// Parse errors into a string and append as parameter to redirect
				$errors  = $result->get_error_message();
				echo wp_json_encode( ['registered' => false, 'message'=> $errors ] );
			} else {
				// Success
				
				if(!empty($_POST['tceo']) && (!empty($_POST['tceo']['tp_cst_email_opt']) && $_POST['tceo']['tp_cst_email_opt']=='yes')){
					
					$esub =  stripslashes(html_entity_decode($_POST['tceo']['tp_cst_email_subject']));
					$emsg =  stripslashes(html_entity_decode($_POST['tceo']['tp_cst_email_message']));
					$find = array( '/\[tp_firstname\]/', '/\[tp_lastname\]/', '/\[tp_username\]/', '/\[tp_email\]/', '/\[tp_password\]/' );
					$replacement = array( $first_name,$last_name, $user_login, $email,$passwordemc );
					$cmessage = preg_replace( $find, $replacement, $emsg );
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );
					 
					wp_mail( $email, $esub, $cmessage, $headers );
				}				
				$message = sprintf(__( 'You have successfully registered to %s. We have emailed your password to the email address you entered.', 'theplus' ), get_bloginfo( 'name' ) );
				$response = ['registered' => true, 'message'=> $message, 'recaptcha' => $check_captcha, 'recaptcha_score' => $resscore ];
				
				//mailchimp subscriber user
				
				if((!empty($dis_mail_chimp) && $dis_mail_chimp=='yes') && (!empty($mail_chimp_check) && $mail_chimp_check=='yes')){
					$sep_cust_mail_chimp_apikey = isset($_POST["mc_custom_apikey"]) ? $_POST["mc_custom_apikey"] : '';
					$sep_cust_mail_chimp_listid = isset($_POST["mc_custom_listid"]) ? $_POST["mc_custom_listid"] : '';
					
					$mc_cst_group_value=$mc_cst_tags_value='';

					if(!empty($_POST['mc_cst_group_value']) && sanitize_text_field($_POST['mc_cst_group_value'])){
						$mc_cst_group_value= sanitize_text_field($_POST['mc_cst_group_value']);
					}
					if(!empty($_POST['mc_cst_tags_value']) && sanitize_text_field($_POST['mc_cst_tags_value'])){
						$mc_cst_tags_value= sanitize_text_field($_POST['mc_cst_tags_value']);
					}
					
					plus_mailchimp_subscribe_using_lr($email, $first_name, $last_name,$dis_mail_chimp,$sep_cust_mail_chimp_apikey,$sep_cust_mail_chimp_listid,$mc_cst_group_value,$mc_cst_tags_value);
				}
				
				if((!empty($auto_loggedin) && $auto_loggedin==true)){
					$access_info = [];
					$access_info['user_login']    = !empty($email) ? $email : "";
					$access_info['user_password'] = !empty($_POST['password']) ? $_POST['password'] : "";
					$access_info['rememberme']    = true;
					$user_signon = wp_signon( $access_info, false );
					if ( !is_wp_error($user_signon) ){				
						$response = ['registered' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'theplus')];
					} else {			
						$response = ['registered' => false, 'message'=> esc_html__('Registered Successfully, Ops! Login Failed...!', 'theplus')];
					}
				}
				echo wp_json_encode($response);
			}
		}

		exit;
	}
}

function plus_mailchimp_subscribe_using_lr($email='', $first_name='', $last_name='',$dis_mail_chimp='',$sep_cust_mail_chimp_apikey='',$sep_cust_mail_chimp_listid='',$mc_cst_group_value='',$mc_cst_tags_value=''){
		
	$list_id=$api_key='';
	if($dis_mail_chimp=='yes' && (!empty($sep_cust_mail_chimp_apikey) && !empty($sep_cust_mail_chimp_listid))){
		$api_key = $sep_cust_mail_chimp_apikey;
		$list_id = $sep_cust_mail_chimp_listid;		
	}else{
		$options = get_option( 'theplus_api_connection_data' );
		$list_id = (!empty($options['theplus_mailchimp_id'])) ? $options['theplus_mailchimp_id'] : '';
		$api_key = (!empty($options['theplus_mailchimp_api'])) ? $options['theplus_mailchimp_api'] : '';
	}
	
	$mc_r_status = 'subscribed';
	if(!empty($_POST['mcl_double_opt_in']) && $_POST['mcl_double_opt_in']=='yes'){
		$mc_r_status = 'pending';
	}
	
	$mc_cst_group_value=$mc_cst_tags_value='';

	if(!empty($_POST['mc_cst_group_value']) && sanitize_text_field($_POST['mc_cst_group_value'])){
		$mc_cst_group_value= sanitize_text_field($_POST['mc_cst_group_value']);
	}
	if(!empty($_POST['mc_cst_tags_value']) && sanitize_text_field($_POST['mc_cst_tags_value'])){
		$mc_cst_tags_value= sanitize_text_field($_POST['mc_cst_tags_value']);
	}
	$result = json_decode( theplus_mailchimp_subscriber_message($email, $mc_r_status, $list_id, $api_key, array('FNAME' => $first_name,'LNAME' => $last_name),$mc_cst_group_value,$mc_cst_tags_value ) );	
	
}

function theplus_load_metro_style_layout($columns='1',$metro_column='3',$metro_style='style-1'){
	$i=($columns!='') ? $columns : 1;
	if(!empty($metro_column)){
		//style-3
		if($metro_column=='3' && $metro_style=='style-1'){
			$i=($i<=10) ? $i : ($i%10);			
		}
		if($metro_column=='3' && $metro_style=='style-2'){
			$i=($i<=9) ? $i : ($i%9);			
		}
		if($metro_column=='3' && $metro_style=='style-3'){
			$i=($i<=15) ? $i : ($i%15);			
		}
		if($metro_column=='3' && $metro_style=='style-4'){
			$i=($i<=8) ? $i : ($i%8);			
		}
		//style-4
		if($metro_column=='4' && $metro_style=='style-1'){
			$i=($i<=12) ? $i : ($i%12);			
		}
		if($metro_column=='4' && $metro_style=='style-2'){
			$i=($i<=14) ? $i : ($i%14);			
		}
		if($metro_column=='4' && $metro_style=='style-3'){
			$i=($i<=12) ? $i : ($i%12);			
		}
		//style-5
		if($metro_column=='5' && $metro_style=='style-1'){
			$i=($i<=18) ? $i : ($i%18);			
		}
		//style-6
		if($metro_column=='6' && $metro_style=='style-1'){
			$i=($i<=16) ? $i : ($i%16);			
		}
	}
	return $i;
}

function theplus_key_notice_ajax(){
	if(!isset($_POST['security']) || empty($_POST['security']) || ! wp_verify_nonce( $_POST['security'], 'theplus-addons' )){
		 die ( 'Invalid Nonce Security checked!');
	} 
	
	if ( get_option( 'theplus-notice-dismissed' ) !== false ) {
		update_option( 'theplus-notice-dismissed', '1' );
	} else {
		$deprecated = null;
		$autoload = 'no';
		add_option( 'theplus-notice-dismissed','1', $deprecated, $autoload );
	}
}
if( is_admin() &&  current_user_can("manage_options") ){
	add_action('wp_ajax_theplus_key_notice','theplus_key_notice_ajax');
}

//post pagination
function theplus_pagination($pages = '', $range = 2, $pagination_next='', $pagination_prev=''){  
	$showitems = ($range * 2)+1;  
	
	global $paged;
	if(empty($paged)) $paged = 1;
	
	if($pages == ''){
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
	
	if(1 != $pages){
		$paginate ="<div class=\"theplus-pagination\">";
		if ( get_previous_posts_link() ){
			$paginate .= get_previous_posts_link('<i class="fas fa-long-arrow-alt-left" aria-hidden="true"></i>'.$pagination_prev);
		}
		
		for ($i=1; $i <= $pages; $i++){
			if (1 != $pages && ( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
			{
				$paginate .= ($paged == $i)? "<span class=\"current\">".esc_html($i)."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".esc_html($i)."</a>";
			}
		}
		
		if ( get_next_posts_link() ){
			get_next_posts_link($pagination_next,1);
		}			
		if ( $paged < $pages ) $paginate .= "<a class='paginate-next' href='".get_pagenum_link($paged + 1)."'>".$pagination_next."<i class='fas fa-long-arrow-alt-right' aria-hidden='true'></i></a>";
		
		$paginate .="</div>\n";
		return $paginate;
	}
}

function theplus_mailchimp_subscriber_message( $email, $status, $list_id, $api_key, $merge_fields = array(), $mc_cst_group_value='', $mc_cst_tags_value=''){

    $data = array(
        'apikey'        => $api_key,
        'email_address' => $email,
        'status'        => $status,
    );
	
	if(!empty($merge_fields)){
		$data['merge_fields'] = $merge_fields;
	}
	
	if(!empty($mc_cst_group_value) && sanitize_text_field($mc_cst_group_value)){
		$interests = explode( ' | ', trim( sanitize_text_field($mc_cst_group_value) ) );
		$interests=array_flip($interests);
		
		foreach($interests as $key => $value){
			$data['interests'][$key] = true;
		}
	}
	
	if(!empty($mc_cst_tags_value) && sanitize_text_field($mc_cst_tags_value)){
		$data['tags'] = explode( '|', trim( sanitize_text_field($mc_cst_tags_value)) );
	}
	
	$mch_api = curl_init();
 
    curl_setopt($mch_api, CURLOPT_URL, 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($data['email_address'])));
    curl_setopt($mch_api, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.base64_encode( 'user:'.$api_key )));
    curl_setopt($mch_api, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
    curl_setopt($mch_api, CURLOPT_RETURNTRANSFER, true); // return the API response
    curl_setopt($mch_api, CURLOPT_CUSTOMREQUEST, 'PUT'); // method PUT
    curl_setopt($mch_api, CURLOPT_TIMEOUT, 10);
    curl_setopt($mch_api, CURLOPT_POST, true);
    curl_setopt($mch_api, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($mch_api, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
 
    $result = curl_exec($mch_api);
    return $result;
}
function plus_mailchimp_subscribe(){
	$options = get_option( 'theplus_api_connection_data' );
	$list_id = (!empty($options['theplus_mailchimp_id'])) ? $options['theplus_mailchimp_id'] : '';
	$api_key = (!empty($options['theplus_mailchimp_api'])) ? $options['theplus_mailchimp_api'] : ''; // YOUR MAILCHIMP API KEY HERE
	
	$FNAME=$LNAME=$BIRTHDAY=$PHONE='';	
	$chimp_field = array();
	if(!empty($_POST['FNAME'])){
		$FNAME= sanitize_text_field($_POST['FNAME']);
		$chimp_field['FNAME'] =$FNAME;
	}
	if(!empty($_POST['LNAME'])){
		$LNAME= sanitize_text_field($_POST['LNAME']);
		$chimp_field['LNAME'] =$LNAME;
	}
	if(!empty($_POST['BIRTHDAY']) && !empty($_POST['BIRTHMONTH'])){
		$BIRTHDAY = sanitize_text_field($_POST['BIRTHMONTH']) . '/' . sanitize_text_field($_POST['BIRTHDAY']);
		$chimp_field['BIRTHDAY'] =$BIRTHDAY;
	}
	if(!empty($_POST['PHONE'])){
		$PHONE= wp_unslash($_POST['PHONE']);
		$chimp_field['PHONE'] =$PHONE;
	}
	
	$mc_status = 'subscribed';
	if(!empty($_POST['mc_double_opt_in']) && $_POST['mc_double_opt_in']=='pending'){
		$mc_status = 'pending';
	}
	
	$mc_cst_group_value = '';
	if(!empty($_POST['mc_cst_group_value']) && sanitize_text_field($_POST['mc_cst_group_value'])){
		$mc_cst_group_value= sanitize_text_field($_POST['mc_cst_group_value']);
	}
	
	$mc_cst_tags_value = '';
	if(!empty($_POST['mc_cst_tags_value']) && sanitize_text_field($_POST['mc_cst_tags_value'])){
		$mc_cst_tags_value= sanitize_text_field($_POST['mc_cst_tags_value']);
	}
	
	$result = json_decode( theplus_mailchimp_subscriber_message($_POST['email'], $mc_status, $list_id, $api_key, $chimp_field,$mc_cst_group_value,$mc_cst_tags_value) );
	
	if( $result->status == 400 ){
		echo 'incorrect';
	} elseif( $result->status == 'subscribed' ){
		echo 'correct';
	} elseif( $result->status == 'pending' ){
		echo 'pending';
	} else {
		echo 'not-verify';
	}
	die;
}
add_action('wp_ajax_plus_mailchimp_subscribe','plus_mailchimp_subscribe');
add_action('wp_ajax_nopriv_plus_mailchimp_subscribe', 'plus_mailchimp_subscribe');

if(!function_exists('theplus_api_check_license')){
	function theplus_api_check_license($tp_api_key='',$home_url='',$check_license='') {
		$store_url = 'https://store.posimyth.com';
		$item_name = 'The Plus Addons for Elementor';
		$option_name = 'theplus_verified';
		$license_action = (!empty($check_license)) ? $check_license : 'activate_license';
		$api_params = array(
			'edd_action' => $license_action,
			'license' => $tp_api_key,
			'item_name' => urlencode( $item_name ),
			'url' => $home_url
		);
		
		//@version 3.3.4
		$response = get_transient( 'theplus_verify_trans_api_store' );		
		if (false === $response || $license_action == 'activate_license') {				
			$response = wp_remote_post( $store_url, array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );
			set_transient('theplus_verify_trans_api_store', $response, 172800); 
		}
		
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			
			$message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : esc_html__( 'An error occurred, please try again.','theplus' );
			return false;
		} else {
			
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$license = 'invalid';
			if ( !empty($license_data) && true == $license_data->success  && !empty($license_data->success)) {
				
				if(!empty($license_data->license)){
					$license = $license_data->license;
				}
				
				$expire_date=$license_data->expires;
				if($expire_date!='lifetime'){
					$expire = strtotime($expire_date);
				}else{
					$expire = $expire_date;
				}
				$today_date = strtotime("today midnight");
				if($expire !='lifetime' && $today_date >= $expire && $license_data->license == 'valid'){
					$verify= '0' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'expired';
				}
				if( $license_data->license == 'valid' ) {
					$verify = '1' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'valid';
				}elseif($license_data->license == 'expired' ){
					$verify = '0' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'expired';
				} else {
					$verify = '0' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'invalid';
				}
			}else{
				$verify = '0' ;
				theplus_check_api_options('theplus_verified',$verify,$license);
				
				return 'success_false';
			}
		}
	}
}

if(!function_exists('theplus_api_check_license_code')){
	function theplus_api_check_license_code($tp_api_key='',$generate_key='',$check_license='') {
		if(isset($tp_api_key) && !empty($tp_api_key) && !empty($generate_key)){
			$get_url='https://store.posimyth.com/theplus-verify/';
			$method='verify';
			
			$get_url=$get_url.'?url=verify_api/'.$method.'/'.$tp_api_key.'/'.$generate_key;
			
			//@version 3.3.4
			$response = get_transient( 'theplus_verify_trans_api_code' );
			if (false === $response || $check_license=='') {				
				$response = wp_remote_get( $get_url );
				set_transient('theplus_verify_trans_api_code', $response, 172800);
			}
			
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$license = 'invalid';
			
			$option_name = 'theplus_verified';
			if( !empty($license_data) && $license_data->success == true ) {
				if(!empty($license_data->license)){
					$license = $license_data->license;
				}
				
				$expire=$license_data->expires;
				
				if($expire !='lifetime' && $license_data->license == 'valid'){
					$verify= '0' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'expired';
				}
				if( $license_data->license == 'valid' ) {
					$verify = '1' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'valid';
				}elseif($license_data->license == 'expired' ){
					$verify = '0' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'expired';
				} else {
					$verify = '0' ;
					theplus_check_api_options('theplus_verified',$verify,$license,$expire);
					
					return 'invalid';
				}
			}else{
				$verify = '0' ;
				theplus_check_api_options('theplus_verified',$verify,$license);
				return 'success_false';
			}
		}else{
			return false;
		}
	}
}

if(!function_exists('plus_simple_crypt')){
	function plus_simple_crypt( $string, $action = 'dy' ) {	    
		$tppk=get_option( 'theplus_purchase_code' );
		$generated = !empty(get_option( 'tp_key_random_generate' )) ? get_option( 'tp_key_random_generate' ) : 'PO$_key';
		
		$secret_key = ( isset($tppk['tp_api_key']) && !empty($tppk['tp_api_key']) ) ? $tppk['tp_api_key'] : $generated;
		$secret_iv = 'PO$_iv';
	    $output = false;
	    $encrypt_method = "AES-128-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	 
	    if( $action == 'ey' ) {
	        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	    }
	    else if( $action == 'dy' ){
	        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	    }
	 
	    return $output;
	}
}

function theplus_check_api_options($option_name,$verify,$valid='',$expire=''){
	
	if($option_name!='' && $verify!=''){	
		$value=array(
			 'verify'=>$verify,
			 'license' => $valid,
			 'expire'=>$expire,
		 );
		
		if ( get_option( $option_name ) ) {
			update_option( $option_name, $value );
		} else {
			$deprecated = null;
			$autoload = 'yes';
			add_option( $option_name,$value, $deprecated, $autoload );
		}
	}
}

if(!function_exists('theplus_check_api_status')){
	function theplus_check_api_status() {
		$option_name = 'theplus_verified';
		$values=get_option( $option_name );
		$expired=!empty($values["expire"]) ? $values["expire"] : '';
		$verify=!empty($values["verify"]) ? $values["verify"] : '';
		$today_date = strtotime("today midnight");
			
		if($expired!='lifetime' && $today_date >= $expired ){
			return false;
		}else if($verify==1){
			return true;
		}else{
			return false;
		}
	}
}

//@version 3.3.5
function check_expired_date_key() {
	$option_name = 'theplus_verified';
	
	$values=get_option( $option_name );
	$expired=!empty($values["expire"]) ? $values["expire"] : '';
	$verify=!empty($values["verify"]) ? $values["verify"] : '';
	$today_date = strtotime("today midnight");
	
		if($expired!='lifetime' && $today_date >= $expired ){
			$verify=0;$expire='';
			theplus_check_api_options($option_name,$verify,'',$expire);
		}
}
add_action( 'admin_init', 'check_expired_date_key', 1 );

if ( !class_exists( 'Theplus_BodyMovin' ) ) {
	class Theplus_BodyMovin {
		public static $animations = array();

		function __construct() {
			add_action( 'wp_footer', array( $this, 'plus_animation_data' ), 5 );			
		}

		public static function plus_addAnimation( $animation = array() ) {
			
			if ( empty( $animation ) || empty( $animation['id'] ) ) {
				return false;
			}
			
			self::$animations[$animation['container_id']] = $animation;
		}
		public static function plus_getAnimations() {
			return apply_filters( 'wpbdmv-animations', self::$animations );
		}

		public static function plus_hasAnimations() {
			$animations = self::plus_getAnimations();
			return empty( $animations ) ? false : true;
		}

		function plus_animation_data() {
			if ( !self::plus_hasAnimations() ) {
				return;
			}
			wp_localize_script( 'theplus-bodymovin', 'wpbodymovin', array(
				'animations' => self::plus_getAnimations(),
				'ajaxurl'    => admin_url( 'admin-ajax.php' )
			) );
		}

	}
	$Theplus_BodyMovin = new Theplus_BodyMovin;
}

/*woo thank you page selection start*/
function theplus_thankyou_content_func(){
	$tp_data=get_option( 'theplus_api_connection_data' );
	$tp_thankyoupage_id = $tp_data['theplus_woo_thank_you_page_select'];	
	if(!empty($tp_thankyoupage_id)){
		echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $tp_thankyoupage_id );
	}else{
		the_content();
	}
}
add_action( 'theplus_thankyou_content', 'theplus_thankyou_content_func' );

add_filter(	'wc_get_template','theplus_checkout_page_template', 51, 3 );

function theplus_checkout_page_template($located, $name, $args){	
	$tp_data=get_option( 'theplus_api_connection_data' );		
	if($name === 'checkout/thankyou.php' && !empty($tp_data['theplus_woo_thank_you_page_select'])){
		$located = THEPLUS_INCLUDES_URL . 'woo-thankyou/thankyou.php';
	}
	return $located;
}
/*woo thank you page selection end*/

//Woocommerce Products
if(class_exists('woocommerce')) {
function theplus_out_of_stock() {
  global $post;
  $id = $post->ID;
  $status = get_post_meta($id, '_stock_status',true);
  
  if ($status == 'outofstock') {
  	return true;
  } else {
  	return false;
  }
}
function theplus_product_badge($out_of_stock_val='') {
 global $post, $product;
 	if (theplus_out_of_stock()) {
		echo '<span class="badge out-of-stock">'.$out_of_stock_val.'</span>';
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
				if( !empty($product->get_sale_price()) ){
					$salePrice = $product->get_sale_price();
					$percentage = round( ( ( $product->get_regular_price() - $salePrice ) / $product->get_regular_price() ) * 100 );
					// $output_html = '<span class="badge onsale perc">&darr; '.$percentage.'%</span>';
					echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
				}
			} else if ($product->get_type() == 'external'){
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
				echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
			}
		} else {
			echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.esc_html__( 'Sale','theplus' ).'</span>', $post, $product);
		}
	}
}
add_action( 'theplus_product_badge', 'theplus_product_badge',3 );

function plus_filter_woocommerce_sale_flash( $output_html, $post, $product ) { 
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
		$output_html = '<span class="badge onsale perc">&darr; '.$maximumper.'%</span>';
	} else if ($product->get_type() == 'simple'){
		if( !empty($product->get_sale_price() )){
			$salePrice = $product->get_sale_price();
			$percentage = round( ( ( $product->get_regular_price() - $salePrice ) / $product->get_regular_price() ) * 100 );
			$output_html = '<span class="badge onsale perc">&darr; '.$percentage.'%</span>';
		}
	} else if ($product->get_type() == 'external'){
		if( !empty($product->get_sale_price() )){
			$salePrice = $product->get_sale_price();
			$percentage = round( ( ( $product->get_regular_price() - $salePrice ) / $product->get_regular_price() ) * 100 );
			$output_html = '<span class="badge onsale perc">&darr; '.$percentage.'%</span>';
		}
	}else {
		$output_html = '<span class="badge onsale">'.esc_html__( 'Sale','theplus' ).'</span>';
	}
    return $output_html;
}; 

add_filter( 'woocommerce_sale_flash', 'plus_filter_woocommerce_sale_flash', 11, 3 );

}

add_action('elementor/widgets/register', function($widgets_manager){
	$elementor_widget_blacklist = [
		'plus-elementor-widget',
	];
	
	foreach($elementor_widget_blacklist as $widget_name){
		$widgets_manager->unregister($widget_name);
	}
}, 15);

function registered_widgets(){

	// widgets class map
	return [
		
		'tp-adv-text-block' => [
			'dependency' => [],
		],
		'tp-advanced-typography' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/adv-typography/plus-adv-typography.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/circletype.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/adv-typography/plus-adv-typography.min.js',
				],
			],
		],
		'tp-advanced-buttons' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/advanced-buttons/plus-advanced-buttons.min.css',
				],
			],
		],
		'tp-advanced-buttons-js' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/advanced-buttons/plus-advanced-buttons.min.js',
				],
			],
		],
		'tp_advertisement_banner' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/addbanner/plus-addbanner.min.css',
				],
			],
		],
		'tp-accordion' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/tabs-tours/plus-tabs-tours.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/accordion/plus-accordion.min.js',
				],
			],
		],
		'tp-age-gate' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/age-gate/plus-age-gate.min.css',
				],
				'js' => [
					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/age-gate/plus-age-gate.min.js',
				],
			],
		],
		'tp-animated-service-boxes' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/animated-service-box/plus-animated-service-boxes.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/animated-service-box/plus-service-box.min.js',
				],
			],
		],
		'tp-audio-player' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/audio-player/plus-audio-player.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/audio-player/plus-audio-player.min.js',					
				],
			],
		],
		'tp-before-after' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/before-after/plus-before-after.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/before-after/plus-before-after.min.js',
				],
			],
		],
		'tp-blockquote' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/block-quote/plus-block-quote.css',
				],
			],
		],
		'tp-blog-listout' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/blog-list/plus-blog-list.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],
		'tp-dynamic-smart-showcase' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/dynamic-smart-showcase/plus-dynamic-smart-showcase.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/dynamic-smart-showcase/plus-dynamic-smart-showcase.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/dynamic-smart-showcase/plus-bss-filter.min.js',
				],
			],
		],
		'tp-breadcrumbs-bar' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/breadcrumbs-bar/plus-breadcrumbs-bar.min.css',
				],				
			],
		],
		'plus-post-filter' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-post-filter.min.css',
				],
			],
		],
		'plus-pagination' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-pagination.css',
				],
			],
		],
		'plus-listing-metro' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/isotope.pkgd.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-metro-list.min.js',
				],
			],
		],
		'plus-listing-masonry' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/isotope.pkgd.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/packery-mode.pkgd.min.js',
				],
			],
		],
		'tp-button' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button.min.css',
				],
			],
		],
		'tp-wp-bodymovin' => [
		],
		'tp-carousel-anything' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/slick.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-slick-carousel.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-carousel-anything.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/slick.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-slick-carousel.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/carousel-anything/plus-carousel-anything.min.js',
				],
			],
		],
		'tp-carousel-remote' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-carousel-remote.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/slick.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/carousel-remote/plus-carousel-remote.min.js',
				],
			],
		],
		'tp-caldera-forms' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/forms-style/plus-caldera-form.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/forms-style/plus-caldera-form.js',
				],
			],
		],
		'tp-cascading-image' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/image-factory/plus-image-factory.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/cascading-image/plus-cascading-image.min.js',
				],
			],
		],
		'tp-chart' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/chart.js', 
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/chart/plus-chart.min.js', 
				], 
			],
		],
		'tp-circle-menu' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/circle-menu/plus-circle-menu.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.circlemenu.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/circle-menu/plus-circle-menu.min.js',
				],
			],
		],
		'tp-clients-listout' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/client-list/plus-client-list.css',					
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],
		'tp-contact-form-7' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/forms-style/plus-cf7-style.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/forms-style/plus-cf7-form.js',
				],
			],
		],
		'tp-coupon-code' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/coupon-code/plus-coupon-code.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/html2canvas.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/peeljs.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/coupon-code/plus-coupon-code.min.js',
				],
			],
		],
		'tp-dynamic-listing' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/dynamic-listing/plus-dynamic-listing.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/dynamic-listing/plus-dynamic-listing.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],
		'tp-dynamic-listout-qview' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/jquery.fancybox.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.fancybox.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/dynamic-listing/plus-dynamic-listing-qview.min.js',
				],
			],
		],
		'tp-custom-field' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/custom-field/plus-custom-field.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/custom-field/plus-custom-field.min.js',					
				],
			],
		],
		'tp-countdown' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/countdown/flipdown.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/countdown/plus-countdown.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/jquery.downCount.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/countdown/flipdown.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/countdown/progressbar.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/countdown/plus-countdown.min.js',
				],
			],
		],
		'tp-dark-mode' => [
			'dependency' => [
				'css' => [										
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/darkmode/plus-dark-mode.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/darkmode.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/darkmode/plus-dark-mode.min.js',
				],
			],
		],
		'tp-draw-svg' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/vivus.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/draw-svg/plus-draw-svg.min.js',
				],
			],
		],
		'tp-dynamic-device' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/lity.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/dynamic-device/plus-dynamic-device.min.css',					
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/lity.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/dynamic-device/plus-dynamic-device.min.js',
				],
			],
		],
		'tp-everest-form' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/forms-style/plus-everest-form.css',
				],
			],
		],
		'tp-smooth-scroll' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/smooth-scroll.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/smooth-scroll/plus-smooth-scroll.min.js',
				],
			],
		],
		'tp-flip-box' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/info-box/plus-info-box.min.css',
				],
			],
		],
		
		'tp-gallery-listout' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/gallery-list/plus-gallery-list.min.css',					
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.hoverdir.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],
		'tp-google-map' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/google-map/plus-gmap.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/google-map/plus-gmap.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/osmmap/markerclusterer.js',
				]
			],
		],
		'tp-gravityt-form' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/forms-style/plus-gravity-form.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/forms-style/plus-gravity-form.js',
				]
			],
		],		
		'tp-heading-animation' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/heading-animation/plus-heading-animation.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/heading-animation/plus-heading-animation.min.js',
				]
			],
		],
		'tp-header-extras' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/header-extras/plus-header-extras.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/buzz.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/header-extras/plus-header-extras.min.js',
				],
			],
		],
		'tp-heading-title' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/heading-title/plus-heading-title.min.css',
				],			
			],
		],
		'tp-heading-title-splite-animation' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.waypoints.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/splittext.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/tweenmax/tweenmax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/heading-title/plus-heading-title.min.js',
				],				
			],
		],
		'tp-hotspot' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tippy.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/hotspot/plus-hotspot.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tippy.all.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/hotspot/plus-hotspot.min.js',
				],
			],
		],
		'tp-horizontal-scroll-advance' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/horizontal-scroll/plus-horizontal-scroll.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/gsap/gsap.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/gsap/ScrollTrigger.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/gsap/ScrollToPlugin.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/horizontal-scroll/plus-horizontal-scroll.min.js',
				],
			],
		],
		'tp-image-factory' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/image-factory/plus-image-factory.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/image-factory/plus-image-factory.min.js',
				],
			],
		],
		
		'tp-info-box' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/info-box/plus-info-box.min.css',
				],
			],
		],
		'tp-info-box-js' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/info-box/plus-info-box.min.js',
				],
			],
		],
		'tp-instagram' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/tp-bootstrap-grid.css',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/plus-extra-adv/plus-instafeed.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/isotope.pkgd.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/instafeed.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/instafeed/plus-instafeed.min.js',
				],
			],
		],
		'tp-mailchimp-subscribe' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/mailchimp/plus-mailchimp.css',
				],
			],
		],
		'tp-messagebox' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/messagebox/plus-messagebox.min.css',
				],
			],
		],
		'tp-messagebox-js' => [
			'dependency' => [
				'js' => [
					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/messagebox/plus-messagebox.min.js',
				],
			],
		],
		'tp-morphing-layouts' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/shape-morph/plus-shape-morph.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/scrollmonitor.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/anime.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/shape-morph/theplus-shape-morph.min.js',
				],
			],
		],
		'tp-mouse-cursor' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/mouse-cursor-widget/plus-mouse-cursors.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/mouse-cursor-widget/plus-mouse-cursors.min.js',
				],
			],
		],
		'tp-navigation-menu' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/navigation-menu/plus-nav-menu.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/navigation-menu/plus-nav-menu.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/headroom.min.js',
				],
			],
		],
		'tp-navigation-menu-lite' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/navigation-menu-lite/plus-nav-menu-lite.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/navigation-menu/plus-nav-menu.min.js',
				],
			],
		],
		'tp-ninja-form' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/forms-style/plus-ninja-form.css',
				],
			],
		],
		'tp-number-counter' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/number-counter/plus-number-counter.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/numscroller.js',
				],
			],
		],
		'tp-post-featured-image' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-feature-image/plus-post-image.min.css',					
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/post-feature-image/plus-post-feature-image.min.js',
				],
			],
		],
		'tp-post-featured-image-js' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/post-feature-image/plus-post-feature-image.min.js',
				],
			],
		],
		'tp-post-title' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-title/plus-post-title.min.css',					
				],				
			],
		],
		'tp-post-content' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-content/plus-post-content.min.css',					
				],				
			],
		],
		'tp-post-meta' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-meta-info/plus-post-meta-info.min.css',
				],
				
			],
		],
		'tp-post-author' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-author/plus-post-author.min.css',
				],				
			],
		],
		'tp-post-comment' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-comment/plus-post-comment.min.css',
				],				
			],
		],
		'tp-post-navigation' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .  'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/post-navigation/plus-post-navigation.min.css',
				],				
			],
		],
		'tp-off-canvas' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/off-canvas/plus-off-canvas.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/offcanvas/plus-offcanvas.js',
				],
			],
		],
		'tp-page-scroll' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/page-scroll/plus-page-scroll.min.css',
				],
				'js'  => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/page-scroll/plus-page-scroll.min.js',
				],
			],
		],
		'tp-fullpage' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/fullpage.css',
				],
				'js'  => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/fullpage.js',
				],
			],
		],
		'tp-pagepiling' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/jquery.pagepiling.css',
				],
				'js'  => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/jquery.pagepiling.min.js',
				],
			],
		],
		'tp-multiscroll' => [
			'dependency' => [
				'js'  => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/jquery.multiscroll.min.js',
				],
			],
		],
		'tp-horizontal-scroll' => [
			'dependency' => [
				'js'  => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/jquery.jInvertScroll.min.js',
				],
			],
		],
		'tp-mobile-menu' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/mobile-menu/plus-mobile-menu.min.css',
				],
				'js'  => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/mobile-menu/plus-mobile-menu.min.js',
				],
			],
		],
		'tp-pricing-list' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/pricing-list/plus-pricing-list.min.css',
				],
			],
		],
		'tp-pricing-table' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tippy.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/pricing-table/plus-pricing-table.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tippy.all.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/pricing-table/plus-pricing-table.min.js',
				],
			],
		],
		'tp-product-listout' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .  'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/product-list/plus-product-list.css',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',					
				],
			],
		],
		'tp-product-listout-swatches' => [
			'dependency' => [
				'css' => [			
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-swatches/woo-swatches-front.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/woo-swatches/woo-swatches-front.js',					
				],
			],
		],
		'tp-product-listout-qcw' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/product-list/plus-product-list-yith.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/jquery.fancybox.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/product-listing/plus-product-listing-qcw.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.fancybox.min.js',
				],
			],
		],
		'tp-ajax-based-pagination' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/ajax-pagination/plus-ajax-pagination.min.js',				
				],
			],
		],
		'plus-product-listout-yithcss' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/product-list/plus-product-list-yith.css',
				],
			],
		],
		'plus-product-listout-quickview' => [
			'dependency' => [				
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/product-listing/plus-product-listing.min.js',					
				],
			],
		],
		'plus-key-animations' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-animation/plus-key-animations.min.css',
				],
			],
		],
		'tp-protected-content' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-password-protected.css',
				],
			],
		],
		'tp-post-search' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/mailchimp/plus-mailchimp.css',
				],
			],
		],
		'tp-progress-bar' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/progress-piechart/plus-progress-piechart.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.waypoints.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/circle-progress.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/progress-bar/plus-progress-bar.min.js',
				],
			],
		],
		'tp-process-steps' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/process-steps/plus-process-steps.min.css',
				],
			],
		],
		'tp-process-steps-js' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/process-steps/plus-process-steps.min.js',
				],
			],
		],
		'tp-row-background' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/row-background/plus-row-background.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/row-background/plus-row-background.min.js',
				],
			],
		],
		'plus-vegas-gallery' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/vegas.css',					
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/vegas.js',
				],
			],
		],
		'plus-row-animated-color' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/effect.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/row-background/plus-row-animate-color.js',
				],
			],
		],
		'plus-row-segmentation' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/anime.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/segmentation.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/row-background/plus-row-segmentation.min.js',
				],
			],
		],
		'plus-row-scroll-color' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/scrolling_background_color.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/scrollmonitor.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/row-background/plus-scroll-bg-color.min.js',
				],
			],
		],
		'plus-row-canvas-particle' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/particles.min.js',
				],
			],
		],
		'plus-row-canvas-particleground' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.particleground.js', //canvas style 6
				],
			],
		],
		'plus-row-canvas-8' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/row-background/plus-row-canvas-style-8.min.js',
				],
			],
		],
		'tp-scroll-navigation' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/scroll-navigation/plus-scroll-navigation.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/pagescroll2id.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/scroll-navigation/plus-scroll-navigation.min.js',
				],
			],
		],
		'tp-scroll-sequence' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/scroll-sequence/tp-scroll-sequence.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/scroll-sequence/tp-scroll-sequence.min.js',
				],
			],
		],
		'tp-search-filter' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/datepicker.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/nouislider.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/jsdelivr.daterangepicker.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/search-filter/plus-search-filter.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/datepicker.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/nouislider.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/moment.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/extra/jsdelivr.daterangepicker.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/js/main/search-filter/plus-search-filter.min.js',
				],
			],
		],
		'tp-search-bar' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/search-bar/plus-search-bar.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/search-bar/plus-search-bar.min.js',
				],
			],
		],
		'tp-shape-divider' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/shape-divider/plus-shape-divider.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/shape-divider/plus-shape-divider.min.js',
				],
			],
		],
		'tp-site-logo' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/site-logo/plus-site-logo.css',
				],		
			],
		],
		'tp-social-embed' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/social-embed/plus-social-embed.min.css',
				],		
			],
		],
		'tp-social-feed' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/social-feed/plus-social-feed.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/jquery.fancybox.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',		
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/social-feed/plus-social-feed.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.fancybox.min.js',
				],
			],
		],
		'tp-social-icon' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/social-icon/plus-social-icon.min.css',
				],				
			],
		],
		'tp-social-reviews' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/social-reviews/plus-social-reviews.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/social-reviews/plus-social-reviews.min.js',
				],
			],
		],
		'tp-social-sharing' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/social-sharing/plus-social-sharing.min.css',
				],
				'js' => [
					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/social-sharing/plus-social-sharing.min.js',
				],
			],
		],
		'tp-style-list' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/stylist-list/plus-style-list.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/stylist-list/plus-stylist-list.min.js',
				],
			],
		],
		'tp-switcher' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/switcher/plus-switcher.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/switcher/plus-switcher.min.js',
				],
			],
		],
		'tp-syntax-highlighter' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/syntax-highlighter/plus-syntax-highlighter.min.css',
				],
			],
		],
		'tp-syntax-highlighter-icons' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/tp-copy-dow-icons.js',
				],
			],
		],
		'prism_default' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-default.js',
				],
			],
		],
		'prism_coy' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-coy.js',
				],
			],
		],
		'prism_dark' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-dark.js',
				],
			],
		],
		'prism_funky' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-funky.js',
				],
			],
		],
		'prism_okaidia' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-okaidia.js',
				],
			],
		],
		'prism_solarizedlight' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-solarizedlight.js',
				],
			],
		],
		'prism_tomorrownight' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-tomorrownight.js',
				],
			],
		],
		'prism_twilight' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/syntax-highlighter/prism-twilight.js',
				],
			],
		],
		'tp-table' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/data-table/plus-data-table.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.datatables.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/data-table/plus-data-table.min.js',
				],
			],
		],
		'tp-table-content' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tocbot.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/table-content/plus-table-content.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tocbot.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/table-content/plus-table-content.min.js',
				],
			],
		],
		'tp-tabs-tours' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/tabs-tours/plus-tabs-tours.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/tabs-tours/plus-tabs-tours.min.js',
				],
			],
		],
		'tp-team-member-listout' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/team-member-list/plus-team-member.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],
		'tp-testimonial-listout' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/slick.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-slick-carousel.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/testimonial/plus-testimonial.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/slick.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-slick-carousel.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/testimonial/plus-testimonial.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],
		'tp-timeline' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/timeline/plus-timeline.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.waypoints.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/isotope.pkgd.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/packery-mode.pkgd.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/velocity/velocity.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/velocity/velocity.ui.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-animation-load.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/timeline/plus-timeline.min.js',					
				],
			],
		],
		'tp-unfold' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/unfold/plus-unfold.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/unfold/plus-unfold.min.js',
				],
			],
		],
		'tp-video-player' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/lity.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/video-player/plus-video-player.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/lity.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/video-player/plus-video-player.min.js',
				],
			],
		],
		'tp-dynamic-categories' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/dynamic-categories/plus-dynamic-categories.min.css',
				],
			],
		],
		'tp-dynamic-categories-st3' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/dynamic-category/plus-dynamic-category.min.js',	
				],
			],
		],
		'tp-wp-forms' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/forms-style/plus-wpforms-form.css',
				],
			],
		],
		'tp-woo-cart' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-cart/plus-woo-cart.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/woo-cart/plus-woo-cart.min.js',	
				],
			],
		],
		'tp-woo-checkout' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-checkout/plus-woo-checkout.min.css',
				],
			],
		],
		'tp-woo-myaccount' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-my-account/plus-woo-my-account.min.css',
				],
			],
		],
		'tp-woo-order-track' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-order-track/plus-woo-order-track.min.css',
				],			
			],
		],
		'tp-woo-single-basic' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-single-basic/plus-woo-single-basic.min.css',
				],
			],
		],
		'tp-woo-single-pricing' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-single-pricing/plus-woo-single-pricing.min.css',
				],				
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/woo-single-pricing/plus-add-to-cart.min.js',
				],
			],
		],
		'tp-woo-single-image' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .  'assets/css/extra/tp-bootstrap-grid.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-single-image/plus-woo-single-image.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/isotope.pkgd.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/posts-listing/plus-posts-listing.min.js',
				],
			],
		],		
		'tp-woo-single-tabs' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-single-tabs/plus-woo-single-tabs.min.css',
				],
			],
		],
		'tp-woo-thank-you' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/woo-thank-you/plus-woo-thank-you.min.css',
				],
			],
		],
		'tp-wp-login-register' => [
			'dependency' => [
				'css' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/wp-login-register/plus-wp-login-register.min.css',
				],
			],
		],
		'tp-wp-login-register-ex' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/login-register/plus-login-register.min.js',
				],
			],
		],
		'plus-lottie-player' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/lottie-player.js',
				],
			],
		],
		'plus-velocity' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.waypoints.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/velocity/velocity.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/velocity/velocity.ui.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-animation-load.min.js',
				],
			],
		],
		'plus-magic-scroll' => [
			'dependency' => [
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/timelinemax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/tweenmax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/scrollmagic/scrollmagic.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/scrollmagic/animation.gsap.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-magic-scroll.min.js',
				],
			],
		],
		'plus-tooltip' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tippy.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tippy.all.min.js',
				],
				],
		],
		'plus-mousemove-parallax' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/tweenmax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-mouse-move-parallax.min.js',
				],
			],
		],
		'plus-tilt-parallax' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tilt.jquery.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-tilt-parallax.min.js',
				],
			],
		],
		'plus-reveal-animation' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.waypoints.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-reveal-animation.min.js',
				],
			],
		],
		'plus-content-hover-effect' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-content-hover-effect.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-content-hover-effect.min.js',
				],
			],
		],
		'plus-button' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button.min.css',
				],
			],
		],
		'plus-button-extra' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button-extra.min.css',
				],
			],
		],
		'plus-carousel' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/slick.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-slick-carousel.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/slick.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-slick-carousel.min.js',
				],
			],
		],
		'plus-imagesloaded' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',
				],
			],
		],
		'plus-isotope' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/isotope.pkgd.js',
				],
			],
		],
		'plus-hover3d' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.hover3d.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-hover-tilt.js',
				],
			],
		],
		'plus-wavify' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/tweenmax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/wavify.js',
				],
			],
		],
		'plus-lity-popup' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/lity.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/lity.min.js',
				],
			],
		],
		'plus-extras-column' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/resizesensor.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/sticky-sidebar.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/column-stickly/plus-column-stickly.min.js',
				],
			],
		],
		'plus-equal-height' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/equal-height/plus-equal-height.min.js',
				],
			],
		],
		'plus-section-column-link' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/section-column-link/plus-section-column-link.min.js',
				],
			],
		],
		'plus-event-tracker' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/event-tracker/plus-event-tracker.min.js',
				],
			],
		],
		'plus-lazyLoad' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/main/lazy_load/tp-lazy_load.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/lazyload.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/lazy_load/tp-lazy_load.js',
				],
			],
		],
		'plus-column-cursor' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/mouse-cursor/plus-mouse-cursor.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/mouse-cursor/plus-mouse-cursor.min.js',
				],
			],
		],
		'plus-extras-section-skrollr' => [
			'dependency' => [
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/skrollr.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-section-skrollr.min.js',
				],
			],
		],
		'plus-adv-typo-extra-js-css' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR .'assets/css/extra/imagerevealbase.css',
				],
				'js' => [										
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/charming.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagesloaded.pkgd.min.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/tweenmax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/imagerevealdemo.js',
				],
			],
		],
		'plus-swiper' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/swiper-bundle.min.css',
				],
				'js' => [					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/swiper-bundle.min.js',
				],
			],
		],
		'plus-backend-editor' => [
			'dependency' => [
				'css' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/extra/tippy.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-button.min.css',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/css/main/plus-extra-adv/plus-content-hover-effect.min.css',
				],
				'js' => [
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/swiper-bundle.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/jquery.waypoints.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/general/modernizr.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/velocity/velocity.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/velocity/velocity.ui.js',					
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tilt.jquery.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tippy.all.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/timelinemax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/tweenmax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/tweenmax/jquery-parallax.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/scrollmagic/scrollmagic.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/scrollmagic/animation.gsap.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/plus-extra-adv/plus-backend-editor.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/process-steps/plus-process-steps.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-animation-load.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-magic-scroll.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-mouse-move-parallax.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-reveal-animation.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/general/plus-content-hover-effect.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/extra/splittext.min.js',
					THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/main/heading-title/plus-heading-title.min.js',
					L_THEPLUS_PATH . DIRECTORY_SEPARATOR . 'assets/js/admin/tp-advanced-shadow-layout.js',
				],
			],
		],
	];
	
}