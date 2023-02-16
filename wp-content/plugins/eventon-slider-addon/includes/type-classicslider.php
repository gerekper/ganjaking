<?php 
global $post; 
    if (!have_posts()) return;
?>
<div class="<?php echo $skin . " " . $class_?>">
    <div class="flexslider_event eosa_slider">
        <span id="eo_s1_arrowLeft" class="eo_s1_arrow eo_s1_arrow_sx"><i class="fa fa-angle-left"></i></span>
        <span id="eo_s1_arrowRight" class="eo_s1_arrow eo_s1_arrow_dx"><i class="fa fa-angle-right"></i></span>
        <div class="eosa_popup" id="<?php echo $id . "_eosa_popup"; ?>">
            <p id="<?php echo $id . "_eosa_popup_p"; ?>">...</p>
            <i class="fa fa-caret-down"></i>
        </div>

        <ul class="slides">
            <?php 
            
            while(have_posts()):
                the_post();  
                $optionArr = array("slider_type" => $slider_type,"c_open_type" => $c_open_type,"open_type" => $open_type, "cover" => $cover);
                $item_array = readEventData($date_out, $date_in, $lan, $optionArr);

                $in = inDateRange($item_array[15],$meta_query_arr);
                if ($in) array_push($global_array, $item_array);
                $arrRepeats = createEventRepeats($slider_type,$date_out,$lan,$meta_query_arr,$item_array,$optionArr);
                
                $global_array = array_merge($global_array,$arrRepeats);
                
            endwhile;  wp_reset_query(); 
                
            // MIKE: SORT ARRAY based on start_date
            if ($orderby == 'asc')
                usort($global_array, 'sort_by_date_in_asc');
            else
                usort($global_array, 'sort_by_date_in_desc');
                
            if ($showevent> 0) array_splice($global_array, $showevent);
            foreach($global_array as $item_array) {
            ?>

            <li>
                <div class="eo_s1_box <?php if ($item_array[18] == "yes") echo "cancelled_event"; ?>">
                    <div class="eo_s1_event_title_box">
                        <div class="eo_s1_main_date " style="border-left-color: #<?php echo $item_array[10]; ?>">
                            <div id="<?php echo $id . '_' .   $eo_index. "_eo_date" ?>">
                                <?php echo $item_array[0]; ?>
                            </div>
                            <span id="<?php echo $id . '_' .   $eo_index. "_eo_date_m" ?>" class="only_date">
                                <?php echo $item_array[1]; ?>
                            </span>
                        </div>
                        <div class="eo_s1_event_title">
                            <div class="eo_s1_cont">
                                <div id="<?php echo $id . '_' .   $eo_index. "_eo_title" ?>">
                                    <?php echo $item_array[2]; ?>
                                </div>
                                <p class="eo_s1_p" id="<?php echo $id . '_' . $eo_index. "_eo_subtitle" ?>" ><?php echo $item_array[3]; ?></p>
                            </div>
                        </div>
                        <div class="event_clear"></div>
                    </div>
                    <div id="<?php echo $id . '_' .   $eo_index. "_eo_image" ?>" class="event_img" style="background-image:url(<?php echo $item_array[4]; ?>);">
                        <?php if($details == "yes") { ?>
                        <div class="eo_s1_details_box">
                            <?php if ($item_array[18] == "yes") echo '<div class="cancelled_label">' . $lan_arr['evcal_evcard_evcancel'] . '</div>'; ?>
                            <div class="eo_left_x1">
                                <div class="eo_s1_tbox">
                                    <p class="eo_s1_t"><i class="fa fa-align-justify eo_i" style="color: #<?php echo $item_array[10]; ?>"></i><?php echo $lan_arr['evcal_evcard_details'] ?></p>
                                    <?php if(strlen($item_array[6]) > 0) { ?>
                                    <p class="eo_s1_t2">
                                        <i class="fa fa-headphones eo_i" style="color: #<?php echo $item_array[10]; ?>"></i><?php echo $lan_arr['evcal_evcard_org'] ?><span class="eo_s1_t2b" id="<?php echo $id . '_' .   $eo_index. "_eo_organizer" ?>">
                                            <?php echo $item_array[6]; ?></span>
                                    </p>
                                    <?php } ?>
                                    <div class="clear"></div>
                                </div>
                                <p class="eo_s1_p" id="<?php echo $id . '_' .   $eo_index. "_eo_desc" ?>"><?php echo $item_array[7]; ?></p>
                            </div>
                            <div class="eo_right_x1">
                                <?php if($map == "yes") { ?><div class="button_generic_eo_2 button_1_eo" onclick="showMapEOSA('<?php echo $item_array[5]; ?>','<?php echo $id; ?>')"><?php echo $lan_arr_eosa["show_map"]; ?></div><?php } ?>
                                <div class="button_generic_eo_2 button_1_eo" onclick="showEventOESAinit('<?php echo $eo_index ?>','<?php echo $id; ?>',<?php echo $id. "_eo_js_array"; ?>,'<?php echo $open_type ?>')"><?php echo $lan_arr_eosa["open_event"]; ?></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="eo_right_x1 btyy">
                            <?php if($map == "yes") { ?><div class="button_generic_eo_2 button_1_eo btxx" onclick="showMapEOSA('<?php echo $item_array[5]; ?>','<?php echo $id; ?>')"><?php echo $lan_arr_eosa["show_map"]; ?></div><?php } ?>
                            <div class="button_generic_eo_2 button_1_eo btxx" onclick="showEventOESAinit('<?php echo $eo_index ?>','<?php echo $id; ?>',<?php echo $id. "_eo_js_array"; ?>,'<?php echo $open_type ?>')"><?php echo $lan_arr_eosa["open_event"]; ?></div>
                            <div class="clear"></div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="eo_s1_downbox">
                        <div class="s1_in s1_left" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxlocation",$eo_index,getEventAddress($item_array[5],'address')); echo getEventAddress($item_array[5],'coords'); ?>>
                            <div class="eosa_popup"></div>
                            <i class="fa fa-map-marker eo_i" style="color: #<?php echo $item_array[10]; ?>"></i>
                            <div><?php echo $lan_arr['evcal_lang_location'] ?></div>
                            <span class="so_title overflow_box" id="<?php echo $id . '_' .   $eo_index. "_eo_location" ?>"><?php echo getEventAddress($item_array[5],'address'); ?></span>
                        </div>
                        <div class="s1_in s1_right" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxtime",$eo_index,$item_array[8]) ?>>
                            <i class="fa fa-clock-o eo_i" style="color: #<?php echo $item_array[10]; ?>"></i>
                            <div><?php echo $lan_arr['evcal_lang_time'] ?></div>
                            <span class="so_title overflow_box" id="<?php echo $id . '_' .   $eo_index. "_eo_time_long" ?>">
                                <?php echo $item_array[8]; ?>
                            </span>
                        </div>
                        <div class="event_clear"></div>
                    </div>
                    <div class="eo_s1_downbox">
                        <?php if (($ef=="all")||($ef=="out")) echo getExtraFieldsHTML($slider_type, $style, $item_array[10],$item_array[14]); ?>
                    </div>
                </div>
            </li>
            <?php 
                $eo_index = $eo_index + 1;
            }
            ?>
        </ul>
        <div class="clear"></div>
        <script type='text/javascript'>
                <?php

                echo "var " .$id. "_eo_js_array = ". json_encode_arr($global_array) . ";\n"; 
                ?>
            jQuery(document).ready(function () {
                jQuery("#<?php echo $id; ?> .flexslider_event").flexslider({
                    controlNav: true,
                    directionNav: false,
                    controlsContainer: "#<?php echo $id; ?> #nav-box-s1",
                    animation: "<?php echo $animation; ?>",
                    start: function (slider) {
                        showSlider('<?php echo $id; ?>');
                    }
                });
            });
                jQuery("#<?php echo $id; ?> #eo_s1_arrowRight").click(function () {
                jQuery("#<?php echo $id; ?> .flexslider_event").flexslider("next");
                });
                jQuery("#<?php echo $id; ?> #eo_s1_arrowLeft").click(function () {
                jQuery("#<?php echo $id; ?> .flexslider_event").flexslider("prev");
                });
        </script>
    </div>
    <div class="clear"></div>
    <div class="s1_box_nav <?php echo $class_ ?>">
        <?php
    if($link == "events_list") {
        echo '<div onclick="showEventList(\'' . $id . '\',' . $id . '_eo_js_array)" class="button_showall button_generic_eo brad20 ' . $class_ .'">' .$lan_arr['evcal_lang_sme']. '</div>';
    } elseif(($link != "none") && (strlen($link) > 0)) {
        echo '<a href="'.$link.'" class="button_showall button_generic_eo brad20 ' . $class_ . '">' .$lan_arr['evcal_lang_sme']. '</a>';
    };
    ?>
    </div>
    <div id="nav-box-s1" class="s1_box_dots <?php echo $class_ ?>"></div>
</div>
<div id="<?php echo $id; ?>_box_dropdown" class="eosa_box_dropdown"></div>
<div class="<?php echo $skin ?>">
    <div class="eosa_fulllist_box <?php echo $skin ?>" id="<?php echo $id; ?>_eosa_fulllist_box"></div>
</div>
