<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$l10n_ls_gutenberg = array(

	// Block options
	'BlockDesc' 				=> __('Insert a LayerSlider slider or popup to your pages and posts.', 'LayerSlider'),
	'BlockEditLabel' 			=> __('Choose slider', 'LayerSlider'),
	'BlockSliderEditorLabel' 	=> __('Open Slider Builder', 'LayerSlider'),

	'BlockExampleTitle' 		=> __('Example Slider Block', 'LayerSlider'),
	'BlockExamplePreview' 		=> LS_ROOT_URL.'/static/admin/img/popup-example-bg.jpg',

	'OverridePanel' 			=> __('Override Slider Settings', 'LayerSlider'),
	'OverridePanelDesc' 		=> __('Overriding slider settings is optional. It can be useful if you want to make small changes to the same slider in certain situations without having duplicates. For example, you might want to change the slider skin on some pages to fit better to a different page style.', 'LayerSlider'),

	'LayoutLabel' 				=> __('Layout', 'LayerSlider'),
	'LayoutInherit' 			=> __('No override', 'LayerSlider'),

	'SkinLabel' 				=> __('Skin', 'LayerSlider'),
	'SkinInherit' 				=> __('No override', 'LayerSlider'),

	'AutoStartLabel' 			=> __('Auto-Start Slideshow', 'LayerSlider'),
	'AutoStartInherit' 			=> __('No override', 'LayerSlider'),
	'AutoStartEnable' 			=> __('Enabled', 'LayerSlider'),
	'AutoStartDisable' 			=> __('Disabled', 'LayerSlider'),

	'FirstSlideLabel' 			=> __('Start With Slide', 'LayerSlider'),
	'FirstSlideInherit' 		=> __('No override', 'LayerSlider'),


	'LayoutPanel' 				=> __('Layout', 'LayerSlider'),
	'LayoutPanelDesc' 			=> __('The Gutenberg editor has a native Spacer block, which you can also use to make more room around the slider.', 'LayerSlider'),
	'MarginLabel' 				=> __('Margins', 'LayerSlider'),
	'MarginAutoPlaceholder' 	=> __('auto', 'LayerSlider'),
	'MarginTopLabel' 			=> __('top', 'LayerSlider'),
	'MarginRightLabel' 			=> __('right', 'LayerSlider'),
	'MarginBottomLabel' 		=> __('bottom', 'LayerSlider'),
	'MarginLeftLabel' 			=> __('left', 'LayerSlider'),


	'PlaceholderDesc' 			=> __('Open the Slider Library with the button below and select the slider you want to insert.', 'LayerSlider'),
	'SliderLibraryButton' 		=> __('Slider Library', 'LayerSlider'),

	'edit_url' 					=> admin_url('admin.php?page=layerslider&action=edit&id='),
	'skins' 					=> array(),
	'layouts' 					=> array(
		'fixedsize' 			=> __('Fixed size', 'LayerSlider'),
		'responsive' 			=> __('Responsive', 'LayerSlider'),
		'fullwidth' 			=> __('Full width', 'LayerSlider'),
		'fullsize' 				=> __('Full size', 'LayerSlider'),
		'hero' 					=> __('Hero scene', 'LayerSlider')
	),
);


$skins = LS_Sources::getSkins();
foreach( $skins as $handle => $skin ) {
	$l10n_ls_gutenberg['skins'][ $handle ] = $skin['name'];
}