=== WooCommerce Free Gift Coupons ===
Contributors: helgatheviking
Requires at least: 4.4.0
Tested up to: 6.1.0
Stable tag: 3.4.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Enables store owners to make a coupon that adds a gift product to the cart.

== Description ==

Enables store owners to make a coupon only that adds a gift item to the cart on using the coupon. For example, user buys X and gets a free Y with coupon code.

== Installation ==

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

== Coupon Configuration ==

WooCommerce Free Gift Coupons works very similarly to the other coupon types. Go to WooCommerce > Coupons and click on "Add Coupon" to create a coupon and define the coupon’s redemption code.

Under "General", an input called "Free Gifts" will appear for all supported coupon types (for combo-style coupons… ie: discount plus gift) and the "Free Gift" coupon type for giving a gift alone.

= Gift Selection =

Start typing the name of the product that you’d like to give away in the "Search for products" input until WooCommerce shows a list of potential matches. Once you see your desired product, you can select it from the list of matching products.

![Coupon creation panel showing where to add free gifts associated with this coupon](https://user-images.githubusercontent.com/507025/96275082-8bca5d80-0f8e-11eb-8e06-81c49d406057.png)

Free Gift coupons follow all the same Usage Restriction and Usage Limits as other Discount Types, such as by email address, minimum cart total, etc.

= Synchronising a Product =

You can sync your free gift product(s)'s quantity with a product in the cart. Go to 'Sync Quantities' section, select a product.

***Note: When a Product is synced with free gift, it is added to the required Products that must be in the cart before the coupon is valid.***

= Free Shipping for Gifts =

This setting allows the customer to not incur shipping for the gift product.  A [free shipping method](https://docs.woocommerce.com/document/free-shipping/) must be enabled.

= Usage Restrictions and Limits =

Free Gift coupons follow all the same Usage Restriction and Usage Limits as other Discount Types, such as by email address, minimum cart total, etc. Read more about [Coupon Management](https://docs.woocommerce.com/document/coupon-management).

The sole exception to this is if you set a product to <em>sync</em> your gift quantities to. This product will be added to the required products list.

= Publish =

When you are done configuring the Coupon, select publish and the Free Gift Coupon will now be active.

== Coupon Redemption ==

The coupon can be redeemed exactly like other coupons.  When the customer enters the appropriate code and clicks "Apply Coupon" a small message is displayed saying the code was applied and the free gift is automatically added to the cart.

![The coupon code "variablegift" is entered and an animation appears to show the gift t-shirts size and color options.](https://user-images.githubusercontent.com/507025/96274766-24aca900-0f8e-11eb-8f64-95f585e74ec7.gif) The free gift is automatically added to the cart[/caption]

== FAQ ==

= Can I give a gift if someone buys a certain number of items? For example, buy 9 'widgets' and get one free? =

Currently, no. WooCommerce doesn't yet support usage limitations by item quantity.