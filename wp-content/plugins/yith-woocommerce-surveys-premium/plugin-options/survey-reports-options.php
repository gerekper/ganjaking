<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(

    'survey-reports'  =>  array(

        'survey_reports' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_wc_surveys_reports'
        )

    )
);


return apply_filters( 'yith_wc_survey_reports_option', $settings ) ;