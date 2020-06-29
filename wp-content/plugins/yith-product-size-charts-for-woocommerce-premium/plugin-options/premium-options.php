<?php

// Exit if accessed directly
!defined( 'YITH_WCPSC' ) && exit();

return array(
    'premium' => array(
        'landing' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wcpsc_premium_tab',
            'hide_sidebar' => true,
        )
    )
);