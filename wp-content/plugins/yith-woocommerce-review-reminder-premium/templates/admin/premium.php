<style>
    .section {
        margin-left: -20px;
        margin-right: -20px;
        font-family: "Raleway", san-serif;
    }

    .section h1 {
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

    .section:nth-child(even) {
        background-color: #fff;
    }

    .section:nth-child(odd) {
        background-color: #f1f1f1;
    }

    .section .section-title img {
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

    .section .section-title h2 {
        display: table-cell;
        vertical-align: middle;
    }

    .section-title {
        display: table;
    }

    .section h3 {
        font-size: 14px;
        line-height: 28px;
        margin-bottom: 0;
        display: block;
    }

    .section p {
        font-size: 13px;
        margin: 25px 0;
    }

    .section ul li {
        margin-bottom: 4px;
    }

    .landing-container {
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }

    .landing-container:after {
        display: block;
        clear: both;
        content: '';
    }

    .landing-container .col-1,
    .landing-container .col-2 {
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }

    .landing-container .col-1 img {
        width: 100%;
    }

    .landing-container .col-1 {
        width: 55%;
    }

    .landing-container .col-2 {
        width: 45%;
    }

    .premium-cta {
        background-color: #808a97;
        color: #fff;
        border-radius: 6px;
        padding: 20px 15px;
    }

    .premium-cta:after {
        content: '';
        display: block;
        clear: both;
    }

    .premium-cta p {
        margin: 7px 0;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }

    .premium-cta a.button {
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url(<?php echo YWRR_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
        border-color: #ff643f;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 9px 50px 9px 70px;
    }

    .premium-cta a.button:hover,
    .premium-cta a.button:active,
    .premium-cta a.button:focus {
        color: #fff;
        background: url(<?php echo YWRR_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
        border-color: #971d00;
        box-shadow: none;
        outline: none;
    }

    .premium-cta a.button:focus {
        top: 1px;
    }

    .premium-cta a.button span {
        line-height: 13px;
    }

    .premium-cta a.button .highlight {
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }

    .premium-cta .highlight {
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

    @media (max-width: 768px) {
        .section {
            margin: 0
        }

        .premium-cta p {
            width: 100%;
        }

        .premium-cta {
            text-align: center;
        }

        .premium-cta a.button {
            float: none;
        }
    }

    @media (max-width: 480px) {
        .wrap {
            margin-right: 0;
        }

        .section {
            margin: 0;
        }

        .landing-container .col-1,
        .landing-container .col-2 {
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

    @media (max-width: 320px) {
        .premium-cta a.button {
            padding: 9px 20px 9px 70px;
        }

        .section .section-title img {
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
                    of <span class="highlight">YITH WooCommerce Review Reminder</span> to benefit from all features!
                </p>
                <a href="https://yithemes.com/themes/plugins/yith-woocommerce-review-reminder/" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight">UPGRADE</span>
                    <span>to the premium version</span>
                </a>
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YWRR_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%">
        <h1>Premium Features</h1>

        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YWRR_ASSETS_URL ?>/images/01.png" alt="Review Title" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWRR_ASSETS_URL ?>/images/01-icon.png" alt="Review Title" />

                    <h2>SELECTION OF THE PRODUCTS TO REVIEW</h2>
                </div>
                <p>Choose to ask users the review of every purchased product, or only a part of them.</p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YWRR_ASSETS_URL ?>/images/02-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWRR_ASSETS_URL ?>/images/02-icon.png" alt="Attachment List" />

                    <h2>DEADLINE FOR EMAIL DISPATCHING</h2>
                </div>
                <p>Set how many days have to pass before sending the email to request a review, after the order has been marked as "Completed".</p>
            </div>
            <div class="col-1">
                <img src="<?php echo YWRR_ASSETS_URL ?>/images/02.png" alt="Attachment List" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YWRR_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 100%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YWRR_ASSETS_URL ?>/images/03.png" alt="Vote the review" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWRR_ASSETS_URL ?>/images/03-icon.png" alt="Vote the review" />

                    <h2>BLOCKLIST</h2>
                </div>
                <p>Add to the blocklist all the users' emails that don't want to review any product of your e-commerce.</p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YWRR_ASSETS_URL ?>/images/04-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWRR_ASSETS_URL ?>/images/04-icon.png" alt="Number" />

                    <h2>EMAIL TEMPLATE</h2>
                </div>
                <p>Select the layout you want for your email choosing among the four possibilities we offer you.</p>
            </div>
            <div class="col-1">
                <img src="<?php echo YWRR_ASSETS_URL ?>/images/04.png" alt="Number" />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    Upgrade to the <span class="highlight">premium version</span>
                    of <span class="highlight">YITH WooCommerce Review Reminder</span> to benefit from all features!
                </p>
                <a href="https://yithemes.com/themes/plugins/yith-woocommerce-review-reminder/" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight">UPGRADE</span>
                    <span>to the premium version</span>
                </a>
            </div>
        </div>
    </div>
</div>