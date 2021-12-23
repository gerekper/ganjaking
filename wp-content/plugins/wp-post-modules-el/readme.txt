=== WP Post Modules ===

Author: SaurabhSharma
Author URI: http://codecanyon.net/user/saurabhsharma
Tags: post modules, post snippets, wp query
Requires at least: 4.3
Tested up to: 5.8.2
Stable tag: 5.8.2


== Description ==

WP Post Modules is an Elementor addon for creating online magazine content, newspaper websites, creative portfolios and blog, effortlessly.

The plugin lets you create post modules with drag and drop UI and intuitive customization settings. With multiple display styles and styling options, the plugin is envisioned to provide an all-purpose tool for creating newspaper/magazine like post sections.


== Key features ==

*   100% responsive design, optimized for retina display
*   10 Pre built Home page layouts (shipped as Elementor JSON template files)
*   Multiple Display styles
	Grid
	- 4 sub styles
    - Unlimited multiple columns
    - Custom Gutters
    List
	- Custom split ratio for thumbnail + content
    - Create any sized list from small to large thumbnails
    - Circular images for list thumbnails
    - Content can be separated as full border, half border or no border
    - Multi Column lists
    Tile
    - Multiple columns
    - Custom gutters
    - Overlay Content with custom background color and gradients
    - Custom overlay content margin/padding
    - Show overlay as always, on-hover or never
    - Image zoom and rotate effect on hover
    Ticker
    - Custom ticker title length
    - Custom ticker animation duration
    - Custom ticker label text
    - Custom ticker label background and foreground with color picker
    Bullet List
    - Plain list with bullets
    - Bullet size and color options
    - List spacing options
*   Responsive controls for typography, columns, grid sizes, gutters, etc.
*   Advanced Typography options with complete Google Fonts library
    - Set font size, line height, font weight, letter spacing, etc.
    - Responsive controls for typography
    - Custom colors for titles, excerpt, category links, post meta, etc.
*   Custom WP Query builder
	- Show posts from categories, tags, custom post types, taxonomies, post IDs, author IDs, etc.
	- Supports offset and post exclusion to prevent redundancy of posts
	- Order and Orderby parameters supported
*    jQuery Owl Slider
	- Supported for Grid, List and Tile display styles and their sub styles
    - Change number of slides per view
    - Toggle animation loop
    - Toggle auto play
    - Set autoplay timeout
    - Set animation speed and animation types (slide, zoom and fade)
    - Toggle auto height
    - Toggle previous/next navigation
    - Toggle dots navigation
    - Stagepadding support
*    Image resize on-the-fly using BFI Thumb
	- Custom width, height and hard crop feature
    - Set image quality from 1 to 100 (Optimization Option)
    - Auto convert images to grayscale
    - Auto colorize image from color picker
*    Text content options
	- Choose heading tag from h1 to h6 and p (SEO factor)
    - Choose main title font size and font weight
    - Set paragraph tag and font size
    - Auto trimming of post excerpt at desired word length
    - Toggle readmore link with custom readmore text
    - Show/hide post meta items like category, author, date, excerpt and comments
    - Auto conversion of category links into expandable dropdown if category links are more than a specifid number
    - Show author avatar in post meta
    - Built in style and function support for WP Review and Post Views Counter plugin
*	Social sharing per post module. (Twitter, Facebook, WhatsApp, LinkedIn, GooglePlus, Pinterest, VK, Reddit, Email)
*   Valid Schema microdata on generated post content
	- Schema properties can be changed from widget settings
*   AJAX next/previous navigation and loadmore feature for grid, list and tile display styles
*   W3C valid HTML markup for plugin generated data
*   Compatible on all major browsers including IE 9 or above
*   Translation ready with POT and PO/MO files included
*   Step by step documentation manual for plugin installation and usage
*   Professional and dedicated support with fast response time


== Plugin Support ==

All support is provided via comments section and email. For any questions related to the plugin or general query, feel free to email me from my profile page message box at http://codecanyon.net/user/saurabhsharma, or comment on the item comments section. I would be glad to respond. Thank you for browsing the plugin.


== Credits ==

The plugin uses following files and resources, as listed:

jQuery Easing
http://gsgd.co.uk/sandbox/jquery/easing/

jQuery Marquee
@author Aamir Afridi - aamirafridi(at)gmail(dot)com / http://aamirafridi.com/jquery/jquery-marquee-plugin

jQuery Owl Carousel
https://github.com/OwlCarousel2/

BFI Thumb
(c) 2013 Benjamin F. Intal / Gambit

Live demo images
www.pexels.com
www. unsplash.com


== Installation ==

For installation and setup, please refer to the documentation/index.html file inside your
main download archive.

== Changelog ==

= 1.9.0 =
* Added "Filter by time period" option
    - Filter posts by today, yesterday, last 7 days, current month, previous month, current year and previous year.
    - WP Post Modules > Content > Query > Filter by time period
* Fixed: Deprecated functions of Elementor widget replaced by latest ones
* Ensured compatibility with WordPress 5.8.2 and Elementor 3.4.x

= 1.8.1 =
* Fixed: Video embeds should show when "Show Embed" is enabled
    - Earlier it required to set post format as "Video"
    - Now it works for "Standard" post format too
* Fixed: Added default width and height for images if no dimensions are provided in module settings
* Tweak: The "BFI Thumb" and "Hard Crop" now set to 'false' by default

= 1.8.0 =
* Added option for 'Filter by Author' on Author archives page
    - See WP Post Modules > Content > Query > Author archive filtering
 (This option is useful when module shortcode is placed in widget areas on author archive page. Module shortcode can be generated using Elementor Pro or Anywhere Elementor plugin ).
* Added option for left/center/right alignment of Ajax Navigation buttons
    - See WP Post Modules > Style > Ajax Options > Navigation Align
* Added absolute position option for Author Avatar
    - See WP Post Modules > Style > Post Meta > Avatar Position Absolute
* Fixed: php notice for undefined publisher logo
* Tweak: Removed overflow: hidden from the wppm-grid container
    - Using a box shadow on the container causes shadow to clip. Now it is fixed.
** Important: Please clear browser caches after updating the plugin

= 1.7.0 =
* Added option for equal height columns
    - See WP Post Modules > Style > Display > Equal Height Columns
* Added options for controlling author avatar size, margin and border radius
    - See WP Post Modules > Style > Post Meta > Author Avatar (Enable this toggle to see more options below)
* Fixed: Content box shadow hiding within parent containers

= 1.6.0 =
* Added text shadow option for post titles
    - See WP Post Modules > Style > Title > Text Shadow
* Ensured compatibility with WordPress 5.5

= 1.5.3 =
* Added vendor specific CSS Flexbox properties for cross-browser compatibility
* Removed clearfix ::before ::after hack from layouts

= 1.5.2 =
* Fixed: Twitter share code updated as per latest Twitter API

= 1.5.1 =
* Fixed: Ticker(Marquee) not working in RTL pages
* Fixed: Replaced "break-word" deprecated property with "break-all" for word-break

= 1.5.0 =
* Added custom field option for image source
    - See WP Post Modules > Style > Images > Image Source
* Added link options for post image
    - See WP Post Modules > Style > Images > Image Link
    - Choose as permalink, media file or none
* Added lightbox support for post images
    - See WP Post Modules > Style > Images > Image Link (Choose Media File) > Lightbox
* Added custom field option for post title
    See WP Post Modules > Style > Title > Title Source
* Ensured compatibility with Advanced Custom Fields Plugin
    - Custom fields generated via ACF supported on post image, title and excerpt

= 1.4.0 =
* Added responsive controls for the following settings:
    - Category links padding and margin
    - Ajax Load More button padding and margin
    - Ajax loadmore button border radius
    - Ajax navigation buttons padding and margin
    - Post title margin
    - Excerpt Margin
    - Category links row margin
    - Post meta row margin
    - Social links outer margin (inline style)
* Added 2 new demo layouts
    - Check /dummy_data/demos/demo_11.json and demo_12.json

= 1.3.1 =
* Added option for Post Meta alignment
    - See WP Post Modules > Style > Post Meta > Post Meta Align

= 1.3.0 =
* Added Responsive control for Image Alignment in list style template
    - See WP Post Modules > Style > Display > Image Alignment
* Added Native HTML support in post titles (bold, italics, etc.)
* Fixed: Design compatibility on IE11
    - Important: Please cross-check number of columns for mobile view in grid and list style modules

= 1.2.0 =
* Added style support for languages with right-to-left orientation
* Added option to enable/disable BFI thumb
    - See WP Post Modules > Style > Images > Enable BFI Thumb
    - If BFI thumb is disabled, native WordPress Thumbnails will be used
    - Native thumbnails with sourceset supported when BFI is disabled
    - Supports OTF Regenerate thumbnails if BFI disabled
    - Performs faster than BFI Thumb resize
* Added customization options for Post Thumbnail Caption
    - See WP Post Modules > Style > Images > Post Thumbnail Caption
    - Added margin dimension field
    - Added typography option
    - Added text align option
    - Added foreground and background color option for caption text

= 1.1.1 =
* Fixed: Duplicate post IDs when same post is shown multiple times on a page


= 1.1.0 =
* Added text trim support for languages that count 'words' by the individual character (such as East Asian languages)
    - Now uses wp_trim_words() native function

= 1.0.2 =
* Added: "overflow: hidden" property to the .wppm-el-post class
* Fixed: Single post term filtering returning no posts in back-end

= 1.0.1 =
* Added missing demos.zip file in dummy_data folder

= 1.0.0 =
* initial relese