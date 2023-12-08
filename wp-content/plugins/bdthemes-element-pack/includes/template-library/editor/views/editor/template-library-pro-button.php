<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
include('bdt-template-library-live-button.php');
?>
<# var isActivated = window.ElementPackLibreryData.license.activated; #>

<# if(isActivated == false){ #>
<# var proLink = window.ElementPackLibreryData.license.link; #>

<a  href="{{ proLink }}" target="_blank" >
	<button class="elementor-template-library-template-action bdt-elementpack-preview-button-go-pro bdt-elementpack-template-library-template-insert bdt-elementpack-preview-button-go-pro elementor-button elementor-button-success" >
		<i class="eicon-heart"></i><span class="elementor-button-title"><?php
			esc_html_e( 'Go Pro', 'bdthemes-element-pack' );
		?></span>
	</button>
</a>
<# } #>