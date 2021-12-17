<div id="a2w-edit-image" class="a2w-modal-wrapper" style="position: relative; display: none;">
    <div tabindex="0" class="a2w-modal wp-core-ui">
        <button type="button" class="a2w-modal-close"><span class="a2w-modal-icon"></span></button>
        <div class="a2w-modal-title">
            <div class="actions">
                <a href="#" id="btn-clear-objects"><span class="dashicons dashicons-image-rotate"></span></a>
                <a href="#" id="btn-undo"><span class="dashicons dashicons-undo"></span></a>
                <a href="#" id="btn-redo"><span class="dashicons dashicons-redo"></span></a>
            </div>
        </div>
        <div class="a2w-modal-content">
            <div class="a2w-edit-photo-loader">
                <div><span class="spinner"></span><span class="message"><?php _e('Loading', 'ali2woo'); ?>...</span></div>
            </div>
            <div class="a2w-edit-photo-container">
                <div class="tui-image-editor"></div>
                <div class="tui-image-editor-controls">
                    <div class='controls-content'>
                        <div class="sub-menu-container" id="crop-sub-menu">
                            
                            <div class="manual-crop-items">
                                <a href="#" class="manual-crop"><?php _e('Manual crop', 'ali2woo'); ?></a>    
                                <div class="actions" style="display:none"><a href="#" class="button-primary apply"><?php _e('Apply', 'ali2woo'); ?></a> <a href="#" class="cancel"><?php _e('Cancel', 'ali2woo'); ?></a></div>
                            </div>
                            <div class="crop-items">
                                <div class="crop-item"><a href="#" class="crop" data-type="original"><span class="crop-recr" style="padding-bottom: 80%;"></span><span class="name"><?php _e('original ratio', 'ali2woo'); ?></span></a></div>
                                <div class="crop-item"><a href="#" class="crop" data-type="1x1 "><span class="crop-recr" style="padding-bottom: 80%;"></span><span class="name">1x1</span></a></div>
                                <div class="crop-item"><a href="#" class="crop" data-type="5x4"><span class="crop-recr" style="padding-bottom: 60%;"></span><span class="name">5x4</span></a></div>
                                <div class="crop-item"><a href="#" class="crop" data-type="4x3"><span class="crop-recr" style="padding-bottom: 55%;"></span><span class="name">4x3</span></a></div>
                                <div class="crop-item"><a href="#" class="crop" data-type="3x2"><span class="crop-recr" style="padding-bottom: 46.67%;"></span><span class="name">3x2</span></a></div>
                                <div class="crop-item"><a href="#" class="crop" data-type="16x9"><span class="crop-recr" style="padding-bottom: 36.25%;"></span><span class="name">16x9</span></a></div>
                            </div>
                        </div>
                        <div class="sub-menu-container menu" id="draw-line-sub-menu">
                            <div class="block-title"><?php _e('Color', 'ali2woo'); ?>:</div>
                            <div class="block">
                                <div><input type="text" id="color-picker"/></div>
                                <div><a href="#" class="get-color"><span></span></a></div>
                            </div>

                            <div class="block-title"><?php _e('Size', 'ali2woo'); ?>:</div>
                            <div class="block">
                                <div style="width:100%"><input id="input-brush-width-range" type="range" min="1" max="50" value="10"></div>
                            </div>
                        </div>
                        <div class="sub-menu-container menu" id="filter-sub-menu">

                            <div class="block">
                                <div class="block-item input-wrapper">
                                    <span class="upload-icon"></span>
                                    <?php _e('Upload', 'ali2woo'); ?>
                                    <input type="file" accept="image/*" id="input-mask-image-file">
                                </div>
                                <?php foreach($srickers as $sricker):?>
                                    <div class="block-item"><a href="#" class="sticker"><img src="<?php echo $sricker ?>"/></a></div>
                                <?php endforeach;?>
                            </div>

                        </div>
                        
                        <div class="sub-menu-container menu" id="draw-text-sub-menu">
                            <div class="block-title"><?php _e('Color', 'ali2woo'); ?>:</div>
                            <div class="block">
                                <div><input type="text" id="text-color-picker"/></div>
                                <div><a href="#" class="get-color text"><span></span></a></div>
                            </div>

                            <div class="block-title"><?php _e('Size', 'ali2woo'); ?>:</div>
                            <div class="block">
                                <select id="input-text-size">
                                    <?php $default_size = 20; ?>
                                    <?php for($i=8;$i<=50;$i++):?>
                                    <option value="<?php echo $i;?>"<?php if($default_size===$i):?> selected<?php endif; ?>><?php echo $i;?></option>
                                    <?php endfor;?>
                                </select>
                                
                                <a href="#" class="btn-text-style" data-style-type="b"><span class="dashicons dashicons-editor-bold"></span></a>
                                <a href="#" class="btn-text-style" data-style-type="i"><span class="dashicons dashicons-editor-italic"></span></a>
                                <a href="#" class="btn-text-style" data-style-type="u"><span class="dashicons dashicons-editor-underline"></span></a>
                            </div>
                            
                        </div>
                    </div>
                    <div class="controls-menu">
                        <a href="#" id="btn-crop"><span class="dashicons dashicons-image-crop"></span></a>
                        <a href="#" id="btn-draw-line"><span class="dashicons dashicons-admin-customizer"></span></a>
                        <a href="#" id="btn-mask-filter"><span class="dashicons dashicons-format-image"></span></a>
                        <a href="#" id="btn-draw-text"><span class="dashicons dashicons-editor-textcolor"></span></a>
                    </div>
                </div>
            </div>            

        </div>
        <div class="a2w-modal-toolbar">
            <span class="spinner"></span>
            <button type="button" class="button-primary save-image"><?php _e('Save', 'ali2woo'); ?></button>
            <button type="button" class="button cancel-image"><?php _e('Cancel', 'ali2woo'); ?></button>
        </div>
    </div>
    <div class="a2w-modal-backdrop"></div>
</div>


