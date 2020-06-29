<?php
$settings = array(

    'deals' => array(
        'home' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_wcdls_deals_tab'
        )
    )
);
return apply_filters( 'yith_wcdls_panel_deals_tab', $settings );