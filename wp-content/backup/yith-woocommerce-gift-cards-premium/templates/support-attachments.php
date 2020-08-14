<?php
/**
 * The template to show the attachmets button for the support area page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/support-attachments.php
 *
 * @see           http://docs.woothemes.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<!-- The element where Fine Uploader will exist. -->
<script type="text/template" id="qq-template">
	<div class="qq-uploader-selector qq-uploader qq-gallery">

		<div class="qq-upload-drop-area-selector qq-upload-drop-area">
			<img src=" <?php echo YITH_YWGC_ASSETS_IMAGES_URL ?>drag.svg" class="ywgc_drag_and_drop_icon" >
			<span class="qq-upload-drop-area-text-selector"><?php _e( 'Drop files or click here', 'yith-woocommerce-gift-cards' ) ?></span>
        </div>
	    <div class="qq-upload-button-selector qq-upload-button">
		    <div>Upload a file</div>
	    </div>
	    <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
	    <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
		    <li>
			    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
			    <div class="qq-progress-bar-container-selector qq-progress-bar-container">
				    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
			    </div>
			    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
			    <div class="qq-thumbnail-wrapper">
				    <img class="qq-thumbnail-selector" qq-max-size="1400" qq-server-scale>
			    </div>
			    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel"><?php _e( 'Cancel', 'yith-woocommerce-gift-cards' ) ?></button>
			    <button type="button" class="qq-upload-use-it-selector qq-upload-use-it ywgc-custom-image-modal-submit-link" ><?php _e( 'Use it!', 'yith-woocommerce-gift-cards' ) ?></button>

			    <div class="qq-file-info">
				    <div class="qq-file-name">
					    <span class="qq-upload-file-selector qq-upload-file"></span>
					    <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
				    </div>
				    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
				    <span class="qq-upload-size-selector qq-upload-size"></span>
			    </div>
		    </li>
	    </ul>
    </div>
</script>
