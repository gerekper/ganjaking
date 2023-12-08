<?php 
define( 'TP_PLUS_SL_STORE_URL', 'https://store.posimyth.com' );
define( 'TP_PLUS_SL_ITEM_ID', 28 );

if ( ! defined( 'ABSPATH' ) ) { exit; }

function plus_get_templates_library($category){
	
	if(!empty($category)){
		
		$data = array(
			'apikey'        => 'https://theplusaddons.com',
			'json' => 'wp-json',
			'version'        => 'v1',
			'template' => 'theplus',
			'category'  => $category
		);
		
		$url_api = $data["apikey"].'/'.$data["json"].'/'.$data["template"];
		
		$api_version = $url_api.'/'.$data["version"];
		
		$api_content_url =$api_version.'/'.$data["category"];
		
		$request = get_transient( 'theplus_get_template_'.$category );		
		if (false === $request) {
			$request = wp_remote_get( $api_content_url );
			set_transient('theplus_get_template_'.$category, $request, 86400); 
		}
		
		if( is_wp_error( $request ) ) {
			return false;
		}
		
		$result = wp_remote_retrieve_body( $request );
		
		return $result;
		
	}else{
		return false;
	}
}
function theplus_template_library_content(){	
	if(!isset($_POST['security']) || empty($_POST['security']) || ! wp_verify_nonce( $_POST['security'], 'theplus-addons' )){
		 die ( 'Invalid Nonce Security checked!');
	} 
   
	$template_library ='';
	$categorypost  = !empty($_POST['category'])? sanitize_text_field(wp_unslash($_POST['category'])) : '';
	
	if(!empty($categorypost)){
		$result  = plus_get_templates_library($categorypost);
	}
	
	
	$json_content='';	
	if(!empty($result)){
	
		$json_content=json_decode($result,true);
	}
	
	if(!empty($json_content)){
	
		foreach ($json_content["content"] as $item) {
			$cate_item='';
			if(!empty($item['categories'])){
			
				foreach($category=$item['categories'] as $term){
					$cate_item .= $term["slug"].' ';
				}
			}
			if(!empty($item['template_type'])){
				$type= $item['template_type'];
			}else{
				$type= 'json';
			}
			$template_library .= '<div class="plus-template-library-template '.esc_attr($cate_item).'">';
				$template_library .= '<div class="template-library-inner-content">';
					$template_library .= '<div class="plus-template-library-template-body">';
						$template_library .= '<img src="'.esc_url($item['thumbnail']).'">';			
							$template_library .= '<div class="plus-template-library-template-download">';
								$template_library .= '<div class="overlay-library-template-inner">';
									$template_library .= '<div class="template-download" data-url="'.esc_attr($item['template_file']).'" data-type="'.esc_attr($type).'"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 512 512"><path fill="#00000094" d="M216 0h80c13.3 0 24 10.7 24 24v168h87.7c17.8 0 26.7 21.5 14.1 34.1L269.7 378.3c-7.5 7.5-19.8 7.5-27.3 0L90.1 226.1c-12.6-12.6-3.7-34.1 14.1-34.1H192V24c0-13.3 10.7-24 24-24zm296 376v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h146.7l49 49c20.1 20.1 52.5 20.1 72.6 0l49-49H488c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z"></path></svg><img src="'.THEPLUS_ASSETS_URL.'images/lazy_load.gif" class="loading-template"></div>';
									$template_library .= '<a href="'.esc_url($item['demo_url']).'" target="_blank" class="template-demo-url" data-url="'.esc_attr__('accordion','theplus').'">
									
									<svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 576 512"><path fill="#00000094" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg></a>';
								$template_library .= '</div>';
							$template_library .= '</div>';
					$template_library .= '</div>';
							
					$template_library .= '<div class="plus-template-library-template-footer">';
						$template_library .= '<div class="plus-template-title">'.esc_html($item['title']).'</div>';
					$template_library .= '</div>';
				$template_library .= '</div>';
			$template_library .= '</div>';
			}
		
		$widget_content='<div class="plus-sub-category-list">';
			$widget_content .='<ul class="sub-category-listing">';
				$widget_content .='<li class="active" data-filter="*">'.esc_html__('All','theplus').'</li>';
				foreach ($json_content["filter_category"] as $item) {
					$widget_content .='<li class="" data-filter="'.esc_attr($item['slug']).'">'.esc_html($item['name']).'</li>';
				}
			$widget_content .='</ul>';
		$widget_content .='</div>';
		$widget_content .='<div class="plus-template-container">';
			$widget_content .='<div class="plus-template-innner-content">';
				$widget_content .=$template_library;
			$widget_content .='</div>';
		$widget_content .='</div>';
		
		echo $widget_content;
	}
	
	die;
}
if( is_admin() &&  current_user_can("manage_options") ){
	add_action('wp_ajax_plus_template_library_content','theplus_template_library_content');
	add_action('wp_ajax_nopriv_plus_template_library_content', 'theplus_template_library_content');
}

function theplus_template_ajax(){	
	if(!isset($_POST['security']) || empty($_POST['security']) || ! wp_verify_nonce( $_POST['security'], 'theplus-addons' )){
		 die ( 'Invalid Nonce Security checked!');
	} 
	
	$widget_category  = !empty($_POST['widget_category'])? sanitize_text_field(wp_unslash($_POST['widget_category'])) : '';
	$template  = !empty($_POST['template'])? sanitize_text_field(wp_unslash($_POST['template'])) : '';
	
	if(!empty($widget_category) && !empty($template)){
		$data = array(
			'apikey'        => 'https://theplusaddons.com',
			'json' => 'json',
			'template' => $template,
			'category'  => $widget_category,
			'file_type' => !empty($_POST["file_type"]) ? sanitize_text_field($_POST["file_type"]) : ''
		);
		$url_api = $data["apikey"].'/'.$data["json"].'/'.$data["category"];
		
		$api_content_url= $url_api.'/'.$data["template"].'.'.$data["file_type"];
		
		$request = wp_remote_get( $api_content_url );
		
		if(!empty($data['file_type']) && $data['file_type']=='zip'){
		
			if( is_wp_error( $request ) ) {
				return false;
			}
			$result = $api_content_url;
		}else{
		
			if( is_wp_error( $request ) ) {
				return false;
			}
			
			$result = wp_remote_retrieve_body( $request );
		}
		
		echo $result;
		
	}else{
		return false;
	}
	die;
}
if( is_admin() &&  current_user_can("manage_options") ){
	add_action('wp_ajax_plus_template_ajax','theplus_template_ajax');
	add_action('wp_ajax_nopriv_plus_template_ajax', 'theplus_template_ajax');
}

if(!function_exists('theplus_get_api_check')){
	function theplus_get_api_check($check_license ='') {
		$home_url=get_home_url();
		
		$purchase_option=get_option( 'theplus_purchase_code' );
		if(isset($purchase_option['tp_api_key']) && !empty($purchase_option['tp_api_key'])){
			$theplus_type=THEPLUS_TYPE;
			if(!empty($theplus_type) && $theplus_type=='code'){
				$home_url=plus_simple_crypt( $home_url, 'ey' );
				return theplus_api_check_license_code($purchase_option['tp_api_key'],$home_url,$check_license);
			}else if(!empty($theplus_type) && $theplus_type=='store'){
				return theplus_api_check_license($purchase_option['tp_api_key'],home_url(),$check_license);
			}
		}else{
			$verify = '0' ;
			theplus_check_api_options('theplus_verified',$verify);
			return false;
		}
	}
}



if(!function_exists('theplus_message_display')){
	function theplus_message_display() {
		$option_name = 'theplus_verified';
		$values=get_option( $option_name );
		
		$purchase_option=get_option( 'theplus_purchase_code' );
		if(isset($purchase_option['tp_api_key']) && !empty($purchase_option['tp_api_key'])){
		
			$check='';
			if( isset($values) && !empty($values) ){
				if( empty($values['license']) ){
					$check = theplus_get_api_check('check_license');					
				}
				if( !empty($values['license']) ){
					$check = $values['license'];
				}
			}
			
			if($check=='expired'){
				echo '<div style="width:auto;position:relative;word-wrap:break-word;background-color:#fff;border-radius:0 0 .25rem .25rem;border:none;margin:0;padding-left:30px;padding-bottom:30px;"><div style="padding:8px 0 8px 15px;border-left:3px solid #f2dede;margin-left:15px;color:#313131;font-size:14px;line-height:26px;display:flex;align-items:center;"><strong>'.esc_html__('Licence key recently Expired 😵','theplus').'</strong> '.esc_html__('Please visit account to renew that.','theplus').'</div></div>';
			}else if($check=='valid'){
				echo '<div style="width:auto;position:relative;word-wrap:break-word;background-color:#fff;border-radius:0 0 .25rem .25rem;border:none;margin:0;padding-left:30px;padding-bottom:30px;"><div style="padding:8px 0 8px 15px;border-left:3px solid #3c763d;margin-left:15px;color:#313131;font-size:14px;line-height:26px;display:flex;align-items:center;"><svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31"><circle data-name="Ellipse 110" cx="15.5" cy="15.5" r="15.5" fill="#56a86a"/>
				<path d="M5.347,9.3,0,3.952,1.085,2.868,5.347,7.053,12.4,0l1.085,1.085Z" transform="translate(8.525 10.85)" fill="#fff"/>
				</svg><strong style="margin-left:15px;">'.esc_html__('  Cheers 🥳','theplus').'</strong> '.esc_html__('You have been succesfully activated.','theplus').'</div></div>';
			}else if($check=='invalid'){
				echo '<div style="width:auto;position:relative;word-wrap:break-word;background-color:#fff;border-radius:0 0 .25rem .25rem;border:none;margin:0;padding-left:30px;padding-bottom:30px;"><div style="padding:8px 0 8px 15px;border-left:3px solid #ebccd1;margin-left:15px;color:#313131;font-size:14px;line-height:26px;display:flex;align-items:center;"><svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31"><circle data-name="Ellipse 110" cx="15.5" cy="15.5" r="15.5" fill="#ff5a6e"/><g transform="translate(-34.508 4)"><g data-name="Symbol 85 – 1" transform="translate(45 6)">
				<path data-name="Union 3" d="M5.032,5.947.915,10.064,0,9.149,4.117,5.032,0,.915.915,0,5.032,4.117,9.149,0l.915.915L5.947,5.032l4.117,4.117-.915.915Z" fill="#fff"/></g></g></svg><strong style="margin-left:15px;">'.esc_html__('   Ops 🤔','theplus').'</strong> '.esc_html__('Invalid Home URL. Please review and enter again in Licence Manager.','theplus').'</div></div>';
			}else{
				echo '<div style="width:auto;position:relative;word-wrap:break-word;background-color:#fff;border-radius:0 0 .25rem .25rem;border:none;margin:0;padding-left:30px;padding-bottom:30px;"><div style="padding:8px 0 8px 15px;border-left:3px solid #ebccd1;margin-left:15px;color:#313131;font-size:14px;line-height:26px;display:flex;align-items:center;"><strong>'.esc_html__('Ops 😒','theplus').'</strong> '.esc_html__('This Licence Key is invalid. Please try again.','theplus').'</div></div>';
			}
		
		}
	
	}
}
