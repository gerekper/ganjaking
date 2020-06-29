<?php
if( !defined('ABSPATH'))
    exit;

if( !function_exists( 'ywcpos_get_email_template_order_content' ) ){

    function ywcpos_get_email_template_order_content( $order, $sent_to_admin = false,  $plain_text = false, $email = '' ){

        ob_start();
        wc_get_template( 'emails/email-order-details.php', array( 'order' => $order, 'sent_to_admin' => $sent_to_admin, 'plain_text' => $plain_text, 'email' => $email ) );
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }
}

if ( ! function_exists( 'yith_download_file' ) ) {

    /**
     * Download a file
     *
     * @param $filepath
     */
    function yith_download_file( $filepath ) {

        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Content-Description: File Transfer' );
        header( 'Content-Disposition: attachment; filename=' . $filepath );
        header( "Content-Type: text/csv; charset=" . get_option( 'blog_charset' ), true );
        header( 'Expires: 0' );
        header( 'Pragma: public' );
      //  header( 'Location: ' . $filepath );

       readfile( $filepath );
        exit;
    }
}

if( !function_exists( 'ywcpos_update_counter_meta' ) ){

    function ywcpos_update_counter_meta( $post_id, $meta_key, $qt=1 ){

        $current_count = get_post_meta( $post_id, $meta_key ,true );
        $current_count = empty( $current_count ) ? $qt : $current_count+$qt;

        update_post_meta( $post_id, $meta_key, $current_count );
    }
}

if( !function_exists( 'ywcpos_update_counter' ) ){

    function ywcpos_update_counter( $option_name, $qt=1){

        $current_count = get_option( $option_name ,0 );

        update_option( $option_name, $current_count+$qt );
    }
}


if( !function_exists( 'pending_thankyou_message' ) ) {
    function pending_thankyou_message() {

        wc_get_template('survey-thankyou-message.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH );
    }
}

add_action( 'pending_order_survey_before_main_content', 'pending_thankyou_message');


if( !function_exists( 'set_pending_order' ) ){
	
	function set_pending_order(){
		global $wpdb;
		
		$held_duration = get_option( 'ywcpos_include_pending_from' );
		
		if ( $held_duration < 1)
			return;
		
			$date = date( "Y-m-d H:i:s", strtotime( '-' . absint( $held_duration ) . ' MINUTES', current_time( 'timestamp' ) ) );
		
			$unpaid_orders = $wpdb->get_col( $wpdb->prepare( "
					SELECT DISTINCT posts.ID
					FROM {$wpdb->posts} AS posts INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
					WHERE 	posts.post_type = 'shop_order'
					AND 	posts.post_status = 'wc-pending'
					AND 	posts.post_modified < %s
					AND     posts.post_parent = 0
					AND     posts.ID NOT IN ( SELECT metapost.post_id
											 FROM {$wpdb->postmeta} AS metapost INNER JOIN {$wpdb->posts} AS post2 ON metapost.post_id = post2.ID 
											 WHERE post2.post_type = 'shop_order'
											 AND ( metapost.meta_key LIKE %s AND metapost.meta_value LIKE 'yes' ) 
											 
											)", $date,'_ywcpos_is_pending' ) );
			
			if ( $unpaid_orders ) {
				
				ywcpos_update_counter('ywcpos_count_pending_order', count( $unpaid_orders ) );
				
				foreach ( $unpaid_orders as $unpaid_order ) {
					
					update_post_meta($unpaid_order, '_ywcpos_is_pending', 'yes' );
				}
			}
		
			wp_clear_scheduled_hook( 'ywpos_check_pending_order' );
			wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'ywpos_check_pending_order' );
	}
}

add_action( 'ywpos_check_pending_order', 'set_pending_order' );