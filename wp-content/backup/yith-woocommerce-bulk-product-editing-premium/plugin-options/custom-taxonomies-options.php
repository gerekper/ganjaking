<?php
// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();

$options = array(
    'custom-taxonomies' => array(
        'custom-taxonomies-tab' => array(
        'type' => 'custom_tab',
        'action' => 'yith_wcbep_custom_taxonomies_tab'
        )
    )
);

return $options;