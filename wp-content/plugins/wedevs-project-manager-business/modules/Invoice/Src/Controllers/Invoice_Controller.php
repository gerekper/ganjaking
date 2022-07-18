<?php
namespace WeDevs\PM_Pro\Modules\Invoice\Src\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Settings\Controllers\Settings_Controller;
use WeDevs\PM\Settings\Transformers\Settings_Transformer;
use WeDevs\PM_Pro\Modules\Invoice\Src\Models\Invoice;
use WeDevs\PM_Pro\Modules\Invoice\Src\Transformers\Invoice_Transformer;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM_Pro\Modules\Invoice\Core\PDF\PDF;
use WeDevs\PM\Core\Notifications\Email;
use Illuminate\Pagination\Paginator;
use WeDevs\PM_Pro\Modules\Invoice\Core\Permission\Payment;

class Invoice_Controller {

    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $per_page = $request->get_param( 'per_page' );
        $per_page = $per_page ? $per_page : 15;
        $frontend = $request->get_param( 'frontend' );

        $page = $request->get_param( 'page' );
        $page = $page ? $page : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        if ( $project_id && empty( $frontend ) ) {
            $invoices = Invoice::where( 'project_id', $project_id )
                ->orderBy( 'created_at', 'DESC' )
                ->paginate( $per_page, ['*'] );

        } else if ( $project_id && ! empty( $frontend ) ) {
            $invoices = Invoice::where( 'project_id', $project_id )
                ->where( 'client_id', get_current_user_id() )
                ->orderBy( 'created_at', 'DESC' )
                ->paginate( $per_page, ['*'] );
        } else {
            $invoices = Invoice::where( 'client_id', get_current_user_id() )
                ->orderBy( 'created_at', 'DESC' )
                ->paginate( $per_page, ['*'] );
        }


        $invoice_collection = $invoices->getCollection();

        $resource = new Collection( $invoice_collection, new Invoice_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $invoices ) );
        $resource->setMeta(
            [
                'client_addresses' => $this->get_clients_address( $resource )
            ]
        );

        return $this->get_response( $resource );
    }

    public function get_clients_address( $resource ) {
        $items = $this->get_response( $resource );
        $address = [];

        foreach ( $items['data'] as $key => $item ) {

            if (
                empty( $item['client_id'] )
                    &&
                array_key_exists( $item['client_id'], $address )
            ) {
                continue;
            }

            $client_id = $item['client_id'];
            $address[$client_id] = Invoice_Transformer::get_client_address( $client_id );
        }

        return $address;
    }

    public function show( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $invoice_id = $request->get_param( 'invoice_id' );

        return $this->get_invoice( $project_id, $invoice_id );
    }

    public function get_invoice( $project_id, $invoice_id ) {
        $invoice_board  = Invoice::where( 'id', $invoice_id )
            ->where( 'project_id', $project_id )
            ->first();

        $resource = new Item( $invoice_board, new Invoice_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        $psotdata = $request->get_params();
        $project_id = $request->get_param('project_id');

        $psotdata['start_at']['date'] = empty( $psotdata['start_at']['date'] )
            ? current_time( 'mysql' )
            : $psotdata['start_at']['date'];

        $psotdata['due_date']['date'] = empty( $psotdata['due_date']['date'] )
            ? current_time( 'mysql' )
            : $psotdata['due_date']['date'];

        $data = [
            'client_id'      => $psotdata['client_id'],
            'title'          => $psotdata['title'],
            'start_at'       => date( 'Y-m-d', strtotime( $psotdata['start_at']['date'] ) ),
            'due_date'       => date( 'Y-m-d', strtotime( $psotdata['due_date']['date'] ) ),
            'discount'       => floatval( $psotdata['discount'] ),
            'partial'        => intval( $psotdata['partial'] ) ? 1 : 0,
            'partial_amount' => floatval( $psotdata['partial_amount'] ),
            'terms'          => $psotdata['terms'],
            'client_note'    => $psotdata['client_notes'],
            'status'         => 0,
            'project_id'     => $project_id,
            'items' => maybe_serialize([
                'entryTasks' => $psotdata['entryTasks'],
                'entryNames' => $psotdata['entryNames'],
            ])
        ];

        $invoice = Invoice::create( $data );

        $resource = new Item( $invoice, new Invoice_Transformer );
        $message = [
            'message' => pm_get_text('success_messages.discuss_created')
        ];

        $response = $this->get_response( $resource, $message );

        return $response;
    }

    public function filter_data() {

    }

    public function update( WP_REST_Request $request ) {
        $psotdata   = $request->get_params();
        $project_id = $request->get_param( 'project_id' );
        $invoice_id = $request->get_param( 'invoice_id' );

        $psotdata['start_at']['date'] = empty( $psotdata['start_at']['date'] )
            ? current_time( 'mysql' )
            : $psotdata['start_at']['date'];

        $psotdata['due_date']['date'] = empty( $psotdata['due_date']['date'] )
            ? current_time( 'mysql' )
            : $psotdata['due_date']['date'];

        $data = [
            'client_id'      => $psotdata['client_id'],
            'title'          => $psotdata['title'],
            'start_at'       => date( 'Y-m-d', strtotime( $psotdata['start_at']['date'] ) ),
            'due_date'       => date( 'Y-m-d', strtotime( $psotdata['due_date']['date'] ) ),
            'discount'       => floatval( $psotdata['discount'] ),
            'partial'        => intval( $psotdata['partial'] )  ? 1 : 0,
            'partial_amount' => floatval( $psotdata['partial_amount'] ),
            'terms'          => $psotdata['terms'],
            'client_note'    => $psotdata['client_notes'],
            'items' => maybe_serialize([
                'entryTasks' => $psotdata['entryTasks'],
                'entryNames' => $psotdata['entryNames'],
            ])
        ];

        $invoice = Invoice::where( 'project_id', $project_id )
            ->where( 'id', $invoice_id )
            ->first();

        if ($invoice) {
            $invoice->update_model( $data );
        }


        $resource = new Item( $invoice, new Invoice_Transformer );

        $message = [
            'message' => pm_get_text('success_messages.task_updated')
        ];
        return $this->get_response( $resource, $message );
    }

    public function destroy( WP_REST_Request $request ) {
        $invoice_id = $request->get_param('invoice_id');
        $invoice = Invoice::find( $invoice_id );
        $invoice->metas()->delete();
        $invoice->delete();

         $message = [
            'message' => pm_get_text('success_messages.invoice_deleted')
        ];

        return $this->get_response(false, $message);
    }

    public static function save_user_address( WP_REST_Request $request ) {
        $user_id = $request->get_param('user_id');
        $address = $request->get_params();
        if ( empty( $user_id ) ) {
            return;
        }
        unset($address['user_id']);

        update_user_meta( $user_id, 'pm_invoice_address', $address );

        $message = [
            'message' => 'success_messages.setting_saved'
        ];

        return $address;
    }

    public function get_user_address(WP_REST_Request $request ) {
        $user_id = $request->get_param('user_id');

        return get_user_meta( $user_id, 'pm_invoice_address', true );
    }

    public function address( WP_REST_Request $request ) {
        $address = $request->get_params();
        $invoice = pm_get_setting('invoice');
        $invoice = $invoice ? $invoice : [];

        foreach ( $address as $name => $value) {
            $invoice[$name] = $value;
        }

        $invoice_settings = Settings_Controller::save_settings([
            'key' => 'invoice',
            'value' => $invoice
        ]);

        $resource = new Item( $invoice_settings, new Settings_Transformer );

        $message = [
            'message' => 'success_messages.setting_saved'
        ];

        return $this->get_response( $resource, $message );
    }

    public function payment(WP_REST_Request $request) {
        $postdata   = $request->get_params();
        $project_id = absint( $request->get_param('project_id') );
        $invoice_id = absint( $request->get_param('invoice_id') );

        $data = [
            'invoice_id'     => $invoice_id,
            'amount'         => floatval( $postdata['amount'] ),
            'paymentDate'    => $postdata['paymentDate'],
            'paymentNotes'   => $postdata['paymentNotes'],
            'paymentGateway' => $postdata['paymentGateway'],
            'project_id'     => $project_id
        ];

        $this->payment_warper( $data );

        return $this->show($request);
    }

    public static function get_payment_status( $data ) {
        $project_id = $data['project_id'];
        $invoice_id = $data['invoice_id'];

        $invoice = Invoice::where( 'project_id', $project_id )
            ->where( 'id', $invoice_id )->first();

        $invoice = new Item( $invoice, new Invoice_Transformer );
        $invoice = pm_get_response( $invoice, [] );
        $invoice_total = pm_pro_invoice_get_invoice_total($invoice['data']['entryTasks'], $invoice['data']['entryNames'], $invoice['data']['discount']);
        $due_amount  = pm_pro_invoice_get_total_due( $invoice['data'] );

        $amount = round( $data['amount'], 4 );
        $invoice_total = round( $invoice_total, 4 );
        $due_amount = round( $due_amount, 4 );

        if ( $amount >= $due_amount ) {
            return 1;
        }

        if ( $amount < $invoice_total ) {
            return 2;
        }

        return 0;
    }

    public static function payment_warper( $data ) {
        $payment_date = empty( $data['paymentDate'] )
            ? current_time( 'mysql' )
            : $data['paymentDate'];

        $status = self::get_payment_status( $data );

        $invoice = Invoice::where( 'project_id', $data['project_id'] )
            ->where( 'id', $data['invoice_id'] )
            ->first();

        if ($invoice) {
            $invoice->update_model( ['status' => $status] );
        }

        $data = [
            'entity_id'   => $data['invoice_id'],
            'entity_type' => 'invoice',
            'meta_key'    => 'invoice_payment',
            'meta_value'  => maybe_serialize([
                'amount'  => floatval( $data['amount'] ),
                'date'    => date( 'Y-m-d', strtotime( $payment_date ) ),
                'notes'   => $data['paymentNotes'],
                'gateway' => $data['paymentGateway']
            ]),
            'project_id' => $data['project_id']
        ];

        return Meta::create($data);
    }

    public function PDF( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $invoice_id = $request->get_param( 'invoice_id' );
        $invoice    = $this->get_invoice( $project_id, $invoice_id );
        $invoice    = $invoice['data'];

        return $this->get_PDF( $invoice );
    }

    public function get_PDF( $invoice, $options = [] ) {
        $default = [
            'stream'               => true,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
        ];

        $options          = wp_parse_args( $options, $default );
        $invoice_settings = pm_get_setting( 'invoice' );
        $currency_code    = empty( $invoice_settings['currency_code'] ) ? 'USD' : $invoice_settings['currency_code'];
        $client_address   = get_user_meta( $invoice['client_id'], 'pm_invoice_address', true );
        $countries        = require_once PM_PRO_INVOICE_PATH . 'includes/ISO_Country_Code.php';
        $currency_symbols = require_once PM_PRO_INVOICE_PATH . 'includes/Currency_Symbols.php';

        $currency_symbol  = empty( $currency_symbols[$currency_code] ) ? 'USD' : html_entity_decode($currency_symbols[$currency_code]);

        ob_start();
            require_once PM_PRO_INVOICE_PATH . '/views/PDF/PDF.php';
        $print_invoice = ob_get_clean();

        return PDF::generator( $print_invoice, $options );
    }

    public function payment_validation( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $invoice_id = $request->get_param( 'invoice_id' );
        $postdata = $request->get_params();

        return Payment::payment_validation($project_id, $invoice_id, $postdata);
    }

    public function mail( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $invoice_id = $request->get_param( 'invoice_id' );
        $invoice    = $this->get_invoice( $project_id, $invoice_id );
        $invoice    = $invoice['data'];
        $project_title = $invoice['project']['data']['title'];

        $pdf_output = $this->get_PDF( $invoice, [
            'stream' => false
        ]);

        $user     = get_userdata( $invoice['client_id'] );
        $subject  = $invoice['title'];
        $to       = $user->user_email;
        $id       = str_replace( ' ', '_', $subject ) . '_' . time() . mt_rand( '1111111', '9999999' );
        $pdf_file = PM_PRO_INVOICE_PATH . "/temp/{$id}.pdf";

        file_put_contents( $pdf_file, $pdf_output );

        if ( !file_exists( $pdf_file ) ) {
            return false;
        }

        $attachments   = array();
        $headers       = '';
        $text_body     = nl2br("Dear {$user->data->display_name},\r\nYou have an invoce attachment from the project $project_title \r\nPlease, check the attachment.");
        $attachments[] = $pdf_file;

        $email_status = Email::send( $to, $subject, $text_body, [], $attachments);

        unlink( $pdf_file );

        return $email_status;
    }

    public function gateway_payment( WP_REST_Request $request ) {
        $project_id = $request->get_param('project_id');
        $data       = $request->get_param('data');
        $amount     = $data['amount'];
        $client_id  = $data['client_id'];
        $invoice_id = $request->get_param('invoice_id');

        $payment_data = [
            'headers' => [
                'content-type' => 'application/json',
                'Authorization' => 'Bearer <access_token$sandbox$pr265wzmrjrmyrr6$ed3cfb5df2884f364087cd9b2b2dd1e0>'
            ],

            'body' => [
                'intent' => 'sale',
                'payer' => [
                    "payment_method" => "paypal"
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => '30.11',
                            'currency' => 'USD',
                        ]
                    ],
                    "invoice_number" => "48787589673",
                ]
            ],
        ];

        $paypal_args = array(
            'cmd' => '_xclick',
            'amount' => '0.3',
            'business' => '', //'joy.mishu5-facilitator@gmail.com',
            'item_name' => 'lksdjflkasd',
            'item_number' => '39485673',
            'email' => '', // 'joy.mishu@gamil.com',
            'no_shipping' => '1',
            'no_note' => '1',
            'currency_code' => 'USD',
            'charset' => 'UTF-8',
            'custom' => '{}',
            'rm' => '2',
        );

        $paypal_url = 'https://www.sandbox.paypal.com/webscr/';

        //wp_remote_post($paypal_url, $payment_data);

        $paypal_url = $paypal_url . '?' . http_build_query( $paypal_args );

        wp_redirect( $paypal_url );

        die();
//https://www.sandbox.paypal.com/webscr/?cmd=_xclick&amount=0.3&business=joy.mishu5-facilitator%40gmail.com&item_name=lksdjflkasd&item_number=39485673&email=joy.mishu%40gamil.com&no_shipping=1&no_note=1&currency_code=USD&charset=UTF-8&custom=%7B%7D&rm=2

    }
}


