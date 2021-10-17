<div id="<?php echo $id; ?>-eoas-full-event-box" class="eoas_evo_pop_body" style="display:none">
    <div class="<?php echo $skin ?> full_content_box">
        <a class="eoas_evopopclose" onclick="jQuery('#<?php echo $id; ?>-eoas-full-event-box').trigger('close');">X</a>
        <div class="eo_s1_event_title_box">
            <div id="<?php echo $id; ?>_eo_main_date" class="eo_s1_main_date big">
                <div id="<?php echo $id; ?>_eo_date">--</div>
                <span id="<?php echo $id; ?>_eo_date_m" class="only_date">--</span>
            </div>
            <div class="eo_s1_event_title big">
                <div id="<?php echo $id; ?>_eo_title">--</div>
                <p id="<?php echo $id; ?>_eo_subtitle" class="eo_s1_p">--</p>
            </div>
            <div class="event_clear"></div>
        </div>
        <div id="<?php echo $id; ?>_eo_image" class="eoas_evcal_evdata_img" onclick="animaImage(this)"></div>
        <div class="eoas_evcal_evdata_row">
            <div class="cancelled_label"><?php echo $lan_arr['evcal_evcard_evcancel'] ?></div>
            <div class="eoas_eventon_full_description">
                <div class="eoas_evo_h3"><i class="fa fa-align-justify eo_i"></i><?php echo $lan_arr['evcal_evcard_details'] ?></div>
                <div class="eoas_eventon_desc_in">
                    <p id="<?php echo $id; ?>_eo_desc">--</p>
                </div>
                <div class="eoas_eventon_descButton" id="<?php echo $id; ?>_descButton"><span onclick="scrollContentT('<?php echo $id; ?>','<?php echo $id; ?>_descButton');"><?php echo $lan_arr['evcal_lang_more'] ?></span></div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="eoas_evcal_evdata_row eoas_tablerow tblgb" style="padding: 0px;">
            <div class="eoas_cell50 eoas_cell50SS big">
                <div class="eoas_evo_h3"><i class="fa fa-map-marker eo_i"></i><?php echo $lan_arr['evcal_lang_location'] ?></div>
                <p id="<?php echo $id; ?>_eo_location" class="eoas_evo_h3p">--</p>
            </div>
            <div class="eoas_cell50 big">
                <div class="eoas_evo_h3"><i class="fa fa-clock-o eo_i"></i><?php echo $lan_arr['evcal_lang_time'] ?></div>
                <p id="<?php echo $id; ?>_eo_time_long" class="eoas_evo_h3p">--</p>
            </div>
            <div class="clear"></div>
        </div>
        <div id="<?php echo $id; ?>-map-canvas-full-event" class="map-canvas-full-event"></div>
        <div id="<?php echo $id; ?>_eo_customfields_box"></div>
        <div id="<?php echo $id; ?>_read_more_box"></div>
        <div id="<?php echo $id; ?>_eo_organizer_box" class="eoas_evcal_evdata_row">
            <div class="eoas_evo_h3"><i class="fa fa-headphones eo_i"></i><?php echo $lan_arr['evcal_evcard_org'] ?></div>
            <p id="<?php echo $id; ?>_eo_organizer" class="eoas_evo_h3p">--</p>
        </div>
        <div class="dropdown_arrow"></div>
    </div>
</div>
