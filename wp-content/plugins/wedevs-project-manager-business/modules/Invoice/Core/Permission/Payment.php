<?php

namespace WeDevs\PM_Pro\Modules\Invoice\Core\Permission;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use WeDevs\PM_Pro\Modules\Invoice\Src\Models\Invoice;
use WeDevs\PM_Pro\Modules\Invoice\Src\Transformers\Invoice_Transformer;

class Payment extends Abstract_Permission {

    public function check() {
        $postdata   = $this->request->get_params();
        $project_id = absint( $this->request->get_param('project_id') );
        $invoice_id = absint( $this->request->get_param('invoice_id') );

        return self::payment_validation($project_id, $invoice_id, $postdata);
    }

    public static function payment_validation($project_id, $invoice_id, $postdata) {
        $amount     =  isset( $postdata['amount'] ) ? $postdata['amount'] : 0;
        $invoice = Invoice::where( 'project_id', $project_id )
            ->where( 'id', $invoice_id )->first();

        $partial = empty( $invoice['partial'] ) ? false : true;

        $invoice        = new Item( $invoice, new Invoice_Transformer );
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

        if ( $amount <= 0 ) {
            return new \WP_Error( 'partial_payment', __( "Please insert your payment amount", "pm" ) );
        }

        if ( $due_amount < $amount ) {
            return new \WP_Error( 'partial_payment', __( "Payment amount should be less than or equal due amount ({$symbol}{$due_amount})", "pm" ) );
        }

        if( intval( $invoice['data']['partial'] ) ) {
            if (
                $due_amount >= $partial_amount
                    &&
                $amount < $partial_amount
            ) {
                return new \WP_Error( 'partial_payment', __( "Payment amount should be greater than or equal to partial amount ({$symbol}{$partial_amount})", "pm" ) );
            } else if (
                $due_amount < $partial_amount
                    &&
                $amount < $due_amount
            ) {
                return new \WP_Error( 'partial_payment', __( "Payment amount should be equal to due amount ({$symbol}{$due_amount})", "pm" ) );
            }
        } else {
            if ( $amount != $due_amount ) {
                return new \WP_Error( 'partial_payment', __( "Payment amount should equeal due amount ({$symbol}{$due_amount})", "pm" ) );
            }
        }

        return true;
    }
}
