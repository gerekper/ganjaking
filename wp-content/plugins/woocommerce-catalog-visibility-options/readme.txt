=== WooCommerce Catalog Visibility Options ===
Contributors: lucasstark
Tags: woocommerce
Requires at least: 3.0
Tested up to: 5.0.2
Copyright: © 2019 Lucas Stark.

WooCommerce Catalog Visibility Options

Provides the ability to hide prices, or show prices only to authenticated users. 
Provides the ability to disable e-commerce functionality by disabling the cart. 
Allows configuration of alternate price content, when prices are disabled or shown only to logged in users. 
Allows configuration of alternate add-to-cart content, when e-commerce functionality is disabled or only available to authenticated users. 

== Installation ==

1. Upload the folder 'woocommerce-catalog-visibility-options' to the '/wp-content/plugins/' directory

2. Activate 'WooCommerce Catalog Visibility Options' through the 'Plugins' menu in WordPress

== Usage ==
Navigate to the WooCommerce Settings area. 
Navigate to the Visibility Options Tab
Choose your options:
Purchases:
    Enabled - No changes to the way your store functions. 
    Disabled - No purchases allowed for anyone. This setting disables all add to cart functionality, and optionally replaces the Add To Cart button text with what you enter a value for Catalog Add to Cart Button Text. 
    Enabled for Logged In Users:  Same as Disabled, however, only applies when a user is not authenticated. 

Prices:
    Enabled - No changes to the way your store functions. 
    Disabled - Disables all prices in the store. Disables prices for users who are not authenticated, and optionally displays the contents of the Catalog Price Text in place of the products price. 
    Enabled for Logged in Users - Disables prices for users who are not authenticated, and optionally displays the contents of the Catalog Price Text in place of the products price. 
*note:  When prices are disabled, or enabled only for authenticated users, add-to-cart functionality is automatically disabled. 

Catalog Add to Cart Button Text:
    Optional text to display in place of the add to cart button when purchases are disabled, or enabled only for logged in users. 

Catalog Price Text:
    Optional text to display in place of the price when prices are disabled, or enabled only for logged in users. 

Alternate Content:
    Optional content that will be used on the single product details page when prices or purchases are disabled or enabled only for logged on users.  Useful for displaying ordering details, or a logon form if requiring user authentication before sales are allowed.  



=======
Includes shortcodes for use when building alternate catalog prices, alternate add to cart buttons, and additional single product details. 

Shortcodes:
[woocommerce_logon_link]
    Uses:
    [woocommerce_logon_link]
    [woocommerce_logon_link]Custom Login Text[/woocommerce_logon_link]

[woocommerce_register_link]
    Uses:
    [woocommerce_register_link]
    [woocommerce_register_link]Custom Registration Text[/woocommerce_register_link]

[woocommerce_forgot_password_link]
    Uses:
    [woocommerce_forgot_password_link]
    [woocommerce_forgot_password_link]Custom Registration Text[/woocommerce_forgot_password_link]


[woocommerce_logon_form]
    Uses:
    [woocommerce_logon_form]



Recommended Contents:
    Alternate Catalog Price Text:
    [woocommerce_logon_link]Login[/woocommerce_logon_link] or [woocommerce_register_link] Register [/woocommerce_register_link]

    Alternate Content:
    To add items to your cart and to view your prices please login below. If you do not have account you can [woocommerce_register_link] and start shopping with us.'

    [woocommerce_logon_form]
