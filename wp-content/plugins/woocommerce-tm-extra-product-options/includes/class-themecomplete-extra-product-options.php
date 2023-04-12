<?php
/**
 * Extra Product Options main class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options main class
 *
 * This class is responsible for displaying the Extra Product Options on the frontend.
 *
 * @package Extra Product Options/Classes
 * @author  ThemeComplete
 * @version 6.0
 */
final class THEMECOMPLETE_Extra_Product_Options {

	/**
	 * Holds the current post id
	 *
	 * @var int|bool
	 */
	private $postid_pre = false;

	/**
	 * Helper for determining various conditionals
	 *
	 * @var array
	 */
	public $wc_vars = [
		'is_product'             => false,
		'is_shop'                => false,
		'is_product_category'    => false,
		'is_product_tag'         => false,
		'is_cart'                => false,
		'is_checkout'            => false,
		'is_account_page'        => false,
		'is_ajax'                => false,
		'is_page'                => false,
		'is_order_received_page' => false,
	];

	/**
	 * Product custom settings
	 *
	 * @var array
	 */
	public $tm_meta_cpf = [];

	/**
	 * Product custom settings options
	 *
	 * @var array
	 */
	public $meta_fields = [
		'exclude'                  => '',
		'price_override'           => '',
		'override_display'         => '',
		'override_final_total_box' => '',
		'override_enabled_roles'   => '',
		'override_disabled_roles'  => '',
	];

	/**
	 * Cache for all the extra options
	 *
	 * @var array
	 */
	private $cpf = [];

	/**
	 * Options cache
	 *
	 * @var array
	 */
	private $cpf_single = [];
	/**
	 * Options cache for prices
	 *
	 * @var array
	 */
	private $cpf_single_epos_prices = [];
	/**
	 * Options cache for the variation element id
	 *
	 * @var array
	 */
	private $cpf_single_variation_element_id = [];
	/**
	 * Options cachefor the variation section id
	 *
	 * @var array
	 */
	private $cpf_single_variation_section_id = [];

	/**
	 * Holds the upload directory for the upload element
	 *
	 * @var string
	 */
	public $upload_dir = '/extra_product_options/';

	/**
	 * Holds the upload files objects
	 *
	 * @var array
	 */
	private $upload_object = [];

	/**
	 * Replacement name for cart fee fields
	 *
	 * @var string
	 */
	public $cart_fee_name = 'tmcartfee_';
	/**
	 * Replacement class name for cart fee fields
	 *
	 * @var string
	 */
	public $cart_fee_class = 'tmcp-fee-field';

	/**
	 * Array of element types that get posted
	 *
	 * @var array
	 */
	public $element_post_types = [];

	/**
	 * Holds builder element attributes
	 *
	 * @var array
	 */
	private $tm_original_builder_elements = [];

	/**
	 * Holds modified builder element attributes
	 *
	 * @var array
	 */
	public $tm_builder_elements = [];

	/**
	 * Holds the cart key when editing a product in the cart
	 * This isn't in our cart class becuase it needed to be initialized
	 * before the plugins_loaded hook.
	 *
	 * @var string|bool
	 */
	public $cart_edit_key = null;

	/**
	 * Containes current option features
	 *
	 * @var array
	 */
	public $current_option_features = [];

	/**
	 * Holds all of the plugin settings
	 *
	 * @var array
	 */
	private $tm_plugin_settings = [];

	/**
	 * Enable/disable flag for outputing plugin specific classes
	 * to the post_class filter
	 *
	 * @var bool
	 */
	private $tm_related_products_output = true;

	/**
	 * Enable/disable flag for checking if we are in related/upsells
	 *
	 * @var bool
	 */
	private $in_related_upsells = false;

	/**
	 * Cart edit key
	 *
	 * @var string
	 */
	public $cart_edit_key_var = 'tm_cart_item_key';

	/**
	 * Cart edit key alternative
	 *
	 * @var string
	 */
	public $cart_edit_key_var_alt = 'tc_cart_edit_key';

	/**
	 * Contains min/man product infomation
	 *
	 * @var array
	 */
	public $product_minmax = [];

	/**
	 * Current free text replacement
	 *
	 * @var string
	 */
	public $current_free_text = '';

	/**
	 * Current free text replacement for associated products
	 *
	 * @var string
	 */
	public $assoc_current_free_text = '';

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @var bool
	 */
	public $is_in_product_shortcode;

	/**
	 * Flag to check if we are in a product loop
	 *
	 * @var bool
	 */
	public $is_in_product_loop;

	/**
	 * Flag to fix several issues when the woocommerce_get_price hook
	 * isn't being used correct by themes or other plugins.
	 *
	 * @var int
	 */
	private $tm_woocommerce_get_price_flag = 0;

	/**
	 * Visible elements cache
	 *
	 * @var array
	 */
	private $visible_elements = [];

	/**
	 * Current element that is being checked if it is visible
	 *
	 * @var array
	 */
	private $current_element_to_check = [];

	/**
	 * If the current product is a composite product
	 *
	 * @var bool
	 */
	public $is_bto = false;

	/**
	 * If we are in the associated product options
	 *
	 * @var bool
	 */
	public $is_inline_epo = false;

	/**
	 * If we are in an associated product
	 *
	 * @var bool
	 */

	public $is_associated = false;

	/**
	 * If associated product is priced individually
	 *
	 * @var int|null
	 */
	public $associated_per_product_pricing = null;

	/**
	 * Associated product type
	 *
	 * @var int|null
	 */
	public $associated_type = false;

	/**
	 * The element id of the associated product
	 *
	 * @var int|null
	 */
	public $associated_element_uniqid = false;

	/**
	 * The associated product counter when adding more than one product
	 * in the same product element.
	 *
	 * @var int|null
	 */
	public $associated_product_counter = false;

	/**
	 * The associated product form prefix when adding more than one product
	 * in the same product element.
	 *
	 * @var int|null
	 */
	public $associated_product_formprefix = false;

	/**
	 * Holds all of the lookup tables.
	 *
	 * @var array
	 */
	public $lookup_tables = [];

	/**
	 * Enable front-end for roles
	 * Select the roles that will have access to the extra options.
	 *
	 * @var string
	 */
	public $tm_epo_roles_enabled = '@everyone';

	/**
	 * Disable front-end for roles
	 * Select the roles that will not have access to the extra options.
	 *
	 * @var string
	 */
	public $tm_epo_roles_disabled = '';

	/**
	 * Enable translations
	 * This will enable the default plugin translation using the pot files.
	 *
	 * @var string
	 */
	public $tm_epo_enable_translations = 'yes';

	/**
	 * Post type hook priority
	 * Do not change this unless you know how it will affect your site!
	 * This is the priority which the post types are loaded by the plugin.
	 *
	 * @var string
	 */
	public $tm_epo_post_type_hook_priority = '';

	/**
	 * Final total box
	 * Select when to show the final total box
	 *
	 * @var string
	 */
	public $tm_epo_final_total_box = 'normal';

	/**
	 * Enable Final total box for all products
	 * Show the Final total box even when the product has no extra options
	 *
	 * @var string
	 */
	public $tm_epo_enable_final_total_box_all = 'no';

	/**
	 * Enable original final total display
	 * Check to enable the display of the undiscounted final total
	 *
	 * @var string
	 */
	public $tm_epo_enable_original_final_total = 'no';

	/**
	 * Enable options VAT display
	 * Check to display the options VAT amount above the options total
	 *
	 * @var string
	 */
	public $tm_epo_enable_vat_options_total = 'no';

	/**
	 * Show Unit price on totals box
	 * Enable this to display the unit price when the totals box is visible
	 *
	 * @var string
	 */
	public $tm_epo_show_unit_price = 'no';

	/**
	 * Include Fees on unit price
	 * Enable this to add any Fees to the unit price
	 *
	 * @var string
	 */
	public $tm_epo_fees_on_unit_price = 'no';

	/**
	 * Total price as Unit Price
	 * Make the total price not being multiplied by the product quantity
	 *
	 * @var string
	 */
	public $tm_epo_total_price_as_unit_price = 'no';

	/**
	 * Disable lazy load images
	 * Enable this to disable lazy loading images.
	 *
	 * @var string
	 */
	public $tm_epo_no_lazy_load = 'yes';

	/**
	 * Preload lightbox images
	 * Enable this to preload the image when using the lightbox feature.
	 *
	 * @var string
	 */
	public $tm_epo_preload_lightbox_image = 'no';

	/**
	 * Enable plugin for WooCommerce shortcodes
	 * Enabling this will load the plugin files to all WordPress pages.
	 * Use with caution.
	 *
	 * @var string
	 */
	public $tm_epo_enable_shortcodes = 'no';

	/**
	 * Enable shortcodes in options strings
	 * Enabling this will allow the use of shortcodes and HTML code
	 * in the options label and description text.
	 *
	 * @var string
	 */
	public $tm_epo_enable_data_shortcodes = 'yes';

	/**
	 * Display
	 * This controls how your fields are displayed on the front-end.
	 * If you choose "Show using action hooks" you have to manually
	 * write the code to your theme or plugin to display the fields
	 * and the placement settings below will not work.
	 * If you use the Composite Products extension you must leave this
	 * setting to "Normal" otherwise the extra options cannot be displayed
	 * on the composite product bundles. See more at the documentation.
	 *
	 * @var string
	 */
	public $tm_epo_display = 'normal';

	/**
	 * Extra Options placement
	 * Select where you want the extra options to appear.
	 *
	 * @var string
	 */
	public $tm_epo_options_placement = 'woocommerce_before_add_to_cart_button';

	/**
	 * Extra Options placement custom hook
	 *
	 * @var string
	 */
	public $tm_epo_options_placement_custom_hook = '';

	/**
	 * Extra Options placement hook priority
	 * Select the Extra Options placement hook priority
	 *
	 * @var string|int
	 */
	public $tm_epo_options_placement_hook_priority = '50';

	/**
	 * Totals box placement
	 * Select where you want the Totals box to appear.
	 *
	 * @var string
	 */
	public $tm_epo_totals_box_placement = 'woocommerce_before_add_to_cart_button';

	/**
	 * Totals box placement custom hook
	 *
	 * @var string
	 */
	public $tm_epo_totals_box_placement_custom_hook = '';

	/**
	 * Totals box placement hook priority
	 * Select the Totals box placement hook priority
	 *
	 * @var string|int
	 */
	public $tm_epo_totals_box_placement_hook_priority = '50';

	/**
	 * Floating Totals box
	 * This will enable a floating box to display your totals box.
	 *
	 * @var string
	 */
	public $tm_epo_floating_totals_box = 'disable';

	/**
	 * Floating Totals box visibility
	 * This determines the floating totals box visibility.
	 *
	 * @var string
	 */
	public $tm_epo_floating_totals_box_visibility = 'always';

	/**
	 * Pixels amount needed to scroll
	 * Select the number of pixels the page needs to scroll for the
	 * floating totals to become visible.
	 *
	 * @var string|int
	 */
	public $tm_epo_floating_totals_box_pixels = '100';

	/**
	 * Add to cart button on floating totals box
	 * Display the add to cart button on floating box.
	 *
	 * @var string
	 */
	public $tm_epo_floating_totals_box_add_button = 'no';

	/**
	 * Change original product price
	 * Check to overwrite the original product price when the price is changing.
	 *
	 * @var string
	 */
	public $tm_epo_change_original_price = 'no';

	/**
	 * Change variation price
	 * Check to overwrite the variation price when the price is changing.
	 *
	 * @var string
	 */
	public $tm_epo_change_variation_price = 'no';

	/**
	 * Force Select Options
	 * This changes the add to cart button on shop and archive pages to
	 * display select options when the product has extra product options.
	 * Enabling this will remove the ajax functionality.
	 *
	 * @var string
	 */
	public $tm_epo_force_select_options = 'no';

	/**
	 * Enable extra options in shop and category view
	 * Check to enable the display of extra options on the shop page and
	 * category view. This setting is theme dependent and some aspects
	 * may not work as expected.
	 *
	 * @var string
	 */
	public $tm_epo_enable_in_shop = 'no';

	/**
	 * Remove Free price label
	 * Check to remove Free price label when product has extra options
	 *
	 * @var string
	 */
	public $tm_epo_remove_free_price_label = 'no';

	/**
	 * Use progressive display on options
	 * Enabling this will hide the options on the product page until
	 * JavaScript is initialized. This is a fail-safe setting and
	 * we recommend to be active.
	 *
	 * @var string
	 */
	public $tm_epo_progressive_display = 'yes';

	/**
	 * Animation delay
	 * How long the animation will take in milliseconds
	 *
	 * @var string|float
	 */
	public $tm_epo_animation_delay = '100';

	/**
	 * Start Animation delay
	 * The delay until the animation starts in milliseconds
	 *
	 * @var string
	 */
	public $tm_epo_start_animation_delay = '0';

	/**
	 * Show quantity selector only for elements with a value
	 * Check show quantity selector only for elements with a value.
	 *
	 * @var string
	 */
	public $tm_epo_show_only_active_quantities = 'yes';

	/**
	 * Hide add-to-cart button until an element is chosen
	 * Check this to show the add to cart button only when at least
	 * one option is filled.
	 *
	 * @var string
	 */
	public $tm_epo_hide_add_cart_button = 'no';

	/**
	 * Hide add-to-cart button until all required elements are chosen
	 * Check this to show the add to cart button only when all required
	 * visible options are filled.
	 *
	 * @var string
	 */
	public $tm_epo_hide_required_add_cart_button = 'no';

	/**
	 * Hide add-to-cart button until all elements are chosen
	 * Check this to show the add to cart button only when all visible
	 * options are filled.
	 *
	 * @var string
	 */
	public $tm_epo_hide_all_add_cart_button = 'no';

	/**
	 * Show full width label for elements.
	 * Check this to force elements to be full width instead of auto.
	 *
	 * @var string
	 */
	public $tm_epo_select_fullwidth = 'yes';

	/**
	 * Show choice description inline.
	 * Check this to disable showing description as a tooltip and
	 * show it inline instead.
	 *
	 * @var string
	 */
	public $tm_epo_description_inline = 'no';

	/**
	 * Hide choice label when using the Show tooltip setting for radio
	 * buttons and checkboxes
	 * Check this to hide the choice label when using the Show tooltip
	 * setting for radio buttons and checkboxes.
	 *
	 * @var string
	 */
	public $tm_epo_swatch_hide_label = 'yes';

	/**
	 * Auto hide price if zero
	 * Check this to globally hide the price display if it is zero.
	 *
	 * @var string
	 */
	public $tm_epo_auto_hide_price_if_zero = 'no';

	/**
	 * Trim zeros in prices
	 * Check this to globally trim zero in prices.
	 * This will be applied to native WooCommerce prices as well.
	 *
	 * @var string
	 */
	public $tm_epo_trim_zeros = 'no';

	/**
	 * Hide element price html when hide price setting is enabled
	 * Check this if you use Google Merchant Center.
	 * It will hide the price html of the element when you enable
	 * its hide price setting.
	 *
	 * @var string
	 */
	public $tm_epo_hide_price_html = 'yes';

	/**
	 * Show prices inside select box choices
	 * Check this to show the price of the select box options
	 * if the price type is fixed.
	 *
	 * @var string
	 */
	public $tm_epo_show_price_inside_option = 'no';

	/**
	 * Show prices inside select box choices even if the prices are hidden
	 * Check this to show the price of the select box options
	 * if the price type is fixed and even if the element hides the price.
	 *
	 * @var string
	 */
	public $tm_epo_show_price_inside_option_hidden_even = 'no';

	/**
	 * Multiply prices inside select box choices with its quantity selector
	 * Check this to multiply the prices of the select box options
	 * with its quantity selector if any.
	 *
	 * @var string
	 */
	public $tm_epo_multiply_price_inside_option = 'yes';

	/**
	 * Use translated values when possible on admin Order
	 * Please note that if the options on the Order change
	 * or get deleted you will get wrong results by enabling this!
	 *
	 * @var string
	 */
	public $tm_epo_wpml_order_translate = 'no';

	/**
	 * Include option pricing in product price
	 * Check this to include the pricing of the options to the product price.
	 *
	 * @var string
	 */
	public $tm_epo_include_possible_option_pricing = 'no';

	/**
	 * Check for empty product price
	 * Check this to have the plugin set to zero the
	 * product price when it is empty.
	 *
	 * @var string
	 */
	public $tm_epo_add_product_price_check = 'yes';

	/**
	 * Use the "From" string on displayed product prices
	 * Check this to alter the price display of a product when it
	 * has extra options with prices.
	 *
	 * @var string
	 */
	public $tm_epo_use_from_on_price = 'no';

	/**
	 * Alter generated product structured data
	 * Alters the generated product structured data.
	 * This may produce wrong results if the options use conditional logic!
	 *
	 * @var string
	 */
	public $tm_epo_alter_structured_data = 'no';

	/**
	 * Responsive options structure
	 * Enable this if you want the options to have responsive display.
	 *
	 * @var string
	 */
	public $tm_epo_responsive_display = 'yes';

	/**
	 * Turn off persistent cart
	 * Enable this if the product has a lot of options.
	 *
	 * @var string
	 */
	public $tm_epo_turn_off_persi_cart = 'no';

	/**
	 * Clear cart button
	 * Enables or disables the clear cart button
	 *
	 * @var string
	 */
	public $tm_epo_clear_cart_button = 'no';

	/**
	 * Cart Field Display
	 * Select how to display your fields in the cart
	 *
	 * @var string
	 */
	public $tm_epo_cart_field_display = 'normal';

	/**
	 * Hide extra options in cart
	 * Enables or disables the display of options in the cart.
	 *
	 * @var string
	 */
	public $tm_epo_hide_options_in_cart = 'no';

	/**
	 * Hide extra options prices in cart
	 * Enables or disables the display of prices of options in the cart.
	 *
	 * @var string
	 */
	public $tm_epo_hide_options_prices_in_cart = 'no';

	/**
	 * Prevent negative priced products
	 * Prevent adding to the cart negative priced products.
	 *
	 * @var string
	 */
	public $tm_epo_no_negative_priced_products = 'no';

	/**
	 * Prevent zero priced products
	 * Prevent adding to the cart zero priced products.
	 *
	 * @var string
	 */
	public $tm_epo_no_zero_priced_products = 'no';

	/**
	 * Hide checkbox element average price
	 * This will hide the average price display on the cart for checkboxes.
	 *
	 * @var string
	 */
	public $tm_epo_hide_cart_average_price = 'yes';

	/**
	 * Show image replacement in cart and checkout
	 * Enabling this will show the images of elements that have
	 * an image replacement.
	 *
	 * @var string
	 */
	public $tm_epo_show_image_replacement = 'no';

	/**
	 * Hide upload file URL in cart and checkout
	 * Enabling this will hide the URL of any uploaded file while
	 * in cart and checkout.
	 *
	 * @var string
	 */
	public $tm_epo_show_hide_uploaded_file_url_cart = 'no';

	/**
	 * Show uploaded image in cart and checkout
	 * Enabling this will show the uploaded images in cart and checkout.
	 *
	 * @var string
	 */
	public $tm_epo_show_upload_image_replacement = 'yes';

	/**
	 * Maximum image width
	 * Set the maximum width of the images that appear on cart.
	 *
	 * @var string
	 */
	public $tm_epo_global_image_max_width = '70%';

	/**
	 * Maximum image height
	 * Set the maximum height of the images that appear on cart.
	 *
	 * @var string
	 */
	public $tm_epo_global_image_max_height = 'none';

	/**
	 * Always use unique values on cart for elements
	 * Enabling this will separate comma separated values for elements.
	 * This is mainly used for multiple checkbox choices.
	 *
	 * @var string
	 */
	public $tm_epo_always_unique_values = 'no';

	/**
	 * Post types to show the saved options
	 * Select the post types where the plugin will modify the
	 * edit order screen to show the saved options.
	 * You can type in your custom post type.
	 *
	 * @var string
	 */
	public $tm_epo_order_post_types = 'shop_order';

	/**
	 * Strip html from emails
	 * Check to strip the html tags from emails
	 *
	 * @var string
	 */
	public $tm_epo_strip_html_from_emails = 'yes';

	/**
	 * Hide uploaded file path
	 * Check to hide the uploaded file path from users (in the Order).
	 *
	 * @var string
	 */
	public $tm_epo_hide_upload_file_path = 'yes';

	/**
	 * Legacy meta data
	 * Check to enable legacy meta data functionality.
	 *
	 * @var string
	 */
	public $tm_epo_legacy_meta_data = 'no';

	/**
	 * Unique meta values
	 * Check to split items with multiple values to unique lines.
	 *
	 * @var string
	 */
	public $tm_epo_unique_meta_values = 'no';

	/**
	 * Prevent options from being sent to emails
	 * Check to disable options from being sent to emails.
	 *
	 * @var string
	 */
	public $tm_epo_global_prevent_options_from_emails = 'no';

	/**
	 * Disable sending the options upon saving the order
	 * Enable this if you are getting a 500 error when trying to
	 * complete the order in the checkout.
	 *
	 * @var string
	 */
	public $tm_epo_disable_sending_options_in_order = 'no';

	/**
	 * Attach upload files to emails
	 * Check to Attach upload files to emails.
	 *
	 * @var string
	 */
	public $tm_epo_global_attach_uploaded_to_emails = 'yes';

	/**
	 * Disable Options on Order status change
	 * Check this only if you are getting server errors on checkout.
	 *
	 * @var string
	 */
	public $tm_epo_disable_options_on_order_status = 'no';

	/**
	 * Hide upload file URL in order
	 * Enabling this will hide the URL of any uploaded file while in order.
	 *
	 * @var string
	 */
	public $tm_epo_show_hide_uploaded_file_url_order = 'no';

	/**
	 * Cart field/value separator
	 * Enter the field/value separator for the cart.
	 *
	 * @var string
	 */
	public $tm_epo_separator_cart_text = ':';

	/**
	 * Option multiple value separator in cart
	 * Enter the value separator for the option that have multiple
	 * values like checkboxes.
	 *
	 * @var string
	 */
	public $tm_epo_multiple_separator_cart_text = ' ';

	/**
	 * Update cart text
	 * Enter the Update cart text when you edit a product.
	 *
	 * @var string
	 */
	public $tm_epo_update_cart_text = '';

	/**
	 * Edit Options text replacement
	 * Enter a text to replace the Edit options text on the cart.
	 *
	 * @var string
	 */
	public $tm_epo_edit_options_text = '';

	/**
	 * Additional Options text replacement
	 * Enter a text to replace the Additional options text when using
	 * the pop up setting on the cart.
	 *
	 * @var string
	 */
	public $tm_epo_additional_options_text = '';

	/**
	 * Close button text replacement
	 * Enter a text to replace the Close button text when using
	 * the pop up setting on the cart.
	 *
	 * @var string
	 */
	public $tm_epo_close_button_text = '';

	/**
	 * Empty cart text
	 * Enter a text to replace the empty cart button text.
	 *
	 * @var string
	 */
	public $tm_epo_empty_cart_text = '';

	/**
	 * Final total text
	 * Enter the Final total text or leave blank for default.
	 *
	 * @var string
	 */
	public $tm_epo_final_total_text = '';

	/**
	 * Unit price text
	 * Enter the Unit price text or leave blank for default.
	 *
	 * @var string
	 */
	public $tm_epo_options_unit_price_text = '';

	/**
	 * Options total text
	 * Enter the Options total text or leave blank for default.
	 *
	 * @var string
	 */
	public $tm_epo_options_total_text = '';

	/**
	 * Options VAT total text
	 * Enter the Options VAT total text or leave blank for default.
	 *
	 * @var string
	 */
	public $tm_epo_vat_options_total_text = '';

	/**
	 * Fees total text
	 * Enter the Fees total text or leave blank for default.
	 *
	 * @var string
	 */
	public $tm_epo_fees_total_text = '';

	/**
	 * Free Price text replacement
	 * Enter a text to replace the Free price label when product has extra options.
	 *
	 * @var string
	 */
	public $tm_epo_replacement_free_price_text = '';

	/**
	 * Force Select options text
	 * Enter a text to replace the add to cart button text when using
	 * the Force select option.
	 *
	 * @var string
	 */
	public $tm_epo_force_select_text = '';

	/**
	 * No zero priced products text
	 * Enter a text to replace the message when trying to add
	 * a zero priced product to the cart.
	 *
	 * @var string
	 */
	public $tm_epo_no_zero_priced_products_text = '';

	/**
	 * No negative priced products text
	 * Enter a text to replace the message when trying to add
	 * a negative priced product to the cart.
	 *
	 * @var string
	 */
	public $tm_epo_no_negative_priced_products_text = '';

	/**
	 * Popup section button text replacement
	 * Enter a text to replace the topup section button text.
	 *
	 * @var string
	 */
	public $tm_epo_popup_section_button_text = '';

	/**
	 * Reset Options text replacement
	 * Enter a text to replace the Reset options text when
	 * using custom variations.
	 *
	 * @var string
	 */
	public $tm_epo_reset_variation_text = '';

	/**
	 * Calendar close button text replacement
	 * Enter a text to replace the Close button text on the calendar.
	 *
	 * @var string
	 */
	public $tm_epo_closetext = '';

	/**
	 * Calendar today button text replacement
	 * Enter a text to replace the Today button text on the calendar.
	 *
	 * @var string
	 */
	public $tm_epo_currenttext = '';

	/**
	 * Slider previous text
	 * Enter a text to replace the previous button text for slider.
	 *
	 * @var string
	 */
	public $tm_epo_slider_prev_text = '';

	/**
	 * Slider next text
	 * Enter a text to replace the next button text for slider.
	 *
	 * @var string
	 */
	public $tm_epo_slider_next_text = '';

	/**
	 * This field is required text
	 * Enter a text to indicate that a field is required.
	 *
	 * @var string
	 */
	public $tm_epo_this_field_is_required_text = '';

	/**
	 * Characters remaining text
	 * Enter a text to replace the Characters remaining text when
	 * using maximum characters on a text field or a textarea.
	 *
	 * @var string
	 */
	public $tm_epo_characters_remaining_text = '';

	/**
	 * Uploading files text
	 * Enter a text to replace the Uploading files text used in the
	 * pop-up after clicking the add to cart button when there are upload fields.
	 *
	 * @var string
	 */
	public $tm_epo_uploading_files_text = '';

	/**
	 * Uploading message text
	 * Enter a message to be used in the pop-up after clicking the
	 * add to cart button when there are upload fields.
	 *
	 * @var string
	 */
	public $tm_epo_uploading_message_text = '';

	/**
	 * Select file text
	 * Enter a text to replace the Select file text used in the
	 * styled upload button.
	 *
	 * @var string
	 */
	public $tm_epo_select_file_text = '';

	/**
	 * Single file text
	 * Enter a text to replace the file text used in the styled upload button.
	 *
	 * @var string
	 */
	public $tm_epo_uploading_num_file = '';

	/**
	 * Multiple files text
	 * Enter a text to replace the files text used in the styled upload button.
	 *
	 * @var string
	 */
	public $tm_epo_uploading_num_files = '';

	/**
	 * Add button text on associated products
	 * Enter a text to replace the add button text on associated products.
	 *
	 * @var string
	 */
	public $tm_epo_add_button_text_associated_products = '';

	/**
	 * Remove button text on associated products
	 * Enter a text to replace the remove button text on associated products.
	 *
	 * @var string
	 */
	public $tm_epo_remove_button_text_associated_products = '';

	/**
	 * Repeater add text
	 * Enter a text to replace the add text button for repeater fields.
	 *
	 * @var string
	 */
	public $tm_epo_add_button_text_repeater = '';

	/**
	 * Enable checkbox and radio styles
	 * Enables or disables extra styling for checkboxes and radio buttons.
	 *
	 * @var string
	 */
	public $tm_epo_css_styles = 'no';

	/**
	 * Style
	 * Select a style for the checkboxes and radio buttons
	 *
	 * @var string
	 */
	public $tm_epo_css_styles_style = 'round';

	/**
	 * Select item border type
	 * Select a style for the selected border when using
	 * image replacements or swatches.
	 *
	 * @var string
	 */
	public $tm_epo_css_selected_border = '';

	/**
	 * Enable validation
	 * Check to enable validation feature for builder elements
	 *
	 * @var string
	 */
	public $tm_epo_global_enable_validation = 'yes';

	/**
	 * Disable error scrolling
	 * Check to disable scrolling to the element with an error
	 *
	 * @var string
	 */
	public $tm_epo_disable_error_scroll = 'no';

	/**
	 * Error label placement
	 * Set the placement for the validation error notification label
	 *
	 * @var string
	 */
	public $tm_epo_global_error_label_placement = '';

	/**
	 * Use options cache
	 * Use options caching for boosting performance.
	 * Disable if you have options that share the same unique ID.
	 *
	 * @var string
	 */
	public $tm_epo_options_cache = 'no';

	/**
	 * Javascript and CSS inclusion mode
	 * Select how to include JS and CSS files
	 *
	 * @var string
	 */
	public $tm_epo_global_js_css_mode = 'dev';

	/**
	 * Disable PNG convert security
	 * Check to disable the conversion to png for image uploads.
	 *
	 * @var string
	 */
	public $tm_epo_global_no_upload_to_png = 'no';

	/**
	 * Override product price
	 * This will globally override the product price with the
	 * price from the options if the total options price is greater then zero.
	 *
	 * @var string
	 */
	public $tm_epo_global_override_product_price = '';

	/**
	 * Options price mode
	 * Select the price mode for the options.
	 *
	 * @var string
	 */
	public $tm_epo_global_options_price_mode = 'sale';

	/**
	 * Reset option values after the product is added to the cart
	 * This will revert the option values to the default ones after
	 * adding the product to the cart
	 *
	 * @var string
	 */
	public $tm_epo_global_reset_options_after_add = 'no';

	/**
	 * Use plus and minus signs on prices in cart and checkout
	 * Choose how you want the sign of options prices to bedisplayed
	 * in cart and checkout.
	 *
	 * @var string
	 */
	public $tm_epo_global_price_sign = '';

	/**
	 * Use plus and minus signs on option prices
	 * Choose how you want the sign of options prices to be displayed
	 * at the product page.
	 *
	 * @var string
	 */
	public $tm_epo_global_options_price_sign = 'minus';

	/**
	 * Input decimal separator
	 * Choose how to determine the decimal separator for user inputs
	 *
	 * @var string
	 */
	public $tm_epo_global_input_decimal_separator = 'browser';

	/**
	 * Displayed decimal separator
	 * Choose which decimal separator to display on currency prices
	 *
	 * @var string
	 */
	public $tm_epo_global_displayed_decimal_separator = '';

	/**
	 * Timezone override for Date element
	 * Choose which timezone the date element will use on the backend
	 * calculations or leave blank for server timezone.
	 *
	 * @var string
	 */
	public $tm_epo_global_date_timezone = '';

	/**
	 * Required state indicator
	 * Enter a string to indicate the required state of a field.
	 *
	 * @var string
	 */
	public $tm_epo_global_required_indicator = '*';

	/**
	 * Required state indicator position
	 * Select the placement of the Required state indicator
	 *
	 * @var string
	 */
	public $tm_epo_global_required_indicator_position = 'left';

	/**
	 * Include tax string suffix on totals box
	 * Enable this to add the WooCommerce tax suffix on the totals box
	 *
	 * @var string
	 */
	public $tm_epo_global_tax_string_suffix = 'no';

	/**
	 * Include the WooCommerce Price display suffix on totals box
	 * Enable this to add the WooCommerce Price display suffix
	 * on the totals box.
	 *
	 * @var string
	 */
	public $tm_epo_global_wc_price_suffix = 'no';

	/**
	 * The jQuery selector for main product image
	 * This is used to change the product image.
	 *
	 * @var string
	 */
	public $tm_epo_global_product_image_selector = '';

	/**
	 * Product image replacement mode
	 * Self mode replaces the actual image and Inline appends
	 * new image elements.
	 *
	 * @var string
	 */
	public $tm_epo_global_product_image_mode = 'self';

	/**
	 * Move out of stock message
	 * This is moves the out of stock message when styled variations
	 * are used just below them.
	 *
	 * @var string
	 */
	public $tm_epo_global_move_out_of_stock = 'no';

	/**
	 * Use internal variation price
	 * Use this if your variable products have a lot of options to
	 * improve performance. Note that this may cause issues with
	 * discount or currency plugins.
	 *
	 * @var string
	 */
	public $tm_epo_no_variation_prices_array = 'no';

	/**
	 * Enable plugin interface on product edit page for roles
	 * Select the roles that will have access to the plugin interfacewhile
	 * on the edit product page. The Admininstrator role always has access.
	 *
	 * @var string
	 */
	public $tm_epo_global_hide_product_enabled = '';

	/**
	 * Hide override settings on products
	 * Enable this to hide the settings tab on the product edit screen
	 *
	 * @var string
	 */
	public $tm_epo_global_hide_product_settings = 'no';

	/**
	 * Hide Builder mode on products
	 * Enable this to hide the builder tab on the product edit screen
	 *
	 * @var string
	 */
	public $tm_epo_global_hide_product_builder_mode = 'no';

	/**
	 * Hide Normal mode on products
	 * Enable this to hide the normal tab on the product edit screen
	 *
	 * @var string
	 */
	public $tm_epo_global_hide_product_normal_mode = 'no';

	/**
	 * Enable WP Rocket CDN
	 * Check to enable the use of WP Rocket cdn for the plugin images
	 * if it is active.
	 *
	 * @var string
	 */
	public $tm_epo_global_cdn_rocket = 'yes';

	/**
	 * Enable Jetpack CDN
	 * Check to enable the use of Jetpack cdn for the plugin images
	 * if it is active.
	 *
	 * @var string
	 */
	public $tm_epo_global_cdn_jetpack = 'no';

	/**
	 * Tooltip max width
	 * Set the max width of the tooltip that appears on the elements.
	 *
	 * @var string
	 */
	public $tm_epo_global_tooltip_max_width = '340px';

	/**
	 * Image mode
	 * Set the image mode that will be used for various image
	 * related functionality.
	 *
	 * @var string
	 */
	public $tm_epo_global_image_mode = 'relative';

	/**
	 * Retrieve image sizes for image replacements
	 * Disable this for slow servers or large amounts of images.
	 *
	 * @var string
	 */
	public $tm_epo_global_retrieve_image_sizes = 'no';

	/**
	 * Radio button undo button
	 * Globally override the undo button for radio buttons
	 *
	 * @var string
	 */
	public $tm_epo_global_radio_undo_button = '';

	/**
	 * Datepicker theme
	 * Select the theme for the datepicker.
	 *
	 * @var string
	 */
	public $tm_epo_global_datepicker_theme = '';

	/**
	 * Datepicker size
	 * Select the size of the datepicker.
	 *
	 * @var string
	 */
	public $tm_epo_global_datepicker_size = '';

	/**
	 * Datepicker position
	 * Select the position of the datepicker.
	 *
	 * @var string
	 */
	public $tm_epo_global_datepicker_position = '';

	/**
	 * Minimum characters for text-field and text-areas
	 * Enter a value for the minimum characters the user must enter.
	 *
	 * @var string
	 */
	public $tm_epo_global_min_chars = '';

	/**
	 * Maximum characters for text-field and text-areas
	 * Enter a value for the minimum characters the user must enter.
	 *
	 * @var string
	 */
	public $tm_epo_global_max_chars = '';

	/**
	 * Upload element inline Image preview
	 * Enable inline preview of the image that will be uploaded.
	 *
	 * @var string
	 */
	public $tm_epo_upload_inline_image_preview = 'no';

	/**
	 * Scroll to the product element upon selection
	 * Enable to scroll the viewport to the product element.
	 *
	 * @var string
	 */
	public $tm_epo_global_product_element_scroll = 'yes';

	/**
	 * Product element scroll offset
	 * Enter a value for the scroll offset when selecting a choice
	 * for the product element.
	 *
	 * @var string|int
	 */
	public $tm_epo_global_product_element_scroll_offset = '-100';

	/**
	 * Sync associated product quantity with main product quantity
	 * Enable to have the quantities of the associated products to be
	 * a multiple of the main product quantity.
	 *
	 * @var string
	 */
	public $tm_epo_global_product_element_quantity_sync = 'yes';

	/**
	 * Upload folder
	 * Changing this will only affect future uploads.
	 *
	 * @var string
	 */
	public $tm_epo_upload_folder = 'extra_product_options';

	/**
	 * Enable pop-up message on uploads
	 * Enables a pop-up when uploads are made.
	 *
	 * @var string
	 */
	public $tm_epo_upload_popup = 'no';

	/**
	 * Enable upload success message
	 * Indicates if the upload was successful with a message.
	 *
	 * @var string
	 */
	public $tm_epo_upload_success_message = 'yes';

	/**
	 * Allowed file types
	 * Select which file types the user will be allowed to upload.
	 *
	 * @var string
	 */
	public $tm_epo_allowed_file_types = '@';

	/**
	 * Custom types
	 * Select custom file types the user will be allowed to upload
	 * separated by commas.
	 *
	 * @var string
	 */
	public $tm_epo_custom_file_types = '';

	/**
	 * CSS code
	 * Only enter pure CSS code without and style tags
	 *
	 * @var string
	 */
	public $tm_epo_css_code = '';

	/**
	 * JavaScript code
	 * Only enter pure JavaScript code without and script tags
	 *
	 * @var string
	 */
	public $tm_epo_js_code = '';

	/**
	 * Username
	 * Your Envato username.
	 *
	 * @var string
	 */
	public $tm_epo_envato_username = '';

	/**
	 * Envato Personal Token
	 *
	 * @var string
	 */
	public $tm_epo_envato_apikey = '';

	/**
	 * Purchase code
	 *
	 * @var string
	 */
	public $tm_epo_envato_purchasecode = '';

	/**
	 * Consent
	 *
	 * @var string
	 */
	public $tm_epo_consent_for_transmit = 'no';

	/**
	 * Custom math formula constants
	 *
	 * @var string
	 */
	public $tm_epo_math = '';

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_Extra_Product_Options|null
	 */
	protected static $instance = null;

	/**
	 * Main Extra Product Options Instance
	 *
	 * Ensures only one instance of Extra Product Options is loaded or can be loaded
	 *
	 * @since 1.0
	 * @static
	 * @see   THEMECOMPLETE_EPO()
	 * @return THEMECOMPLETE_Extra_Product_Options - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->is_bto = false;

		$this->cart_edit_key_var     = apply_filters( 'wc_epo_cart_edit_key_var', 'tm_cart_item_key' );
		$this->cart_edit_key_var_alt = apply_filters( 'wc_epo_cart_edit_key_var_alt', 'tc_cart_edit_key' );
		$this->cart_edit_key         = null;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST[ $this->cart_edit_key_var ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->cart_edit_key = filter_var( wp_unslash( $_REQUEST[ $this->cart_edit_key_var ] ), FILTER_SANITIZE_SPECIAL_CHARS );

		} else {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_REQUEST[ $this->cart_edit_key_var_alt ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->cart_edit_key = filter_var( wp_unslash( $_REQUEST[ $this->cart_edit_key_var_alt ] ), FILTER_SANITIZE_SPECIAL_CHARS );

			} else {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_REQUEST['update-composite'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$this->cart_edit_key = filter_var( wp_unslash( $_REQUEST['update-composite'] ), FILTER_SANITIZE_SPECIAL_CHARS );

				}
			}
		}

		// Add compatibility actions and filters with other plugins and themes.
		THEMECOMPLETE_EPO_COMPATIBILITY();

		add_action( 'plugins_loaded', [ $this, 'plugin_loaded' ], 3 );
		add_action( 'wp_loaded', [ $this, 'wp_loaded' ] );
		add_action( 'plugins_loaded', [ $this, 'tm_epo_add_elements' ], 12 );

	}

	/**
	 * Handles the display of builder sections
	 *
	 * @param boolean $bool If options are displayed inline (associated product).
	 * @since 5.0
	 * @return void
	 */
	public function set_inline_epo( $bool = false ) {
		$this->is_inline_epo = $bool;
	}

	/**
	 * Adds additional builder elements from 3rd party plugins
	 *
	 * @since 1.0
	 * @return void
	 */
	public function tm_epo_add_elements() {

		do_action( 'tm_epo_register_addons' );
		do_action( 'tm_epo_register_extra_multiple_choices' );

		$this->tm_original_builder_elements = THEMECOMPLETE_EPO_BUILDER()->get_elements();

		if ( is_array( $this->tm_original_builder_elements ) ) {

			foreach ( $this->tm_original_builder_elements as $key => $value ) {

				if ( 'post' === $value->is_post ) {
					$this->element_post_types[] = $value->post_name_prefix;
				}

				if ( 'post' === $value->is_post || 'display' === $value->is_post ) {
					$this->tm_builder_elements[ $value->post_name_prefix ] = $value;
				}
			}
		}

	}

	/**
	 * Setup the plugin
	 *
	 * @since 1.0
	 * @return void
	 */
	public function plugin_loaded() {

		$this->tm_plugin_settings = THEMECOMPLETE_EPO_SETTINGS()->plugin_settings();
		$this->get_plugin_settings();

		THEMECOMPLETE_EPO_ORDER();

		if ( ! wp_doing_ajax() && is_admin() ) {
			return;
		}

		$this->get_override_settings();
		$this->add_plugin_actions();

		THEMECOMPLETE_EPO_SCRIPTS();
		THEMECOMPLETE_EPO_DISPLAY();
		THEMECOMPLETE_EPO_CART();

		THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS();

	}

	/**
	 * Setup the plugin
	 *
	 * @since 6.1
	 * @return void
	 */
	public function wp_loaded() {
		$this->generate_lookuptables();
	}

	/**
	 * Gets all of the plugin settings
	 *
	 * @since 1.0
	 * @return void
	 */
	public function get_plugin_settings() {

		foreach ( apply_filters( 'wc_epo_get_settings', $this->tm_plugin_settings ) as $key => $value ) {
			if ( is_array( $value ) && 3 === count( $value ) ) {
				$method    = $value[2];
				$classname = $value[1];
				if ( is_object( $classname ) && call_user_func( [ $classname, $method ] ) ) {
					$this->$key = get_option( $key );
					if ( false === $this->$key ) {
						$this->$key = $value[0];
					}
				} else {
					$this->$key = get_option( $key );
					if ( false === $this->$key ) {
						$this->$key = $value;
					}
				}
			} else {
				$this->$key = get_option( $key );
				if ( false === $this->$key ) {
					$this->$key = $value;
				}
			}
			$this->$key = wp_unslash( $this->$key );
		}

		if ( 'custom' === $this->tm_epo_options_placement ) {
			$this->tm_epo_options_placement = $this->tm_epo_options_placement_custom_hook;
		}

		if ( 'custom' === $this->tm_epo_totals_box_placement ) {
			$this->tm_epo_totals_box_placement = $this->tm_epo_totals_box_placement_custom_hook;
		}

		$this->upload_dir = $this->tm_epo_upload_folder;
		$this->upload_dir = str_replace( '/', '', $this->upload_dir );
		$this->upload_dir = sanitize_file_name( $this->upload_dir );
		$this->upload_dir = '/' . $this->upload_dir . '/';

		if ( $this->is_quick_view() ) {
			$this->tm_epo_options_placement_hook_priority    = 50;
			$this->tm_epo_totals_box_placement_hook_priority = 50;
			$this->tm_epo_options_placement                  = 'woocommerce_before_add_to_cart_button';
			$this->tm_epo_totals_box_placement               = 'woocommerce_before_add_to_cart_button';
		}

		// Backwards compatibility.
		if ( 'display' === $this->tm_epo_force_select_options ) {
			$this->tm_epo_force_select_options = 'yes';
		}
		if ( 'normal' === $this->tm_epo_force_select_options ) {
			$this->tm_epo_force_select_options = 'no';
		}

		if ( 'show' === $this->tm_epo_clear_cart_button ) {
			$this->tm_epo_clear_cart_button = 'yes';
		}
		if ( 'normal' === $this->tm_epo_clear_cart_button ) {
			$this->tm_epo_clear_cart_button = 'no';
		}

		if ( 'hide' === $this->tm_epo_hide_options_in_cart ) {
			$this->tm_epo_hide_options_in_cart = 'yes';
		}
		if ( 'normal' === $this->tm_epo_hide_options_in_cart ) {
			$this->tm_epo_hide_options_in_cart = 'no';
		}

		if ( 'hide' === $this->tm_epo_hide_options_prices_in_cart ) {
			$this->tm_epo_hide_options_prices_in_cart = 'yes';
		}
		if ( 'normal' === $this->tm_epo_hide_options_prices_in_cart ) {
			$this->tm_epo_hide_options_prices_in_cart = 'no';
		}

		if ( 'on' === $this->tm_epo_css_styles ) {
			$this->tm_epo_css_styles = 'yes';
		}
		if ( '' === $this->tm_epo_css_styles ) {
			$this->tm_epo_css_styles = 'no';
		}

		if ( '' !== $this->tm_epo_global_image_max_width || '' !== $this->tm_epo_global_image_max_height ) {
			$image_css = '.woocommerce #content table.cart img.epo-upload-image, .woocommerce table.cart img.epo-upload-image, .woocommerce-page #content table.cart img.epo-upload-image, .woocommerce-page table.cart img.epo-upload-image, .epo-upload-image {';
			if ( '' !== $this->tm_epo_global_image_max_width ) {
				$image_css .= 'max-width: calc(' . esc_attr( $this->tm_epo_global_image_max_width ) . ' - 0.5em)  !important;';
			}
			if ( '' !== $this->tm_epo_global_image_max_height ) {
				$image_css .= 'max-height: ' . esc_attr( $this->tm_epo_global_image_max_height ) . ' !important;';
			}
			$image_css            .= '}';
			$this->tm_epo_css_code = $image_css . "\n" . $this->tm_epo_css_code;
		}

		if ( ! isset( $this->tm_epo_enable_vat_options_total ) ) {
			$this->tm_epo_enable_vat_options_total = 'no';
		}

	}

	/**
	 * Gets custom settings for the current product
	 *
	 * @since 1.0
	 * @return void
	 */
	public function get_override_settings() {
		foreach ( $this->meta_fields as $key => $value ) {
			$this->tm_meta_cpf[ $key ] = $value;
		}
	}

	/**
	 * Add required actions and filters
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_plugin_actions() {

		// Initialize custom product settings.
		if ( $this->is_quick_view() ) {
			add_action( 'init', [ $this, 'init_settings' ] );
		} else {
			if ( $this->is_enabled_shortcodes() ) {
				add_action( 'init', [ $this, 'init_settings_pre' ] );
			} else {
				add_action( 'template_redirect', [ $this, 'init_settings' ] );
			}
		}

		add_action( 'template_redirect', [ $this, 'init_vars' ], 1 );

		// Force Select Options.
		add_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'add_to_cart_url' ], 50, 1 );
		add_action( 'woocommerce_product_add_to_cart_text', [ $this, 'add_to_cart_text' ], 10, 1 );
		add_filter( 'woocommerce_cart_redirect_after_error', [ $this, 'woocommerce_cart_redirect_after_error' ], 50, 2 );

		// Enable shortcodes for element labels.
		add_filter( 'woocommerce_tm_epo_option_name', [ $this, 'tm_epo_option_name' ], 10, 5 );

		// Add custom class to product div used to initialize the plugin JavaScript.
		add_filter( 'post_class', [ $this, 'tm_post_class' ] );
		add_filter( 'body_class', [ $this, 'tm_body_class' ] );

		// Helper to flag various page positions.
		add_filter( 'woocommerce_related_products_columns', [ $this, 'tm_woocommerce_related_products_args' ], 10, 1 );
		add_action( 'woocommerce_before_single_product', [ $this, 'tm_enable_post_class' ], 1 );
		add_action( 'woocommerce_after_single_product', [ $this, 'tm_enable_post_class' ], 1 );
		add_action( 'woocommerce_upsells_orderby', [ $this, 'tm_woocommerce_related_products_args' ], 10, 1 );
		add_action( 'woocommerce_after_single_product_summary', [ $this, 'tm_woocommerce_after_single_product_summary' ], 99999 );

		// Image filter.
		add_filter( 'tm_image_url', [ $this, 'tm_image_url' ] );

		// Alter the price filter.
		if ( 'yes' === $this->tm_epo_add_product_price_check ) {
			add_filter( 'woocommerce_product_get_price', [ $this, 'woocommerce_product_get_price' ], 1, 2 );
		}

		// Alter product display price to include possible option pricing.
		if ( ! is_admin() && 'yes' === $this->tm_epo_include_possible_option_pricing ) {
			add_filter( 'woocommerce_product_get_price', [ $this, 'tm_woocommerce_get_price' ], 2, 2 );
		}
		if ( ! is_admin() && 'yes' === $this->tm_epo_use_from_on_price ) {
			add_filter( 'woocommerce_show_variation_price', [ $this, 'tm_woocommerce_show_variation_price' ], 50, 3 );
			if ( 'no' === $this->tm_epo_include_possible_option_pricing ) {
				add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
			}
		}

		// Override the minimum characters of text fields globally.
		add_filter( 'wc_epo_global_min_chars', [ $this, 'wc_epo_global_min_chars' ], 10, 3 );
		// Override the maximum characters of text fields globally.
		add_filter( 'wc_epo_global_max_chars', [ $this, 'wc_epo_global_max_chars' ], 10, 3 );

		if ( 'yes' === $this->tm_epo_global_no_upload_to_png ) {
			add_filter( 'wc_epo_no_upload_to_png', '__return_false' );
		}

		// Alter generated Product structured data.
		add_filter( 'woocommerce_structured_data_product_offer', [ $this, 'woocommerce_structured_data_product_offer' ], 10, 2 );

		// Enable shortcodes in options strings.
		if ( 'yes' === $this->tm_epo_enable_data_shortcodes ) {
			add_filter( 'wc_epo_kses', [ $this, 'wc_epo_kses' ], 10, 3 );
			add_filter( 'wc_epo_label_in_cart', [ $this, 'wc_epo_label_in_cart' ], 10, 1 );
		}

		// Enable shortcodes on prices.
		add_filter( 'wc_epo_apply_discount', [ $this, 'enable_shortcodes' ], 10, 3 );
		// Enable shortcodes on various properties.
		add_filter( 'wc_epo_enable_shortocde', [ $this, 'enable_shortcodes' ], 10, 3 );

		// Set the flag for the product loop.
		add_action( 'woocommerce_before_shop_loop_item', [ $this, 'woocommerce_before_shop_loop_item' ], 0 );
		add_action( 'woocommerce_after_shop_loop_item', [ $this, 'woocommerce_after_shop_loop_item' ], 999999 );

		// Change prices in the product loop.
		add_filter( 'woocommerce_get_price_html', [ $this, 'woocommerce_get_price_html' ], 999999, 2 );

		// Trim zeros in prices.
		if ( 'yes' === $this->tm_epo_trim_zeros ) {
			add_filter( 'woocommerce_price_trim_zeros', '__return_true' );
		}
	}

	/**
	 * Flag to check if we are in the product loop
	 *
	 * @since 6.2
	 * @return void
	 */
	public function woocommerce_before_shop_loop_item() {

		$this->is_in_product_loop = true;

	}

	/**
	 * Flag to check if we are in the product loop
	 *
	 * @since 6.2
	 * @return void
	 */
	public function woocommerce_after_shop_loop_item() {

		$this->is_in_product_loop = false;

	}

	/**
	 * Alter product display price to include possible option pricing on the product loop
	 *
	 * @param mixed        $price The product price.
	 * @param object|false $product The product object.
	 * @since 6.2
	 */
	public function woocommerce_get_price_html( $price = '', $product = false ) {

		if ( ! $this->is_in_product_loop ) {
			return $price;
		}

		$tm_meta_cpf = themecomplete_get_post_meta( $product, 'tm_meta_cpf', true );
		if ( is_array( $tm_meta_cpf ) ) {
			$tm_price_display_mode          = isset( $tm_meta_cpf['price_display_mode'] ) ? $tm_meta_cpf['price_display_mode'] : 'none';
			$tm_price_display_override      = isset( $tm_meta_cpf['price_display_override'] ) ? $tm_meta_cpf['price_display_override'] : '';
			$tm_price_display_override_sale = isset( $tm_meta_cpf['price_display_override_sale'] ) ? $tm_meta_cpf['price_display_override_sale'] : '';
			$tm_price_display_override_to   = isset( $tm_meta_cpf['price_display_override_to'] ) ? $tm_meta_cpf['price_display_override_to'] : '';

			switch ( $tm_price_display_mode ) {
				case 'price':
					if ( '' !== $tm_price_display_override_sale ) {
						$price = ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( wc_format_decimal( $tm_price_display_override ), wc_format_decimal( $tm_price_display_override_sale ) )
							: '<del>' . themecomplete_price( wc_format_decimal( $tm_price_display_override ) ) . '</del> <ins>' . themecomplete_price( wc_format_decimal( $tm_price_display_override_sale ) ) . '</ins>'
						);
					} else {
						$price = themecomplete_price( wc_format_decimal( $tm_price_display_override ) );
					}
					break;
				case 'from':
					$price = ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() );
					if ( '' !== $tm_price_display_override_sale ) {
						$price .= ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( wc_format_decimal( $tm_price_display_override ), wc_format_decimal( $tm_price_display_override_sale ) )
							: '<del>' . themecomplete_price( wc_format_decimal( $tm_price_display_override ) ) . '</del> <ins>' . themecomplete_price( wc_format_decimal( $tm_price_display_override_sale ) ) . '</ins>'
						);
					} else {
						$price .= themecomplete_price( wc_format_decimal( $tm_price_display_override ) );
					}
					break;
				case 'range':
					$price = themecomplete_price( wc_format_decimal( $tm_price_display_override ) ) . ' - ' . themecomplete_price( wc_format_decimal( $tm_price_display_override_to ) );
					break;
			}
		}

		return apply_filters( 'woocommerce_epo_get_price_html', $price, $product );

	}

	/**
	 * Enable shortcodes on an element property
	 *
	 * @param mixed   $property The option property.
	 * @param mixed   $original_property The original option property.
	 * @param integer $post_id The post id where the filter was used.
	 *
	 * @since 6.0.4
	 * @return mixed
	 */
	public function enable_shortcodes( $property = '', $original_property = '', $post_id = 0 ) {

		if ( is_array( $property ) ) {
			foreach ( $property as $key => $value ) {
				$property[ $key ] = themecomplete_do_shortcode( $value );
			}
		} else {
			$property = themecomplete_do_shortcode( $property );
		}
		return $property;

	}

	/**
	 * Enable shortcodes in options strings
	 *
	 * @param string  $text Filtered text.
	 * @param string  $original_text Original text.
	 * @param boolean $shortcode If shortcode should be enabled.
	 * @since 4.9.2
	 * @return string
	 */
	public function wc_epo_kses( $text = '', $original_text = '', $shortcode = true ) {

		$text = $original_text;

		if ( $shortcode ) {
			$text = themecomplete_do_shortcode( $text );
		}

		return $text;

	}

	/**
	 * Enable shortcodes in cart option strings
	 *
	 * @param string $text The element label text.
	 * @since 4.9.2
	 * @return string
	 */
	public function wc_epo_label_in_cart( $text = '' ) {

		return themecomplete_do_shortcode( $text );

	}

	/**
	 * Get product min/max prices
	 *
	 * @param WC_Product $product Product object.
	 * @since 4.8.1
	 * @return array
	 */
	public function get_product_min_max_prices( $product ) {

		$id   = themecomplete_get_id( $product );
		$type = themecomplete_get_product_type( $product );

		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $id );
		if ( ! THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) || 'variation' === $type ) {
			return [];
		}

		$override_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $id, 'product' ) );
		$tm_meta_cpf = themecomplete_get_post_meta( $override_id, 'tm_meta_cpf', true );

		$price_override = ( 'no' === $this->tm_epo_global_override_product_price )
			? 0
			: ( ( 'yes' === $this->tm_epo_global_override_product_price )
				? 1
				: ( ! empty( $tm_meta_cpf['price_override'] ) ? 1 : 0 ) );

		if ( ! isset( $this->product_minmax[ $id ] ) ) {
			$this->product_minmax[ $id ] = $this->add_product_tc_prices( $product );
		}

		$minmax = $this->product_minmax[ $id ];

		if ( 'variable' === $type || 'variable-subscription' === $type ) {
			$prices = $product->get_variation_prices( false );

			// Calculate min price.
			$min_price       = current( $prices['price'] );
			$tc_min_variable = isset( $minmax['tc_min_variable'][ key( $prices['price'] ) ] )
				? $minmax['tc_min_variable'][ key( $prices['price'] ) ]
				: ( isset( $minmax['tc_min_variable'] )
					? $minmax['tc_min_variable']
					: 0
				);

			if ( is_array( $tc_min_variable ) ) {
				$tc_min_variable = min( $tc_min_variable );
			}

			$min_raw = floatval( apply_filters( 'wc_epo_options_min_price', $tc_min_variable, $product, true ) );

			$min_price = $min_price + $min_raw;

			// include taxes.
			$min_price = $this->tc_get_display_price( $product, $min_price );

			if ( $price_override ) {
				if ( ! empty( $min_raw ) ) {
					$min_price = $min_raw;
				}
				$this->product_minmax[ $id ]['is_override'] = 1;
			}

			$min = $this->tc_get_display_price( $product, $min_raw );

			// Calculate max price.
			$copy_prices = $prices['price'];
			$added_max   = [];
			foreach ( $copy_prices as $vkey => $vprice ) {
				$added_price_max = is_array( $this->product_minmax[ $id ]['tc_max_variable'] )
					? ( isset( $this->product_minmax[ $id ]['tc_max_variable'][ $vkey ] )
						? $this->product_minmax[ $id ]['tc_max_variable'][ $vkey ]
						: 0 )
					: $this->product_minmax[ $id ]['tc_max_variable'];

				$added_price          = floatval( apply_filters( 'wc_epo_options_max_price_raw', $added_price_max, $product, false ) );
				$added_max[]          = $added_price;
				$copy_prices[ $vkey ] = $vprice + $added_price;
			}
			asort( $copy_prices );
			$max_price = end( $copy_prices );

			asort( $added_max );

			$max_raw = floatval( apply_filters( 'wc_epo_options_max_price', end( $added_max ), $product, false ) );

			$max_price = $this->tc_get_display_price( $product, $max_price );

			if ( $price_override && ! ( empty( $this->product_minmax[ $id ]['tc_min_variable'] ) && empty( $this->product_minmax[ $id ]['tc_max_variable'] ) ) ) {
				$max_price = $max_price - $this->tc_get_display_price( $product, floatval( $prices['price'][ key( $copy_prices ) ] ) );
			}

			$max = $this->tc_get_display_price( $product, $max_raw );

			$min_regular_price = floatval( current( $prices['regular_price'] ) ) + $min_raw;
			$max_regular_price = floatval( end( $prices['regular_price'] ) ) + $max_raw;

			// include taxes.
			$min_regular_price = $this->tc_get_display_price( $product, $min_regular_price );
			$max_regular_price = $this->tc_get_display_price( $product, $max_regular_price );

		} else {

			// Calculate min price.
			$min_raw = floatval( apply_filters( 'wc_epo_options_min_price', $minmax['tc_min_price'], $product, false ) );

			if ( $price_override ) {

				if ( ! empty( $min_raw ) ) {
					$new_min = $min_raw;
				} else {
					$new_min = $product->get_price();
				}

				$min_raw = $new_min;

				$this->product_minmax[ $id ]['is_override'] = 1;
			}

			$this->product_minmax[ $id ]['tc_min_price'] = $min_raw;

			$display_price         = $this->tc_get_display_price( $product );
			$display_regular_price = $this->tc_get_display_price( $product, $this->tc_get_regular_price( $product ) );

			if ( $price_override && $min_raw <= 0 ) {
				$display_price = $display_regular_price;
			}

			$min       = $this->tc_get_display_price( $product, $min_raw );
			$min_price = $display_price;

			// Calculate max price.
			$max_raw                                     = floatval( apply_filters( 'wc_epo_options_max_price', $this->product_minmax[ $id ]['tc_max_price'], $product, false ) );
			$this->product_minmax[ $id ]['tc_max_price'] = $max_raw;
			$max       = $this->tc_get_display_price( $product, $max_raw );
			$max_price = $this->tc_get_display_price( $product, (float) apply_filters( 'wc_epo_product_price', $product->get_price() ) + $max_raw );

			$min_regular_price = floatval( $display_regular_price );
			$max_regular_price = floatval( $this->tc_get_display_price( $product, $product->get_regular_price() ) ) + $max;

		}

		return [
			'min_raw'             => $min_raw,
			'max_raw'             => $max_raw,
			'min'                 => $min,
			'max'                 => $max,
			'min_price'           => $min_price,
			'max_price'           => $max_price,

			'min_regular_price'   => isset( $min_regular_price ) ? $min_regular_price : 0,
			'max_regular_price'   => isset( $max_regular_price ) ? $max_regular_price : 0,

			'formatted_min'       => wc_format_decimal( $min, wc_get_price_decimals() ),
			'formatted_max'       => wc_format_decimal( $max, wc_get_price_decimals() ),
			'formatted_min_price' => wc_format_decimal( $min_price, wc_get_price_decimals() ),
			'formatted_max_price' => wc_format_decimal( $max_price, wc_get_price_decimals() ),

		];

	}

	/**
	 * Alter generated product structured data
	 *
	 * @param array  $markup The markup array.
	 * @param object $product The product object.
	 * @since 4.8.1
	 * @return array
	 */
	public function woocommerce_structured_data_product_offer( $markup, $product ) {

		if ( 'no' === $this->tm_epo_alter_structured_data ) {
			return $markup;
		}

		$min_max = $this->get_product_min_max_prices( $product );

		if ( empty( $min_max ) ) {
			return $markup;
		}

		$min_price = $min_max['formatted_min_price'];
		$max_price = $min_max['formatted_max_price'];

		if ( isset( $markup['priceSpecification'] ) && is_array( $markup['priceSpecification'] ) && isset( $markup['priceSpecification']['price'] ) ) {
			$markup['priceSpecification']['price'] = $min_price;
			$markup['price']                       = $min_price;
		}
		if ( isset( $max_price ) && isset( $markup['lowPrice'] ) && isset( $markup['highPrice'] ) ) {
			$markup['lowPrice']  = $min_price;
			$markup['highPrice'] = $max_price;
		}

		return $markup;

	}

	/**
	 * Override the minimum characters of text fields globally
	 *
	 * @param string $min The minimum characters.
	 * @param string $element The element type.
	 * @param string $element_uniqueid The element unique id.
	 * @since 1.0
	 * @return string
	 */
	public function wc_epo_global_min_chars( $min = '', $element = '', $element_uniqueid = '' ) {
		$element = str_replace( '_min_chars', '', $element );

		if ( ( 'textfield' === $element || 'textarea' === $element ) && '' !== $this->tm_epo_global_min_chars && '' === $min ) {
			$min = $this->tm_epo_global_min_chars;
		}

		return $min;
	}

	/**
	 * Override the maximum characters of text fields globally
	 *
	 * @param string $max The maximum characters.
	 * @param string $element The element type.
	 * @param string $element_uniqueid The element unique id.
	 * @since 1.0
	 * @return string
	 */
	public function wc_epo_global_max_chars( $max = '', $element = '', $element_uniqueid = '' ) {
		$element = str_replace( '_min_chars', '', $element );
		if ( ( 'textfield' === $element || 'textarea' === $element ) && '' !== $this->tm_epo_global_max_chars && '' === $max ) {
			$max = $this->tm_epo_global_max_chars;
		}

		return $max;
	}

	/**
	 * Initialize custom product settings
	 *
	 * @since 1.0
	 * @return void
	 */
	public function init_settings_pre() {

		$postid = false;
		if ( function_exists( 'ux_builder_is_iframe' ) && ux_builder_is_iframe() ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['post_id'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$postid = absint( wp_unslash( $_GET['post_id'] ) );
			}
		} else {
			if ( ! isset( $_SERVER['HTTP_HOST'] ) || ! isset( $_SERVER['REQUEST_URI'] ) ) {
				$postid = 0;
			} else {
				$url    = 'http://' . esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
				$postid = THEMECOMPLETE_EPO_HELPER()->get_url_to_postid( $url );
			}
		}

		$this->postid_pre = $postid;
		$product          = wc_get_product( $postid );

		$check1 = ( 0 === (int) $postid );
		$check2 = ( $product
					&& is_object( $product )
					&& property_exists( $product, 'post' )
					&& property_exists( $product->post, 'post_type' )
					&& ( in_array( $product->post->post_type, [ 'product', 'product_variation' ], true ) ) );
		$check3 = ( $product
					&& is_object( $product )
					&& property_exists( $product, 'post_type' )
					&& ( in_array( $product->post_type, [ 'product', 'product_variation' ], true ) ) );

		if ( $check1 || $check2 || $check3 ) {
			add_action( 'template_redirect', [ $this, 'init_settings' ] );
		} else {
			$this->init_settings();
		}

	}

	/**
	 * Initialize variables
	 *
	 * @since 1.0
	 * @return void
	 */
	public function init_vars() {
		$this->wc_vars = [
			'is_product'             => is_product(),
			'is_shop'                => is_shop(),
			'is_product_category'    => is_product_category(),
			'is_product_tag'         => is_product_tag(),
			'is_cart'                => is_cart(),
			'is_checkout'            => is_checkout(),
			'is_account_page'        => is_account_page(),
			'is_ajax'                => wp_doing_ajax(),
			'is_page'                => is_page(),
			'is_order_received_page' => is_order_received_page(),
		];

		// Disable floating totals box on non product pages.
		if ( ! $this->wc_vars['is_product'] ) {
			$this->tm_epo_floating_totals_box = '';
		}
	}

	/**
	 * Initialize custom product settings
	 *
	 * @since 1.0
	 * @return void
	 */
	public function init_settings() {

		if ( is_admin() && ! $this->is_quick_view() ) {
			return;
		}

		// Re populate options for WPML.
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			// todo:Find another place to re init settings for WPML.
			$this->get_plugin_settings();
		}

		do_action( 'wc_epo_init_settings' );

		$post_max = ini_get( 'post_max_size' );

		// post_max_size debug.
		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ( empty( $_FILES ) && empty( $_POST ) && isset( $_SERVER['REQUEST_METHOD'] ) && strtolower( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) === 'post'
			&& isset( $_SERVER['CONTENT_LENGTH'] ) && (float) $_SERVER['CONTENT_LENGTH'] > $post_max )
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			|| ( is_admin() && ( ! isset( $_GET ) || ( isset( $_GET ) && isset( $_GET['post_type'] ) && isset( $_GET['action'] ) && ! THEMECOMPLETE_EPO_HELPER()->str_startswith( wp_unslash( $_GET['post_type'] ), 'ct_template' ) && ! THEMECOMPLETE_EPO_HELPER()->str_startswith( wp_unslash( $_GET['action'] ), 'oxy_render_' ) ) ) )
		) {
			/* translators: %s: post max size */
			wc_add_notice( sprintf( esc_html__( 'Trying to upload files larger than %s is not allowed!', 'woocommerce-tm-extra-product-options' ), $post_max ), 'error' );

		}

		global $post, $product;
		$this->set_tm_meta();
		$this->init_settings_after();

	}

	/**
	 * Initialize custom product settings
	 *
	 * @since 1.0
	 * @return void
	 */
	public function init_settings_after() {

		global $post, $product;
		// Check if the plugin is active for the user.
		if ( $this->check_enable() ) {
			if ( ( $this->is_enabled_shortcodes() || is_product() || $this->is_quick_view() )
				&& ( 'normal' === $this->tm_epo_display || 'normal' === $this->tm_meta_cpf['override_display'] )
				&& 'action' !== $this->tm_meta_cpf['override_display']
			) {
				// Add options to the page.
				$this->tm_epo_options_placement_hook_priority = floatval( $this->tm_epo_options_placement_hook_priority );
				if ( ! is_numeric( $this->tm_epo_options_placement_hook_priority ) ) {
					$this->tm_epo_options_placement_hook_priority = 50;
				}
				$this->tm_epo_totals_box_placement_hook_priority = floatval( $this->tm_epo_totals_box_placement_hook_priority );
				if ( ! is_numeric( $this->tm_epo_totals_box_placement_hook_priority ) ) {
					$this->tm_epo_totals_box_placement_hook_priority = 50;
				}

				add_action( $this->tm_epo_options_placement, [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_fields' ], $this->tm_epo_options_placement_hook_priority );
				add_action( $this->tm_epo_options_placement, [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ], $this->tm_epo_options_placement_hook_priority + 99999 );
				add_action( $this->tm_epo_totals_box_placement, [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_totals' ], $this->tm_epo_totals_box_placement_hook_priority );
			}
		}

		if ( 'yes' === $this->tm_epo_enable_in_shop && ( is_shop() || is_product_category() || is_product_tag() || function_exists( 'dokan' ) ) ) {
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'tm_woocommerce_after_shop_loop_item' ], 9 );
		}

		add_action( 'woocommerce_shortcode_before_product_loop', [ $this, 'woocommerce_shortcode_before_product_loop' ] );
		add_action( 'woocommerce_shortcode_after_product_loop', [ $this, 'woocommerce_shortcode_after_product_loop' ] );
		if ( $this->is_enabled_shortcodes() ) {
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'tm_enable_options_on_product_shortcode' ], 1 );
		}

		$this->current_free_text       = esc_attr__( 'Free!', 'woocommerce' );
		$this->assoc_current_free_text = $this->current_free_text;
		if ( $this->tm_epo_replacement_free_price_text ) {
			$this->assoc_current_free_text = $this->tm_epo_replacement_free_price_text;
		}
		if ( 'yes' === $this->tm_epo_remove_free_price_label ) {
			$this->assoc_current_free_text = '';
		}

		if ( 'yes' === $this->tm_epo_remove_free_price_label && 'no' === $this->tm_epo_include_possible_option_pricing ) {

			if ( $post || $this->postid_pre ) {

				if ( $post ) {
					$thiscpf = $this->get_product_tm_epos( $post->ID, '', false, true );
				}

				if ( is_product() && is_array( $thiscpf ) && ( ! empty( $thiscpf['global'] ) || ! empty( $thiscpf['local'] ) ) ) {
					if ( $product &&
						( is_object( $product ) && ! is_callable( [ $product, 'get_price' ] ) ) ||
						( ! is_object( $product ) )
					) {
						$product = wc_get_product( $post->ID );
					}
					if ( $product &&
						is_object( $product ) && is_callable( [ $product, 'get_price' ] )
					) {

						if ( ! (float) $product->get_price() > 0 ) {
							if ( $this->tm_epo_replacement_free_price_text ) {
								$this->current_free_text = $this->tm_epo_replacement_free_price_text;
								add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
							} else {
								if ( 'no' === $this->tm_epo_use_from_on_price ) {
									$this->current_free_text = '';
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
								}
							}
						}

						add_filter( 'woocommerce_get_price_html', [ $this, 'related_get_price_html' ], 10, 2 );

					}
				} else {
					if ( is_shop() || is_product_category() || is_product_tag() ) {
						add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html_shop' ], 10, 2 );
					} elseif ( ! is_product() && $this->is_enabled_shortcodes() ) {
						if ( $this->tm_epo_replacement_free_price_text ) {
							$this->current_free_text = $this->tm_epo_replacement_free_price_text;
							add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
						} else {
							if ( 'no' === $this->tm_epo_use_from_on_price ) {
								$this->current_free_text = '';
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
							}
							add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
						}
					} elseif ( is_product() ) {
						add_filter( 'woocommerce_get_price_html', [ $this, 'related_get_price_html2' ], 10, 2 );
					}
				}
			} else {
				if ( $this->is_quick_view() ) {
					if ( $this->tm_epo_replacement_free_price_text ) {
						$this->current_free_text = $this->tm_epo_replacement_free_price_text;
						add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
					} else {
						add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
						if ( 'no' === $this->tm_epo_use_from_on_price ) {
							$this->current_free_text = '';
							remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
						}
					}
				}
			}
		} elseif ( $this->tm_epo_replacement_free_price_text ) {
			$this->current_free_text = $this->tm_epo_replacement_free_price_text;
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
		}

		if ( 'yes' === $this->tm_epo_use_from_on_price && is_product() && $post ) {
			if ( $product &&
				( is_object( $product ) && ! is_callable( [ $product, 'get_price' ] ) ) ||
				( ! is_object( $product ) )
			) {
				$product = wc_get_product( $post->ID );
			}
			if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {
				$this->current_free_text = $this->tm_get_price_html( $product->get_price(), $product );
			}
		}

	}

	/**
	 * Get the theme name
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
	 *
	 * @return false|string
	 */
	public function get_theme( $header = '' ) {

		$out = '';
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme = wp_get_theme();
			if ( $theme ) {
				$out = $theme->get( $header );
			}
		}

		return $out;

	}

	/**
	 * Check if we have a support theme quickview
	 *
	 * @return bool
	 */
	public function is_supported_quick_view() {

		$theme_name = strtolower( $this->get_theme( 'Name' ) );
		$theme      = explode( ' ', $theme_name );
		if ( isset( $theme[0] ) && isset( $theme[1] ) ) {
			$theme = $theme[0];
		} else {
			$theme = explode( '-', $theme_name );
			if ( isset( $theme[0] ) && isset( $theme[1] ) ) {
				$theme = $theme[0];
			}
		}

		if ( is_array( $theme ) ) {
			$theme = $theme_name;
		}

		if (
			'flatsome' === $theme // ( https://themeforest.net/item/flatsome-multipurpose-responsive-woocommerce-theme/5484319 ).
			|| 'kleo' === $theme // ( https://themeforest.net/item/kleo-pro-community-focused-multipurpose-buddypress-theme/6776630 ).
			|| 'venedor' === $theme // ( https://themeforest.net/item/venedor-responsive-prestashop-theme/8743123 ).
			|| 'elise' === $theme // ( https://themeforest.net/item/elise-modern-multipurpose-wordpress-theme/10768925 ).
			|| 'minshop' === $theme // ( https://themify.me/themes/minshop ).
			|| 'porto' === $theme // ( https://themeforest.net/item/porto-responsive-wordpress-ecommerce-theme/9207399 ).
			|| 'grace' === $theme // ( https://demo.themedelights.com/Wordpress/WP001/ ).
			|| 'woodmart' === $theme // ( https://themeforest.net/item/woodmart-woocommerce-wordpress-theme/20264492 ).
		) {
			return true;
		}

		return false;

	}

	/**
	 * Check if plugin scripts can be loaded
	 *
	 * @since 1.0
	 * @return bool
	 */
	public function can_load_scripts() {

		if (
			(
				( class_exists( 'WC_Quick_View' ) || $this->is_supported_quick_view() )
				&& (
					THEMECOMPLETE_EPO()->wc_vars['is_shop']
					|| THEMECOMPLETE_EPO()->wc_vars['is_product_category']
					|| THEMECOMPLETE_EPO()->wc_vars['is_product_tag'] ) )
			|| $this->is_enabled_shortcodes()
			|| THEMECOMPLETE_EPO()->wc_vars['is_product']
			|| THEMECOMPLETE_EPO()->wc_vars['is_cart']
			|| THEMECOMPLETE_EPO()->wc_vars['is_checkout']
			|| THEMECOMPLETE_EPO()->wc_vars['is_order_received_page']
			|| ( 'yes' === $this->tm_epo_enable_in_shop
				&& (
					THEMECOMPLETE_EPO()->wc_vars['is_shop']
					|| THEMECOMPLETE_EPO()->wc_vars['is_product_category']
					|| THEMECOMPLETE_EPO()->wc_vars['is_product_tag'] ) )
		) {

			return true;

		}

		return false;

	}

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @since 1.0
	 * @return void
	 */
	public function woocommerce_shortcode_before_product_loop() {

		$this->is_in_product_shortcode = true;

	}

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @since 1.0
	 * @return void
	 */
	public function woocommerce_shortcode_after_product_loop() {

		$this->is_in_product_shortcode = false;

	}

	/**
	 * Displays options in [product] shortcode
	 *
	 * @since 1.0
	 * @return void
	 */
	public function tm_enable_options_on_product_shortcode() {

		if ( $this->is_in_product_shortcode ) {
			$this->tm_woocommerce_after_shop_loop_item();
		}

	}

	/**
	 * Displays options in shop page
	 *
	 * @since 1.0
	 * @return void
	 */
	public function tm_woocommerce_after_shop_loop_item() {

		$post_id = get_the_ID();
		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );
		if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
			echo '<div class="tm-has-options tc-after-shop-loop"><form class="cart">';
			THEMECOMPLETE_EPO_DISPLAY()->frontend_display( $post_id, 'tc_' . $post_id, false );
			echo '</form></div>';
		}

	}

	/**
	 * Generate min/max prices for the $product
	 *
	 * @param object|false $product The product object.
	 * @since 1.0
	 * @return mixed
	 */
	public function add_product_tc_prices( $product = false ) {

		if ( $product ) {
			$id = themecomplete_get_id( $product );

			if ( isset( $this->product_minmax[ $id ] ) ) {
				return $this->product_minmax[ $id ];
			}

			$this->product_minmax[ $id ] = [
				'tc_min_price'    => 0,
				'tc_max_price'    => 0,
				'tc_min_variable' => 0,
				'tc_max_variable' => 0,
				'tc_min_max'      => false,
			];

			$epos = $this->get_product_tm_epos( $id, '', false, true );

			if ( is_array( $epos ) && ( ! empty( $epos['global'] ) || ! empty( $epos['local'] ) ) ) {
				if ( ! empty( $epos['price'] ) ) {

					$minmax = THEMECOMPLETE_EPO_HELPER()->sum_array_values( $epos, true );

					if ( ! isset( $minmax['min'] ) ) {
						$minmax['min'] = 0;
					}
					if ( ! isset( $minmax['max'] ) ) {
						$minmax['max'] = 0;
					}
					$min                    = $minmax['min'];
					$max                    = $minmax['max'];
					$minmax['tc_min_price'] = $min;
					$minmax['tc_max_price'] = $max;

					$minmax['tc_min_variable'] = $min;
					$minmax['tc_max_variable'] = $max;

					$minmax['tc_min_max']        = true;
					$this->product_minmax[ $id ] = [
						'tc_min_price'    => $min,
						'tc_max_price'    => $max,

						'tc_min_variable' => $min,
						'tc_max_variable' => $max,

						'tc_min_max'      => true,
					];

					if ( is_array( $min ) && is_array( $max ) ) {
						$this->product_minmax[ $id ] = [
							'tc_min_price'    => min( $min ),
							'tc_max_price'    => max( $max ),
							'tc_min_variable' => $min,
							'tc_max_variable' => $max,
							'tc_min_max'      => true,
						];
						$minmax['tc_min_price']      = min( $min );
						$minmax['tc_max_price']      = max( $max );
						$minmax['tc_min_variable']   = $min;
						$minmax['tc_max_variable']   = $max;
					}

					return $minmax;
				} else {
					return $this->product_minmax[ $id ];
				}
			} else {
				$this->product_minmax[ $id ] = false;
			}
		}

		return false;

	}

	/**
	 * Alter the price filter
	 *
	 * @param float        $price The product price.
	 * @param object|false $product The product object.
	 * @since 4.8.4
	 * @return float
	 */
	public function woocommerce_product_get_price( $price = 0, $product = false ) {

		if ( '' === $price ) {

			$minmax = $this->add_product_tc_prices( $product );
			if ( false !== $minmax ) {
				$price = 0;
			}
		}

		return $price;

	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @param float        $price The product price.
	 * @param object|false $product The product object.
	 * @since 1.0
	 * @return float
	 */
	public function tm_woocommerce_get_price( $price = 0, $product = false ) {

		$this->tm_woocommerce_get_price_flag ++;

		if ( 1 === $this->tm_woocommerce_get_price_flag ) {
			if ( ! is_admin() && ! $this->wc_vars['is_product'] && 'no' === $this->tm_epo_use_from_on_price ) {

				add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );

			} else {
				$minmax = $this->add_product_tc_prices( $product );
				if ( $minmax ) {
					add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
					add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				}
			}
		}

		return $price;

	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @param boolean      $show See if prices should be shown for each variation after selection.
	 * @param object|false $product The product object.
	 * @param object|false $variation The variable product object.
	 * @since 1.0
	 * @return bool
	 */
	public function tm_woocommerce_show_variation_price( $show = true, $product = false, $variation = false ) {

		if ( $product && $variation ) {
			$epos = $this->get_product_tm_epos( themecomplete_get_id( $product ), '', false, true );
			if ( is_array( $epos ) && ( ! empty( $epos['global'] ) || ! empty( $epos['local'] ) ) ) {
				if ( ! empty( $epos['price'] ) ) {
					$minmax = THEMECOMPLETE_EPO_HELPER()->sum_array_values( $epos );
					if ( ! empty( $minmax['max'] ) ) {
						$show = true;
					}
				}
			}
		}

		return $show;

	}

	/**
	 * Returns the product's active price
	 *
	 * @param object|false $product The product object.
	 * @since 1.0
	 * @return mixed
	 */
	public function tc_get_price( $product = false ) {

		$tc_min_price = 0;
		$id           = themecomplete_get_id( $product );
		if ( false !== $id && isset( $this->product_minmax[ $id ] ) ) {
			$tc_min_price = $this->product_minmax[ $id ]['tc_min_price'];
		}

		if ( empty( $this->product_minmax[ $id ]['is_override'] ) ) {
			$price = (float) apply_filters( 'wc_epo_product_price', $product->get_price() ) + (float) $tc_min_price;
		} else {
			$price = (float) $tc_min_price;
		}
		return apply_filters( 'tc_woocommerce_product_get_price', $price, $product );

	}

	/**
	 * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @param object|false $product The product object.
	 * @param mixed        $price The product price.
	 * @param integer      $qty The product quantity.
	 * @since 1.0
	 * @return string
	 */
	public function tc_get_display_price( $product = false, $price = '', $qty = 1 ) {

		if ( '' === $price ) {
			$price = $this->tc_get_price( $product );
		}

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price    = 'incl' === $tax_display_mode ? themecomplete_get_price_including_tax(
			$product,
			[
				'qty'   => $qty,
				'price' => $price,
			]
		) : themecomplete_get_price_excluding_tax(
			$product,
			[
				'qty'   => $qty,
				'price' => $price,
			]
		);

		return $display_price;

	}

	/**
	 * Returns the product's regular price.
	 *
	 * @param object|false $product The product object.
	 * @since 1.0
	 */
	public function tc_get_regular_price( $product = false ) {

		$tc_min_price = 0;
		$id           = themecomplete_get_id( $product );
		if ( isset( $this->product_minmax[ $id ] ) ) {
			$tc_min_price = $this->product_minmax[ $id ]['tc_min_price'];
		}
		return apply_filters( 'tc_woocommerce_product_get_regular_price', (float) apply_filters( 'wc_epo_product_price', $product->get_regular_price() ) + (float) $tc_min_price, $product );

	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @param mixed        $price The product price.
	 * @param object|false $product The product object.
	 * @since 1.0
	 */
	public function tm_get_price_html( $price = '', $product = false ) {

		$original_price = $price;

		$min_max = $this->get_product_min_max_prices( $product );
		$type    = themecomplete_get_product_type( $product );

		if ( empty( $min_max ) || 'variation' === $type ) {
			$check_filter_1 = has_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ] );
			$check_filter_2 = has_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ] );
			if ( $check_filter_1 ) {
				remove_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11 );
			}
			if ( $check_filter_2 ) {
				remove_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11 );
			}
			$price = $product->get_price_html();
			if ( $check_filter_1 ) {
				add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
			}
			if ( $check_filter_2 ) {
				add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
			}

			return $price;
		}

		$use_from  = ( 'yes' === $this->tm_epo_use_from_on_price );
		$free_text = ( 'yes' === $this->tm_epo_remove_free_price_label ) ? ( '' !== $this->tm_epo_replacement_free_price_text ? $this->tm_epo_replacement_free_price_text : '' ) : esc_attr__( 'Free!', 'woocommerce' );

		$min               = $min_max['min_raw'];
		$max               = $min_max['max_raw'];
		$min_price         = $min_max['min_price'];
		$max_price         = $min_max['max_price'];
		$min_regular_price = $min_max['min_regular_price'];
		$max_regular_price = $min_max['max_regular_price'];

		if ( 'variable' === $type || 'variable-subscription' === $type ) {
			$is_free = (float) 0 === (float) $min_price && (float) 0 === (float) $max_price;

			if ( $product->is_on_sale() ) {

				$displayed_price = ( function_exists( 'wc_get_price_to_display' )
					? wc_format_sale_price( $min_regular_price, $min_price )
					: '<del>' . ( is_numeric( $min_regular_price ) ? wc_price( $min_regular_price ) : $min_regular_price ) . '</del> <ins>' . ( is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price ) . '</ins>'
				);
				$price           = $min_price !== $max_price
					? ( ! $use_from
						/* translators: %1 %2: from price to price  */
						? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . $displayed_price )
					: $displayed_price;

				$regular_price = $min_regular_price !== $max_regular_price
					? ( ! $use_from
						/* translators: %1 %2: from price to price  */
						? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_regular_price ), themecomplete_price( $max_regular_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_regular_price ) )
					: themecomplete_price( $min_regular_price );
				$regular_price = '<del>' . $regular_price . '</del>';
				if ( $min_price === $max_price && $min_regular_price === $max_regular_price ) {
					$price = themecomplete_price( $max_price );
				}
				$price = ( ! $use_from ? ( $regular_price . ' <ins>' . $price . '</ins>' ) : $price ) . $product->get_price_suffix();

			} elseif ( $is_free ) {
				$price = apply_filters( 'woocommerce_variable_free_price_html', $free_text, $product );
			} else {
				$price = $min_price !== $max_price
					? ( ! $use_from
						/* translators: %1 %2: from price to price  */
						? sprintf( esc_html_x( '%1$s &ndash; %2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_price ) )
					: themecomplete_price( $min_price );
				$price = $price . $product->get_price_suffix();
			}
		} else {

			$display_price         = $min_price;
			$display_regular_price = $min_regular_price;

			$price = '';
			if ( $this->tc_get_price( $product ) > 0 ) {

				if ( $product->is_on_sale() && $this->tc_get_regular_price( $product ) ) {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {

						$displayed_price = ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( $display_regular_price, $display_price )
							: '<del>' . ( is_numeric( $display_regular_price ) ? wc_price( $display_regular_price ) : $display_regular_price ) . '</del> <ins>' . ( is_numeric( $display_price ) ? wc_price( $display_price ) : $display_price ) . '</ins>'
						);
						$price          .= ( function_exists( 'wc_get_price_html_from_text' )
								? wc_get_price_html_from_text()
								: $product->get_price_html_from_text() )
											. $displayed_price;
						$price          .= $product->get_price_suffix();
					} else {
						$price .= $original_price;
					}
				} else {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() );
					}
					$price .= themecomplete_price( $display_price ) . $product->get_price_suffix();

				}
			} elseif ( $this->tc_get_price( $product ) === '' ) {

				$price = apply_filters( 'woocommerce_empty_price_html', '', $product );

			} elseif ( (float) $this->tc_get_price( $product ) === (float) 0 ) {
				if ( $product->is_on_sale() && $this->tc_get_regular_price( $product ) ) {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min > 0 ) ? $min : 0 );
					} else {

						$price .= $original_price;

						$price = apply_filters( 'woocommerce_free_sale_price_html', $price, $product );
					}
				} else {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min > 0 ) ? $min : 0 );
					} else {

						$price = '<span class="amount">' . $free_text . '</span>';

						$price = apply_filters( 'woocommerce_free_price_html', $price, $product );
					}
				}
			}
		}

		return apply_filters( 'wc_epo_get_price_html', $price, $product );

	}

	/**
	 * Image filter
	 *
	 * @param string $url The image url.
	 * @since 1.0
	 */
	public function tm_image_url( $url = '' ) {

		if ( is_array( $url ) ) {
			foreach ( $url as $url_key => $url_value ) {
				if ( ! is_array( $url_value ) ) {
					$url[ $url_key ] = $this->get_cdn_url( $url_value );
				}
			}
		} else {
			$url = $this->get_cdn_url( $url );
		}

		// SSL support.
		$url = THEMECOMPLETE_EPO_HELPER()->to_ssl( $url );

		return $url;

	}

	/**
	 * Get cdn url
	 *
	 * @param string $url The cdn url.
	 * @since 6.0
	 */
	public function get_cdn_url( $url = '' ) {

		if ( is_admin() || is_array( $url ) ) {
			return $url;
		}

		$ext = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );

		if ( 'php' === $ext ) {
			return $url;
		}

		// WP Rocket cdn.
		if ( 'yes' === $this->tm_epo_global_cdn_rocket && defined( 'WP_ROCKET_VERSION' ) && function_exists( 'get_rocket_cdn_cnames' ) && function_exists( 'get_rocket_cdn_url' ) ) {
			$zone   = [ 'all', 'images' ];
			$cnames = get_rocket_cdn_cnames( $zone );
			if ( $cnames ) {
				$url = get_rocket_cdn_url( $url, $zone );
			}
		}

		// Jetpack cdn.
		if ( 'yes' === $this->tm_epo_global_cdn_jetpack && function_exists( 'jetpack_photon_url' ) && is_string( $url ) ) {
			$url = jetpack_photon_url( $url );
		}

		return $url;

	}

	/**
	 * Flag related products start
	 *
	 * @since 1.0
	 */
	public function tm_enable_post_class() {

		$this->tm_related_products_output = true;

	}

	/**
	 * Flag related products end
	 *
	 * @since 1.0
	 */
	public function tm_disable_post_class() {

		$this->tm_related_products_output = false;

	}

	/**
	 * Flag related upsells start
	 *
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function tm_woocommerce_related_products_args( $args ) {

		$this->tm_disable_post_class();
		$this->in_related_upsells = true;

		return $args;

	}

	/**
	 * Flag related upsells end
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_after_single_product_summary() {

		$this->in_related_upsells = false;

	}

	/**
	 * Add custom class to the body tag
	 *
	 * @param array $classes Array of classes.
	 * @since 1.0
	 */
	public function tm_body_class( $classes = [] ) {

		$post_id = get_the_ID();

		if (
			// disable in admin interface.
			is_admin() ||

			// disable if not in the product div.
			! $this->tm_related_products_output ||

			// disable if not in a product page, shop or product archive page.
			! (
				'product' === get_post_type( $post_id ) ||
				$this->wc_vars['is_product'] ||
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_cart'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			) ||

			// disable if options are not visible in shop/archive pages.
			( (
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			)
			&&
			'no' === $this->tm_epo_enable_in_shop
			)

		) {
			return $classes;
		}

		if ( 'yes' === $this->tm_epo_responsive_display ) {
			$classes[] = 'tm-responsive';
		}

		return $classes;

	}

	/**
	 * Add custom class to product div used to initialize the plugin JavaScript
	 *
	 * @param array $classes Array of classes.
	 * @since 1.0
	 */
	public function tm_post_class( $classes = [] ) {

		$post_id = get_the_ID();

		if (
			// disable in admin interface.
			is_admin() ||

			// disable if not in the product div.
			! $this->tm_related_products_output ||

			// disable if not in a product page, shop or product archive page.
			! (
				'product' === get_post_type( $post_id ) ||
				$this->wc_vars['is_product'] ||
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			) ||

			// disable if options are not visible in shop/archive pages.
			( (
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			)
			&&
			'no' === $this->tm_epo_enable_in_shop
			)

		) {
			return $classes;
		}

		// enabling "global $post;" here will cause issues on certain Visual composer shortcodes.

		if ( $post_id && ( $this->wc_vars['is_product'] || 'product' === get_post_type( $post_id ) ) ) {

			$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );

			// Product has styled variations.
			if ( ! empty( $has_epo['variations'] ) && empty( $has_epo['variations_disabled'] ) ) {
				$classes[] = 'tm-has-styled-variations';
			}

			// Product has extra options.
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$classes[] = 'tm-has-options';

				// Product doesn't have extra options but the final total box is enabled for all products.
			} elseif ( 'yes' === $this->tm_epo_enable_final_total_box_all ) {

				$classes[] = 'tm-no-options-pxq';

				// Search for composite products extra options.
			} else {

				$extra_classes = apply_filters( 'wc_epo_tm_post_class_no_options', [], $post_id );

				if ( ! empty( $extra_classes ) ) {
					$classes = array_merge( $classes, $extra_classes );
				} else {
					if ( isset( $has_epo['variations'] ) && ! empty( $has_epo['variations'] ) && isset( $has_epo['variations_disabled'] ) && empty( $has_epo['variations_disabled'] ) ) {
						$classes[] = 'tm-variations-only';
					} else {
						$classes[] = 'tm-no-options';
					}
				}
			}
		}

		return $classes;

	}

	/**
	 * Check if we are in edit mode
	 *
	 * @since 1.0
	 */
	public function is_edit_mode() {

		return ! empty( $this->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'tm-edit' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	}

	/**
	 * Check if the plugin is active for the user
	 *
	 * @since 1.0
	 */
	public function check_enable() {

		$enable         = false;
		$enabled_roles  = $this->tm_epo_roles_enabled;
		$disabled_roles = $this->tm_epo_roles_disabled;

		if ( isset( $this->tm_meta_cpf['override_enabled_roles'] ) && '' !== $this->tm_meta_cpf['override_enabled_roles'] ) {
			$enabled_roles = $this->tm_meta_cpf['override_enabled_roles'];
		}
		if ( isset( $this->tm_meta_cpf['override_disabled_roles'] ) && '' !== $this->tm_meta_cpf['override_disabled_roles'] ) {
			$disabled_roles = $this->tm_meta_cpf['override_disabled_roles'];
		}
		// Get all roles.
		$current_user = wp_get_current_user();

		if ( ! is_array( $enabled_roles ) ) {
			$enabled_roles = [ $enabled_roles ];
		}
		if ( ! is_array( $disabled_roles ) ) {
			$disabled_roles = [ $disabled_roles ];
		}

		// Check if plugin is enabled for everyone.
		foreach ( $enabled_roles as $key => $value ) {
			if ( '@everyone' === $value ) {
				$enable = true;
			}
			if ( '@loggedin' === $value && is_user_logged_in() ) {
				$enable = true;
			}
		}

		if ( $current_user instanceof WP_User ) {
			$roles = $current_user->roles;
			// Check if plugin is enabled for current user.
			if ( is_array( $roles ) ) {

				foreach ( $roles as $key => $value ) {
					if ( in_array( $value, $enabled_roles, true ) ) {
						$enable = true;
						break;
					}
				}

				foreach ( $roles as $key => $value ) {
					if ( in_array( $value, $disabled_roles, true ) ) {
						$enable = false;
						break;
					}
				}
			}
		}

		return $enable;

	}

	/**
	 * Check if we are on a supported quickview mode
	 *
	 * @since 1.0
	 */
	public function is_quick_view() {

		return apply_filters( 'woocommerce_tm_quick_view', false );

	}

	/**
	 * Check if the setting "Enable plugin for WooCommerce shortcodes" is active
	 *
	 * @since 1.0
	 */
	public function is_enabled_shortcodes() {
		return 'yes' === $this->tm_epo_enable_shortcodes;

	}

	/**
	 * Apply wc_epo_get_current_currency_price filter to prices
	 *
	 * @param mixed  $price The option price.
	 * @param string $type The option type.
	 * @since 1.0
	 */
	public function tm_epo_price_filtered( $price = '', $type = '' ) {

		return apply_filters( 'wc_epo_get_current_currency_price', $price, $type );

	}

	/**
	 * Enable shortcodes for labels
	 *
	 * @param string       $label The element label.
	 * @param array|null   $args The element array.
	 * @param integer|null $counter The choice counter.
	 * @param string|null  $value The choice value.
	 * @param string|null  $vlabel The choice label.
	 * @since 1.0
	 */
	public function tm_epo_option_name( $label = '', $args = null, $counter = null, $value = null, $vlabel = null ) {

		if ( ( null === $this->associated_per_product_pricing || 1 === $this->associated_per_product_pricing ) &&
			'yes' === $this->tm_epo_show_price_inside_option &&
			( empty( $args['hide_amount'] ) || 'yes' === $this->tm_epo_show_price_inside_option_hidden_even ) &&
			null !== $value &&
			null !== $vlabel &&
			isset( $args['rules_type'] ) &&
			isset( $args['rules_type'][ $value ] ) &&
			isset( $args['rules_type'][ $value ][0] ) &&
			empty( $args['rules_type'][ $value ][0] )
		) {
			$display_price = ( isset( $args['rules_filtered'][ $value ][0] ) ) ? $args['rules_filtered'][ $value ][0] : '';
			$qty           = 1;

			if ( 'yes' === $this->tm_epo_multiply_price_inside_option ) {
				if ( ! empty( $args['quantity'] ) && ! empty( $args['quantity_default_value'] ) ) {
					$qty = floatval( $args['quantity_default_value'] );
				}
			}
			$display_price = floatval( $display_price ) * $qty;

			if ( ( 'yes' === $this->tm_epo_auto_hide_price_if_zero && ! empty( $display_price ) ) || ( 'yes' !== $this->tm_epo_auto_hide_price_if_zero && '' !== $display_price ) ) {
				$symbol = '';
				if ( '' === $this->tm_epo_global_options_price_sign ) {
					$symbol = apply_filters( 'wc_epo_price_in_dropdown_plus_sign', '+' );
				}

				global $product, $associated_product;
				$current_product = $product;
				if ( ! $product && $associated_product ) {
					$current_product = $associated_product;
				}
				if ( $current_product && wc_tax_enabled() ) {
					$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

					if ( 'excl' === $tax_display_mode ) {
						$display_price = themecomplete_get_price_excluding_tax( $current_product, [ 'price' => $display_price ] );
					} else {
						$display_price = themecomplete_get_price_including_tax( $current_product, [ 'price' => $display_price ] );
					}
				}

				if ( floatval( $display_price ) === 0 ) {
					$symbol = '';
				} elseif ( floatval( $display_price ) < 0 ) {
					$symbol = apply_filters( 'wc_epo_price_in_dropdown_minus_sign', '-' );
				}
				$display_price = apply_filters( 'wc_epo_price_in_dropdown', ' (' . $symbol . wc_price( abs( $display_price ) ) . ')', $display_price );

				$label .= $display_price;

			}
		}

		return apply_filters( 'wc_epo_label', apply_filters( 'wc_epo_kses', $label, $label ) );

	}

	/**
	 * Alters the Free label html
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @since 1.0
	 */
	public function get_price_html( $price = '', $product = '' ) {

		if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {
			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}
		} else {
			return sprintf( $this->tm_epo_replacement_free_price_text, $price );
		}

	}

	/**
	 * Fix for related products when replacing free label
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @since 1.0
	 */
	public function related_get_price_html( $price = '', $product = '' ) {

		if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {
			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {
				if ( $this->tm_epo_replacement_free_price_text ) {
					return sprintf( $this->tm_epo_replacement_free_price_text, $price );
				} else {
					$price = '';
				}
			}
		} else {
			if ( $this->tm_epo_replacement_free_price_text ) {
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			} else {
				$price = '';
			}
		}

		return $price;

	}

	/**
	 * Fix for related products when replacing free label
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @since 1.0
	 */
	public function related_get_price_html2( $price = '', $product = '' ) {

		if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {

			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {

				$thiscpf = $this->get_product_tm_epos( themecomplete_get_id( $product ), '', false, true );

				if ( is_array( $thiscpf ) && ( ! empty( $thiscpf['global'] ) || ! empty( $thiscpf['local'] ) ) ) {
					if ( $this->tm_epo_replacement_free_price_text ) {
						return sprintf( $this->tm_epo_replacement_free_price_text, $price );
					} else {
						$price = '';
					}
				}
			}
		}

		return $price;

	}

	/**
	 * Free label text replacement
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @since 1.0
	 */
	public function get_price_html_shop( $price = '', $product = '' ) {

		if ( $product &&
			is_object( $product ) && is_callable( [ $product, 'get_price' ] )
			&& ! (float) $product->get_price() > 0
		) {

			if ( $this->tm_epo_replacement_free_price_text ) {
				$price = sprintf( $this->tm_epo_replacement_free_price_text, $price );
			} else {
				$price = '';
			}
		}

		return $price;

	}

	/**
	 * Replaces add to cart text when the force select setting is enabled
	 *
	 * @param string $text The add to cart text.
	 * @since 1.0
	 */
	public function add_to_cart_text( $text = '' ) {

		global $product;

		if ( ( is_product() && ! $this->in_related_upsells ) || $this->is_in_product_shortcode ) {
			return $text;
		}
		if ( 'no' === $this->tm_epo_enable_in_shop
			&& 'yes' === $this->tm_epo_force_select_options
			&& is_object( $product )
			&& property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$text = ( ! empty( $this->tm_epo_force_select_text ) ) ? esc_html( $this->tm_epo_force_select_text ) : esc_html__( 'Select options', 'woocommerce-tm-extra-product-options' );
			}
		}
		if ( 'yes' === $this->tm_epo_enable_in_shop && ! $this->in_related_upsells ) {
			$text = esc_html__( 'Add to cart', 'woocommerce' );
		}

		return $text;

	}

	/**
	 * Prevenets ajax add to cart when product has extra options and the force select setting is enabled
	 *
	 * @param string $url The url.
	 * @since 1.0
	 */
	public function add_to_cart_url( $url = '' ) {

		global $product;

		if ( ! is_product()
			&& 'yes' === $this->tm_epo_force_select_options
			&& is_object( $product )
			&& property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$url = get_permalink( themecomplete_get_id( $product ) );
			}
		}

		return $url;

	}

	/**
	 * Redirect to product URL
	 * THis is used when using the forced select setting
	 *
	 * @param string  $url The url to redirect to.
	 * @param integer $product_id The product id.
	 * @since 1.0
	 */
	public function woocommerce_cart_redirect_after_error( $url = '', $product_id = 0 ) {

		$product = wc_get_product( $product_id );

		if ( 'yes' === $this->tm_epo_force_select_options
			&& is_object( $product )
			&& property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$url = get_permalink( themecomplete_get_id( $product ) );
			}
		}

		return $url;

	}

	/**
	 * Sets current product settings
	 *
	 * @param integer $override_id Set meta or not.
	 * @since 1.0
	 */
	public function set_tm_meta( $override_id = 0 ) {

		if ( empty( $override_id ) ) {
			if ( isset( $_REQUEST['add-to-cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$override_id = absint( wp_unslash( $_REQUEST['add-to-cart'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} else {
				global $post;
				if ( ! is_null( $post ) && property_exists( $post, 'ID' ) && property_exists( $post, 'post_type' ) ) {
					if ( 'product' !== $post->post_type ) {
						return;
					}
					$override_id = $post->ID;
				}
			}
		}
		if ( empty( $override_id ) ) {
			return;
		}

		// Translated products inherit original product meta overrides.
		$override_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $override_id, 'product' ) );

		$this->tm_meta_cpf = themecomplete_get_post_meta( $override_id, 'tm_meta_cpf', true );
		if ( ! is_array( $this->tm_meta_cpf ) ) {
			$this->tm_meta_cpf = [];
		}
		foreach ( $this->meta_fields as $key => $value ) {
			$this->tm_meta_cpf[ $key ] = isset( $this->tm_meta_cpf[ $key ] ) ? $this->tm_meta_cpf[ $key ] : $value;
		}
		$this->tm_meta_cpf['metainit'] = 1;

	}

	/**
	 * Calculates the formula price
	 *
	 * @param string        $_price The math formula.
	 * @param array         $post_data The posted data.
	 * @param array         $element The element array.
	 * @param string        $key The posted element value.
	 * @param string        $attribute The posted element name.
	 * @param integer|false $attribute_quantity The option quantity of this element.
	 * @param integer       $key_id The array key of the posted element values array.
	 * @param integer       $keyvalue_id The array key for the values of the posted element values array.
	 * @param boolean       $per_product_pricing If the product has pricing, true or false.
	 * @param mixed         $cpf_product_price The product price.
	 * @param integer       $variation_id The variation id.
	 * @param integer       $price_default_value The value to return if the formula fails.
	 * @param string        $currency The currency to set the result to.
	 * @param string        $current_currency The current currency.
	 * @param array         $price_per_currencies The price per currencies array.
	 * @param array         $tmdata Saved tmdata array.
	 * @since 1.0
	 */
	public function calculate_math_price( $_price = '', $post_data = [], $element = [], $key = null, $attribute = null, $attribute_quantity = false, $key_id = 0, $keyvalue_id = 0, $per_product_pricing = null, $cpf_product_price = false, $variation_id = 0, $price_default_value = 0, $currency = false, $current_currency = false, $price_per_currencies = null, $tmdata = [] ) {

		$formula = $_price;

		// This happens when the user has prevented the totals box from being displayed.
		if ( ! isset( $post_data['tc_form_prefix'] ) ) {
			$post_data['tc_form_prefix'] = '';
		}

		$form_prefix = $post_data['tc_form_prefix'];

		if ( false !== $this->associated_element_uniqid && isset( $post_data['tc_form_prefix_assoc'] ) && isset( $post_data['tc_form_prefix_assoc'][ $this->associated_element_uniqid ] ) ) {
			$form_prefix = $post_data['tc_form_prefix_assoc'][ $this->associated_element_uniqid ];
			if ( is_array( $form_prefix ) ) {
				$form_prefix = THEMECOMPLETE_EPO()->associated_product_formprefix;
			}
		}
		if ( '' !== $form_prefix ) {
			$form_prefix = str_replace( '_', '', $form_prefix );
			$form_prefix = '_' . $form_prefix;
		}

		$current_id         = $element['uniqid'] . $form_prefix;
		$current_attributes = THEMECOMPLETE_EPO_CART()->element_id_array[ $current_id ]['name_inc'];
		if ( $current_attributes ) {
			if ( ! is_array( $current_attributes ) ) {
				$current_attributes = [ $current_attributes ];
			}
		} else {
			$current_attributes = [];
		}

		// constants.
		$constants = json_decode( $this->tm_epo_math, true );
		if ( is_array( $constants ) ) {
			foreach ( $constants as $constant ) {
				if ( '' !== $constant['name'] && '' !== $constant['value'] ) {
					if ( THEMECOMPLETE_EPO_HELPER()->str_startswith( $constant['value'], '{' ) ) {
						$formula = str_replace( '{' . $constant['name'] . '}', $constant['value'], $formula );
					} else {
						$formula = str_replace( '{' . $constant['name'] . '}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( themecomplete_convert_local_numbers( $constant['value'] ) ), true ), $formula );
					}
				}
			}
		}

		// Replaces any number between curly braces with the current currency.
		$formula = preg_replace_callback(
			'/\{(\d+)\}/',
			function( $matches ) {
				return apply_filters( 'wc_epo_get_currency_price', $matches[1], false, '' );
			},
			$formula
		);

		// the number of options the user has selected.
		$formula = str_replace( '{this.count}', floatval( count( array_intersect_key( $post_data, array_flip( $current_attributes ) ) ) ), $formula );

		// the total option quantity of this element.
		$current_attributes_quantity = array_map(
			function ( $y ) {
				return $y . '_quantity';
			},
			$current_attributes
		);
		$quantity_intersect          = array_intersect_key( $post_data, array_flip( $current_attributes_quantity ) );
		$quantity_intersect          = array_map(
			function ( $y ) {
				if ( is_array( $y ) ) {
					$y = array_sum(
						array_map(
							function ( $x ) {
								if ( is_array( $x ) ) {
									$x = array_sum( $x );
								} return $x;
							},
							$y
						)
					);
				} return $y;
			},
			$quantity_intersect
		);

		$formula = str_replace( '{this.count.quantity}', floatval( array_sum( (array) $quantity_intersect ) ), $formula );

		// the option quantity of this element.
		$current_quantity = '';
		if ( false === $attribute_quantity ) {
			if ( isset( $post_data[ $attribute . '_quantity' ] ) ) {
				$attribute_quantity = $post_data[ $attribute . '_quantity' ];
			}
		}
		if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $key_id ] ) ) {
			$attribute_quantity = $attribute_quantity[ $key_id ];
			if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $keyvalue_id ] ) ) {
				$attribute_quantity = $attribute_quantity[ $keyvalue_id ];
			}
		}
		$current_quantity = $attribute_quantity;

		// the option/element quantity.
		$formula = str_replace( '{this.quantity}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $current_quantity ), true ), $formula );

		if ( isset( $element['options'] ) && isset( $element['options'][ $key ] ) ) {
			// the option/element value.
			$formula = str_replace( '{this.value}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $element['options'][ $key ] ), true ), $formula );
			// the option/element value length.
			$formula = str_replace( '{this.value.length}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( strlen( $element['options'][ $key ] ) ), true ), $formula );
		} else {
			// the option/element value.
			if ( isset( $post_data[ $attribute ] ) ) {
				$attribute_value = $post_data[ $attribute ];
			} elseif ( isset( $tmdata['tmcp_post_fields'][ $attribute ] ) ) {
				$attribute_value = $tmdata['tmcp_post_fields'][ $attribute ];
			} elseif ( isset( $_REQUEST[ $attribute ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$attribute_value = wp_unslash( $_REQUEST[ $attribute ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} elseif ( isset( $_FILES[ $attribute ] ) && isset( $_FILES[ $attribute ]['name'] ) ) {
				$attribute_value = wp_unslash( $_FILES[ $attribute ]['name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} else {
				$attribute_value = '';
			}

			if ( is_array( $attribute_value ) && isset( $attribute_value[ $key_id ] ) ) {
				$attribute_value = $attribute_value[ $key_id ];
				if ( is_array( $attribute_value ) && isset( $attribute_value[ $keyvalue_id ] ) ) {
					$attribute_value = $attribute_value[ $keyvalue_id ];
				}
			}
			$formula = str_replace( '{this.value}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $attribute_value ), true ), $formula );
			// the option/element value length.
			$formula = str_replace( '{this.value.length}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( strlen( $attribute_value ) ), true ), $formula );
		}

		// product quantity.
		if ( isset( $post_data['quantity'] ) ) {
			$formula = str_replace( '{quantity}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $post_data['quantity'] ), true ), $formula );
		}

		// original product price.
		$product_price = $cpf_product_price;
		if ( ! $product_price ) {
			$product_price = 0;
		}

		$formula = str_replace( '{product_price}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $product_price ), true ), $formula );

		preg_match_all( '/\{(\s)*?field\.([^}]*)}/', $formula, $matches );

		if ( is_array( $matches ) && isset( $matches[2] ) && is_array( $matches[2] ) ) {

			foreach ( $matches[2] as $matchkey => $match ) {
				$val  = 0;
				$type = '';
				$pos  = strrpos( $match, '.' );

				if ( false !== $pos ) {

					$id     = substr( $match, 0, $pos );
					$pos_id = strrpos( $id, '.' );
					if ( substr_count( $id, '.' ) > 1 && false !== $pos_id ) {
						$id  = substr( $id, 0, $pos_id );
						$pos = $pos_id;
					}
					$id   = $id . $form_prefix;
					$type = substr( $match, $pos + 1 );
					if ( 'text' === $type || 'rawvalue' === $type ) {
						$val = '';
					}

					$thiselement = isset( THEMECOMPLETE_EPO_CART()->element_id_array[ $id ] ) ? THEMECOMPLETE_EPO_CART()->element_id_array[ $id ] : null;
					if ( $thiselement ) {

						$priority              = $thiselement['priority'];
						$pid                   = $thiselement['pid'];
						$section_id            = $thiselement['section_id'];
						$element_key           = $thiselement['element_key'];
						$thiselement           = THEMECOMPLETE_EPO_CART()->global_price_array[ $priority ][ $pid ]['sections'][ $section_id ]['elements'][ $element_key ];
						$_price_per_currencies = isset( $thiselement['price_per_currencies'] ) ? $thiselement['price_per_currencies'] : [];

						$thisattributes = THEMECOMPLETE_EPO_CART()->element_id_array[ $id ]['name_inc'];
						if ( ! is_array( $thisattributes ) ) {
							$thisattributes = [ $thisattributes ];
						}

						$thisattributes = array_unique( $thisattributes );

						if ( is_array( $thisattributes ) ) {
							foreach ( $thisattributes as $thisattribute ) {
								if ( ! isset( $post_data[ $thisattribute ] ) ) {
									continue;
								}
								$thiskey = $post_data[ $thisattribute ];

								if ( in_array( $type, [ 'price', 'value', 'value.length', 'rawvalue', 'text', 'text.length', 'quantity' ], true ) ) {
									switch ( $type ) {
										case 'price':
											$_price_type = $this->get_element_price_type( '', $thiselement, $thiskey, $per_product_pricing, $variation_id );
											// The price types percentcurrenttotal and fixedcurrenttotal
											// create a vicious circle when used with the math formula.
											if ( 'percentcurrenttotal' !== $_price_type && 'fixedcurrenttotal' !== $_price_type ) {
												if ( is_array( $thiskey ) ) {
													foreach ( $thiskey as $thiskey_id => $thiskey_value ) {
														if ( ! is_array( $thiskey_value ) ) {
															$thiskey_value = [ $thiskey_value ];
														}
														foreach ( $thiskey_value as $thiskeyvalue_id => $thiskeyvalue_value ) {
															$val += floatval( $this->calculate_price( $post_data, $thiselement, $thiskey, $thisattribute, false, $thiskey_id, $thiskeyvalue_id, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $_price_per_currencies, $tmdata ) );
														}
													}
												} else {
													$val += floatval( $this->calculate_price( $post_data, $thiselement, $thiskey, $thisattribute, false, 0, 0, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $_price_per_currencies, $tmdata ) );
												}
											}
											break;
										case 'value':
										case 'text':
										case 'rawvalue':
											if ( is_array( $thiskey ) ) {
												foreach ( $thiskey as $thiskey_id => $thiskey_value ) {
													if ( ! is_array( $thiskey_value ) ) {
														$thiskey_value = [ $thiskey_value ];
													}
													foreach ( $thiskey_value as $thiskeyvalue_id => $thiskeyvalue_value ) {
														if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskeyvalue_value ] ) ) {
															if ( 'text' === $type || 'rawvalue' === $type ) {
																$temp_value = $thiselement['options'][ $thiskeyvalue_value ];
																if ( '' === $temp_value ) {
																	$temp_value = "''";
																} elseif ( ! is_numeric( $temp_value ) ) {
																	$temp_value = "'" . $temp_value . "'";
																}
																$val .= $temp_value;
															} else {
																$val += THEMECOMPLETE_EPO_HELPER()->unformat( $thiselement['options'][ $thiskeyvalue_value ] );
															}
														} else {
															if ( 'text' === $type || 'rawvalue' === $type ) {
																$temp_value = $thiskeyvalue_value;
																if ( '' === $temp_value ) {
																	$temp_value = "''";
																} elseif ( ! is_numeric( $temp_value ) ) {
																	$temp_value = "'" . $temp_value . "'";
																}
																$val .= $temp_value;
															} else {
																$val += THEMECOMPLETE_EPO_HELPER()->unformat( $thiskeyvalue_value );
															}
														}
													}
												}
											} else {
												if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskey ] ) ) {
													if ( 'text' === $type || 'rawvalue' === $type ) {
														$temp_value = $thiselement['options'][ $thiskey ];
														if ( '' === $temp_value ) {
															$temp_value = "''";
														} elseif ( ! is_numeric( $temp_value ) ) {
															$temp_value = "'" . $temp_value . "'";
														}
														$val .= $temp_value;
													} else {
														$val += THEMECOMPLETE_EPO_HELPER()->unformat( $thiselement['options'][ $thiskey ] );
													}
												} else {
													if ( 'select' === $thiselement['type'] ) {
														$thiskey = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $thiskey, '_' );
													}
													if ( 'text' === $type || 'rawvalue' === $type ) {
														$temp_value = $thiskey;
														if ( '' === $temp_value ) {
															$temp_value = "''";
														} elseif ( ! is_numeric( $temp_value ) ) {
															$temp_value = "'" . $temp_value . "'";
														}
														$val .= $temp_value;
													} else {
														$val += THEMECOMPLETE_EPO_HELPER()->unformat( $thiskey );
													}
												}
											}
											break;
										case 'value.length':
										case 'text.length':
											if ( is_array( $thiskey ) ) {
												foreach ( $thiskey as $thiskey_id => $thiskey_value ) {
													if ( ! is_array( $thiskey_value ) ) {
														$thiskey_value = [ $thiskey_value ];
													}
													foreach ( $thiskey_value as $thiskeyvalue_id => $thiskeyvalue_value ) {
														if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskeyvalue_value ] ) ) {
															$val += strlen( $thiselement['options'][ $thiskeyvalue_value ] );
														} else {
															$val += strlen( $thiskeyvalue_value );
														}
													}
												}
											} else {
												if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskey ] ) ) {
													$val += strlen( $thiselement['options'][ $thiskey ] );
												} else {
													$val += strlen( $thiskey );
												}
											}
											break;
										case 'quantity':
										case 'count.quantity':
											if ( isset( $post_data[ $thisattribute . '_quantity' ] ) ) {
												$thisquantity = array_map(
													function ( $y ) {
														if ( is_array( $y ) ) {
															$y = array_sum(
																array_map(
																	function ( $x ) {
																		if ( is_array( $x ) ) {
																			$x = array_sum( $x );
																		} return $x;
																	},
																	$y
																)
															);
														} return $y;
													},
													(array) $post_data[ $thisattribute . '_quantity' ]
												);
												$val         += floatval( array_sum( $thisquantity ) );
											}
											break;
										case 'count':
											++ $val;
											break;
									}
								}
							}

							if ( 'count' === $type ) {
								$val = floatval( count( array_intersect_key( $post_data, array_flip( $thisattributes ) ) ) );
							}
						}
					}
				}
				if ( ! is_numeric( $val ) && ( 'text' === $type || 'rawvalue' === $type ) ) {
					// This can happen if the value of the element is not posted, like a radio button that is not selected.
					if ( '' === $val ) {
						$val = "''";
					}
					$formula = str_replace( $matches[0][ $matchkey ], $val, $formula );
				} else {
					$val     = THEMECOMPLETE_EPO_HELPER()->convert_to_number( $val, true );
					$formula = str_replace( $matches[0][ $matchkey ], $val, $formula );
				}
			}
		}

		$formula = themecomplete_convert_local_numbers( $formula );

		// Do the math.
		if ( version_compare( phpversion(), THEMECOMPLETE_EPO_PHP_VERSION, '<' ) ) {
			return $formula ? THEMECOMPLETE_EPO_MATH_DEPRECATED::evaluate( $formula ) : 0;
		}
		return $formula ? THEMECOMPLETE_EPO_MATH::evaluate( $formula ) : 0;

	}

	/**
	 * Get element's saved price type
	 *
	 * @param array $tmcp Saved element data.
	 * @param mixed $key The posted element value.
	 *
	 * @return string
	 */
	public function get_saved_element_price_type( $tmcp = [], $key = false ) {
		$price_type = '';
		$key        = ( false !== $key ) ? $key : ( isset( $tmcp['key'] ) ? $tmcp['key'] : 0 );
		$key        = esc_attr( $key );

		if ( ! isset( $tmcp['element']['rules_type'][ $key ] ) ) {// field price rule.
			if ( isset( $tmcp['element']['rules_type'][0][0] ) ) {// general rule.
				$price_type = $tmcp['element']['rules_type'][0][0];
			}
		} else {
			if ( isset( $tmcp['element']['rules_type'][ $key ][0] ) ) {// general field variation rule.
				$price_type = $tmcp['element']['rules_type'][ $key ][0];
			} elseif ( isset( $tmcp['element']['rules_type'][0][0] ) ) {// general rule.
				$price_type = $tmcp['element']['rules_type'][0][0];
			}
		}

		return $price_type;
	}

	/**
	 * Get element's saved price
	 *
	 * @param array        $tmcp Saved element data.
	 * @param array        $element The element array.
	 * @param string|false $key The posted element value.
	 *
	 * @return string
	 */
	public function get_saved_element_price( $tmcp = [], $element = [], $key = false ) {
		$price = '';
		$key   = ( false !== $key ) ? $key : ( isset( $tmcp['key'] ) ? $tmcp['key'] : 0 );
		$key   = esc_attr( $key );

		if ( THEMECOMPLETE_EPO_WPML()->is_multi_currency() && isset( $element['price_rules'][ $key ] ) ) {
			if ( isset( $element['price_rules'][ $key ][0] ) ) {// general rule.
				$price = $element['price_rules'][ $key ][0];
			}
		} elseif ( isset( $element['price_rules'][ $key ] ) && isset( $element['price_rules'][ $key ][0] ) && '' !== $element['price_rules'][ $key ][0] ) {
			$price = $element['price_rules'][ $key ][0];
		} else {
			if ( ! isset( $tmcp['element']['rules'][ $key ] ) ) {// field price rule.
				if ( isset( $tmcp['element']['rules'][0][0] ) ) {// general rule.
					$price = $tmcp['element']['rules'][0][0];
				}
			} else {
				if ( isset( $tmcp['element']['rules'][ $key ][0] ) ) {// general field variation rule.
					$price = $tmcp['element']['rules'][ $key ][0];
				} elseif ( isset( $tmcp['element']['rules'][0][0] ) ) {// general rule.
					$price = $tmcp['element']['rules'][0][0];
				}
			}
		}

		return $price;
	}

	/**
	 * Get the element price type
	 *
	 * @param string       $price_type_default_value The default price type.
	 * @param array        $element The element array.
	 * @param string|null  $key The posted element value.
	 * @param boolean|null $per_product_pricing If the product has pricing, true or false.
	 * @param integer|null $variation_id The variation id.
	 * @since 5.0.11
	 */
	public function get_element_price_type( $price_type_default_value = '', $element = [], $key = null, $per_product_pricing = null, $variation_id = null ) {

		$_price_type = $price_type_default_value;
		// This currently happens for multiple file uploads.
		if ( is_array( $key ) ) {
			$key = 0;
		}
		$key = esc_attr( $key );
		if ( $per_product_pricing ) {

			if ( ! isset( $element['price_rules_type'][ $key ] ) ) {// field price rule.
				if ( $variation_id && isset( $element['price_rules_type'][0][ $variation_id ] ) ) {// general variation rule.
					$_price_type = $element['price_rules_type'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule.
					$_price_type = $element['price_rules_type'][0][0];
				}
			} else {
				if ( $variation_id && isset( $element['price_rules_type'][ $key ][ $variation_id ] ) ) {// field price rule.
					$_price_type = $element['price_rules_type'][ $key ][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][ $key ][0] ) ) {// general field variation rule.
					$_price_type = $element['price_rules_type'][ $key ][0];
				} elseif ( $variation_id && isset( $element['price_rules_type'][0][ $variation_id ] ) ) {// general variation rule.
					$_price_type = $element['price_rules_type'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule.
					$_price_type = $element['price_rules_type'][0][0];
				}
			}
		}

		return $_price_type;
	}

	/**
	 * Get the element price
	 *
	 * @param mixed        $price_default_value The default value to return.
	 * @param string       $_price_type The price type.
	 * @param array        $element The element array.
	 * @param string|null  $key The posted element value.
	 * @param boolean|null $per_product_pricing If the product has pricing, true or false.
	 * @param integer|null $variation_id The variation id.
	 * @param array|null   $price_per_currencies The price per currencies array.
	 * @param string|null  $currency The currency to set the result to.
	 * @since 6.0
	 */
	public function get_element_price( $price_default_value = 0, $_price_type = '', $element = [], $key = null, $per_product_pricing = null, $variation_id = null, $price_per_currencies = null, $currency = null ) {

		$_price = $price_default_value;
		// This currently happens for multiple file uploads.
		if ( is_array( $key ) ) {
			$key = 0;
		}
		$key = esc_attr( $key );
		if ( $per_product_pricing ) {

			if ( ! isset( $element['price_rules'][ $key ] ) ) {// field price rule.
				if ( $variation_id && isset( $element['price_rules'][0][ $variation_id ] ) ) {// general variation rule.
					$_price = $element['price_rules'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule.
					$_price = $element['price_rules'][0][0];
				}
			} else {
				if ( $variation_id && isset( $element['price_rules'][ $key ][ $variation_id ] ) ) {// field price rule.
					$_price = $element['price_rules'][ $key ][ $variation_id ];
				} elseif ( isset( $element['price_rules'][ $key ][0] ) ) {// general field variation rule.
					$_price = $element['price_rules'][ $key ][0];
				} elseif ( $variation_id && isset( $element['price_rules'][0][ $variation_id ] ) ) {// general variation rule.
					$_price = $element['price_rules'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule.
					$_price = $element['price_rules'][0][0];
				}
			}

			if ( ( 'percent' === $_price_type || 'percentcurrenttotal' === $_price_type ) && '' === $_price && isset( $element['price_rules_original'] ) ) {
				if ( ! isset( $element['price_rules_original'][ $key ] ) ) {// field price rule.
					if ( $variation_id && isset( $element['price_rules_original'][0][ $variation_id ] ) ) {// general variation rule.
						$_price = $element['price_rules_original'][0][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][0][0] ) ) {// general rule.
						$_price = $element['price_rules_original'][0][0];
					}
				} else {
					if ( $variation_id && isset( $element['price_rules_original'][ $key ][ $variation_id ] ) ) {// field price rule.
						$_price = $element['price_rules_original'][ $key ][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][ $key ][0] ) ) {// general field variation rule.
						$_price = $element['price_rules_original'][ $key ][0];
					} elseif ( $variation_id && isset( $element['price_rules_original'][0][ $variation_id ] ) ) {// general variation rule.
						$_price = $element['price_rules_original'][0][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][0][0] ) ) {// general rule.
						$_price = $element['price_rules_original'][0][0];
					}
				}
			}

			$currency_price = false;
			if ( $currency && $price_per_currencies && is_array( $price_per_currencies ) && isset( $price_per_currencies[ $currency ] ) ) {
				if ( isset( $price_per_currencies[ $currency ][ $key ] ) ) {
					if ( isset( $price_per_currencies[ $currency ][ $key ][0] ) ) {
						$currency_price = $price_per_currencies[ $currency ][ $key ][0];
						if ( '' !== $currency_price ) {
							$currency = false;
						} else {
							$currency_price = false;
						}
					}
				} elseif ( '' !== $key && isset( $price_per_currencies[ $currency ][0] ) && isset( $price_per_currencies[ $currency ][0][0] ) ) {
					$currency_price = $price_per_currencies[ $currency ][0][0];
					if ( '' !== $currency_price ) {
						$currency = false;
					} else {
						$currency_price = false;
					}
				}
			}

			if ( false !== $currency_price ) {
				$_price = $currency_price;
			}

			if ( 'math' !== $_price_type && '' !== $_price ) {
				$_price = floatval( wc_format_decimal( $_price, false, true ) );
			}
		}

		if ( is_array( $_price ) ) {
			$_price = $price_default_value;
		}

		if ( null !== $price_per_currencies ) {
			$_price = [
				'price'    => $_price,
				'currency' => $currency,
			];
		}

		return $_price;
	}

	/**
	 * Calculates the correct option price
	 *
	 * @param array|null    $post_data The posted data.
	 * @param array         $element The element array.
	 * @param string|null   $key The posted element value.
	 * @param string|null   $attribute The posted element name.
	 * @param integer|false $attribute_quantity The option quantity of this element.
	 * @param integer       $key_id The array key of the posted element values array.
	 * @param integer       $keyvalue_id The array key for the values of the posted element values array.
	 * @param boolean|null  $per_product_pricing If the product has pricing, true or false.
	 * @param mixed         $cpf_product_price The product price.
	 * @param integer|null  $variation_id The variation id.
	 * @param integer|false $price_default_value The value to return if the formula fails.
	 * @param string|false  $currency The currency to set the result to.
	 * @param string|false  $current_currency The current currency.
	 * @param array|null    $price_per_currencies The price per currencies array.
	 * @param array|false   $tmcp Saved element data.
	 * @param array         $tmdata Saved tmdata array.
	 * @since 1.0
	 */
	public function calculate_price( $post_data = null, $element = [], $key = null, $attribute = null, $attribute_quantity = false, $key_id = 0, $keyvalue_id = 0, $per_product_pricing = null, $cpf_product_price = false, $variation_id = null, $price_default_value = false, $currency = false, $current_currency = false, $price_per_currencies = null, $tmcp = false, $tmdata = [] ) {

		$element = apply_filters( 'wc_epo_get_element_for_display', $element );

		if ( is_null( $post_data ) && isset( $_POST ) ) { // phpcs:ignore
			$post_data = wp_unslash( $_POST ); // phpcs:ignore
		}
		if ( empty( $post_data ) && isset( $_REQUEST['tcajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_data = wp_unslash( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$post_data = stripslashes_deep( $post_data );

		if ( false === $attribute_quantity ) {
			if ( isset( $post_data[ $attribute . '_quantity' ] ) ) {
				$attribute_quantity = $post_data[ $attribute . '_quantity' ];
			}
		}
		if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $key_id ] ) ) {
			$attribute_quantity = $attribute_quantity[ $key_id ];
			if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $keyvalue_id ] ) ) {
				$attribute_quantity = $attribute_quantity[ $keyvalue_id ];
			}
		}

		// This should only trigger manually for internal calculation in math price.
		if ( false === $attribute_quantity ) {
			$attribute_quantity = 1;
		}

		$posted_attribute = '';
		if ( isset( $post_data[ $attribute ] ) ) {
			$posted_attribute = $post_data[ $attribute ];
		} elseif ( isset( $_FILES[ $attribute ] ) ) {
			$posted_attribute = wp_unslash( $_FILES[ $attribute ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		if ( is_array( $posted_attribute ) && isset( $posted_attribute[ $key_id ] ) ) {
			$posted_attribute = $posted_attribute[ $key_id ];
			if ( is_array( $posted_attribute ) && isset( $posted_attribute[ $keyvalue_id ] ) ) {
				$posted_attribute = $posted_attribute[ $keyvalue_id ];
			}
		}

		// This currently happens for multiple file uploads and repeaters.
		if ( is_array( $key ) ) {
			if ( 'multiple_file_upload' === $element['type'] ) {
				$key = [ 0 ];
			}
		}

		if ( ! is_array( $key ) ) {
			$key = [ $key ];
		}

		$price = 0;

		foreach ( $key as $thiskey ) {

			$_price = $this->calculate_key_price( $posted_attribute, $post_data, $element, $thiskey, $attribute, $attribute_quantity, $key_id, $keyvalue_id, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $price_per_currencies, $tmcp, $tmdata );
			if ( '' === $price_default_value && '' === $_price ) {
				$price = $_price;
			} else {
				if ( false === $price_default_value || '' === $price_default_value ) {
					$price_default_value = 0;
				}
				$price = floatval( $_price ) + $price;
			}
		}

		return $price;

	}

	/**
	 * Calculates the correct option price for the $key
	 *
	 * @param string        $posted_attribute The posted attribute.
	 * @param array|null    $post_data The posted data.
	 * @param array         $element The element array.
	 * @param string|null   $key The posted element value.
	 * @param string|null   $attribute The posted element name.
	 * @param integer|false $attribute_quantity The option quantity of this element.
	 * @param integer       $key_id The array key of the posted element values array.
	 * @param integer       $keyvalue_id The array key for the values of the posted element values array.
	 * @param boolean|null  $per_product_pricing If the product has pricing, true or false.
	 * @param mixed         $cpf_product_price The product price.
	 * @param integer|null  $variation_id The variation id.
	 * @param mixed         $price_default_value The value to return if the formula fails.
	 * @param string|false  $currency The currency to set the result to.
	 * @param string|false  $current_currency The current currency.
	 * @param array|null    $price_per_currencies The price per currencies array.
	 * @param array|false   $tmcp Saved element data.
	 * @param array         $tmdata Saved tmdata array.
	 * @since 6.0
	 */
	public function calculate_key_price( $posted_attribute = '', $post_data = null, $element = [], $key = null, $attribute = null, $attribute_quantity = false, $key_id = 0, $keyvalue_id = 0, $per_product_pricing = null, $cpf_product_price = false, $variation_id = null, $price_default_value = 0, $currency = false, $current_currency = false, $price_per_currencies = null, $tmcp = false, $tmdata = [] ) {

		if ( false === $price_default_value ) {
			$price_default_value = 0;
		}

		$key = esc_attr( $key );

		$original_currency = $currency;

		$_price_type = $this->get_element_price_type( '', $element, $key, $per_product_pricing, $variation_id );
		$_price      = $this->get_element_price( $price_default_value, $_price_type, $element, $key, $per_product_pricing, $variation_id, $price_per_currencies, $currency );
		if ( is_array( $_price ) ) {
			if ( false === $_price['currency'] ) {
				$currency = false;
			}
			$_price = $_price['price'];
		}

		if ( $per_product_pricing && '' !== $key ) {

			if ( false !== $cpf_product_price ) {
				$cpf_product_price = apply_filters( 'wc_epo_original_price_type_mode', $cpf_product_price, $post_data );
			}
			switch ( $_price_type ) {
				case 'percent_cart_total':
					$_price = ( floatval( $_price ) / 100 ) * floatval( WC()->cart->get_cart_contents_total() );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;

				case 'percent':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_convert_to_currency', $cpf_product_price, $current_currency, $currency );
						}
						$_price = ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price );
					}
					break;
				case 'percentcurrenttotal':
					$_original_price = $_price;
					if ( false !== $cpf_product_price ) {
						if ( '' !== $_price ) {
							if ( isset( $post_data[ $attribute . '_hidden' ] ) ) {
								$_price = floatval( $post_data[ $attribute . '_hidden' ] );
							}
							if ( isset( $post_data['tm_epo_options_static_prices'] ) ) {
								$_price = ( floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $cpf_product_price ) ) * ( floatval( $_original_price ) / 100 );
								if ( $attribute_quantity > 0 ) {
									$_price = $_price * floatval( $attribute_quantity );
								}
							}

							if ( $attribute_quantity > 0 ) {
								$_price = $_price / floatval( $attribute_quantity );
							}
						}
					}
					break;
				case 'fixedcurrenttotal':
					$_original_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, '', $current_currency, $price_per_currencies, $key, $attribute );

					if ( '' !== $_price && isset( $post_data[ $attribute . '_hiddenfixed' ] ) ) {
						$_price = floatval( $post_data[ $attribute . '_hiddenfixed' ] );

						if ( isset( $post_data['tm_epo_options_static_prices'] ) ) {
							$_price = ( floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $_original_price ) );
							if ( $attribute_quantity > 0 ) {
								$_price = $_price * floatval( $attribute_quantity );
							}
						}

						if ( $attribute_quantity > 0 ) {
							$_price = $_price / floatval( $attribute_quantity );
						}
					}
					break;
				case 'word':
					$_price = floatval( floatval( $_price ) * floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'wordpercent':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_price = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) * ( floatval( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'wordnon':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'wordpercentnon':
					if ( false !== $cpf_product_price ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;

				case 'char':
					$_price = floatval( floatval( $_price ) * floatval( strlen( stripcslashes( utf8_decode( $posted_attribute ) ) ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercent':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_price = floatval( strlen( stripcslashes( utf8_decode( $posted_attribute ) ) ) ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'charnofirst':
					$_textlength = floatval( strlen( stripcslashes( utf8_decode( $posted_attribute ) ) ) ) - 1;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;

				case 'charnon':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( strlen( stripcslashes( utf8_decode( $posted_attribute ) ) ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnon':
					if ( false !== $cpf_product_price ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( stripcslashes( utf8_decode( $posted_attribute ) ) ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'charnonnospaces':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( strlen( preg_replace( '/\s+/', '', stripcslashes( utf8_decode( $posted_attribute ) ) ) ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnonnospaces':
					if ( false !== $cpf_product_price ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( preg_replace( '/\s+/', '', stripcslashes( utf8_decode( $posted_attribute ) ) ) ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;

				case 'charnospaces':
					$_price = floatval( floatval( $_price ) * strlen( preg_replace( '/\s+/', '', stripcslashes( utf8_decode( $posted_attribute ) ) ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnofirst':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( stripcslashes( utf8_decode( $posted_attribute ) ) ) ) - 1;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'step':
					$_price = floatval( floatval( $_price ) * floatval( stripcslashes( $posted_attribute ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'currentstep':
					$_price = floatval( stripcslashes( $posted_attribute ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'intervalstep':
					if ( isset( $element['min'] ) ) {
						$_min   = floatval( $element['min'] );
						$_price = floatval( floatval( $_price ) * ( floatval( stripcslashes( $posted_attribute ) ) - $_min ) );
						if ( $currency ) {
							$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
					}
					break;
				case 'row':
					$_price = floatval( floatval( $_price ) * ( substr_count( stripcslashes( utf8_decode( $posted_attribute ) ), "\r\n" ) + 1 ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'math':
					$_price = $this->calculate_math_price( $_price, $post_data, $element, $key, $attribute, $attribute_quantity, $key_id, $keyvalue_id, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $original_currency, $current_currency, $price_per_currencies, $tmdata );
					break;
				default:
					// fixed price.
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
			}

			$_price = floatval( $_price ) * floatval( $attribute_quantity );

			if ( '' === $price_default_value && (float) 0 === $_price ) {
				$_price = '';
			}
		} else {
			$_price = $price_default_value;
		}

		return $_price;

	}


	/**
	 * Conditional logic (checks if an element is visible)
	 *
	 * @param array  $element The element array.
	 * @param array  $section The section array.
	 * @param array  $sections The sections array.
	 * @param string $form_prefix The form prefix.
	 * @since 1.0
	 */
	public function is_visible( $element = [], $section = [], $sections = [], $form_prefix = '' ) {

		$id                                    = uniqid();
		$this->current_element_to_check[ $id ] = [];

		return $this->is_visible_do( $id, $element, $section, $sections, $form_prefix );

	}

	/**
	 * Get all lookup tables
	 *
	 * @since 6.1
	 */
	public function fetch_all_lookuptables() {
		$meta_array = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'lookuptable_meta', '', '!=', 'NOT EXISTS' );

		$meta_query_args = [
			'relation' => 'AND',
			$meta_array,
			[
				[
					'key'     => 'lookuptable_meta',
					'compare' => 'EXISTS',
				],
			],
		];

		$args = [
			'post_type'        => THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE,
			'post_status'      => [ 'publish' ],
			'numberposts'      => -1,
			'orderby'          => 'ID',
			'order'            => 'asc',
			'meta_query'       => $meta_query_args, // phpcs:ignore WordPress.DB.SlowDBQuery
			'suppress_filters' => false,
		];

		$lookuptables = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

		return $lookuptables;
	}

	/**
	 * Generate all lookup tables
	 * and save them in $this->lookup_tables
	 *
	 * @since 6.1
	 */
	public function generate_lookuptables() {

		$lookuptables = $this->fetch_all_lookuptables();
		if ( $lookuptables ) {
			foreach ( $lookuptables as $table ) {
				$meta = themecomplete_get_post_meta( $table->ID, 'lookuptable_meta', true );

				if ( ! is_array( $meta ) ) {
					$meta = [];
				}

				foreach ( $meta as $table_name => $table_data ) {
					$index = 0;
					if ( isset( $this->lookup_tables[ $table_name ] ) ) {
						$index = count( $this->lookup_tables[ $table_name ] );
					}
					$this->lookup_tables[ $table_name ][ $index ]['data'] = $table_data;
				}
			}
		}
	}
	/**
	 * Conditional logic (checks if an element is visible)
	 *
	 * @param string $id The index of the current element to check in the $this->current_element_to_check array.
	 * @param array  $element The element array.
	 * @param array  $section The section array.
	 * @param array  $sections The sections array.
	 * @param string $form_prefix The form prefix.
	 * @since 1.0
	 */
	private function is_visible_do( $id = '0', $element = [], $section = [], $sections = [], $form_prefix = '' ) {

		$is_element = false;
		$is_section = false;

		$array_prefix = $form_prefix;
		if ( '' === $form_prefix ) {
			$array_prefix = '_';
		}

		$uniqid = isset( $element['uniqid'] ) ? $element['uniqid'] : false;

		if ( ! $uniqid ) {
			$uniqid     = isset( $element['sections_uniqid'] ) ? $element['sections_uniqid'] : false;
			$is_section = true;
		} else {
			$is_element = true;
		}

		if ( ! $uniqid ) {
			return false;
		}
		if ( isset( $this->visible_elements[ $array_prefix ][ $uniqid ] ) ) {
			return $this->visible_elements[ $array_prefix ][ $uniqid ];
		}

		$logic = false;

		if ( $is_element ) {

			// Element.
			if ( ! $this->is_visible_do( $id, $section, [], $sections, $form_prefix ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = false;

				return false;
			}
			if ( ! isset( $element['logic'] ) || empty( $element['logic'] ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = true;

				return true;
			}
			$logic = (array) json_decode( $element['clogic'] );
		} elseif ( $is_section ) {
			// Section.
			if ( ! isset( $element['sections_logic'] ) || empty( $element['sections_logic'] ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = true;

				return true;
			}
			$logic = (array) json_decode( $element['sections_clogic'] );
		} else {
			$this->visible_elements[ $array_prefix ][ $uniqid ] = true;

			return true;
		}

		if ( $logic ) {

			$rule_toggle = $logic['toggle'];
			$rule_what   = $logic['what'];
			$matches     = 0;
			$checked     = 0;
			$show        = true;

			switch ( $rule_toggle ) {
				case 'show':
					$show = false;
					break;
				case 'hide':
					$show = true;
					break;
			}

			if ( ! isset( $this->current_element_to_check[ $id ] ) ) {
				$this->current_element_to_check[ $id ] = [];
			}

			if ( in_array( $uniqid, $this->current_element_to_check[ $id ], true ) ) {
				return true;
			}

			$this->current_element_to_check[ $id ][] = $uniqid;

			foreach ( $logic['rules'] as $key => $rule ) {
				$matches ++;

				if ( $this->tm_check_field_match( $id, $rule, $sections, $form_prefix ) ) {
					$checked ++;
				}
			}

			$this->current_element_to_check[ $id ] = [];

			if ( 'all' === $rule_what ) {
				if ( $checked > 0 && $checked === $matches ) {
					$show = ! $show;
				}
			} else {
				if ( $checked > 0 ) {
					$show = ! $show;
				}
			}
			$this->visible_elements[ $array_prefix ][ $uniqid ] = $show;

			return $show;

		}

		$this->visible_elements[ $array_prefix ][ $uniqid ] = false;

		return false;
	}

	/**
	 * Conditional logic (checks element conditions)
	 *
	 * @param string       $id The index of the current element to check in the $this->current_element_to_check array.
	 * @param object|false $rule The rule object.
	 * @param array|false  $sections The sections array.
	 * @param string       $form_prefix The form prefix.
	 * @since 1.0
	 */
	public function tm_check_field_match( $id = '0', $rule = false, $sections = false, $form_prefix = '' ) {

		if ( empty( $rule ) || empty( $sections ) ) {
			return false;
		}

		$array_prefix = $form_prefix;
		if ( '' === $form_prefix ) {
			$array_prefix = '_';
		}

		$section_id = $rule->section;
		$element_id = $rule->element;
		$operator   = $rule->operator;
		$value      = isset( $rule->value ) ? $rule->value : null;

		if ( (string) $section_id === (string) $element_id ) {
			return $this->tm_check_section_match( $element_id, $operator, $rule, $sections, $form_prefix );
		}
		if ( ! isset( $sections[ $section_id ] )
			|| ! isset( $sections[ $section_id ]['elements'] )
			|| ! isset( $sections[ $section_id ]['elements'][ $element_id ] )
			|| ! isset( $sections[ $section_id ]['elements'][ $element_id ]['type'] )
		) {
			return false;
		}

		// variations logic.
		if ( 'variations' === $sections[ $section_id ]['elements'][ $element_id ]['type'] ) {
			return $this->tm_variation_check_match( $form_prefix, $value, $operator );
		}

		if ( ! isset( $sections[ $section_id ]['elements'][ $element_id ]['name_inc'] ) ) {
			return false;
		}

		$element_array    = $sections[ $section_id ]['elements'][ $element_id ];
		$element_uniqueid = $element_array['uniqid'];

		if ( isset( $this->visible_elements[ $array_prefix ][ $element_uniqueid ] ) ) {
			if ( ! $this->visible_elements[ $array_prefix ][ $element_uniqueid ] ) {
				return false;
			}
		} else {
			if ( in_array( $element_uniqueid, $this->current_element_to_check[ $id ], true ) ) { // phpcs:ignore
				// Getting here means that two elements depend on each other
				// This is a logical error when creating the conditional logic in the builder.
			} elseif ( ! $this->is_visible_do( $id, $element_array, $sections[ $section_id ], $sections, $form_prefix ) ) {
				return false;
			}
		}

		$element_to_check = $element_array['name_inc'];

		$element_type = $element_array['type'];
		$posted_value = null;

		if ( 'product' === $element_type ) {
			$element_type = 'select';
		}

		switch ( $element_type ) {
			case 'radio':
				$radio_checked_length = 0;
				$element_to_check     = array_unique( $element_to_check );

				// Element array contains the form_prefix so we don't append it again.
				$element_to_check = $element_to_check[0];

				if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$posted_value = stripslashes_deep( $posted_value );
					$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
					if ( ! empty( $element_array['connector'] ) ) {
						if ( in_array( $posted_value, $element_array['options'], true ) ) {
							$radio_checked_length ++;
						}
					} else {
						$radio_checked_length ++;
					}
					$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );
				}
				if ( 'is' === $operator || 'isnot' === $operator ) {
					if ( 0 === (int) $radio_checked_length ) {
						return false;
					}
				} elseif ( 'isnotempty' === $operator ) {
					return $radio_checked_length > 0;
				} elseif ( 'isempty' === $operator ) {
					return 0 === (int) $radio_checked_length;
				}
				break;
			case 'checkbox':
				$checkbox_checked_length = 0;
				$ret                     = false;
				$element_to_check        = array_unique( $element_to_check );
				foreach ( $element_to_check as $key => $name_value ) {
					// Element array contains the form_prefix so we don't append it again.
					$element_to_check[ $key ] = $name_value;
					$posted_value             = null;
					if ( isset( $_REQUEST[ $element_to_check[ $key ] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$checkbox_checked_length ++;
						$posted_value = wp_unslash( $_REQUEST[ $element_to_check[ $key ] ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$posted_value = stripslashes_deep( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );

						if ( $this->tm_check_match( $posted_value, $value, $operator ) ) {
							$ret = true;
						} else {
							if ( 'isnot' === $operator ) {
								$ret = false;
								break;
							}
						}
					}
				}
				if ( 'is' === $operator || 'isnot' === $operator ) {
					if ( 0 === (int) $checkbox_checked_length ) {
						return false;
					}

					return $ret;
				} elseif ( 'isnotempty' === $operator ) {
					return $checkbox_checked_length > 0;
				} elseif ( 'isempty' === $operator ) {
					return 0 === (int) $checkbox_checked_length;
				}
				break;
			case 'select':
			case 'textarea':
			case 'textfield':
			case 'color':
			case 'range':
				// Element array contains the form_prefix so we don't append it again.
				if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$posted_value = stripslashes_deep( $posted_value );
					if ( 'select' === $element_type ) {
						$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );
					}
				}
				break;
		}

		return $this->tm_check_match( $posted_value, $value, $operator );

	}

	/**
	 * Conditional logic (checks section conditions)
	 *
	 * @param string       $element_id The element id.
	 * @param string       $operator The logic operator.
	 * @param object|false $rule The rule object.
	 * @param array|false  $sections The sections array.
	 * @param string       $form_prefix The form prefix.
	 * @since 1.0
	 */
	public function tm_check_section_match( $element_id, $operator, $rule = false, $sections = false, $form_prefix = '' ) {

		$array_prefix = $form_prefix;
		if ( '' === $form_prefix ) {
			$array_prefix = '_';
		}

		if ( isset( $this->visible_elements ) && isset( $this->visible_elements[ $array_prefix ] ) && isset( $this->visible_elements[ $array_prefix ][ $element_id ] ) ) {

			if ( false === $this->visible_elements[ $array_prefix ][ $element_id ] ) {
				if ( 'isnotempty' === $operator ) {
					return false;
				} elseif ( 'isempty' === $operator ) {
					return true;
				}
			}
		}

		$all_checked = true;
		$section_id  = $element_id;
		if ( isset( $sections[ $section_id ] ) && isset( $sections[ $section_id ]['elements'] ) ) {
			foreach ( $sections[ $section_id ]['elements'] as $id => $element ) {
				if ( $this->is_visible_do( $id, $element, $sections[ $section_id ], $sections, $form_prefix ) ) {
					if ( ! isset( $sections[ $section_id ]['elements'][ $id ]['name_inc'] ) ) {
						continue;
					}
					$element_to_check = $sections[ $section_id ]['elements'][ $id ]['name_inc'];
					$element_type     = $sections[ $section_id ]['elements'][ $id ]['type'];
					$posted_value     = null;
					if ( 'product' === $element_type ) {
						$element_type = 'select';
					}
					switch ( $element_type ) {
						case 'radio':
							$radio_checked_length = 0;
							$element_to_check     = array_unique( $element_to_check );

							// Element array contains the form_prefix so we don't append it again.
							$element_to_check = $element_to_check[0];

							if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$element_array = $sections[ $section_id ]['elements'][ $element_id ];

								$_posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								$_posted_value = stripslashes_deep( $posted_value );
								$_posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
								if ( ! empty( $element_array['connector'] ) ) {
									if ( in_array( $posted_value, $element_array['options'], true ) ) {
										$radio_checked_length ++;
									}
								} else {
									$radio_checked_length ++;
								}
							}
							if ( 'isnotempty' === $operator ) {
								$all_checked = $all_checked && $radio_checked_length > 0;
								if ( $radio_checked_length > 0 ) {
									$posted_value = $radio_checked_length;
								}
							} elseif ( 'isempty' === $operator ) {
								$all_checked = $all_checked && 0 === (int) $radio_checked_length;
							}
							break;
						case 'checkbox':
							$checkbox_checked_length = 0;

							$element_to_check = array_unique( $element_to_check );
							foreach ( $element_to_check as $key => $name_value ) {
								$element_to_check[ $key ] = $name_value . $form_prefix;
								if ( isset( $_REQUEST[ $element_to_check[ $key ] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									$checkbox_checked_length ++;
								}
							}
							if ( 'isnotempty' === $operator ) {
								$all_checked = $all_checked && $checkbox_checked_length > 0;
								if ( $checkbox_checked_length > 0 ) {
									$posted_value = $checkbox_checked_length;
								}
							} elseif ( 'isempty' === $operator ) {
								$all_checked = $all_checked && 0 === (int) $checkbox_checked_length;
							}
							break;

						case 'selectmultiple':
							$element_to_check .= $form_prefix;
							if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								$posted_value = stripslashes_deep( $posted_value );
								$val          = [];
								if ( is_array( $posted_value ) ) {
									foreach ( $posted_value as $copy ) {
										foreach ( $copy as $i => $option ) {
											$option    = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $option );
											$option    = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $option, '_' );
											$val[ $i ] = $option;
										}
									}
								}
								$posted_value = $val;
							}
							break;
						default:
							$element_to_check .= $form_prefix;
							if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								$posted_value = stripslashes_deep( $posted_value );
								if ( 'select' === $element_type ) {
									$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
									$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );
								}
							}
							break;
					}
					if ( is_array( $posted_value ) ) {
						$all_checked = $all_checked && THEMECOMPLETE_EPO_HELPER()->array_some(
							$posted_value,
							function( $item ) use ( $operator ) {
								return $this->tm_check_match( $item, '', $operator );
							}
						);
					} else {
						$all_checked = $all_checked && $this->tm_check_match( $posted_value, '', $operator );
					}
				}
			}
		}

		return $all_checked;

	}

	/**
	 * Conditional logic (checks variation conditions)
	 *
	 * @param string $form_prefix The form prefix.
	 * @param string $value The value to check against.
	 * @param string $operator The logic operator.
	 * @since 1.0
	 */
	public function tm_variation_check_match( $form_prefix, $value, $operator ) {

		$posted_value = $this->get_posted_variation_id( $form_prefix );

		return $this->tm_check_match( $posted_value, $value, $operator, true );

	}

	/**
	 * Conditional logic (checks conditions)
	 *
	 * @param string  $posted_value The posted value.
	 * @param string  $value The value to check against.
	 * @param string  $operator The logic operator.
	 * @param boolean $include_zero If zero value counts as empty.
	 * @since 1.0
	 */
	public function tm_check_match( $posted_value, $value, $operator, $include_zero = false ) {

		$posted_value = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $posted_value ) ) );
		$value        = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $value ) ) );
		switch ( $operator ) {
			case 'is':
				return ( null !== $posted_value && $value === $posted_value );
			case 'isnot':
				return ( null !== $posted_value && $value !== $posted_value );
			case 'isempty':
				if ( $include_zero ) {
					return ( ! ( ( null !== $posted_value && '' !== $posted_value && '0' !== $posted_value && 0 !== $posted_value ) ) );
				}
				return ( ! ( ( null !== $posted_value && '' !== $posted_value ) ) );
			case 'isnotempty':
				if ( $include_zero ) {
					return ( ( null !== $posted_value && '' !== $posted_value && '0' !== $posted_value && 0 !== $posted_value ) );
				}

				return ( ( null !== $posted_value && '' !== $posted_value ) );
			case 'startswith':
				return THEMECOMPLETE_EPO_HELPER()->str_startswith( $posted_value, $value );
			case 'endswith':
				return THEMECOMPLETE_EPO_HELPER()->str_endsswith( $posted_value, $value );
			case 'greaterthan':
				return floatval( $posted_value ) > floatval( $value );
			case 'lessthan':
				return floatval( $posted_value ) < floatval( $value );
			case 'greaterthanequal':
				return floatval( $posted_value ) >= floatval( $value );
			case 'lessthanequal':
				return floatval( $posted_value ) <= floatval( $value );
		}

		return false;

	}

	/**
	 * Upload file
	 *
	 * @param array   $file The file array.
	 * @param integer $key_id The array key of the posted element values array.
	 * @param integer $keyvalue_id The array key for the values of the posted element values array.
	 *
	 * @return array|mixed
	 */
	public function upload_file( $file, $key_id = 0, $keyvalue_id = 0 ) {

		$tmp_name = $file['tmp_name'];
		if ( is_array( $tmp_name ) && isset( $tmp_name[ $key_id ] ) ) {
			$tmp_name = $tmp_name[ $key_id ];
			if ( is_array( $tmp_name ) && isset( $tmp_name[ $keyvalue_id ] ) ) {
				$tmp_name = $tmp_name[ $keyvalue_id ];
			}
		}

		if ( is_array( $file ) ) {
			foreach ( $file as $key => $value ) {
				if ( is_array( $value ) && isset( $value[ $key_id ] ) ) {
					$value = $value[ $key_id ];
					if ( is_array( $value ) && isset( $value[ $keyvalue_id ] ) ) {
						$value = $value[ $keyvalue_id ];
					}
				}
				$file[ $key ] = $value;
			}
		}
		$tmp_name = $file['tmp_name'];

		if ( '' === $tmp_name ) {
			return false;
		}

		if ( is_array( $file ) && ! empty( $tmp_name ) && isset( $this->upload_object[ $tmp_name ] ) ) {
			$this->upload_object[ $tmp_name ]['tc'] = true;

			return $this->upload_object[ $tmp_name ];
		}
		if ( ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) ) {
			define( 'ALLOW_UNFILTERED_UPLOADS', true );
		}
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/media.php';
		add_filter( 'upload_dir', [ $this, 'upload_dir_trick' ] );
		add_filter( 'upload_mimes', [ $this, 'upload_mimes_trick' ] );
		$upload = wp_handle_upload(
			$file,
			[
				'test_form' => false,
				'test_type' => false,
			]
		);
		remove_filter( 'upload_dir', [ $this, 'upload_dir_trick' ] );
		remove_filter( 'upload_mimes', [ $this, 'upload_mimes_trick' ] );

		if ( is_array( $file ) && ! empty( $tmp_name ) ) {
			$this->upload_object[ $tmp_name ] = $upload;
		}

		return $upload;

	}

	/**
	 * Alter allowed file mime and type
	 *
	 * @return mixed|void
	 */
	public function get_allowed_mimes() {

		$mimes = [];

		$tm_epo_custom_file_types  = $this->tm_epo_custom_file_types;
		$tm_epo_allowed_file_types = $this->tm_epo_allowed_file_types;

		$tm_epo_custom_file_types = explode( ',', $tm_epo_custom_file_types );
		if ( ! is_array( $tm_epo_custom_file_types ) ) {
			$tm_epo_custom_file_types = [];
		}
		if ( ! is_array( $tm_epo_allowed_file_types ) ) {
			$tm_epo_allowed_file_types = [ '@' ];
		}
		$tm_epo_allowed_file_types = array_merge( $tm_epo_allowed_file_types, $tm_epo_custom_file_types );
		$tm_epo_allowed_file_types = array_unique( $tm_epo_allowed_file_types );

		$wp_get_ext_types  = wp_get_ext_types();
		$wp_get_mime_types = wp_get_mime_types();

		foreach ( $tm_epo_allowed_file_types as $key => $value ) {
			if ( ! $value ) {
				continue;
			}
			if ( '@' === $value ) {
				$mimes = $wp_get_mime_types;
			} else {
				$value = ltrim( $value, '@' );
				switch ( $value ) {
					case 'image':
					case 'audio':
					case 'video':
					case 'document':
					case 'spreadsheet':
					case 'interactive':
					case 'text':
					case 'archive':
					case 'code':
						if ( isset( $wp_get_ext_types[ $value ] ) && is_array( $wp_get_ext_types[ $value ] ) ) {
							foreach ( $wp_get_ext_types[ $value ] as $k => $extension ) {
								$type = false;
								foreach ( $wp_get_mime_types as $exts => $_mime ) {
									if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
										$type = $_mime;
										break;
									}
								}
								if ( $type ) {
									$mimes[ $extension ] = $type;
								}
							}
						}
						break;

					default:
						$type = false;
						foreach ( $wp_get_mime_types as $exts => $_mime ) {
							if ( preg_match( '!^(' . $exts . ')$!i', $value ) ) {
								$type = $_mime;
								break;
							}
						}
						if ( $type ) {
							$mimes[ $value ] = $type;
						} else {
							$mimes[ $value ] = 'application/octet-stream';
						}
						break;
				}
			}
		}

		$allowed_mimes = [];
		foreach ( $mimes as $key => $value ) {
			$value = explode( '|', $key );

			foreach ( $value as $k => $v ) {
				$v               = str_replace( '.', '', trim( $v ) );
				$v               = '.' . $v;
				$allowed_mimes[] = $v;
			}
		}

		return apply_filters( 'wc_epo_get_allowed_mimes', $allowed_mimes );

	}

	/**
	 * Alter allowed file mime and type
	 *
	 * @param array $existing_mimes The existing mimes array.
	 *
	 * @return mixed|void
	 */
	public function upload_mimes_trick( $existing_mimes = [] ) {

		$mimes = [];

		$tm_epo_custom_file_types  = $this->tm_epo_custom_file_types;
		$tm_epo_allowed_file_types = $this->tm_epo_allowed_file_types;

		$tm_epo_custom_file_types = explode( ',', $tm_epo_custom_file_types );
		if ( ! is_array( $tm_epo_custom_file_types ) ) {
			$tm_epo_custom_file_types = [];
		}
		if ( ! is_array( $tm_epo_allowed_file_types ) ) {
			$tm_epo_allowed_file_types = [ '@' ];
		}
		$tm_epo_allowed_file_types = array_merge( $tm_epo_allowed_file_types, $tm_epo_custom_file_types );
		$tm_epo_allowed_file_types = array_unique( $tm_epo_allowed_file_types );

		$wp_get_ext_types  = wp_get_ext_types();
		$wp_get_mime_types = wp_get_mime_types();

		foreach ( $tm_epo_allowed_file_types as $key => $value ) {
			if ( ! $value ) {
				continue;
			}
			if ( '@' === $value ) {
				$mimes = $existing_mimes;
			} else {
				$value = ltrim( $value, '@' );
				switch ( $value ) {
					case 'image':
					case 'audio':
					case 'video':
					case 'document':
					case 'spreadsheet':
					case 'interactive':
					case 'text':
					case 'archive':
					case 'code':
						if ( isset( $wp_get_ext_types[ $value ] ) && is_array( $wp_get_ext_types[ $value ] ) ) {
							foreach ( $wp_get_ext_types[ $value ] as $k => $extension ) {
								$type = false;
								foreach ( $wp_get_mime_types as $exts => $_mime ) {
									if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
										$type = $_mime;
										break;
									}
								}
								if ( $type ) {
									$mimes[ $extension ] = $type;
								}
							}
						}
						break;

					default:
						$type = false;
						foreach ( $wp_get_mime_types as $exts => $_mime ) {
							if ( preg_match( '!^(' . $exts . ')$!i', $value ) ) {
								$type = $_mime;
								break;
							}
						}
						if ( $type ) {
							$mimes[ $value ] = $type;
						} else {
							$mimes[ $value ] = 'application/octet-stream';
						}
						break;
				}
			}
		}

		return apply_filters( 'wc_epo_upload_mimes', $mimes );

	}

	/**
	 * Alter upload directory
	 *
	 * @param array $param Array of information about the uplaod directory.
	 *
	 * @return mixed
	 */
	public function upload_dir_trick( $param ) {

		global $woocommerce;
		$unique_dir = apply_filters( 'wc_epo_upload_unique_dir', md5( $woocommerce->session->get_customer_id() ) );
		$subdir     = $this->upload_dir . $unique_dir;
		if ( empty( $param['subdir'] ) ) {
			$param['path']   = $param['path'] . $subdir;
			$param['url']    = $param['url'] . $subdir;
			$param['subdir'] = $subdir;
		} else {
			$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
			$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
			$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
		}

		return $param;

	}

	/**
	 * Apply custom filter
	 *
	 * @param string $value The value to apply the filter.
	 * @param string $filter The filter to apply.
	 * @param string $element The Element name.
	 * @param string $element_uniqueid The Element unique ID.
	 *
	 * @return mixed|string|void
	 */
	private function tm_apply_filter( $value = '', $filter = '', $element = '', $element_uniqueid = '' ) {

		// Normalize posted strings.
		$value = THEMECOMPLETE_EPO_HELPER()->normalize_data( $value );

		if ( ! empty( $filter ) ) {
			$value = apply_filters( $filter, $value, $element, $element_uniqueid );
		}

		return apply_filters( 'wc_epo_setting', apply_filters( 'tm_translate', $value ), $element, $element_uniqueid );

	}

	/**
	 * Get builder element
	 *
	 * @param string        $element The Element name.
	 * @param array         $builder The builder array.
	 * @param array         $current_builder The current builder array.
	 * @param integer|false $index The element index in the builder array.
	 * @param mixed         $alt Alternative value.
	 * @param string        $identifier Identifier 'sections' or the current element.
	 * @param string        $apply_filters Filter name to apply to the returned value.
	 * @param string        $element_uniqueid The Element unique ID.
	 *
	 * @return mixed|string|void
	 */
	public function get_builder_element( $element, $builder, $current_builder, $index = false, $alt = '', $identifier = 'sections', $apply_filters = '', $element_uniqueid = '' ) {

		$original_index = $index;

		list( $use_original_builder, $index ) = apply_filters( 'wc_epo_use_original_builder', [ true, $index ], $element, $builder, $current_builder, $identifier );

		if ( isset( $builder[ $element ] ) ) {
			if ( ! $use_original_builder ) {
				if ( false !== $index ) {
					if ( isset( $current_builder[ $element ][ $index ] ) ) {
						if ( is_object( $current_builder[ $element ][ $index ] ) ) {
							$current_builder[ $element ][ $index ] = wp_json_encode( $current_builder[ $element ][ $index ] );
						}
						if ( is_object( $builder[ $element ][ $original_index ] ) ) {
							$builder[ $element ][ $original_index ] = wp_json_encode( $builder[ $element ][ $original_index ] );
						}
						return $this->tm_apply_filter( THEMECOMPLETE_EPO_HELPER()->build_array( $current_builder[ $element ][ $index ], $builder[ $element ][ $original_index ] ), $apply_filters, $element, $element_uniqueid );
					} else {
						return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
					}
				} else {
					if ( is_object( $current_builder[ $element ] ) ) {
						$current_builder[ $element ] = wp_json_encode( $current_builder[ $element ] );
					}
					if ( is_object( $builder[ $element ][ $original_index ] ) ) {
						$builder[ $element ] = wp_json_encode( $builder[ $element ] );
					}
					return $this->tm_apply_filter( THEMECOMPLETE_EPO_HELPER()->build_array( $current_builder[ $element ], $builder[ $element ] ), $apply_filters, $element, $element_uniqueid );
				}
			}
			if ( false !== $index ) {
				if ( isset( $builder[ $element ][ $index ] ) ) {
					if ( is_object( $builder[ $element ][ $index ] ) ) {
						$builder[ $element ][ $index ] = wp_json_encode( $builder[ $element ][ $index ] );
					}
					return $this->tm_apply_filter( $builder[ $element ][ $index ], $apply_filters, $element, $element_uniqueid );
				} else {
					return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
				}
			} else {
				if ( is_object( $builder[ $element ][ $original_index ] ) ) {
					$builder[ $element ] = wp_json_encode( $builder[ $element ] );
				}
				return $this->tm_apply_filter( $builder[ $element ], $apply_filters, $element, $element_uniqueid );
			}
		} else {
			return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
		}

	}

	/**
	 * Gets a list of all the Extra Product Options (normal and global)
	 * for the specific $post_id.
	 *
	 * @param integer $post_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $no_cache If we should use cached results.
	 * @param boolean $no_disabled If disabled elements should be skipped.
	 * @since 1.0
	 */
	public function get_product_tm_epos( $post_id = 0, $form_prefix = '', $no_cache = false, $no_disabled = false ) {

		if ( empty( $post_id ) || apply_filters( 'wc_epo_disable', false, $post_id ) || ! $this->check_enable() ) {
			return [];
		}

		$post_type = get_post_type( $post_id );

		// Support for variable products in product element.
		if ( ! in_array( $post_type, [ 'product', 'product_variation' ], true ) ) {
			return [];
		}

		$product      = wc_get_product( $post_id );
		$product_type = themecomplete_get_product_type( $product );

		// Yith gift cards are not supported.
		if ( 'gift-card' === $product_type ) {
			return [];
		}

		// disable cache for associated products
		// as they may have discounts which will not
		// show up on the product page if the product
		// is already in the cart.
		if ( ! $this->is_inline_epo && isset( $this->cpf[ $post_id ][ "{$no_disabled}" ][ "f{$form_prefix}" ] ) ) {
			return $this->cpf[ $post_id ][ "{$no_disabled}" ][ "f{$form_prefix}" ];
		}

		if ( 'yes' === $this->tm_epo_global_enable_validation ) {
			$this->current_option_features[] = 'validation';
		}

		if ( 'no' === $this->tm_epo_no_lazy_load ) {
			$this->current_option_features[] = 'lazyload';
		}

		$this->set_tm_meta( $post_id );

		$in_cat = [];
		$in_tax = [];

		$tmglobalprices                   = [];
		$variations_for_conditional_logic = [];

		$terms = get_the_terms( $post_id, 'product_cat' );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$in_cat[] = $term->term_id;
			}
		}

		$custom_product_taxonomies = get_object_taxonomies( 'product' );
		if ( is_array( $custom_product_taxonomies ) && count( $custom_product_taxonomies ) > 0 ) {
			foreach ( $custom_product_taxonomies as $tax ) {
				if ( 'product_cat' === $tax || 'translation_priority' === $tax ) {
					continue;
				}
				$terms = get_the_terms( $post_id, $tax );
				if ( $terms ) {
					$in_tax[ $tax ] = [];
					foreach ( $terms as $term ) {
						$in_tax[ $tax ][] = $term->slug;
					}
				}
			}
		}

		// Get Normal (Local) options.
		$args = [
			'post_type'        => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			'post_status'      => [ 'publish' ], // get only enabled extra options.
			'numberposts'      => -1,
			'orderby'          => 'menu_order',
			'order'            => 'asc',
			'suppress_filters' => true,
			'post_parent'      => floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ),
		];
		THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
		$tmlocalprices = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
		THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

		$tm_meta_cpf_global_forms = ( isset( $this->tm_meta_cpf['global_forms'] ) && is_array( $this->tm_meta_cpf['global_forms'] ) ) ? $this->tm_meta_cpf['global_forms'] : [];
		foreach ( $tm_meta_cpf_global_forms as $key => $value ) {
			$tm_meta_cpf_global_forms[ $key ] = absint( $value );
		}
		$tm_meta_cpf_global_forms_added = [];

		$post_original_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) );

		if ( ! $this->tm_meta_cpf['exclude'] ) {

			/**
			 * Procedure to get global forms
			 * that apply to all products or
			 * specific product categories.
			 */
			$meta_array  = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'tm_meta_disable_categories', 1, '!=', 'NOT EXISTS' );
			$meta_array2 = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'tm_meta_product_exclude_ids', '"' . $post_id . '";', 'NOT LIKE', 'NOT EXISTS' );

			$meta_query_args = [
				'relation' => 'AND', // Optional, defaults to "AND".
				$meta_array,
				$meta_array2,
			];

			$args = [
				'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
				'post_status' => [ 'publish' ], // get only enabled global extra options.
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'asc',
				'meta_query'  => $meta_query_args, // phpcs:ignore WordPress.DB.SlowDBQuery
			];

			// phpcs:ignore WordPress.DB.SlowDBQuery
			$args['tax_query'] = [
				'relation' => 'OR',
				// Get Global options that belong to the product categories.
				[
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $in_cat,
					'operator'         => 'IN',
					'include_children' => false,
				],
				// Get Global options that have no catergory set (they apply to all products).
				[
					'taxonomy' => 'product_cat',
					'operator' => 'NOT EXISTS',
				],
			];

			THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
			THEMECOMPLETE_EPO_WPML()->remove_term_filters();
			$tmp_tmglobalprices = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

			foreach ( $in_tax as $tax => $tax_temrs ) {

				$args_tax               = $args;
				$args_tax['meta_query'] = $meta_array2; // phpcs:ignore WordPress.DB.SlowDBQuery
				// phpcs:ignore WordPress.DB.SlowDBQuery
				$args_tax['tax_query']  = [
					// Get Global options that belong to the product tag.
					[
						'taxonomy'         => $tax,
						'field'            => 'slug',
						'terms'            => $tax_temrs,
						'operator'         => 'IN',
						'include_children' => false,
					],
				];
				$tmp_tmglobalprices_tax = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args_tax );
				$tmp_tmglobalprices     = array_merge( $tmp_tmglobalprices, $tmp_tmglobalprices_tax );
				$tmp_tmglobalprices     = array_unique( $tmp_tmglobalprices, SORT_REGULAR );

			}

			THEMECOMPLETE_EPO_WPML()->restore_term_filters();
			THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

			if ( $tmp_tmglobalprices ) {
				$wpml_tmp_tmglobalprices       = [];
				$wpml_tmp_tmglobalprices_added = [];
				foreach ( $tmp_tmglobalprices as $price ) {

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$price_meta_lang                 = themecomplete_get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
						$original_product_id             = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						$double_check_disable_categories = themecomplete_get_post_meta( $original_product_id, 'tm_meta_disable_categories', true );
						THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
						THEMECOMPLETE_EPO_WPML()->remove_term_filters();

						$double_check_terms = false;
						foreach ( $in_tax as $tax => $tax_temrs ) {
							$double_check_terms = get_terms(
								[
									'taxonomy'   => $tax,
									'object_ids' => $price->ID,
								]
							);

							if ( $double_check_terms ) {
								break;
							}
						}

						THEMECOMPLETE_EPO_WPML()->restore_term_filters();
						THEMECOMPLETE_EPO_WPML()->restore_sql_filter();
						if ( ! $double_check_disable_categories || $double_check_terms ) {

							if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $price_meta_lang
								|| ( '' === $price_meta_lang && THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() )
							) {
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
								if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() !== $price_meta_lang && '' !== $price_meta_lang ) {
									$wpml_tmp_tmglobalprices_added[ $original_product_id ] = $price;
								}
							} else {
								if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $price_meta_lang || '' === $price_meta_lang ) {
									$wpml_tmp_tmglobalprices[ $original_product_id ] = $price;
								}
							}
						}
					} else {
						$tmglobalprices[]                 = $price;
						$tm_meta_cpf_global_forms_added[] = $price->ID;
					}
				}
				// Replace missing translation with original.
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_gp_keys = array_keys( $wpml_tmp_tmglobalprices );
					foreach ( $wpml_gp_keys as $key => $value ) {
						if ( ! isset( $wpml_tmp_tmglobalprices_added[ $value ] ) ) {
							$price                            = $wpml_tmp_tmglobalprices[ $value ];
							$tmglobalprices[]                 = $price;
							$tm_meta_cpf_global_forms_added[] = $price->ID;
						}
					}
				}
			}

			$original_post_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id, 'product' ) );

			/**
			 * Get Global options that apply to the product
			 */
			$args = [
				'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
				'post_status' => [ 'publish' ], // get only enabled global extra options.
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'asc',
				// phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_query'  => [
					[
						'key'     => 'tm_meta_product_ids',
						'value'   => ':"' . $original_post_id . '";',
						'compare' => 'LIKE',

					],
				],
			];

			$available_variations = apply_filters( 'wc_epo_global_forms_available_variations', $product->get_children() );
			$glue                 = [];

			foreach ( $available_variations as $variation_id ) {
				$variations_for_conditional_logic[] = $variation_id;
				$glue[]                             = [
					'key'     => 'tm_meta_product_ids',
					'value'   => ':"' . $variation_id . '";',
					'compare' => 'LIKE',
				];
			}
			if ( $glue ) {
				$args['meta_query']['relation'] = 'OR';
				$args['meta_query']             = array_merge( $args['meta_query'], $glue ); // phpcs:ignore WordPress.DB.SlowDBQuery
			}

			$tmglobalprices_products = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

			if ( $tmglobalprices_products ) {

				$global_id_array = [];
				if ( isset( $tmglobalprices ) ) {
					foreach ( $tmglobalprices as $price ) {
						$global_id_array[] = $price->ID;
					}
				} else {
					$tmglobalprices = [];
				}

				$wpml_tmglobalprices_products       = [];
				$wpml_tmglobalprices_products_added = [];
				foreach ( $tmglobalprices_products as $price ) {

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$price_meta_lang     = themecomplete_get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
						$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );

						if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $price_meta_lang
							|| ( '' === $price_meta_lang && THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() )
						) {
							if ( ! in_array( $price->ID, $global_id_array, true ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
								if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() !== $price_meta_lang && '' !== $price_meta_lang ) {
									$wpml_tmglobalprices_products_added[ $original_product_id ] = $price;
								}
							}
						} else {
							if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $price_meta_lang || '' === $price_meta_lang ) {
								$wpml_tmglobalprices_products[ $original_product_id ] = $price;
							}
						}
					} else {
						if ( ! in_array( $price->ID, $global_id_array, true ) ) {
							$global_id_array[]                = $price->ID;
							$tmglobalprices[]                 = $price;
							$tm_meta_cpf_global_forms_added[] = $price->ID;
						}
					}
				}
				// Replace missing translation with original.
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_gp_keys = array_keys( $wpml_tmglobalprices_products );
					foreach ( $wpml_gp_keys as $key => $value ) {
						if ( ! isset( $wpml_tmglobalprices_products_added[ $value ] ) ) {
							$price = $wpml_tmglobalprices_products[ $value ];
							if ( ! in_array( $price->ID, $global_id_array, true ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}
					}
				}
			}

			/**
			 * Get Global options that apply to the product
			 * only for translated products
			 */
			if ( floatval( $post_id ) !== $post_original_id ) {
				// Get Global options that apply to the product.
				$args = [
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
					// phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_query'  => [
						[
							'key'     => 'tm_meta_product_ids',
							'value'   => ':"' . $post_original_id . '";',
							'compare' => 'LIKE',

						],
					],

				];

				THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
				THEMECOMPLETE_EPO_WPML()->remove_term_filters();
				$tmglobalprices_products = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
				THEMECOMPLETE_EPO_WPML()->restore_term_filters();
				THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

				if ( $tmglobalprices_products ) {

					$global_id_array = [];
					if ( isset( $tmglobalprices ) ) {
						foreach ( $tmglobalprices as $price ) {
							$global_id_array[] = $price->ID;
						}
					} else {
						$tmglobalprices = [];
					}

					$wpml_tmglobalprices_products       = [];
					$wpml_tmglobalprices_products_added = [];
					foreach ( $tmglobalprices_products as $price ) {

						if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
							$price_meta_lang     = themecomplete_get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
							$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );

							if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $price_meta_lang
								|| ( '' === $price_meta_lang && THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() )
							) {
								if ( ! in_array( $price->ID, $global_id_array, true ) ) {
									$global_id_array[]                = $price->ID;
									$tmglobalprices[]                 = $price;
									$tm_meta_cpf_global_forms_added[] = $price->ID;
									if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() !== $price_meta_lang && '' !== $price_meta_lang ) {
										$wpml_tmglobalprices_products_added[ $original_product_id ] = $price;
									}
								}
							} else {
								if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $price_meta_lang || '' === $price_meta_lang ) {
									$wpml_tmglobalprices_products[ $original_product_id ] = $price;
								}
							}
						} else {
							if ( ! in_array( $price->ID, $global_id_array, true ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}
					}
					// Replace missing translation with original.
					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$wpml_gp_keys = array_keys( $wpml_tmglobalprices_products );
						foreach ( $wpml_gp_keys as $key => $value ) {
							if ( ! isset( $wpml_tmglobalprices_products_added[ $value ] ) ) {
								$price = $wpml_tmglobalprices_products[ $value ];
								if ( ! in_array( $price->ID, $global_id_array, true ) ) {
									$query = new WP_Query(
										[
											'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
											'post_status' => [ 'publish' ],
											'numberposts' => -1,
											'posts_per_page' => -1,
											'orderby'     => 'date',
											'order'       => 'asc',
											'no_found_rows' => true,
											// phpcs:ignore WordPress.DB.SlowDBQuery
											'meta_query'  => [
												'relation' => 'AND',
												[
													'key' => THEMECOMPLETE_EPO_WPML_LANG_META,
													'value' => THEMECOMPLETE_EPO_WPML()->get_default_lang(),
													'compare' => '!=',
												],
												[
													'key' => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
													'value' => $price->ID,
													'compare' => '=',
												],
											],
										]
									);
									if ( ! empty( $query->posts ) ) {
										if ( ! in_array( $query->post->ID, $global_id_array, true ) ) {
											$global_id_array[]                = $query->post->ID;
											$tmglobalprices[]                 = $query->post;
											$tm_meta_cpf_global_forms_added[] = $query->post->ID;
										}
									} else {
										$global_id_array[]                = $price->ID;
										$tmglobalprices[]                 = $price;
										$tm_meta_cpf_global_forms_added[] = $price->ID;
									}
								}
							}
						}
					}
				}
			}

			/**
			 * Support for conditional logic based on variations
			 * on translated products
			 */
			if ( floatval( $post_id ) !== $post_original_id ) {
				// Get Global options that apply to the product.
				$args = [
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
					// phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_query'  => [
						[
							'key'     => 'tm_meta_product_ids',
							'value'   => ':"' . $post_original_id . '";',
							'compare' => 'LIKE',

						],
					],
				];

				$product              = wc_get_product( $post_original_id );
				$available_variations = apply_filters( 'wc_epo_global_forms_available_variations', $product->get_children() );
				$glue                 = [];

				foreach ( $available_variations as $variation_id ) {
					$variations_for_conditional_logic[] = $variation_id;
					$glue[]                             = [
						'key'     => 'tm_meta_product_ids',
						'value'   => ':"' . floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $variation_id ) ) . '";',
						'compare' => 'LIKE',
					];
				}

				if ( $glue ) {
					$args['meta_query']['relation'] = 'OR'; // phpcs:ignore WordPress.DB.SlowDBQuery
					$args['meta_query']             = array_merge( $args['meta_query'], $glue ); // phpcs:ignore WordPress.DB.SlowDBQuery

					$tmglobalprices_products = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

					// Merge Global options.
					if ( $tmglobalprices_products ) {
						$global_id_array = [];
						if ( isset( $tmglobalprices ) ) {
							foreach ( $tmglobalprices as $price ) {
								$global_id_array[] = $price->ID;
							}
						} else {
							$tmglobalprices = [];
						}
						foreach ( $tmglobalprices_products as $price ) {
							if ( ! in_array( $price->ID, $global_id_array, true ) ) {
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}
					}
				}
			}
		}

		$tm_meta_cpf_global_forms_added = array_unique( $tm_meta_cpf_global_forms_added );

		$tm_meta_cpf_global_forms = apply_filters( 'wc_epo_additional_global_forms', $tm_meta_cpf_global_forms, $post_id, $form_prefix, $this );
		$tm_meta_cpf_global_forms = array_unique( $tm_meta_cpf_global_forms );

		foreach ( $tm_meta_cpf_global_forms as $key => $value ) {
			if ( ! in_array( $value, $tm_meta_cpf_global_forms_added, true ) ) {
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {

					$tm_meta_lang = themecomplete_get_post_meta( $value, THEMECOMPLETE_EPO_WPML_LANG_META, true );
					if ( empty( $tm_meta_lang ) ) {
						$tm_meta_lang = THEMECOMPLETE_EPO_WPML()->get_default_lang();
					}
					$meta_query   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, THEMECOMPLETE_EPO_WPML()->get_lang(), '=', 'EXISTS' );
					$meta_query[] = [
						'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
						'value'   => $value,
						'compare' => '=',
					];

					$query = new WP_Query(
						[
							'post_type'      => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
							'post_status'    => [ 'publish' ],
							'numberposts'    => -1,
							'posts_per_page' => -1,
							'orderby'        => 'date',
							'order'          => 'asc',
							'no_found_rows'  => true,
							'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery
						]
					);

					if ( ! empty( $query->posts ) ) {
						if ( $query->post_count > 1 ) {

							foreach ( $query->posts as $current_post ) {
								$metalang = themecomplete_get_post_meta( $current_post->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );

								if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $metalang ) {
									$tmglobalprices[] = get_post( $current_post->ID );
									break;
								}
							}
						} else {
							$tmglobalprices[] = get_post( $query->post->ID );
						}
					} elseif ( empty( $query->posts ) ) {
						$tmglobalprices[] = get_post( $value );
					}
				} else {
					$ispostactive = get_post( $value );
					if ( $ispostactive && 'publish' === $ispostactive->post_status ) {
						$tmglobalprices[] = get_post( $value );
					}
				}
			}
		}

		// Add current product to Global options array (has to be last to not conflict).
		$tmglobalprices[] = THEMECOMPLETE_EPO_HELPER()->get_cached_post( $post_id );

		// End of DB init.

		$epos                        = $this->generate_global_epos( $tmglobalprices, $post_id, $this->tm_original_builder_elements, $variations_for_conditional_logic, $no_cache, $no_disabled );
		$global_epos                 = $epos['global'];
		$raw_epos                    = $epos['raw_epos'];
		$epos_prices                 = $epos['price'];
		$variation_element_id        = $epos['variation_element_id'];
		$variation_section_id        = $epos['variation_section_id'];
		$variations_disabled         = $epos['variations_disabled'];
		$global_product_epos_uniqids = $epos['product_epos_uniqids'];
		$product_epos_choices        = $epos['product_epos_choices'];

		if ( is_array( $global_epos ) ) {
			ksort( $global_epos );
		}

		$product_epos = $this->generate_local_epos( $tmlocalprices, $post_id );

		$global_epos = $this->tm_fill_element_names( $post_id, $global_epos, $product_epos, $form_prefix, 'epo' );

		$epos = [
			'global'               => $global_epos,
			'raw_epos'             => $raw_epos,
			'global_ids'           => $tmglobalprices,
			'local'                => $product_epos['product_epos'],
			'price'                => $epos_prices,
			'variation_element_id' => $variation_element_id,
			'variation_section_id' => $variation_section_id,
			'variations_disabled'  => $variations_disabled,
			'epos_uniqids'         => array_merge( $product_epos['product_epos_uniqids'], $global_product_epos_uniqids ),
			'product_epos_choices' => $product_epos_choices,
		];

		$this->cpf[ $post_id ][ "{$no_disabled}" ][ "f{$form_prefix}" ] = $epos;

		return $epos;

	}

	/**
	 * Generate normal (local) option array
	 *
	 * @param array   $tmlocalprices Array of posts for normal options.
	 * @param integer $post_id The product id.
	 *
	 * @return array
	 */
	public function generate_local_epos( $tmlocalprices, $post_id ) {
		$product_epos         = [];
		$product_epos_uniqids = [];
		if ( $tmlocalprices ) {
			THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
			$attributes      = themecomplete_get_attributes( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ) );
			$wpml_attributes = themecomplete_get_attributes( $post_id );

			foreach ( $tmlocalprices as $price ) {

				$tmcp_id = absint( $price->ID );

				$n = themecomplete_get_post_meta( $tmcp_id, 'tmcp_attribute', true );
				if ( ! isset( $attributes[ $n ] ) ) {
					continue;
				}
				$att = $attributes[ $n ];
				if ( $att['is_variation'] || sanitize_title( $att['name'] ) !== $n ) {
					continue;
				}

				$tmcp_required                           = themecomplete_get_post_meta( $tmcp_id, 'tmcp_required', true );
				$tmcp_hide_price                         = themecomplete_get_post_meta( $tmcp_id, 'tmcp_hide_price', true );
				$tmcp_limit                              = themecomplete_get_post_meta( $tmcp_id, 'tmcp_limit', true );
				$product_epos[ $tmcp_id ]['is_form']     = 0;
				$product_epos[ $tmcp_id ]['required']    = empty( $tmcp_required ) ? 0 : 1;
				$product_epos[ $tmcp_id ]['hide_price']  = empty( $tmcp_hide_price ) ? 0 : 1;
				$product_epos[ $tmcp_id ]['limit']       = empty( $tmcp_limit ) ? '' : $tmcp_limit;
				$product_epos[ $tmcp_id ]['name']        = themecomplete_get_post_meta( $tmcp_id, 'tmcp_attribute', true );
				$product_epos[ $tmcp_id ]['is_taxonomy'] = themecomplete_get_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', true );
				$product_epos[ $tmcp_id ]['label']       = wc_attribute_label( $product_epos[ $tmcp_id ]['name'] );
				$product_epos[ $tmcp_id ]['type']        = themecomplete_get_post_meta( $tmcp_id, 'tmcp_type', true );
				$product_epos_uniqids[]                  = $product_epos[ $tmcp_id ]['name'];

				// Retrieve attributes.
				$product_epos[ $tmcp_id ]['attributes']      = [];
				$product_epos[ $tmcp_id ]['attributes_wpml'] = [];
				if ( $product_epos[ $tmcp_id ]['is_taxonomy'] ) {
					if ( ! ( $attributes[ $product_epos[ $tmcp_id ]['name'] ]['is_variation'] ) ) {
						$orderby = wc_attribute_orderby( $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'] );
						$args    = 'orderby=name&hide_empty=0';
						switch ( $orderby ) {
							case 'name':
								$args = [
									'orderby'    => 'name',
									'hide_empty' => false,
									'menu_order' => false,
								];
								break;
							case 'id':
								$args = [
									'orderby'    => 'id',
									'order'      => 'ASC',
									'menu_order' => false,
									'hide_empty' => false,
								];
								break;
							case 'menu_order':
								$args = [
									'menu_order' => 'ASC',
									'hide_empty' => false,
								];
								break;
						}

						$all_terms = THEMECOMPLETE_EPO_WPML()->get_terms( null, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], $args );

						if ( $all_terms ) {
							foreach ( $all_terms as $term ) {
								$has_term     = has_term( (int) $term->term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ) ) ? 1 : 0;
								$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() && function_exists( 'icl_object_id' ) ? icl_object_id( $term->term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], false ) : false;
								if ( $has_term ) {
									$product_epos[ $tmcp_id ]['attributes'][ esc_attr( $term->slug ) ] = apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $term->name ), null, null );
									if ( $wpml_term_id ) {
										$wpml_term = get_term( $wpml_term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'] );
										$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( $term->slug ) ] = apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $wpml_term->name ), null, null );
									} else {
										$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( $term->slug ) ] = $product_epos[ $tmcp_id ]['attributes'][ esc_attr( $term->slug ) ];
									}
								}
							}
						}
					}
				} else {
					if ( isset( $attributes[ $product_epos[ $tmcp_id ]['name'] ] ) ) {
						$options      = array_map( 'trim', explode( WC_DELIMITER, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) );
						$wpml_options = isset( $wpml_attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) ? array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) ) : $options;
						foreach ( $options as $k => $option ) {
							$product_epos[ $tmcp_id ]['attributes'][ esc_attr( sanitize_title( $option ) ) ]      = esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $option, null, null ) );
							$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( sanitize_title( $option ) ) ] = esc_html( apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, null, null ) );
						}
					}
				}

				// Retrieve price rules.
				$_regular_price                    = themecomplete_get_post_meta( $tmcp_id, '_regular_price', true );
				$_regular_price_type               = themecomplete_get_post_meta( $tmcp_id, '_regular_price_type', true );
				$product_epos[ $tmcp_id ]['rules'] = $_regular_price;

				$_regular_price_filtered                    = THEMECOMPLETE_EPO_HELPER()->array_map_deep( $_regular_price, $_regular_price_type, [ $this, 'tm_epo_price_filtered' ] );
				$product_epos[ $tmcp_id ]['rules_filtered'] = $_regular_price_filtered;

				$product_epos[ $tmcp_id ]['rules_type'] = $_regular_price_type;
				if ( ! is_array( $_regular_price ) ) {
					$_regular_price = [];
				}
				if ( ! is_array( $_regular_price_type ) ) {
					$_regular_price_type = [];
				}
				foreach ( $_regular_price as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$_regular_price[ $key ][ $k ] = wc_format_localized_price( $v );
					}
				}
				foreach ( $_regular_price_type as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$_regular_price_type[ $key ][ $k ] = $v;
					}
				}
				$product_epos[ $tmcp_id ]['price_rules']          = $_regular_price;
				$product_epos[ $tmcp_id ]['price_rules_filtered'] = $_regular_price_filtered;
				$product_epos[ $tmcp_id ]['price_rules_type']     = $_regular_price_type;
			}
			THEMECOMPLETE_EPO_WPML()->restore_sql_filter();
		}

		return [
			'product_epos'         => $product_epos,
			'product_epos_uniqids' => $product_epos_uniqids,
		];
	}

	/**
	 * Generate global (builder) option array
	 *
	 * @param array   $tmglobalprices Array of posts (global or directly on the product) that have saved options.
	 * @param integer $post_id The product id.
	 * @param array   $tm_original_builder_elements Builder element attributes.
	 * @param array   $variations_for_conditional_logic The variations used in conditiona logic.
	 * @param boolean $no_cache If we should use cached results.
	 * @param boolean $no_disabled If disabled elements should be skipped.
	 * @return array
	 */
	public function generate_global_epos( $tmglobalprices, $post_id, $tm_original_builder_elements, $variations_for_conditional_logic = [], $no_cache = false, $no_disabled = false ) {
		$global_epos              = [];
		$product_epos_uniqids     = [];
		$product_epos_choices     = [];
		$epos_prices              = [];
		$extra_section_logic      = [];
		$extra_section_hide_logic = [];
		$raw_epos                 = [];

		$not_isset_global_post = false;
		if ( ! isset( $GLOBALS['post'] ) ) {
			$not_isset_global_post = true;
			$GLOBALS['post']       = $post_id; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}

		$post_original_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) );

		$variation_element_id = false;
		$variation_section_id = false;

		$enable_sales = 'sale' === $this->tm_epo_global_options_price_mode;

		if ( $tmglobalprices ) {

			foreach ( $tmglobalprices as $price ) {
				if ( ! is_object( $price ) ) {
					continue;
				}

				$original_product_id = $price->ID;
				$object              = $price;
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $price->ID, $price->post_type );
					if ( ! $wpml_is_original_product ) {
						$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						if ( 'product' === $price->post_type ) {
							$object = wc_get_product( $original_product_id );
						} else {
							$object = get_post( $original_product_id );
						}
					}
				}

				$tmcp_id                     = absint( $original_product_id );
				$tmcp_meta                   = themecomplete_get_post_meta( $object, 'tm_meta', true );
				$enabled_roles               = themecomplete_get_post_meta( $object, 'tm_meta_enabled_roles', true );
				$disabled_roles              = themecomplete_get_post_meta( $object, 'tm_meta_disabled_roles', true );
				$tm_meta_product_ids         = themecomplete_get_post_meta( $object, 'tm_meta_product_ids', true );
				$tm_meta_product_exclude_ids = themecomplete_get_post_meta( $object, 'tm_meta_product_exclude_ids', true );

				if ( ! empty( $enabled_roles ) || ! empty( $disabled_roles ) ) {
					$enable = false;
					if ( ! is_array( $enabled_roles ) ) {
						$enabled_roles = [ $enabled_roles ];
					}
					if ( ! is_array( $disabled_roles ) ) {
						$disabled_roles = [ $disabled_roles ];
					}
					if ( isset( $enabled_roles[0] ) && '' === $enabled_roles[0] ) {
						$enabled_roles = [];
					}

					if ( isset( $disabled_roles[0] ) && '' === $disabled_roles[0] ) {
						$disabled_roles = [];
					}

					if ( empty( $enabled_roles ) && ! empty( $disabled_roles ) ) {
						$enable = true;
					}

					// Get all roles.
					$current_user = wp_get_current_user();

					foreach ( $enabled_roles as $key => $value ) {
						if ( '@everyone' === $value ) {
							$enable = true;
						}
						if ( '@loggedin' === $value && is_user_logged_in() ) {
							$enable = true;
						}
					}

					foreach ( $disabled_roles as $key => $value ) {
						if ( '@everyone' === $value ) {
							$enable = false;
						}
						if ( '@loggedin' === $value && is_user_logged_in() ) {
							$enable = false;
						}
					}

					if ( $current_user instanceof WP_User ) {
						$roles = $current_user->roles;

						if ( is_array( $roles ) ) {

							foreach ( $roles as $key => $value ) {
								if ( in_array( $value, $enabled_roles, true ) ) {
									$enable = true;
									break;
								}
							}

							foreach ( $roles as $key => $value ) {
								if ( in_array( $value, $disabled_roles, true ) ) {
									$enable = false;
									break;
								}
							}
						}
					}

					if ( ! $enable ) {
						continue;
					}
				}

				$current_builder = THEMECOMPLETE_EPO_WPML()->is_active() ? themecomplete_get_post_meta( $price, 'tm_meta_wpml', true ) : [];

				if ( ! $current_builder ) {
					$current_builder = [];
				} else {
					if ( ! isset( $current_builder['tmfbuilder'] ) ) {
						$current_builder['tmfbuilder'] = [];
					}
					$current_builder = $current_builder['tmfbuilder'];
				}

				$priority = isset( $tmcp_meta['priority'] ) ? absint( $tmcp_meta['priority'] ) : 1000;

				if ( isset( $tmcp_meta['tmfbuilder'] ) ) {

					$global_epos[ $priority ][ $tmcp_id ]['is_form']     = 1;
					$global_epos[ $priority ][ $tmcp_id ]['is_taxonomy'] = 0;
					$global_epos[ $priority ][ $tmcp_id ]['name']        = $price->post_title;
					$global_epos[ $priority ][ $tmcp_id ]['description'] = $price->post_excerpt;
					$global_epos[ $priority ][ $tmcp_id ]['sections']    = [];

					$builder = $tmcp_meta['tmfbuilder'];
					if ( is_array( $builder ) && count( $builder ) > 0 && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] ) > 0 ) {
						// All the elements.
						$_elements = $builder['element_type'];
						// All element sizes.
						$_div_size = $builder['div_size'];

						// All sections (holds element count for each section).
						$_sections = $builder['sections'];
						// All section sizes.
						$_sections_size = $builder['sections_size'];
						// All section styles.
						$_sections_style = $builder['sections_style'];
						// All section placements.
						$_sections_placement = $builder['sections_placement'];

						$_sections_slides = isset( $builder['sections_slides'] ) ? $builder['sections_slides'] : '';

						if ( ! is_array( $_sections ) ) {
							$_sections = [ count( $_elements ) ];
						}
						if ( ! is_array( $_sections_size ) ) {
							$_sections_size = array_fill( 0, count( $_sections ), 'w100' );
						}
						if ( ! is_array( $_sections_style ) ) {
							$_sections_style = array_fill( 0, count( $_sections ), '' );
						}
						if ( ! is_array( $_sections_placement ) ) {
							$_sections_placement = array_fill( 0, count( $_sections ), 'before' );
						}

						if ( ! is_array( $_sections_slides ) ) {
							$_sections_slides = array_fill( 0, count( $_sections ), '' );
						}

						$_helper_counter = 0;
						$_counter        = [];
						$_sectionscount  = count( $_sections );

						for ( $_s = 0; $_s < $_sectionscount; $_s ++ ) {
							$_sections_uniqid = $this->get_builder_element( 'sections_uniqid', $builder, $current_builder, $_s, THEMECOMPLETE_EPO_HELPER()->tm_temp_uniqid( count( $_sections ) ) );
							$_sections[ $_s ] = (int) $_sections[ $_s ];

							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ] = [
								'total_elements'           => $_sections[ $_s ],
								'sections_size'            => $_sections_size[ $_s ],
								'sections_slides'          => isset( $_sections_slides[ $_s ] ) ? $_sections_slides[ $_s ] : '',
								'sections_tabs_labels'     => $this->get_builder_element( 'sections_tabs_labels', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_style'           => $_sections_style[ $_s ],
								'sections_placement'       => $_sections_placement[ $_s ],
								'sections_uniqid'          => $_sections_uniqid,
								'sections_clogic'          => $this->get_builder_element( 'sections_clogic', $builder, $current_builder, $_s, false, 'sections', '', $_sections_uniqid ),
								'sections_logic'           => $this->get_builder_element( 'sections_logic', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_class'           => $this->get_builder_element( 'sections_class', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_type'            => $this->get_builder_element( 'sections_type', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_popupbutton'     => $this->get_builder_element( 'sections_popupbutton', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_popupbuttontext' => $this->get_builder_element( 'sections_popupbuttontext', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_background_color' => $this->get_builder_element( 'sections_background_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label_background_color'   => $this->get_builder_element( 'sections_label_background_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description_background_color' => $this->get_builder_element( 'sections_subtitle_background_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label_size'               => $this->get_builder_element( 'section_header_size', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label'                    => $this->get_builder_element( 'section_header_title', $builder, $current_builder, $_s, '', 'sections', 'wc_epo_label', $_sections_uniqid ),
								'label_color'              => $this->get_builder_element( 'section_header_title_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label_position'           => $this->get_builder_element( 'section_header_title_position', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description'              => $this->get_builder_element( 'section_header_subtitle', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description_position'     => $this->get_builder_element( 'section_header_subtitle_position', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description_color'        => $this->get_builder_element( 'section_header_subtitle_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'divider_type'             => $this->get_builder_element( 'section_divider_type', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
							];

							$this->current_option_features[] = 'section' . $this->get_builder_element( 'sections_type', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid );

							$element_no_in_section = -1;
							$section_slides        = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_slides'];
							if ( '' !== $section_slides && ( 'slider' === $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_type'] || 'tabs' === $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_type'] ) ) {
								$section_slides = explode( ',', $section_slides );

							}
							$section_slides_copy = $section_slides;
							for ( $k0 = $_helper_counter; $k0 < $_helper_counter + $_sections[ $_s ]; $k0 ++ ) {
								if ( ! isset( $_elements[ $k0 ] ) ) {
									continue;
								}

								$element_no_in_section ++;
								$current_element = $_elements[ $k0 ];

								$is_override_element      = false;
								$original_current_element = $current_element;
								if ( ( $this->is_bto || $this->is_associated ) && 'product' === $current_element ) {
									$current_element     = 'header';
									$is_override_element = true;
								}
								$element_object = isset( $tm_original_builder_elements[ $current_element ] ) ? $tm_original_builder_elements[ $current_element ] : false;

								$raw_epos[] = $current_element;

								// Delete logic for variations section - not applicable.
								if ( 'variations' === $current_element ) {
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic']  = '';
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] = '';
								}

								if ( $element_object ) {
									if ( ! isset( $_counter[ $original_current_element ] ) ) {
										$_counter[ $original_current_element ] = 0;
									} else {
										$_counter[ $original_current_element ] ++;
									}
									$current_counter = $_counter[ $original_current_element ];

									if ( $is_override_element ) {
										if ( current_user_can( 'administrator' ) ) {
											if ( $this->is_bto ) {
												$builder['header_title'][ $current_counter ]         = esc_html__( 'Product element is not supported for components!', 'woocommerce-tm-extra-product-options' );
												$current_builder['header_title'][ $current_counter ] = esc_html__( 'Product element is not supported for components!', 'woocommerce-tm-extra-product-options' );
											}
											if ( $this->is_associated ) {
												$builder['header_title'][ $current_counter ] = esc_html__( 'Product element is not supported within another product element!', 'woocommerce-tm-extra-product-options' );
											}
										}
									}

									$_options                         = [];
									$_options_all                     = []; // even disabled ones - currently used for WPML translation at get_wpml_translation_by_id.
									$_regular_price                   = [];
									$_regular_price_filtered          = [];
									$_original_regular_price_filtered = [];
									$_regular_price_type              = [];
									$_new_type                        = $current_element;
									$_prefix                          = '';
									$_min_price0                      = '';
									$_min_price10                     = '';
									$_min_price                       = '';
									$_max_price                       = '';
									$_regular_currencies              = [];
									$price_per_currencies_original    = [];
									$price_per_currencies             = [];
									$_description                     = false;
									$_extra_multiple_choices          = false;
									$_use_lightbox                    = '';
									$_current_deleted_choices         = [];
									$_is_price_fee                    = '';

									if ( $element_object ) {
										if (
											( true === $element_object->is_addon && 'display' === $element_object->is_post ) ||
											( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type ) ||
											( 'multiple' === $element_object->type || 'multipleall' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) ||
											'template' === $element_object->type
											) {
											$_prefix = $original_current_element . '_';
										}

										$is_override_element_prefix = $is_override_element ? $original_current_element . '_' : $_prefix;

										$element_uniqueid = $this->get_builder_element( $is_override_element_prefix . 'uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $original_current_element );

										$is_enabled  = $this->get_builder_element( $is_override_element_prefix . 'enabled', $builder, $current_builder, $current_counter, '2', $original_current_element, '', $element_uniqueid );
										$is_required = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										// Currently $no_disabled is disabled by default
										// to allow the conditional logic
										// to work correctly when there is a disabled element.
										if ( $no_disabled ) {
											if ( '' === $is_enabled || '0' === $is_enabled ) {

												if ( is_array( $section_slides ) ) {
													$elements_done = 0;
													foreach ( $section_slides as $section_slides_key => $section_slides_value ) {
														$section_slides_value = (int) $section_slides_value;
														$elements_done        = $elements_done + $section_slides_value;
														$previous_done        = $elements_done - $section_slides_value;

														if ( $element_no_in_section >= $previous_done && $element_no_in_section < $elements_done ) {
															$section_slides_copy[ $section_slides_key ]                                 = (string) ( (int) ( $section_slides_copy[ $section_slides_key ] ) - 1 );
															$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_slides'] = implode( ',', $section_slides_copy );
															break;
														}
													}
												}

												continue;
											}
										}

										$tm_epo_options_cache = ( ! $no_cache && 'yes' === $this->tm_epo_options_cache ) ? true : false;

										if ( isset( $wpml_is_original_product ) && ! empty( $wpml_is_original_product ) && apply_filters( 'wc_epo_use_elements_cache', $tm_epo_options_cache ) && isset( $this->cpf_single[ $element_uniqueid ] ) ) {
											$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];
											if ( isset( $this->cpf_single_epos_prices[ $element_uniqueid ] ) ) {
												$epos_prices[] = $this->cpf_single_epos_prices[ $element_uniqueid ];
											}
											if ( isset( $this->cpf_single_variation_element_id[ $element_uniqueid ] ) ) {
												$variation_element_id = $this->cpf_single_variation_element_id[ $element_uniqueid ];
											}
											if ( isset( $this->cpf_single_variation_section_id[ $element_uniqueid ] ) ) {
												$variation_section_id = $this->cpf_single_variation_section_id[ $element_uniqueid ];
											}

											continue;
										}

										if ( $is_enabled && 'template' === $current_element ) {
											$templateids = $this->get_builder_element( $_prefix . 'templateids', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											if ( empty( $templateids ) ) {
												continue;
											}
											$templateglobalprices = get_post( $templateids );
											if ( $templateglobalprices ) {
												if ( 'publish' !== $templateglobalprices->post_status ) {
													continue;
												}
											} else {
												continue;
											}
											$templateglobalprices = [ $templateglobalprices ];
											$template_elements    = $this->generate_global_epos( $templateglobalprices, $post_id, $tm_original_builder_elements, $variations_for_conditional_logic, $no_cache, $no_disabled );

											// Add template elements
											// Each foreach loop should produce only 1 result!
											if ( isset( $template_elements['global'] ) ) {
												$added_element_uniqid = false;
												foreach ( $template_elements['global'] as $pid_element ) {
													foreach ( $pid_element as $id_element ) {
														foreach ( $id_element['sections'] as $id_sections ) {
															if ( isset( $id_sections['elements'] ) ) {
																foreach ( $id_sections['elements'] as $added_element ) {
																	$added_element_uniqid  = $added_element['uniqid'];
																	$added_element['size'] = $_div_size[ $k0 ];
																	if ( ! $is_enabled ) {
																		$added_element['enabled'] = $is_enabled;
																	}
																	$added_element['uniqid'] = $element_uniqueid;
																	$added_element['clogic'] = $this->get_builder_element( $_prefix . 'clogic', $builder, $current_builder, $current_counter, false, $current_element, '', $element_uniqueid );
																	$added_element['logic']  = $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
																	$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $added_element;
																	$this->cpf_single[ $element_uniqueid ]                                 = $added_element;
																}
															}
														}
													}
												}
												$product_epos_uniqids[]                    = $element_uniqueid;
												$product_epos_choices[ $element_uniqueid ] = $template_elements['product_epos_choices'];
												// This should always be true!
												if ( $added_element_uniqid ) {
													if ( isset( $product_epos_choices[ $element_uniqueid ][ $added_element_uniqid ] ) ) {
														$product_epos_choices[ $element_uniqueid ] = $product_epos_choices[ $element_uniqueid ][ $added_element_uniqid ];
													}
												}
											}
											continue;
										}

										if ( isset( $builder[ $current_element . '_fee' ] ) && isset( $builder[ $current_element . '_fee' ][ $current_counter ] ) ) {
											$_is_price_fee = $builder[ $current_element . '_fee' ][ $current_counter ];
										}

										// Backwards compatibility.
										$swatchmode   = $this->get_builder_element( $_prefix . 'swatchmode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$show_tooltip = $this->get_builder_element( $_prefix . 'show_tooltip', $builder, $current_builder, $current_counter, '0', $current_element, '', $element_uniqueid );
										if ( '0' === $show_tooltip ) {
											$show_tooltip = $swatchmode;
										}

										if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type ) {
											$_prefix = $current_element . '_';

											$_is_field_required     = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_changes_product_image = $this->get_builder_element( $_prefix . 'changes_product_image', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_replacement_mode      = $this->get_builder_element( $_prefix . 'replacement_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_swatch_position       = $this->get_builder_element( $_prefix . 'swatch_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_use_images            = $this->get_builder_element( $_prefix . 'use_images', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_use_colors            = $this->get_builder_element( $_prefix . 'use_colors', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_price                 = isset( $builder[ $current_element . '_price' ] ) ? $builder[ $current_element . '_price' ][ $current_counter ] : '';
											$_price                 = $this->get_builder_element( $_prefix . 'price', $builder, $current_builder, $current_counter, $_price, $current_element, 'wc_epo_option_regular_price', $element_uniqueid );

											$_original_regular_price_filtered = $_price;
											if ( $enable_sales && isset( $builder[ $current_element . '_sale_price' ][ $current_counter ] ) && '' !== $builder[ $current_element . '_sale_price' ][ $current_counter ] ) {
												$_price = $builder[ $current_element . '_sale_price' ][ $current_counter ];
												$_price = $this->get_builder_element( $_prefix . 'sale_price', $builder, $current_builder, $current_counter, $_price, $current_element, 'wc_epo_option_sale_price', $element_uniqueid );
											}

											$_price                           = apply_filters( 'wc_epo_apply_discount', $_price, $_original_regular_price_filtered, $post_id );
											$_original_regular_price_filtered = apply_filters( 'wc_epo_enable_shortocde', $_original_regular_price_filtered, $_original_regular_price_filtered, $post_id );

											$this_price_type = '';

											$_regular_price_type    = [ [ '' ] ];
											$_for_filter_price_type = '';

											// backwards compatiiblity.
											if ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) {
												$_regular_price_type = $builder[ $current_element . '_price_type' ][ $current_counter ];
												$this_price_type     = $_regular_price_type;

												switch ( $_regular_price_type ) {
													case 'fee':
														$_regular_price_type = '';
														$_is_price_fee       = '1';
														break;
													case 'stepfee':
														$_regular_price_type = 'step';
														$_is_price_fee       = '1';
														break;
													case 'currentstepfee':
														$_regular_price_type = 'currentstep';
														$_is_price_fee       = '1';
														break;
												}
												$_for_filter_price_type = $_regular_price_type;
												$_regular_price_type    = [ [ $_regular_price_type ] ];
											}

											if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type ) {
												$_regular_price = [ [ $_price ] ];
											} else {
												$_regular_price = [ [ wc_format_decimal( $_price, false, true ) ] ];
											}

											$lookuptable   = $this->get_builder_element( $_prefix . 'lookuptable', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$lookuptable_x = $this->get_builder_element( $_prefix . 'lookuptable_x', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$lookuptable_y = $this->get_builder_element( $_prefix . 'lookuptable_y', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											if ( 'lookuptable' === $this_price_type ) {
												$table     = trim( THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $lookuptable, '|' ) );
												$table_num = trim( substr( $lookuptable, ( 0 - ( strlen( strrchr( $lookuptable, '|' ) ) - 1 ) ) ) );

												$xy            = '0';
												$lookuptable_x = trim( $lookuptable_x );
												$lookuptable_y = trim( $lookuptable_y );
												if ( ! empty( $lookuptable_x ) ) {
													if ( ! THEMECOMPLETE_EPO_HELPER()->str_startswith( $lookuptable_x, '{' ) ) {
														$lookuptable_x = '{field.' . $lookuptable_x . '.text}';
													}
												}
												if ( ! empty( $lookuptable_y ) ) {
													if ( ! THEMECOMPLETE_EPO_HELPER()->str_startswith( $lookuptable_y, '{' ) ) {
														$lookuptable_y = '{field.' . $lookuptable_y . '.text}';
													}
												}
												if ( ! empty( $lookuptable_x ) && ! empty( $lookuptable_y ) ) {
													$xy = '[' . $lookuptable_x . ', ' . $lookuptable_y . ']';
												} elseif ( ! empty( $lookuptable_x ) ) {
													$xy = $lookuptable_x;
												} elseif ( ! empty( $lookuptable_y ) ) {
													$xy = $lookuptable_y;
												}
												$this_price_type                  = 'math';
												$_regular_price_type              = [ [ $this_price_type ] ];
												$_price                           = 'lookuptable(' . $xy . ', ["' . $table . '", ' . $table_num . '])';
												$_original_regular_price_filtered = $_price;
												$_regular_price                   = [ [ $_price ] ];
											}

											if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
												global $woocommerce_wpml;
												global $sitepress;
												if ( $woocommerce_wpml && isset( $original_product_id ) && isset( $wpml_is_original_product ) ) {

													$basetype     = $price->post_type;
													$translations = THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_translations( THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_trid( $original_product_id, 'post_product' ), 'product' );

													$woocommerce_wpml_currencies = $woocommerce_wpml->settings['currency_options'];

													foreach ( $woocommerce_wpml_currencies as $currency => $currency_data ) {

														$thisbuilder = [];

														if ( ! isset( $currency_data['languages'] ) ) {
															continue;
														}

														foreach ( $currency_data['languages'] as $lang => $is_lang_enabled ) {

															if ( $is_lang_enabled && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {

																if ( 'product' === $basetype ) {

																	if ( isset( $translations[ $lang ] ) ) {

																		$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $translations[ $lang ]->element_id, 'product' );

																		if ( $this_wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																			$thisbuilder = $builder;
																		} else {
																			$thisbuilder = themecomplete_get_post_meta( $translations[ $lang ]->element_id, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = [];
																				}
																			}
																		}
																	}
																} else {
																	if ( $wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																		$thisbuilder = $builder;
																	} else {
																		$args                 = [
																			'post_type'   => $basetype,
																			'post_status' => [ 'publish', 'draft' ], // get only enabled global extra options.
																			'numberposts' => -1,
																			'orderby'     => 'date',
																			'order'       => 'asc',
																		];
																		$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $lang, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
																		$args['meta_query'][] = [
																			'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
																			'value'   => $original_product_id,
																			'compare' => '=',
																		];
																		$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

																		if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {
																			$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $other_translations[0]->ID, $basetype );
																			$thisbuilder                   = themecomplete_get_post_meta( $other_translations[0]->ID, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = [];
																				}
																			}
																		}
																	}
																}

																break;
															}
														}
														if ( $currency !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
															$_current_currency_price      = ( isset( $thisbuilder[ $current_element . '_price_' . $currency ] ) && isset( $thisbuilder[ $current_element . '_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ $current_element . '_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_price_' . $currency ][ $current_counter ] : '';
															$_current_currency_sale_price = ( isset( $thisbuilder[ $current_element . '_sale_price_' . $currency ] ) && isset( $thisbuilder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] : '';
														} else {
															$_current_currency_price      = isset( $thisbuilder[ $current_element . '_price' ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_price' ][ $current_counter ] : '';
															$_current_currency_sale_price = isset( $thisbuilder[ $current_element . '_sale_price' ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_sale_price' ][ $current_counter ] : '';
														}

														if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ) ) {
															if ( is_array( $_current_currency_price ) ) {
																foreach ( $_current_currency_price as $_k => $_v ) {
																	if ( '' !== $_v ) {
																		$_current_currency_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																	}
																}
															}
														}
														if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ) ) {
															if ( is_array( $_current_currency_sale_price ) ) {
																foreach ( $_current_currency_sale_price as $_k => $_v ) {
																	if ( '' !== $_v ) {
																		$_current_currency_sale_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																	}
																}
															}
														}

														if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type ) {
															$price_per_currencies_original[ $currency ] = [ [ $_current_currency_price ] ];
														} else {
															$price_per_currencies_original[ $currency ] = [ [ wc_format_decimal( $_current_currency_price, false, true ) ] ];
														}

														$_current_currency_price          = apply_filters( 'wc_epo_option_regular_price' . $currency, $_current_currency_price, $_prefix . 'price' . $currency, $element_uniqueid );
														$_current_currency_sale_price     = apply_filters( 'wc_epo_option_sale_price' . $currency, $_current_currency_price, $_prefix . 'sale_price' . $currency, $element_uniqueid );
														$_original_current_currency_price = $_current_currency_price;

														if ( $enable_sales && $_current_currency_sale_price && '' !== $_current_currency_sale_price ) {
															$_current_currency_price = $_current_currency_sale_price;
														}
														$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );

														if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type ) {
															$price_per_currencies[ $currency ] = [ [ $_current_currency_price ] ];
														} else {
															$price_per_currencies[ $currency ] = [ [ wc_format_decimal( $_current_currency_price, false, true ) ] ];
														}
													}
												}
											} else {
												foreach ( THEMECOMPLETE_EPO_HELPER()->get_currencies() as $currency ) {
													$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );

													if ( '' === $mt_prefix ) {
														$_current_currency_price          = $_price;
														$_original_current_currency_price = $_current_currency_price;
													} else {
														$_current_currency_price          = isset( $builder[ $current_element . '_price' . $mt_prefix ][ $current_counter ] ) ? $builder[ $current_element . '_price' . $mt_prefix ][ $current_counter ] : '';
														$_current_currency_sale_price     = isset( $builder[ $current_element . '_sale_price' . $mt_prefix ][ $current_counter ] ) ? $builder[ $current_element . '_sale_price' . $mt_prefix ][ $current_counter ] : '';
														$_current_currency_price          = $this->get_builder_element( $_prefix . 'price' . $mt_prefix, $builder, $current_builder, $current_counter, $_current_currency_price, $current_element, 'wc_epo_option_regular_price' . $mt_prefix, $element_uniqueid );
														$_current_currency_sale_price     = $this->get_builder_element( $_prefix . 'sale_price' . $mt_prefix, $builder, $current_builder, $current_counter, $_current_currency_sale_price, $current_element, 'wc_epo_option_sale_price' . $mt_prefix, $element_uniqueid );
														$_original_current_currency_price = $_current_currency_price;
														if ( $enable_sales && $_current_currency_sale_price && '' !== $_current_currency_sale_price ) {
															$_current_currency_price = $_current_currency_sale_price;
														}
														$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );
													}

													if ( '' !== $_original_current_currency_price ) {
														if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type ) {
															$price_per_currencies_original[ $currency ] = [ [ $_original_current_currency_price ] ];
														} else {
															$price_per_currencies_original[ $currency ] = [ [ wc_format_decimal( $_original_current_currency_price, false, true ) ] ];
														}
													}

													if ( '' !== $_current_currency_price ) {
														if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type ) {
															$price_per_currencies[ $currency ] = [ [ $_current_currency_price ] ];
														} else {
															$price_per_currencies[ $currency ] = [ [ wc_format_decimal( $_current_currency_price, false, true ) ] ];
														}
													}
												}
											}

											$new_currency = false;
											$mt_prefix    = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( null, '' );

											$_current_currency_original_price = isset( $price_per_currencies_original[ $mt_prefix ] ) ? $price_per_currencies_original[ $mt_prefix ][0][0] : '';
											$_current_currency_price          = isset( $price_per_currencies[ $mt_prefix ] ) ? $price_per_currencies[ $mt_prefix ][0][0] : '';

											if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
												if ( '' === $_current_currency_original_price && $mt_prefix !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
													$_current_currency_original_price = isset( $price_per_currencies_original[ $woocommerce_wpml->multi_currency->get_default_currency() ] ) ? $price_per_currencies_original[ $woocommerce_wpml->multi_currency->get_default_currency() ][0][0] : '';
													if ( '' !== $_current_currency_original_price && 'math' !== $this_price_type && 'lookuptable' !== $this_price_type ) {
														$_current_currency_original_price = apply_filters( 'wc_epo_get_current_currency_price', $_current_currency_original_price, $this_price_type );
													}
												}
												if ( '' === $_current_currency_price && $mt_prefix !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
													$_current_currency_price = isset( $price_per_currencies[ $woocommerce_wpml->multi_currency->get_default_currency() ] ) ? $price_per_currencies[ $woocommerce_wpml->multi_currency->get_default_currency() ][0][0] : '';
													if ( '' !== $_current_currency_price && 'math' !== $this_price_type && 'lookuptable' !== $this_price_type ) {
														$_current_currency_price = apply_filters( 'wc_epo_get_current_currency_price', $_current_currency_price, $this_price_type );
													}
												}
											}

											if ( '' !== $mt_prefix && '' !== $_current_currency_price ) {
												$_price                           = $_current_currency_price;
												$_original_regular_price_filtered = $_current_currency_original_price;

												$_regular_currencies = [ themecomplete_get_woocommerce_currency() ];
												$new_currency        = true;
											}

											if ( ! $new_currency ) {
												$_price                           = apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type );
												$_original_regular_price_filtered = apply_filters( 'wc_epo_get_current_currency_price', $_original_regular_price_filtered, $_for_filter_price_type );
											}

											$_price                           = apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
											$_original_regular_price_filtered = apply_filters( 'wc_epo_price', $_original_regular_price_filtered, $_for_filter_price_type, $post_id );

											if ( '' === $_is_price_fee && '' !== $_price && isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) && '' === $builder[ $current_element . '_price_type' ][ $current_counter ] ) {
												$_min_price = wc_format_decimal( $_price, false, true );
												$_max_price = $_min_price;
												if ( $_is_field_required ) {
													$_min_price0 = $_min_price;
												} else {
													$_min_price0  = 0;
													$_min_price10 = $_min_price;
												}
											} else {
												$_min_price  = false;
												$_max_price  = $_min_price;
												$_min_price0 = 0;
											}

											if ( 'math' === $this_price_type ) {
												$_regular_price_filtered          = [ [ $_price ] ];
												$_original_regular_price_filtered = [ [ $_original_regular_price_filtered ] ];
											} else {
												$_regular_price_filtered          = [ [ wc_format_decimal( $_price, false, true ) ] ];
												$_original_regular_price_filtered = [ [ wc_format_decimal( $_original_regular_price_filtered, false, true ) ] ];
											}
										} elseif ( 'multiple' === $element_object->type || 'multipleall' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {
											$_prefix = $current_element . '_';

											$_is_field_required = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );

											$_changes_product_image = $this->get_builder_element( $_prefix . 'changes_product_image', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_replacement_mode      = $this->get_builder_element( $_prefix . 'replacement_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_swatch_position       = $this->get_builder_element( $_prefix . 'swatch_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_use_images            = $this->get_builder_element( $_prefix . 'use_images', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_use_colors            = $this->get_builder_element( $_prefix . 'use_colors', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
											$_use_lightbox          = $this->get_builder_element( $_prefix . 'use_lightbox', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );

											if ( isset( $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) ) {

												$_prices = $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ];
												$_prices = $this->get_builder_element( 'multiple_' . $current_element . '_options_price', $builder, $current_builder, $current_counter, $_prices, $current_element, 'wc_epo_multiple_prices', $element_uniqueid );

												$_original_prices = $_prices;
												$_sale_prices     = $_prices;
												if ( $enable_sales && isset( $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) ) {
													$_sale_prices = $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ];
													$_sale_prices = $this->get_builder_element( 'multiple_' . $current_element . '_sale_prices', $builder, $current_builder, $current_counter, $_sale_prices, $current_element, 'wc_epo_multiple_sale_prices', $element_uniqueid );
												}
												$_prices = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_prices, $_sale_prices );
												$_prices = apply_filters( 'wc_epo_apply_discount', $_prices, $_original_prices, $post_id );

												$_original_prices = apply_filters( 'wc_epo_enable_shortocde', $_original_prices, $_original_prices, $post_id );

												$_values      = $this->get_builder_element( 'multiple_' . $current_element . '_options_value', $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_values', $element_uniqueid );
												$_titles      = $this->get_builder_element( 'multiple_' . $current_element . '_options_title', $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_titles', $element_uniqueid );
												$_images      = $this->get_builder_element( 'multiple_' . $current_element . '_options_image', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
												$_imagesc     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagec', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
												$_imagesp     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagep', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
												$_imagesl     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagel', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
												$_color       = $this->get_builder_element( 'multiple_' . $current_element . '_options_color', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
												$_prices_type = $this->get_builder_element( 'multiple_' . $current_element . '_options_price_type', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );

												if ( ! is_array( $_values ) ) {
													$_values = [ $_values ];
												}
												if ( ! is_array( $_titles ) ) {
													$_titles = [ $_titles ];
												}
												if ( ! is_array( $_images ) ) {
													$_images = [ $_images ];
												}
												if ( ! is_array( $_imagesc ) ) {
													$_imagesc = [ $_imagesc ];
												}
												if ( ! is_array( $_imagesp ) ) {
													$_imagesp = [ $_imagesp ];
												}
												if ( ! is_array( $_imagesl ) ) {
													$_imagesl = [ $_imagesl ];
												}
												if ( ! is_array( $_color ) ) {
													$_color = [ $_color ];
												}
												if ( ! is_array( $_prices_type ) ) {
													$_prices_type = [ $_prices_type ];
												}

												if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
													global $woocommerce_wpml;
													global $sitepress;
													if ( $woocommerce_wpml && isset( $original_product_id ) && isset( $wpml_is_original_product ) ) {

														$basetype     = $price->post_type;
														$translations = THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_translations( THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_trid( $original_product_id, 'post_product' ), 'product' );

														$woocommerce_wpml_currencies = $woocommerce_wpml->settings['currency_options'];

														foreach ( $woocommerce_wpml_currencies as $currency => $currency_data ) {

															$thisbuilder = [];

															if ( ! isset( $currency_data['languages'] ) ) {
																continue;
															}

															foreach ( $currency_data['languages'] as $lang => $is_lang_enabled ) {

																if ( $is_lang_enabled && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {

																	if ( 'product' === $basetype ) {

																		if ( isset( $translations[ $lang ] ) ) {

																			$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $translations[ $lang ]->element_id, 'product' );

																			if ( $this_wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																				$thisbuilder = $builder;
																			} else {
																				$thisbuilder = themecomplete_get_post_meta( $translations[ $lang ]->element_id, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																					if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																						$thisbuilder = $thisbuilder['tmfbuilder'];
																					} else {
																						$thisbuilder = [];
																					}
																				}
																			}
																		}
																	} else {
																		if ( $wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																			$thisbuilder = $builder;
																		} else {
																			$args                 = [
																				'post_type'   => $basetype,
																				'post_status' => [ 'publish', 'draft' ], // get only enabled global extra options.
																				'numberposts' => -1,
																				'orderby'     => 'date',
																				'order'       => 'asc',
																			];
																			$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $lang, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
																			$args['meta_query'][] = [
																				'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
																				'value'   => $original_product_id,
																				'compare' => '=',
																			];
																			$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

																			if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {
																				$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $other_translations[0]->ID, $basetype );
																				$thisbuilder                   = themecomplete_get_post_meta( $other_translations[0]->ID, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																					if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																						$thisbuilder = $thisbuilder['tmfbuilder'];
																					} else {
																						$thisbuilder = [];
																					}
																				}
																			}
																		}
																	}

																	break;
																}
															}

															if ( empty( $thisbuilder ) ) {
																$thisbuilder = $builder;
															}

															if ( $currency !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
																$_current_currency_price      = ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] : '';
																$_current_currency_sale_price = ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] : '';
															} else {
																$_current_currency_price      = isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] : '';
																$_current_currency_sale_price = isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] : '';
															}

															if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ) ) {
																if ( is_array( $_current_currency_price ) ) {
																	foreach ( $_current_currency_price as $_k => $_v ) {
																		if ( '' !== $_v ) {
																			$_current_currency_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																		}
																	}
																}
															}
															if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ) ) {
																if ( is_array( $_current_currency_sale_price ) ) {
																	foreach ( $_current_currency_sale_price as $_k => $_v ) {
																		if ( '' !== $_v ) {
																			$_current_currency_sale_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																		}
																	}
																}
															}

															$_current_currency_price          = apply_filters( 'wc_epo_multiple_prices' . $currency, $_current_currency_price, 'multiple_' . $current_element . '_options_price' . $currency, $element_uniqueid );
															$_current_currency_sale_price     = apply_filters( 'wc_epo_multiple_sale_prices' . $currency, $_current_currency_sale_price, 'multiple_' . $current_element . '_options_sale_price' . $currency, $element_uniqueid );
															$_original_current_currency_price = $_current_currency_price;

															if ( $enable_sales ) {
																$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_current_currency_sale_price );
															}
															$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );

															$price_per_currencies_original[ $currency ] = $_original_current_currency_price;
															if ( ! is_array( $price_per_currencies_original[ $currency ] ) ) {
																$price_per_currencies_original[ $currency ] = [];
															}

															$price_per_currencies[ $currency ] = $_current_currency_price;
															if ( ! is_array( $price_per_currencies[ $currency ] ) ) {
																$price_per_currencies[ $currency ] = [];
															}

															foreach ( $_prices as $_n => $_price ) {
																if ( ! isset( $_prices_type[ $_n ] ) ) {
																	continue;
																}

																$to_price = '';
																if ( is_array( $_original_current_currency_price ) && isset( $_original_current_currency_price[ $_n ] ) ) {
																	$to_price = $_original_current_currency_price[ $_n ];
																}
																if ( 'math' === $_prices_type[ $_n ] ) {
																	$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
																} else {
																	$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
																}

																$to_price = '';
																if ( is_array( $_current_currency_price ) && isset( $_current_currency_price[ $_n ] ) ) {
																	$to_price = $_current_currency_price[ $_n ];
																}
																if ( 'math' === $_prices_type[ $_n ] ) {
																	$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
																} else {
																	$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
																}
															}
														}
													}
												} else {
													foreach ( THEMECOMPLETE_EPO_HELPER()->get_currencies() as $currency ) {
														$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );

														if ( '' === $mt_prefix ) {
															$_current_currency_price          = $_original_prices;
															$_original_current_currency_price = $_original_prices;
															if ( $enable_sales ) {
																$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_prices );
															}
														} else {
															$_current_currency_price          = $this->get_builder_element( 'multiple_' . $current_element . '_options_price' . $mt_prefix, $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_prices' . $mt_prefix, $element_uniqueid );
															$_current_currency_sale_price     = $this->get_builder_element( 'multiple_' . $current_element . '_options_sale_price' . $mt_prefix, $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_sale_prices' . $mt_prefix, $element_uniqueid );
															$_original_current_currency_price = $_current_currency_price;
															if ( $enable_sales ) {
																$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_current_currency_sale_price );
															}
															$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );
														}

														$price_per_currencies_original[ $currency ] = $_original_current_currency_price;
														if ( ! is_array( $price_per_currencies_original[ $currency ] ) ) {
															$price_per_currencies_original[ $currency ] = [];
														}
														$price_per_currencies[ $currency ] = $_current_currency_price;
														if ( ! is_array( $price_per_currencies[ $currency ] ) ) {
															$price_per_currencies[ $currency ] = [];
														}
														foreach ( $_prices as $_n => $_price ) {
															if ( ! isset( $_prices_type[ $_n ] ) ) {
																continue;
															}
															$to_price = '';
															if ( is_array( $_original_current_currency_price ) && isset( $_original_current_currency_price[ $_n ] ) ) {
																$to_price = $_original_current_currency_price[ $_n ];
															}
															if ( 'math' === $_prices_type[ $_n ] ) {
																$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
															} else {
																$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
															}
															$to_price = '';
															if ( is_array( $_current_currency_price ) && isset( $_current_currency_price[ $_n ] ) ) {
																$to_price = $_current_currency_price[ $_n ];
															}
															if ( 'math' === $_prices_type[ $_n ] ) {
																$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
															} else {
																$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
															}
														}
													}
												}

												$mt_prefix                         = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( true, '' );
												$_original_current_currency_prices = $price_per_currencies_original[ $mt_prefix ];
												$_current_currency_prices          = $price_per_currencies[ $mt_prefix ];

												// Backwards compatibility.
												if ( '' === $_replacement_mode ) {
													if ( '' !== $_use_images ) {
														$_replacement_mode = 'image';
													} elseif ( '' !== $_use_colors ) {
														$_replacement_mode = 'color';
													} else {
														$_replacement_mode = 'none';
													}
												}
												if ( '' === $_swatch_position ) {
													switch ( $_replacement_mode ) {
														case 'none':
															$_swatch_position = 'center';
															break;
														case 'image':
															if ( ! empty( $_use_images ) ) {
																switch ( $_use_images ) {
																	case 'images':
																		$_swatch_position = 'center';
																		break;
																	default:
																		$_swatch_position = $_use_images;
																		break;
																}
															}
															break;
														case 'color':
															if ( ! empty( $_use_colors ) ) {
																switch ( $_use_colors ) {
																	case 'color':
																		$_swatch_position = 'center';
																		break;
																	default:
																		$_swatch_position = $_use_colors;
																		break;
																}
															}
															break;
													}
													if ( '' === $_swatch_position ) {
														$_swatch_position = 'center';
													}
												}

												if ( 'images' === $_changes_product_image && 'image' !== $_replacement_mode ) {
													$_imagesp               = $_images;
													$_images                = [];
													$_imagesc               = [];
													$_changes_product_image = 'custom';
												}
												if ( 'image' !== $_replacement_mode ) {
													$_use_lightbox = '';
												}

												$_url         = $this->get_builder_element( 'multiple_' . $current_element . '_options_url', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
												$_description = $this->get_builder_element( 'multiple_' . $current_element . '_options_description', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
												$_enabled     = $this->get_builder_element( 'multiple_' . $current_element . '_options_enabled', $builder, $current_builder, $current_counter, [], $current_element, '1', $element_uniqueid );
												$_fee         = $this->get_builder_element( 'multiple_' . $current_element . '_options_fee', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );

												foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_multiple_options as $__key => $__name ) {
													$_extra_name                             = $__name['name'];
													$_extra_multiple_choices[ $_extra_name ] = $this->get_builder_element( 'multiple_' . $current_element . '_options_' . $_extra_name, $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
												}

												$_values_c  = $_values;
												$_values_ce = $_values;
												$mt_prefix  = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( null, '' );
												$_nn        = 0;
												foreach ( $_prices as $_n => $_price ) {

													if ( isset( $_enabled[ $_n ] ) && ( '0' === $_enabled[ $_n ] || '' === $_enabled[ $_n ] ) ) {
														$_options_all[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = $_titles[ $_n ];
														unset( $_images[ $_n ] );
														unset( $_imagesc[ $_n ] );
														unset( $_imagesp[ $_n ] );
														unset( $_imagesl[ $_n ] );
														unset( $_color[ $_n ] );
														unset( $_url[ $_n ] );
														unset( $_description[ $_n ] );
														unset( $_titles[ $_n ] );
														unset( $_values[ $_n ] );
														unset( $_original_prices[ $_n ] );
														unset( $_prices_type[ $_n ] );
														unset( $_values_ce[ $_n ] );
														if ( isset( $_current_currency_prices ) && is_array( $_current_currency_prices ) ) {
															unset( $_current_currency_prices[ $_n ] );
														}
														if ( isset( $_original_current_currency_prices ) && is_array( $_original_current_currency_prices ) ) {
															unset( $_original_current_currency_prices[ $_n ] );
														}
														if ( isset( $_fee ) && is_array( $_fee ) ) {
															unset( $_fee[ $_n ] );
														}
														unset( $_sale_prices[ $_n ] );

														do_action( 'wc_epo_admin_option_is_disable', $_n );
														$_current_deleted_choices[] = $_n;
														continue;
													}

													if ( ! isset( $_prices_type[ $_n ] ) ) {
														continue;
													}

													// backwards compatibility.
													if ( isset( $_prices_type[ $_n ] ) ) {
														if ( 'fee' === $_prices_type[ $_n ] ) {
															if ( 'checkboxes' === $current_element ) {
																$_fee[ $_n ] = '1';
															} else {
																$_is_price_fee = '1';
															}
															$_prices_type[ $_n ] = '';
														}
													}

													$new_currency = false;
													if ( '' !== $mt_prefix
														&& '' !== $_current_currency_prices
														&& is_array( $_current_currency_prices )
														&& isset( $_current_currency_prices[ $_n ] )
														&& '' !== $_current_currency_prices[ $_n ]
													) {
														$new_currency            = true;
														$_price                  = $_current_currency_prices[ $_n ];
														$_original_prices[ $_n ] = $_original_current_currency_prices[ $_n ];
														$_regular_currencies[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ themecomplete_get_woocommerce_currency() ];
													}

													if ( 'math' === $_prices_type[ $_n ] ) {
														$_f_price = $_price;
													} else {
														$_f_price = wc_format_decimal( $_price, false, true );
													}

													$_regular_price[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $_f_price ];
													$_for_filter_price_type                                        = isset( $_prices_type[ $_n ] ) ? $_prices_type[ $_n ] : '';

													if ( ! $new_currency ) {
														$_price                  = apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type );
														$_original_prices[ $_n ] = apply_filters( 'wc_epo_get_current_currency_price', $_original_prices[ $_n ], $_for_filter_price_type );
													}
													$_price                  = apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
													$_original_prices[ $_n ] = apply_filters( 'wc_epo_price', $_original_prices[ $_n ], $_for_filter_price_type, $post_id );

													if ( 'math' === $_prices_type[ $_n ] ) {
														$_f_price = $_price;
														$_regular_price_filtered[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]           = [ $_price ];
														$_original_regular_price_filtered [ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $_original_prices[ $_n ] ];
													} else {
														$_f_price = wc_format_decimal( $_price, false, true );
														$_regular_price_filtered[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]           = [ $_f_price ];
														$_original_regular_price_filtered [ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $_original_prices[ $_n ], false, true ) ];
													}

													$_regular_price_type[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = isset( $_prices_type[ $_n ] ) ? [ ( $_prices_type[ $_n ] ) ] : [ '' ];
													$_options_all[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]        = $_titles[ $_n ];
													$_options[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]            = $_titles[ $_n ];
													$_values_c[ $_n ]  = $_values[ $_n ] . '_' . $_n;
													$_values_ce[ $_n ] = $_values_c[ $_n ];
													if ( ( ( isset( $_fee[ $_n ] ) && '1' !== $_fee[ $_n ] ) || ! isset( $_fee[ $_n ] ) ) && isset( $_prices_type[ $_n ] ) && '' === $_prices_type[ $_n ] && ( ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) && '' === $builder[ $current_element . '_price_type' ][ $current_counter ] ) || ! isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) ) {
														if ( false !== $_min_price && '' !== $_price ) {
															if ( '' === $_min_price ) {
																$_min_price = $_f_price;
															} else {
																if ( $_min_price > $_f_price ) {
																	$_min_price = $_f_price;
																}
															}
															if ( '' === $_min_price0 ) {
																if ( $_is_field_required ) {
																	$_min_price0 = floatval( $_min_price );
																} else {
																	$_min_price0 = 0;
																}
															} else {
																if ( $_is_field_required && $_min_price0 > floatval( $_min_price ) ) {
																	$_min_price0 = floatval( $_min_price );
																}
															}
															if ( '' === $_min_price10 ) {
																$_min_price10 = floatval( $_min_price );
															} else {
																if ( $_min_price10 > floatval( $_min_price ) ) {
																	$_min_price10 = floatval( $_min_price );
																}
															}
															if ( '' === $_max_price ) {
																$_max_price = $_f_price;
															} else {
																if ( 'checkboxes' === $current_element ) {
																	// needs work for Limit selection/Exact selection/Minimum selection.
																	$_max_price = $_max_price + $_f_price;
																} else {
																	if ( $_max_price < $_f_price ) {
																		$_max_price = $_f_price;
																	}
																}
															}
														} else {
															if ( '' === $_price ) {
																$_min_price0  = 0;
																$_min_price10 = 0;
															}
														}
													} else {
														$_min_price = false;
														$_max_price = $_min_price;
														if ( '' === $_min_price0 ) {
															$_min_price0 = 0;
														} else {
															if ( $_min_price0 > floatval( $_min_price ) ) {
																$_min_price0 = floatval( $_min_price );
															}
														}
														if ( '' === $_min_price10 ) {
															$_min_price10 = 0;
														} else {
															if ( $_min_price10 > floatval( $_min_price ) ) {
																$_min_price10 = floatval( $_min_price );
															}
														}
													}
													$_nn ++;
												}

												$_images          = array_values( $_images );
												$_imagesc         = array_values( $_imagesc );
												$_imagesp         = array_values( $_imagesp );
												$_imagesl         = array_values( $_imagesl );
												$_color           = array_values( $_color );
												$_url             = array_values( $_url );
												$_description     = array_values( $_description );
												$_titles          = array_values( $_titles );
												$_values          = array_values( $_values );
												$_original_prices = array_values( $_original_prices );
												$_prices_type     = array_values( $_prices_type );
												if ( isset( $_current_currency_prices ) && is_array( $_current_currency_prices ) ) {
													$_current_currency_prices = array_values( $_current_currency_prices );
												}
												if ( isset( $_original_current_currency_prices ) && is_array( $_original_current_currency_prices ) ) {
													$_original_current_currency_prices = array_values( $_original_current_currency_prices );
												}
												if ( isset( $_fee ) && is_array( $_fee ) ) {
													$_fee = array_values( $_fee );
												}
												$_sale_prices = array_values( $_sale_prices );
												$_values_c    = array_values( $_values_c );
												$_values_ce   = array_values( $_values_ce );
												$_prices      = array_values( $_prices );

												do_action( 'wc_epo_admin_option_reindex' );
											}
										}
									}
									$default_value = '';
									if ( isset( $builder[ 'multiple_' . $current_element . '_options_default_value' ][ $current_counter ] ) ) {
										$default_value = $builder[ 'multiple_' . $current_element . '_options_default_value' ][ $current_counter ];

										$disabled_count = count(
											array_filter(
												$_current_deleted_choices,
												function ( $n ) use ( $default_value ) {
													return $n <= $default_value;
												}
											)
										);

										if ( is_array( $default_value ) ) {
											foreach ( $default_value as $key => $value ) {
												if ( '' !== $value ) {
													$this_disabled_count   = count(
														array_filter(
															$_current_deleted_choices,
															function ( $n ) use ( $default_value, $value ) {
																return (int) $n < (int) $value && $n <= $default_value;
															}
														)
													);
													$default_value[ $key ] = (string) ( (int) $value - (int) $this_disabled_count );
												}
											}
											if ( 'selectbox' === $current_element && isset( $default_value[ $current_counter ] ) ) {
												$default_value = (string) $default_value[ $current_counter ];
											}
										} else {
											if ( '' !== $default_value ) {
												$default_value = (string) ( (int) $default_value - (int) $disabled_count );
											}
										}
									} elseif ( isset( $builder[ $_prefix . 'default_value' ] ) && isset( $builder[ $_prefix . 'default_value' ][ $current_counter ] ) ) {
										$default_value = (string) $builder[ $_prefix . 'default_value' ][ $current_counter ];
									}
									$default_value = apply_filters( 'wc_epo_enable_shortocde', $default_value, $default_value, $post_id );

									switch ( $current_element ) {

										case 'selectbox':
											$_new_type = 'select';
											if ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) {
												// backwards compatibility.
												$selectbox_fee = $builder[ $current_element . '_price_type' ][ $current_counter ];
												$_is_price_fee = ( 'fee' === $selectbox_fee ) ? '1' : '';

											}

											break;

										case 'selectboxmultiple':
											$_new_type = 'selectmultiple';
											break;

										case 'radiobuttons':
											$_new_type = 'radio';
											break;

										case 'checkboxes':
											$_new_type = 'checkbox';
											break;

									}

									$_rules_type = $_regular_price_type;
									foreach ( $_regular_price_type as $key => $value ) {
										foreach ( $value as $k => $v ) {
											$_regular_price_type[ $key ][ $k ] = $v;
										}
									}

									$_rules          = $_regular_price;
									$_rules_filtered = $_regular_price_filtered;
									foreach ( $_regular_price as $key => $value ) {
										foreach ( $value as $k => $v ) {
											if ( 'math' !== $_regular_price_type[ $key ][ $k ] ) {
												$_regular_price[ $key ][ $k ]          = wc_format_localized_price( $v );
												$_regular_price_filtered[ $key ][ $k ] = wc_format_localized_price( $v );
											}
										}
									}

									if ( 'variations' !== $current_element ) {
										$this->cpf_single_epos_prices[ $element_uniqueid ] = [
											'uniqueid' => $element_uniqueid,
											'required' => $is_required,
											'element'  => $element_no_in_section,
											'section_uniqueid' => $_sections_uniqid,
											'minall'   => floatval( $_min_price10 ),
											'min'      => floatval( $_min_price0 ),
											'max'      => floatval( $_max_price ),
											'clogic'   => $this->get_builder_element( $_prefix . 'clogic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section_clogic' => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'],
											'logic'    => $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section_logic' => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'],
										];
										$epos_prices[]                                     = $this->cpf_single_epos_prices[ $element_uniqueid ];
									}
									if ( false !== $_min_price ) {
										$_min_price = wc_format_localized_price( $_min_price );
									}
									if ( false !== $_max_price ) {
										$_max_price = wc_format_localized_price( $_max_price );
									}

									// Fix for getting right results for dates even if the user enters wrong format.
									$format         = $this->get_builder_element( $_prefix . 'format', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									$data           = $this->get_date_format( $format );
									$date_format    = $data['date_format'];
									$sep            = $data['sep'];
									$disabled_dates = $this->get_builder_element( $_prefix . 'disabled_dates', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									if ( $disabled_dates ) {
										$disabled_dates = explode( ',', $disabled_dates );
										foreach ( $disabled_dates as $key => $value ) {
											if ( ! $value ) {
												continue;
											}
											$value = str_replace( '.', '-', $value );
											$value = str_replace( '/', '-', $value );
											$value = explode( '-', $value );
											if ( count( $value ) !== 3 ) {
												continue;
											}
											switch ( $format ) {
												case '0':
												case '2':
												case '4':
													$value = $value[2] . '-' . $value[1] . '-' . $value[0];
													break;
												case '1':
												case '3':
												case '5':
													$value = $value[2] . '-' . $value[0] . '-' . $value[1];
													break;
											}
											$value_to_date = date_create( $value );
											if ( ! $value_to_date ) {
												continue;
											}
											$value                  = date_format( $value_to_date, $date_format );
											$disabled_dates[ $key ] = $value;
										}
										$disabled_dates = implode( ',', $disabled_dates );

									}
									$enabled_only_dates = $this->get_builder_element( $_prefix . 'enabled_only_dates', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									if ( $enabled_only_dates ) {
										$enabled_only_dates = explode( ',', $enabled_only_dates );
										foreach ( $enabled_only_dates as $key => $value ) {
											if ( ! $value ) {
												continue;
											}
											$value = str_replace( '.', '-', $value );
											$value = str_replace( '/', '-', $value );
											$value = explode( '-', $value );
											if ( count( $value ) !== 3 ) {
												continue;
											}
											switch ( $format ) {
												case '0':
												case '2':
												case '4':
													$value = $value[2] . '-' . $value[1] . '-' . $value[0];
													break;
												case '1':
												case '3':
												case '5':
													$value = $value[2] . '-' . $value[0] . '-' . $value[1];
													break;
											}
											$value_to_date = date_create( $value );
											if ( ! $value_to_date ) {
												continue;
											}
											$value                      = date_format( $value_to_date, $date_format );
											$enabled_only_dates[ $key ] = $value;
										}
										$enabled_only_dates = implode( ',', $enabled_only_dates );
									}

									if ( $is_enabled ) {
										$this->current_option_features[] = $current_element;
									}

									$repeater  = $this->get_builder_element( $_prefix . 'repeater', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									$connector = $this->get_builder_element( $_prefix . 'connector', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									if ( '' !== $connector ) {
										$repeater = '';
									}

									if ( 'header' !== $current_element && 'divider' !== $current_element ) {
										if ( 'variations' === $current_element ) {
											$this->cpf_single_variation_element_id[ $element_uniqueid ] = $this->get_builder_element( $_prefix . 'uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $current_element, '', $element_uniqueid );
											$variation_element_id                                       = $this->cpf_single_variation_element_id[ $element_uniqueid ];
											$this->cpf_single_variation_section_id[ $element_uniqueid ] = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_uniqid'];
											$variation_section_id                                       = $this->cpf_single_variation_section_id[ $element_uniqueid ];
										}
										$product_epos_uniqids[] = $element_uniqueid;
										if ( in_array( $_new_type, [ 'select', 'radio', 'checkbox' ], true ) ) {
											$product_epos_choices[ $element_uniqueid ] = array_keys( $_rules_type );
										}

										$_extra_multiple_choices = ( false !== $_extra_multiple_choices ) ? $_extra_multiple_choices : [];

										$this->cpf_single[ $element_uniqueid ] =
											array_merge(
												THEMECOMPLETE_EPO_BUILDER()->get_custom_properties( $builder, $_prefix, $_counter, $_elements, $k0, $current_builder, $current_counter, $current_element ),
												$_extra_multiple_choices,
												[
													'_'    => THEMECOMPLETE_EPO_BUILDER()->get_default_properties( $builder, $_prefix, $_counter, $_elements, $k0 ),
													'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'builder' => ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ? $current_builder : $builder,
													'original_builder' => $builder,
													'section' => $_sections_uniqid,
													'type' => $_new_type,
													'size' => $_div_size[ $k0 ],

													'include_tax_for_fee_price_type' => $this->get_builder_element( $_prefix . 'include_tax_for_fee_price_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tax_class_for_fee_price_type' => $this->get_builder_element( $_prefix . 'tax_class_for_fee_price_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'hide_element_label_in_cart' => $this->get_builder_element( $_prefix . 'hide_element_label_in_cart', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_value_in_cart' => $this->get_builder_element( $_prefix . 'hide_element_value_in_cart', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_label_in_order' => $this->get_builder_element( $_prefix . 'hide_element_label_in_order', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_value_in_order' => $this->get_builder_element( $_prefix . 'hide_element_value_in_order', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_label_in_floatbox' => $this->get_builder_element( $_prefix . 'hide_element_label_in_floatbox', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_value_in_floatbox' => $this->get_builder_element( $_prefix . 'hide_element_value_in_floatbox', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'enabled' => $is_enabled,
													'required' => $is_required,
													'replacement_mode' => isset( $_replacement_mode ) ? $_replacement_mode : 'none',
													'swatch_position' => isset( $_swatch_position ) ? $_swatch_position : 'center',
													'use_images' => isset( $_use_images ) ? $_use_images : '',
													'use_colors' => isset( $_use_colors ) ? $_use_colors : '',
													'use_lightbox' => $_use_lightbox,
													'use_url' => $this->get_builder_element( $_prefix . 'use_url', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'items_per_row' => $this->get_builder_element( $_prefix . 'items_per_row', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'items_per_row_r' => [
														'desktop'        => $this->get_builder_element( $_prefix . 'items_per_row', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'tablets_galaxy' => $this->get_builder_element( $_prefix . 'items_per_row_tablets_galaxy', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'tablets'        => $this->get_builder_element( $_prefix . 'items_per_row_tablets', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'tablets_small'  => $this->get_builder_element( $_prefix . 'items_per_row_tablets_small', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'iphone6_plus'   => $this->get_builder_element( $_prefix . 'items_per_row_iphone6_plus', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'iphone6'        => $this->get_builder_element( $_prefix . 'items_per_row_iphone6', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'galaxy'         => $this->get_builder_element( $_prefix . 'items_per_row_samsung_galaxy', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'iphone5'        => $this->get_builder_element( $_prefix . 'items_per_row_iphone5', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'smartphones'    => $this->get_builder_element( $_prefix . 'items_per_row_smartphones', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													],

													'label_size' => $this->get_builder_element( $_prefix . 'header_size', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'label' => $this->get_builder_element( $_prefix . 'header_title', $builder, $current_builder, $current_counter, '', $current_element, 'wc_epo_label', $element_uniqueid ),
													'label_position' => $this->get_builder_element( $_prefix . 'header_title_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'label_color' => $this->get_builder_element( $_prefix . 'header_title_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'description' => $this->get_builder_element( $_prefix . 'header_subtitle', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'description_position' => $this->get_builder_element( $_prefix . 'header_subtitle_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'description_color' => $this->get_builder_element( $_prefix . 'header_subtitle_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'divider_type' => $this->get_builder_element( $_prefix . 'divider_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'placeholder' => $this->get_builder_element( $_prefix . 'placeholder', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'min_chars' => $this->get_builder_element( $_prefix . 'min_chars', $builder, $current_builder, $current_counter, false, $current_element, 'wc_epo_global_min_chars', $element_uniqueid ),
													'max_chars' => $this->get_builder_element( $_prefix . 'max_chars', $builder, $current_builder, $current_counter, false, $current_element, 'wc_epo_global_max_chars', $element_uniqueid ),
													'hide_amount' => $this->get_builder_element( $_prefix . 'hide_amount', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'text_before_price' => $this->get_builder_element( $_prefix . 'text_before_price', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'text_after_price' => $this->get_builder_element( $_prefix . 'text_after_price', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'options' => $_options,
													'options_all' => $_options_all,
													'min_price' => $_min_price,
													'max_price' => $_max_price,
													'rules' => $_rules,
													'price_rules' => $_regular_price,
													'rules_filtered' => $_rules_filtered,
													'price_rules_filtered' => $_regular_price_filtered,
													'original_rules_filtered' => $_original_regular_price_filtered,
													'price_rules_type' => $_regular_price_type,
													'rules_type' => $_rules_type,
													'currencies' => $_regular_currencies,
													'price_per_currencies' => $price_per_currencies,
													'lookuptable' => $this->get_builder_element( $_prefix . 'lookuptable', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'lookuptable_x' => $this->get_builder_element( $_prefix . 'lookuptable_x', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'lookuptable_y' => $this->get_builder_element( $_prefix . 'lookuptable_y', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'images' => isset( $_images ) ? $_images : '',
													'imagesc' => isset( $_imagesc ) ? $_imagesc : '',
													'imagesp' => isset( $_imagesp ) ? $_imagesp : '',
													'imagesl' => isset( $_imagesl ) ? $_imagesl : '',
													'color' => isset( $_color ) ? $_color : '',
													'url'  => isset( $_url ) ? $_url : '',

													'cdescription' => ( false !== $_description ) ? $_description : '',
													'extra_multiple_choices' => ( false !== $_extra_multiple_choices ) ? $_extra_multiple_choices : [],
													'limit' => $this->get_builder_element( $_prefix . 'limit_choices', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'exactlimit' => $this->get_builder_element( $_prefix . 'exactlimit_choices', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'minimumlimit' => $this->get_builder_element( $_prefix . 'minimumlimit_choices', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'clear_options' => $this->get_builder_element( $_prefix . 'clear_options', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'option_values_all' => isset( $_values_c ) ? $_values_c : [],
													'option_values' => isset( $_values_ce ) ? $_values_ce : [],
													'button_type' => $this->get_builder_element( $_prefix . 'button_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'uniqid' => $element_uniqueid,
													'clogic' => $this->get_builder_element( $_prefix . 'clogic', $builder, $current_builder, $current_counter, false, $current_element, '', $element_uniqueid ),
													'logic' => $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'format' => $format,
													'start_year' => $this->get_builder_element( $_prefix . 'start_year', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'end_year' => $this->get_builder_element( $_prefix . 'end_year', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'min_date' => $this->get_builder_element( $_prefix . 'min_date', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'max_date' => $this->get_builder_element( $_prefix . 'max_date', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disabled_dates' => $disabled_dates,
													'enabled_only_dates' => $enabled_only_dates,
													'exlude_disabled' => $this->get_builder_element( $_prefix . 'exlude_disabled', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disabled_weekdays' => $this->get_builder_element( $_prefix . 'disabled_weekdays', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disabled_months' => $this->get_builder_element( $_prefix . 'disabled_months', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'time_format' => $this->get_builder_element( $_prefix . 'time_format', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'custom_time_format' => $this->get_builder_element( $_prefix . 'custom_time_format', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'min_time' => $this->get_builder_element( $_prefix . 'min_time', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'max_time' => $this->get_builder_element( $_prefix . 'max_time', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'showhour' => $this->get_builder_element( $_prefix . 'showhour', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'showminute' => $this->get_builder_element( $_prefix . 'showminute', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'showsecond' => $this->get_builder_element( $_prefix . 'showsecond', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tranlation_hour' => $this->get_builder_element( $_prefix . 'tranlation_hour', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tranlation_minute' => $this->get_builder_element( $_prefix . 'tranlation_minute', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tranlation_second' => $this->get_builder_element( $_prefix . 'tranlation_second', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'theme' => $this->get_builder_element( $_prefix . 'theme', $builder, $current_builder, $current_counter, 'epo', $current_element, '', $element_uniqueid ),
													'theme_size' => $this->get_builder_element( $_prefix . 'theme_size', $builder, $current_builder, $current_counter, 'medium', $current_element, '', $element_uniqueid ),
													'theme_position' => $this->get_builder_element( $_prefix . 'theme_position', $builder, $current_builder, $current_counter, 'normal', $current_element, '', $element_uniqueid ),

													'tranlation_day' => $this->get_builder_element( $_prefix . 'tranlation_day', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tranlation_month' => $this->get_builder_element( $_prefix . 'tranlation_month', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tranlation_year' => $this->get_builder_element( $_prefix . 'tranlation_year', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'default_value' => $default_value,

													'is_cart_fee' => '1' === $_is_price_fee,
													'is_cart_fee_multiple' => isset( $_fee ) ? $_fee : [],
													'class' => $this->get_builder_element( $_prefix . 'class', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'container_id' => $this->get_builder_element( $_prefix . 'container_id', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'show_tooltip' => $show_tooltip,
													'changes_product_image' => isset( $_changes_product_image ) ? $_changes_product_image : '',
													'min'  => $this->get_builder_element( $_prefix . 'min', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'max'  => $this->get_builder_element( $_prefix . 'max', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'freechars' => $this->get_builder_element( $_prefix . 'freechars', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'step' => $this->get_builder_element( $_prefix . 'step', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'pips' => $this->get_builder_element( $_prefix . 'pips', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'noofpips' => $this->get_builder_element( $_prefix . 'noofpips', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'show_picker_value' => $this->get_builder_element( $_prefix . 'show_picker_value', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'quantity' => $this->get_builder_element( $_prefix . 'quantity', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_min' => $this->get_builder_element( $_prefix . 'quantity_min', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_max' => $this->get_builder_element( $_prefix . 'quantity_max', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_step' => $this->get_builder_element( $_prefix . 'quantity_step', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_default_value' => $this->get_builder_element( $_prefix . 'quantity_default_value', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'mode' => $this->get_builder_element( $_prefix . 'mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'layout_mode' => $this->get_builder_element( $_prefix . 'layout_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'categoryids' => $this->get_builder_element( $_prefix . 'categoryids', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'productids' => $this->get_builder_element( $_prefix . 'productids', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'priced_individually' => $this->get_builder_element( $_prefix . 'priced_individually', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'order' => $this->get_builder_element( $_prefix . 'order', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'orderby' => $this->get_builder_element( $_prefix . 'orderby', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disable_epo' => $this->get_builder_element( $_prefix . 'disable_epo', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'shipped_individually' => $this->get_builder_element( $_prefix . 'shipped_individually', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'maintain_weight' => $this->get_builder_element( $_prefix . 'maintain_weight', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'discount' => $this->get_builder_element( $_prefix . 'discount', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'discount_type' => $this->get_builder_element( $_prefix . 'discount_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'discount_exclude_addons' => $this->get_builder_element( $_prefix . 'discount_exclude_addons', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hiddenin' => $this->get_builder_element( $_prefix . 'hiddenin', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'show_title' => $this->get_builder_element( $_prefix . 'show_title', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_price' => $this->get_builder_element( $_prefix . 'show_price', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_description' => $this->get_builder_element( $_prefix . 'show_description', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_meta' => $this->get_builder_element( $_prefix . 'show_meta', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_image' => $this->get_builder_element( $_prefix . 'show_image', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),

													'repeater' => $repeater,
													'repeater_quantity' => $this->get_builder_element( $_prefix . 'repeater_quantity', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'repeater_min_rows' => $this->get_builder_element( $_prefix . 'repeater_min_rows', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'repeater_max_rows' => $this->get_builder_element( $_prefix . 'repeater_max_rows', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'repeater_button_label' => $this->get_builder_element( $_prefix . 'repeater_button_label', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'connector' => $connector,

													'validation1' => $this->get_builder_element( $_prefix . 'validation1', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
												]
											);
										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];

									} elseif ( 'header' === $current_element ) {

										$product_epos_uniqids[] = $element_uniqueid;

										$this->cpf_single[ $element_uniqueid ] = [
											'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section'      => $_sections_uniqid,
											'type'         => $_new_type,
											'size'         => $_div_size[ $k0 ],
											'required'     => '',
											'enabled'      => $is_enabled,
											'replacement_mode' => 'none',
											'swatch_position' => 'center',
											'use_images'   => '',
											'use_colors'   => '',
											'use_url'      => '',
											'items_per_row' => '',
											'label_size'   => $this->get_builder_element( $_prefix . 'header_size', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'label'        => $this->get_builder_element( $_prefix . 'header_title', $builder, $current_builder, $current_counter, '', $current_element, 'wc_epo_label', $element_uniqueid ),
											'label_position' => $this->get_builder_element( $_prefix . 'header_title_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'label_color'  => $this->get_builder_element( $_prefix . 'header_title_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'description'  => $this->get_builder_element( $_prefix . 'header_subtitle', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'description_color' => $this->get_builder_element( $_prefix . 'header_subtitle_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'description_position' => $this->get_builder_element( $_prefix . 'header_subtitle_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'divider_type' => '',
											'placeholder'  => '',
											'max_chars'    => '',
											'hide_amount'  => '',
											'options'      => $_options,
											'options_all'  => $_options_all,
											'min_price'    => $_min_price,
											'max_price'    => $_max_price,
											'rules'        => $_rules,
											'price_rules'  => $_regular_price,
											'rules_filtered' => $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type' => $_regular_price_type,
											'rules_type'   => $_rules_type,
											'images'       => '',
											'limit'        => '',
											'exactlimit'   => '',
											'minimumlimit' => '',
											'option_values' => [],
											'button_type'  => '',
											'class'        => $this->get_builder_element( 'header_class', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'uniqid'       => $this->get_builder_element( 'header_uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $current_element, '', $element_uniqueid ),
											'clogic'       => $this->get_builder_element( 'header_clogic', $builder, $current_builder, $current_counter, false, $current_element, '', $element_uniqueid ),
											'logic'        => $this->get_builder_element( 'header_logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'format'       => '',
											'start_year'   => '',
											'end_year'     => '',
											'tranlation_day' => '',
											'tranlation_month' => '',
											'tranlation_year' => '',
											'show_tooltip' => '',
											'changes_product_image' => '',
											'min'          => '',
											'max'          => '',
											'step'         => '',
											'pips'         => '',

										];
										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];

									} elseif ( 'divider' === $current_element ) {

										$product_epos_uniqids[] = $element_uniqueid;

										$this->cpf_single[ $element_uniqueid ]                                 = [
											'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section'      => $_sections_uniqid,
											'type'         => $_new_type,
											'size'         => $_div_size[ $k0 ],
											'required'     => '',
											'enabled'      => $is_enabled,
											'replacement_mode' => 'none',
											'swatch_position' => 'center',
											'use_images'   => '',
											'use_colors'   => '',
											'use_url'      => '',
											'items_per_row' => '',
											'label_size'   => '',
											'label'        => '',
											'label_color'  => '',
											'label_position' => '',
											'description'  => '',
											'description_color' => '',
											'divider_type' => $this->get_builder_element( $_prefix . 'divider_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'placeholder'  => '',
											'max_chars'    => '',
											'hide_amount'  => '',
											'options'      => $_options,
											'options_all'  => $_options_all,
											'min_price'    => $_min_price,
											'max_price'    => $_max_price,
											'rules'        => $_rules,
											'price_rules'  => $_regular_price,
											'rules_filtered' => $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type' => $_regular_price_type,
											'rules_type'   => $_rules_type,
											'images'       => '',
											'limit'        => '',
											'exactlimit'   => '',
											'minimumlimit' => '',
											'option_values' => [],
											'button_type'  => '',
											'class'        => $this->get_builder_element( 'divider_class', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'uniqid'       => $this->get_builder_element( 'divider_uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $current_element, '', $element_uniqueid ),
											'clogic'       => $this->get_builder_element( 'divider_clogic', $builder, $current_builder, $current_counter, false, $current_element, '', $element_uniqueid ),
											'logic'        => $this->get_builder_element( 'divider_logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'format'       => '',
											'start_year'   => '',
											'end_year'     => '',
											'tranlation_day' => '',
											'tranlation_month' => '',
											'tranlation_year' => '',
											'show_tooltip' => '',
											'changes_product_image' => '',
											'min'          => '',
											'max'          => '',
											'step'         => '',
											'pips'         => '',
										];
										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];
									}
								}
							}

							$_helper_counter = $_helper_counter + $_sections[ $_s ];

							if ( $post_id !== $original_product_id ) {

								if ( is_array( $tm_meta_product_ids ) ) {
									foreach ( $variations_for_conditional_logic as $variation_id ) {
										if ( in_array( $variation_id, $tm_meta_product_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
											$extra_logic            = [];
											$extra_logic['section'] = $_sections_uniqid;
											$extra_logic['toggle']  = 'show';
											$extra_logic['what']    = 'any';
											$extra_logic['rules']   = [];
											$rule                   = [];
											$rule['section']        = $variation_section_id; // this will be addeed correctly later.
											$rule['element']        = 0;
											$rule['operator']       = 'is';
											$rule['value']          = floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $variation_id, 'product', null, 'product_variation' ) );
											$extra_logic['rules'][] = $rule;

											$extra_section_logic[] = [
												'priority' => $priority,
												'tmcp_id'  => $tmcp_id,
												'_s'       => $_s,
												'extra_logic' => $extra_logic,
											];

										}
									}
								}

								if ( is_array( $tm_meta_product_exclude_ids ) ) {
									foreach ( $variations_for_conditional_logic as $variation_id ) {
										if ( in_array( $variation_id, $tm_meta_product_exclude_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
											$extra_hide_logic            = [];
											$extra_hide_logic['section'] = $_sections_uniqid;
											$extra_hide_logic['toggle']  = 'hide';
											$extra_hide_logic['what']    = 'any';
											$extra_hide_logic['rules']   = [];
											$rule                        = [];
											$rule['section']             = $variation_section_id; // this will be addeed correctly later.
											$rule['element']             = 0;
											$rule['operator']            = 'is';
											$rule['value']               = floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $variation_id, 'product', null, 'product_variation' ) );
											$extra_hide_logic['rules'][] = $rule;

											$extra_section_hide_logic[] = [
												'priority' => $priority,
												'tmcp_id'  => $tmcp_id,
												'_s'       => $_s,
												'extra_hide_logic' => $extra_hide_logic,
											];

										}
									}
								}
							}
						}
					}
				}
			}
		}

		if ( $variation_section_id ) {
			foreach ( $extra_section_logic as $section_logic ) {
				$section_logic['extra_logic']['rules'][0]['section'] = $variation_section_id;
				$priority = $section_logic['priority'];
				$tmcp_id  = $section_logic['tmcp_id'];
				$_s       = $section_logic['_s'];

				if ( ! empty( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'] ) ) {
					// If the secion already has logic that logic must be changed
					// to ANY in order to accomodate the adding the variations
					// This means that if the current logic is set to ALL then
					// you will get wrong results. This is a limiations of the
					// current conditional logic system.
					$current_section_logic = json_decode( stripslashes_deep( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] ) );
					if ( is_object( $current_section_logic ) ) {
						$current_section_logic->what    = 'any';
						$current_section_logic->rules[] = $section_logic['extra_logic']['rules'][0];
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] = wp_json_encode( $current_section_logic );
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic']  = '1';
					}
				} else {
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] = wp_json_encode( $section_logic['extra_logic'] );
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic']  = '1';
				}
			}

			foreach ( $extra_section_hide_logic as $section_logic ) {
				$section_logic['extra_hide_logic']['rules'][0]['section'] = $variation_section_id;
				$priority = $section_logic['priority'];
				$tmcp_id  = $section_logic['tmcp_id'];
				$_s       = $section_logic['_s'];

				if ( ! empty( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'] ) ) {
					// If the secion already has logic that logic must be changed
					// to ANY in order to accomodate the adding the variations
					// This means that if the current logic is set to ALL then
					// you will get wrong results. This is a limiations of the
					// current conditional logic system.
					$current_section_logic = json_decode( stripslashes_deep( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] ) );
					if ( is_object( $current_section_logic ) ) {
						$current_section_logic->what    = 'any';
						$current_section_logic->rules[] = $section_logic['extra_hide_logic']['rules'][0];
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] = wp_json_encode( $current_section_logic );
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic']  = '1';
					}
				} else {
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'] = wp_json_encode( $section_logic['extra_hide_logic'] );
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic']  = '1';
				}
			}

			if ( empty( $extra_section_logic )
				&& 1 === count( $global_epos )
				&& isset( $global_epos[1000] )
				&& isset( $global_epos[1000][ $post_id ] )
				&& isset( $global_epos[1000][ $post_id ]['sections'] )
				&& 1 === count( $global_epos[1000][ $post_id ]['sections'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['total_elements'] )
				&& (int) 1 === (int) $global_epos[1000][ $post_id ]['sections'][0]['total_elements']
				&& 1 === count( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['type'] )
				&& 'variations' === $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['type']
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] )
				&& (int) 1 === (int) $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled']
			) {
				$global_epos = [];
			}

			if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
				if ( empty( $extra_section_logic )
					&& 1 === count( $global_epos )
					&& isset( $global_epos[1000] )
					&& isset( $global_epos[1000][ $post_original_id ] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'] )
					&& 1 === count( $global_epos[1000][ $post_original_id ]['sections'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['total_elements'] )
					&& (int) 1 === (int) $global_epos[1000][ $post_original_id ]['sections'][0]['total_elements']
					&& 1 === count( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['type'] )
					&& 'variations' === $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['type']
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] )
					&& (int) 1 === (int) $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled']
				) {
					$global_epos = [];
				}
			}
		}

		$variations_disabled       = false;
		$isset_variations_disabled = ( isset( $global_epos[1000] )
									&& isset( $global_epos[1000] )
									&& isset( $global_epos[1000][ $post_id ] )
									&& isset( $global_epos[1000][ $post_id ]['sections'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] ) );

		if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
			$isset_variations_disabled = ( isset( $global_epos[1000] )
				&& isset( $global_epos[1000] )
				&& isset( $global_epos[1000][ $post_original_id ] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder'] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] ) );
		}
		if ( $isset_variations_disabled ) {
			$variations_disabled = ( isset( $global_epos[1000] )
									&& isset( $global_epos[1000] )
									&& isset( $global_epos[1000][ $post_id ] )
									&& isset( $global_epos[1000][ $post_id ]['sections'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] )
									&& (int) 1 === (int) $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] );

			if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
				$variations_disabled = ( isset( $global_epos[1000] )
					&& isset( $global_epos[1000] )
					&& isset( $global_epos[1000][ $post_original_id ] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] )
					&& (int) 1 === (int) $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] );
			}
		}

		if ( $not_isset_global_post ) {
			unset( $GLOBALS['post'] );
		}

		return [
			'global'               => $global_epos,
			'price'                => $epos_prices,
			'variation_element_id' => $variation_element_id,
			'variation_section_id' => $variation_section_id,
			'variations_disabled'  => $variations_disabled,
			'raw_epos'             => $raw_epos,
			'product_epos_uniqids' => $product_epos_uniqids,
			'product_epos_choices' => $product_epos_choices,
		];
	}

	/**
	 * Return date format data for the date element
	 *
	 * @param string $format The format code.
	 *
	 * @since 6.1
	 * @return array
	 */
	public function get_date_format( $format = '0' ) {

		$date_format         = 'd/m/Y';
		$sep                 = '/';
		$element_date_format = 'dd/mm/yy';
		$date_placeholder    = 'dd/mm/yyyy';
		$date_mask           = '00/00/0000';

		switch ( $format ) {
			case '0':
				$date_format         = 'd/m/Y';
				$sep                 = '/';
				$element_date_format = 'dd/mm/yy';
				$date_placeholder    = 'dd/mm/yyyy';
				$date_mask           = '00/00/0000';
				break;
			case '1':
				$date_format         = 'm/d/Y';
				$sep                 = '/';
				$element_date_format = 'mm/dd/yy';
				$date_placeholder    = 'mm/dd/yyyy';
				$date_mask           = '00/00/0000';
				break;
			case '2':
				$date_format         = 'd.m.Y';
				$sep                 = '.';
				$element_date_format = 'dd.mm.yy';
				$date_placeholder    = 'dd.mm.yyyy';
				$date_mask           = '00.00.0000';
				break;
			case '3':
				$date_format         = 'm.d.Y';
				$sep                 = '.';
				$element_date_format = 'mm.dd.yy';
				$date_placeholder    = 'mm.dd.yyyy';
				$date_mask           = '00.00.0000';
				break;
			case '4':
				$date_format         = 'd-m-Y';
				$sep                 = '-';
				$element_date_format = 'dd-mm-yy';
				$date_placeholder    = 'dd-mm-yyyy';
				$date_mask           = '00-00-0000';
				break;
			case '5':
				$date_format         = 'm-d-Y';
				$sep                 = '-';
				$element_date_format = 'mm-dd-yy';
				$date_placeholder    = 'mm-dd-yyyy';
				$date_mask           = '00-00-0000';
				break;

			case '6':
				$date_format         = 'Y/m/d';
				$sep                 = '/';
				$element_date_format = 'yy/mm/dd';
				$date_placeholder    = 'yyyy/mm/dd';
				$date_mask           = '0000/00/00';
				break;
			case '7':
				$date_format         = 'Y/d/m';
				$sep                 = '/';
				$element_date_format = 'yy/dd/mm';
				$date_placeholder    = 'yyyy/dd/mm';
				$date_mask           = '0000/00/00';
				break;
			case '8':
				$date_format         = 'Y.m.d';
				$sep                 = '.';
				$element_date_format = 'yy.mm.dd';
				$date_placeholder    = 'yyyy.mm.dd';
				$date_mask           = '0000.00.00';
				break;
			case '9':
				$date_format         = 'Y.d.m';
				$sep                 = '.';
				$element_date_format = 'yy.dd.mm';
				$date_placeholder    = 'yyyy.dd.mm';
				$date_mask           = '0000.00.00';
				break;
			case '10':
				$date_format         = 'Y-m-d';
				$sep                 = '-';
				$element_date_format = 'yy-mm-dd';
				$date_placeholder    = 'yyyyy-mm-dd';
				$date_mask           = '0000-00-00';
				break;
			case '11':
				$date_format         = 'Y-d-m';
				$sep                 = '-';
				$element_date_format = 'yy-dd-mm';
				$date_placeholder    = 'yyyy-dd-mm';
				$date_mask           = '0000-00-00';
				break;
		}

		$data = [
			'date_format'         => $date_format,
			'sep'                 => $sep,
			'element_date_format' => $element_date_format,
			'date_placeholder'    => $date_placeholder,
			'date_mask'           => $date_mask,
		];

		return $data;

	}

	/**
	 * Translate $attributes to post names
	 *
	 * @param array  $attributes Element option choices.
	 * @param array  $type element type.
	 * @param string $field_loop Field loop.
	 * @param string $form_prefix should be passed with _ if not empty.
	 * @param string $name_prefix Name prefix.
	 * @param array  $element The element array.
	 *
	 * @return array
	 */
	public function get_post_names( $attributes, $type, $field_loop = '', $form_prefix = '', $name_prefix = '', $element = [] ) {

		$fields = [];
		$loop   = 0;

		$element_object = $this->tm_builder_elements[ $type ];
		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $attribute ) {
				$name_inc = '';
				if ( ! empty( $element_object->post_name_prefix ) ) {
					if ( 'multiple' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {
						$name_inc = 'tmcp_' . $name_prefix . $element_object->post_name_prefix . '_' . $field_loop . $form_prefix;
					} elseif ( 'multipleall' === $element_object->type ) {
						$name_inc = 'tmcp_' . $name_prefix . $element_object->post_name_prefix . '_' . $field_loop . '_' . $loop . $form_prefix;
					}
				}
				$fields[] = $name_inc;
				$loop ++;
			}
		} else {
			if ( ! empty( $element_object->type ) && ! empty( $element_object->post_name_prefix ) ) {
				$name_inc = 'tmcp_' . $name_prefix . $element_object->post_name_prefix . '_' . $field_loop . $form_prefix;
				if ( isset( $element['mode'] ) && 'product' !== $element['mode'] && isset( $element['type'] ) && 'product' === $element['type'] && isset( $element['layout_mode'] ) && ( 'checkbox' === $element['layout_mode'] || 'thumbnailmultiple' === $element['layout_mode'] ) ) {
					$name_inc = $name_inc . '_*';
				}
			}

			if ( ! empty( $name_inc ) ) {
				$fields[] = $name_inc;
			}
		}

		return $fields;

	}

	/**
	 * Get posted variations id
	 *
	 * @param string $form_prefix The form prefix.
	 *
	 * @return null
	 */
	public function get_posted_variation_id( $form_prefix = '' ) {

		$variation_id = null;
		if ( isset( $_REQUEST[ 'variation_id' . $form_prefix ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$variation_id = wp_unslash( $_REQUEST[ 'variation_id' . $form_prefix ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		return $variation_id;

	}

	/**
	 * Append name_inc functions (required for condition logic to check if an element is visible)
	 *
	 * @param integer $post_id The product id.
	 * @param array   $global_epos The global options array.
	 * @param array   $product_epos The normal options array.
	 * @param string  $form_prefix The form prefix.
	 * @param string  $add_identifier The identifier (currently not used).
	 *
	 * @return array
	 */
	public function tm_fill_element_names( $post_id = 0, $global_epos = [], $product_epos = [], $form_prefix = '', $add_identifier = '' ) {

		$global_price_array = $global_epos;
		$local_price_array  = $product_epos;

		$global_prices = [
			'before' => [],
			'after'  => [],
		];
		foreach ( $global_price_array as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[ $section['sections_placement'] ][ $priority ][ $pid ]['sections'][ $section_id ] = $section;
						}
					}
				}
			}
		}
		$unit_counter    = 0;
		$field_counter   = 0;
		$element_counter = 0;
		$connectors      = [];

		// global options before local.
		foreach ( $global_prices['before'] as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				$args    = [
					'priority'        => $priority,
					'pid'             => $pid,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
					'connectors'      => $connectors,
				];
				$_return = $this->fill_builder_display( $global_epos, $field, 'before', $args, $form_prefix, $add_identifier );

				$global_epos     = $_return['global_epos'];
				$unit_counter    = $_return['unit_counter'];
				$field_counter   = $_return['field_counter'];
				$element_counter = $_return['element_counter'];
				$connectors      = $_return['connectors'];

			}
		}

		// normal (local) options.
		if ( is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {
			$attributes = themecomplete_get_attributes( $post_id );
			if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
				foreach ( $local_price_array['product_epos'] as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[ $field['name'] ] ) && ! $attributes[ $field['name'] ]['is_variation'] ) {
						$attribute     = $attributes[ $field['name'] ];
						$field_counter = 0;
						if ( $attribute['is_taxonomy'] ) {
							switch ( $field['type'] ) {
								case 'select':
									$element_counter ++;
									break;
								case 'radio':
								case 'checkbox':
									$element_counter ++;
									break;
							}
						} else {
							switch ( $field['type'] ) {
								case 'select':
									$element_counter ++;
									break;
								case 'radio':
								case 'checkbox':
									$element_counter ++;
									break;
							}
						}
						$unit_counter ++;
					}
				}
			}
		}

		// global options after normal (local).
		foreach ( $global_prices['after'] as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				$args    = [
					'priority'        => $priority,
					'pid'             => $pid,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
					'connectors'      => $connectors,
				];
				$_return = $this->fill_builder_display( $global_epos, $field, 'after', $args, $form_prefix, $add_identifier );

				$global_epos     = $_return['global_epos'];
				$unit_counter    = $_return['unit_counter'];
				$field_counter   = $_return['field_counter'];
				$element_counter = $_return['element_counter'];
				$connectors      = $_return['connectors'];

			}
		}

		return $global_epos;

	}

	/**
	 * Generates correct html names for the builder fields
	 *
	 * @param array  $global_epos The global options array.
	 * @param array  $field The element field array.
	 * @param string $where Placement of the section 'before' or 'after'.
	 * @param array  $args Array of arguments.
	 * @param string $form_prefix The form prefix (shoud be passed with _ if not empty).
	 * @param string $add_identifier The identifier (currently not used).
	 *
	 * @return array
	 */
	public function fill_builder_display( $global_epos, $field, $where, $args, $form_prefix = '', $add_identifier = '' ) {

		$priority        = $args['priority'];
		$pid             = $args['pid'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];
		$connectors      = $args['connectors'];

		$element_type_counter = [];
		$cart_fee_name        = $this->cart_fee_name;

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
			foreach ( $field['sections'] as $_s => $section ) {
				if ( ! isset( $section['sections_placement'] ) || $section['sections_placement'] !== $where ) {
					continue;
				}
				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					foreach ( $section['elements'] as $arr_element_counter => $element ) {

						$is_enabled = isset( $element['enabled'] ) ? $element['enabled'] : 2;
						// Currently $no_disabled is disabled by default
						// to allow the conditional logic
						// to work correctly when there is a disabled element.
						if ( '' === $is_enabled || '0' === $is_enabled ) {
							continue;
						}
						$field_counter = 0;

						if ( ! empty( $add_identifier ) ) {
							$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['add_identifier'] = $add_identifier;
						}
						if ( isset( $this->tm_builder_elements[ $element['type'] ] ) && 'post' === $this->tm_builder_elements[ $element['type'] ]->is_post ) {

							$element_object = $this->tm_builder_elements[ $element['type'] ];

							$c_element_counter = $element_counter;
							if ( isset( $element['connector'] ) && isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
								$c_element_counter = $connectors[ 'c-' . sanitize_key( $element['connector'] ) ];
							}

							if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

								if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
									$element_type_counter[ $element['type'] ] = 0;
								}

								$name_inc      = $element_object->post_name_prefix . '_' . $c_element_counter;
								$base_name_inc = $name_inc;

								$is_cart_fee = ! empty( $element['is_cart_fee'] );
								if ( $is_cart_fee ) {
									$name_inc = $cart_fee_name . $name_inc;
								}

								$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, false, false, $element_type_counter[ $element['type'] ] );

								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc']        = $name_inc;
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc_prefix'] = ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '';

								$name_inc = 'tmcp_' . $name_inc . ( ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '' );
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['name_inc']    = $name_inc;
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_cart_fee'] = $is_cart_fee;

								$global_epos = apply_filters( 'global_epos_fill_builder_display', $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, false, false, false );

								$element_type_counter[ $element['type'] ] ++;
							} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

								$choice_counter = 0;

								if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
									$element_type_counter[ $element['type'] ] = 0;
								}

								foreach ( $element['options'] as $value => $label ) {

									if ( 'multipleall' === $element_object->type ) {
										$name_inc = $element_object->post_name_prefix . '_' . $c_element_counter . '_' . $field_counter;
									} else {
										$name_inc = $element_object->post_name_prefix . '_' . $c_element_counter;
									}

									$base_name_inc = $name_inc;

									if ( 'checkbox' === $element['type'] ) {
										$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $field_counter ] );
									} else {
										$is_cart_fee = ! empty( $element['is_cart_fee'] );
									}
									if ( $is_cart_fee ) {
										$name_inc = $cart_fee_name . $name_inc;
									}

									$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc'][ $field_counter ]        = $name_inc;
									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc_prefix'][ $field_counter ] = ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '';

									$name_inc = 'tmcp_' . $name_inc . ( ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '' );
									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['name_inc'][] = $name_inc;

									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_cart_fee_multiple'][ $field_counter ] = $is_cart_fee;

									$global_epos = apply_filters( 'global_epos_fill_builder_display', $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

									$choice_counter ++;

									$field_counter ++;

								}

								$element_type_counter[ $element['type'] ] ++;

							}
							if ( isset( $element['connector'] ) && '' !== $element['connector'] ) {
								if ( ! isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
									$element_counter ++;
								}
								$connectors[ 'c-' . sanitize_key( $element['connector'] ) ] = $c_element_counter;
							} else {
								$element_counter ++;
							}
						}
					}
				}
			}
			$unit_counter ++;
		}

		return [
			'global_epos'     => $global_epos,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'connectors'      => $connectors,
		];

	}

}

define( 'THEMECOMPLETE_EPO_INCLUDED', 1 );
