<?php
// Exit if accessed directly
!defined( 'YITH_WCMBS' ) && exit();

$tab = array(
    'shortcodes' => array(
        'shortcodes-tab' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wcmbs_render_admin_shortcodes_tab',
        )
    )
);

return $tab;