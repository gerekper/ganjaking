<?php
global $porto_layout;
if ( empty( $porto_layout ) ) {
	$porto_layout = porto_meta_layout();
	$porto_layout = $porto_layout[0];
}
if ( porto_header_type_is_preset() ) {
	get_template_part( 'header/header_' . porto_get_header_type() );
} else {
	get_template_part( 'header/header_builder' );
}
