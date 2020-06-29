<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Donations_Shortcode' ) ){

    class YITH_WC_Donations_Shortcode{


        public static function print_shortcode( $atts, $content='' ){

            global $YITH_Donations;
            $ajax_cart_en         = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
            $ajax_class = $ajax_cart_en ? 'ywcds_ajax_add_donation' : '';
	        $ajax_class   = apply_filters( 'ywcds_ajax_class', $ajax_class );
	        $donation_id = yit_wpml_object_id( get_option('_ywcds_donation_product_id'), 'product', true );
            $default   =   array(
                'message_for_donation' => get_option( 'ywcds_message_for_donation' ),
                'button_class' => 'ywcds_add_donation_product button alt '.$ajax_class,
                'product_id' => $donation_id,
                'button_text' => $YITH_Donations->get_message('text_button' ),
	            'donation_amount' => '',
	            'donation_amount_style' => 'label',
	            'show_extra_desc' => 'off',
	            'extra_desc_label' => ''

            );


            $atts   =   shortcode_atts( $default, $atts );



            ob_start();
            wc_get_template( 'add-donation-form-widget.php', $atts , '', YWCDS_TEMPLATE_PATH);
            $template = ob_get_contents();
            ob_end_clean();
            return $template;
           // return wc_get_template('add-donation-form-widget.php', $atts );
        }

        public static function summary_donation( $atts ){

        	$default_atts = array(
        		'summary_from' => 'week',
		        'include_tax' => 'off'
	        );

        	$atts = shortcode_atts( $default_atts, $atts );

        	return wc_get_template_html( 'summary-donations.php', $atts, '',YWCDS_TEMPLATE_PATH );
        }
    }
}

add_shortcode( 'yith_wcds_donations', array( 'YITH_WC_Donations_Shortcode', 'print_shortcode' ) );
add_shortcode( 'yith_wcds_donations_summary', array( 'YITH_WC_Donations_Shortcode', 'summary_donation' ) );
