<?php
/**
 * Template item
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<# var proLink = window.ElementPackLibreryData.license.link; #>
<# var isActivated = window.ElementPackLibreryData.license.activated; #>
<# var newDemoRateDate = window.ElementPackLibreryData.new_demo_rang_date; #>

<div class="elementor-template-library-template-body">
	<div class="elementor-template-library-template-screenshot">
		<div class="elementor-template-library-template-preview">
			<i class="fa fa-search-plus"></i>
		</div>
		<img src="{{ thumbnail }}" alt="">
	</div>
    <# if ( newDemoRateDate < date ) { #>
    <span class="bdt-new-item">NEW</span>
    <# } #>
    <# if ( 1 == is_pro ) { #>
    <span class="bdt-pro-item">PRO</span>
    <# } #>
</div>
<div class="elementor-template-library-template-controls">
    <# if ( 1 != is_pro ) { #>
        <?php include('bdt-template-library-item-import-btn.php'); ?>
    <# } else { #>
        <# if(isActivated) { #>
            <?php include('bdt-template-library-item-import-btn.php'); ?>
        <# } else { #>
            <a class="elementor-template-library-template-action elementor-button bdt-elementpack-template-library-template-go-pro" href="{{ proLink }}" target="_blank">
                <i class="eicon-heart"></i><span class="elementor-button-title"><?php
                    esc_html_e( 'Get Pro', 'bdthemes-element-pack' );
                ?></span>
            </a>
	    <# } #>
	<# } #>

</div>
<div class="elementor-template-library-template-name">{{ title }}</div>