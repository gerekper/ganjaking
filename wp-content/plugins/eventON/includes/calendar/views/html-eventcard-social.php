<?php 
/**
 * EventCard Social html content
 * @4.5.9
 */

$__calendar_type = EVO()->calendar->__calendar_type;
$evo_opt = $evOPT;

$event_id = $EVENT->ID;
$repeat_interval = $EVENT->ri;
$event_post_meta = get_post_custom($event_id);

// check if social media to show or not
if( (!empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='yes' && $__calendar_type=='single') 
	|| ( empty($evo_opt['evosm_som']) ) || ( !empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='no' ) ){
	
	$post_title = get_the_title($event_id);
	$event_permalink = get_permalink($event_id);

	// Link to event
		$permalink = $EVENT->get_permalink();
		$encodeURL = EVO()->cal->check_yn('evosm_diencode','evcal_1') ? $permalink:  urlencode($permalink);

	// thumbnail
		$img_id = get_post_thumbnail_id($event_id);
		$img_src = ($img_id)? wp_get_attachment_image_src($img_id,'thumbnail'): false;

	// event details
		$summary = EVO()->frontend->filter_evo_content(get_post_field('post_content',$event_id));
	
	$summary = (!empty($summary)? urlencode(eventon_get_normal_excerpt($summary, 16)): '--');
	$imgurl = $img_src? urlencode($img_src[0]):'';
	
	
	$output_sm = EVO()->calendar->helper->get_social_share_htmls(array(
		'post_title'=> $post_title,
		'summary'=> $summary,
		'imgurl'=> $imgurl,
		'permalink'=> $permalink,
		'encodeURL'=> $encodeURL,
		'datetime_string'=> $EVENT->get_formatted_smart_time('', 'utc' )
	));

	// social share header text 4.5.9
		$SS_header = '';
		if( EVO()->cal->check_yn('eventonsm_header')){
			$SS_header = "<h3 class='evo_h3'>". evo_lang('Share this event') ."</h3>";
		}

	if(!empty($output_sm)){
		$O = $SS_header . "<div class='evo_metarow_socialmedia evcal_evdata_row '>".$output_sm."</div>";
		echo $O;
	}
}

EVO()->calendar->__calendar_type ='default';