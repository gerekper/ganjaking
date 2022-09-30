<?php
// Exit if accessed directly
!defined( 'YITH_WCMBS' ) && exit();

$tab = array(
    'vendor-members' => array(
        'vendor-members-tab' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wcmbs_vendor_render_members_tab',
        )
    )
);

return $tab;