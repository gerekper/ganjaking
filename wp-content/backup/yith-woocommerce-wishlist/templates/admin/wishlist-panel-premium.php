<style>
	.landing{
		margin-right: 15px;
		border: 1px solid #d8d8d8;
		border-top: 0;
	}
    .section{
	    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
	    background: #fafafa;
    }
    .section h1{
        text-align: center;
        text-transform: uppercase;
        color: #445674;
        font-size: 35px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0 0;
    }
    .section .section-title h2{
        vertical-align: middle;
        padding: 0;
	    line-height: normal;
        font-size: 24px;
        font-weight: 700;
        color: #445674;
        text-transform: uppercase;
	    background: none;
	    border: none;
	    text-align: center;
    }
    .section p{
        margin: 15px 0;
	    font-size: 19px;
	    line-height: 32px;
	    font-weight: 300;
	    text-align: center;
    }
    .section ul li{
        margin-bottom: 4px;
    }
    .section.section-cta{
	    background: #fff;
    }
    .cta-container,
    .landing-container{
	    display: flex;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding: 30px 0;
	    align-items: center;
    }
    .landing-container-wide{
	    flex-direction: column;
    }
    .cta-container{
	    display: block;
	    max-width: 860px;
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
    .landing-container .col-1{
	    width: 58.33333333%;
    }
    .landing-container .col-2{
	    width: 41.66666667%;
    }
    .landing-container .col-1 img,
    .landing-container .col-2 img,
    .landing-container .col-wide img{
        max-width: 100%;
    }
    .wishlist-cta{
        color: #4b4b4b;
        border-radius: 10px;
        padding: 30px 25px;
	    display: flex;
	    align-items: center;
	    justify-content: space-between;
	    width: 100%;
	    box-sizing: border-box;
    }
    .wishlist-cta:after{
        content: '';
        display: block;
        clear: both;
    }
    .wishlist-cta p{
        margin: 10px 0;
	    line-height: 1.5em;
        display: inline-block;
	    text-align: left;
    }
    .wishlist-cta a.button{
        border-radius: 25px;
        float: right;
        background: #e09004;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 10px 50px 8px;
	    text-align: center;
	    text-transform: uppercase;
	    font-weight: 600;
	    font-size: 20px;
		line-height: normal;
	    border: none;
    }
    .wishlist-cta a.button:hover,
    .wishlist-cta a.button:active,
    .wp-core-ui .yith-plugin-ui .wishlist-cta a.button:focus{
        color: #fff;
        background: #d28704;
        box-shadow: none;
        outline: none;
    }
    .wishlist-cta .highlight{
        text-transform: uppercase;
        background: none;
        font-weight: 500;
    }

    @media (max-width: 991px){
	    .landing-container{
		    display: block;
		    padding: 50px 0 30px;
	    }

	    .landing-container .col-1,
	    .landing-container .col-2{
		    float: none;
		    width: 100%;
	    }

	    .wishlist-cta{
		    display: block;
		    text-align: center;
	    }

	    .wishlist-cta p{
		    text-align: center;
		    display: block;
		    margin-bottom: 30px;
	    }
	    .wishlist-cta a.button{
		    float: none;
		    display: inline-block;
	    }
    }
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="cta-container">
            <div class="wishlist-cta">
                <p><?php echo sprintf (__('Upgrade to the %1$spremium version%2$s%3$sof %1$sYITH WooCommerce Wishlist%2$s to benefit from all features!','yith-woocommerce-wishlist'),'<span class="highlight">','</span>','<br/>');?></p>
                <a href="<?php echo YITH_WCWL_Admin()->get_premium_landing_uri(); ?>" target="_blank" class="wishlist-cta-button button btn">
                   <?php _e('Upgrade','yith-woocommerce-wishlist');?>
                </a>
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <h1><?php _e('Premium Features', 'yith-woocommerce-wishlist');?></h1>
        <div class="landing-container">
            <div class="col-2">
	            <div class="section-title">
		            <h2><?php _e('Allow your customers to create multiple wishlists', 'yith-woocommerce-wishlist');?></h2>
	            </div>
	            <p><?php _e( 'Christmas, Birthday... users will be able to create and manage multiple wishlists, in case they prefer to keep the products sorted by category or other parameters.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
            <div class="col-1">
	            <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/01.png" alt="<?php _e('Multiple Wishlist', 'yith-woocommerce-wishlist');?>" />
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
	        <div class="col-1">
		        <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/02.png" alt="<?php _e('Wishlist Private', 'yith-woocommerce-wishlist');?>" />
	        </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('A transparent privacy management', 'yith-woocommerce-wishlist');?></h2>
                </div>
                <p><?php _e( 'Your customers can set a privacy option for each wishlist and choose whether sharing the wishlist or making it private.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Allow your customers to ask for an estimate, directly from their wishlist page', 'yith-woocommerce-wishlist');?></h2>
                </div>
                <p><?php _e( 'And give only registered users the privilege to use the wishlist functionalities.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
	        <div class="col-1">
		        <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/03.png" alt="<?php _e('Estimate Cost', 'yith-woocommerce-wishlist');?>" />
	        </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/04.png" alt="<?php _e('Admin Panel', 'yith-woocommerce-wishlist');?>" />
            </div>
	        <div class="col-2">
		        <div class="section-title">
			        <h2><?php _e('An advanced and more versatile management of the wishlist', 'yith-woocommerce-wishlist');?></h2>
		        </div>
		        <p><?php _e( 'Your customers can sort the products in the wishlist thanks to the drag&drop option, move products from one wishlist to another, manage product quantity, download the wishlist content to a .pdf file, share the wishlist on their social networks, and much more!', 'yith-woocommerce-wishlist' ) ?></p>
	        </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Monitor your customers’ wishlists and the popular products', 'yith-woocommerce-wishlist');?></h2>
                </div>
                <p><?php _e( 'You can see your customers’ wishlists, gain insight into the products they are more interested in and plan targeted marketing strategies.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
	        <div class="col-1">
		        <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/05.png" alt="<?php _e('Search Wishlists', 'yith-woocommerce-wishlist');?>" />
	        </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/06.png" alt="<?php _e('\'ADD TO CART\'', 'yith-woocommerce-wishlist');?>" />
            </div>
	        <div class="col-2">
		        <div class="section-title">
			        <h2><?php _e('Send promotional emails for products in wishlists to push customers to buy', 'yith-woocommerce-wishlist');?></h2>
		        </div>
		        <p><?php _e( 'Just three clicks to send promotional emails with discount coupons to customers who have added specific products to their wishlist and push them to buy.', 'yith-woocommerce-wishlist' ) ?></p>
	        </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Let users buy the product right from the wishlist page', 'yith-woocommerce-wishlist');?></h2>
                </div>
                <p><?php _e( 'Let them move products from one wishlist to the cart in one click, keeping also the information about the size, colour or quantity selected when added to the wishlist.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
	        <div class="col-1">
		        <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/07.png" alt="<?php _e('DISABLE WISHLIST', 'yith-woocommerce-wishlist');?>" />
	        </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container landing-container-wide">
            <div class="col-wide">
                <div class="section-title">
                    <h2><?php _e('Choose a charming layout for your wishlist page', 'yith-woocommerce-wishlist');?></h2>
                </div>
                <p><?php _e( 'The wishlist is one of the most used functionalities in an ecommerce store but often the page layout is not enhanced enough and looks unattractive to the user. With our plugin, you can choose among some alternative layouts and offer an even more interesting experience to users who creates a wishlist on your website.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
            <div class="col-wide">
                <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/08.png" alt="<?php _e('UNLOGGED USERS', 'yith-woocommerce-wishlist');?>" />
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Wishlist widgets for the header and sidebars', 'yith-woocommerce-wishlist');?></h2>
                </div>
                <p><?php _e( 'Increase the wishlist visibility through our modern widgets that you can use in the header, in the sidebars, wherever you want.', 'yith-woocommerce-wishlist' ) ?></p>
            </div>
	        <div class="col-1">
		        <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/09.png" alt="<?php _e('POPULAR TABLE', 'yith-woocommerce-wishlist');?>" />
	        </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCWL_URL ?>assets/images/landing/10.png" alt="<?php _e('FUNCTIONALITIES', 'yith-woocommerce-wishlist');?>" />
            </div>
	        <div class="col-2">
		        <div class="section-title">
			        <h2><?php _e('Allow users to monitor the price of the products in their wishlist', 'yith-woocommerce-wishlist');?></h2>
		        </div>
		        <p><?php _e( 'We took inspiration from one of the most interesting features of Amazon product pages: from now on, users can realise what’s the best time to buy a product and how much they can save when there’s a promotion running or a discount on the product they’ve added to the wishlist.', 'yith-woocommerce-wishlist' ) ?></p>
	        </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="cta-container">
            <div class="wishlist-cta">
                <p><?php echo sprintf (__('Upgrade to the %1$spremium version%2$s%3$sof %1$sYITH WooCommerce Wishlist%2$s to benefit from all features!','yith-woocommerce-wishlist'),'<span class="highlight">','</span>','<br/>');?></p>
                <a href="<?php echo YITH_WCWL_Admin()->get_premium_landing_uri();?>" target="_blank" class="wishlist-cta-button button btn">
                    <?php _e( 'Upgrade', 'yith-woocommerce-wishlist' ); ?>
                </a>
            </div>
        </div>
    </div>
</div>
