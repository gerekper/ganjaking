<?php

/**
 * Description of DebugPageController
 *
 * @author Ali2Woo Team
 *
 * @autoload: a2w_before_admin_menu
 */

namespace Ali2Woo;

class DebugPageController extends AbstractAdminPage
{

    public function __construct()
    {
        if (a2w_check_defined('A2W_DEBUG_PAGE')) {
            parent::__construct(__('Debug', 'ali2woo'), __('Debug', 'ali2woo'), 'edit_plugins', 'a2w_debug', 1100);
        }
    }

    public function render($params = array())
    {
        echo "<br/><b>DEBUG</b><br/>";
    }

}
