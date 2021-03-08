<?php

/* @var $args array */

/* @var $account \YoutubeFeed\Api\Channel\YoutubeChannelSnippet */
$account = $args['account'];

/* @var $videos \YoutubeFeed\Api\Video\YoutubeVideo[] */
$videos = $args['posts'];

$width = 100/$args['columns'];

$yt_link = "https://www.youtube.com/watch?v=";
?>

<div class='wyoutube-videos-container'>
    <?php foreach ($videos as $video): ?>
        <?= $args['yimages_link'] == 'yt_link' ?  sprintf('<a href="%s%s" target="_blank" style="text-decoration: none;">', $yt_link, $video->id->videoId) : ''?>
                <div class="wyoutube-video-container" data-remodal-target="<?= $video->id->videoId ?>"
                     style="margin-top: 10px; width: <?=$width-2?>%; <?= $args['yimages_link'] == 'ypopup' ? 'cursor: pointer' : ''?> ">
                    <img src="<?= $video->snippet->thumbnails->medium->url ?>" alt="">
                    <div class="wyoutuve-video-title ellipsis-2-lines">
                        <?= $video->snippet->title ?>
                    </div>
                    <div class="woutube-video-specs">
                        <div class="wyoutube-video-watches">
                            <?= sprintf("%s %s", $video->statistics->viewCount, __('views', 'yft'))?>
                        </div>
                        <div class="wyoutube-video-publish">
                            <?= time_elapsed_string($video->snippet->publishedAt) ?>
                        </div>
                    </div>
                </div>
	    <?= $args['yimages_link'] == 'yt_link' ? "</a>" : ''?>
    <?php endforeach; ?>
</div>
