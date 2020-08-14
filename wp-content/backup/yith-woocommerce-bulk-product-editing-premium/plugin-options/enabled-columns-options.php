<?php
// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();

$options = array(
    'enabled-columns' => array(
        'enabled-tab' => array(
        'type' => 'custom_tab',
        'action' => 'yith_wcbep_enabled_columns_tab'
        )
    )
);

return $options;