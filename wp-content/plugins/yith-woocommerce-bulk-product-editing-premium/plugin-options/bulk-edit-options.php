<?php
// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();

$settings = array(
    'bulk-edit' => array(
        'bep-tab' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wcbep_bulk_edit_main_tab',
            'hide_sidebar' => true,
        )
    )
);

return apply_filters( 'yith_wcbep_panel_settings_options', $settings );