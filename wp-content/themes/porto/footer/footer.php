<?php
global $porto_settings;

$footer_type = $porto_settings['footer-type'];
get_template_part( 'footer/footer_' . $footer_type );
