<?php

// Exit if accessed directly
!defined( 'YITH_WCET' ) && exit();

return array(
    'premium' => array(
        'landing' => array(
            'type'         => 'custom_tab',
            'action'       => 'yith_wcet_premium_tab',
            'hide_sidebar' => true,

        )
    )
);