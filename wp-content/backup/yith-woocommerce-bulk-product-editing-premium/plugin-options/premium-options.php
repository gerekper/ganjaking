<?php

// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();

return array(
    'premium' => array(
        'landing' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wcbep_premium_tab',
            'hide_sidebar' => true,
        )
    )
);