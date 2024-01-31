<?php

/**
 * Description of TransferPageController
 *
 * @author Ali2Woo Team
 *
 * @autoload: a2w_admin_init
 */

namespace Ali2Woo;

class TransferPageController extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct(__('Transfer', 'ali2woo'), __('Transfer', 'ali2woo'), 'import', 'a2w_transfer', 95);
    }

    public function render($params = array())
    {
        $this->saveHandler();
        $this->model_put("hash", $this->getSettingsString());
        $this->include_view("transfer.php");
    }

    private function getSettingsString()
    {
        $settings = get_option('a2w_settings', array());

        $hash = base64_encode(serialize($settings));

        return $hash;
    }

    private function saveHandler()
    {
        if (isset($_POST['transfer_form']) && !empty($_POST['hash'])) {
            if (!$settings = base64_decode($_POST['hash'])) {
                $this->model_put("error", __('Hash is not correct', 'ali2woo'));
                return;
            }

            if (!$settings = unserialize($settings)) {
                $this->model_put("error", __('Hash is not correct', 'ali2woo'));
                return;
            }

            update_option('a2w_settings', $settings);
        }
    }
}
