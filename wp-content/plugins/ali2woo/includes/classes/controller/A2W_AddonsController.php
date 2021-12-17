<?php
/**
 * Description of A2W_AddonsController
 *
 * @author andrey
 * 
 * @autoload: a2w_admin_init
 * 
 */

if (!class_exists('A2W_AddonsController')) {
    class A2W_AddonsController extends A2W_AbstractAdminPage {
        private $update_period = 3600; //60*60*1;
        private $addons;
        
        public function __construct() {
            $this->addons = get_option('a2w_addons_data', array());
            if(empty($this->addons['addons'])){
                $this->addons['addons'] = array();
            }
            $new_addons_cnt = is_admin()?$this->get_new_addons_count():0;
            
            parent::__construct(__('Add-ons/Extensions', 'ali2woo'), __('Add-ons', 'ali2woo'). ($new_addons_cnt ? ' <span class="update-plugins count-' . $new_addons_cnt . '"><span class="plugin-count">' . $new_addons_cnt . '</span></span>' : ''), 'manage_options', 'a2w_addons', 100);

            if (empty($this->addons['next_update']) || $this->addons['next_update'] < time()) {
                $request = a2w_remote_get(a2w_get_setting('api_endpoint').'addons.php');
                if (!is_wp_error($request) && intval($request['response']['code']) == 200) {
                    $this->addons['addons'] = json_decode($request['body'], true);
                }
                $this->addons['next_update'] = time() + $this->update_period;
                update_option('a2w_addons_data', $this->addons, 'no');
            }
        }

        public function render($params = array()) {
            $this->set_viewed_addons();
            $this->model_put('addons', $this->addons);
            $this->include_view('addons.php');
        }
        
        private function get_new_addons_count() {
            if(empty($this->addons['viewed_addons'])){
                return empty($this->addons['addons'])?0:count($this->addons['addons']);
            }else{
                $viewed_cnt = 0;
                $addons = !empty($this->addons['addons']) ? $this->addons['addons'] : array();
                foreach ($addons as $addon) {
                    if (in_array($addon['id'], $this->addons['viewed_addons'])) {
                        $viewed_cnt++;
                    }
                }
                return count($addons) - $viewed_cnt;
            }
        }
        
        private function set_viewed_addons() {
            $this->addons['viewed_addons'] = array();
            foreach ($this->addons['addons'] as $addon) {
                $this->addons['viewed_addons'][]=$addon['id'];
            }
            update_option('a2w_addons_data', $this->addons, 'no');
        }
    }
}
