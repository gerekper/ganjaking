<?php
// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();

$options = array(
    'custom-fields' => array(
        'custom-fields-tab' => array(
        'type' => 'custom_tab',
        'action' => 'yith_wcbep_custom_fields_tab'
        )
    )
);

return $options;