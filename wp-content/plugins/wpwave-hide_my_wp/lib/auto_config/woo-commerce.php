<?php
if ($this->opt('rename_plugins')){
    $path = trim($this->opt('new_plugin_path'), '/ ') .'/'. $this->hash(WC_PLUGIN_BASENAME);
}else{
    $woo_folder = trim(str_replace(WP_PLUGIN_URL,'', WC()->plugin_url()), '/') ;
    $path = trim($this->opt('new_plugin_path'), ' /') .'/'. $woo_folder;
}

$theme_path = '';

$this->top_replace_old[]="rel='stylesheet' id='woocommerce";
$this->top_replace_new[]="rel='stylesheet' id='wc";

$this->auto_replace_urls[]= $prefix .'wp-content/plugins/woocommerce/assets/css/woocommerce.css=='. $path .'/assets/css/ec.css';
$this->auto_replace_urls[]=$prefix . 'wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min.js=='.$path.'/assets/js/wc.min.js';

$this->auto_replace_urls[]=$prefix .'wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css=='.$path.'/assets/css/wc-layout.css';
$this->auto_replace_urls[]=$prefix .'wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css=='.$path.'/assets/css/wc-smallscreen.css';



//specific to "storefront" theme.
$this->top_replace_old[]="rel='stylesheet' id='storefront-woocommerce-style-css";
$this->top_replace_new[]="rel='stylesheet' id='storefront-wc-style-css";

$this->top_replace_old[]="<style id='storefront-woocommerce-style-inline-css";
$this->top_replace_new[]="<style id='storefront-wc-style-inline-css";

$this->top_replace_old[]="woocommerce-product-search-field-0";
$this->top_replace_new[]="wc-product-search-field-0";

$this->top_replace_old[]="woocommerce-product-search-field-1";
$this->top_replace_new[]="wc-product-search-field-1";

//general woocommerce replacements. 
$this->top_replace_old[]="<style id='woocommerce-inline-inline-css";
$this->top_replace_new[]="<style id='wc-inline-inline-css";


// $this->auto_replace_urls[]=$prefix . 'wp-content/themes/storefront/assets/js/woocommerce/header-cart.min.js=='.'skin/assets/js/storefront-headercart.min.js';


//store front theme ends here. 


$internal_js = array();
$before= '<script type=\'text/javascript\'>
/* <![CDATA[ */
';
$after = '
/* ]]> */
</script>';

add_filter('woocommerce_ajax_get_endpoint',  array(&$this, 'wc_endpoint'), 10, 1);

$endpoint = str_replace('/', '\/', WC_AJAX::get_endpoint('%%endpoint%%'));

//TODO: doesn't work in init only after query
//'is_checkout'               => is_page( wc_get_page_id( 'checkout' ) ) && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,


$objects = array(

    'replace_wc' =>
        array(
            'ajax_url'    => WC()->ajax_url(),
            'wc_ajax_url' => WC_AJAX::get_endpoint( "%%endpoint%%" )
        ),
    'wc-geolocation' => array(
        'wc_ajax_url'  => WC_AJAX::get_endpoint( "%%endpoint%%" ),
        'home_url'     => home_url(),
        'is_available' => ! ( is_cart() || is_account_page() || is_checkout() || is_customize_preview() ) ? '1' : '0',
        'hash'         => isset( $_GET['v'] ) ? wc_clean( $_GET['v'] ) : ''
    ),
    'wc-single-product' => array(
        'i18n_required_rating_text' => esc_attr__( 'Please select a rating', 'woocommerce' ),
        'review_rating_required'    => get_option( 'woocommerce_review_rating_required' ),
    ),

    //wc-checkout remained from class-wc-frontend-scripts.php data

    'wc-address-i18n' => array(
        'locale'             => json_encode( WC()->countries->get_country_locale() ),
        'locale_fields'      => json_encode( WC()->countries->get_country_locale_field_selectors() ),
        'i18n_required_text' => esc_attr__( 'required', 'woocommerce' ),
    ),
    'wc-cart' => array(
        'ajax_url'                     => WC()->ajax_url(),
        'wc_ajax_url'                  => WC_AJAX::get_endpoint( "%%endpoint%%" ),
        'update_shipping_method_nonce' => wp_create_nonce( "update-shipping-method" ),
        'apply_coupon_nonce'           => wp_create_nonce( "apply-coupon" ),
        'remove_coupon_nonce'          => wp_create_nonce( "remove-coupon" ),
    ),
    'wc-cart-fragments' => array(
        'ajax_url'      => WC()->ajax_url(),
        'wc_ajax_url'   => WC_AJAX::get_endpoint( "%%endpoint%%" ),
        'fragment_name' => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments' )
    ),
    'wc-add-to-cart' => array(
        'ajax_url'                => WC()->ajax_url(),
        'wc_ajax_url'             => WC_AJAX::get_endpoint( "%%endpoint%%" ),
        'i18n_view_cart'          => esc_attr__( 'View Cart', 'woocommerce' ),
        'cart_url'                => apply_filters( 'woocommerce_add_to_cart_redirect', wc_get_cart_url() ),
        'is_cart'                 => is_cart(),
        'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
    ),
    'wc-add-to-cart-variation' =>
    // We also need the wp.template for this script :)
    //	wc_get_template( 'single-product/add-to-cart/variation.php' );

        array(
            'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
            'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
            'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' )
        ),
    'wc-country-select' => array(
        'countries'                 => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
        'i18n_select_state_text'    => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
        'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
        'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
        'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
        'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
        'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
        'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
        'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
        'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
        'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
        'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
        'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
        'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' )
    )

);


//From WooCommerce
foreach($objects as $handle=>$params){
    foreach ( (array) $params as $key => $value ) {
        if ( !is_scalar($value) )
            continue;

        $params[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
    }

    $object_name = str_replace( '-', '_', $handle ) . '_params';
    $internal_js[]= $script = "var $object_name = " . wp_json_encode( $params ) . ';';
}

$internal_js[]= 'var wc_single_product_params = {"i18n_required_rating_text":"'.esc_attr__( 'Please select a rating', 'woocommerce' ).'","review_rating_required":"'.get_option( 'woocommerce_review_rating_required' ).'"};';

$woocommerce_params = array(
        'ajax_url'    => WC()->ajax_url(),
        'wc_ajax_url' => WC_AJAX::get_endpoint( "%%endpoint%%" )
    );


//remove woocommerce_params from auto.js because Wapanalyzer detects using this variable (object)

$this->top_replace_old[] = "var woocommerce_params = " . wp_json_encode( $woocommerce_params ) . ';';
$this->top_replace_new[]= '';


//instead define it using prototype based method which is undetected by Wapanalyzer. We are using
//defineProperty to make the prototype definition compatible with jQuery. 

$internal_js[] = "
Object.defineProperty(Object.prototype, 'woocommerce_params',{
  value :  " . wp_json_encode( $woocommerce_params ) . ",
  enumerable : false
});

";


//is_page doesn't work should be after query


foreach ($internal_js as $int_js) {
    $this->top_replace_old[]= $before . $int_js . $after;
    $this->top_replace_new[]= '';
    $this->auto_config_internal_js .= $int_js ."\n\n" ;
}

add_action('wp_loaded', 'hmwp_wc_remove_no_js_items');
/** Remove inline CSS */
function hmwp_wc_remove_no_js_items() {
    remove_action('wp_head', 'wc_gallery_noscript');
}

//Changing mailchimp for WooCoomerce JS
$this->auto_replace_urls[]= $prefix .'wp-content/plugins/mailchimp-for-woocommerce/public/js/mailchimp-woocommerce-public.min.js=='.$prefix .'wp-content/plugins/mailchimp-for-woocommerce/public/js/mailchimp-wc-public.min.js';


?>