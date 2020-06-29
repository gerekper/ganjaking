<?php
if(seed_cspv5_cu('none')){
    return '';
}


wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('imagesloaded');
wp_enqueue_script('masonry');

?>

<!-- css -->
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>template/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

<!-- Plugins -->
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/jquery.nouislider.css" rel="stylesheet">
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/switchery.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SEED_CSPV5_PLUGIN_URL ?>template/css/select2.min.css">
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/fontawesome-iconpicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css">
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/1.1.1/introjs.min.css"> -->
 
<!-- Editor css -->
<link href="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/css/editor-style.css" rel="stylesheet">

<div id="seed-cspv5-customizer-wrapper">

<!-- BEGIN SIDEBAR -->
<div id="seed-cspv5-sidebar" data-pages="csp-sidebar">
   
<!-- Right Side Of Navbar -->
<div id="preview-actions">
<a id="seed-back" href="<?php echo admin_url(); ?>options-general.php?page=seed_cspv5">&#8592; Back to Settings</a>
<?php if (!defined('SEED_CSPV5_REMOVE_BRANDING')) { ?>
 Coming Soon Page Pro by <img style="width:100px;margin-bottom:8px;vertical-align: middle;" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>admin/seedprod-logo-black.png"> <br>
<?php } ?>
<button id="publish-btn" class="button-primary" data-step="4" data-loading-text="Saving..."><i class="fa fa-save"></i> <?php _e('Save','seedprod-coming-soon-pro'); ?></button>
    <a id="refresh_page"  class="button-secondary"><i class="fa fa-refresh"></i> <?php _e('Refresh','seedprod-coming-soon-pro'); ?></a>
    <a id="preview_desktop" class="button-secondary"><i class="fa fa-desktop"></i> <?php _e('Desktop','seedprod-coming-soon-pro'); ?></a>
    <a id="preview_mobile"  class="button-secondary"><i class="fa fa-mobile-phone"></i> <?php _e('Mobile','seedprod-coming-soon-pro'); ?></a>

    

                            

    <div id="qn-wrapper" style="display:none" data-step="2" data-intro="<?php _e("After you select your theme use the Quick Nav to expore the options that can be changed. You'll want to start by selecting the <strong>Content Settings</strong>",'seedprod-coming-soon-pro'); ?>" >
    <?php seed_cspv5_select('quick_nav',array(''=>__('Section Quick Nav'),
                        'Content' => array('content'=>__('Content Settings'),
                        'form'=>__('Form Settings'),
                        'social-profiles'=>__('Social Profiles'),
                        'social-buttons'=>__('Social Sharing Buttons'),
                        'countdown'=>__('Countdown'),
                        'progress-bar'=>__('Progress Bar'),
                        'footer'=>__('Footer Credit'),
                        'page'=>__('Page Settings')
                        ),
                        'Design' => array('theme'=>__('Theme'),
                        'background'=>__('Background Settings'),
                        'container'=>__('Content Container'),
                        'elements'=>__('Elements Colors'),
                        'typography'=>__('Typography'),
                        'custom-css'=>__('Custom CSS')
                        ),
                        'header-language-settings' => __('Customize Text'),
                        'header-advanced-settings' => __('Advanced Settings'),
                        'view-subscribers' => __('View Subscribers')
                        ),$selected = null); ?>
    </div>
    <?php if(!empty($page->html)){ ?>
    <small style="font-size:11px; background:#fff0c4; padding:2px">Please make all your changes in the your custom HTML.</small>
    <?php } ?>

</div>

<!-- BEGIN SIDEBAR MENU -->
<div class="csp-sidebar-menu">
<div class="csp-sidebar">
<form id="seed_cspv5_customizer">
<input type="hidden" id="disabled_fields" name="disabled_fields" value="<?php echo ( empty($settings->disabled_fields) ) ? '' : $settings->disabled_fields  ?>">
<input type="hidden" id="first_run" name="first_run" value="">


<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

<div class="panel panel-default">
    <a name="header-theme"></a>
    <div class="panel-heading" role="tab" id="theme">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-theme" aria-expanded="false" aria-controls="collapse-theme">
            <i class="fa fa-magic"></i> <?php _e('Theme','seedprod-coming-soon-pro') ?>
            </a>
        </h4>
    </div>
    <div id="collapse-theme" class="panel-collapse collapse" role="tabpanel" aria-labelledby="theme">
        <div class="panel-body">
               <div class="form-group" data-step="1" data-intro="Happy with your theme? If not you can change it here.">
                        <label class="control-label"><?php _e('Theme','seedprod-coming-soon-pro') ?></label>
                        <input id="theme" class="form-control input-sm" name="theme" type="hidden" value="<?php echo $settings->theme ?>">
                        <button type="button" id="theme-picker" class="stock-theme button-primary"><?php _e('Select a Theme','seedprod-coming-soon-pro') ?></button>  
                            <div class="img-preview">
                                        <img id="theme-preview" src="<?php echo SEED_CSPV5_THEME_BASE_URL.$settings->theme ?>.jpg">
                                <!--  <i class="fa fa-close"></i> -->
                            </div>
                </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
<div class="panel-heading" role="tab" id="content-settings">
    <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-ontent-settings" aria-expanded="false" aria-controls="collapse-ontent-settings">
        <i class="fa fa-file"></i> <?php _e('Content','seedprod-coming-soon-pro'); ?>
        </a>
    </h4>
</div>
<div id="collapse-ontent-settings" class="panel-collapse collapse" role="tabpanel" aria-labelledby="content-settings">
        <div class="panel-body">
        <div class="form-group">
            <label class="control-label"><?php _e('Page Name','seedprod-coming-soon-pro'); ?></label>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Enter a name for your page to help you distinguish it.','seedprod-coming-soon-pro'); ?>"></i>
            <input id="name" class="form-control input-sm" name="name" type="text" value="<?php echo esc_attr(htmlentities($page->name,ENT_QUOTES, "UTF-8")) ?>">
            <span class="help-block" for="name"></span>        
        </div>
        
        <div class="form-group">
            <label class="control-label"><?php _e('Logo','seedprod-coming-soon-pro'); ?></label>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Upload a logo or teaser image.','seedprod-coming-soon-pro'); ?>"></i>
            <input id="logo" class="form-control input-sm" name="logo" type="text" value="<?php echo  $settings->logo ?>">
            <input id='logo_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' /><br>
                <?php if(!empty($settings->logo)): ?>
                <div class="img-preview">
                    <img id="logo-preview" src="<?php echo  $settings->logo ?>">
                    <?php else: ?>
                    <div class="img-preview" style="display:none;">
                        <img id="logo-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif" >
                    <?php endif; ?>
                    <i class="fa fa-close"></i>
                </div>
        </div>
        
        <div class="form-group">
            <label class="control-label" ><?php _e('Headline','seedprod-coming-soon-pro'); ?></label>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Enter a headline for your page. Replace the default headline if it exists.','seedprod-coming-soon-pro'); ?>"></i>
            
            <input id="headline" class="form-control input-sm" name="headline" type="text" value="<?php echo htmlentities($settings->headline,ENT_QUOTES, "UTF-8") ?>">        
        </div>
        
        <div class="form-group">
            <label class="control-label" ><?php _e('Description','seedprod-coming-soon-pro') ?></label>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Tell the visitor what to expect from your site. Also supports WordPress shortcodes and video embeds. Most shortcodes require that you enable Enable 3rd Party Plugins which can be found under the advanced tab.','seedprod-coming-soon-pro') ?>"></i>
            <?php
            $content   = $settings->description;
            $editor_id = 'description';
            $args      = array(
                 'textarea_name' => "description",
            ); 
            
            wp_editor( $content, $editor_id, $args ); 
            ?>
        </div>
     
    


   
       
       
                <div class="form-group adv">
                    <label class="control-label"><?php _e('Sections Order','seedprod-coming-soon-pro') ?></label>  
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Use the bar icon to drag and drop the sections."></i>
                    <ul id="blocks" class="list-unstyled">
                        <?php foreach($settings->blocks as $v){ ?>
                        <li>
                            <input type="hidden"  name="blocks[]"  value="<?php echo $v ?>">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-bars"></i></div>
                                <input type="text" class="form-control input-sm" readonly value="<?php echo ucwords(str_replace('_', ' ', $v)) ?>" style="color:#2c2c2c" />
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Favicon','seedprod-coming-soon-pro') ?></label> 
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Favicons are displayed in a browser tab. Note: You may need to clear your browser cache to view.','seedprod-coming-soon-pro') ?>"></i> 
                    <small><a href='http://tools.dynamicdrive.com/favicon/' target='_blank'><?php _e('Need Help creating a favicon','seedprod-coming-soon-pro') ?></a>?</small>
                    <input id="favicon" class="form-control input-sm" name="favicon" type="hidden" value="<?php echo  $settings->favicon ?>">
                    <input id='favicon_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' /><br>
                    <?php if(!empty($settings->favicon)): ?>
                    <div class="img-preview">
                        <img id="favicon-preview" src="<?php echo  $settings->favicon ?>">
                        <?php else: ?>
                        <div class="img-preview" style="display:none;">
                            <img id="favicon-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif" >
                        <?php endif; ?>
                        <i class="fa fa-close"></i>
                    </div> 
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label"><?php _e('SEO Title','seedprod-coming-soon-pro') ?></label>
                        <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This is what search engine will use as the title for your page.','seedprod-coming-soon-pro') ?>"></i>
                        <input id="seo_title" class="form-control input-sm" maxlength="80" name="seo_title" type="text" value="<?php echo htmlentities($settings->seo_title,ENT_QUOTES, "UTF-8"); ?>">         
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php _e('SEO Description','seedprod-coming-soon-pro') ?></label>
                        <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This is what search engine will use as the description for your page.','seedprod-coming-soon-pro') ?>"></i>
                        <input id="seo_description" class="form-control input-sm" maxlength="350" name="seo_description" type="text" value="<?php echo htmlentities($settings->seo_description,ENT_QUOTES, "UTF-8") ?>">       
                    </div>

                    <div class="form-group">
                        <label class="control-label">Facebook &amp; Twitter Thumbnail</label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Image must be at least 200px * 200px.','seedprod-coming-soon-pro') ?>"></i>
                        <input id="facebook_thumbnail" class="form-control input-sm" name="facebook_thumbnail" type="hidden" value="<?php echo  $settings->facebook_thumbnail ?>">
                        <input id='facebook_thumbnail_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' /><br>
                        <?php if(!empty($settings->facebook_thumbnail)): ?>
                        <div class="img-preview">
                            <img id="facebook_thumbnail-preview" src="<?php echo  $settings->facebook_thumbnail ?>">
                            <?php else: ?>
                            <div class="img-preview" style="display:none;">
                                <img id="facebook_thumbnail-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif" >
                            <?php endif; ?>
                            <i class="fa fa-close"></i>
                        </div>       
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label"><?php _e('Google Analytics Code','seedprod-coming-soon-pro') ?></label>
                        <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Paste in your Google Analytics code. Include the <script> tags.','seedprod-coming-soon-pro') ?>"></i>

                        <textarea id="ga_analytics" class="form-control input-sm" name="ga_analytics" cols="50" rows="10"><?php echo $settings->ga_analytics ?></textarea> 
                        
                        <small>Paste in the enitre tracking script Google Analytics provides.</small>       
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <a name="header-background-settings"></a>
            <div class="panel-heading" role="tab" id="background-settings">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-background-settings" aria-expanded="false" aria-controls="collapse-background-settings">
                    <i class="fa fa-photo"></i> <?php _e('Background Settings','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-background-settings" class="panel-collapse collapse" role="tabpanel" aria-labelledby="background-settings">
                <div class="panel-body">
                    <div class="form-group">
                            <label class="control-label"><?php _e('Background Color','seedprod-coming-soon-pro') ?></label>
                            <div class="input-group background_color_picker">
                                <input id="background_color" class="form-control input-sm" data-format="hex" name="background_color" type="text" value="<?php echo $settings->background_color ?>">  
                                <span class="input-group-addon"><i></i></span>  
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label"><?php _e('Background Image','seedprod-coming-soon-pro') ?></label> 
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Tip: Make sure to select the full size image when inserting.','seedprod-coming-soon-pro') ?>"></i>
                            <br>
                            <button type="button" class="stock-image button-primary" data-toggle="modal" data-target="#image-picker"><?php _e('Select Stock Image','seedprod-coming-soon-pro') ?></button> 
                            or
                            <input id="background_image" class="form-control input-sm" name="background_image" type="hidden" value="<?php echo  $settings->background_image ?>">
                            <input id='background_image_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' /><br>
                                <?php if(!empty($settings->background_image)): ?>
                                <div class="img-preview">
                                    <img id="background_image-preview" src="<?php echo  $settings->background_image ?>">
                                    <?php else: ?>
                                    <div class="img-preview" style="display:none;">
                                        <img id="background_image-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif" >
                                    <?php endif; ?>
                                    <i class="fa fa-close"></i>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Background Advanced Settings','seedprod-coming-soon-pro') ?></label>
                                <input id="enable_background_adv_settings" class="switchery" name="enable_background_adv_settings" type="checkbox" value="1" <?php echo (!empty($settings->enable_background_adv_settings) && $settings->enable_background_adv_settings == '1') ? 'checked' : '' ?>>       
                            </div>
                            <div id="background_adv_settings">
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Enable Background Color Overlay','seedprod-coming-soon-pro') ?></label>
                                    <input id="enable_background_overlay" class="switchery" name="enable_background_overlay" type="checkbox" value="1" <?php echo (!empty($settings->enable_background_overlay) && $settings->enable_background_overlay == '1') ? 'checked' : '' ?>>       
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Background Overlay','seedprod-coming-soon-pro') ?></label>
                                    <div class="input-group background_overlay_picker">
                                        <input id="background_overlay" class="form-control input-sm" name="background_overlay" type="text" value="<?php echo $settings->background_overlay ?>">  
                                        <span class="input-group-addon"><i></i></span>  
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Background Size','seedprod-coming-soon-pro') ?></label>
                                    <?php seed_cspv5_select('background_size',array('auto'=>'Auto','cover'=>'Cover','contain'=>'Contain'),$settings->background_size); ?>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Background Repeat','seedprod-coming-soon-pro') ?></label>
                                    <?php seed_cspv5_select('background_repeat',array('repeat'=>'Repeat','repeat-x'=>'Repeat-X','repeat-y'=>'Repeat-Y', 'no-repeat'=>'No-Repeat'),$settings->background_repeat); ?>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Background Position','seedprod-coming-soon-pro') ?></label>
                                    <?php seed_cspv5_select('background_position',array('left top'=>'Left Top','left center'=>'Left Center','left bottom'=>'Left Bottom', 'right top' => 'Right Top', 'right center' => 'Right Center', 'right bottom' => 'Right Bottom', 'center top' => 'Center Top', 'center center' => 'Center Center', 'center bottom' => 'Center Bottom'),$settings->background_position); ?>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Background Attachment','seedprod-coming-soon-pro') ?></label>
                                    <?php seed_cspv5_select('background_attachment',array('scroll'=>'Scroll','fixed'=>'Fixed'),$settings->background_attachment); ?>
                                </div>
                            </div>
                            
                            <div class="form-group"><label class="control-label"><?php _e('Background Slideshow','seedprod-coming-soon-pro') ?></label> 
                                <input id="bg_slideshow" class="switchery" name="bg_slideshow" type="checkbox" value="1" <?php echo (!empty($settings->bg_slideshow)) ? 'checked' : '' ?>>       
                            </div>
                            <div id="bg_slideshow_settings">

                                <div class="form-group">
                                    <label class="control-label"><?php _e('Slideshow Images','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e("Select 'Choose Image' to add a new image to the slide show. Click the 'X' icon to remove an image. Use the 'Bar Icon' to drag and drop the order.",'seedprod-coming-soon-pro') ?>"></i>
                                    <div id="slides">
                                        <?php if(!empty($settings->bg_slideshow_images) && count($settings->bg_slideshow_images) > 0) { ?>
                                        <?php $c = 0 ?>
                                        <?php foreach($settings->bg_slideshow_images as $v){ ?>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-bars"></i></div>
                                            <input type="text" id="bg_slideshow_images_<?php echo $c ?>" name="bg_slideshow_images[<?php echo $c; ?>]" class="form-control input-sm" value="<?php echo $v ?>">
                                            <div class="input-group-addon"><img class="slide-preview" src="<?php echo $v ?>"></div>
                                            <div class="input-group-addon slide-delete"><i class="fa fa-close"></i></div>
                                        </div>
                                        <?php $c++ ?>
                                        <?php } ?>
                                        <?php }else{ ?>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-bars"></i></div>
                                            <input type="text" id="bg_slideshow_images_0" name="bg_slideshow_images[0]" class="form-control input-sm">
                                            <div class="input-group-addon"><img class="slide-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif"></div>
                                            <div class="input-group-addon slide-delete"><i class="fa fa-close"></i></div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <input id='bg_slideshow_tmp' type="hidden">
                                    <input id='bg_slideshow_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' />
                                </div>
                            

                        
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Slide Speed','seedprod-coming-soon-pro') ?></label>
                                <input id="bg_slideshow_slide_speed" class="form-control input-sm" name="bg_slideshow_slide_speed" type="hidden" value="<?php echo $settings->bg_slideshow_slide_speed ?>">
                                <div class="bg-master m-b-10 m-t-40" id="bg_slideshow_slide_speed_slider"></div>
                            
                            </div>
                            <div class="form-group"><label class="control-label"><?php _e('Slideshow Randomize','seedprod-coming-soon-pro') ?></label>
                                <input id="bg_slideshow_randomize" class="switchery" name="bg_slideshow_randomize" type="checkbox" value="1" <?php echo (!empty($settings->bg_slideshow_randomize)) ? 'checked' : '' ?>>        
                            </div>
                            <!--<div class="form-group">
                                <label class="control-label"><?php _e('Slide Transition','seedprod-coming-soon-pro') ?></label>
                                <?php seed_cspv5_select('bg_slideshow_slide_transition',array('0'=>'None','1'=>'Fade','2'=>'Slide Top','3'=>'Slide Right','4'=>'Slide Bottom','5'=>'Slide Left','6'=>'Carousel Right','7'=>'Carousel Left'),(!empty($settings->bg_slideshow_slide_transition)) ? $settings->bg_slideshow_slide_transition : '1') ?>
                            </div>-->
                            
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Background Video','seedprod-coming-soon-pro') ?></label>
                                <input id="bg_video" class="switchery" name="bg_video" type="checkbox" value="1" <?php echo (!empty($settings->bg_video)) ? 'checked' : '' ?>>      
                            </div>
                            <div id="bg_video_settings">
                                <div class="form-group">
                                    <p>Mobile devices block background videos, the background image will be used as a fallback.</p>
                                    <label class="control-label"><?php _e('Background Video URL','seedprod-coming-soon-pro') ?></label>
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Paste in the YouTube.com video url link to add background video. Please only use videos you own the right to.','seedprod-coming-soon-pro') ?>"></i>
                                    <input id="bg_video_url" class="form-control input-sm" name="bg_video_url" type="text" value="<?php echo $settings->bg_video_url ?>">      
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Enable Video Audio','seedprod-coming-soon-pro') ?></label>
                                    <input class="switchery" id="bg_video_audio" name="bg_video_audio" type="checkbox" value="1" <?php echo (!empty($settings->bg_video_audio) && $settings->bg_video_audio == '1') ? 'checked' : '' ?>>       
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?php _e('Enable Video Loop','seedprod-coming-soon-pro') ?></label>
                                    <input class="switchery" id="bg_video_loop" name="bg_video_loop" type="checkbox" value="1" <?php echo (!empty($settings->bg_video_loop) && $settings->bg_video_loop == '1' ) ? 'checked' : '' ?>>      
                                </div>
                            </div>
                       
                </div>
            </div>
        </div>


        <div class="panel panel-default">
            <a name="header-content-container"></a>
            <div class="panel-heading" role="tab" id="content-container">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-content-container" aria-expanded="false" aria-controls="collapse-content-container">
                    <i class="fa fa-square-o"></i> <?php _e('Content Container','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-content-container" class="panel-collapse collapse" role="tabpanel" aria-labelledby="content-container">
                <div class="panel-body">
                    <div class="form-group">
                                <label class="control-label"><?php _e('Container Transparent','seedprod-coming-soon-pro') ?></label>
                                <input id="container_transparent" class="switchery" name="container_transparent" type="checkbox" value="1" <?php echo (!empty($settings->container_transparent) && $settings->container_transparent == '1') ? 'checked' : '' ?>>      
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Container Color &amp; Opacity','seedprod-coming-soon-pro') ?></label>
                                <div class="input-group container_color_picker">
                                    <input id="container_color" class="form-control input-sm" name="container_color" type="text" value="<?php echo $settings->container_color ?>">  
                                    <span class="input-group-addon"><i></i></span>  
                                </div>
                            
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Container Radius','seedprod-coming-soon-pro') ?></label>
                                <input id="container_radius" class="form-control input-sm" name="container_radius" type="hidden" value="<?php echo $settings->container_radius ?>">
                                <div class="bg-master m-b-10 m-t-40" id="container_radius_slider"></div>
                            
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Container Position','seedprod-coming-soon-pro') ?></label>
                                <?php seed_cspv5_select('container_position',array(
                                '1'=>'Center Center',
                                '2'=>'Center Top',
                                '3'=>'Center Bottom',
                                '4'=>'Left Center',
                                '5'=>'Left Top',
                                '6'=>'Left Bottom',
                                '7'=>'Right Center',
                                '8'=>'Right Top',
                                '9'=>'Right Bottom',
                                ),$settings->container_position); ?>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Container Max Width','seedprod-coming-soon-pro') ?></label>
                                <input id="container_width" class="form-control input-sm" name="container_width" type="hidden" value="<?php echo $settings->container_width ?>"> 
                                <div class="bg-master m-b-10 m-t-40" id="container_width_slider"></div>
                            </div>
                            

                            <div class="form-group">
                                <label class="control-label"><?php _e('Container Animation','seedprod-coming-soon-pro') ?></label>
                                <select id="container_effect_animation" name="container_effect_animation" class="form-control input-sm">
                                    <option value="">None</option>
                                    <optgroup label="Attention Seekers">
                                        <option value="bounce">bounce</option>
                                        <option value="flash">flash</option>
                                        <option value="pulse">pulse</option>
                                        <option value="rubberBand">rubberBand</option>
                                        <option value="shake">shake</option>
                                        <option value="swing">swing</option>
                                        <option value="tada">tada</option>
                                        <option value="wobble">wobble</option>
                                        <option value="jello">jello</option>
                                    </optgroup>
                                    <optgroup label="Bouncing Entrances">
                                        <option value="bounceIn">bounceIn</option>
                                        <option value="bounceInDown">bounceInDown</option>
                                        <option value="bounceInLeft">bounceInLeft</option>
                                        <option value="bounceInRight">bounceInRight</option>
                                        <option value="bounceInUp">bounceInUp</option>
                                    </optgroup>
                                    <optgroup label="Fading Entrances">
                                        <option value="fadeIn">fadeIn</option>
                                        <option value="fadeInDown">fadeInDown</option>
                                        <option value="fadeInDownBig">fadeInDownBig</option>
                                        <option value="fadeInLeft">fadeInLeft</option>
                                        <option value="fadeInLeftBig">fadeInLeftBig</option>
                                        <option value="fadeInRight">fadeInRight</option>
                                        <option value="fadeInRightBig">fadeInRightBig</option>
                                        <option value="fadeInUp">fadeInUp</option>
                                        <option value="fadeInUpBig">fadeInUpBig</option>
                                    </optgroup>
                                    <optgroup label="Flippers">
                                        <option value="flip">flip</option>
                                        <option value="flipInX">flipInX</option>
                                        <option value="flipInY">flipInY</option>
                                    </optgroup>
                                    <optgroup label="Lightspeed">
                                        <option value="lightSpeedIn">lightSpeedIn</option>
                                    </optgroup>
                                    <optgroup label="Rotating Entrances">
                                        <option value="rotateIn">rotateIn</option>
                                        <option value="rotateInDownLeft">rotateInDownLeft</option>
                                        <option value="rotateInDownRight">rotateInDownRight</option>
                                        <option value="rotateInUpLeft">rotateInUpLeft</option>
                                        <option value="rotateInUpRight">rotateInUpRight</option>
                                    </optgroup>
                                    <optgroup label="Sliding Entrances">
                                        <option value="slideInUp">slideInUp</option>
                                        <option value="slideInDown">slideInDown</option>
                                        <option value="slideInLeft">slideInLeft</option>
                                        <option value="slideInRight">slideInRight</option>
                                    </optgroup>
                                    <optgroup label="Zoom Entrances">
                                        <option value="zoomIn">zoomIn</option>
                                        <option value="zoomInDown">zoomInDown</option>
                                        <option value="zoomInLeft">zoomInLeft</option>
                                        <option value="zoomInRight">zoomInRight</option>
                                        <option value="zoomInUp">zoomInUp</option>
                                    </optgroup>
                                </select>
                            </div>
                            
                </div>
            </div>
        </div>


        <div class="panel panel-default">
            <a name="header-element-colors"></a>
            <div class="panel-heading" role="tab" id="element-colors">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-element-colors" aria-expanded="false" aria-controls="collapse-element-colors">
                    <i class="fa fa-paint-brush"></i> <?php _e('Element Colors','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-element-colors" class="panel-collapse collapse" role="tabpanel" aria-labelledby="element-colors">
                <div class="panel-body">
                           <div class="form-group">
                            <label class="control-label"><?php _e('Elements Color','seedprod-coming-soon-pro') ?></label> <small><?php _e('(Button, Countdown & Progress Bar)','seedprod-coming-soon-pro') ?></small>
                            <div class="input-group button_color_picker">
                                <input id="button_color" class="form-control input-sm" name="button_color" type="text" value="<?php echo $settings->button_color ?>">
                                <span class="input-group-addon"><i></i></span>  
                            </div>
                        </div>
                        

                        <div class="form-group"><label class="control-label"><?php _e('Elements Flat','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This will remove gradients and make element colors flat.','seedprod-coming-soon-pro') ?>"></i>
                            <input id="container_flat" class="switchery" checked="checked" name="container_flat" type="checkbox" value="1" <?php echo (!empty($settings->container_flat) && $settings->container_flat == '1') ? 'checked' : '' ?>>        
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label"><?php _e('Form Input Background Color','seedprod-coming-soon-pro') ?></label>
                            <div class="input-group form_color_picker">
                                <input id="form_color" class="form-control input-sm" name="form_color" type="text" value="<?php echo $settings->form_color ?>">
                                <span class="input-group-addon"><i></i></span>  
                            </div>
                        </div>
                  
                </div>
            </div>
        </div>


        <div class="panel panel-default">
            <a name="header-typography"></a>
            <div class="panel-heading" role="tab" id="typography">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-typography" aria-expanded="false" aria-controls="collapse-typography">
                    <i class="fa fa-font"></i> <?php _e('Typography','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-typography" class="panel-collapse collapse" role="tabpanel" aria-labelledby="typography">
                <div class="panel-body">
                    <div class="form-group">
                                <label class="control-label"><?php _e('Text Font','seedprod-coming-soon-pro') ?></label>
                                <?php $f = $font_families; 
                                array_shift($f);
                                ?>
                                <?php seed_cspv5_select('text_font',$f,$settings->text_font); ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Text Weight & Style','seedprod-coming-soon-pro') ?></label>
                                <?php seed_cspv5_select("text_weight",array('400'=>'Normal 400','Bold 700'=>'Bold','400 italic'=>'Normal 400 Italic', '700italic' => 'Bold 700 Italic'),$settings->text_weight); ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Text Subset','seedprod-coming-soon-pro') ?></label>
                                <?php seed_cspv5_select("text_subset",array(''=>'Default'),$settings->text_subset); ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Text Color','seedprod-coming-soon-pro') ?></label>
                                <div class="input-group text_color_picker">
                                    <input id="text_color" class="form-control input-sm" name="text_color" type="text" value="<?php echo $settings->text_color ?>"> 
                                    <span class="input-group-addon"><i></i></span>  
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Text Size','seedprod-coming-soon-pro') ?></label>
                                <input id="text_size" name="text_size" type="hidden" value="<?php echo ( empty($settings->text_size) ) || $settings->text_size == 'false' ? '16' : $settings->text_size ?>">
                                <div class="bg-master m-b-10 m-t-40" id="text_size_slider"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Text Line Height','seedprod-coming-soon-pro') ?></label>
                                <input id="text_line_height" name="text_line_height" type="hidden" value="<?php echo ( empty($settings->text_line_height) || $settings->text_line_height == 'false' ) ? '1.5' : $settings->text_line_height ?>">
                                <div class="bg-master m-b-10 m-t-40" id="text_line_height_slider"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Headline Font','seedprod-coming-soon-pro') ?></label>
                                <?php seed_cspv5_select('headline_font',$font_families,$settings->headline_font); ?>      
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Headline Weight & Style','seedprod-coming-soon-pro') ?></label>
                                <select id="headline_weight" class="form-control input-sm" name="headline_weight"><option value="400" selected="selected">Normal 400</option><option value="Bold 700">Bold</option><option value="400 italic">Normal 400 Italic</option><option value="700italic">Bold 700 Italic</option></select>       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Headline Subset','seedprod-coming-soon-pro') ?></label>
                                <select id="headline_subset" class="form-control input-sm" name="headline_subset"><option value="" selected="selected">Default</option></select>       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Headline Color','seedprod-coming-soon-pro') ?></label>
                                <div class="input-group headline_color_picker">
                                    <input id="headline_color" class="form-control input-sm" name="headline_color" type="text" value="<?php echo $settings->headline_color ?>">  
                                    <span class="input-group-addon"><i></i></span>  
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Headline Size','seedprod-coming-soon-pro') ?></label>
                                <input id="headline_size" name="headline_size" type="hidden" value="<?php echo ( empty($settings->headline_size) || $settings->headline_size == 'false' ) ? '42' : $settings->headline_size ?>">
                                <div class="bg-master m-b-10 m-t-40" id="headline_size_slider"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Headline Line Height','seedprod-coming-soon-pro') ?></label>
                                <input id="headline_line_height" name="headline_line_height" type="hidden" value="<?php echo ( empty($settings->headline_line_height) || $settings->headline_line_height == 'false' ) ? '1' : $settings->headline_line_height  ?>">
                                <div class="bg-master m-b-10 m-t-40" id="headline_line_height_slider"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Button Font','seedprod-coming-soon-pro') ?></label>
                                <?php seed_cspv5_select('button_font',$font_families,$settings->button_font); ?>     
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Button Weight & Style','seedprod-coming-soon-pro') ?></label>
                                <select id="button_weight" class="form-control input-sm" name="button_weight"><option value="400" selected="selected">Normal 400</option><option value="Bold 700">Bold</option><option value="400 italic">Normal 400 Italic</option><option value="700italic">Bold 700 Italic</option></select>       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Button Subset','seedprod-coming-soon-pro') ?></label>
                                <select id="button_subset" class="form-control input-sm" name="button_subset"><option value="" selected="selected">Default</option></select>       
                            </div>
                            <!--         <div class="form-group">
                                <label class="control-label">Button Size</label>
                                <input id="button_size" name="button_size" type="hidden" value="14">
                                <br><br><br>
                                <div class="bg-master m-b-10" id="button_size_slider"></div>
                                </div>
                                -->
                            <div class="form-group">
                                <label class="control-label"><?php _e('Typekit ID','seedprod-coming-soon-pro') ?></label>
                                <input id="typekit_id" class="form-control input-sm" name="typekit_id" type="text" value="<?php echo $settings->typekit_id ?>">       
                            </div>
                </div>
            </div>
        </div>



 

        <div class="panel panel-default">
            <a name="header-email-form"></a>
            <div class="panel-heading" role="tab" id="email-form">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-email-form" aria-expanded="false" aria-controls="collapse-email-form">
                    <i class="fa fa-envelope"></i> <?php _e('Email Form Settings','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-email-form" class="panel-collapse collapse" role="tabpanel" aria-labelledby="email-form">
                <div class="panel-body">
                     <div class="form-group">
                <label class="control-label"><?php _e('Enable Form','seedprod-coming-soon-pro') ?></label>
                <input id="enable_form" class="switchery" name="enable_form" type="checkbox" value="1" <?php echo (!empty($settings->enable_form) && $settings->enable_form == '1') ? 'checked' : '' ?>>       
            </div>
            
            <div id="form_settings">
                
                <div class="form-group adv">
                    <label class="control-label"><?php _e('Display Name','seedprod-coming-soon-pro') ?></label>
                    <input id="display_name" class="switchery" name="display_name" type="checkbox" value="1" <?php echo (!empty($settings->display_name) && $settings->display_name == '1') ? 'checked' : '' ?>>       
                    <label><?php _e('Require Name','seedprod-coming-soon-pro') ?></label>
                    <input id="require_name" class="switchery" name="require_name" type="checkbox" value="1" <?php echo (!empty($settings->require_name) && $settings->require_name == '1') ? 'checked' : '' ?>>
                    <br>
                    
                </div>
                <div class="form-group">
                    <label class="control-label"><?php _e('Optin Confirmation Checkbox','seedprod-coming-soon-pro') ?></label>
                    <input id="display_optin_confirm" class="switchery" name="display_optin_confirm" type="checkbox" value="1" <?php echo (!empty($settings->display_optin_confirm) && $settings->display_optin_confirm == '1') ? 'checked' : '' ?>>       
                    
                </div>
                <div id="optin_settings">
                <div class="form-group">
                    <label class="control-label" ><?php _e('Optin Confirmation Text','seedprod-coming-soon-pro'); ?></label>
                    
                    <input id="optin_confirmation_text" class="form-control input-sm" name="optin_confirmation_text" type="text" value="<?php echo htmlentities((!empty($settings->optin_confirmation_text)) ? $settings->optin_confirmation_text : 'Yes, Please Send Me Marketing Emails',ENT_QUOTES, "UTF-8") ?>">        
                </div>
                </div>

               
                <div class="form-group">
                    <label class="control-label"><?php _e('Form Width','seedprod-coming-soon-pro') ?></label>
                    <input id="form_width" name="form_width" type="hidden" value="<?php echo (empty($settings->form_width)) ? '100' : $settings->form_width ?>">
                    <div class="bg-master m-b-10 m-t-40" id="form_width_slider"></div>
                </div>
               

                <?php if(seed_cspv5_cu('fb')){ ?>
                            <div class="form-group">
                                <button type="button" id="form-builder" class="button-primary"><?php _e('Manage Form Fields','seedprod-coming-soon-pro') ?></button>  
                            </div>
                            <?php } ?>
                
                <div class="form-group adv">
                    <label class="control-label"><?php _e('Save Subscribers To','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('By default we save all your email subscribers which you can export. You can also directly integrate with one of the email providers below.','seedprod-coming-soon-pro') ?>"></i>
                    <?php seed_cspv5_select('emaillist',$emaillist,$settings->emaillist); ?>
                    <p>      
                    <a id="mail-config-link" href="javascript:void(0)" class="button-primary" data-link=""><?php _e('Configure','seedprod-coming-soon-pro') ?> </a>
                    </p>
                </div>

                 <div class="form-group">
                    <button type="button" id="autoresponder" class="button-primary"><?php _e('Configure Optional Autoresponder','seedprod-coming-soon-pro') ?></button>  
                </div>
 
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Thank You Message','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Leave a thank you message or incentive information after the visitor has subscribed.','seedprod-coming-soon-pro') ?>"></i>
                        <?php
                        $content   = $settings->thankyou_msg;
                        $editor_id = 'thankyou_msg';
                        $args      = array(
                             'textarea_name' => "thankyou_msg" 
                        ); 
                        
                        wp_editor( $content, $editor_id, $args ); 
                        ?>      
                </div>
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Enable Referrer Tracking','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Referrer Tracking adds a  unique link on your Thank You Page that encourages your visitors to share. This allows you to track who referred who.','seedprod-coming-soon-pro') ?>"></i>
                    <input id="enable_reflink" class="switchery" name="enable_reflink" type="checkbox" value="1" <?php echo (!empty($settings->enable_reflink) && $settings->enable_reflink == '1') ? 'checked' : '' ?>>       
                </div>
                <div class="form-group">
                    <label class="control-label"><?php _e('Enable Fraud Detection','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This caps the sign up limit to 3 per IP address.','seedprod-coming-soon-pro') ?>"></i>
                    <input id="enable_fraud_detection" class="switchery" name="enable_fraud_detection" type="checkbox" value="1" <?php echo (!empty($settings->enable_fraud_detection) && $settings->enable_fraud_detection == '1') ? 'checked' : '' ?>>       
                </div>

                <div class="form-group">
                    <label class="control-label"><?php _e('Enable Prize Levels','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Assign prizes to people who reach referral goals.') ?>"></i>
                    <input id="enable_prize_levels" class="switchery" name="enable_prize_levels" type="checkbox" value="1" <?php echo (!empty($settings->enable_prize_levels) && $settings->enable_prize_levels == '1') ? 'checked' : '' ?>> 
                    <a id="prize-config-link" style="margin-top:3px" href="javascript:void(0)" class="button-primary" data-link=""><?php _e('Configure Prize Levels','seedprod-coming-soon-pro') ?> </a>      
                </div>

                <div class="form-group">
                    <label class="control-label"><?php _e('Enable Return Auto Return','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This will place a cookie on the visitors machine and will auto submit the form and show the users referral or prize status.','seedprod-coming-soon-pro') ?>"></i>
                    <input id="rp_return_user" class="switchery" name="rp_return_user" type="checkbox" value="1" <?php echo (!empty($settings->rp_return_user) && $settings->rp_return_user == '1') ? 'checked' : '' ?>>       
                </div>

            </div>           
                </div>
            </div>
        </div>

         <div class="panel panel-default">
            <a name="header-contatc-form"></a>
            <div class="panel-heading" role="tab" id="contatc-form">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-contatc-form" aria-expanded="false" aria-controls="collapse-contatc-form">
                    <i class="fa fa-envelope-o"></i> <?php _e('Contact Form Settings','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-contatc-form" class="panel-collapse collapse" role="tabpanel" aria-labelledby="contatc-form">
                <div class="panel-body">
                
                     <div class="form-group">
                <label class="control-label"><?php _e('Enable Contact Form','seedprod-coming-soon-pro') ?></label>
                <input id="enable_cf_form" class="switchery" name="enable_cf_form" type="checkbox" value="1" <?php echo (!empty($settings->enable_cf_form) && $settings->enable_cf_form == '1') ? 'checked' : '' ?>>       
            </div>

                <div id="cf_form_settings">
                    <div class="form-group">
                                <label class="control-label">Email(s) to Send Contact Form</label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Seperate multiple emails with a comma."></i>
                                <input id="cf_form_emails" class="form-control input-sm" name="cf_form_emails" type="text" value="<?php echo (!empty($settings->cf_form_emails)) ? $settings->cf_form_emails : '' ?>">       
                            </div>

                            <div class="form-group">
                            <label class="control-label"><?php _e('Contact Us Color','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Leave blank to use default color."></i>
                            <div class="input-group contactform_color_picker">
                                <input id="contactform_color" class="form-control input-sm" data-format="hex" name="contactform_color" type="text" value="<?php echo (empty($settings->contactform_color))?'':$settings->contactform_color ?>">  
                                <span class="input-group-addon"><i></i></span>  
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?php _e('Contact Form Confirmation Message','seedprod-coming-soon-pro') ?></label>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Enter an optional confirmation mesaage.','seedprod-coming-soon-pro') ?>"></i>
                                <?php
                                if(empty($settings->cf_confirmation_msg)){
                                    $settings->cf_confirmation_msg = '';
                                }
                                $content   = $settings->cf_confirmation_msg;
                                $editor_id = 'cf_confirmation_msg';
                                $args      = array(
                                     'textarea_name' => "cf_confirmation_msg" 
                                ); 
                                
                                wp_editor( $content, $editor_id, $args ); 
                                ?>      
                        </div>

                </div>  
                </div>           
            </div>
        </div>



        <div class="panel panel-default">
    <a name="header-social-profiles"></a>
    <div class="panel-heading" role="tab" id="social-profiles">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-social-profiles" aria-expanded="false" aria-controls="collapse-social-profiles">
            <i class="fa fa-facebook-official"></i> <?php _e('Social Profiles','seedprod-coming-soon-pro') ?>
            </a>
        </h4>
    </div>
    <div id="collapse-social-profiles" class="panel-collapse collapse" role="tabpanel" aria-labelledby="social-profiles">
        <div class="panel-body">
    <div class="form-group">
                <label class="control-label"><?php _e('Enable Social Profiles','seedprod-coming-soon-pro') ?></label>
                <input id="enable_socialprofiles" class="switchery" name="enable_socialprofiles" type="checkbox" value="1" <?php echo (!empty($settings->enable_socialprofiles) && $settings->enable_socialprofiles == '1') ? 'checked' : '' ?>>       
            </div>
            <div id="socialprofiles_settings">
                
                <div class="form-group">
                <small ><a style="font-size:10px;line-height:1" href="https://support.seedprod.com/article/86-adding-custom-icons" target="_blank">Learn how to use any Font Awesome Icon or a Custom Icon.</a></small><br>
                    <label class="control-label"><?php _e('Social Profiles','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e("Click the 'Add Profile Link' button to add a new profile. Then select an icon, enter the url and optionally use the bar icons to rearrange by dragging and dropping.",'seedprod-coming-soon-pro') ?>"></i>

                    <div id="social_profiles_repeatable_container">
                        <?php $s_c = 0 ?>
                        <?php 
                        if(!empty($settings->social_profiles) && count($settings->social_profiles) > 0) {
                        foreach($settings->social_profiles as $v){
                        ?>
                        <div class="field-group">
                            <div class="btn-group" >
                                <button id="bicon_<?php echo $s_c ?>" data-selected="graduation-cap" type="button" class="icp icp-dd btn btn-default btn-sm dropdown-toggle iconpicker-component" data-toggle="dropdown">
                                Icon  <i class="fa fa-fw <?php echo $v->icon ?>"></i>
                                <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu"></div>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-bars"></i></div>
                                <input type="text" name="social_profiles[<?php echo $s_c ?>][url]" value="<?php echo $v->url ?>" id="social_profiles_<?php echo $s_c ?>"  class="form-control input-sm" placeholder="Enter Url"/>
                                <input type="hidden" name="social_profiles[<?php echo $s_c ?>][icon]" value="<?php echo $v->icon ?>" id="icon_<?php echo $s_c ?>"  class="form-control input-sm"/>
                                <div class="input-group-addon"><i class="fa fa-close delete"></i></div>
                            </div>
                        </div>
                        <?php $s_c++;}}?>
                    </div>
                    <input type="button" value="Add Profile Link" class="add button-primary" />
                </div>

                 <div class="form-group">
                            <label class="control-label"><?php _e('Social Profile Color','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Leave blank to use default color."></i>
                            <div class="input-group socialprofile_color_picker">
                                <input id="socialprofile_color" class="form-control input-sm" data-format="hex" name="socialprofile_color" type="text" value="<?php echo (empty($settings->socialprofile_color))?'':$settings->socialprofile_color ?>">  
                                <span class="input-group-addon"><i></i></span>  
                            </div>
                        </div>
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Size','seedprod-coming-soon-pro') ?></label>
                    <?php seed_cspv5_select('social_profiles_size',array(''=>'1x','fa-lg'=>'1.5x','fa-2x'=>'2x','fa-3x'=>'3x','fa-4x'=>'4x','fa-5x'=>'5x'),$settings->social_profiles_size); ?>
                </div>
                
                <div class="form-group"><label class="control-label"><?php _e('Open Links In New Window','seedprod-coming-soon-pro') ?></label>
                    <input id="social_profiles_blank" class="switchery" name="social_profiles_blank" type="checkbox" value="1" <?php echo (!empty($settings->social_profiles_blank) && $settings->social_profiles_blank == '1') ? 'checked' : '' ?>>       
                </div>
            </div>        
        </div>
    </div>
</div>


            <div class="panel panel-default">
                <a name="header-social-buttons"></a>
                <div class="panel-heading" role="tab" id="social-buttons">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-social-buttons" aria-expanded="false" aria-controls="collapse-social-buttons">
                        <i class="fa fa-share-square-o"></i> <?php _e('Social Share Buttons','seedprod-coming-soon-pro') ?>
                        </a>
                    </h4>
                </div>
                <div id="collapse-social-buttons" class="panel-collapse collapse" role="tabpanel" aria-labelledby="social-buttons">
                    <div class="panel-body">
                          <div class="form-group">
                            <label class="control-label"><?php _e('Enable Social Share Buttons','seedprod-coming-soon-pro') ?></label>
                            <input id="enable_socialbuttons" class="switchery" name="enable_socialbuttons" type="checkbox" value="1" <?php echo (!empty($settings->enable_socialbuttons) && $settings->enable_socialbuttons == '1') ? 'checked' : '' ?>>       
                        </div>
                        <div id="socialbuttons_settings">
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Show Share Buttons On','seedprod-coming-soon-pro') ?></label>  
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('If Referrer Tracking is enabled Social Share Buttons can only be shown on the Thank You page.','seedprod-coming-soon-pro') ?>"></i>
                                <?php seed_cspv5_select('show_sharebutton_on',array('front'=>'Front Page','thank-you'=>'Thank You Page','both'=>'Both Pages'),$settings->show_sharebutton_on); ?>
                            </div>
                            
                            <ul id="share_buttons" class="list-unstyled">
                                <li>
                                    <div class="form-group">
                                        <label class="control-label">Twitter</label>
                                        <input id="share_buttons_twitter" name="share_buttons[twitter]" type="checkbox" value="1" <?php echo (!empty($settings->share_buttons->twitter) && $settings->share_buttons->twitter == '1') ? 'checked' : '' ?>>        
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Tweet Text</label>
                                        <input id="tweet_text" class="form-control input-sm" name="tweet_text" type="text" value="<?php echo $settings->tweet_text ?>">        
                                    </div>
                                </li>
                                <li>
                                    <div class="form-group">
                                        <label class="control-label">Facebook Share</label>
                                        <input id="share_buttons_facebook" name="share_buttons[facebook]" type="checkbox" value="1" <?php echo (!empty($settings->share_buttons->facebook) && $settings->share_buttons->facebook == '1') ? 'checked' : '' ?>>         
                                    </div>
                                </li>
                                <!-- <li>
                                    <div class="form-group">
                                        <label class="control-label">Facebook Send</label>
                                        <input id="share_buttons_facebook_send" name="share_buttons[facebook_send]" type="checkbox" value="1" <?php echo (!empty($settings->share_buttons->facebook_send) && $settings->share_buttons->facebook_send == '1') ? 'checked' : '' ?>>       
                                    </div>
                                </li> -->
                                <!-- <li>
                                    <div class="form-group">
                                        <label class="control-label">Google Plus</label>
                                        <input id="share_buttons_googleplus" name="share_buttons[googleplus]" type="checkbox" value="1" <?php echo (!empty($settings->share_buttons->googleplus) && $settings->share_buttons->googleplus == '1') ? 'checked' : '' ?>>        
                                    </div>
                                </li> -->
                                <li>
                                    <div class="form-group">
                                        <label class="control-label">LinkedIn</label>
                                        <input id="share_buttons_linkedin" name="share_buttons[linkedin]" type="checkbox" value="1" <?php echo (!empty($settings->share_buttons->linkedin) && $settings->share_buttons->linkedin == '1') ? 'checked' : '' ?>>        
                                    </div>
                                </li>
                                <li>
                                    <div class="form-group">
                                        <label class="control-label">Pin It</label>
                                        <input id="share_buttons_pinterest" name="share_buttons[pinterest]" type="checkbox" value="1" <?php echo (!empty($settings->share_buttons->pinterest) && $settings->share_buttons->pinterest == '1') ? 'checked' : '' ?>>         
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Pinterest Thumbnail</label>
                                        <input id="pinterest_thumbnail" class="form-control input-sm" name="pinterest_thumbnail" type="hidden" value="<?php echo  $settings->pinterest_thumbnail ?>">
                                        <input id='pinterest_thumbnail_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' /><br>
                                        <?php if(!empty($settings->pinterest_thumbnail)): ?>
                                        <div class="img-preview">
                                            <img id="pinterest_thumbnail-preview" src="<?php echo  $settings->pinterest_thumbnail ?>">
                                            <?php else: ?>
                                            <div class="img-preview" style="display:none;">
                                                <img id="pinterest_thumbnail-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif" >
                                            <?php endif; ?>
                                            <i class="fa fa-close"></i>
                                        </div>      
                                    </div>
                                </li>
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
              <div class="panel panel-default">
    <a name="header-countdown"></a>
    <div class="panel-heading" role="tab" id="countdown">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-countdown" aria-expanded="false" aria-controls="collapse-countdown">
            <i class="fa fa-calendar"></i> <?php _e('Countdown','seedprod-coming-soon-pro') ?>
            </a>
        </h4>
    </div>
    <div id="collapse-countdown" class="panel-collapse collapse" role="tabpanel" aria-labelledby="countdown">
        <div class="panel-body">
            <div class="form-group"><label class="control-label"><?php _e('Enable Countdown','seedprod-coming-soon-pro') ?></label>
                <input id="enable_countdown" class="switchery" name="enable_countdown" type="checkbox" value="1" <?php echo (!empty($settings->enable_countdown) && $settings->enable_countdown == '1') ? 'checked' : '' ?>>       
            </div>
            <div id="countdown_settings">
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Countdown Timezone','seedprod-coming-soon-pro') ?></label>
                    <select id="countdown_timezone" name="countdown_timezone" class="form-control input-sm"><option value="Africa/Abidjan">Africa/Abidjan</option><option value="Africa/Accra">Africa/Accra</option><option value="Africa/Addis_Ababa">Africa/Addis_Ababa</option><option value="Africa/Algiers">Africa/Algiers</option><option value="Africa/Asmara">Africa/Asmara</option><option value="Africa/Asmera">Africa/Asmera</option><option value="Africa/Bamako">Africa/Bamako</option><option value="Africa/Bangui">Africa/Bangui</option><option value="Africa/Banjul">Africa/Banjul</option><option value="Africa/Bissau">Africa/Bissau</option><option value="Africa/Blantyre">Africa/Blantyre</option><option value="Africa/Brazzaville">Africa/Brazzaville</option><option value="Africa/Bujumbura">Africa/Bujumbura</option><option value="Africa/Cairo">Africa/Cairo</option><option value="Africa/Casablanca">Africa/Casablanca</option><option value="Africa/Ceuta">Africa/Ceuta</option><option value="Africa/Conakry">Africa/Conakry</option><option value="Africa/Dakar">Africa/Dakar</option><option value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option><option value="Africa/Djibouti">Africa/Djibouti</option><option value="Africa/Douala">Africa/Douala</option><option value="Africa/El_Aaiun">Africa/El_Aaiun</option><option value="Africa/Freetown">Africa/Freetown</option><option value="Africa/Gaborone">Africa/Gaborone</option><option value="Africa/Harare">Africa/Harare</option><option value="Africa/Johannesburg">Africa/Johannesburg</option><option value="Africa/Juba">Africa/Juba</option><option value="Africa/Kampala">Africa/Kampala</option><option value="Africa/Khartoum">Africa/Khartoum</option><option value="Africa/Kigali">Africa/Kigali</option><option value="Africa/Kinshasa">Africa/Kinshasa</option><option value="Africa/Lagos">Africa/Lagos</option><option value="Africa/Libreville">Africa/Libreville</option><option value="Africa/Lome">Africa/Lome</option><option value="Africa/Luanda">Africa/Luanda</option><option value="Africa/Lubumbashi">Africa/Lubumbashi</option><option value="Africa/Lusaka">Africa/Lusaka</option><option value="Africa/Malabo">Africa/Malabo</option><option value="Africa/Maputo">Africa/Maputo</option><option value="Africa/Maseru">Africa/Maseru</option><option value="Africa/Mbabane">Africa/Mbabane</option><option value="Africa/Mogadishu">Africa/Mogadishu</option><option value="Africa/Monrovia">Africa/Monrovia</option><option value="Africa/Nairobi">Africa/Nairobi</option><option value="Africa/Ndjamena">Africa/Ndjamena</option><option value="Africa/Niamey">Africa/Niamey</option><option value="Africa/Nouakchott">Africa/Nouakchott</option><option value="Africa/Ouagadougou">Africa/Ouagadougou</option><option value="Africa/Porto-Novo">Africa/Porto-Novo</option><option value="Africa/Sao_Tome">Africa/Sao_Tome</option><option value="Africa/Timbuktu">Africa/Timbuktu</option><option value="Africa/Tripoli">Africa/Tripoli</option><option value="Africa/Tunis">Africa/Tunis</option><option value="Africa/Windhoek">Africa/Windhoek</option><option value="America/Adak">America/Adak</option><option value="America/Anchorage">America/Anchorage</option><option value="America/Anguilla">America/Anguilla</option><option value="America/Antigua">America/Antigua</option><option value="America/Araguaina">America/Araguaina</option><option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option><option value="America/Argentina/Catamarca">America/Argentina/Catamarca</option><option value="America/Argentina/ComodRivadavia">America/Argentina/ComodRivadavia</option><option value="America/Argentina/Cordoba">America/Argentina/Cordoba</option><option value="America/Argentina/Jujuy">America/Argentina/Jujuy</option><option value="America/Argentina/La_Rioja">America/Argentina/La_Rioja</option><option value="America/Argentina/Mendoza">America/Argentina/Mendoza</option><option value="America/Argentina/Rio_Gallegos">America/Argentina/Rio_Gallegos</option><option value="America/Argentina/Salta">America/Argentina/Salta</option><option value="America/Argentina/San_Juan">America/Argentina/San_Juan</option><option value="America/Argentina/San_Luis">America/Argentina/San_Luis</option><option value="America/Argentina/Tucuman">America/Argentina/Tucuman</option><option value="America/Argentina/Ushuaia">America/Argentina/Ushuaia</option><option value="America/Aruba">America/Aruba</option><option value="America/Asuncion">America/Asuncion</option><option value="America/Atikokan">America/Atikokan</option><option value="America/Atka">America/Atka</option><option value="America/Bahia">America/Bahia</option><option value="America/Bahia_Banderas">America/Bahia_Banderas</option><option value="America/Barbados">America/Barbados</option><option value="America/Belem">America/Belem</option><option value="America/Belize">America/Belize</option><option value="America/Blanc-Sablon">America/Blanc-Sablon</option><option value="America/Boa_Vista">America/Boa_Vista</option><option value="America/Bogota">America/Bogota</option><option value="America/Boise">America/Boise</option><option value="America/Buenos_Aires">America/Buenos_Aires</option><option value="America/Cambridge_Bay">America/Cambridge_Bay</option><option value="America/Campo_Grande">America/Campo_Grande</option><option value="America/Cancun">America/Cancun</option><option value="America/Caracas">America/Caracas</option><option value="America/Catamarca">America/Catamarca</option><option value="America/Cayenne">America/Cayenne</option><option value="America/Cayman">America/Cayman</option><option value="America/Chicago">America/Chicago</option><option value="America/Chihuahua">America/Chihuahua</option><option value="America/Coral_Harbour">America/Coral_Harbour</option><option value="America/Cordoba">America/Cordoba</option><option value="America/Costa_Rica">America/Costa_Rica</option><option value="America/Creston">America/Creston</option><option value="America/Cuiaba">America/Cuiaba</option><option value="America/Curacao">America/Curacao</option><option value="America/Danmarkshavn">America/Danmarkshavn</option><option value="America/Dawson">America/Dawson</option><option value="America/Dawson_Creek">America/Dawson_Creek</option><option value="America/Denver">America/Denver</option><option value="America/Detroit">America/Detroit</option><option value="America/Dominica">America/Dominica</option><option value="America/Edmonton">America/Edmonton</option><option value="America/Eirunepe">America/Eirunepe</option><option value="America/El_Salvador">America/El_Salvador</option><option value="America/Ensenada">America/Ensenada</option><option value="America/Fort_Wayne">America/Fort_Wayne</option><option value="America/Fortaleza">America/Fortaleza</option><option value="America/Glace_Bay">America/Glace_Bay</option><option value="America/Godthab">America/Godthab</option><option value="America/Goose_Bay">America/Goose_Bay</option><option value="America/Grand_Turk">America/Grand_Turk</option><option value="America/Grenada">America/Grenada</option><option value="America/Guadeloupe">America/Guadeloupe</option><option value="America/Guatemala">America/Guatemala</option><option value="America/Guayaquil">America/Guayaquil</option><option value="America/Guyana">America/Guyana</option><option value="America/Halifax">America/Halifax</option><option value="America/Havana">America/Havana</option><option value="America/Hermosillo">America/Hermosillo</option><option value="America/Indiana/Indianapolis">America/Indiana/Indianapolis</option><option value="America/Indiana/Knox">America/Indiana/Knox</option><option value="America/Indiana/Marengo">America/Indiana/Marengo</option><option value="America/Indiana/Petersburg">America/Indiana/Petersburg</option><option value="America/Indiana/Tell_City">America/Indiana/Tell_City</option><option value="America/Indiana/Vevay">America/Indiana/Vevay</option><option value="America/Indiana/Vincennes">America/Indiana/Vincennes</option><option value="America/Indiana/Winamac">America/Indiana/Winamac</option><option value="America/Indianapolis">America/Indianapolis</option><option value="America/Inuvik">America/Inuvik</option><option value="America/Iqaluit">America/Iqaluit</option><option value="America/Jamaica">America/Jamaica</option><option value="America/Jujuy">America/Jujuy</option><option value="America/Juneau">America/Juneau</option><option value="America/Kentucky/Louisville">America/Kentucky/Louisville</option><option value="America/Kentucky/Monticello">America/Kentucky/Monticello</option><option value="America/Knox_IN">America/Knox_IN</option><option value="America/Kralendijk">America/Kralendijk</option><option value="America/La_Paz">America/La_Paz</option><option value="America/Lima">America/Lima</option><option value="America/Los_Angeles">America/Los_Angeles</option><option value="America/Louisville">America/Louisville</option><option value="America/Lower_Princes">America/Lower_Princes</option><option value="America/Maceio">America/Maceio</option><option value="America/Managua">America/Managua</option><option value="America/Manaus">America/Manaus</option><option value="America/Marigot">America/Marigot</option><option value="America/Martinique">America/Martinique</option><option value="America/Matamoros">America/Matamoros</option><option value="America/Mazatlan">America/Mazatlan</option><option value="America/Mendoza">America/Mendoza</option><option value="America/Menominee">America/Menominee</option><option value="America/Merida">America/Merida</option><option value="America/Metlakatla">America/Metlakatla</option><option value="America/Mexico_City">America/Mexico_City</option><option value="America/Miquelon">America/Miquelon</option><option value="America/Moncton">America/Moncton</option><option value="America/Monterrey">America/Monterrey</option><option value="America/Montevideo">America/Montevideo</option><option value="America/Montreal">America/Montreal</option><option value="America/Montserrat">America/Montserrat</option><option value="America/Nassau">America/Nassau</option><option value="America/New_York">America/New_York</option><option value="America/Nipigon">America/Nipigon</option><option value="America/Nome">America/Nome</option><option value="America/Noronha">America/Noronha</option><option value="America/North_Dakota/Beulah">America/North_Dakota/Beulah</option><option value="America/North_Dakota/Center">America/North_Dakota/Center</option><option value="America/North_Dakota/New_Salem">America/North_Dakota/New_Salem</option><option value="America/Ojinaga">America/Ojinaga</option><option value="America/Panama">America/Panama</option><option value="America/Pangnirtung">America/Pangnirtung</option><option value="America/Paramaribo">America/Paramaribo</option><option value="America/Phoenix">America/Phoenix</option><option value="America/Port-au-Prince">America/Port-au-Prince</option><option value="America/Port_of_Spain">America/Port_of_Spain</option><option value="America/Porto_Acre">America/Porto_Acre</option><option value="America/Porto_Velho">America/Porto_Velho</option><option value="America/Puerto_Rico">America/Puerto_Rico</option><option value="America/Rainy_River">America/Rainy_River</option><option value="America/Rankin_Inlet">America/Rankin_Inlet</option><option value="America/Recife">America/Recife</option><option value="America/Regina">America/Regina</option><option value="America/Resolute">America/Resolute</option><option value="America/Rio_Branco">America/Rio_Branco</option><option value="America/Rosario">America/Rosario</option><option value="America/Santa_Isabel">America/Santa_Isabel</option><option value="America/Santarem">America/Santarem</option><option value="America/Santiago">America/Santiago</option><option value="America/Santo_Domingo">America/Santo_Domingo</option><option value="America/Sao_Paulo">America/Sao_Paulo</option><option value="America/Scoresbysund">America/Scoresbysund</option><option value="America/Shiprock">America/Shiprock</option><option value="America/Sitka">America/Sitka</option><option value="America/St_Barthelemy">America/St_Barthelemy</option><option value="America/St_Johns">America/St_Johns</option><option value="America/St_Kitts">America/St_Kitts</option><option value="America/St_Lucia">America/St_Lucia</option><option value="America/St_Thomas">America/St_Thomas</option><option value="America/St_Vincent">America/St_Vincent</option><option value="America/Swift_Current">America/Swift_Current</option><option value="America/Tegucigalpa">America/Tegucigalpa</option><option value="America/Thule">America/Thule</option><option value="America/Thunder_Bay">America/Thunder_Bay</option><option value="America/Tijuana">America/Tijuana</option><option value="America/Toronto">America/Toronto</option><option value="America/Tortola">America/Tortola</option><option value="America/Vancouver">America/Vancouver</option><option value="America/Virgin">America/Virgin</option><option value="America/Whitehorse">America/Whitehorse</option><option value="America/Winnipeg">America/Winnipeg</option><option value="America/Yakutat">America/Yakutat</option><option value="America/Yellowknife">America/Yellowknife</option><option value="Antarctica/Casey">Antarctica/Casey</option><option value="Antarctica/Davis">Antarctica/Davis</option><option value="Antarctica/DumontDUrville">Antarctica/DumontDUrville</option><option value="Antarctica/Macquarie">Antarctica/Macquarie</option><option value="Antarctica/Mawson">Antarctica/Mawson</option><option value="Antarctica/McMurdo">Antarctica/McMurdo</option><option value="Antarctica/Palmer">Antarctica/Palmer</option><option value="Antarctica/Rothera">Antarctica/Rothera</option><option value="Antarctica/South_Pole">Antarctica/South_Pole</option><option value="Antarctica/Syowa">Antarctica/Syowa</option><option value="Antarctica/Troll">Antarctica/Troll</option><option value="Antarctica/Vostok">Antarctica/Vostok</option><option value="Arctic/Longyearbyen">Arctic/Longyearbyen</option><option value="Asia/Aden">Asia/Aden</option><option value="Asia/Almaty">Asia/Almaty</option><option value="Asia/Amman">Asia/Amman</option><option value="Asia/Anadyr">Asia/Anadyr</option><option value="Asia/Aqtau">Asia/Aqtau</option><option value="Asia/Aqtobe">Asia/Aqtobe</option><option value="Asia/Ashgabat">Asia/Ashgabat</option><option value="Asia/Ashkhabad">Asia/Ashkhabad</option><option value="Asia/Baghdad">Asia/Baghdad</option><option value="Asia/Bahrain">Asia/Bahrain</option><option value="Asia/Baku">Asia/Baku</option><option value="Asia/Bangkok">Asia/Bangkok</option><option value="Asia/Beirut">Asia/Beirut</option><option value="Asia/Bishkek">Asia/Bishkek</option><option value="Asia/Brunei">Asia/Brunei</option><option value="Asia/Calcutta">Asia/Calcutta</option><option value="Asia/Chita">Asia/Chita</option><option value="Asia/Choibalsan">Asia/Choibalsan</option><option value="Asia/Chongqing">Asia/Chongqing</option><option value="Asia/Chungking">Asia/Chungking</option><option value="Asia/Colombo">Asia/Colombo</option><option value="Asia/Dacca">Asia/Dacca</option><option value="Asia/Damascus">Asia/Damascus</option><option value="Asia/Dhaka">Asia/Dhaka</option><option value="Asia/Dili">Asia/Dili</option><option value="Asia/Dubai">Asia/Dubai</option><option value="Asia/Dushanbe">Asia/Dushanbe</option><option value="Asia/Gaza">Asia/Gaza</option><option value="Asia/Harbin">Asia/Harbin</option><option value="Asia/Hebron">Asia/Hebron</option><option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh</option><option value="Asia/Hong_Kong">Asia/Hong_Kong</option><option value="Asia/Hovd">Asia/Hovd</option><option value="Asia/Irkutsk">Asia/Irkutsk</option><option value="Asia/Istanbul">Asia/Istanbul</option><option value="Asia/Jakarta">Asia/Jakarta</option><option value="Asia/Jayapura">Asia/Jayapura</option><option value="Asia/Jerusalem">Asia/Jerusalem</option><option value="Asia/Kabul">Asia/Kabul</option><option value="Asia/Kamchatka">Asia/Kamchatka</option><option value="Asia/Karachi">Asia/Karachi</option><option value="Asia/Kashgar">Asia/Kashgar</option><option value="Asia/Kathmandu">Asia/Kathmandu</option><option value="Asia/Katmandu">Asia/Katmandu</option><option value="Asia/Khandyga">Asia/Khandyga</option><option value="Asia/Kolkata">Asia/Kolkata</option><option value="Asia/Krasnoyarsk">Asia/Krasnoyarsk</option><option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur</option><option value="Asia/Kuching">Asia/Kuching</option><option value="Asia/Kuwait">Asia/Kuwait</option><option value="Asia/Macao">Asia/Macao</option><option value="Asia/Macau">Asia/Macau</option><option value="Asia/Magadan">Asia/Magadan</option><option value="Asia/Makassar">Asia/Makassar</option><option value="Asia/Manila">Asia/Manila</option><option value="Asia/Muscat">Asia/Muscat</option><option value="Asia/Nicosia">Asia/Nicosia</option><option value="Asia/Novokuznetsk">Asia/Novokuznetsk</option><option value="Asia/Novosibirsk">Asia/Novosibirsk</option><option value="Asia/Omsk">Asia/Omsk</option><option value="Asia/Oral">Asia/Oral</option><option value="Asia/Phnom_Penh">Asia/Phnom_Penh</option><option value="Asia/Pontianak">Asia/Pontianak</option><option value="Asia/Pyongyang">Asia/Pyongyang</option><option value="Asia/Qatar">Asia/Qatar</option><option value="Asia/Qyzylorda">Asia/Qyzylorda</option><option value="Asia/Rangoon">Asia/Rangoon</option><option value="Asia/Riyadh">Asia/Riyadh</option><option value="Asia/Saigon">Asia/Saigon</option><option value="Asia/Sakhalin">Asia/Sakhalin</option><option value="Asia/Samarkand">Asia/Samarkand</option><option value="Asia/Seoul">Asia/Seoul</option><option value="Asia/Shanghai">Asia/Shanghai</option><option value="Asia/Singapore">Asia/Singapore</option><option value="Asia/Srednekolymsk">Asia/Srednekolymsk</option><option value="Asia/Taipei">Asia/Taipei</option><option value="Asia/Tashkent">Asia/Tashkent</option><option value="Asia/Tbilisi">Asia/Tbilisi</option><option value="Asia/Tehran">Asia/Tehran</option><option value="Asia/Tel_Aviv">Asia/Tel_Aviv</option><option value="Asia/Thimbu">Asia/Thimbu</option><option value="Asia/Thimphu">Asia/Thimphu</option><option value="Asia/Tokyo">Asia/Tokyo</option><option value="Asia/Ujung_Pandang">Asia/Ujung_Pandang</option><option value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option><option value="Asia/Ulan_Bator">Asia/Ulan_Bator</option><option value="Asia/Urumqi">Asia/Urumqi</option><option value="Asia/Ust-Nera">Asia/Ust-Nera</option><option value="Asia/Vientiane">Asia/Vientiane</option><option value="Asia/Vladivostok">Asia/Vladivostok</option><option value="Asia/Yakutsk">Asia/Yakutsk</option><option value="Asia/Yekaterinburg">Asia/Yekaterinburg</option><option value="Asia/Yerevan">Asia/Yerevan</option><option value="Atlantic/Azores">Atlantic/Azores</option><option value="Atlantic/Bermuda">Atlantic/Bermuda</option><option value="Atlantic/Canary">Atlantic/Canary</option><option value="Atlantic/Cape_Verde">Atlantic/Cape_Verde</option><option value="Atlantic/Faeroe">Atlantic/Faeroe</option><option value="Atlantic/Faroe">Atlantic/Faroe</option><option value="Atlantic/Jan_Mayen">Atlantic/Jan_Mayen</option><option value="Atlantic/Madeira">Atlantic/Madeira</option><option value="Atlantic/Reykjavik">Atlantic/Reykjavik</option><option value="Atlantic/South_Georgia">Atlantic/South_Georgia</option><option value="Atlantic/St_Helena">Atlantic/St_Helena</option><option value="Atlantic/Stanley">Atlantic/Stanley</option><option value="Australia/ACT">Australia/ACT</option><option value="Australia/Adelaide">Australia/Adelaide</option><option value="Australia/Brisbane">Australia/Brisbane</option><option value="Australia/Broken_Hill">Australia/Broken_Hill</option><option value="Australia/Canberra">Australia/Canberra</option><option value="Australia/Currie">Australia/Currie</option><option value="Australia/Darwin">Australia/Darwin</option><option value="Australia/Eucla">Australia/Eucla</option><option value="Australia/Hobart">Australia/Hobart</option><option value="Australia/LHI">Australia/LHI</option><option value="Australia/Lindeman">Australia/Lindeman</option><option value="Australia/Lord_Howe">Australia/Lord_Howe</option><option value="Australia/Melbourne">Australia/Melbourne</option><option value="Australia/NSW">Australia/NSW</option><option value="Australia/North">Australia/North</option><option value="Australia/Perth">Australia/Perth</option><option value="Australia/Queensland">Australia/Queensland</option><option value="Australia/South">Australia/South</option><option value="Australia/Sydney">Australia/Sydney</option><option value="Australia/Tasmania">Australia/Tasmania</option><option value="Australia/Victoria">Australia/Victoria</option><option value="Australia/West">Australia/West</option><option value="Australia/Yancowinna">Australia/Yancowinna</option><option value="Brazil/Acre">Brazil/Acre</option><option value="Brazil/DeNoronha">Brazil/DeNoronha</option><option value="Brazil/East">Brazil/East</option><option value="Brazil/West">Brazil/West</option><option value="CET">CET</option><option value="CST6CDT">CST6CDT</option><option value="Canada/Atlantic">Canada/Atlantic</option><option value="Canada/Central">Canada/Central</option><option value="Canada/East-Saskatchewan">Canada/East-Saskatchewan</option><option value="Canada/Eastern">Canada/Eastern</option><option value="Canada/Mountain">Canada/Mountain</option><option value="Canada/Newfoundland">Canada/Newfoundland</option><option value="Canada/Pacific">Canada/Pacific</option><option value="Canada/Saskatchewan">Canada/Saskatchewan</option><option value="Canada/Yukon">Canada/Yukon</option><option value="Chile/Continental">Chile/Continental</option><option value="Chile/EasterIsland">Chile/EasterIsland</option><option value="Cuba">Cuba</option><option value="EET">EET</option><option value="EST">EST</option><option value="EST5EDT">EST5EDT</option><option value="Egypt">Egypt</option><option value="Eire">Eire</option><option value="Etc/GMT">Etc/GMT</option><option value="Etc/GMT+0">Etc/GMT+0</option><option value="Etc/GMT+1">Etc/GMT+1</option><option value="Etc/GMT+10">Etc/GMT+10</option><option value="Etc/GMT+11">Etc/GMT+11</option><option value="Etc/GMT+12">Etc/GMT+12</option><option value="Etc/GMT+2">Etc/GMT+2</option><option value="Etc/GMT+3">Etc/GMT+3</option><option value="Etc/GMT+4">Etc/GMT+4</option><option value="Etc/GMT+5">Etc/GMT+5</option><option value="Etc/GMT+6">Etc/GMT+6</option><option value="Etc/GMT+7">Etc/GMT+7</option><option value="Etc/GMT+8">Etc/GMT+8</option><option value="Etc/GMT+9">Etc/GMT+9</option><option value="Etc/GMT-0">Etc/GMT-0</option><option value="Etc/GMT-1">Etc/GMT-1</option><option value="Etc/GMT-10">Etc/GMT-10</option><option value="Etc/GMT-11">Etc/GMT-11</option><option value="Etc/GMT-12">Etc/GMT-12</option><option value="Etc/GMT-13">Etc/GMT-13</option><option value="Etc/GMT-14">Etc/GMT-14</option><option value="Etc/GMT-2">Etc/GMT-2</option><option value="Etc/GMT-3">Etc/GMT-3</option><option value="Etc/GMT-4">Etc/GMT-4</option><option value="Etc/GMT-5">Etc/GMT-5</option><option value="Etc/GMT-6">Etc/GMT-6</option><option value="Etc/GMT-7">Etc/GMT-7</option><option value="Etc/GMT-8">Etc/GMT-8</option><option value="Etc/GMT-9">Etc/GMT-9</option><option value="Etc/GMT0">Etc/GMT0</option><option value="Etc/Greenwich">Etc/Greenwich</option><option value="Etc/UCT">Etc/UCT</option><option value="Etc/UTC">Etc/UTC</option><option value="Etc/Universal">Etc/Universal</option><option value="Etc/Zulu">Etc/Zulu</option><option value="Europe/Amsterdam">Europe/Amsterdam</option><option value="Europe/Andorra">Europe/Andorra</option><option value="Europe/Athens">Europe/Athens</option><option value="Europe/Belfast">Europe/Belfast</option><option value="Europe/Belgrade">Europe/Belgrade</option><option value="Europe/Berlin">Europe/Berlin</option><option value="Europe/Bratislava">Europe/Bratislava</option><option value="Europe/Brussels">Europe/Brussels</option><option value="Europe/Bucharest">Europe/Bucharest</option><option value="Europe/Budapest">Europe/Budapest</option><option value="Europe/Busingen">Europe/Busingen</option><option value="Europe/Chisinau">Europe/Chisinau</option><option value="Europe/Copenhagen">Europe/Copenhagen</option><option value="Europe/Dublin">Europe/Dublin</option><option value="Europe/Gibraltar">Europe/Gibraltar</option><option value="Europe/Guernsey">Europe/Guernsey</option><option value="Europe/Helsinki">Europe/Helsinki</option><option value="Europe/Isle_of_Man">Europe/Isle_of_Man</option><option value="Europe/Istanbul">Europe/Istanbul</option><option value="Europe/Jersey">Europe/Jersey</option><option value="Europe/Kaliningrad">Europe/Kaliningrad</option><option value="Europe/Kiev">Europe/Kiev</option><option value="Europe/Lisbon">Europe/Lisbon</option><option value="Europe/Ljubljana">Europe/Ljubljana</option><option value="Europe/London">Europe/London</option><option value="Europe/Luxembourg">Europe/Luxembourg</option><option value="Europe/Madrid">Europe/Madrid</option><option value="Europe/Malta">Europe/Malta</option><option value="Europe/Mariehamn">Europe/Mariehamn</option><option value="Europe/Minsk">Europe/Minsk</option><option value="Europe/Monaco">Europe/Monaco</option><option value="Europe/Moscow">Europe/Moscow</option><option value="Europe/Nicosia">Europe/Nicosia</option><option value="Europe/Oslo">Europe/Oslo</option><option value="Europe/Paris">Europe/Paris</option><option value="Europe/Podgorica">Europe/Podgorica</option><option value="Europe/Prague">Europe/Prague</option><option value="Europe/Riga">Europe/Riga</option><option value="Europe/Rome">Europe/Rome</option><option value="Europe/Samara">Europe/Samara</option><option value="Europe/San_Marino">Europe/San_Marino</option><option value="Europe/Sarajevo">Europe/Sarajevo</option><option value="Europe/Simferopol">Europe/Simferopol</option><option value="Europe/Skopje">Europe/Skopje</option><option value="Europe/Sofia">Europe/Sofia</option><option value="Europe/Stockholm">Europe/Stockholm</option><option value="Europe/Tallinn">Europe/Tallinn</option><option value="Europe/Tirane">Europe/Tirane</option><option value="Europe/Tiraspol">Europe/Tiraspol</option><option value="Europe/Uzhgorod">Europe/Uzhgorod</option><option value="Europe/Vaduz">Europe/Vaduz</option><option value="Europe/Vatican">Europe/Vatican</option><option value="Europe/Vienna">Europe/Vienna</option><option value="Europe/Vilnius">Europe/Vilnius</option><option value="Europe/Volgograd">Europe/Volgograd</option><option value="Europe/Warsaw">Europe/Warsaw</option><option value="Europe/Zagreb">Europe/Zagreb</option><option value="Europe/Zaporozhye">Europe/Zaporozhye</option><option value="Europe/Zurich">Europe/Zurich</option><option value="GB">GB</option><option value="GB-Eire">GB-Eire</option><option value="GMT">GMT</option><option value="GMT+0">GMT+0</option><option value="GMT-0">GMT-0</option><option value="GMT0">GMT0</option><option value="Greenwich">Greenwich</option><option value="HST">HST</option><option value="Hongkong">Hongkong</option><option value="Iceland">Iceland</option><option value="Indian/Antananarivo">Indian/Antananarivo</option><option value="Indian/Chagos">Indian/Chagos</option><option value="Indian/Christmas">Indian/Christmas</option><option value="Indian/Cocos">Indian/Cocos</option><option value="Indian/Comoro">Indian/Comoro</option><option value="Indian/Kerguelen">Indian/Kerguelen</option><option value="Indian/Mahe">Indian/Mahe</option><option value="Indian/Maldives">Indian/Maldives</option><option value="Indian/Mauritius">Indian/Mauritius</option><option value="Indian/Mayotte">Indian/Mayotte</option><option value="Indian/Reunion">Indian/Reunion</option><option value="Iran">Iran</option><option value="Israel">Israel</option><option value="Jamaica">Jamaica</option><option value="Japan">Japan</option><option value="Kwajalein">Kwajalein</option><option value="Libya">Libya</option><option value="MET">MET</option><option value="MST">MST</option><option value="MST7MDT">MST7MDT</option><option value="Mexico/BajaNorte">Mexico/BajaNorte</option><option value="Mexico/BajaSur">Mexico/BajaSur</option><option value="Mexico/General">Mexico/General</option><option value="NZ">NZ</option><option value="NZ-CHAT">NZ-CHAT</option><option value="Navajo">Navajo</option><option value="PRC">PRC</option><option value="PST8PDT">PST8PDT</option><option value="Pacific/Apia">Pacific/Apia</option><option value="Pacific/Auckland">Pacific/Auckland</option><option value="Pacific/Bougainville">Pacific/Bougainville</option><option value="Pacific/Chatham">Pacific/Chatham</option><option value="Pacific/Chuuk">Pacific/Chuuk</option><option value="Pacific/Easter">Pacific/Easter</option><option value="Pacific/Efate">Pacific/Efate</option><option value="Pacific/Enderbury">Pacific/Enderbury</option><option value="Pacific/Fakaofo">Pacific/Fakaofo</option><option value="Pacific/Fiji">Pacific/Fiji</option><option value="Pacific/Funafuti">Pacific/Funafuti</option><option value="Pacific/Galapagos">Pacific/Galapagos</option><option value="Pacific/Gambier">Pacific/Gambier</option><option value="Pacific/Guadalcanal">Pacific/Guadalcanal</option><option value="Pacific/Guam">Pacific/Guam</option><option value="Pacific/Honolulu">Pacific/Honolulu</option><option value="Pacific/Johnston">Pacific/Johnston</option><option value="Pacific/Kiritimati">Pacific/Kiritimati</option><option value="Pacific/Kosrae">Pacific/Kosrae</option><option value="Pacific/Kwajalein">Pacific/Kwajalein</option><option value="Pacific/Majuro">Pacific/Majuro</option><option value="Pacific/Marquesas">Pacific/Marquesas</option><option value="Pacific/Midway">Pacific/Midway</option><option value="Pacific/Nauru">Pacific/Nauru</option><option value="Pacific/Niue">Pacific/Niue</option><option value="Pacific/Norfolk">Pacific/Norfolk</option><option value="Pacific/Noumea">Pacific/Noumea</option><option value="Pacific/Pago_Pago">Pacific/Pago_Pago</option><option value="Pacific/Palau">Pacific/Palau</option><option value="Pacific/Pitcairn">Pacific/Pitcairn</option><option value="Pacific/Pohnpei">Pacific/Pohnpei</option><option value="Pacific/Ponape">Pacific/Ponape</option><option value="Pacific/Port_Moresby">Pacific/Port_Moresby</option><option value="Pacific/Rarotonga">Pacific/Rarotonga</option><option value="Pacific/Saipan">Pacific/Saipan</option><option value="Pacific/Samoa">Pacific/Samoa</option><option value="Pacific/Tahiti">Pacific/Tahiti</option><option value="Pacific/Tarawa">Pacific/Tarawa</option><option value="Pacific/Tongatapu">Pacific/Tongatapu</option><option value="Pacific/Truk">Pacific/Truk</option><option value="Pacific/Wake">Pacific/Wake</option><option value="Pacific/Wallis">Pacific/Wallis</option><option value="Pacific/Yap">Pacific/Yap</option><option value="Poland">Poland</option><option value="Portugal">Portugal</option><option value="ROC">ROC</option><option value="ROK">ROK</option><option value="Singapore">Singapore</option><option value="Turkey">Turkey</option><option value="UCT">UCT</option><option value="US/Alaska">US/Alaska</option><option value="US/Aleutian">US/Aleutian</option><option value="US/Arizona">US/Arizona</option><option value="US/Central">US/Central</option><option value="US/East-Indiana">US/East-Indiana</option><option value="US/Eastern">US/Eastern</option><option value="US/Hawaii">US/Hawaii</option><option value="US/Indiana-Starke">US/Indiana-Starke</option><option value="US/Michigan">US/Michigan</option><option value="US/Mountain">US/Mountain</option><option value="US/Pacific">US/Pacific</option><option value="US/Pacific-New">US/Pacific-New</option><option value="US/Samoa">US/Samoa</option><option value="UTC">UTC</option><option value="Universal">Universal</option><option value="W-SU">W-SU</option><option value="WET">WET</option><option value="Zulu">Zulu</option></select>
                </div>
                
                <div class="form-group" style="position:relative">
                    <label class="control-label"><?php _e('End Date','seedprod-coming-soon-pro') ?></label>
                    <input id="countdown_date" class="form-control input-sm" name="countdown_date" type="text" value="<?php echo $settings->countdown_date ?>">    
                </div>
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Format','seedprod-coming-soon-pro') ?></label>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e("Optional Format for display - upper case for always, lower case only if non-zero, 'D' days, 'H' hours, 'M' minutes, 'S' seconds. Default: dHMS",'seedprod-coming-soon-pro'); ?>"></i>
                    <input id="countdown_format" class="form-control input-sm" name="countdown_format" type="text" value="<?php echo $settings->countdown_format ?>">    
                </div>
                <div class="form-group"><label class="control-label"><?php _e('Auto Launch','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This will automatically launch your site when countdown reaches the end. The Admin will receive an email when the site is launched.','seedprod-coming-soon-pro'); ?>"></i>
                    <input id="countdown_launch" class="switchery" name="countdown_launch" type="checkbox" value="1" <?php echo (!empty($settings->countdown_launch) && $settings->countdown_launch == '1') ? 'checked' : '' ?>>       
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <a name="header-progress-bar"></a>
    <div class="panel-heading" role="tab" id="progress-bar">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-progress-bar" aria-expanded="false" aria-controls="collapse-progress-bar">
            <i class="fa fa-tasks"></i> <?php _e('Progress Bar','seedprod-coming-soon-pro') ?>
            </a>
        </h4>
    </div>
    <div id="collapse-progress-bar" class="panel-collapse collapse" role="tabpanel" aria-labelledby="progress-bar">
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label"><?php _e('Enable Progress Bar','seedprod-coming-soon-pro') ?></label>
                <input id="enable_progressbar" class="switchery" name="enable_progressbar" type="checkbox" value="1" <?php echo (!empty($settings->enable_progressbar) && $settings->enable_progressbar == '1') ? 'checked' : '' ?>>       
            </div>
            <div id="progress_bar_settings">
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Progress Bar Method','seedprod-coming-soon-pro') ?></label> 
                    <?php seed_cspv5_select('progress_bar_method',array('date'=>'Set a Start & End Date','percentage'=>'Set a Manual Pecentage'),$settings->progress_bar_method); ?>
                        
                </div>
                
                <div id="progress_bar_dates">
                    <div class="form-group" style="position: relative">
                        <label class="control-label"><?php _e('Start Date','seedprod-coming-soon-pro') ?></label>
                        <input id="progress_bar_start_date" class="form-control input-sm" name="progress_bar_start_date" type="text" value="<?php echo $settings->progress_bar_start_date ?>">     
                    </div>
                    <div class="form-group" style="position: relative">
                        <label class="control-label"><?php _e('End Date','seedprod-coming-soon-pro') ?></label>
                        <input id="progress_bar_end_date" class="form-control input-sm" name="progress_bar_end_date" type="text" value="<?php echo $settings->progress_bar_end_date ?>">      
                    </div>
                </div>
                <div id="progress_bar_pecentage">
                    <div class="form-group">
                        <label class="control-label"><?php _e('Percent Complete Override','seedprod-coming-soon-pro') ?></label>
                        <input id="progressbar_percentage" name="progressbar_percentage" type="hidden" value="<?php echo $settings->progressbar_percentage ?>">
                        <div class="bg-master m-b-10 m-t-40" id="progressbar_percentage_slider"></div>
                    </div>
                </div>
             
            </div>
        </div>
    </div>
</div>



<div class="panel panel-default">
    <a name="header-footer-credit-settings"></a>
    <div class="panel-heading" role="tab" id="footer-credit-settings">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-footer-credit-settings" aria-expanded="false" aria-controls="collapse-footer-credit-settings">
            <i class="fa fa-long-arrow-down"></i> <?php _e('Footer Credit Settings','seedprod-coming-soon-pro') ?>
            </a>
        </h4>
    </div>
    <div id="collapse-footer-credit-settings" class="panel-collapse collapse" role="tabpanel" aria-labelledby="footer-credit-settings">
        <div class="panel-body">
          <div class="form-group">
                <label class="control-label"><?php _e('Enable Footer Credit','seedprod-coming-soon-pro') ?></label>
                <input id="enable_footercredit" class="switchery" name="enable_footercredit" type="checkbox" value="1" <?php echo (!empty($settings->enable_footercredit) && $settings->enable_footercredit == '1') ? 'checked' : '' ?>>       
            </div>
            <div id="footercredit_settings">
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Credit Type','seedprod-coming-soon-pro') ?></label> 
                    <?php seed_cspv5_select('credit_type',array('text'=>'Custom Text','image'=>'Custom Image','affiliate'=> 'Affiliate Link'),$settings->credit_type); ?>
                        
                </div>
                
                <div class="form-group">
                    <label class="control-label"><?php _e('Credit Position','seedprod-coming-soon-pro') ?></label> 
                    <?php seed_cspv5_select('credit_position',array('float_left'=>'Float Left','center'=>'Center','float_right'=> 'Float Right'),(!empty($settings->credit_position)) ? $settings->credit_position : 'float_right'); ?>
                        
                </div>
                <div class="form-group">
                    <label class="control-label"><?php _e('Credit Text','seedprod-coming-soon-pro') ?></label>
                    <input id="footer_credit_text" class="form-control input-sm" name="footer_credit_text" type="text" value="<?php echo htmlentities($settings->footer_credit_text,ENT_QUOTES, "UTF-8") ?>">    
                </div>
                <div class="form-group">
                    <label class="control-label"><?php _e('Credit Image','seedprod-coming-soon-pro') ?></label>
                    <input id="footer_credit_img" class="form-control input-sm" name="footer_credit_img" type="hidden" value="<?php echo  $settings->footer_credit_img ?>">
                   <input id='footer_credit_img_upload_image_button' class='button-primary upload-button' type='button' value='<?php _e( 'Choose Image', 'seedprod-coming-soon-pro' ) ?>' /><br>
                        <?php if(!empty($settings->logo)): ?>
                        <div class="img-preview">
                            <img id="footer_credit_img-preview" src="<?php echo  $settings->footer_credit_img ?>">
                            <?php else: ?>
                            <div class="img-preview" style="display:none;">
                                <img id="footer_credit_img-preview" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif" >
                            <?php endif; ?>
                            <i class="fa fa-close"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php _e('Credit Link','seedprod-coming-soon-pro') ?></label>
                        <input id="footer_credit_link" class="form-control input-sm" name="footer_credit_link" type="text" value="<?php echo $settings->footer_credit_link ?>">       
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php _e('Credit Color','seedprod-coming-soon-pro') ?></label>
                        <div class="input-group footer_text_color_picker">
                            <input id="footer_text_color" class="form-control input-sm" data-format="hex" name="footer_text_color" type="text" value="<?php echo (!empty($settings->footer_text_color)) ? $settings->footer_text_color : $settings->button_color ?>">  
                            <span class="input-group-addon"><i></i></span>  
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php _e('Affiliate Link','seedprod-coming-soon-pro') ?></label>
                        <input id="footer_affiliate_link" class="form-control input-sm" name="footer_affiliate_link" type="text" value="<?php echo $settings->footer_affiliate_link ?>">       
                    </div>
                </div>
        </div>
    </div>
</div>
                <div class="panel panel-default">
                    <a name="header-language-settings"></a>
                    <div class="panel-heading" role="tab" id="language-settings">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-language-settings" aria-expanded="false" aria-controls="collapse-language-settings">
                            <i class="fa fa-language"></i> <?php _e('Customize Text','seedprod-coming-soon-pro') ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse-language-settings" class="panel-collapse collapse" role="tabpanel" aria-labelledby="language-settings">
                        <div class="panel-body">
                            <?php if(seed_cspv5_cu('ml')){ ?>
                            <div class="form-group">
                                <button type="button" id="language-builder" class="button-primary"><?php _e('Manage Multiple Languages','seedprod-coming-soon-pro') ?></button>  
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Subscribe Button','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_subscribe_button" class="form-control input-sm" name="txt_subscribe_button" type="text" value="<?php echo htmlentities($settings->txt_subscribe_button,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Email Field','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_email_field" class="form-control input-sm" name="txt_email_field" type="text" value="<?php echo htmlentities($settings->txt_email_field,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Name Field','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_name_field" class="form-control input-sm" name="txt_name_field" type="text" value="<?php echo htmlentities($settings->txt_name_field,ENT_QUOTES, "UTF-8") ?>">      
                            </div>
                            <div class="form-group">
                                <label class="control-label" ><?php _e('Privacy Policy Text','seedprod-coming-soon-pro') ?></label>
                                <input id="privacy_policy_link_text" class="form-control input-sm" name="privacy_policy_link_text" type="text" value="<?php echo htmlentities((empty($settings->privacy_policy_link_text))?'':$settings->privacy_policy_link_text,ENT_QUOTES, "UTF-8") ?>">       
                            </div>

                            <div class="form-group">
                                <label class="control-label" ><?php _e('Privacy Policy More Text (This will open a pop up with more text.)','seedprod-coming-soon-pro') ?></label>
                                <input id="privacy_policy" class="form-control input-sm" name="privacy_policy" type="text" value="<?php echo htmlentities((empty($settings->privacy_policy))?'':$settings->privacy_policy,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Already Subscribed','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_already_subscribed_msg" class="form-control input-sm" name="txt_already_subscribed_msg" type="text" value="<?php echo htmlentities($settings->txt_already_subscribed_msg,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Invalid Email','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_invalid_email_msg" class="form-control input-sm" name="txt_invalid_email_msg" type="text" value="<?php echo htmlentities($settings->txt_invalid_email_msg,ENT_QUOTES, "UTF-8") ?>">   
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php _e('Invalid Name','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_invalid_name_msg" class="form-control input-sm" name="txt_invalid_name_msg" type="text" value="<?php echo htmlentities($settings->txt_invalid_name_msg,ENT_QUOTES, "UTF-8") ?>">   
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php _e('Optin Confirmation Required','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_optin_confirmation_required" class="form-control input-sm" name="txt_optin_confirmation_required" type="text" value="<?php echo htmlentities((!empty($settings->txt_optin_confirmation_required)) ?$settings->txt_optin_confirmation_required : 'Optin Confirmation Required',ENT_QUOTES, "UTF-8") ?>">   
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Referral URL Message','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_stats_referral_url" class="form-control input-sm" name="txt_stats_referral_url" type="text" value="<?php echo htmlentities($settings->txt_stats_referral_url,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Referral Stats Message','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_stats_referral_stats" class="form-control input-sm" name="txt_stats_referral_stats" type="text" value="<?php echo htmlentities($settings->txt_stats_referral_stats,ENT_QUOTES, "UTF-8") ?>">        
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Referral Stats Clicks','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_stats_referral_clicks" class="form-control input-sm" name="txt_stats_referral_clicks" type="text" value="<?php echo htmlentities($settings->txt_stats_referral_clicks,ENT_QUOTES, "UTF-8") ?>">      
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Referral Stats Subscribers','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_stats_referral_subscribers" class="form-control input-sm" name="txt_stats_referral_subscribers" type="text" value="<?php echo htmlentities($settings->txt_stats_referral_subscribers,ENT_QUOTES, "UTF-8") ?>">        
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Days','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_days" class="form-control input-sm" name="txt_countdown_days" type="text" value="<?php echo htmlentities($settings->txt_countdown_days,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Day','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_day" class="form-control input-sm" name="txt_countdown_day" type="text" value="<?php echo htmlentities($settings->txt_countdown_day,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Hours','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_hours" class="form-control input-sm" name="txt_countdown_hours" type="text" value="<?php echo htmlentities($settings->txt_countdown_hours,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Hour','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_hour" class="form-control input-sm" name="txt_countdown_hour" type="text" value="<?php echo htmlentities($settings->txt_countdown_hour,ENT_QUOTES, "UTF-8") ?>">      
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Minutes','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_minutes" class="form-control input-sm" name="txt_countdown_minutes" type="text" value="<?php echo htmlentities($settings->txt_countdown_minutes,ENT_QUOTES, "UTF-8") ?>">      
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Minute','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_minute" class="form-control input-sm" name="txt_countdown_minute" type="text" value="<?php echo htmlentities($settings->txt_countdown_minute,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Seconds','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_seconds" class="form-control input-sm" name="txt_countdown_seconds" type="text" value="<?php echo htmlentities($settings->txt_countdown_seconds,ENT_QUOTES, "UTF-8") ?>">       
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Second','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_countdown_second" class="form-control input-sm" name="txt_countdown_second" type="text" value="<?php echo htmlentities($settings->txt_countdown_second,ENT_QUOTES, "UTF-8") ?>">     
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Contact Form Label','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_contact_us" class="form-control input-sm" name="txt_contact_us" type="text" value="<?php echo (empty($settings->txt_contact_us))? 'Contact Us' : htmlentities($settings->txt_contact_us,ENT_QUOTES, "UTF-8") ?>">     
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Contact Form Email','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_contact_form_email" class="form-control input-sm" name="txt_contact_form_email" type="text" value="<?php echo (empty($settings->txt_contact_form_email))? 'Email' :htmlentities($settings->txt_contact_form_email,ENT_QUOTES, "UTF-8") ?>">     
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Contact Form Message','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_contact_form_msg" class="form-control input-sm" name="txt_contact_form_msg" type="text" value="<?php echo(empty($settings->txt_contact_form_msg))? 'Message' :htmlentities($settings->txt_contact_form_msg,ENT_QUOTES, "UTF-8") ?>">     
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Contact Form Send','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_contact_form_send" class="form-control input-sm" name="txt_contact_form_send" type="text" value="<?php echo(empty($settings->txt_contact_form_send))? 'Send' :htmlentities($settings->txt_contact_form_send,ENT_QUOTES, "UTF-8") ?>">     
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php _e('Contact Form Error','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_contact_form_error" class="form-control input-sm" name="txt_contact_form_error" type="text" value="<?php echo (empty($settings->txt_contact_form_error))? 'Please enter your email and a message.' :htmlentities($settings->txt_contact_form_error,ENT_QUOTES, "UTF-8") ?>">     
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?php _e('Prize Level Refer Message','seedprod-coming-soon-pro') ?></label>
                                <input id="txt_prize_level_more" class="form-control input-sm" name="txt_prize_level_more" type="text" value="<?php echo (empty($settings->txt_prize_level_more))? 'Refer %d more subscribers to claim this.' :htmlentities($settings->txt_prize_level_more,ENT_QUOTES, "UTF-8") ?>"> 
                                <small>Default: Refer %d more subscribers to claim this.<br>Note: %d is the number placeholder and is required.</small>   
                            </div>
                        </div>
                    </div>
                </div>
                       <div class="panel panel-default">
            <a name="header-custom-css"></a>
            <div class="panel-heading" role="tab" id="custom-css">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-custom-css" aria-expanded="false" aria-controls="collapse-custom-css">
                    <i class="fa fa-css3"></i> <?php _e('Custom CSS','seedprod-coming-soon-pro') ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-custom-css" class="panel-collapse collapse" role="tabpanel" aria-labelledby="custom-css">
                <div class="panel-body">
                    <div class="form-group">
                                <label class="control-label"><?php _e('Custom CSS','seedprod-coming-soon-pro') ?></label>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Need to tweak the styles? Add your custom CSS here.','seedprod-coming-soon-pro') ?>"></i>
                    
                                <textarea id="theme_css" class="form-control input-sm" name="theme_css" cols="50" rows="10" style="display:none"><?php  echo ( empty($settings->theme_css) ) ? '' : $settings->theme_css ?></textarea> 
                                <textarea id="theme_scripts" class="form-control input-sm" name="theme_scripts" cols="50" rows="10" style="display:none"><?php echo ( empty($settings->theme_scripts) ) ? '' : $settings->theme_scripts ?></textarea> 
                                <textarea id="custom_css" class="form-control input-sm" name="custom_css" cols="50" rows="10"><?php echo $settings->custom_css ?></textarea>        
                            </div>
                </div>
            </div>
        </div>
                <div class="panel panel-default">
                    <a name="header-advanced-settings"></a>
                    <div class="panel-heading" role="tab" id="advanced-settings">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-advanced-settings" aria-expanded="false" aria-controls="collapse-advanced-settings">
                            <i class="fa fa-code"></i> <?php _e('Advanced Scripts','seedprod-coming-soon-pro') ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse-advanced-settings" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced-settings">
                        <div class="panel-body">
                            <div class="form-group"><label class="control-label"><?php _e('Enable 3rd Party Plugins','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This allows other plugins to work inside the landing page. If you are unsure do not enable it. This can cause styling issues with the page.','seedprod-coming-soon-pro'); ?>"></i>
                                <input id="enable_wp_head_footer" class="switchery" name="enable_wp_head_footer" type="checkbox" value="1" <?php echo (!empty($settings->enable_wp_head_footer) && $settings->enable_wp_head_footer == '1') ? 'checked' : '' ?>>       
                            </div>
                            <div class="form-group"><label class="control-label"><?php _e('Enable FitVid','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Makes your videos responsive.','seedprod-coming-soon-pro'); ?>"></i>
                                <input id="enable_fitvid" class="switchery" name="enable_fitvid" type="checkbox" value="1" <?php echo (!empty($settings->enable_fitvid) && $settings->enable_fitvid == '1') ? 'checked' : '' ?>>       
                            </div>

                            <div class="form-group"><label class="control-label"><?php _e('Enable RetinaJS','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Serve high-resolution images to devices with retina displays if available.','seedprod-coming-soon-pro'); ?>"></i>
                                <input id="enable_retinajs" class="switchery" name="enable_retinajs" type="checkbox" value="1" <?php echo (!empty($settings->enable_retinajs) && $settings->enable_retinajs == '1') ? 'checked' : '' ?>>       
                            </div>

                            

                            <div class="form-group"><label class="control-label"><?php _e('Enable Recaptcha','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e("Prevent spam by enabling Google's Recaptcha",'seedprod-coming-soon-pro'); ?>"></i>
                                <input id="enable_recaptcha" class="switchery" name="enable_recaptcha" type="checkbox" value="1" <?php echo (!empty($settings->enable_recaptcha) && $settings->enable_recaptcha == '1') ? 'checked' : '' ?>>       
                            </div>
                             <div id="recaptcha_adv_settings">
                             <div class="form-group"><label class="control-label"><?php _e('Use Invisible Recaptcha','seedprod-coming-soon-pro') ?></label> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e("INstead of Using Recaptch V2 this will use the Invisible Recaptcha",'seedprod-coming-soon-pro'); ?>"></i>
                                <input id="enable_invis_recaptcha" class="switchery" name="enable_invis_recaptcha" type="checkbox" value="1" <?php echo (!empty($settings->enable_invis_recaptcha) && $settings->enable_invis_recaptcha == '1') ? 'checked' : '' ?>>       
                            </div>
                           
                              <div class="form-group">
                                <label class="control-label"><?php _e('Recaptcha Site Key','seedprod-coming-soon-pro') ?></label>
                                <input id="recaptcha_site_key" class="form-control input-sm" name="recaptcha_site_key" type="text" value="<?php if(isset($settings->recaptcha_site_key)){ echo htmlentities($settings->recaptcha_site_key,ENT_QUOTES, "UTF-8");} ?>">     
                            </div>
                              <div class="form-group">
                                <label class="control-label"><?php _e('Recaptcha Secret Key','seedprod-coming-soon-pro') ?></label>
                                <input id="recaptcha_secret_key" class="form-control input-sm" name="recaptcha_secret_key" type="text" value="<?php if(isset($settings->recaptcha_secret_key)){ echo htmlentities($settings->recaptcha_secret_key,ENT_QUOTES, "UTF-8");} ?>">     
                            </div>
                            </div>
                            
                         
                            <div class="form-group">
                                <label class="control-label"><?php _e('Header Scripts','seedprod-coming-soon-pro') ?></label>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Enter any custom scripts. You can enter Javascript or CSS. This will be rendered before the closing head tag.','seedprod-coming-soon-pro') ?>"></i>
                                <textarea id="header_scripts" class="form-control input-sm" name="header_scripts" cols="50" rows="10"><?php echo $settings->header_scripts ?></textarea>        
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Footer Scripts','seedprod-coming-soon-pro') ?></label>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Enter any custom scripts. This will be rendered before the closing body tag.','seedprod-coming-soon-pro') ?>"></i>
                                <textarea id="footer_scripts" class="form-control input-sm" name="footer_scripts" cols="50" rows="10"><?php echo $settings->footer_scripts ?></textarea>        
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"><?php _e('Conversion Scripts','seedprod-coming-soon-pro') ?></label>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('This will render only after the form has been submitted. This will be rendered before the closing body tag.','seedprod-coming-soon-pro') ?>"></i>
                                <textarea id="conversion_scripts" class="form-control input-sm" name="conversion_scripts" cols="50" rows="10"><?php echo $settings->conversion_scripts ?></textarea>        
                            </div>

                            
                            <div class="form-group">

                                <label class="control-label"><?php _e('Import/Export Settings','seedprod-coming-soon-pro') ?></label>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('Paste in your settings and click Import to import your settings. Click Export to get your settings.','seedprod-coming-soon-pro') ?>"></i>
                                <textarea id="import_export_settings" class="form-control input-sm" name="import_settings" cols="50" rows="10"></textarea> 
                                <br>
                                <button class="button-primary" id="import-settings">Import Settings</button>
                                <button class="button-primary" id="export-settings">Export Settings</button>    
                            </div>
                            
                            <div class="form-group">

                                <label class="control-label"><?php _e('Edit Rendered HTML','seedprod-coming-soon-pro') ?></label>
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php _e('By editing the page\'s html you will no longer be able to use the customizer settings. The html below instead will be used.','seedprod-coming-soon-pro') ?>"></i>
                                <p>Use with caution! By editing the page's html you will no longer be able to use the customizer settings above. The html below instead will be used. Remove any html to go back to using the customizer settings. <a href="https://secure.helpscout.net/docs/53c018c6e4b0bbe0c00d5e98/article/53cfb5ebe4b07bed53571775/" target="_blank">Learn how to use this setting.</a></p>
                                <textarea id="html" class="form-control input-sm" cols="50" rows="10"><?php echo $page->html ?></textarea> 
                                <br>
                                <button class="button-primary" id="get_html">Get HTML</button> 
                                <button class="button-primary" id="save_html">Save HTML</button>   
                            </div>
                            <input type="hidden" id="check_mic" name="check_mic" value="sibilance">

                        </div>
                    </div>
                </div>
               
            </div>
            <input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>">
            </form>

        </div>
        <div id="dragbar"></div>
        <!-- /.sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- END SIDEBAR MENU -->
</div>
<!-- END SIDEBAR -->

<!-- START PAGE-CONTAINER -->
<div class="page-container">
    <!-- START PAGE HEADER WRAPPER -->
    <!-- END PAGE HEADER WRAPPER -->
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper">
        <!-- START PAGE CONTENT -->
        <div class="content">
            <!-- START CONTAINER FLUID -->
            <div class="container-fluid container-fixed-lg">
                <div id="ajax-status"><img src="<?php echo admin_url() ?>/images/spinner.gif"></div>
                <!-- BEGIN PlACE PAGE CONTENT HERE -->
                <div id="preview-wrapper" class="main" data-step="3" data-intro="All your changes will show up in the Preview window as you make them.">
                    <iframe id="preview" src="<?php echo home_url('/','relative').'?seed_cspv5_preview='. $page_id?>" ></iframe>  
                </div>
                <!-- END PLACE PAGE CONTENT HERE -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTAINER -->


<!-- Image Picker Modal --> 
<div class="modal" id="image-picker" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Stock Background Images','seedprod-coming-soon-pro' ) ?></h4>
            </div>
            <div class="modal-body">...</div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 


<!-- JS -->

<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>template/js/bootstrap.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/moment.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.repeatable.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.validate.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.nouislider.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.liblink.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/switchery.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/wNumb.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>template/js/select2.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/fontawesome-iconpicker.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.fitvids.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/jquery.color.min.js"></script>
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/js/csp-app.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js" type="text/javascript"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/1.1.1/intro.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.5/clipboard.min.js"></script> -->
<script src="<?php echo SEED_CSPV5_PLUGIN_URL ?>admin/field-types/js/upload.js"></script>

<!-- App JS -->
<script>

var blank_gif = '<?php echo SEED_CSPV5_PLUGIN_URL ?>customizer/img/blank.gif';
var page_id = '<?php echo $page_id; ?>';
var refresh = true;
var timeout;
var page = '1';
var query = '';
var preview_url = '<?php echo home_url('/','relative'); ?>?seed_cspv5_preview=<?php echo $page_id; ?>';
var path = '<?php echo admin_url(); ?>';
var s_c = <?php echo $s_c; ?>;
var progressbar_percentage = <?php echo ( empty($settings->progressbar_percentage) ) ? '0' : $settings->progressbar_percentage ?>;

var form_width = <?php echo ( empty($settings->form_width) ) ? '100' : $settings->form_width ?>;
<?php $backgrounds_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_backgrounds','seed_cspv5_backgrounds')); ?>
var index_backgrounds = "<?php echo $backgrounds_ajax_url; ?>";
<?php $backgrounds_sideload_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_backgrounds_sideload','seed_cspv5_backgrounds_sideload')); ?>
var sideload_backgrounds = "<?php echo $backgrounds_sideload_ajax_url; ?>";

<?php $backgrounds_download_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_backgrounds_download','seed_cspv5_backgrounds_download')); ?>
var download_backgrounds = "<?php echo $backgrounds_download_ajax_url; ?>";

var container_effect_animation = '<?php echo $settings->container_effect_animation ?>';
var container_radius = <?php echo ( empty($settings->container_radius) ) ? '0' : $settings->container_radius ?>;
var bg_slideshow_slide_speed = <?php echo ( empty($settings->bg_slideshow_slide_speed) ) ? '0' : $settings->bg_slideshow_slide_speed ?>;
var container_width = <?php echo ( empty($settings->container_width) ) ? '0' : absint($settings->container_width) ?>;

var headline_size = <?php echo ( empty($settings->headline_size) || $settings->headline_size == 'false' ) ? '42' : $settings->headline_size ?>;
var headline_line_height = <?php echo ( empty($settings->headline_line_height) || $settings->headline_line_height == 'false' ) ? '1' : $settings->headline_line_height ?>;
var text_size = <?php echo ( empty($settings->text_size) || $settings->text_size == 'false' ) ? '16' : $settings->text_size ?>;
var text_line_height = <?php echo ( empty($settings->text_line_height) || $settings->text_line_height == 'false' ) ? '1.5' : $settings->text_line_height ?>;
var headline_weight = '<?php echo (empty($settings->headline_weight))?'400':$settings->headline_weight ?>';
var headline_subset = '<?php echo $settings->headline_subset ?>';
var text_weight = '<?php echo $settings->text_weight ?>';
var text_subset = '<?php echo $settings->text_subset ?>';
var button_weight = '<?php echo $settings->button_weight ?>';
var button_subset = '<?php echo $settings->button_subset ?>';
<?php $save_page_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_page','seed_cspv5_save_page')); ?>
var save_url = "<?php echo $save_page_ajax_url; ?>";

<?php $get_html_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_get_html','seed_cspv5_get_html')); ?>
var get_html_url = "<?php echo $get_html_ajax_url; ?>";

<?php $save_html_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_save_html','seed_cspv5_save_html','html_wpnonce')); ?>
var save_html_url = "<?php echo $save_html_ajax_url; ?>";

var countdown_timezone = '<?php echo $settings->countdown_timezone ?>';
var theme_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_themes";
var prize_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_prizes";
var form_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_form";
var autoresponder_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_autoresponder";
var language_link = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5_language";
var view_subscribers_url = "<?php echo admin_url(); ?>options-general.php?page=seed_cspv5&tab=seed_cspv5_tab_subscribers&page_id=<?php echo $page_id; ?>";

<?php $export_page_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_export_page_settings','seed_cspv5_export_page_settings')); ?>
var export_page_ajax_url = "<?php echo $export_page_ajax_url; ?>";

<?php $import_page_ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_import_page_settings','seed_cspv5_import_page_settings')); ?>
var import_page_ajax_url = "<?php echo $import_page_ajax_url; ?>";

jQuery.fn.datetimepicker.defaults.icons = {
    time: 'fa fa-clock-o',
    date: 'fa fa-calendar',
    up: 'fa fa-chevron-up',
    down: 'fa fa-chevron-down',
    previous: 'fa fa-chevron-left',
    next: 'fa fa-chevron-right',
    today: 'fa fa-dot-circle-o',
    clear: 'fa fa-trash',
    close: 'fa fa-times'
};

</script>

<script type="text/template" id="social_profiles_template">
<div class="field-group">
    <div class="btn-group" >
        <button id="bicon_{?}" data-selected="graduation-cap" type="button" class="icp icp-dd btn btn-default btn-sm dropdown-toggle iconpicker-component" data-toggle="dropdown">
            Icon  <i class="fa fa-fw"></i>
            <span class="caret"></span>
        </button>
        <div class="dropdown-menu"></div>
    </div>
                               
    <div class="input-group">
    <div class="input-group-addon"><i class="fa fa-bars"></i></div>
    <input type="text" name="social_profiles[{?}][url]" value=""  id="social_profiles_{?}" class="form-control input-sm" placeholder="<?php _e('Enter Url','seedprod-coming-soon-pro') ?>" />
    <input type="hidden" name="social_profiles[{?}][icon]" value="" id="icon_{?}"  class="form-control input-sm"/>
    <div class="input-group-addon slide-delete"><i class="fa fa-close delete"></i></div>

    </div>
</div>
</script>


<script type="text/javascript">

    var google_fonts = <?php echo $fonts_json; ?>;

    <?php if(isset($_GET['tab']) && $_GET['tab'] == 'design'){ ?>
    jQuery("#collapse-theme").collapse('show');
    <?php } ?>
    
    <?php if(isset($_GET['tab']) && $_GET['tab'] == 'content'){ ?>
    jQuery("#collapse-ontent-settings").collapse('show');
    <?php } ?>

    <?php if(isset($_GET['tab']) && $_GET['tab'] == 'form'){ ?>
    jQuery("#collapse-email-form").collapse('show');
    <?php } ?>
    
    // Sidebar drag
    var i = 0;
    jQuery('#dragbar').mousedown(function(e) {

    e.preventDefault();
    jQuery("#preview").hide();
    jQuery(window.top).mousemove(function(e) {
        if(e.pageX > 300){    
        jQuery('#seed-cspv5-sidebar,#preview-actions').css("width", e.pageX + 2);
        jQuery('.page-container').css("padding-left", e.pageX + 2);
        }
    });
    });

    jQuery(window.top).mouseup(function(e) {
        jQuery(window.top).unbind('mousemove');
        jQuery("#preview").show();
    });
    
</script>

</div>


