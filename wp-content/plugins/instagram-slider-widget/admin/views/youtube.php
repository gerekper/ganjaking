<?php

use Instagram\Includes\WIS_Plugin;

/**
 * @var $accounts \YoutubeFeed\Api\Channel\YoutubeChannelItem[]
 */
$accounts       = WIS_Plugin::app()->getPopulateOption( WYT_ACCOUNT_OPTION_NAME, array() );
$count_accounts = !empty($accounts) ? count($accounts) : 0 ;
?>
<div class="factory-bootstrap-445 factory-fontawesome-000">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                    <div class="col-md-12">
                        <div class="wyt-add-form">
                            <input type="text" name="wyt_api_key" id="wyt_api_key"  class="" style="width: 550px;"
                                   value="<?= WIS_Plugin::app()->getOption( WYT_API_KEY_OPTION_NAME, '') ?>" placeholder="<?php _e( 'Youtube api key.', 'yft' ) ?>">
                        </div>
                        <div class="" style="display: inline-block;">
                            <a href="<?= admin_url();?>?page=manual-wisw" target="_blank">How to get Youtube API key</a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="wyt-add-form">
                            <input type="text" name="wyt_feed_link" id="wyt_feed_link"  class="" style="width: 550px; margin-top: 5px;"
                                   placeholder="<?php _e( 'Channel link. Example: https://www.youtube.com/channel/UC0WP5P-ufpRfjbNrmOWwLBQ', 'yft' ) ?>">
                        </div>
                        <div class="" style="display: inline-block">
                            <a href="https://support.google.com/youtube/answer/6180214" target="_blank">How to get channel link</a>
                        </div>
                        <br>

	                        <?php
	                        if ( $count_accounts >= 1 && ! WIS_Plugin::app()->is_premium() ) : ?>
                                <div class="wyt-add-form" style="margin-top: 15px !important;">
                                <span class="wyt-btn-Youtube-account wyt-btn-Youtube-account-disabled" >
                                <?php _e( 'Save', 'instagram-slider-widget' ) ?></span>
                                <span class="instagram-account-pro"><?php echo sprintf( __( "More accounts in <a href='%s'>PRO version</a>", 'instagram-slider-widget' ), WIS_Plugin::app()->get_support()->get_pricing_url( true, "wis_settings" ) ); ?></span>
	                        <?php else: ?>
                                <div class="wyt-add-form">
                                <input type="submit" class="wyt-btn-Youtube-account"
                                       value="<?php _e( 'Save', 'yft' ) ?>">
	                        <?php endif; ?>

                        </div>
                    </div>
                </form>

                <div class="col-md-12">
					<?php
					if ( !empty($accounts) ) :
						?>
                        <h3><?php _e( 'Connected channels', 'yft' ) ?></h3>
                        <div class="yt-channels">
							<?php
                            /**
                             * @var $account \YoutubeFeed\Api\Channel\YoutubeChannelItem
                             */
							foreach ( $accounts as $channelId => $account ) {
								?>
                                <a style="text-decoration: none; color: black;position: relative" target="_blank" href="https://youtube.com/channel/<?= $channelId ?>">
                                    <div class="yt-channel-container col-md-2">
                                        <div class="wyt-close-icon" data-id="<?=$channelId?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="grey" height="25" viewBox="0 0 25 25" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        </div>
                                        <div class="yt-channel-inner">
                                            <div class="yt-channel-img-container">
                                                <img class="yt-channel-img" src="<?= $account->snippet->thumbnails->high->url ?>"
                                                     width="100%" alt=""/>
                                            </div>
                                            <div class="yt-channel-title ellipsis">
                                                        <?= $account->snippet->title ?>
                                            </div>
                                            <hr>
                                            <div class="yt-channel-desc ellipsis-5-lines">
                                                <?= $account->snippet->description ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
								<?php
							}
							?>
                        </div>
						<?php wp_nonce_field( $this->plugin->getPrefix() . 'settings_form', $this->plugin->getPrefix() . 'nonce' ); ?>
					<?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div id="wyt-dashboard-widget" class="wyt-right-widget">
				<?php
				if ( ! WIS_Plugin::app()->is_premium() ) {
					WIS_Plugin::app()->get_adverts_manager()->render_placement( 'right_sidebar' );
				}
				?>
            </div>
        </div>
    </div>
</div>
