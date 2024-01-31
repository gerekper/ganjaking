<?php

/**
 * Description of Migrate
 *
 * @author Ali2Woo Team
 * 
 * @autoload: a2w_admin_init
 */

namespace Ali2Woo;

class Migrate {
    public function __construct() {
        $this->migrate();
    }
    
    public function migrate(){
        $cur_version = get_option('a2w_db_version', '');
        if(version_compare($cur_version, "3.0.8", '<')) {
            $this->migrate_to_308();
        }

        if(version_compare($cur_version, A2W()->version, '<')) {
            update_option('a2w_db_version', A2W()->version, 'no');
        }
    }

    private function migrate_to_308(){
        a2w_error_log('migrate to 3.0.8');
        ProductShippingMeta::clear_in_all_product();;
    }
}
