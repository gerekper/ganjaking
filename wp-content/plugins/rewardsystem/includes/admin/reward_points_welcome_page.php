<script>
    jQuery(document).ready(function () {
        jQuery('.tab_1').click(function () {
            jQuery('.tab_1').addClass('active_welcome');
            jQuery('.tab_2').removeClass('active_welcome');
            jQuery('.tab_3').removeClass('active_welcome');
            jQuery('.con_1').show();
            jQuery('.con_2').hide();
            jQuery('.con_3').hide();
        });
            jQuery('.tab_2').click(function () {
                jQuery('.tab_2').addClass('active_welcome');
                jQuery('.tab_1').removeClass('active_welcome');
                jQuery('.tab_3').removeClass('active_welcome');
                jQuery('.con_1').hide();
                jQuery('.con_2').show();
                jQuery('.con_3').hide();
            });
        jQuery('.tab_3').click(function () {
            jQuery('.tab_3').addClass('active_welcome');
            jQuery('.tab_1').removeClass('active_welcome');
            jQuery('.tab_2').removeClass('active_welcome');
            jQuery('.con_1').hide();
            jQuery('.con_2').hide();
            jQuery('.con_3').show();
        });
    });
</script>

<div class=" welcome_page">
    <div class="welcome_header" > 
        <div class="welcome_title" >
            <h1>Welcome to <strong>SUMO Reward Points</strong></h1>
        </div>
        <div class="branding_logo" >
            <a href="http://fantasticplugins.com/" target="_blank" ><img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/Fantastic-Plugins-final-Logo.png" alt="" /></a>
        </div>
    </div> 

    <p>
        Thanks for installing SUMO Reward Points...
    </p>
    
    <div class="welcomepage_tab">
        <ul>
            <li><a href="#about" class="tab_1 active_welcome">About SUMO Reward Points</a></li>
            <li><a href="#compatibl plugins" class="tab_3">Compatible Plugins</a></li>
            <li><a href="#other plugins" class="tab_2">Our Other Plugins</a></li> 
        </ul>        
        <a href="<?php echo admin_url( 'admin.php?page=rewardsystem_callback'); ?>" class="admin_btn" >Go to Settings</a>        
        <a href="http://fantasticplugins.com/support/" class="support_btn" target="_blank" >Contact Support</a>        
    </div>
<!--            about sumo reward points tab content      -->
    <div class="con_1">
        <div class="section_1">
            <div class='section_a1'> 
                <h3>Points to Consider</h3>
                <ul>                    
                    <li>Product Purchase Reward Points has to be enabled at product level for Product Purchase Reward Points to work</li>
                    <li>Product Purchase Reward Points can only be awarded to individual products and not order total</li>
                    <li>By Default, Product Purchase Reward Points will be added to user's account when order status becomes completed</li>
                    <li>WooCommerce Coupons has to be enabled for Points Redeeming functionality to work</li>
                    <li>Earning Point Conversion settings has to be configured for using Reward Type "By Percentage of Product Price"</li>
                    <li>Redeeming Point Conversion settings has to be configured for Points Redeeming functionality to work</li>
                    <li>Product Purchase Reward Points display and the actual Points Earned may be different based on your Tax Setup</li>
                    <li>Reward Points used for Redeeming and actual discount obtained may be different based on your Tax Setup</li>
                    <li>Social Reward Points has to be enabled at product level for Social Reward Points to work</li>
                    <li>Social Reward Points will work only with Inbuilt Social Icons</li>
                </ul>
            </div> 
        </div>
    </div>
<!--            compatibility tab content      -->    
    <div class="con_3">
        <div class="Brand_featured">
            <h2>SUMO Reward Points is Compatible with</h2>
            <div class="feature">
                
                <div class="two_fet_img">
                    <a href="https://codecanyon.net/item/sumo-subscriptions-woocommerce-subscription-system/16486054?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/sumo_subscription.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Subscriptions</h4>
                        </div>
                        <div class="description">
                            <p><strong>SUMO Subscriptions</strong> is a subscription extension for WooCommerce. Using <b>SUMO Subscriptions</b>, you can create and sell subscription products in your existing WooCommerce shop.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
                
                <a href="https://codecanyon.net/item/sumo-memberships-woocommerce-membership-system/16642362?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/sumo_membership.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Memberships</h4>
                        </div>
                        <div class="description">
                            <p><strong>SUMO Memberships </strong>is a membership extension for WooCommerce. Using <b>SUMO Memberships</b>, you can restrict/provide access to specific Pages, Posts, Products, URL.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
               </div>
                
              <div class="two_fet_img">
                <a href="https://codecanyon.net/item/sumo-discounts-advanced-pricing-woocommerce-discount-system/17116628?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/sumo_discount.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Discounts</h4>
                        </div>
                        <div class="description">
                            <p><strong>SUMO Discounts</strong> is a WooCommerce Extension Plugin. Using <b>SUMO Discounts</b> plugin you can provide discounts to your users in multiple ways.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a> 
                
                <a href="https://codecanyon.net/item/sumo-coupons-woocommerce-coupon-system/16082070?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/sumo_coupons.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Coupons</h4>
                        </div>
                        <div class="description">
                            <p><strong>SUMO Coupons</strong>  is a WooCommerce Loyalty Coupon System. Using <b>SUMO Coupons</b> you can offer coupons to your customers for Account Sign Up, Product Purchases, Writing Reviews etc. </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
              </div>
            </div>
        </div>
    </div>
<!--            our other plugins tab content      -->     
    <div class="con_2">
        <div class="con2_title">
          <h2>Our Other WooCommerce Plugins</h2>
        </div>
        <div class="feature">
             <div class="two_fet_img">
                <a href="https://codecanyon.net/item/woocommerce-recover-abandoned-cart/7715167?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/Recover_abandoned_cart.png" alt=""/>
                        <div class="hide">
                            <h4>Recover Abandoned Cart</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Recover Abandoned Cart</strong> is a WooCommerce Extension. Using <b>Recover Abandoned Cart</b>, you can send follow up emails with Purchase links to users who have Abandoned their Purchase.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
                
                <a href="https://codecanyon.net/item/sumo-affiliates-woocommerce-affiliate-system/18273930?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/sumo_affiliates.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Affiliates</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Affiliates</strong> is a Affiliate System for WooCommerce. Using <b>SUMO Affiliates</b> you can run Affiliate Promotions in your existing WooCommerce Shop.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
             </div>
            
             <div class="two_fet_img">
                <a href="https://codecanyon.net/item/woocommerce-pay-your-price/7000238?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/pay_your_price.png" alt=""/>
                        <div class="hide">
                            <h4>Pay Your Price</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Pay Your Price</strong> is a WooCommerce Extension. Using <b>Pay Your Price</b>, Users can pay their own price for the Products. </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a> 
                
                <a href="https://codecanyon.net/item/galaxy-funder-woocommerce-crowdfunding-system/7360954?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/Galaxy_funder.png" alt=""/>
                        <div class="hide">
                            <h4>Galaxy Funder</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Galaxy Funder</strong> is a Crowdfunding Extension for WooCommerce. Using <b>Galaxy Funder</b> you can run <b>Keep What you Raise</b> Crowdfunding Campaigns in your existing WooCommerce Shop. </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
             </div>
            
        <div class="feature">
            <div class="two_fet_img">
               <a href="https://codecanyon.net/item/universe-funder-woocommerce-crowdfunding-system/10283380?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/Universe_Funder.png" alt=""/>
                        <div class="hide">
                            <h4>Universe Funder</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Universe Funder</strong> is a Crowdfunding Extension for WooCommerce. Using <b>Universe Funder</b> you can run <b>All or Nothing </b> Crowdfunding Campaigns in your existing WooCommerce Shop </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            
               <a href="https://codecanyon.net/item/woocommerce-paypal-adaptive-split-payment/7948397?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo SRP_PLUGIN_DIR_URL;?>/assets/images/paypal_adaptive_split_payment.png" alt=""/>
                        <div class="hide">
                            <h4>PayPal Adaptive Split Payment</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>PayPal Adaptive Split Payment</strong> is a Payment Gateway Extension for WooCommerce. Using <b>PayPal Adaptive Split Payment</b>, the Order amount can be split between a maximum of six different Receivers</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
            
        </div>
   </div>
</div>
