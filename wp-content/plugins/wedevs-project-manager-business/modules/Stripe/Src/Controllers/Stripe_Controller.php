<?php

namespace WeDevs\PM_Pro\Modules\Stripe\Src\Controllers;

/**
 *
 */
use WP_REST_Request;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM_Pro\Modules\Invoice\Src\Models\Invoice;
use League\Fractal\Resource\Item as Item;
use WeDevs\PM_Pro\Modules\Invoice\Src\Transformers\Invoice_Transformer;
use WeDevs\PM_Pro\Modules\Invoice\Src\Controllers\Invoice_Controller;

class Stripe_Controller {

	public function gateway_stripe ( WP_REST_Request $request ) {
        $invoice_id     = (int) $request->get_param('invoice_id');
        $project_id     = (int) $request->get_param('project_id');
        $amount         = $request->get_param('amount');
        $stripe         = $request->get_param('stripe');
        $amount         =  isset(  $amount  ) ?  $amount  : 0;
        $invoice_model  = Invoice::find( $invoice_id );

        $invoice        = new Item( $invoice_model, new Invoice_Transformer );
        $invoice        = pm_get_response( $invoice, [] );
        $invoice_total  = pm_pro_invoice_get_invoice_total($invoice['data']['entryTasks'], $invoice['data']['entryNames'], $invoice['data']['discount']);

        $partial_amount = isset( $invoice['data']['partial_amount'] ) ? floatval($invoice['data']['partial_amount']) : 0;

        $paid_amount    = pm_pro_invoice_get_total_paid( $invoice['data']['payments']['data'] );
        $due_amount     = pm_pro_invoice_get_total_due( $invoice['data'] );
        $symbol         = pm_pro_get_invoice_currencey_symbol();

        $amount         = round( $amount, 4 );
        $due_amount     = round( $due_amount, 4 );
        $invoice_total  = round( $invoice_total, 4 );
        $partial_amount = round( $partial_amount, 4 );
        $paid_amount    = round( $paid_amount, 4 );


        $invoice_settings = pm_get_setting( 'invoice' );
        $currency_code    = empty( $invoice_settings['currency_code'] ) ? 'USD' : $invoice_settings['currency_code'];

        if ( !file_exists( pm_pro_config( 'define.module_path' ) . '/Stripe/libs/stripe-php/init.php' ) ) {
            return;
        }
        require_once pm_pro_config('define.module_path') . '/Stripe/libs/stripe-php/init.php';

        \Stripe\Stripe::setApiKey( $this->get_secret_key() );

        try {
            $return_data = \Stripe\Charge::create( array(
                "amount" => $amount*100,
                "currency" => $currency_code,
                "source" => $stripe['id'],
            ));

            if (  $return_data->paid  ) {
                $note = sprintf( __( 'Payment from Stripe, id: %s' ), $return_data->id );
                $data = [
                    'invoice_id'     => $invoice_id,
                    'amount'         => floatval( $amount ),
                    'paymentDate'    => current_time( 'mysql' ),
                    'paymentNotes'   => $note,
                    'paymentGateway' => 'stripe',
                    'project_id'     => $project_id
                ];

                Invoice_Controller::payment_warper( $data );
            }

            wp_send_json_success(
                [
                    'message' => __( 'Payment has been done successfully', 'pm-pro' )
                ]
            );

        } catch ( \Stripe\Error\Card $e) {
            return new \WP_Error( 'Stripe', $e->getJsonBody() );
        }
	}

    /**
     * Get the secret key
     *
     * @return string
     */
    function get_secret_key() {
        $invoice_settings = pm_get_setting( 'invoice' );
        if ( $invoice_settings['stripe_test_secret']  == 'true') {
            return $invoice_settings['secret_key' ];
        } else {
            return $invoice_settings['live_secret_key'];
        }
    }
}
