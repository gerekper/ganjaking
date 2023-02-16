<div id="<?php echo $id; ?>-mini-event-box" class="eo_minievent_box">
    <div class="<?php echo $skin ?>">
        <div class="eoas_evo_minipop_arrow"><i class="fa fa-caret-up"></i></div>
        <div class="eoas_evo_minipop_body">
            <div class="eoas_minipop_img" id="<?php echo $id; ?>_mebox_img">
                <div class="cancelled_label"><?php echo $lan_arr['evcal_evcard_evcancel'] ?></div>
                <div class="mobile_close_card" onclick="jQuery('.eosa_box_card').css('display', 'none');"><i class="fa fa-times"></i></div>
            </div>
            <div class="eo_s4_event_title_boxContainer">
                <div class="eo_s4_event_title_box">
                    <div class="eo_s4_main_date"  id="<?php echo $id; ?>_mebox_border">
                        <div id="<?php echo $id; ?>_mebox_day"></div>
                        <span id="<?php echo $id; ?>_mebox_month" class="only_date"></span>
                    </div>
                    <div class="eo_s4_event_title">
                        <div id="<?php echo $id; ?>_mebox_title"><span></span></div>
                        <p class="eo_s4_p"  id="<?php echo $id; ?>_mebox_subtitle"></p>
                    </div>
                    <div class="event_clear"></div>
                </div>
            </div>
            <div class="eo_s4_details_box">
                <p id="<?php echo $id; ?>_mebox_desc"></p>
            </div>
            <div class="eo_s4_downbox">
                <div class="eo_s4_row">
                    <span class="eo_icon_box_2"><i class="fa fa-map-marker"></i></span>
                    <span class="so_title"  id="<?php echo $id; ?>_mebox_location"></span>
                </div>
                <div class="eo_s4_row" style="border-top: 1px solid rgb(112, 111, 111);">
                    <span class="eo_icon_box_2"><i class="fa fa-clock-o"></i></span>
                    <span class="so_title"  id="<?php echo $id; ?>_mebox_date"></span>
                </div>
                <div class="event_clear"></div>
            </div>
            <div class="eo_s4_buttonbox">
                <span onclick="showEventCard('<?php echo $id ?>','<?php echo $c_open_type ?>')"><?php echo $lan_arr_eosa["open_event"]; ?></span><span onclick="showMapCard('<?php echo $id ?>')"><?php echo $lan_arr_eosa["show_map"]; ?></span>
            </div>
        </div>
        <div class="eoas_evo_minipop_arrow_down"><i class="fa fa-caret-down"></i></div>
    </div>
</div>
