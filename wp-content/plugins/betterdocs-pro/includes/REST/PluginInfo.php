<?php

namespace WPDeveloper\BetterDocsPro\REST;
use WPDeveloper\BetterDocs\Core\BaseAPI;

class PluginInfo extends BaseAPI {

    /**
     * @return mixed
     */
    public function register() {
        $this->get( '/plugin_info/', [$this, 'get_plugin_info'] );
    }

    public function get_plugin_info() {
        return [
            'betterdocs_dir_url'     => BETTERDOCS_PRO_ABSURL,
            'betterdocs_rest_url'    => get_rest_url(),
            'betterdocs_version'     => BETTERDOCS_VERSION,
            'betterdocs_pro_version' => BETTERDOCS_PRO_VERSION
        ];
    }
}
