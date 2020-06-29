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
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    width: 60%;
}
.premium-cta a.button{
    border-radius: 6px;
    height: 60px;
    float: right;
    background: url(<?php echo YITH_WCET_URL?>assets/images/upgrade.png) #ff643f no-repeat 13px 13px;
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
    background: url(<?php echo YITH_WCET_URL?>assets/images/upgrade.png) #971d00 no-repeat 13px 13px;
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
    background: url(<?php echo YITH_WCET_URL?>assets/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.two{
    background: url(<?php echo YITH_WCET_URL?>assets/images/02-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.three{
    background: url(<?php echo YITH_WCET_URL?>assets/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.four{
    background: url(<?php echo YITH_WCET_URL?>assets/images/04-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.five{
    background: url(<?php echo YITH_WCET_URL?>assets/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.six{
    background: url(<?php echo YITH_WCET_URL?>assets/images/06-bg.png) no-repeat #fff; background-position: 85% 75%
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
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Email Templates%2$s to benefit from all features!','yith-woocommerce-email-templates'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-email-templates');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-email-templates');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-woocommerce-email-templates');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCET_URL?>assets/images/01.png" alt="Email template" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCET_URL?>assets/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('A template for each kind of email ','yith-woocommerce-email-templates');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('With the premium version of the plugin you can assign a different template to each kind of email, customizing freely %1$stexts%2$s and %1$scolors%2$s.%3$sA vital feature for your shop! ', 'yith-woocommerce-email-templates'), '<b>', '</b>','<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCET_URL?>assets/images/02-icon.png" alt="icon 02" />
                    <h2><?php _e('4 different layouts','yith-woocommerce-email-templates');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Four detailed layouts you can use to create your %1$semail templates%2$s.%3$sFour different choices to please everyone\'s taste and have the right solution for each email type at hand.', 'yith-woocommerce-email-templates'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCET_URL?>assets/images/02.png" alt="4 layouts" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCET_URL?>assets/images/03.png" alt="Style" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCET_URL?>assets/images/03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'Customizable style ','yith-woocommerce-email-templates');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Give your personal touch to each email template, changing %1$scolors%2$s and %1$scontent%2$s. A rich option panel is available to help you get the result you want with few clicks.%3$s Thanks to the preview, you will be able to check immediately if the final template is the way you need, or if you have to change something more.', 'yith-woocommerce-email-templates'), '<b>', '</b>','<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCET_URL?>assets/images/04-icon.png" alt="icon 04" />
                    <h2><?php _e('Customized links ','yith-woocommerce-email-templates');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Do you want to add a small menu ahead of the body of your email? With the premium version of YITH WooCommerce Email Templates plugin you can!%3$sAdd one or more entries and assign them a link: %1$sa rapid way to get easily where you want!%2$s', 'yith-woocommerce-email-templates'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCET_URL?>assets/images/04.png" alt="Links" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCET_URL?>assets/images/05.png" alt="Social network" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCET_URL?>assets/images/05-icon.png" alt="icon 05" />
                    <h2><?php _e('Social network sites','yith-woocommerce-email-templates');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Live the social network even in your emails. Add links to the most famous %1$ssocial network sites%2$s, and give your users the freedom to access them with a simple click from the received email. ','yith-woocommerce-email-templates'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCET_URL?>assets/images/06-icon.png" alt="icon 06" />
                    <h2><?php _e('Footer','yith-woocommerce-email-templates');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'An additional section of your WooCommerce email that you can now customize. Add the %1$stext%2$s you want to add in the footer and a %1$slogo%2$s: it can even be different from the one you used in the top part of the email.%3$sNow the template is truly complete in every part! ','yith-woocommerce-email-templates' ),'<b>','</b>','<br>' ) ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCET_URL?>assets/images/06.png" alt="Footer" />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Email Templates%2$s to benefit from all features!','yith-woocommerce-email-templates'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-email-templates');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-email-templates');?></span>
                </a>
            </div>
        </div>
    </div>
</div>