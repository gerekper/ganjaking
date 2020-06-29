=== WP Sitemap Page ===
Contributors: funnycat
Donate link: http://www.infowebmaster.fr/dons.php
Tags: sitemap, generator, page list, site map, html sitemap, sitemap generator, dynamic sitemap, seo
Requires at least: 3.0
Tested up to: 5.2.2
Stable tag: 1.6.3
License: GPLv2 or later


Add a sitemap on any of your page using the simple shortcode [wp_sitemap_page]. Improve the SEO and navigation of your website.


== Description ==
An easy way to **add a sitemap** on one of your pages becomes reality thanks to this WordPress plugin. Just use the shortcode [wp_sitemap_page] on any of your pages. This will automatically generate a sitemap of all your pages and posts.

Be carefull, this plugin do not generate an XML sitemap. It only allow you to list all your pages and posts on a single page. This is a sitemap for human not for search engines bots.

= Why this plugin is useful? =
Such a sitemap is useful for many reasons:

*   **Easy navigation** for the users. They can find easily pages or previous posts
*   **Improve the SEO** of a website

= Current features =
*   Display all pages, posts and categories
*   Display the Custom Post Type (such as: "event", "book" …)
*   Display the taxonomies
*   Display only one kind of content using the attribute "only", like `[wp_sitemap_page only="page"]`
*   Easy to use
*   Possibility to customize the way it will be displayed through the admin panel
*   Possibility to exclude some pages or some Custom Post Type (CPT)
*   Posts and categories displayed hierarchically
*   Has CSS class to customize it if you want
*   Available in multi-languages (cf. English, French, Russian, Italian, Spanish, Dutch, Czech, Persian …). You can add your own translation if you want

Want a WordPress developer? Want to add a translation? Feel free to [contact me](http://en.tonyarchambeau.com/contact.html).


== Installation ==
1. Unzip the plugin and upload the "wp-sitemap-page" folder to your "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" administration page in WordPress
3. Create a new page where you plan to set-up your sitemap
4. Use the shortcode [wp_sitemap_page] on this page. Save the page and visualize it. That's it, your sitemap should be visible on this page.


== Frequently Asked Questions ==
= Does this plugin works for a huge website? =
No. The sitemap is dynamically generated without using any cache. Trying to generate a huge sitemap will be very slow.

= Does it generate an XML sitemap? =
No. The purpose of this plugin is to generate a sitemap on one of your pages. This is simply a list of all your pages and posts.

= Does it work with Custom Post Type? =
Yes. It works fine with the Custom Post Type since version 1.0.4

= Is it possible to get only the pages, the posts or a Custom Post Type? =
Yes, it is. You only have to use one of these shortcode:

*   `[wp_sitemap_page only="page"]` to display only the pages
*   `[wp_sitemap_page only="post"]` to display only the posts
*   `[wp_sitemap_page only="category"]` to display only the categories
*   `[wp_sitemap_page only="tag"]` to display only the tags
*   `[wp_sitemap_page only="archive"]` to display only the archives
*   `[wp_sitemap_page only="author"]` to display only the authors
*   You can display any kind of Custom Post Type, using the keyword of the post type inside the "only" attribute. For example: `[wp_sitemap_page only="event"]` or `[wp_sitemap_page only="book"]`


= Which languages does WP Sitemap Page support? =
This plugin is available through the following languages :

*   English (default language)
*   French (`fr_FR`, `fr_CA`, `fr_BE`, `fr_CH`, `fr_LU`) by [Tony Archambeau](http://tonyarchambeau.com/)
*   Russian (`ru_RU`) by [skesov.ru](http://skesov.ru/)
*   Dutch (`nl_NL`) by EvertRuisch
*   Farsi/Persian (`fa_IR`) by Seyyed Mostafa Ahadzadeh
*   Italian (`it_IT`) by Nima
*   Spanish (`es_ES`) by Raul
*   Czech (`cs_CZ`) by [Roman Opet](https://www.high-definition.cz/)
*   Polish (`pl_PL`) by [Mariusz](http://www.trojmiasto.us/)
*   Deutsch (`de_DE`) by Arno
*   Swedish (`sv_SE`)
*   Portuguese and Brazilian Portuguese (`pt_BR`, `pt_PT`) by Miguel Müller
*   Finnish (`fi_FI`) by [pokis.fi](http://www.pokis.fi/)
*   Hungarian (`hu_HU`)
*   Hebrew (`he_IL`) by Ahrale
*   Bokmål (`nb_NO`) by Ã˜ystein
*   Romanian (`ro_RO`)
*   Indonesian (`id_ID`)
*   Japanese (`ja`)
*   Ukranian (`ru`)
*   Chinese (`zh_CN`)

If you want to add another language, feel free to [contact me](http://en.tonyarchambeau.com/contact.html) in order to send the file with the correct translation files (.mo and .po). Thanks a lot!


== Screenshots ==
1. Example of a sitemap on a French blog
2. Settings page


== Changelog ==

= 1.6.1 =
* Nothing really new, but up-to-date
= 1.6 =
* Add main CSS class wrapper to add a style for the entire sitemap
* Possibility to sort the posts ascendent or descendent
* Add Ukranian and Chinese translations
= 1.5.6 =
* Oups, the last translations were not send properly. Here it is.
= 1.5.5 =
* Add Romanian and japanese translations
* Functionality to sort the pages, post categories, authors or Custom Post Type (CPT)
= 1.5.4 =
* Small fix about a core WordPress update
* Add Bokmål translation
* Add Hebrew translation
= 1.5.3 =
* Add Hebrew translation
= 1.5.2 =
* Fix a compatibility problem with WPML (more details here : http://wpml.org/forums/topic/wp-sitemap-page/ )
= 1.5.1 =
* Update the translation for the following language: French, Russian, German, Sweden, Italian, Spanish, Hungarian and Portuguese.
= 1.5.0 =
* Add the taxonomy
* Feature to add a nofollow attribute to the page, post and custom post type links
* Feature to show only the private pages
= 1.4.0 =
* Fix bug about the missing &lt;ul&gt; tags
* Fix the class attribute name of some &lt;h2&gt; tags
* Do not display the titles with the attribute display_title="false". It give the opportunity to create a sitemap in any languages of your choice
* Improve the functionnality to exclude pages and posts
* Possibility to exclude the content protected by password
* Add some hooks to help others developers to adapt the sitemap
= 1.3.0 =
* Possibility to add a sitemap with any of these elements : category, tag, archive or author
* Improve the back-office interface
* Possibility to add a link about the plugin at the end of the sitemap
* Update of the translation
= 1.1.2 =
* Filter only one kind of content using the shortcode with the attribute "only"
* Add languages : Finnish and Hungarian
= 1.1.1 =
* Add languages : Deutsch, Swedish and Brazilian Portuguese
= 1.1.0 =
* Add archives pages on the sitemap (optional)
* Add authors pages on the sitemap (optional)
* Improve the security
* Add polish language
= 1.0.12 =
* Add czech language
= 1.0.11 =
* Do not display duplicate entries when user are using some plugins such as WPML
= 1.0.10 =
* Add spanish language
= 1.0.9 =
* Add italian language
= 1.0.8 =
* Possibility to exclude all the posts, all the pages or any Custom Post Type
* Add persian language
= 1.0.7 =
* Fix a problem with the Custom Post Type that are not hierarchical
= 1.0.6 =
* Add and update some translation (Russian and Dutch)
* Add the Custom Post Type on the sitemap. There was an error in the 1.0.4 version
= 1.0.5 =
* Possibility to exclude some pages
* Fix some translation bug
* Add the Russian language, French (Belgium, Switzerland, Luxembourg, Canada)
= 1.0.4 =
* Fix some bug
* Include the Custom Post Type on the sitemap
* Possibility to customize the way the posts will be displayed
= 1.0.3 =
* Fix a translation error
= 1.0.2 =
* Fix a bug of a function that has been renamed.
= 1.0.1 =
* Add french translation
= 1.0 =
* Initial Release.


== Upgrade notice ==


== How to uninstall WP Sitemap Page ==
To uninstall WP Sitemap Page, you just have to de-activate the plugin from the plugins list.
