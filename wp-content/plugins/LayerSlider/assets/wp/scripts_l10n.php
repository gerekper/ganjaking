<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$l10n_ls = array(

	'adminURL' 	=> admin_url( 'admin.php' ),

	// General
	'save' 		=> __('Save changes', 'LayerSlider'),
	'saving' 	=> __('Saving ...', 'LayerSlider'),
	'saved' 	=> __('Saved', 'LayerSlider'),
	'error' 	=> __('ERROR', 'LayerSlider'),
	'untitled' 	=> __('Untitled', 'LayerSlider'),
	'working' 	=> __('Working ...', 'LayerSlider'),
	'stop' 		=> __('Stop', 'LayerSlider'),

	'slideNoun' 	=> _x('Slide', 'noun', 'LayerSlider'),
	'slideVerb' 	=> _x('Slide', 'verb', 'LayerSlider'),
	'layer' 		=> __('Layer', 'Layer'),

	'selectAll' 	=> __('Select all', 'LayerSlider'),
	'deselectAll' 	=> __('Deselect all', 'LayerSlider'),

	// Notify OSD
	'notifySliderSaved' 	=> __('Slider saved successfully', 'LayerSlider'),
	'notifyCaptureSlide' 	=> __('Capturing slide. This might take a moment ...', 'LayerSlider'),
	'notifyPixieSave'		=> __('Saving image. This might take a moment ...'),

	// Modules
	'moduleDLErrorTitle' 	=> __('Download Error', 'LayerSlider'),

	// Activation
	'activationErrorTitle' 	=> __('Something went wrong ...', 'LayerSlider'),
	'activationTemplate' 	=> __('Product activation is required to access premium templates.', 'LayerSlider'),
	'activationFeature' 	=> __('Product activation is required to access this feature.', 'LayerSlider'),
	'activationUpdate' 		=> __('Product activation is required to receive automatic updates.', 'LayerSlider'),

	'purchaseWWPopups' 		=> __('Purchase This Popup Template Pack ', 'LayerSlider'),

	// Sliders list
	'SLRemoveSlider' 			=> __('Are you sure you want to remove this slider?', 'LayerSlider'),
	'SLExportSliderHTML' 		=> __("You’re about to export this slider as HTML. This option is for the jQuery version of LayerSlider and you will *NOT* be able to use the downloaded package on WordPress sites. For that, you need to choose the regular export option. Are you sure you want to continue?\n\nThis message will be suppressed after a couple of attempts. Please mind the difference in the future between the various export methods to avoid potential harm and data loss.", 'LayerSlider'),
	'SLUploadSlider' 			=> __('Uploading, please wait ...', 'LayerSlider'),
	'SLEnterCode' 				=> __('Please enter a valid license key. For more information, please click on the “Where’s my license key?” button.', 'LayerSlider'),
	'SLDeactivate' 				=> __('Are you sure you want to deactivate this site?', 'LayerSlider'),
	'SLPermissions' 			=> __('WARNING: This option controls who can access to this plugin, you can easily lock out yourself by accident. Please, make sure that you have entered a valid capability without whitespaces or other invalid characters. Do you want to proceed?', 'LayerSlider'),
	'SLJQueryConfirm' 			=> __('Do not enable this option unless you’re  experiencing issues with jQuery on your site. This option can easily cause unexpected issues when used incorrectly. Do you want to proceed?', 'LayerSlider'),
	'SLJQueryReminder' 			=> __('Do not forget to disable this option later on if it does not help, or if you experience unexpected issues. This includes your entire site, not just LayerSlider.', 'LayerSlider'),

	'SLImportNotice' 	=> sprintf( __('Importing is taking longer than usual. This might be completely normal, but can also indicate a server configuration issue. Please visit %sSystem Status%s to check for potential causes if this screen is stuck.', 'LayerSlider'), '<a href="'.admin_url( 'admin.php?page=layerslider-options&section=system-status' ).'" target="_blank">', '</a>'),
	'SLImportErrorTitle' 	=> __('Import Error', 'LayerSlider'),
	'SLImportError' 	=> __('It seems there is a server issue that prevented LayerSlider from importing your selected slider. Please check LayerSlider → Options → System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. Retrying the import might also help.', 'LayerSlider'),
	'SLImportHTTPError' => __("It seems there is a server issue that prevented LayerSlider from importing your selected slider. Please check LayerSlider → Options → System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. Retrying the import might also help. Your HTTP server thrown the following error: \n\n %s", 'LayerSlider'),
	'SLActivationError' => __("It seems there is a server issue that prevented LayerSlider from performing product activation. Please check LayerSlider → Options → System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. Your HTTP server thrown the following error: \n\n %s", 'LayerSlider'),
	'SLRemoveGroupButton' => __('Clear & Remove Group', 'LayerSlider'),
	'SLRemoveGroupTooltip' => __('Moves all sliders out of this group, then deletes it. All of your sliders will remain available on the main grid.', 'LayerSlider'),
	'SLRemoveGroupConfirm' => __("You’re about to remove this group. All your sliders will be moved and remain available on the main grid. \n\nContinue?", 'LayerSlider'),

	// Template Store
	'TSVersionWarningTitle' 	=> __('Plugin update required', 'LayerSlider'),
	'TSVersionWarningContent' 	=> sprintf(__('This slider template requires a newer version of LayerSlider in order to work properly. This is due to additional features introduced in a later version than you have. For updating instructions, please refer to our %sonline documentation%s.', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#updating" target="_blank">', '</a>'),

	// Google Fonts
	'GFEmptyList' 		=> __('You haven’t added any Google Font to your collection yet.', 'LayerSlider'),
	'GFEmptyCharset' 	=> __('You need to have at least one character set added. Please select another item before removing this one.', 'LayerSlider'),
	'GFFontFamily' 		=> __('Choose a font family', 'LayerSlider'),
	'GFFontVariant' 	=> __('Select %s font variants', 'LayerSlider'),

	// Slider Builder
	'SBSlideTitle' 				=> __('Slide #%d', 'LayerSlider'),
	'SBSlideCopyTitle' 			=> __('Slide #%d copy', 'LayerSlider'),
	'SBLayerTitle' 				=> __('Layer #%d', 'LayerSlider'),
	'SBLayerCopyTitle' 			=> __('Layer #%d copy', 'LayerSlider'),
	'SBUndoLayer' 				=> __('Layer settings', 'LayerSlider'),
	'SBUndoLayerStyles' 		=> __('Layer styles', 'LayerSlider'),
	'SBUndoSlide' 				=> __('Slide settings', 'LayerSlider'),
	'SBUndoNewLayer' 			=> __('New layer', 'LayerSlider'),
	'SBUndoNewLayers' 			=> __('New layers', 'LayerSlider'),
	'SBUndoVideoPoster' 		=> __('Video poster', 'LayerSlider'),
	'SBUndoRemoveVideoPoster'	=> __('Remove video poster', 'LayerSlider'),
	'SBUndoLayerPosition' 		=> __('Layer position', 'LayerSlider'),
	'SBUndoRemoveLayer' 		=> __('Remove layer(s)', 'LayerSlider'),
	'SBUndoHideLayer' 			=> __('Hide layer', 'LayerSlider'),
	'SBUndoLockLayer' 			=> __('Lock layer', 'LayerSlider'),
	'SBUndoPasteSettings' 		=> __('Paste layer settings', 'LayerSlider'),
	'SBUndoSlideImage' 			=> __('Slide image', 'LayerSlider'),
	'SBUndoLayerImage' 			=> __('Layer image', 'LayerSlider'),
	'SBUndoRemoveLayerBG' 		=> __('Remove layer background', 'LayerSlider'),
	'SBUndoLayerBackground' 	=> __('Layer background', 'LayerSlider'),
	'SBUndoSortLayers' 			=> __('Sort layers', 'LayerSlider'),
	'SBUndoLayerType' 			=> __('Layer type', 'LayerSlider'),
	'SBUndoLayerMedia' 			=> __('Layer media', 'LayerSlider'),
	'SBUndoLayerResize' 		=> __('Layer resize', 'LayerSlider'),
	'SBUndoAlignLayer' 			=> __('Align layer(s)', 'LayerSlider'),
	'SBUndoRemoveSlideImage' 	=> __('Remove slide image', 'LayerSlider'),
	'SBUndoRemoveLayerImage' 	=> __('Remove layer image', 'LayerSlider'),
	'SBDragMe' 					=> __('Drag me :)', 'LayerSlider'),
	'SBPreviewImagePlaceholder'	=> __('Double click to<br> set image', 'LayerSlider'),
	'SBPreviewMediaPlaceholder'	=> __('Double click to<br> add media', 'LayerSlider'),
	'SBPreviewIconPlaceholder'	=> __('Double click to<br> add icon', 'LayerSlider'),
	'SBPreviewTextPlaceholder' 	=> __('Text Layer', 'LayerSlider'),
	'SBPreviewHTMLPlaceholder' 	=> __('HTML Layer', 'LayerSlider'),
	'SBPreviewButtonPlaceholder' => __('Button Label', 'LayerSlider'),
	'SBPreviewPostPlaceholder' 	=> __('Howdy, [author]', 'LayerSlider'),
	'SBPreviewSlide' 			=> __('Preview Slide', 'LayerSlider'),
	'SBLayerPreviewMultiSelect' => __('Layer Preview is not available in Multiple Selection Mode. Select only one layer to use this feature. ', 'LayerSlider'),
	'SBPreviewLinkNotAvailable' => __('Auto-generated URLs are not available in Preview. This layer will link to “%s” on your front-end pages.', 'LayerSlider'),
	'SBStaticUntil' 			=> __('Until the end of Slide #%d', 'LayerSlider'),
	'SBPasteLayerError'			=> __('There’s nothing to paste. Copy a layer first!', 'LayerSlider'),
	'SBPasteError' 				=> __('There is nothing to paste!', 'LayerSlider'),
	'SBRemoveSlide' 			=> __('Are you sure you want to remove this slide?', 'LayerSlider'),
	'SBRemoveLayer' 			=> __('Are you sure you want to remove this layer?', 'LayerSlider'),
	'SBMediaLibraryImage' 		=> __('Pick an image to use it in LayerSlider WP', 'LayerSlider'),
	'SBMediaLibraryMedia'		=> __('Choose video or audio files', 'LayerSlider'),
	'SBUploadError' 			=> __('Upload error', 'LayerSlider'),
	'SBUploadErrorMessage' 		=> __('Upload error: %s', 'LayerSlider'),
	'SBInvalidFormat' 			=> __('Invalid format', 'LayerSlider'),
	'SBEnterImageURL' 			=> __('Enter an image URL', 'LayerSlider'),
	'SBTransitionApplyOthers' 	=> __('Are you sure you want to apply the currently selected transitions and effects on the other slides?', 'LayerSlider'),
	'SBPostFilterWarning' 		=> __('No posts were found with the current filters.', 'LayerSlider'),
	'SBSaveError' 				=> __("It seems there is a server issue that prevented LayerSlider from saving your work. Please check LayerSlider → Options → System Status for potential errors, try to temporarily disable themes/plugins to rule out incompatibility issues or contact your hosting provider to resolve server configuration problems. Your HTTP server thrown the following error: \n\n %s", 'LayerSlider'),
	'SBUnsavedChanges' 			=> __('You have unsaved changes on this page. Do you want to leave and discard the changes made since your last save?', 'LayerSlider'),
	'SBLinkTextPage' 			=> __('Linked to WP Page: %s', 'LayerSlider'),
	'SBLinkTextPost' 			=> __('Linked to WP Post: %s', 'LayerSlider'),
	'SBLinkTextAttachment' 		=> __('Linked to WP Attachment: %s', 'LayerSlider'),
	'SBLinkPostDynURL' 			=> __('Linked to: Post URL from Dynamic content', 'LayerSlider'),
	'SBLinkSmartAction' 		=> __('LayerSlider Action: %s', 'LayerSlider'),
	'SBImportLayerNoSlider' 	=> __('No sliders found.', 'LayerSlider'),
	'SBImportLayerNoSlide' 		=> __('No slides found.', 'LayerSlider'),
	'SBImportLayerNoLayer' 		=> __('No layers found.', 'LayerSlider'),

	'SBImportLayerSelectSlide' 	=> __('Select a slide first.', 'LayerSlider'),

	'SBLayerTypeImg' 			=> __('Image', 'LayerSlider'),
	'SBLayerTypeIcon' 			=> __('Icon', 'LayerSlider'),
	'SBLayerTypeText' 			=> __('Text', 'LayerSlider'),
	'SBLayerTypeButton' 		=> __('Button', 'LayerSlider'),
	'SBLayerTypeMedia' 			=> __('Audio / Video', 'LayerSlider'),
	'SBLayerTypeHTML' 			=> __('HTML', 'LayerSlider'),
	'SBLayerTypePost' 			=> __('Dynamic', 'LayerSlider'),



	// Transition Builder
	'TBTransitionName' 		=> __('Type transition name', 'LayerSlider'),
	'TBRemoveTransition' 	=> __('Remove transition', 'LayerSlider'),
	'TBRemoveConfirmation' 	=> __('Are you sure you want to remove this transition?', 'LayerSlider'),
);