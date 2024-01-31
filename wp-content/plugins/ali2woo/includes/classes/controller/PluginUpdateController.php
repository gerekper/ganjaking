<?php

/**
 * Description of PluginUpdateController
 *
 * @author Ali2Woo Team
 * 
 * @autoload: a2w_admin_init
 */

namespace Ali2Woo;

class PluginUpdateController extends AbstractController {

    public function __construct() {
        new Update(
            A2W()->version,
            get_setting('api_endpoint').'update.php',
            A2W()->plugin_name, '19821022',
            get_setting('item_purchase_code')
        );
    }
}

