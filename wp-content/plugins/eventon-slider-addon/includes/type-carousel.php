<?php 
echo "<style> #" . $id . " .flexslider_event .slides > li { margin-right: " . $car_itemmargin . "px; } </style>";
global $post; 
    if (!have_posts()) return;
?>
<div class="flexslider_event eosa_carousel <?php echo $skin ?>">
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
            $optionArr = array("slider_type" => $slider_type,"c_open_type" => $c_open_type,"open_type" => $open_type, "cover" => $cover, "ef" => $ef);
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
            <div class="eo_s2_box <?php if ($item_array[18] == "yes") echo "cancelled_event"; ?>">
                <div id="<?php echo $id . '_' . $eo_index. "_eo_image" ?>" class="event_img_s2" style="background-image:url(<?php echo $item_array[4] ?>);">
                    <div class="box_overlaybox">
                        <div class="eo_s2_prebutton_open">
                            <div class="eo_s2_button_open" onclick="showEventOESAinit('<?php echo $eo_index ?>','<?php echo $id; ?>',<?php echo $id. "_eo_js_array"; ?>,'<?php echo $open_type ?>')"><?php echo $lan_arr_eosa["open_event"]; ?></div>
                        </div>
                    </div>
                    <div class="eo_s2_event_title_box">
                        <div class="eo_s2_main_date" style="border-left-color: #<?php echo $item_array[10]; ?>">
                            <div id="<?php echo $id . '_' .  $eo_index. "_eo_date" ?>">
                                <?php echo $item_array[0]; ?>
                            </div>
                            <span id="<?php echo $id . '_' . $eo_index. "_eo_date_m" ?>" class="only_date">
                                <?php echo $item_array[1]; ?>
                            </span>
                        </div>
                        <div class="eo_s2_event_title">
                            <div id="<?php echo $id . '_' .   $eo_index. "_eo_title" ?>">
                                <span><?php echo $item_array[2]; ?></span>
                            </div>
                            <p class="eo_s2_p" id="<?php echo $id . '_' . $eo_index. "_eo_subtitle" ?>" ><?php echo $item_array[3]; ?></p>
                        </div>
                        <div class="event_clear"></div>
                    </div>
                </div>
                <div class="eo_s2_details_box">
                    <?php if ($item_array[18] == "yes") echo '<div class="cancelled_label">' . $lan_arr['evcal_evcard_evcancel'] . '</div>'; ?>
                    <p class="eo_s2_px" id="<?php echo $id . '_' . $eo_index. "_eo_desc" ?>"><?php echo $item_array[7]; ?></p>
                </div>
                <div class="eo_s2_downbox">
                    <div class="eo_s2_row" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxlocation",$eo_index,getEventAddress($item_array[5],'address')); echo getEventAddress($item_array[5],'coords'); ?> onclick="showMapEOSA('<?php echo $item_array[5] . "','" . $id; ?>')">
                        <span class="eo_icon_box_2"><i class="fa fa-map-marker"></i></span>
                        <span class="so_title overflow_box" id="<?php echo $id . '_' . $eo_index. "_eo_location" ?>"><?php echo getEventAddress($item_array[5],'address'); ?></span>
                    </div>
                    <div class="eo_s2_row" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxtime",$eo_index,$item_array[8]) ?>>
                        <span class="eo_icon_box_2"><i class="fa fa-clock-o"></i></span>
                        <span class="so_title overflow_box" id="<?php echo $id . '_' . $eo_index. "_eo_time_long" ?>">
                            <?php echo $item_array[8]; ?>
                        </span>
                    </div>
                    <?php if (($ef=="all")||($ef=="out")) echo getExtraFieldsHTML($slider_type, $style, "",$item_array[14]); ?>
                    <div class="event_clear"></div>
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
                controlsContainer: "#<?php echo $id; ?> #nav-box-s2",  
                animation: "slide", 
                slideshow: <?php if ($eo_index < 3) echo "false"; else echo "true"; ?>,
                itemWidth: <?php echo $car_itemwidth; ?>,
                itemMargin: <?php echo $car_itemmargin; ?>, 
                minItems: <?php if($car_minitems == 'auto') { echo "getGridSize('" . $id . "','" . $car_itemwidth . "')"; } else echo $car_minitems; ?>,
                maxItems: <?php if($car_maxitems == 'auto') { echo "getGridSize('" . $id . "','" . $car_itemwidth . "')"; } else echo $car_maxitems; ?>,              
                move: <?php echo $car_move; ?>,
                start: function (slider) {
                    showSlider('<?php echo $id; ?>');
                    hideNavigationArrow('#<?php echo $id; ?>');
                },
                before: function (slider) {
                    jQuery('.box_overlaybox').css('display', 'none');
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
<div class="c1_box_nav <?php echo $skin ?>">
    <?php
        if($link == "events_list") {
            echo '<div onclick="showEventList(\'' . $id . '\',' . $id . '_eo_js_array)" class="button_showall button_generic_eo brad20">' .$lan_arr['evcal_lang_sme']. '</div>';
        } elseif(($link != "none") && (strlen($link) > 0)) {
            echo '<a href="'.$link.'" class="button_showall button_generic_eo brad20">' .$lan_arr['evcal_lang_sme']. '</a>';
        };
        ?>
    <div id="nav-box-s2" class="c1_box_dots"></div>
</div>
<div class="clear"></div>
<div id="<?php echo $id; ?>_box_dropdown" class="eosa_box_dropdown"></div>
<div class="<?php echo $skin ?>">
    <div class="eosa_fulllist_box <?php echo $skin ?>" id="<?php echo $id; ?>_eosa_fulllist_box"></div>
</div>