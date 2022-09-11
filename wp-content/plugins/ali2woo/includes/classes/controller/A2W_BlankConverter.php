<?php

/**
 * Description of A2W_BlankConverter
 *
 * @author Andrey
 *
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_BlankConverter')) {

    class A2W_BlankConverter extends A2W_AbstractAdminPage
    {
        public function __construct()
        {
            if(!apply_filters('a2w_converter_installed', false)){
                parent::__construct(__('Migration Tool', 'ali2woo'), __('Migration Tool', 'ali2woo'), 'import', 'a2w_converter', 1000);
            }
        }

        public function render($params = array())
        {
            ?>
            <h1><?php _e('Migration Tool', 'ali2woo'); ?></h1>
            <p><?php _e('The migration plugin is not installed.', 'ali2woo'); ?></p>
            <p><a href="https://wordpress.org/plugins/ali2woo-migration-tool/"><?php _e('Download and install plugin.', 'ali2woo'); ?></a></p>
            <?php
        }
    }
}
