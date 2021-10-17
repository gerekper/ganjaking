<?php
/*
 * RSS Page
 * This page handles the event RSS feed.
 * 
 */ 

$events = new WP_Query(
	array(
		'post_type'=>'ajde_events',
		'posts_per_page'=>-1,
		'post_status'=>'publish'
	)
);

// date
	$evorss_date = get_evoOPT('1', 'evorss_date');

header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'."\n";;
?>
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php do_action('rss2_ns'); ?>>
	<channel>
		<title><?php bloginfo_rss('name'); ?> - Event Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
		
		<?php while($events->have_posts()): $events->the_post();

			$event_meta = get_post_custom($events->post->ID);

			// featured image
			$output= false;
			if(has_post_thumbnail($events->post->ID)){
				$output = get_the_post_thumbnail( $events->post->ID, 'medium', array( 'style' => 'float:right; margin:0 0 10px 10px;' ) );
			}
			$image = $output? $output:null;

			// event date/times
			$start = 'START: '.date('D, d M Y H:i:a', $event_meta['evcal_srow'][0]);
			$end = ' <br/>END: '.date('D, d M Y H:i:a', $event_meta['evcal_erow'][0]).' <br/>';

			// pubdate
			$pubDate =  (!$evorss_date && $evorss_date=='yes')? date('D, d M Y H:i:s T', $event_meta['evcal_srow'][0]):
				mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false);

			// organizer
				$author = (!empty($event_meta['evcal_organizer']))? $event_meta['evcal_organizer'][0]: get_the_author();

		?>
			<item>
                <title><?php the_title_rss(); ?></title>
                <link><?php the_permalink_rss(); ?></link>
                <pubDate><?php echo $pubDate; ?></pubDate>
                <dc:creator><?php echo $author; ?></dc:creator>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <description><![CDATA[<?php echo $image.$start.$end; the_excerpt_rss() ?>]]></description>
                <content:encoded><![CDATA[<?php echo $start.$end; the_excerpt_rss() ?>]]></content:encoded>
                <?php rss_enclosure(); ?>
                <?php do_action('rss2_item'); ?>
            </item>
        <?php endwhile; ?>
	</channel>
</rss>