<?php

// Exit if accessed directly
!defined( 'YITH_WCCOS' ) && exit();

return array(
    'premium' => array(
        'landing' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wccos_premium_tab',
            'hide_sidebar' => true,
        )
    )
);