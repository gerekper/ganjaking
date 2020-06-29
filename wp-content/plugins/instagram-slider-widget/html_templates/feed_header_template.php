<?php
/** @var array $args account data */
/** @var string $username account username */
/** @var string $profile_pic_url URL of account profile picture */
/** @var int $posts_count count of account posts */
/** @var int $followers count of account followers */
/** @var string $profile_url ULR of account */

$username        = isset($args['username']) ?  $args['username'] : '';
$profile_pic_url = $args['profile_picture_url'];
$posts_count     = $args['media_count'];
$followers       = $args['followers_count'];
$profile_url     = "https://www.instagram.com/$username/";
?>

<div class="wis-feed-header">
    <a href="<?php echo esc_url($profile_url)?>" target="_blank" style="text-decoration: none;border: 0 !important;">
        <div class="wis-box">
            <div class="wis-header-img">
                <div class="wis-round wis-header-neg">
                    <i class="wis-header-neg-icon"></i>
                </div>
                <img class="wis-round" style="position: relative" src="<?php echo esc_url( $profile_pic_url ) ?>" alt=""
                     width="50" height="50">
            </div>
            <div class="wis-header-info">
                <p class="wis-header-info-username"><?php echo esc_html( $username )?></p>
                <p style="margin-top: 0; font-size: 11px">
                    <span class="fa fa-image">&nbsp;<?php echo esc_html( $posts_count ) ?></span>&nbsp;&nbsp;
                    <span class="fa fa-user">&nbsp;<?php echo esc_html( $followers ) ?></span>
                </p>
            </div>
        </div>
    </a>
</div>
<br>
