<?php
// exit if accessed directly
!defined('YITH_WTBE') && exit();
$settings = array(

    'settings' => array(

        'header'    => array(

            array(
                'name' => __( 'General Settings', 'yith-wordpress-title-bar-effects' ),
                'type' => 'title'
            ),

            array( 'type' => 'close' )
        ),


        'settings' => array(

            array( 'type' => 'open' ),

            array(
                'id'      => 'yith-wtbe-enabled',
                'name'    => __( 'Enable WordPress Title Bar Effects', 'yith-wordpress-title-bar-effects' ),
                'desc'    => '',
                'type'    => 'on-off',
                'std'     => 'yes'
            ),

            array(
                'id'      => 'yith-wtbe-animation',
                'name'    => __( 'Select animation', 'yith-wordpress-title-bar-effects' ),
                'desc'    => '',
                'type'    => 'select',
                'options' => array(
                    'typing'                => __( 'Typing', 'yith-wordpress-title-bar-effects' ),
                    'scrolling'             => __( 'Scrolling', 'yith-wordpress-title-bar-effects' ),
                    'intermittence'         => __( 'Intermittence', 'yith-wordpress-title-bar-effects' ),

                ),
                'std'     => 'typing',
            ),

            array(
                'id'      => 'yith-wtbe-change-tab',
                'name'    => __( 'Change tab', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Enable effects when users switch to another browser tab.','yith-wordpress-title-bar-effects'),
                'type'    => 'on-off',
                'std'     => 'yes'
            ),

            array(
                'id'      => 'yith-wtbe-speed-animation',
                'name'    => __( 'Animation speed', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __( 'Set the animation speed (milliseconds). This is an inversely proportionate value, so, the smaller the value, the quicker the animation is performed.','yith-wordpress-title-bar-effects' ),
                'type'    => 'number',
                'std'     => '500'
            ),

            array(
                'id'      => 'yith-wtbe-delay-start',
                'name'    => __( 'Delay time to start', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Set delay time (milliseconds) to start the effect after the page is loaded.','yith-wordpress-title-bar-effects'),
                'type'    => 'number',
                'std'     => '500'
            ),

            array(
                'id'      => 'yith-wtbe-delay-stop',
                'name'    => __( 'Effect duration', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Set the duration of the effect (milliseconds). Count starts after delay time.','yith-wordpress-title-bar-effects'),
                'type'    => 'number',
                'std'     => '60000'
            ),

            array(
                'id'      => 'yith-wtbe-delay-cycle',
                'name'    => __( 'Time between animation cycles', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Set the time (milliseconds) that has to pass between an animation cycle and the following one','yith-wordpress-title-bar-effects'),
                'type'    => 'number',
                'std'     => '3000',
            ),

            array(
                'id'      => 'yith-wtbe-title-bar',
                'name'    => __( 'Title', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Title shown on the tab when the animation is applied. If empty, the animation effect will be applied on the default page title.','yith-wordpress-title-bar-effects'),
                'type'    => 'textarea',
                'std'     => '',
            ),
            array(
                'id'      => 'yith-wtbe-title-bar-prefix',
                'name'    => __( 'Title prefix', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Text to prefix to the page title','yith-wordpress-title-bar-effects'),
                'type'    => 'text',
                'std'     => '',
            ),
            array(
                'id'      => 'yith-wtbe-title-bar-suffix',
                'name'    => __( 'Title suffix', 'yith-wordpress-title-bar-effects' ),
                'desc'    => __('Text to suffix to the page title','yith-wordpress-title-bar-effects'),
                'type'    => 'text',
                'std'     => '',
            ),
            array( 'type' => 'close' ),
        )
    )
);

return apply_filters( 'yith_wbte_panel_settings_options', $settings );