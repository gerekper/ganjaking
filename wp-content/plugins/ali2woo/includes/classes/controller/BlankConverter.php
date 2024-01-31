<?php
/**
 * Description of BlankConverter
 *
 * @author Ali2Woo Team
 *
 * @autoload: a2w_admin_init
 */

namespace Ali2Woo;

class BlankConverter extends AbstractAdminPage
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
        <h1>Migration Tool</h1>
        <p>The conversion plugin is not installed.</p>
        <p><a href="#">Download and install plugin</a></p>
        <?php
    }
}

