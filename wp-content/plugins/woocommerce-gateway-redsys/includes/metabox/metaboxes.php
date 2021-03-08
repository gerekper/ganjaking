<?php

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function add_redsys_meta_box() {
	if (  WCRed()->is_redsys_order( get_the_ID() ) ) {
		
		$date   = WCRed()->get_order_date( get_the_ID() );
		$hour   = WCRed()->get_order_hour( get_the_ID() );
		$auth   = WCRed()->get_order_auth( get_the_ID() );
		$number = WCRed()->get_order_mumber( get_the_ID() );
		
		echo '<h4>' . esc_html__('Payment Details', 'woocommerce-redsys') . '</h4>';
		echo '<p><strong>' . esc_html__( 'Paid with', 'woocommerce-redsys' ) . ': </strong><br />' . WCRed()->get_gateway( get_the_ID() )  . '</p>';
		if ( $number ) {
			echo '<p><strong>' . esc_html__( 'Redsys Order Number', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $number ) . '</p>';
		}
		if ( $date ) {
			echo '<p><strong>' . esc_html__( 'Redsys Date', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $date ) . '</p>';
		}
		
		if ( $hour ) {
			echo '<p><strong>' . esc_html__( 'Redsys Hour', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $hour ) . '</p>';
		}
		
		if ( $auth ) {
			echo '<p><strong>' . esc_html__( 'Redsys Authorisation Code', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $auth ) . '</p>';
		}
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' );

function redsys_tab( $tabs ) {
	$tabs['redsys'] = array(
		'label'  => __( 'Redsys', 'woocommerce-redsys' ),
		'target' => 'redsys',
		'class'  => array(),
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'redsys_tab' );

// Adding content to custom panel
function redsys_tab_panel() {
	?>
	<div id="redsys" class="panel woocommerce_options_panel">
		<div class="options_group">
			<?php
			$field = array(
				'id'          => '_redsystokenr',
				'label'       => __( 'Get Redsys Token', 'woocommerce-redsys' ),
				'description' => __( 'This option will get a special token that doesn\'t need the customer verification. Only get it if you need it. For example, you need to change orders created by you.', 'woocommerce-redsys' ) 
			);
			woocommerce_wp_checkbox( $field );
			?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'redsys_tab_panel' );

// Saving data
function save_redsys_product( $post_id ) {

	$product      = wc_get_product( $post_id );
	$redsystokenr = isset( $_POST['_redsystokenr'] ) ? $_POST['_redsystokenr'] : 'no';
	$product->update_meta_data( '_redsystokenr', $redsystokenr );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_redsys_product' );
