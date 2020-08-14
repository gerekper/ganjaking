<style>
    .section{
        margin-left: -20px;
        margin-right: -20px;
        font-family: "Raleway",san-serif;
    }
    .section h1{
        text-align: center;
        text-transform: uppercase;
        color: #808a97;
        font-size: 35px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0 0;
    }
    .section ul{
        list-style-type: disc;
        padding-left: 15px;
    }
    .section:nth-child(even){
        background-color: #fff;
    }
    .section:nth-child(odd){
        background-color: #f1f1f1;
    }
    .section .section-title img{
        display: table-cell;
        vertical-align: middle;
        width: auto;
        margin-right: 15px;
    }
    .section h2,
    .section h3 {
        display: inline-block;
        vertical-align: middle;
        padding: 0;
        font-size: 24px;
        font-weight: 700;
        color: #808a97;
        text-transform: uppercase;
    }

    .section .section-title h2{
        display: table-cell;
        vertical-align: middle;
        line-height: 25px;
    }

    .section-title{
        display: table;
    }

    .section h3 {
        font-size: 14px;
        line-height: 28px;
        margin-bottom: 0;
        display: block;
    }

    .section p{
        font-size: 13px;
        margin: 15px 0;
    }
    .section ul li{
        margin-bottom: 4px;
    }
    .landing-container{
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }
    .landing-container:after{
        display: block;
        clear: both;
        content: '';
    }
    .landing-container .col-1,
    .landing-container .col-2{
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }
    .landing-container .col-1 img{
        width: 100%;
    }
    .landing-container .col-1{
        width: 55%;
    }
    .landing-container .col-2{
        width: 45%;
    }
    .premium-cta{
        background-color: #808a97;
        color: #fff;
        border-radius: 6px;
        padding: 20px 15px;
    }
    .premium-cta:after{
        content: '';
        display: block;
        clear: both;
    }
    .premium-cta p{
        margin: 7px 0;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }
    .premium-cta a.button{
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url(<?php echo YITH_WCDN_ASSETS_URL ?>images/upgrade.png) #ff643f no-repeat 13px 13px;
        border-color: #ff643f;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 9px 50px 9px 70px;
    }
    .premium-cta a.button:hover,
    .premium-cta a.button:active,
    .premium-cta a.button:focus{
        color: #fff;
        background: url(<?php echo YITH_WCDN_ASSETS_URL ?>images/upgrade.png) #971d00 no-repeat 13px 13px;
        border-color: #971d00;
        box-shadow: none;
        outline: none;
    }
    .premium-cta a.button:focus{
        top: 1px;
    }
    .premium-cta a.button span{
        line-height: 13px;
    }
    .premium-cta a.button .highlight{
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }
    .premium-cta .highlight{
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

    .section.one{
        background: url(<?php echo YITH_WCDN_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
    }
    .section.two{
        background: url(<?php echo YITH_WCDN_ASSETS_URL ?>/images/02-bg.png) no-repeat #fff; background-position: 15% 100%;
    }
    .section.three{
        background: url(<?php echo YITH_WCDN_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
    }
    .section.four{
        background: url(<?php echo YITH_WCDN_ASSETS_URL ?>/images/04-bg.png) no-repeat #fff; background-position: 15% 100%;
    }


    @media (max-width: 768px) {
        .section{margin: 0}
        .premium-cta p{
            width: 100%;
        }
        .premium-cta{
            text-align: center;
        }
        .premium-cta a.button{
            float: none;
        }
    }

    @media (max-width: 480px){
        .wrap{
            margin-right: 0;
        }
        .section{
            margin: 0;
        }
        .landing-container .col-1,
        .landing-container .col-2{
            width: 100%;
            padding: 0 15px;
        }
        .section-odd .col-1 {
            float: left;
            margin-right: -100%;
        }
        .section-odd .col-2 {
            float: right;
            margin-top: 65%;
        }
    }

    @media (max-width: 320px){
        .premium-cta a.button{
            padding: 9px 20px 9px 70px;
        }

        .section .section-title img{
            display: none;
        }
    }
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH Desktop Notifications for WooCommerce%2$s to benefit from all features!','yith-desktop-notifications-for-woocommerce'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-desktop-notifications-for-woocommerce');?></span>
                    <span><?php _e('to the premium version','yith-desktop-notifications-for-woocommerce');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-desktop-notifications-for-woocommerce');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/01.png" alt="Feature 01" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('5 notification types','yith-desktop-notifications-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php _e('Have you noticed how useful can be a notification system that shows the actions related to your shop on your desktop?', 'yith-desktop-notifications-for-woocommerce');?>
                </p>

                <p>
                    <?php echo sprintf(__('%1$sWhy limiting it to notify you only about new orders?%2$s', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>');?>
                </p>
                <p>
                    <?php echo sprintf(__('With the premium version of the plugin, you can enable the notifications also for the following actions:', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>');?>
                </p>
                <ul>
                    <li>
                        <?php _e('the order was refunded','yith-desktop-notifications-for-woocommerce') ?>
                    </li>
                    <li>
                        <?php _e('the order changed its status','yith-desktop-notifications-for-woocommerce') ?>
                    </li>
                    <li>
                        <?php _e('a specific product of the shop was sold','yith-desktop-notifications-for-woocommerce') ?>
                    </li>
                    <li>
                        <?php _e('the product turned into “out of stock” after the last sale','yith-desktop-notifications-for-woocommerce') ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/02-icon.png" alt="icon 02" />
                    <h2><?php _e('Custom notifications','yith-desktop-notifications-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('You can edit the body of each notification as you prefer by inserting %1$stitle%2$s and %1$sdescription%2$s to suit better your notices. .', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>','<br>');?>
                </p>
                <p>
                    <?php echo sprintf(__('And that’s not all! By associating an %1$sicon%2$s and a %1$ssound%2$s to the messages when they are shown, they will stand out even more.  ', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/02.png" alt="feature 02" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/03.png" alt="Feature 03" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'User roles','yith-desktop-notifications-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Who are the recipients of the notification you just configured? It’s up to you!%3$s The premium version of the plugin allows %1$snotifying%2$s only those users with a specific %1$srole%2$s in your shop. An essential feature to use the plugin at its best.', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>', '<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/04-icon.png" alt="icon 04" />
                    <h2><?php _e('Notification duration','yith-desktop-notifications-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('For %1$show long do you want to show%2$s the notification?', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>');?>
                </p>
                <p>
                    <?php echo sprintf(__('There are two options: remove it only when it was selected or hide it automatically at the end of the configured time span.', 'yith-desktop-notifications-for-woocommerce'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCDN_URL . '/assets' ?>/images/04.png" alt="Feature 04" />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH Desktop Notifications for WooCommerce%2$s to benefit from all features!','yith-desktop-notifications-for-woocommerce'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-desktop-notifications-for-woocommerce');?></span>
                    <span><?php _e('to the premium version','yith-desktop-notifications-for-woocommerce');?></span>
                </a>
            </div>
        </div>
    </div>
</div>