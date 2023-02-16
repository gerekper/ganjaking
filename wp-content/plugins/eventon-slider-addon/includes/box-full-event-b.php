<div class="eo_fullevent_box_s2" id="<?php echo $id; ?>-eoas-full-event-box" style="display:none">
    <div class="<?php echo $skin ?> full_content_box">
        <a class="eoas_evopopclose_2" onclick="jQuery('#<?php echo $id; ?>-eoas-full-event-box').trigger('close');">X</a>
        <div class="eo_boxs2_img" id="<?php echo $id; ?>_eo_image"  onclick="animaImage(this)">
            <div class="eo_boxs2_header">
                <div class="eo_boxs2_col">
                    <p class="boxs2_col" id="<?php echo $id; ?>_eo_date"></p>
                    <p class="boxs2_col2" id="<?php echo $id; ?>_eo_date_m"></p>
                </div>
                <div class="eo_boxs2_col2">
                    <div id="<?php echo $id; ?>_eo_title"></div>
                </div>
            </div>
            <div class="eo_boxs2_row oflow" id="<?php echo $id; ?>_eo_subtitle"></div>
        </div>
        <div class="eo_boxs2_datetime_box">
            <div class="boxs2_co oflow"><i class="fa fa-map-marker eos_color"></i><span id="<?php echo $id; ?>_eo_location">--</span></div>
            <hr />
            <div class="boxs2_co oflow"><i class="fa fa-clock-o eos_color"></i><span id="<?php echo $id; ?>_eo_time_long">--</span></div>
        </div>
        <div class="eo_boxs2_b">
            <div class="cancelled_label"><?php echo $lan_arr['evcal_evcard_evcancel'] ?></div>
            <p class="eo_boxs2_desc_tit"><i class="fa fa-align-justify eos_color"></i><?php echo $lan_arr['evcal_evcard_details'] ?></p>
            <p class="eo_boxs2_desc" id="<?php echo $id; ?>_eo_desc">... </p>
            <div class="eoas_eventon_descButton" id="<?php echo $id; ?>_descButton"><span onclick="scrollContentT('<?php echo $id; ?>','<?php echo $id; ?>_descButton');"><?php echo $lan_arr['evcal_lang_more'] ?></span></div>
            <div class="clear"></div>
            <div id="<?php echo $id; ?>-map-canvas-full-event" class="map-canvas-full-event"></div>
            <div class="eo_boxs2_organizer oflow"><i class="fa fa-headphones eos_color"></i><?php echo $lan_arr['evcal_evcard_org']; ?> <span id="<?php echo $id; ?>_eo_organizer">--</span></div>
            <div id="<?php echo $id; ?>_eo_customfields_box"></div>
        </div>
        <div class="dropdown_arrow"></div>
    </div>
</div>
