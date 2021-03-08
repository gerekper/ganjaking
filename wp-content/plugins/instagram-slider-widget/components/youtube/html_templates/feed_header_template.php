<?php
/* @var $account \YoutubeFeed\Api\Channel\YoutubeChannelItem */
$account = $args['account'];

$username        = $account->snippet->title;
$profile_pic_url = $account->snippet->thumbnails->default->url;
$posts_count     = $account->statistics->videoCount;
$followers       = !$account->statistics->hiddenSubscriberCount ? sprintf('%s %s',$account->statistics->subscriberCount , __('subscribers', 'yft')) : __('user has hidden the number of followers', 'yft');
$profile_url     = "https://youtube.com/channel/" . $account->snippet->channelId;
?>

<div class="wyt-feed-header">
    <div class="wyt-account-container">
        <div class="wyt-main-info" >
            <img class="wyt-round" src="<?php echo esc_url( $profile_pic_url ) ?>"
                 alt=""
                 width="90" height="90">
            <div class="" style="margin-left: 3%;width: 100%; color: grey">
                <div class="wyt-header-info-username ellipsis" style="">
                    <?php echo esc_html( $username )?>
                </div>
                <div class="wyt-header-info-followers">
                    <?php echo esc_html( $followers ) ?>
                </div>
            </div>
        </div>
        <div class="wyt-subscribe-button-container">
            <div class="wyt-subscribe-button">
                <a href="https://youtube.com/channel/<?= $account->snippet->channelId ?>" target="_blank" style=" text-decoration: none;color: white; font-size: 1rem"><?= __('subscribe', 'yft') ?></a>
            </div>
        </div>
    </div>
</div>
<br>
<hr>
