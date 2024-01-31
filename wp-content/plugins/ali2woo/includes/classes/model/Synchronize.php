<?php

/**
 * Description of Synchronize
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;
    
class Synchronize {
    public function sync_products($ids, $action='add'){
        if(in_array($action, array('add', 'remove'))){
            $request_url = RequestHelper::build_request('sync_data');
            $data = array('type'=>'single', 'pc'=>$this->get_product_cnt(),'action'=>$action, 'ids'=>implode(',', (is_array($ids)?$ids:array($ids))));
            $request = a2w_remote_post($request_url, $data);
            if (is_wp_error($request)) {
                $request = a2w_remote_post($request_url, $data);
            }
        }
    }
    
    public function gloabal_sync_products(){
        $request_url = RequestHelper::build_request('sync_data');
        $data = array('type'=>'gloabal', 'pc'=>$this->get_product_cnt(),'data'=>gzcompress(implode(',', $this->get_product_ids()), 9));           
        $request = a2w_remote_post($request_url, $data);
        if (is_wp_error($request)) {
            $request = a2w_remote_post($request_url, $data);
        }
    }
    
    public function get_product_cnt(){
        global $wpdb;
        $cnt = $wpdb->get_var( "select count(pm.meta_value) from {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm on (p.ID=pm.post_id) WHERE p.post_status<>'trash' and pm.meta_key='_a2w_external_id'");
        return intval($cnt);
    }
    
    public function get_product_ids(){
        global $wpdb;
        $results = $wpdb->get_results( "select pm.meta_value as eid from {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm on (p.ID=pm.post_id) WHERE p.post_status<>'trash' and pm.meta_key='_a2w_external_id'", ARRAY_A);
        $ids = array();
        foreach($results as $r){
            $ids[] = $r['eid'];
        }
        return $ids;
    }
}
