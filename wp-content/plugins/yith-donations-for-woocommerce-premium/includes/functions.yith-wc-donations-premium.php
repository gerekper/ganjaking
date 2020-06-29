<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !function_exists(  'ywcds_get_gateway' ) ){

    function ywcds_get_gateway(){
        $payment = WC()->payment_gateways->payment_gateways();
        $gateways = array();
        foreach($payment as $gateway){
            if ( $gateway->enabled == 'yes' ){
                $gateways[$gateway->id] = $gateway->title;
            }
        }
        return $gateways;
    }
}

if( !function_exists( 'ywcds_get_donations_orders' ) ){

    function ywcds_get_donations_orders( $order_data_from='', $order_data_to='' ){

        global $wpdb;

        $query_data =   '';

        if( $order_data_from != '' && $order_data_to!='' )
            $query_data=" AND {$wpdb->posts}.post_date >= '".$order_data_from."' AND {$wpdb->posts}.post_date < '".$order_data_to."'";

        $query  =   "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                     WHERE {$wpdb->posts}.post_type='shop_order' AND {$wpdb->postmeta}.meta_key='_ywcds_order_has_donation' AND {$wpdb->postmeta}.meta_value='true' AND {$wpdb->posts}.post_status ='wc-completed'".$query_data;

        return $wpdb->get_col( $query );

    }
}
if( !function_exists('ywcds_get_donations_item' ) ){

    function ywcds_get_donations_item( $order_id ){

        $items_line      = array();

        $order   =   wc_get_order( $order_id );

        $donation_id    =   get_option('_ywcds_donation_product_id');

        $is_wc_version_3_0 = version_compare( WC()->version, '3.0.0','>=' );
        foreach( $order->get_items() as $items ){

            if( $items['product_id']    == $donation_id ){

                if( $is_wc_version_3_0 ){

                    $name  = $items->get_meta('_ywcds_donation_name');
                    $name = $name !=='' ? $name : $items['name'];

                }else{
                    $name = isset( $items['item_meta']['_ywcds_donation_name'] ) ? $items['item_meta']['_ywcds_donation_name'][0] : $items['name'];
                }


                $total =    $items['line_total'];

                $items_line[]   =   array(
                        'product_name'  =>  $name,
                        'total'         =>  $total
                        );
            }
        }


        return $items_line;
    }
}


if( !function_exists( 'ywcds_get_min_donation' ) ) {
	function ywcds_get_min_donation() {
		return apply_filters( 'ywcds_get_minimum_donation', get_option( 'ywcds_min_donation' ) );
	}
}

if( !function_exists( 'ywcds_get_max_donation' ) ) {
	function ywcds_get_max_donation() {
		return apply_filters( 'ywcds_get_maximum_donation', get_option( 'ywcds_max_donation' ) );
	}
}



if( !function_exists( 'ywcds_add_gutenberg_block' ) ){

	function ywcds_add_gutenberg_block(){

		$block = array(
				'yith-wcds-donations' => array(
					'style' => 'ywcds_style_frontend',
					'title' => _x( 'Donation Form', '[gutenberg]: donation form', 'yith-donations-for-woocommerce'),
					'description' => _x( 'Add a simple form to let your customers add donations to the cart!', '[gutenberg]: Add a simple form to let your customers add donations to the cart!', 'yith-donations-for-woocommerce'),
					'shortcode_name' => 'yith_wcds_donations',
					'keywords' => array( _x( 'Donation Form', '[gutenberg]: donation form', 'yith-donations-for-woocommerce') ),
					'attributes' => array(
						'donation_amount' => array(
							'type' => 'text',
							'label' => _x('Donation pre-set amounts. Enter the available donation amounts that your users can choose from. Separate values with |', '[gutenberg]: Donation pre-set amounts', 'yith-donations-for-woocommerce'),
							'default' => ''
						),
						'donation_amount_style' => array(
							'type' => 'select',
							'label' =>  __( 'Style','yith-donations-for-woocommerce' ),
							'options' => array(
								'radio' => __( 'Radio Button', 'yith-donations-for-woocommerce' ),
								'label'    => __( 'Label', 'yith-donations-for-woocommerce' )
							),
							'default' => 'label'
						),
						'show_extra_desc' => array(
							'type' => 'select',
							'label' => __('Show an extra field in the donation form', 'yith-donations-for-woocommerce' ),
							'default' => 'off',
							'options' =>array( 'on' => __('Yes', 'yith-donations-for-woocommerce' ), 'off' => __('No' , 'yith-donations-for-woocommerce' ) )

						),

						'extra_desc_label' => array(
							'type' => 'text',
							'label' => __('Show an extra field in the donation form','yith-donations-for-woocommerce' ),
							'default' => ''
						),

						'button_text' => array(
							'type' => 'text',
							'default' => get_option( 'ywcds_button_text' ),
							'label' => __( 'Add Donation label', 'yith-donations-for-woocommerce')
 						)
					)

				),
			'yith-wcds-donations-summary' => array(
				'style' => 'ywcds_style_frontend',
				'title' => _x( 'Donation Summary', '[gutenberg]: donation summary', 'yith-donations-for-woocommerce'),
				'description' => _x( 'Show users the number of donations made so far!', '[gutenberg]:Show users the number of donations made so far!', 'yith-donations-for-woocommerce'),
				'shortcode_name' => 'yith_wcds_donations_summary',
				'keywords' => array( _x( 'Donation Summary', '[gutenberg]: donation summary', 'yith-donations-for-woocommerce') ),
				'attributes' => array(
					'summary_from' =>array(
						'type' => 'select',
						'label' => __('Show donations of','yith-donations-for-woocommerce'),
						'options' => array(
							'day' => __('Today', 'yith-donations-for-woocommerce-premium'),
							'week' => __('Last week','yith-donations-for-woocommerce'),
							'last_month' => __('Last month', 'yith-donations-for-woocommerce'),
							'month' => __('This month','yith-donations-for-woocommerce'),
							'year' => __('Last year','yith-donations-for-woocommerce'),
							'always' => __('Ever','yith-donations-for-woocommerce')
						),
						'default' => 'week'
					),
					'include_tax' => array(
						'type' => 'select',
						'label' => __('Include tax in total', 'yith-donations-for-woocommerce'),
						'options' => array(
							'off' => __('No', 'yith-donations-for-woocommerce'),
							'on' => __('Yes','yith-donations-for-woocommerce')
						),
						'default' => 'off'
					)
				)
			)
		);

		yith_plugin_fw_gutenberg_add_blocks( $block );
	}
}

if ( ! function_exists( 'ywcds_synchronize_product' ) ) {
	function ywcds_synchronize_product() {

		$synchronized = get_option( 'ywcds_sychronized_product', false );
		global $sitepress;

		if ( ! $synchronized && ! is_null( $sitepress ) ) {
			$paged = 1;
			$args  = array(
				'post_type'       => 'product',
				'post_status'     => 'publish',
				'posts_per_page'  => 15,
				'paged'           => $paged,
				'fields'          => 'ids',
				'meta_query'      => array(
					array(
						'key'     => '_ywcds_donation_associate',
						'compare' => 'EXISTS'
					)
				),
				'suppress_filter' => true,
			);

			$product_ids = get_posts( $args );

			while ( count( $product_ids ) > 0 ) {

				foreach ( $product_ids as $product_id ) {


					$associate  = get_post_meta( $product_id, '_ywcds_donation_associate', true );
					$compulsive = get_post_meta( $product_id, '_ywcds_donation_obligatory', true );
					$translation_ids = $sitepress->get_element_translations( $product_id, 'product' );

					foreach( $translation_ids as $translation ){
						$translated_id = $translation->element_id;
						update_post_meta( $translated_id, '_ywcds_donation_associate', $associate );
						update_post_meta( $translated_id, '_ywcds_donation_obligatory', $compulsive );
					}


				}

				$paged ++;

				$args['paged'] = $paged;

				$product_ids = get_posts( $args );
			}
		}

		update_option( 'ywcds_sychronized_product', true );

	}
}

add_action( 'admin_init', 'ywcds_synchronize_product' );
