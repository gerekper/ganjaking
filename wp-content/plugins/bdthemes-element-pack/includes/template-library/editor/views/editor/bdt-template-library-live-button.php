<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ;?>
<# if ( '' != demo_url ) { #>
	<a class="elementor-template-library-template-action bdt-elementpack-preview-button-live"  href="{{demo_url}}" target="_blank">
		<i class="eicon-editor-external-link"></i>
		<span class="elementor-button-title"><?php
			esc_html_e( 'Live Preview', 'bdthemes-element-pack' );
		?></span>
	</a>