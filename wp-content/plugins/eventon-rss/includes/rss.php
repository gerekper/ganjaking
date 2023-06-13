<?php
/*
 * RSS Page
 * This page handles the event RSS feed.
 * @version 1.1.5
 */ 

$debug = false;

// Calendar events arguments
$data_args = apply_filters('evorss_feed_events_args', array(
	'hide_past'=> 'yes',
));

// add new args from url
$url_passing_args = apply_filters('evorss_url_args', array(
	'event_type','event_type_2','event_type_3','event_location','event_organizer'
));

foreach( $url_passing_args  as $A){
	if(!isset($_REQUEST[$A])) continue;
	$data_args[$A] = $_REQUEST[ $A ];
}

// data arguments
$data_args['hide_past'] = 'no';
$data_args['sort_by'] = 'sort_date';
$data_args['event_order'] = 'ASC';
$data_args['number_of_months'] = '5';

// override with settings values
	foreach( array(
		'event_order' =>'evorss_order',
		'sort_by' =>'evorss_orderby',
		'hide_past' =>'evorss_hide_past',
	) as $f=>$v){
		if(EVO()->cal->get_prop( $v,'evcal_1') ) $data_args[ $f ] =  EVO()->cal->get_prop( $v,'evcal_1');
	}

// post author
$force_post_author = EVO()->cal->check_yn('evorss_paut','evcal_1');
$force_evo_date_formatting = EVO()->cal->check_yn('evorss_evotimeformating','evcal_1');

$data_args = EVO()->calendar->process_arguments($data_args, false);

EVO()->calendar->shell->set_calendar_range($data_args);


// date
	$force_event_time = EVO()->cal->check_yn('evorss_date','evcal_1');
	$feed_date_format = apply_filters('evorss_feed_date_format', 'D, d M Y H:i:s');

header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'."\n";



// general values
	$offset = get_option('gmt_offset');
	$offset = empty($offset)? 0:$offset;

	$T = $offset;
	$offset = $offset>0? 
		'+'. (strlen($offset) == 1? '0':''). $offset:
		( strlen($offset) == 2? '-0'. ($offset*-1): $offset);

	$_offset_additional = $offset .'00';

	$rss_title = EVO()->cal->get_prop('evorss_title','evcal_1');
	$rss_title = $rss_title? $rss_title: apply_filters('evorss_blog_title', get_bloginfo_rss('name') .' - Event Feed')
?>
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php do_action('rss2_ns'); ?> >

    <?php if($debug) print_r($data_args);?>

	<channel>
		<title><?php echo $rss_title;?></title>
		<addonversion><?php echo EVORSS()->version;?></addonversion>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description'); echo $T;?></description>
        <lastBuildDate><?php echo mysql2date($feed_date_format.' '.$_offset_additional, get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
       
        <?php //do_action('rss2_head'); ?>
		
		<?php

		$events_data = EVO()->evo_generator->evo_get_wp_events_array('', $data_args);


		
		$site_url = get_bloginfo_rss('url');
		if(empty($events_data)){
			?><item>No Events</item><?php

		}
		if(!empty($events_data)):


		// FOR EACH EVENT
		foreach($events_data as $event_data):

			$start_time = $end_time = null;

			// **** fetching by posted date not pulling events in old months

			$RI = isset($event_data['event_repeat_interval'])? $event_data['event_repeat_interval']: 0;
			$event_id = $event_data['event_id'];

			$EVENT = new EVO_Event($event_id, $event_data['event_pmv'], $RI);
			$EVENT->get_event_post();


			// featured image
			$output= $imageurl = false;
			if(has_post_thumbnail($event_id)){
				$output = get_the_post_thumbnail( $event_id, 'medium', array( 'style' => 'float:right; margin:0 0 10px 10px;' ) );

				$thumb_id = get_post_thumbnail_id($event_id);
				$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'medium', true);
				$imageurl = $thumb_url_array[0];
			}
			
			$image = $output? 
				"<div style='width:100%'><img src='" . $imageurl . "' width='{$thumb_url_array[1]}' height='{$thumb_url_array[2]}'/><br/></div>"
				:null;

			// excerpt for the feed
				$excerpt = eventon_get_normal_excerpt($EVENT->content, 20);
				$excerpt = $EVENT->content;
				$description = apply_filters('evorss_before_feed_item_description', $image.$start_time.$end_time, $image, $start_time, $end_time) . ' '. $excerpt;

			// event date/times
			if(empty($event_data['event_start_unix'])) continue;
			
			// date and time
			if($force_evo_date_formatting){
				$start_time = $EVENT->get_formatted_smart_time();
			}else{
				$start_time = evo_lang('START') .': '.  date_i18n($feed_date_format, $event_data['event_start_unix']);
				$endtime = !empty($event_data['event_end_unix'])? $event_data['event_end_unix']: $event_data['event_start_unix']; 
				$end_time = ' <br/>'. evo_lang('END') .': '.date_i18n($feed_date_format, $endtime).' <br/>';
			}
			

			// pubdate
				$pubDate =  $force_event_time? 
					date_i18n($feed_date_format.' '.$_offset_additional, $event_data['event_start_unix']):
					mysql2date($feed_date_format.' '.$_offset_additional, $EVENT->post_date, false);

			// organizer
				$author = $EVENT->author;
				if(!$force_post_author){
					$event_organizer = $EVENT->get_organizer_term_id('all');
					if($event_organizer) $author = $event_organizer->term_name;
				}

		?>
			<item>
                <title><?php echo apply_filters('the_title_rss', get_the_title($event_id) ); ?></title>
                <link><?php the_permalink($event_id); ?></link>
                <pubDate><?php echo $pubDate ?></pubDate>
                <dc:creator><?php echo $author; ?></dc:creator>
                <guid isPermaLink='false'><?php echo $site_url.'/'.$event_id.'/var/ri-'.$RI ?></guid>
                <description><![CDATA[<?php echo $description ?>]]></description>
                <?php 
                // if an event image is set
                if($imageurl):
                	// convert https image urls to http because that would cause error on feed validation
                	$imageurl = str_replace('https', 'http', $imageurl);
                ?>
					<enclosure url='<?php echo $imageurl;?>' type='image/jpg' length="131842"/>
                <?php endif;?>

                <content:encoded><![CDATA[<?php echo $description ?>]]></content:encoded>
               
                <?php rss_enclosure(); ?>
                
                <?php do_action('rss2_item'); ?>
            </item>

        <?php 
        	endforeach;
        	endif;
        ?>

	</channel>
</rss>