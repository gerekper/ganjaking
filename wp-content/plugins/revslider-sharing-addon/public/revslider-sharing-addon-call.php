<?php
require_once("../../../../wp-load.php");

function sharing_get_the_excerpt($post_id){
	if(empty($current_post)) $current_post = get_post($post_id);
	$excerpt = get_the_excerpt();
	if(empty($excerpt)){
		$mycontent = wp_strip_all_tags(html_entity_decode($current_post->post_content,true));
		$excerpt = str_replace("&hellip;","",wp_trim_words( $mycontent , 25 ));
	}
	return $excerpt;
}

if(isset($_REQUEST['tpurl'])){

	$url = urldecode($_REQUEST['tpurl']);
	$share = urldecode($_REQUEST['share']);
	$slider_id = urldecode($_REQUEST['slider']);
	$source = urldecode($_REQUEST['source']);
	$post_id = $source == "post" ? url_to_postid( $url ) : $url;

	$share = str_replace(",", "&", $share);

	if($post_id){
		switch($source){
		case 'twitter':
			$rs = new RevSliderSlider();
			$rs->initByID($slider_id);
			$credentials = array(
			  'consumer_key'    => $rs->get_param('twitter-consumer-key'),
			  'consumer_secret' => $rs->get_param('twitter-consumer-secret')
			);

			$twitter_api = new RevSliderTwitterApi( $credentials , 0);

			$query = 'count=1&screen_name='.$rs->get_param('twitter-user-id').'&max_id='.$post_id;

			$tweet = $twitter_api->query( $query );
			$tweet = $tweet[0];

			$matches = null;
			$contents = preg_match_all('/\\{\\{content\\:.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches[0] as $content) {
					$content_replace = $content;
					$content_split = explode(":", $content);
					if(isset($content_split[1]) && $content_split[1]=="words"){
						$mycontent = wp_strip_all_tags(html_entity_decode($tweet->text));
						$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
					else{
						$mycontent = wp_strip_all_tags(html_entity_decode($tweet->text));
						$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
				}
			}

			$share = str_replace(
				array(
					'{{title}}',
					'{{content}}',
					'{{link}}',
					'tp_revslider_sharing_get_permalink',
					'{{date_published}}',
					'{{author_name}}',
					'{{retweet_count}}',
					'{{favorite_count}}',
					'{{image_url_large}}',
					'{{image_large}}'
				),
				array(
					urlencode($tweet->text),
					urlencode($tweet->text),
					urlencode('https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str),
					urlencode('https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str),
					urlencode(date_i18n( get_option( 'date_format' ), strtotime( $tweet->created_at ) ) ),
					urlencode($tweet->user->screen_name),
					urlencode($tweet->retweet_count),
					urlencode($tweet->favorite_count),
					isset($tweet->entities->media[0]->media_url_https) ? urlencode($tweet->entities->media[0]->media_url_https) : '',
					isset($tweet->entities->media[0]->media_url_https) ? urlencode($tweet->entities->media[0]->media_url_https) : ''
				),
				$share
			);
			break;
		case 'flickr':
			$rs = new RevSliderSlider();
			$rs->initByID($slider_id);

			$flickr_api = $rs->get_param('flickr-api-key');
			$flickr = new RevSliderFlickr($flickr_api);

			$type = $rs->get_param('flickr-type');
			$item_count = $rs->get_param('flickr-count');

			$url = 'https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key='.$flickr_api.'&photo_id='.$post_id.'&format=json&nojsoncallback=1';

			$transient_name = 'revslider_' . md5($url);

			if ( false !== ($data = get_transient( $transient_name))){
			  $photo_info = $data;
			}
			else {
				$photo_info = json_decode(wp_remote_fopen($url));
				set_transient( $transient_name, $photo_info, 86400 );
			}

			$url = 'https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key='.$flickr_api.'&photo_id='.$post_id.'&format=json&nojsoncallback=1';

			$photo_sizes = json_decode(wp_remote_fopen($url));

			$photo_sizes_array = array();

			foreach ($photo_sizes as $photo_size) {
				if(is_object($photo_size)){
					foreach ($photo_size->size as $size) {
						$photo_sizes_array[$size->label] = $size->url;
					}
				}
			}

			$matches = null;
			$contents = preg_match_all('/\\{\\{content\\:.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches as $content) {
					$content_replace = $content[0];
					$content_split = explode(":", $content[0]);
					if(isset($content_split[1]) && $content_split[1]=="words"){
						if(empty($current_post)) $current_post = get_post($post_id);
						$mycontent = wp_strip_all_tags(html_entity_decode($photo_info->photo->description->_content,true));
						$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,str_replace("}}", "", $mycontent),$share);
					}
					else{
						if(empty($current_post)) $current_post = get_post($post_id);
						$mycontent = wp_strip_all_tags(html_entity_decode($photo_info->photo->description->_content,true));
						$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,str_replace("}}", "", $mycontent),$share);
					}
				}
			}

			$share = str_replace(
				array(
					'{{title}}',
					'{{excerpt}}',
					'{{content}}',
					'{{link}}',
					'tp_revslider_sharing_get_permalink',
					'{{date}}',
					'{{author_name}}',
					'{{views}}',
					'{{image_url_original}}',
					'{{image_original}}',
					'{{image_url_large}}',
					'{{image_large}}',
					'{{image_url_large-square}}',
					'{{image_large-square}}',
					'{{image_url_medium}}',
					'{{image_medium}}',
					'{{image_url_medium-800}}',
					'{{image_medium-800}}',
					'{{image_url_medium-640}}',
					'{{image_medium-640}}',
					'{{image_url_small}}',
					'{{image_small}}',
					'{{image_url_small-320}}',
					'{{image_small-320}}',
					'{{image_url_thumbnail}}',
					'{{image_thumbnail}}',
					'{{image_url_square}}',
					'{{image_square}}',
				),
				array(
					urlencode($photo_info->photo->title->_content),
					urlencode(wp_strip_all_tags(html_entity_decode($photo_info->photo->description->_content))),
					urlencode(wp_strip_all_tags(html_entity_decode($photo_info->photo->description->_content))),
					urlencode($photo_info->photo->urls->url[0]->_content),
					urlencode($photo_info->photo->urls->url[0]->_content),
					urlencode( date(get_option('date_format'),$photo_info->photo->dates->posted)),
					urlencode($photo_info->photo->owner->realname),
					urlencode($photo_info->photo->views),
					urlencode($photo_sizes_array['Original']),
					urlencode($photo_sizes_array['Original']),
					urlencode($photo_sizes_array['Large']),
					urlencode($photo_sizes_array['Large']),
					urlencode($photo_sizes_array['Large Square']),
					urlencode($photo_sizes_array['Large Square']),
					urlencode($photo_sizes_array['Medium']),
					urlencode($photo_sizes_array['Medium']),
					urlencode($photo_sizes_array['Medium 800']),
					urlencode($photo_sizes_array['Medium 800']),
					urlencode($photo_sizes_array['Medium 640']),
					urlencode($photo_sizes_array['Medium 640']),
					urlencode($photo_sizes_array['Small']),
					urlencode($photo_sizes_array['Small']),
					urlencode($photo_sizes_array['Small 320']),
					urlencode($photo_sizes_array['Small 320']),
					urlencode($photo_sizes_array['Thumbnail']),
					urlencode($photo_sizes_array['Thumbnail']),
					urlencode($photo_sizes_array['Square']),
					urlencode($photo_sizes_array['Square']),
				),
				$share
			);
			//die($share);
			break;
		case 'vimeo':
			$rs = new RevSliderSlider();
			$rs->initByID($slider_id);

			$type = $rs->get_param('vimeo-type-source');
			$count = $rs->get_param('vimeo-count');
			$value = $rs->get_param('vimeo-username');
			$channel = $rs->get_param('vimeo-channelname');


			if($type=="user"){
				$url = "https://vimeo.com/api/v2/".$value."/videos.json";
			}
			else{
				$url = "https://vimeo.com/api/v2/".$type."/".$channel."/videos.json";
			}

			$rsp = json_decode(wp_remote_fopen($url));

			//var_dump($rsp);

			foreach($rsp as $slide){
				//var_dump($slide);
				$matches = null;
				$contents = preg_match_all('/\\{\\{content\\:.*?\\}\\}/', $share, $matches);
				if($contents){
					foreach ($matches[0] as $content) {
						$content_replace = $content;
						$content_split = explode(":", $content);
						if(isset($content_split[1]) && $content_split[1]=="words"){
							$mycontent = wp_strip_all_tags(html_entity_decode($slide->description));
							$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
							$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
						}
						else{
							$mycontent = wp_strip_all_tags(html_entity_decode($slide->description));
							$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
							$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
						}
					}
				}
				if($slide->id == $post_id){
					$share = str_replace(
						array(
							'{{title}}',
							'{{excerpt}}',
							'{{content}}',
							'{{link}}',
							'tp_revslider_sharing_get_permalink',
							'{{date_published}}',
							'{{author_name}}',
							'{{image_url_thumbnail_small}}',
							'{{image_thumbnail_small}}',
							'{{image_url_thumbnail_medium}}',
							'{{image_thumbnail_medium}}',
							'{{image_url_thumbnail_large}}',
							'{{image_thumbnail_large}}',
						),
						array(
							urlencode($slide->title),
							urlencode(wp_strip_all_tags(html_entity_decode($slide->description))),
							urlencode(wp_strip_all_tags(html_entity_decode($slide->description))),
							urlencode($slide->url),
							urlencode($slide->url),
							urlencode( date(get_option('date_format'),strtotime($slide->upload_date) ) ),
							urlencode($slide->user_name),
							urlencode($slide->thumbnail_small),
							urlencode($slide->thumbnail_small),
							urlencode($slide->thumbnail_medium),
							urlencode($slide->thumbnail_medium),
							urlencode($slide->thumbnail_large),
							urlencode($slide->thumbnail_large),
						),
						$share
					);
					//die($share);
					break;
				}
			}
			break;
		case 'youtube':
			$rs = new RevSliderSlider();
			$rs->initByID($slider_id);
			$url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id='.$post_id.'&key='.$rs->get_param('youtube-api');
			$rsp = json_decode(wp_remote_fopen($url));

			$matches = null;
			$contents = preg_match_all('/\\{\\{content\\:.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches[0] as $content) {
					$content_replace = $content;
					$content_split = explode(":", $content);
					if(isset($content_split[1]) && $content_split[1]=="words"){
						$mycontent = wp_strip_all_tags(html_entity_decode($rsp->items[0]->snippet->description));
						$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
					else{
						$mycontent = wp_strip_all_tags(html_entity_decode($rsp->items[0]->snippet->description));
						$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
				}
			}

			$share = str_replace(
				array(
					'{{title}}',
					'{{excerpt}}',
					'{{content}}',
					'{{date_published}}',
					'{{link}}',
					'tp_revslider_sharing_get_permalink',
					'{{image_url_default}}',
					'{{image_default}}',
					'{{image_url_medium}}',
					'{{image_medium}}',
					'{{image_url_high}}',
					'{{image_high}}',
					'{{image_url_standard}}',
					'{{image_standard}}',
					'{{image_url_maxres}}',
					'{{image_maxres}}',
				),
				array(
					urlencode($rsp->items[0]->snippet->title),
					urlencode(html_entity_decode($rsp->items[0]->snippet->description)),
					urlencode(html_entity_decode($rsp->items[0]->snippet->description)),
					urlencode( date(get_option('date_format'),strtotime($rsp->items[0]->snippet->publishedAt) ) ),
					urlencode('https://www.youtube.com/watch?v='.$post_id),
					urlencode('https://www.youtube.com/watch?v='.$post_id),
					urlencode($rsp->items[0]->snippet->thumbnails->default->url),
					urlencode($rsp->items[0]->snippet->thumbnails->default->url),
					urlencode($rsp->items[0]->snippet->thumbnails->medium->url),
					urlencode($rsp->items[0]->snippet->thumbnails->medium->url),
					urlencode($rsp->items[0]->snippet->thumbnails->high->url),
					urlencode($rsp->items[0]->snippet->thumbnails->high->url),
					urlencode($rsp->items[0]->snippet->thumbnails->high->url),
					urlencode($rsp->items[0]->snippet->thumbnails->high->url),
					urlencode($rsp->items[0]->snippet->thumbnails->high->url),
					urlencode($rsp->items[0]->snippet->thumbnails->high->url)
				),
				$share
			);
			//die($share);
			break;
		case 'facebook':
			$rs = new RevSliderSlider();
			$rs->initByID($slider_id);
			$oauth = wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$rs->get_param('facebook-app-id')."&client_secret=".$rs->get_param('facebook-app-secret'));
			$url = "https://graph.facebook.com/".$post_id."/?".$oauth."&fields=name,link,created_time,updated_time,from,likes.limit(1).summary(true),picture,attachments";
			$photo_info = json_decode(wp_remote_fopen($url));

			$matches = null;
			$contents = preg_match_all('/\\{\\{content\\:.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches[0] as $content) {
					$content_replace = $content;
					$content_split = explode(":", $content);
					if(isset($content_split[1]) && $content_split[1]=="words"){
						$mycontent = wp_strip_all_tags(html_entity_decode($photo_info->attachments->data[0]->description));
						$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
					else{
						$mycontent = wp_strip_all_tags(html_entity_decode($photo_info->attachments->data[0]->description));
						$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
				}
			}

			$share = str_replace(
				array(
					'{{author_name}}',
					'{{title}}',
					'{{content}}',
					'{{link}}',
					'{{date_published}}',
					'{{date_modified}}',
					'{{likes}}',
					'tp_revslider_sharing_get_permalink',
					'{{image_url_full}}',
					'{{image_full}}',
					'{{image_url_thumbnail}}',
					'{{image_thumbnail}}'
				),
				array(
					urlencode($photo_info->from->name),
					urlencode(html_entity_decode($photo_info->name)),
					urlencode(html_entity_decode($photo_info->attachments->data[0]->description)),
					urlencode($photo_info->link),
					urlencode( date(get_option('date_format'),strtotime($photo_info->created_time) ) ),
					urlencode( date(get_option('date_format'),strtotime($photo_info->updated_time) ) ),
					$photo_info->likes->summary->total_count,
					urlencode($photo_info->link),
					isset($photo_info->attachments->data[0]->media->image->src) ? urlencode($photo_info->attachments->data[0]->media->image->src) : '',
					isset($photo_info->attachments->data[0]->media->image->src) ? urlencode($photo_info->attachments->data[0]->media->image->src) : '',
					isset($photo_info->picture) ? urlencode($photo_info->picture) : '',
					isset($photo_info->picture) ? urlencode($photo_info->picture) : '',
				),
				$share
			);
			break;
		case 'instagram':
			$sharelink = urlencode('https://instagram.com/p/'.Revslider_Sharing_Addon_Public::mediaid_to_shortcode($post_id));

			$max_id_url = 'https://api.instagram.com/oembed/?url='.$sharelink;
			$max_id_rsp = json_decode(wp_remote_fopen($max_id_url));

			//var_dump($post_id);

			$matches = null;
			$contents = preg_match_all('/\\{\\{content\\:.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches[0] as $content) {
					$content_replace = $content;
					$content_split = explode(":", $content);
					if(isset($content_split[1]) && $content_split[1]=="words"){
						$mycontent = wp_strip_all_tags(html_entity_decode($max_id_rsp->title));
						$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
					else{
						$mycontent = wp_strip_all_tags(html_entity_decode($max_id_rsp->title));
						$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,urlencode(str_replace("}}", "", html_entity_decode($mycontent))),$share);
					}
				}
			}

			$share = str_replace(
				array(
					'{{author_name}}',
					'{{title}}',
					'{{content}}',
					'{{link}}',
					'{{date}}',
					'tp_revslider_sharing_get_permalink',
					'{{likes}}',
					'{{image_url_standard_resolution}}',
					'{{image_standard_resolution}}',
					'{{image_url_low_resolution}}',
					'{{image_low_resolution}}',
					'{{image_url_thumbnail}}',
					'{{image_thumbnail}}'
				),
				array(
					urlencode($max_id_rsp->author_name),
					urlencode($max_id_rsp->title),
					urlencode($max_id_rsp->title),
					$sharelink,
					urlencode($max_id_rsp->thumbnail_url),
					$sharelink,
					urlencode($max_id_rsp->thumbnail_url),
					urlencode($max_id_rsp->thumbnail_url),
					urlencode($max_id_rsp->thumbnail_url),
					urlencode($max_id_rsp->thumbnail_url),
					urlencode($max_id_rsp->thumbnail_url),
					urlencode($max_id_rsp->thumbnail_url)
				),
				$share
			);
			// return($share);
			break;
		case 'post':
		case 'posts':
		case 'gallery':
			$sharelink = is_numeric($url) ? get_permalink( $url ) : $url;

			$author_display_name = "";
			if(strpos($share, "author_name") !== false){
				$current_post = get_post($post_id);
				$author_id= $current_post->post_author;
				$author_display_name = get_the_author_meta("display_name",$author_id);
			}

			if(strpos($share, "num_comments")  !== false){
				if(empty($current_post)) $current_post = get_post($post_id);
				$share = str_replace("{{num_comments}}",$current_post->comment_count,$share);
			}

			if(strpos($share, "taglist") !== false){
				$posttags = get_the_tags($post_id);
				$posttags_array = array();
				if ($posttags) {
				  foreach($posttags as $tag) {
					$posttags_array[] = $tag->name;
				  }
				  $posttags = implode(",", $posttags_array);
				}
				$share = str_replace("{{taglist}}",$posttags,$share);
			}

			$matches = null;
			$contents = preg_match_all('/\\{\\{featured\\_image\\_.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches as $content) {
					$size = str_replace(array("featured_image_","url_","{{","}}"), array("","","",""), $content);

					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size[0] );

					$share = str_replace($content,$image,$share);
				}
			}

			$matches = null;
			$contents = preg_match_all('/\\{\\{content.*?\\}\\}/', $share, $matches);
			if($contents){
				foreach ($matches as $content) {
					$content_replace = $content[0];
					$content_split = explode(":", $content[0]);
					if(isset($content_split[1]) && $content_split[1]=="words"){
						if(empty($current_post)) $current_post = get_post($post_id);
						$mycontent = wp_strip_all_tags(html_entity_decode($current_post->post_content,true));
						$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,str_replace("}}", "", $mycontent),$share);
					}
					else{
						if(empty($current_post)) $current_post = get_post($post_id);
						$mycontent = wp_strip_all_tags(html_entity_decode($current_post->post_content,true));
						$mycontent = substr($mycontent , 0, str_replace("}}", "", $content_split[2]));
						$share = str_replace($content_replace,str_replace("}}", "", $mycontent),$share);
					}
				}
			}

			$matches = null;
			preg_match_all('/\\{\\{meta:.*?\\}\\}/', $share, $matches);
			if($matches){
				$matches = $matches[0];
				foreach($matches as $match){
					$meta = str_replace('{{meta:', '', $match);
					$meta = str_replace('}}', '',$meta);
					$meta = str_replace('_REVSLIDER_', '-', $meta);
					$meta_val = get_post_meta($post_id, $meta, true);
					$share = str_replace($match, $meta_val, $share);
				}
			}
			
			$share = str_replace(
				array(
					'{{title}}',
					"{{excerpt}}",
					"tp_revslider_sharing_get_permalink",
					"{{link}}",
					"{{author_name}}",
					"{{date}}",
					"{{alias}}",
					"{{date_modified}}",
					"{{catlist}}",
					"{{catlist_raw}}"
				),
				array(
					urlencode(html_entity_decode(get_the_title($post_id))),
					urlencode(sharing_get_the_excerpt($post_id)),
					urlencode($sharelink),
					urlencode($sharelink),
					urlencode($author_display_name),
					urlencode(get_the_date(get_option('date_format'), $post_id  ) ),
					urlencode(get_post_field( 'post_name', get_post($post_id) ) ),
					urlencode(get_the_time( get_option('date_format'), $post_id ) ),
					urlencode(wp_strip_all_tags(get_the_category_list( ',', '', $post_id ))),
					urlencode(wp_strip_all_tags(get_the_category_list( ',', '', $post_id )))
				),
				$share
			);

			// Check for Woo
			if(function_exists('get_product')){
				$product = get_product($post_id);
				if($product !== false){
					$wc_full_price = $product->get_price_html();
					$wc_price = wc_price($product->get_price());
					$wc_price_no_cur = $product->get_price();
					$wc_stock = $product->get_total_stock();
					$wc_rating = $product->get_rating_html();
					$wc_star_rating = '<div class="rs-starring">';
					preg_match_all('#<strong class="rating">.*?</span>#', $wc_rating, $match);
					if(!empty($match) && isset($match[0]) && isset($match[0][0])){
						$wc_star_rating .= str_replace($match[0][0], '', $wc_rating);
					}
					$wc_star_rating .= '</div>';
					$wc_categories = $product->get_categories(',');
					$wc_add_to_cart = $product->add_to_cart_url();
					$wc_add_to_cart_button = '';


					$wc_sku = $product->get_sku();
					$wc_stock_quantity = $product->get_stock_quantity();
					$wc_rating_count = $product->get_rating_count();
					$wc_review_count = $product->get_review_count();
					$wc_tags = $product->get_tags();

					$share = str_replace(
						array(
							'{{wc_sku}}',
							'{{wc_full_price}}',
							'{{wc_price}}',
							'{{wc_price_no_cur}}',
							'{{wc_stock}}',
							'{{wc_stock_quantity}}',
							'{{wc_rating_count}}',
							'{{wc_review_count}}',
							'{{wc_rating}}',
							'{{wc_star_rating}}',
							'{{wc_categories}}',
							'{{wc_tags}}',
							'{{wc_add_to_cart}}',
							'{{wc_add_to_cart_button}}',
						),
						array(
							urlencode(wp_strip_all_tags(html_entity_decode($wc_sku))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_full_price))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_price))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_price_no_cur))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_stock))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_stock_quantity))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_rating_count))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_review_count))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_rating))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_star_rating))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_categories))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_tags))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_add_to_cart))),
							urlencode(wp_strip_all_tags(html_entity_decode($wc_add_to_cart))),
						),
						$share
					);
				}
			}
			break;
			default:
				break;
		}
	}
	if(!empty($share)){
		header("Location: $share");
    exit;
	}
}
?>