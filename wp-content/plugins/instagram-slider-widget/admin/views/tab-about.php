<style>
    .wis-section-wrapper {
        width: auto;
        margin-top: 10px;
        margin-right: 15px;
    }

    .wis-section {
        padding: 20px 29px 0px 29px;
    }

    .container
    {
        width: 100%;
    }
    .wis-section-thin
    {
        padding: 5px 0px 5px 0px;
    }
    .wis-section-thin .subheader
    {
        font-style: italic;
    }
    .wis-section-wrapper h1
    {
        text-transform: uppercase;
        color: whitesmoke;
    }
    .wis-section-wrapper h2
    {
        text-transform: uppercase;
        color: #e53030;
    }

    .wis-section img
    {
        border-radius: 10px;
        margin: 10px 0px 0px 10px;
    }
    .wis-section-img
    {
        text-align: center;
    }

    .black-background
    {
        background-color: rgba(0,0,0,0.8);
        color: whitesmoke;
    }

    .wis-section {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        margin-right: auto;
        margin-left: auto;
        position: relative;
        max-width: 1140px;
        /*min-height: 600px;*/
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
    }

    .wis-section-intro {
        background-image: url('<?php echo WIS_PLUGIN_URL;?>/admin/assets/img/fon.jpg');
        background-position: bottom center;
        background-size: cover;
        box-shadow: 0px 0px 34px 0px rgba(107, 107, 107, 0.5);
        transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
        text-align: center;
    }

    .wis-section-intro .container h2 {
        font-size: 61px;
        font-weight: 500;
        text-transform: uppercase;
        line-height: 1.1em;
        color: #fff;
        text-align: center;
    }

    .wis-section-intro .container p {
        margin-bottom: 1.6em;
        color: #fffcfc;
        font-family: "Arial", Sans-serif;
        font-size: 22px;
        line-height: 1.3em;
        letter-spacing: 1.1px;
    }

    .wis-section-changelog h4 {
        font-size: 1.3333333333333rem;
    }

    .wis-section-changelog p,
    .wis-section-changelog ul > li {
        font-size: 15px;
    }

    .wis-section-changelog ul {
        list-style: inherit;
        margin-left: 40px;
        width: 100%;
    }

    #wpfooter {
        position: relative !important;
    }
    .center-section
    {
        text-align: center;
    }


</style>

<div class="wis-section-wrapper">
    <section class="wis-section-thin center-section black-background">
        <h1><?php echo __( 'Social Slider Widget', 'instagram-slider-widget' ); ?></h1>
        <p class="subheader"><?php echo __( 'Display Instagram feeds in widgets, posts, pages, or anywhere else using shortcodes.', 'instagram-slider-widget' ); ?></p>
    </section>
    <section class="wis-section wis-section-changelog">
        <div class="container">
            <div>
                <h2><?php echo __( 'FEATURES', 'instagram-slider-widget' ); ?></h2>
                <ul>
                    <li>Super easy to set up. Just create a widget with the necessary settings and add it anywhere on your website using shortcodes.</li>
                    <li>Show Instagram feeds on your website without authorization. Just enter a username and see the result (make sure to check the Instagram limitations on showing feeds without authorization).</li>
                    <li>Show Instagram feeds on your website without authorization using hashtags (make sure to check the Instagram limitations on showing feeds without authorization).</li>
                    <li>Authorize several Instagram accounts and show multiple feeds simultaneously with no limitations.</li>
                    <li>Fully responsive for mobile devices. Great view on any screen size and width.</li>
                    <li>Flexible layout settings. Customize the size, the number of images and columns, image spacing and much more!</li>
                    <li>Show thumbnails, medium or full-size images from your Instagram feed</li>
                    <li>Customize the display order by date, popularity or random.</li>
                    <li>Show or hide the account title.</li>
                </ul>
            </div>
        </div>
    </section>
    <section class="wis-section-thin center-section">
        <iframe width="720" height="405" src="https://www.youtube.com/embed/7EQirSL0xm4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </section>
    <section class="wis-section wis-section-changelog">
        <div class="container">
            <div>
                <h2><?php echo __( 'BENEFITS', 'instagram-slider-widget' ); ?></h2>
                <ul>
                    <li>Increase your visitors’ engagement. Get more followers on your Instagram account.</li>
                    <li>Time-saving. You no longer need to post fresh images and entries on the website. Just upload them on Instagram, and your website visitors will know about it instantly.</li>
                    <li>A new way of posting news. Flexible settings for widget display templates give your content a seamless and attractive view on any website design.</li>
                    <li>Keep up your website. Post news on Instagram more often, and Social Slider Widget will take care of the rest.</li>
                    <li>Intuitive settings. It will only take 10 seconds to configure the widget and get the content on your website; no need to authorize on Instagram or handle tokens. And you can authorize your account in one click!</li>
                </ul>
            </div>
        </div>
    </section>
    <section class="wis-section wis-section-changelog">
        <div class="container">
            <div>
                <h2><?php echo __( 'PRO VERSION', 'instagram-slider-widget' ); ?></h2>
                <p>To maintain the free version and provide prompt, effective & free support, we offer the Pro version.</p>
                <p>In the <a href="https://cm-wp.com/instagram-slider-widget/">Pro version</a>, you can:</p>
                <ul>
                    <li>
                        Authorize several accounts and display multiple feeds simultaneously (fully compatible with Instagram December 11, 2018, API changes)
                        <br>
                        <div class="wis-section-img"><img width="672" height="226" src="https://cm-wp.com/wp-content/uploads/2019/11/pic1.jpg" alt=""></div>
                    </li>
                    <li>
                        Display how many likes and comments each post has
                        <br>
                        <div class="wis-section-img"><img width="660" height="234" src="https://cm-wp.com/wp-content/uploads/2019/11/pic2.jpg" alt=""></div>
                    </li>
                    <li>
                        Create carousels of posts
                    </li>
                    <li>
                        Use <a href="https://cm-wp.com/instagram-slider-widget/masonry/">Masonry</a> or <a href="https://cm-wp.com/instagram-slider-widget/highlight/">Highlight</a> for feeds
                        <br>
                        <div class="wis-section-img"><img width="450" height="389" src="https://cm-wp.com/wp-content/uploads/2019/11/slider5.jpg" alt="">
                        <img width="450" height="389" src="https://cm-wp.com/wp-content/uploads/2019/11/slider6.jpg" alt="">
                        </div>
                    </li>
                    <li>
                        Display captions for images and videos.
                        <br>
                        <div class="wis-section-img"><img width="490" height="622" src="https://cm-wp.com/wp-content/uploads/2019/11/2019-11-01_11-03-07.png" alt=""></div>
                    </li>
                    <li>
                        View the photos and videos from your feed in a beautiful pop-up lightbox which allows users to experience your content without leaving your site.
                        <br>
                        <div class="wis-section-img"><img width="660" height="454" src="https://cm-wp.com/wp-content/uploads/2019/12/2019-12-09_12-34-33.png" alt=""></div>
                    </li>
                </ul>
                <p>Learn more about the <a href="https://cm-wp.com/instagram-slider-widget/">Pro version</a> or watch the <a href="https://cm-wp.com/instagram-slider-widget/#demos">demo</a>.</p>
            </div>
        </div>
    </section>
    <section class="wis-section wis-section-changelog">
        <div class="container">
            <div>
                <h2><?php echo __( 'FEEDBACK AND SUPPORT', 'instagram-slider-widget' ); ?></h2>
                <p>Our goal is to create a simple yet powerful plugin – customized and multifunctional, with prompt and helpful support.
                    For any difficulties or questions about the setup, open a ticket on the <a href="https://cm-wp.com/support/">support</a> and get support in no time.</p>
            </div>
        </div>
    </section>
    <section class="wis-section wis-section-changelog">
        <div class="container">
            <div>
                <h2><?php echo __( 'WHY DO YOU NEED IT?', 'instagram-slider-widget' ); ?></h2>
                <ul>
                    <li>Increase your visitors’ engagement. Get more followers on your Instagram account.</li>
                    <li>Time-saving. You no longer need to post fresh images and entries on the website. Just upload them on Instagram, and your website visitors will know about it instantly.</li>
                    <li>A new way of posting news. Flexible settings for widget display templates give your content a seamless and attractive view on any website design.</li>
                    <li>Keep up your website. Post news on Instagram more often, and Social Slider Widget will take care of the rest.</li>
                    <li>Intuitive settings. It will only take 10 seconds to configure the widget and get the content on your website; no need to authorize on Instagram or handle tokens. And you can authorize your account in one click!</li>
                    <li>Great user support.</li>
                </ul>
            </div>
        </div>
    </section>

</div>