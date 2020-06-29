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
        margin: 25px 0;
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
        font-size: 15px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }
    .premium-cta a.button{
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url('<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/upgrade.png') #ff643f no-repeat 13px 13px;
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
        background: url(<?php echo YITH_WAPO_ASSETS_URL?>/img/tab-premium/upgrade.png) #971d00 no-repeat 13px 13px;
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
                    Upgrade to the <span class="highlight">premium version</span>
                    of <span class="highlight">YITH Woocommerce Product Add-Ons</span> to benefit from all features!
                </p>
                <a href="<?php echo $this->get_premium_landing_uri(); ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight">UPGRADE</span>
                    <span>to the premium version</span>
                </a>
            </div>

        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/01-bg.png) no-repeat #fff; background-position: 85% 75%">
        <h1>Premium Features</h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/01.png" alt="Socials icon" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/01-icon.png" alt="Socials"/>
                    <h2><?php _e('More option typologies','yith-woocommerce-product-add-ons') ?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Select, upload, color and date are only some of options typologies that you will find in the premium version of the plugin. Adding new options to the products of your shop will be %1$seasy%2$s and users could select the ones they need during their purchase','yith-woocommerce-product-add-ons'),'<b>','</b>' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/02-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/02-icon.png" alt="screen" />
                    <h2><?php _e('Product price','yith-woocommerce-product-add-ons'); ?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Assign a price to each option to update the %1$sproduct total%2$s amount automatically, basing on the add-ons selected by the user.','yith-woocommerce-product-add-ons'),'<b>','</b>' ); ?>
                </p>
                <p>
                    <?php echo sprintf( __('But that’s not all! The greatest news is the possibility to calculate the %1$spercentage price%2$s of the additional element in respect to the original product price.','yith-woocommerce-product-add-ons'),'<b>','</b>' ); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/02.png" alt="icon" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/03-bg.png) no-repeat #fff; background-position: 85% 100%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/03.png" alt="Screenshot" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/03-icon.png" alt="icon" />
                    <h2><?php _e('Option details','yith-woocommerce-product-add-ons') ?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('%1$sTitle, description%2$s and %1$simage%2$s: specify these elements for all your options so that users can make the best choice for their purchase.','yith-woocommerce-product-add-ons'),'<b>','</b>' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/04-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/04-icon.png" alt="Icon" />
                    <h2><?php _e('Advanced attributes','yith-woocommerce-product-add-ons') ?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Enhance your shop by creating variations with attributes of label, color or image typology as valid alternative to WooCommerce classic solutions. By advanced variations, your products will be shown in a more professional way.','yith-woocommerce-product-add-ons'),'<b>','</b>' ); ?>
                </p>

            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/04.png" alt="Screenshot" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/05-bg.png) no-repeat #fff; background-position: 85% 100%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/05.png" alt="Screenshot" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WAPO_ASSETS_URL ?>/img/tab-premium/05-icon.png" alt="icon" />
                    <h2><?php _e('Add-on/variation dependency','yith-woocommerce-product-add-ons') ?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Have you created and configured the add-on for your %1$svariable product%2$s and you want to show it only for a specific variation? Now you can do it with YITH WooCommerce Product Add-Ons. %3$s By %1$ssetting the dependency between variation and add-on%2$s the field will automatically be hidden until the user selects the variation','yith-woocommerce-product-add-ons'),'<b>','</b>', '<br>' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    Upgrade to the <span class="highlight">premium version</span>
                    of <span class="highlight">YITH Woocommerce Product Add-Ons</span> to benefit from all features!
                </p>
                <a href="<?php echo $this->get_premium_landing_uri(); ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight">UPGRADE</span>
                    <span>to the premium version</span>
                </a>
            </div>
        </div>
    </div>
</div>