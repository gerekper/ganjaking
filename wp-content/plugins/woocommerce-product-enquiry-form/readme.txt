=== WooCommerce Product Enquiry Form ===
Contributors: woothemes

Adds an enquiry form tab to certain product pages which allows customers to contact you about a product. Also includes optional reCAPTCHA for preventing spam.


== Usage ==

After enabling the plugin the product enquiry form will appear on product pages in its own tab.

Go to WooCommerce > Settings > General to edit the address the enquiries will go to.

Editing a product will allow you to toggle the 'Disable enquiry form?' option if you want to disable the form on certain products.

== Hooks ==

You can extend the plugin using the following filters:

product_enquiry_tab_title
product_enquiry_heading
product_enquiry_success_message
product_enquiry_email_subject
product_enquiry_email_message

and actions:

product_enquiry_process_form
product_enquiry_before_form
product_enquiry_before_message
product_enquiry_after_message
product_enquiry_after_form
