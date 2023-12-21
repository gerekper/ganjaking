<?php
/**
 * Dashboard home tab template
 */

defined( 'ABSPATH' ) || die();
?>
<div class="ha-dashboard-panel">
	<?php if ( file_exists(HAPPY_ADDONS_DIR_PATH.'assets/imgs/admin/promo_banner.jpg') && ! ha_has_pro() ) : ?>
    <div class="ha-home-banner" style="<?php echo 'background-image: url('.HAPPY_ADDONS_ASSETS.'imgs/admin/promo_banner.jpg)'; ?>">
        <div class="ha-home-banner__content">
			<style>
				.ha-home-banner-promo-button {
					margin-left: auto;
					color: #292D2B;
					background: #FF931F;
					font-size: 24px;
					font-weight: 800;
					padding: 15px 20px;
					border-radius: 5px;
					text-decoration: none;
				}
				.ha-home-banner-promo-button:hover {
					color: #292D2B;
					background: #FFFFFF;
				}
			</style>
			<a class="ha-home-banner-promo-button" target="_blank" href="https://happyaddons.com/pricing/">Claim Yours</a>
        </div>
    </div>
	<?php else: ?>
    <div class="ha-home-banner">
        <div class="ha-home-banner__content">
            <img class="ha-home-banner__logo" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/halogo.svg" alt="">
            <span class="ha-home-banner__divider"></span>
            <h2><?php esc_html_e('Thanks a lot ', 'happy-elementor-addons'); ?><br><span><?php esc_html_e('for choosing HappyAddons', 'happy-elementor-addons'); ?></span></h2>
        </div>
    </div>
	<?php endif; ?>

    <div class="ha-home-body">
        <div class="ha-row ha-py-5 ha-align-items-center">
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/knowledge.svg" alt="">
                <h3 class="ha-feature-title"><?php esc_html_e('Knowledge & Wiki', 'happy-elementor-addons'); ?></h3>
                <p class="f18"><?php esc_html_e('We have created full-proof documentation for you. It will help you to understand how our plugin works.', 'happy-elementor-addons'); ?></p>
                <a class="ha-btn ha-btn-primary" target="_blank" rel="noopener" href="https://happyaddons.com/go/docs"><?php esc_html_e('Take Me to The Knowledge Page', 'happy-elementor-addons'); ?></a>
            </div>
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/art1.png" alt="">
            </div>
        </div>
        <div class="ha-row ha-py-5 ha-pt-0">
            <div class="ha-col ha-col-12">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/film.svg" alt="">
                <h3 class="ha-feature-title"><?php esc_html_e('Video Tutorial', 'happy-elementor-addons'); ?></h3>
                <p class="f16"><?php esc_html_e('How to use Floating Effects and manage CSS Transform?', 'happy-elementor-addons'); ?></p>
            </div>
            <div class="ha-col ha-col-4">
                <a href="https://www.youtube.com/watch?v=KSRaUaD30Jc" class="ha-feature-sub-title-a">
                    <img class="ha-img-fluid ha-rounded" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/crossdomain-video-cover.jpg" alt="">
                    <h4 class="ha-feature-sub-title"><?php esc_html_e('Cross Domain Copy Paste (Pro)', 'happy-elementor-addons'); ?></h4>
                </a>
            </div>
            <div class="ha-col ha-col-4">
                <a href="https://www.youtube.com/watch?v=LmtacsLcFPU" class="ha-feature-sub-title-a">
                    <img class="ha-img-fluid ha-rounded" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/translate-video-cover.jpg" alt="">
                    <h4 class="ha-feature-sub-title"><?php esc_html_e('Happy Effects - CSS Transform', 'happy-elementor-addons'); ?></h4>
                </a>
            </div>
            <div class="ha-col ha-col-4">
                <a href="https://www.youtube.com/watch?v=F33g3zqkeog" class="ha-feature-sub-title-a">
                    <img class="ha-img-fluid ha-rounded" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/floating-video-cover.jpg" alt="">
                    <h4 class="ha-feature-sub-title"><?php esc_html_e('Happy Effects - Floating Effects', 'happy-elementor-addons'); ?></h4>
                </a>
            </div>
            <div class="ha-col ha-col-12 ha-align-center ha-pt-2">
                <a class="ha-btn ha-btn-secondary" target="_blank" rel="noopener" href="https://www.youtube.com/channel/UC1-e7ewkKB1Dao1U90QFQFA"><?php esc_html_e('View more videos', 'happy-elementor-addons'); ?></a>
            </div>
        </div>
        <div class="ha-row ha-align-items-end ha-py-5 ha-pt-0">
            <div class="ha-col ha-col-9">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/faq.svg" alt="">
                <h3 class="ha-feature-title ha-text-primary"><?php esc_html_e('FAQ', 'happy-elementor-addons'); ?></h3>
                <p class="f16 ha-mb-0"><?php esc_html_e('Frequently Asked Questions', 'happy-elementor-addons'); ?></p>
            </div>
            <div class="ha-col ha-col-3 ha-align-right">
                <a class="btn-more" target="_blank" rel="noopener" href="https://happyaddons.com/go/faq"><?php esc_html_e('Get More FAQ >', 'happy-elementor-addons'); ?></a>
            </div>
            <div class="ha-col ha-col-12">
                <div class="ha-row">
                    <div class="ha-col ha-col-6 ha-pt-3">
                        <h4 class="f18"><?php esc_html_e('Can I use these addons in my client project?', 'happy-elementor-addons'); ?></h4>
                        <p class="ha-mb-0 f16"><?php esc_html_e('Yes, absolutely, no holds barred. Use it to bring colorful moments to your customers. And don’t forget to check out our premium features.', 'happy-elementor-addons'); ?></p>
                    </div>
                    <div class="ha-col ha-col-6 ha-pt-3">
                        <h4 class="f18"><?php esc_html_e('Is there any support policy available for the free users?', 'happy-elementor-addons'); ?></h4>
                        <p class="ha-mb-0 f16"><?php esc_html_e('Free or pro version, both comes with excellent support from us. However, pro users will get priority support.', 'happy-elementor-addons'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $appsero = \Happy_Addons\Elementor\Base::instance()->appsero;
        $margin_top = '';
        if ( $appsero ) :
            if ( ! $appsero->insights()->notice_dismissed() && ! $appsero->insights()->tracking_allowed() ) :
                $optin_url  = add_query_arg( $appsero->slug . '_tracker_optin', 'true' );
                $margin_top = 'ha-py-5';
                ?>
                <div class="ha-row">
                    <div class="ha-col ha-col-12">
                        <div class="ha-cta ha-rounded">
                        <div class="ha-row ha-align-items-center">
                            <div class="ha-col-8">
                                <h3 class="ha-feature-title"><?php esc_html_e('Call for Contributors', 'happy-elementor-addons'); ?></h3>
                                <p class="f16"><?php esc_html_e('Are you interested to contribute to making this plugin more awesome?', 'happy-elementor-addons'); ?></p>
                                <a class="link btn-how-to-contribute" href="#"><?php esc_html_e('How am I going to contribute?', 'happy-elementor-addons'); ?></a>
                                <p class="ha-mb-0" style="display: none;"><?php esc_html_e('By allowing Happy Elementor Addons to collect non-sensitive diagnostic data and usage information so that we can make sure optimum compatibility.
                                    Happy Elementor Addons collect - Server environment details (php, mysql, server, WordPress versions), Number of users in your site, Site language, Number of active and inactive plugins, Site name and url, Your name and email address. We are using Appsero to collect your data. ', 'happy-elementor-addons'); ?><a href="https://appsero.com/privacy-policy/" target="_blank" style="color:#fff"><?php esc_html_e('Learn more', 'happy-elementor-addons'); ?></a><?php esc_html_e(' about how Appsero collects and handle your data.', 'happy-elementor-addons'); ?></p>
                            </div>
                            <div class="ha-cta-action ha-col-4 ha-align-right">
                                <a class="btn-contribute" href="<?php echo esc_url( $optin_url ); ?>"><?php esc_html_e('I like to contribute', 'happy-elementor-addons'); ?></a>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <?php
            endif;
        endif;
        ?>

        <div class="ha-row <?php echo $margin_top; ?>">
            <div class="ha-col ha-col-6">
                <div class="ha-border-box ha-min-height-455">
                    <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/support-call.svg" alt="">
                    <h3 class="ha-feature-title ha-text-secondary"><?php esc_html_e('Support And Feedback', 'happy-elementor-addons'); ?></h3>
                    <p class="f16"><?php esc_html_e('Feeling like to consult with an expert? Take live Chat support immediately from ', 'happy-elementor-addons'); ?><a href="https://happyaddons.com/" target="_blank" rel="noopener"><?php esc_html_e('HappyAddons', 'happy-elementor-addons'); ?></a><?php esc_html_e('. We are always ready to help you 24/7.', 'happy-elementor-addons'); ?></p>
                    <p class="f16 ha-mb-2"><strong><?php esc_html_e('Or if you’re facing technical issues with our plugin, then please create a support ticket', 'happy-elementor-addons'); ?></strong></p>
                    <a class="ha-btn ha-btn-secondary" target="_blank" rel="noopener" href="https://happyaddons.com/go/contact-support"><?php esc_html_e('Get Support', 'happy-elementor-addons'); ?></a>
                </div>
            </div>
            <div class="ha-col ha-col-6">
                <div class="ha-border-box ha-min-height-455">
                    <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/newspaper.svg" alt="">
                    <h3 class="ha-feature-title ha-text-primary"><?php esc_html_e('Newsletter Subscription', 'happy-elementor-addons'); ?></h3>
                    <p class="f16"><?php esc_html_e('To get updated news, current offers, deals, and tips please subscribe to our Newsletters.', 'happy-elementor-addons'); ?></p>
                    <a class="ha-btn ha-btn-primary" target="_blank" rel="noopener" href="https://happyaddons.com/go/subscribe"><?php esc_html_e('Subscribe Now', 'happy-elementor-addons'); ?></a>
                </div>
            </div>
        </div>

        <div class="ha-row ha-py-5 ha-align-items-center">
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/art2.png" alt="">
            </div>
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/cross-game.svg" alt="">
                <h3 class="ha-feature-title"><?php esc_html_e('Missing Any Feature?', 'happy-elementor-addons'); ?></h3>
                <p class="f16"><?php esc_html_e('Are you in need of a feature that’s not available in our plugin? Feel free to do a
                    feature request from here,', 'happy-elementor-addons'); ?></p>
                <a class="ha-btn ha-btn-primary" target="_blank" rel="noopener" href="https://happyaddons.com/roadmaps/#ideas"><?php esc_html_e('Request Feature', 'happy-elementor-addons'); ?></a>
            </div>
        </div>

        <div class="ha-row ha-py-5">
            <div class="ha-col ha-col-12">
                <div class="ha-border-box">
                    <div class="ha-row ha-align-items-center">
                        <div class="ha-col ha-col-3" >
                            <img class="ha-img-fluid ha-pr-2" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/c-icon.png" alt="">
                        </div>
                        <div class="ha-col ha-col-8">
                            <h3 class="ha-feature-title ha-text-secondary ha-mt-0"><?php esc_html_e('Happy with Our Work?', 'happy-elementor-addons'); ?></h3>
                            <p class="f16 ha-mb-2"><?php esc_html_e('We are really thankful to you that you have chosen our plugin. If our plugin brings a smile in your face while working, please share your happiness by giving us a 5***** rating in WordPress Org. It will make us happy and won’t take more than 2 mins.', 'happy-elementor-addons'); ?></p>
                            <a class="ha-btn ha-btn-secondary" target="_blank" rel="noopener" href="https://wordpress.org/support/plugin/happy-elementor-addons/reviews/?filter=5"><?php esc_html_e('I’m Happy to Give You 5*', 'happy-elementor-addons'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
