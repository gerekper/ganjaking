=== WooCommerce Measurement Price Calculator ===
Author: skyverge
Tags: woocommerce
Requires at least: 4.4
Tested up to: 5.4.1
Requires PHP: 5.6

Allows the customer to enter width, height, length, weight, area or volume, and calculates the minimum number of product required and the price.

== Installation ==

1. Upload the folder 'woocommerce-measurement-price-calculator' to the '/wp-content/plugins/' directory
2. Activate 'WooCommerce Measurement Price Calculator' through the 'Plugins' menu in WordPress

== Usage ==

Configure Product Physical Property Units

1. Navigate to WooCommerce -> Settings -> Catalog
2. Locate the "Product Data" section
3. Select the Weight Unit, Dimension Unit, Area Unit and Volume Unit you want to define your product properties in


Product Physical Properties

1. Edit or create a new non-virtual, simple or variable product
2. Find the Product Data panel and click the General tab
3. Define any of the physical properties needed, in the units configured above:  weight, dimensions, area and/or volume.  The Measurement Price Calculator plugin will automatically calculate area and volume based on the dimensions set, or the area/volume can be directly set using the Area and Volume fields respectively.

Configure Measurement Price Calculator

1. Edit a non-virtual, simple or variable product, find the Product Data panel and click the Measurement tab
2. Choose the measurement you wish to provide a calculator for, ie Dimensions, Area, Volume or Weight
3. If you want to create a read-only calculator that simply displays the measurement amount rather than allowing the customer to set the amount desired, uncheck the 'Editable' box

The Measurement Price Calculator form will now appear on the front end product detail page for your simple or variable product
