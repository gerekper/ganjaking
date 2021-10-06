<?php

/**
 * Description of A2W_PluginUpdateController
 *
 * @author Andrey
 * 
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_PluginUpdateController')) {

    class A2W_PluginUpdateController extends A2W_AbstractController {

        private $update;

        public function __construct() {

            $this->update = new A2W_Update(A2W()->version, a2w_get_setting('api_endpoint').'update.php', A2W()->plugin_name, '19821022', a2w_get_setting('item_purchase_code'));

            //add_action('in_plugin_update_message-ali2woo/ali2woo.php', array($this, 'plugin_update_message'), 10, 3);
        }
        
        public function plugin_update_message($plugin_file, $plugin_data='', $status=''){
            echo ' <em><a href="'.admin_url( 'admin.php?page=a2w_setting').'">Register</a> your copy of plugin to receive access to automatic upgrades and support.</em>';
        }

    }

}
