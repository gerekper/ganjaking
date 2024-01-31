<?php

/**
 * Description of ImportProcess
 *
 * @author Ali2Woo Team
 * 
 */

namespace Ali2Woo;

class ImportProcess extends \WP_Background_Process {
    
    protected $action = 'a2w_import_process';

    public function __construct() {
        parent::__construct();
    }

    public function schedule_event(){
        parent::schedule_event();
    }

    /**
     * Task
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        a2w_init_error_handler();
        try {
            $ts = microtime(true);
            a2w_info_log("START_STEP[id:".$item['product_id'].", extId: ".$item['id'].", step: ".$item['step']."]");

            if(substr($item['step'], 0, strlen('reviews')) === 'reviews'){
                if(get_setting('load_review')){
                    $reviews_model = new Review();

                    $result = $reviews_model->load($item['product_id'], true, array('step'=>$item['step']));

                    if(!empty($result['new_steps'])) {
                        // add new steps to new queue
                        ImportProcess::create_new_queue($item['product_id'], $item['id'], $result['new_steps'], false);
                    }

                    if($item['step']=='reviews'){
                        add_filter($this->identifier . '_time_exceeded', array($this, 'finish_iteration'));
                    }

                    if ($result['state'] === 'error') {
                        throw new \Exception($result['message']);
                    }
                }
            }else{
                /** @var $woocommerce_model  Woocommerce */ 
                $woocommerce_model = A2W()->getDI()->get('Ali2Woo\Woocommerce');
                $product_import_model = new ProductImport();

                $product = $product_import_model->get_product($item['id'], true);

                unset($product_import_model);

                if ($product) {
                    $result = $woocommerce_model->add_product($product, $item);

                    unset($woocommerce_model, $product);

                    if(!empty($result['new_steps'])){
                        // add new steps to new queue
                        ImportProcess::create_new_queue($item['product_id'], $item['id'], $result['new_steps']);
                    }

                    if(
                        // (!get_setting('use_external_image_urls') && substr($item['step'], 0, strlen('preload_images')) === 'preload_images') || 
                        $item['step']=='finishing'
                    ){
                        add_filter($this->identifier . '_time_exceeded', array($this, 'finish_iteration'));
                    }
                    
                    if ($result['state'] === 'error') {
                        throw new \Exception($result['message']);
                    }
                } else {
                    throw new \Exception('product not found in import list');
                }    
            }

            a2w_info_log("DONE_STEP[time: ".(microtime(true)-$ts).", id:".$item['product_id'].", extId: ".$item['id'].", step: ".$item['step']."]");
            
        } catch (\Throwable $e) {
            a2w_print_throwable($e);
        } catch (\Exception $e) {
            a2w_print_throwable($e);
        }

        return false;
    }

    public function finish_iteration($res) {
        return true;
    } 

    public static function init() {
        new ImportProcess();
    }

    public static function create_new_queue($product_id, $external_id, $steps, $start = true) {
        $new_queue = new ImportProcess();
        foreach($steps as $step) {
            $new_queue->push_to_queue(array('id'=>$external_id, 'step'=>$step, 'product_id'=>$product_id));
            a2w_info_log("ADD_STEP[id:".$product_id.", extId: ".$external_id.", step: ".$step."]");
        }
        $new_queue->save();
        if($start){
            $new_queue->dispatch();
        }
        return $new_queue;
    }

    public function num_in_queue() {
        global $wpdb;

        $table  = $wpdb->options;
        $column = 'option_name';

        if ( is_multisite() ) {
            $table  = $wpdb->sitemeta;
            $column = 'meta_key';
        }

        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

        $count = $wpdb->get_var( $wpdb->prepare( "
        SELECT COUNT(*)
        FROM {$table}
        WHERE {$column} LIKE %s
        ", $key ) );

        return $count;
    }

    public function clean_queue() {
        global $wpdb;

        $table        = $wpdb->options;
        $column       = 'option_name';
        $key_column   = 'option_id';
        $value_column = 'option_value';

        if ( is_multisite() ) {
            $table        = $wpdb->sitemeta;
            $column       = 'meta_key';
            $key_column   = 'meta_id';
            $value_column = 'meta_value';
        }

        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

        $query = $wpdb->get_results( $wpdb->prepare( "
        SELECT *
        FROM {$table}
        WHERE {$column} LIKE %s
        ORDER BY {$key_column} ASC
        ", $key ) );

        foreach ( $query as $row ) {
            $this->delete( $row->$column );
        }
    }
}
