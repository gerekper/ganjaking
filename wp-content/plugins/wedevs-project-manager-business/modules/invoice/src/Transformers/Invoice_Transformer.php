<?php

namespace WeDevs\PM_Pro\Modules\invoice\src\Transformers;

use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\invoice\src\Models\Invoice;
use WeDevs\PM_Pro\Modules\invoice\src\Transformers\Invoice_Meta_Transformer;
use WeDevs\PM\Project\Transformers\Project_Transformer;

class Invoice_Transformer extends TransformerAbstract {

    use Resource_Editors;

    protected $defaultIncludes = [
        'payments', 'project'
    ];

    protected $availableIncludes = [
        
    ];

    public function transform( Invoice $item ) {
        
        return [
            'id'             => $item->id,
            'project_id'     => $item->project_id,
            'status'         => $item->status,
            'client_id'      => $item->client_id,
            'title'          => $item->title,
            'start_at'       => format_date( make_carbon_date( $item->start_at ) ),
            'due_date'       => format_date( make_carbon_date( $item->due_date ) ),
            'discount'       => $item->discount,
            'partial'        => $item->partial,
            'partial_amount' => $item->partial_amount,
            'terms'          => $item->terms,
            'client_notes'   => $item->client_note,
            'entryTasks'     => $this->filter_items( maybe_unserialize( $item->items ), 'task' ),
            'entryNames'     => $this->filter_items( maybe_unserialize( $item->items ), 'name' ),
            'client_address' => $this->get_client_address( $item->client_id )
        ];
    }

    public function includePayments( Invoice $item ) {
        $invoice_meta = $item->metas()->get();

        return $this->collection( $invoice_meta, new Invoice_Meta_Transformer );
    }

    public function includeProject( Invoice $item ) {
        $project = $item->project()->first();

        return new Item( $project, new Project_Transformer );
    }

    public static function get_client_address( $client_id ) {
        return get_user_meta( $client_id, 'pm_invoice_address', true );
    }

    public function filter_items( $items, $type ) {
        if ( $type == 'task' ) {
            return empty( $items['entryTasks'] ) ? [] : $items['entryTasks'];
        }  

        return empty( $items['entryNames'] ) ? [] : $items['entryNames']; 
    }
}