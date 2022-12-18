<?php
global $porto_settings;

$footer_type = isset( $porto_settings['footer-type'] ) ? $porto_settings['footer-type'] : '1';
get_template_part( 'footer/footer_' . $footer_type );
