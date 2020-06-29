*** This readme must accompany the Plugin at all times and not to be altered or changed in any way. ***

=== Email Attachments for WooCommerce ===

Contributors: InoPlugs
Tags: woocommerce, woo commerce, e-commerce, shop, cart, ecommerce, files, attachments, email, e mail, e-mail
Requires at least: 3.3
Tested up to: 3.3.1
Stable tag: 1.1.0

== Description ==

This plugin is an add on to the E-Mails generated automatically by WooCommerce. It allows you to:

-  Change the default upload folder to any destination you like
-  Define an individual notification headline and text, which indicates that attachments have been sent - it will be placed in the footer section of the E-Mail
-  Send CC and/or BCC Copies to multiple addresses and define them individually for each E-Mail type
-  Upload files and select them individually for each E-Mail type
-  Delete files which are not needed any longer
-  On deactivation/deinstallation keep or remove all your settings and files

The following, automatically generated E-Mail types by WooCommerce are supported:

-  New Order
-  Processing Order E-Mail
-  Completed Order E-Mail
-  Invoice E-Mail
-  Customer Note
-  Low Stock Information
-  No Stock Information
-  Backorder Information
-  New Customer Account

To ensure optimal performance for your site, this plugin is designed to remove all its entries in the database and the uploaded files from the server on deactivation or deinstallation, if you do not want to use it any more. You have the possibility to keep all your settings and data for a later reactivation, if you like. You are also able to change the upload folder to any destination of your choice, at any time. In this case all already uploaded files are automatically moved, so you won't loose any of them.

If you prefer to upload or delete your files via ftp manually, this is also possible and the files are automatically recognised and you can select them for attachments.

Only files present in the selected upload folder will be used as attachment, as the plugin checks the existance of the selected files.

An internal fallback to a default folder and an automatic upgrade of internal options to a newer version (if detected) ensures the high reliability and easy usage of this plugin.

Most of the code is based on modern OOP-Technics (object orientated programming) and ajax requests - this ensures a high stability of this plugin. In addition the possibility of interference with other plugins due to identical function names is very low.



== Installation ==

1. Upload the folder 'woocommerce-email-attachments' to the '/wp-content/plugins/' directory

2. Activate 'WooCommerce E-Mail Attachments (by Inoplugs)' through the 'Plugins' menu in WordPress

== Usage ==

You can use the integrated ajax file uploader (covered in this readme or other methodes - refer to the included documentation in this case):

1) Go to 'WordPress Dashboard -> WooCommere -> Settings -> E-Mail Attachments'
2) Upload the files you want to use as attachments with the "Upload File" button. This opens the standard file selection window, where you can select a single file to upload. To upload more files, click the button again and repeat this process for each additional file. You get a report for every uploaded file (if uploaded was successful and the name of the file on the server).
3) Refresh the selection list with the "Reinitialize selection list" button to make the new uploaded files selectable.
4) For each E-Mail select the files you want to use as attachments
5) Click "Save Settings" - button at bottom

That's it!

== Support ==

If you have any problems, questions or suggestions please use the contact fotm on our website

http://inoplugs.com/contact/

== Disclaimer ==

It is not responsible for any harm or wrong doing this Plugin may cause. Users are fully responsible for their own use. This Plugin is to be used WITHOUT warranty.