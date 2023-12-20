<?php



add_filter( 'edd_checkout_button_purchase', 'seedprod_pro_edd_override_purchase_button', 10 );

/**
 * Add class to EDD Purchase Button.
 *
 * @param string $button EDD Purchase Button.
 * @return string $button EDD Purchase Button.
 */
function seedprod_pro_edd_override_purchase_button( $button ) {
	$button = str_replace( 'edd-submit', 'edd-submit sp-button', $button );
	return $button;
}

/**
 * Render EDD Downloads Grid Shortcode for Builder Preview.
 */
function seedprod_pro_render_shortcode_edd_downloads_grid() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$category         = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
		$exclude_category = isset( $_GET['exclude_category'] ) ? sanitize_text_field( wp_unslash( $_GET['exclude_category'] ) ) : '';
		$tags             = isset( $_GET['tags'] ) ? sanitize_text_field( wp_unslash( $_GET['tags'] ) ) : '';
		$exclude_tags     = isset( $_GET['exclude_tags'] ) ? sanitize_text_field( wp_unslash( $_GET['exclude_tags'] ) ) : '';
		$relation         = isset( $_GET['relation'] ) ? sanitize_text_field( wp_unslash( $_GET['relation'] ) ) : '';
		$number           = isset( $_GET['number'] ) ? sanitize_text_field( wp_unslash( $_GET['number'] ) ) : '';
		$price            = isset( $_GET['price'] ) ? sanitize_text_field( wp_unslash( $_GET['price'] ) ) : '';
		$excerpt          = isset( $_GET['excerpt'] ) ? sanitize_text_field( wp_unslash( $_GET['excerpt'] ) ) : '';
		$full_content     = isset( $_GET['full_content'] ) ? sanitize_text_field( wp_unslash( $_GET['full_content'] ) ) : '';
		$buy_button       = isset( $_GET['buy_button'] ) ? sanitize_text_field( wp_unslash( $_GET['buy_button'] ) ) : '';
		$columns          = isset( $_GET['columns'] ) ? sanitize_text_field( wp_unslash( $_GET['columns'] ) ) : '';
		$thumbnails       = isset( $_GET['thumbnails'] ) ? sanitize_text_field( wp_unslash( $_GET['thumbnails'] ) ) : '';
		$order_by         = isset( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : '';
		$order            = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$ids              = isset( $_GET['ids'] ) ? sanitize_text_field( wp_unslash( $_GET['ids'] ) ) : '';
		$pagination       = isset( $_GET['pagination'] ) ? sanitize_text_field( wp_unslash( $_GET['pagination'] ) ) : '';

		echo do_shortcode( "[sp_edd_downloads_grid category='$category' exclude_category='$exclude_category' tags='$tags' exclude_tags='$exclude_tags' relation='$relation' number='$number' price='$price' excerpt='$excerpt' full_content='$full_content' buy_button='$buy_button' columns='$columns' thumbnails='$thumbnails' order_by='$order_by' order='$order' ids='$ids' pagination='$pagination']" );
		exit;
	}
}

// Add [sp_edd_downloads_grid] shortcode.
add_shortcode( 'sp_edd_downloads_grid', 'seedprod_pro_edd_shortcode_downloads_grid' );

/**
 * EDD Downloads Grid Shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return void|string $output Shortcode output.
 */
function seedprod_pro_edd_shortcode_downloads_grid( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'category'         => '',
			'exclude_category' => '',
			'tags'             => '',
			'exclude_tags'     => '',
			'relation'         => '',
			'number'           => '',
			'price'            => '',
			'excerpt'          => '',
			'full_content'     => '',
			'buy_button'       => '',
			'columns'          => '',
			'thumbnails'       => '',
			'order_by'         => '',
			'order'            => '',
			'ids'              => '',
			'pagination'       => '',
		),
		$atts
	);

	$category         = $shortcode_args['category'];
	$exclude_category = $shortcode_args['exclude_category'];
	$tags             = $shortcode_args['tags'];
	$exclude_tags     = $shortcode_args['exclude_tags'];
	$relation         = $shortcode_args['relation'];
	$number           = $shortcode_args['number'];
	$price            = $shortcode_args['price'] === 'true' ? 'yes' : 'no'; // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
	$excerpt          = $shortcode_args['excerpt'] === 'true' ? 'yes' : 'no'; // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
	$full_content     = $shortcode_args['full_content'] === 'true' ? 'yes' : 'no'; // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
	$buy_button       = $shortcode_args['buy_button'] === 'true' ? 'yes' : 'no'; // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
	$columns          = $shortcode_args['columns'];
	$thumbnails       = $shortcode_args['thumbnails'];
	$order_by         = $shortcode_args['order_by'];
	$order            = $shortcode_args['order'];
	$ids              = $shortcode_args['ids'];
	$pagination       = $shortcode_args['pagination'];

	// Check if the EDD Instance exists.
	if ( ! in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// edd_go_to_checkout button
	$downloads_grid_html = do_shortcode( "[downloads category='$category' exclude_category='$exclude_category' tags='$tags' exclude_tags='$exclude_tags' relation='$relation' number='$number' price='$price' excerpt='$excerpt' full_content='$full_content' buy_button='$buy_button' columns='$columns' thumbnails='$thumbnails' orderby='$order_by' order='$order' ids='$ids' pagination='$pagination']" );

	$downloads_grid_html = str_replace( 'edd_go_to_checkout button', 'edd_go_to_checkout sp-button', $downloads_grid_html );
	$downloads_grid_html = str_replace( 'edd-add-to-cart button', 'edd-add-to-cart sp-button', $downloads_grid_html );

	return $downloads_grid_html;
}

/**
 * Render EDD Downloads Buy Now Button Shortcode for Builder Preview.
 */
function seedprod_pro_render_shortcode_edd_buy_now_button() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$product_id         = isset( $_GET['product_id'] ) ? sanitize_text_field( wp_unslash( $_GET['product_id'] ) ) : '';
		$direct_to_checkout = isset( $_GET['direct_to_checkout'] ) ? sanitize_text_field( wp_unslash( $_GET['direct_to_checkout'] ) ) : '';
		$show_price         = isset( $_GET['show_price'] ) ? sanitize_text_field( wp_unslash( $_GET['show_price'] ) ) : '';
		$btn_txt            = isset( $_GET['btn_txt'] ) ? sanitize_text_field( wp_unslash( $_GET['btn_txt'] ) ) : '';
		$before_icon        = isset( $_GET['before_icon'] ) ? sanitize_text_field( wp_unslash( $_GET['before_icon'] ) ) : '';
		$after_icon         = isset( $_GET['after_icon'] ) ? sanitize_text_field( wp_unslash( $_GET['after_icon'] ) ) : '';

		echo do_shortcode( "[sp_buy_now_button product_id='$product_id' btn_txt='$btn_txt' direct_to_checkout='$direct_to_checkout' show_price='$show_price' before_icon='$before_icon' after_icon='$after_icon']" );
		exit;
	}
}

// Add [sp_buy_now_button] shortcode.
add_shortcode( 'sp_buy_now_button', 'seedprod_pro_edd_buy_now_button_shortcode' );

/**
 * Render SeedProd EDD Shortcode[sp_buy_now_button].
 *
 * @return void|string Shortcode HTML.
 */
function seedprod_pro_edd_buy_now_button_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(
			'product_id'         => '',
			'direct_to_checkout' => 'true',
			'show_price'         => 'true',
			'btn_txt'            => '',
			'before_icon'        => '',
			'after_icon'         => '',
		),
		$atts
	);

	// Check if the EDD Instance exists.
	if ( ! in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	$show_price         = ( 'true' === $shortcode_args['show_price'] ) ? 1 : 0;
	$product_id         = $shortcode_args['product_id'];
	$btn_txt            = $shortcode_args['btn_txt'];
	$direct_to_checkout = $shortcode_args['direct_to_checkout'];
	$before_icon        = $shortcode_args['before_icon'];
	$after_icon         = $shortcode_args['after_icon'];

	// Add global button class.
	$purchase_link = do_shortcode( "[purchase_link id='$product_id' text='$btn_txt' direct='$direct_to_checkout' price='$show_price']" );
	$purchase_link = str_replace( 'edd-add-to-cart button', 'edd-add-to-cart sp-button', $purchase_link );
	$purchase_link = str_replace( 'edd_go_to_checkout button', 'edd_go_to_checkout sp-button', $purchase_link );

	// Insert before Icon.
	if ( '' !== $purchase_link && '' !== $before_icon ) {
		$doc = new DOMDocument();
		// Using LIBXML_NOERROR to prevent HTML errors since HTML5 is not supported by libxml2.
		$doc->loadHTML( $purchase_link, LIBXML_NOERROR );
		$xpath = new DOMXpath( $doc );

		// Get button text <span>.
		$button_span = $xpath->query( '//span[contains(@class, "edd-add-to-cart-label")]' )->item( 0 );

		$before_icon_html = $doc->createElement( 'i' );
		$class_attribute  = $doc->createAttribute( 'class' );

		// Value for the created attribute
		$class_attribute->value = $before_icon;

		// Don't forget to append it to the element
		$before_icon_html->appendChild( $class_attribute );
		$button_span->parentNode->insertBefore( $before_icon_html, $button_span ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		$purchase_link = $doc->saveHTML();
	}

	// Insert after Icon.
	if ( '' !== $purchase_link && '' !== $after_icon ) {
		$doc = new DOMDocument();
		// Using LIBXML_NOERROR to prevent HTML errors since HTML5 is not supported by libxml2.
		$doc->loadHTML( $purchase_link, LIBXML_NOERROR );
		$xpath = new DOMXpath( $doc );

		// Get button text <span>.
		$button_span = $xpath->query( '//span[contains(@class, "edd-loading")]' )->item( 0 );

		$after_icon_html = $doc->createElement( 'i' );
		$class_attribute = $doc->createAttribute( 'class' );

		// Value for the created attribute
		$class_attribute->value = $after_icon;

		// Don't forget to append it to the element
		$after_icon_html->appendChild( $class_attribute );

		$button_span->parentNode->insertBefore( $after_icon_html, $button_span ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$purchase_link = $doc->saveHTML();
	}

	return $purchase_link;
}

// Add [sp_edd_cart] shortcode.
add_shortcode( 'sp_edd_cart', 'seedprod_pro_edd_cart_shortcode' );

/**
 * Render SeedProd EDD Shortcode[sp_edd_cart].
 *
 * @return void|string
 */
function seedprod_pro_edd_cart_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	// Check if the EDD Instance exists.
	if ( ! in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	wp_enqueue_style( 'edd-styles' );
	$cart_html = do_shortcode( '[download_cart]' );

	// Insert after Icon.
	if ( '' !== $cart_html ) {
		$doc = new DOMDocument();
		// Using LIBXML_NOERROR to prevent HTML errors since HTML5 is not supported by libxml2.
		$doc->loadHTML( $cart_html, LIBXML_NOERROR );
		$xpath = new DOMXpath( $doc );

		// Get button text <span>.
		$button_span     = $xpath->query( '//li[contains(@class, "edd_checkout")]' )->item( 0 );
		$class_attribute = $doc->createAttribute( 'class' );

		// Value for the created attribute
		$class_attribute->value = 'sp-button';

		$button_span->firstChild->appendChild( $class_attribute ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$cart_html = $doc->saveHTML();
	}

	return $cart_html;
}

/**
 * Render EDD Cart Shortcode for Builder Preview
 */
function seedprod_pro_render_shortcode_edd_cart() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		echo do_shortcode( '[sp_edd_cart]' );
		exit;
	}
}

// Add [sp_edd_checkout] shortcode.
add_shortcode( 'sp_edd_checkout', 'seedprod_pro_edd_checkout_shortcode' );

/**
 * Render SeedProd EDD Shortcode[sp_edd_checkout].
 *
 * @return void|string HTML for the checkout form OR void.
 */
function seedprod_pro_edd_checkout_shortcode( $atts ) {
	// Set default shortcode args.
	$shortcode_args = shortcode_atts(
		array(),
		$atts
	);

	global $post;

	// Check if the EDD Instance exists.
	if ( ! in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Load global checkout script & vars.
	$version  = edd_admin_get_script_version();
	$currency = new \EDD\Currency\Currency( edd_get_currency() );

	wp_localize_script(
		'edd-checkout-global',
		'edd_global_vars',
		apply_filters(
			'edd_global_checkout_script_vars',
			array(
				'ajaxurl'               => esc_url_raw( edd_get_ajax_url() ),
				'checkout_nonce'        => wp_create_nonce( 'edd_checkout_nonce' ),
				'checkout_error_anchor' => '#edd_purchase_submit',
				'currency_sign'         => $currency->symbol,
				'currency_pos'          => $currency->position,
				'decimal_separator'     => $currency->decimal_separator,
				'thousands_separator'   => $currency->thousands_separator,
				'no_gateway'            => __( 'Please select a payment method', 'easy-digital-downloads' ),
				'no_discount'           => __( 'Please enter a discount code', 'easy-digital-downloads' ), // Blank discount code message
				'enter_discount'        => __( 'Enter discount', 'easy-digital-downloads' ),
				'discount_applied'      => __( 'Discount Applied', 'easy-digital-downloads' ), // Discount verified message
				'no_email'              => __( 'Please enter an email address before applying a discount code', 'easy-digital-downloads' ),
				'no_username'           => __( 'Please enter a username before applying a discount code', 'easy-digital-downloads' ),
				'purchase_loading'      => __( 'Please Wait...', 'easy-digital-downloads' ),
				'complete_purchase'     => edd_get_checkout_button_purchase_label(),
				'taxes_enabled'         => edd_use_taxes() ? '1' : '0',
				'edd_version'           => $version,
				'current_page'          => '2768',
			)
		)
	);

	$script = "jQuery(document).ready(function($) {
		$( '#edd-email[type=\"hidden\"]' ).trigger( 'blur' );
	} );";
	wp_add_inline_script( 'edd-checkout-global', $script );
	wp_enqueue_script( 'edd-checkout-global' );
	wp_enqueue_style( 'edd-styles' );
	wp_enqueue_script( 'jQuery.payment' );
	wp_enqueue_script( 'creditCardValidator' );

	// Load pro checkout script & vars.
	wp_enqueue_script( 'edd-pro-checkout', EDD_PLUGIN_URL . 'assets/pro/js/checkout.js', array( 'edd-checkout-global' ), EDD_VERSION, true );
	wp_localize_script(
		'edd-pro-checkout',
		'EDDProCheckout',
		array(
			'ajax'  => edd_get_ajax_url(),
			'api'   => 'https://geo.easydigitaldownloads.com/v3/geolocate/json',
			'taxes' => edd_use_taxes(),
			'debug' => edd_doing_script_debug(),
			'nonce' => wp_create_nonce( 'edd-pro-geoip' ),
		)
	);

	// Load stripe checkout script if enabled.
	// We're going to assume Payment Elements needs to load...
	$deps = array( 'edd-styles' );

	if ( ! wp_script_is( 'edd-styles', 'enqueued' ) ) {
		$deps = array();
	}
	$style_src = EDDSTRIPE_PLUGIN_URL . 'assets/css/build/paymentelements.min.css';

	// But if the user has Card Elements, we need to load that instead.
	$elements_mode = edds_get_elements_mode();
	if ( 'card-elements' === $elements_mode ) {
		$style_src = EDDSTRIPE_PLUGIN_URL . 'assets/css/build/cardelements.min.css';
	}

	wp_register_style(
		'edd-stripe',
		$style_src,
		$deps,
		EDD_STRIPE_VERSION . '-' . $elements_mode
	);

	wp_enqueue_style( 'edd-stripe' );

	// We're going to assume Payment Elements needs to load...
	$publishable_key_option = edd_is_test_mode() ? 'test_publishable_key' : 'live_publishable_key';
	$publishable_key        = edd_get_option( $publishable_key_option, '' );
	$script_source          = EDDSTRIPE_PLUGIN_URL . 'assets/js/build/paymentelements.min.js';

	$script_deps = array(
		'sandhills-stripe-js-v3',
		'jquery',
		'edd-ajax',
	);

	// But if the user has Card Elements, we need to load that instead.
	$elements_mode = edds_get_elements_mode();
	if ( 'card-elements' === $elements_mode ) {
		$script_source = EDDSTRIPE_PLUGIN_URL . 'assets/js/build/cardelements.min.js';
		$script_deps[] = 'jQuery.payment';
	}

	wp_register_script(
		'edd-stripe-js',
		$script_source,
		$script_deps,
		EDD_STRIPE_VERSION . '-' . $elements_mode,
		true
	);

	wp_enqueue_script( 'edd-stripe-js' );

	$stripe_localized_vars = array(
		'publishable_key'                => trim( $publishable_key ),
		'isTestMode'                     => edd_is_test_mode() ? 'true' : 'false',
		'elementsMode'                   => $elements_mode,
		'is_ajaxed'                      => edd_is_ajax_enabled() ? 'true' : 'false',
		'currency'                       => edd_get_currency(),
		'country'                        => edd_get_option( 'base_country', 'US' ),
		'locale'                         => edds_get_stripe_checkout_locale(),
		'is_zero_decimal'                => edds_is_zero_decimal_currency() ? 'true' : 'false',
		'checkout'                       => edd_get_option( 'stripe_checkout' ) ? 'true' : 'false',
		'store_name'                     => ! empty( edd_get_option( 'entity_name' ) ) ? edd_get_option( 'entity_name' ) : get_bloginfo( 'name' ),
		'submit_text'                    => edd_get_option( 'stripe_checkout_button_text', __( 'Next', 'easy-digital-downloads' ) ),
		'image'                          => edd_get_option( 'stripe_checkout_image' ),
		'zipcode'                        => edd_get_option( 'stripe_checkout_zip_code', false ) ? 'true' : 'false',
		'billing_address'                => edd_get_option( 'stripe_checkout_billing', false ) ? 'true' : 'false',
		'remember_me'                    => edd_get_option( 'stripe_checkout_remember', false ) ? 'true' : 'false',
		'no_key_error'                   => __( 'Stripe publishable key missing. Please enter your publishable key in Settings.', 'easy-digital-downloads' ),
		'checkout_required_fields_error' => __( 'Please fill out all required fields to continue your purchase.', 'easy-digital-downloads' ),
		'checkout_agree_to_terms'        => __( 'Please agree to the terms to complete your purchase.', 'easy-digital-downloads' ),
		'checkout_agree_to_privacy'      => __( 'Please agree to the privacy policy to complete your purchase.', 'easy-digital-downloads' ),
		'generic_error'                  => __( 'Unable to complete your request. Please try again.', 'easy-digital-downloads' ),
		'prepaid'                        => edd_get_option( 'stripe_allow_prepaid', false ) ? 'true' : 'false',
		'successPageUri'                 => edd_get_success_page_uri(),
		'failurePageUri'                 => edd_get_failed_transaction_uri(),
		'debuggingEnabled'               => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'true' : 'false',
		'formLoadingText'                => __( 'Please wait...', 'easy-digital-downloads' ),
		'cartHasSubscription'            => function_exists( 'edd_recurring' ) && edd_recurring()->cart_contains_recurring() ? 'true' : 'false',
	);

	$stripe_vars = apply_filters(
		'edd_stripe_js_vars',
		$stripe_localized_vars
	);

	wp_localize_script( 'edd-stripe-js', 'edd_stripe_vars', $stripe_vars );

	$checkout_shortcode_html = do_shortcode( '[download_checkout]' );
	$checkout_shortcode_html = str_replace( 'edd-submit', 'edd-submit sp-button', $checkout_shortcode_html );
	$checkout_shortcode_html = str_replace( 'edd-cart-saving-button edd-submit button', 'edd-cart-saving-button edd-submit sp-button', $checkout_shortcode_html );

	ob_start();
	// Output the SVG icons
	edd_print_payment_icons(
		array(
			'mastercard',
			'visa',
			'americanexpress',
			'discover',
			'paypal',
			'amazon',
		)
	);
	echo $checkout_shortcode_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$render = ob_get_clean();
	return $render;
}

/**
 * Render EDD Checkout Shortcode for Builder Preview
 */
function seedprod_pro_render_shortcode_edd_checkout() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		echo do_shortcode( '[sp_edd_checkout]' );
		exit;
	}
}

/**
 * Render EDD Checkout Shortcode for Builder Preview
 */
function seedprod_pro_edd_checkout_purchase_form() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		echo do_action( 'edd_purchase_form' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}
}

/**
 * Get EDD download tags.
 *
 * @return JSON object.
 */
function seedprod_pro_get_edd_download_taxonomy() {
	$taxonomy = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Check if EDD is installed and active.
		if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// Fetch taxonomy.
			$args = array(
				'taxonomy'   => isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '',
				'hide_empty' => false,
			);

			$taxonomy = get_terms( $args );
		}
	}

	wp_send_json( $taxonomy );
}

/**
 * Get EDD downloads.
 *
 * @return JSON object.
 */
function seedprod_pro_get_edd_downloads() {
	$downloads = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Check if EDD is installed and active.
		if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// Fetch Downloads.
			$args = array(
				'post_type'      => 'download',
				'status'         => 'publish',
				'posts_per_page' => -1,
			);

			$download_list = get_posts( $args );

			foreach ( $download_list as $download ) {
				$api         = new EDD_API();
				$downloads[] = $api->get_product_data( $download );
			}
		}
	}

	wp_send_json( $downloads );
}

