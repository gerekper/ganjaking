<?php
namespace WeDevs\PM_Pro\Modules\Invoice\Core\Paypal;

use WeDevs\PM_Pro\Modules\Invoice\Src\Controllers\Invoice_Controller;

class Paypal {

    public function __construct() {
    	add_action( 'init', [ $this, 'paypal_validation'] );
    }

	public function paypal_validation() {
	    // $test = Array (
	    //     'payer_email'            => 'joy.mishu5@gmail.com',
	    //     'payer_id'               => '9QLPK364BPCM6',
	    //     'payer_status'           => 'UNVERIFIED',
	    //     'first_name'             => 'Asaquzzaman',
	    //     'last_name'              => 'Mishu',
	    //     'txn_id'                 => '0V622453FP9770117',
	    //     'mc_currency'            => 'USD',
	    //     'mc_fee'                 => '0.32',
	    //     'mc_gross'               => '5',
	    //     'protection_eligibility' => 'ELIGIBLE',
	    //     'payment_fee'            => '0.32',
	    //     'payment_gross'          => '5',
	    //     'payment_status'         => 'Completed',
	    //     'payment_type'           => 'instant',
	    //     'item_name'              => 'sdrgadg',
	    //     'item_number'            => '44',
	    //     'quantity'               => '1',
	    //     'txn_type'               => 'web_accept',
	    //     'payment_date'           => '2018-02-19T04:12:14Z',
	    //     'business'               => 'joy.mishu5-facilitator@gmail.com',
	    //     'receiver_id'            => 'LAB7J2PKBCFWE',
	    //     'notify_version'         => 'UNVERSIONED',
	    //     'custom'                 => '{\"invoice_id\":46,\"user_id\":20,\"project_id\":5,\"gateway\":\"https://www.sandbox.paypal.com/webscr/\"}',
	    //     'verify_sign'            => 'AFe8SRo0RnXYPgKJ9baHubmmAGaTA76UVrLy697QSl8giGc0wz9wBdBw',
	    // );

	    if ( !isset( $_POST['verify_sign'] ) ) {
	        return;
	    }

	    $validate = $this->pm_pro_invoice_validateIpn();

	    if( $validate ) {
			$custom = json_decode( stripcslashes( $_POST['custom'] ) );
			$amount = $_POST['mc_gross'];
			$date   = $_POST['payment_date'];
			$note   = sprintf( __( 'Payment from PayPal, Txn: %s' ), $_POST['txn_id'] );

	        pm_add_meta( $custom->invoice_id, $custom->project_id, 'invoice_paypal_txn_id', 'txn_id', $_POST['txn_id'] );

	        $data = [
				'invoice_id'     => $custom->invoice_id,
				'amount'         => $_POST['mc_gross'],
				'paymentDate'    => $_POST['payment_date'],
				'paymentNotes'   => '',
				'paymentGateway' => 'paypal',
				'project_id'     => $custom->project_id
	        ];

	        Invoice_Controller::payment_warper( $data );
	    }
	}

	/**
	 * Validate the IPN notification
	 *
	 * @param none
	 * @return boolean
	 */
	public function pm_pro_invoice_validateIpn() {

	    $custom = json_decode( stripcslashes($_POST['custom']) );

	    // $txn_record = pm_get_meta( $custom->invoice_id, $custom->project_id, 'invoice_paypal_txn_id', 'txn_id' );

	    // if ( !$txn_record ) {
	    //     return false;
	    // }

	    // if( $txn_record['meta_value'] == $_POST['txn_id'] ) {
	    //     return false;
	    // }

	    global $wp_version;

	    // Get recieved values from post data
	    $ipn_data = (array) stripslashes_deep( $_POST );
	    $ipn_data['cmd'] = '_notify-validate';

	    // Send back post vars to paypal
	    $params = array(
	        'body' => $ipn_data,
	        'sslverify' => false,
	        'timeout' => 30,
	        'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
	    );

	    $response = wp_remote_post( $custom->gateway, $params );

	    if ( !is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && (strcmp( $response['body'], "VERIFIED" ) == 0) ) {
	        return true;
	    } else {
	        return false;
	    }
	}
}
