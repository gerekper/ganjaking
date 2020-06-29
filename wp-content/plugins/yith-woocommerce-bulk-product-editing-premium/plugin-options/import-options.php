<?php
// Exit if accessed directly
! defined( 'YITH_WCBEP' )  && exit();

$import = array(
    'import' => array(
        'bep-tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wcbep_import_tab'
        )
    )
);

return $import;