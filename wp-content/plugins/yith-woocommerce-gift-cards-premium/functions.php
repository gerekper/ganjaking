<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once( YITH_YWGC_DIR . 'lib/class-yith-woocommerce-gift-cards.php' );
require_once( YITH_YWGC_DIR . 'lib/class-yith-woocommerce-gift-cards-premium.php' );
require_once( YITH_YWGC_DIR . 'lib/class-yith-ywgc-backend.php' );
require_once( YITH_YWGC_DIR . 'lib/class-yith-ywgc-backend-premium.php' );
require_once( YITH_YWGC_DIR . 'lib/class-yith-ywgc-frontend.php' );
require_once( YITH_YWGC_DIR . 'lib/class-yith-ywgc-frontend-premium.php' );

require_once( YITH_YWGC_DIR . 'lib/class-yith-ywgc-gift-card.php' );
require_once( YITH_YWGC_DIR . 'lib/class-yith-ywgc-gift-card-premium.php' );

/** Define constant values */
defined( 'YWGC_CUSTOM_POST_TYPE_NAME' ) || define( 'YWGC_CUSTOM_POST_TYPE_NAME', 'gift_card' );
defined( 'YWGC_GIFT_CARD_PRODUCT_TYPE' ) || define( 'YWGC_GIFT_CARD_PRODUCT_TYPE', 'gift-card' );
defined( 'YWGC_PRODUCT_PLACEHOLDER' ) || define( 'YWGC_PRODUCT_PLACEHOLDER', '_ywgc_placeholder' );
defined( 'YWGC_CATEGORY_TAXONOMY' ) || define( 'YWGC_CATEGORY_TAXONOMY', 'giftcard-category' );

/** Race conditions - Gift cards duplicates */
defined( 'YWGC_RACE_CONDITION_BLOCKED' ) || define( 'YWGC_RACE_CONDITION_BLOCKED', '_ywgc_race_condition_blocked' );
defined( 'YWGC_RACE_CONDITION_UNIQUID' ) || define( 'YWGC_RACE_CONDITION_UNIQUID', '_ywgc_race_condition_uniqid' );

/*  plugin actions */
defined( 'YWGC_ACTION_RETRY_SENDING' ) || define( 'YWGC_ACTION_RETRY_SENDING', 'retry-sending' );
defined( 'YWGC_ACTION_DOWNLOAD_PDF' ) || define( 'YWGC_ACTION_DOWNLOAD_PDF', 'donwload-gift-pdf' );
defined( 'YWGC_ACTION_ENABLE_CARD' ) || define( 'YWGC_ACTION_ENABLE_CARD', 'enable-gift-card' );
defined( 'YWGC_ACTION_DISABLE_CARD' ) || define( 'YWGC_ACTION_DISABLE_CARD', 'disable-gift-card' );
defined( 'YWGC_ACTION_ADD_DISCOUNT_TO_CART' ) || define( 'YWGC_ACTION_ADD_DISCOUNT_TO_CART', 'ywcgc-add-discount' );
defined( 'YWGC_ACTION_VERIFY_CODE' ) || define( 'YWGC_ACTION_VERIFY_CODE', 'ywcgc-verify-code' );
defined( 'YWGC_ACTION_PRODUCT_ID' ) || define( 'YWGC_ACTION_PRODUCT_ID', 'ywcgc-product-id' );
defined( 'YWGC_ACTION_GIFT_THIS_PRODUCT' ) || define( 'YWGC_ACTION_GIFT_THIS_PRODUCT', 'ywcgc-gift-this-product' );

/*  gift card post_metas */
defined( 'YWGC_META_GIFT_CARD_ORDERS' ) || define( 'YWGC_META_GIFT_CARD_ORDERS', '_ywgc_orders' );
defined( 'YWGC_META_GIFT_CARD_CUSTOMER_USER' ) || define( 'YWGC_META_GIFT_CARD_CUSTOMER_USER', '_ywgc_customer_user' ); // Refer to user that use the gift card
defined( 'YWGC_ORDER_ITEM_DATA' ) || define( 'YWGC_ORDER_ITEM_DATA', '_ywgc_order_item_data' );

/*  order item metas    */
defined( 'YWGC_META_GIFT_CARD_POST_ID' ) || define( 'YWGC_META_GIFT_CARD_POST_ID', '_ywgc_gift_card_post_id' );
defined( 'YWGC_META_GIFT_CARD_CODE' ) || define( 'YWGC_META_GIFT_CARD_CODE', '_ywgc_gift_card_code' );
defined( 'YWGC_META_GIFT_CARD_STATUS' ) || define( 'YWGC_META_GIFT_CARD_STATUS', '_ywgc_gift_card_status' );

/* Gift card status */




if ( ! function_exists( 'ywgc_get_status_label' ) ) {
	/**
	 * Retrieve the status label for every gift card status
	 *
	 * @param YITH_YWGC_Gift_Card $gift_card
	 *
	 * @return string
	 */
	function ywgc_get_status_label( $gift_card ) {
		return $gift_card->get_status_label();
	}
}

if ( ! function_exists( 'ywgc_get_order_item_giftcards' ) ) {
	/**
	 * Retrieve the gift card ids associated to an order item
	 *
	 * @param int $order_item_id
	 *
	 * @return string|void
	 * @author Lorenzo Giuffrida
	 * @since  1.0.0
	 */
	function ywgc_get_order_item_giftcards( $order_item_id ) {

		/*
		 * Let third party plugin to change the $order_item_id
		 *
		 * @since 1.3.7
		 */
		$order_item_id = apply_filters( 'yith_get_order_item_gift_cards', $order_item_id );
		$gift_ids      = wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_POST_ID );

		if ( is_numeric( $gift_ids ) ) {
			$gift_ids = array( $gift_ids );
		}

		if ( ! is_array( $gift_ids ) ) {
			$gift_ids = array();
		}

		return $gift_ids;
	}
}

if ( ! function_exists( 'ywgc_set_order_item_giftcards' ) ) {
	/**
	 * Retrieve the gift card ids associated to an order item
	 *
	 * @param int   $order_item_id the order item
	 * @param array $ids           the array of gift card ids associated to the order item
	 *
	 * @return string|void
	 * @author Lorenzo Giuffrida
	 * @since  1.0.0
	 */
	function ywgc_set_order_item_giftcards( $order_item_id, $ids ) {

        $ids = apply_filters( 'yith_ywgc_set_order_item_meta_gift_card_ids', $ids, $order_item_id );

        wc_update_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_POST_ID, $ids );

        $gift_card_codes = array();
        foreach ( $ids as $gc_id ){

            $gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gc_id ) );
            $gc_code = $gc->get_code();

            $gift_card_codes[] = $gc_code;
        }

        wc_update_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_CODE, $gift_card_codes );

        do_action( 'yith_ywgc_set_order_item_meta_gift_card_ids_updated', $order_item_id, $ids );
	}
}

if ( ! function_exists( 'yith_get_attachment_image_url' ) ) {

	/**
	 * @return string
	 */
	function yith_get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {

		if ( function_exists( 'wp_get_attachment_image_url' ) ) {
			$header_image_url = wp_get_attachment_image_url( $attachment_id, $size );
		} else {
			$header_image     = wp_get_attachment_image_src( $attachment_id, $size );
			$header_image_url = $header_image['url'];
		}
        $header_image_url = apply_filters('yith_ywcgc_attachment_image_url',$header_image_url);
		return $header_image_url;
	}

}


add_filter( 'yit_fw_metaboxes_type_args', 'ywgc_filter_balance_display' );

if ( ! function_exists( 'ywgc_filter_balance_display' ) ) {
	/**
	 * Fix the current balance display to match WooCommerce settings
	 * @param $args
	 *
	 * @return mixed
	 */
	function ywgc_filter_balance_display( $args ) {

		if ( $args['args']['args']['id'] == '_ywgc_balance_total' ) {
			$args['args']['args']['value'] = round( $args['args']['args']['value'], wc_get_price_decimals() );
		}

		return $args;
	}
}

if ( ! function_exists( 'ywgc_disallow_gift_cards_with_same_title' ) ) {
	/**
	 * Avoid new gift cards with the same name
	 *
	 * @param $messages
	 */
	function ywgc_disallow_gift_cards_with_same_title( $messages ) {
		global $post;
		global $wpdb;
		$title       = $post->post_title;
		$post_id     = $post->ID;

        do_action('yith_ywgc_before_disallow_gift_cards_with_same_title_query', $post_id, $messages );

        if (get_post_type($post_id) !== 'gift_card' || (get_post_type($post_id) == 'gift_card' && $title == '')) {
            return $messages;
        }

		$wtitlequery = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'gift_card' AND post_title = %s AND ID != %d ";
		$wresults = $wpdb->get_results( $wpdb->prepare( $wtitlequery, $title, $post_id ) );

		if ( $wresults ) {
			$error_message = __('This title is already used. Please choose another one', 'yith-woocommerce-gift-cards');
			add_settings_error( 'post_has_links', '', $error_message, 'error' );
			settings_errors( 'post_has_links' );
			$post->post_status = 'draft';
			wp_update_post( $post );

			return;
		}

		return $messages;

	}

	add_action( 'post_updated_messages', 'ywgc_disallow_gift_cards_with_same_title' );
}


/**
 * Set noindex meta tag for gift card default products
 */
function yith_wcgc_add_tagseo_metarob() {
	if ( get_post_type( get_the_ID() ) == 'product'){
		$post_id = get_the_ID();
		$product = new WC_Product($post_id);
		if( $product->get_id() == YITH_WooCommerce_Gift_Cards_Premium::get_instance()->default_gift_card_id){
			?>
			<meta name="robots" content="noindex">
			<?php
		}
	}
}

add_action('wp_head', 'yith_wcgc_add_tagseo_metarob');


/* Shortcode to print gift card list */
add_shortcode('yith_wcgc_show_gift_card_list','yith_wcgc_show_gift_card_list');

function yith_wcgc_show_gift_card_list( $atts,$content = null ){
    YITH_YWGC_Frontend_Premium::get_instance ()->yith_ywgc_gift_cards_content();
    return ob_get_clean();
}


/* Convert gift card delivery date to timestamp */
add_action('init','yith_wcgc_convert_date_to_timestamp');

function yith_wcgc_convert_date_to_timestamp(){

    $delivery_date_converted_in_timestamp = get_option( 'yith_wcgc_delivery_date_converted_in_timestamp' );

    if( ! $delivery_date_converted_in_timestamp ){ // Remove NOT logic condition to execute again the process (if the option is already saved)


        $gift_cards = get_posts([
            'post_type' => YWGC_CUSTOM_POST_TYPE_NAME,
            'post_status' => 'any',
            'numberposts' => -1,
        ]);
        foreach ( $gift_cards as $gift_card ){
            $delivery_date = get_post_meta( $gift_card->ID ,'_ywgc_delivery_date', true );
            $delivery_send_date = get_post_meta( $gift_card->ID ,'_ywgc_delivery_send_date', true );
            $expiration_date = get_post_meta( $gift_card->ID ,'_ywgc_expiration', true );

            if( $delivery_date != '' ){
                $timestamp = strtotime( $delivery_date );
                $timestamp && update_post_meta( $gift_card->ID,'_ywgc_delivery_date', $timestamp );
            }
            if( $delivery_send_date != '' ){
                $timestamp = strtotime( $delivery_send_date );
                $timestamp && update_post_meta( $gift_card->ID,'_ywgc_delivery_send_date', $timestamp );
            }
            if( $expiration_date != '' ){
                $timestamp = strtotime( $expiration_date );
                $timestamp && update_post_meta( $gift_card->ID,'_ywgc_expiration', $timestamp );
            }
        }
        update_option( 'yith_wcgc_delivery_date_converted_in_timestamp',true );
    }

}

/* Convert gift card delivery date to timestamp */
add_action('init','yith_wcgc_convert_date_picker_dates');

function yith_wcgc_convert_date_picker_dates(){

    $checker = get_option( 'yith_wcgc_date_pickers_convert_v5' );

    if(  ! $checker ){ // Remove NOT logic condition to execute again the process (if the option is already saved)

        $gift_cards = get_posts([
            'post_type' => YWGC_CUSTOM_POST_TYPE_NAME,
            'post_status' => 'any',
            'numberposts' => -1,
        ]);

        $date_format = apply_filters('yith_wcgc_date_format','Y-m-d');


        foreach ( $gift_cards as $gift_card ){

            $gift_card = YITH_YWGC()->get_gift_card_by_code( $gift_card->post_title );

            $delivery_date = $gift_card->delivery_date;
            $expiration_date = $gift_card->expiration;

            if( $delivery_date != '' ){

                $delivery_date_format = date_i18n ( $date_format, $delivery_date );
                update_post_meta( $gift_card->ID,'_ywgc_delivery_date_formatted', $delivery_date_format );
            }
            if( $expiration_date != '' ){

                $expiration_date_format = $expiration_date != '0' ? date_i18n ( $date_format, $expiration_date ) : '';
                update_post_meta( $gift_card->ID,'_ywgc_expiration_date_formatted', $expiration_date_format );
            }
        }
        update_option( 'yith_wcgc_date_pickers_convert_v5',true );
    }
}

/**
 * Make a backup of the database
 */
function yith_wcgc_make_backup_db(){
    global $wpdb;

// Get a list of the tables
    $tables = $wpdb->get_results('SHOW TABLES');

    $upload_dir = wp_upload_dir();
    $backup_dir = $upload_dir['basedir'] . '/backups';
    if (! is_dir($backup_dir)) {
        mkdir( $backup_dir, 0700 );
    }
    $file_path = $backup_dir . '/database-' . time() . '.sql';
    $file = fopen($file_path, 'w');

    foreach ($tables as $table)
    {
        foreach( $table as $index => $table_name ){

            $schema = $wpdb->get_row('SHOW CREATE TABLE ' . $table_name, ARRAY_A);
            fwrite($file, $schema['Create Table'] . ';' . PHP_EOL);

            $rows = $wpdb->get_results('SELECT * FROM ' . $table_name, ARRAY_A);

            if( $rows )
            {
                fwrite($file, 'INSERT INTO ' . $table_name . ' VALUES ');

                $total_rows = count($rows);
                $counter = 1;
                foreach ($rows as $row => $fields)
                {
                    $line = '';
                    foreach ($fields as $key => $value)
                    {
                        $value = addslashes($value);
                        $line .= '"' . $value . '",';
                    }

                    $line = '(' . rtrim($line, ',') . ')';

                    if ($counter != $total_rows)
                    {
                        $line .= ',' . PHP_EOL;
                    }

                    fwrite($file, $line);

                    $counter++;
                }

                fwrite($file, '; ' . PHP_EOL);
            }
        }

    }

    fclose($file);
}


function ywgc_get_attachment_id_from_url( $attachment_url = '' ) {

    global $wpdb;
    $attachment_id = false;

    if ( '' == $attachment_url )
        return;

    $upload_dir_paths = wp_upload_dir();

    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

        $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

        $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

        $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
                          WHERE wposts.ID = wpostmeta.post_id
                          AND wpostmeta.meta_key = '_wp_attached_file'
                          AND wpostmeta.meta_value = '%s'
                          AND wposts.post_type = 'attachment'", $attachment_url ) );
    }

    return $attachment_id;
}
