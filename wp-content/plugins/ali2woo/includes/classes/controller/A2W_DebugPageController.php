<?php

/**
 * Description of A2W_DebugPageController
 *
 * @author andrey
 *
 * @autoload: a2w_before_admin_menu
 */
if (!class_exists('A2W_DebugPageController')) {

    class A2W_DebugPageController extends A2W_AbstractAdminPage
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
}
