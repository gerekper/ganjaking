<?php 
 echo "<style> #" . $id . " .flexslider_event .slides > li { margin-right: " . $car_itemmargin . "px; } </style>";
 global $post; 
    if (!have_posts()) return;
?>
<div class="flexslider_event eosa_carousel <?php echo $skin ?>">
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
              
            // MIKE
            $in = inDateRange($item_array[15],$meta_query_arr);
            if ($in)  array_push($global_array, $item_array);
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
            <?php  $openLink = "showEventOESAinit('" . $eo_index . "','" .  $id . "', " .$id. "_eo_js_array,'" . $open_type . "')"; ?>
            <div class="eo_card_box <?php if ($item_array[18] == "yes") echo "cancelled_event"; ?>">
                <?php if ($item_array[18] == "yes") echo '<div class="cancelled_label">' . $lan_arr['evcal_evcard_evcancel'] . '</div>'; ?>
                <div id="<?php echo $id . '_' . $eo_index. "_eo_image" ?>" onclick="<?php echo $openLink ?>" class="eo_card_img" style="background-image:url(<?php echo $item_array[4]; ?>);" infobox="<?php echo $id . '_' . $eo_index ?>">
                    <span class="eosa_info_box" id="<?php echo $id . '_' . $eo_index . '_info_box' ?>" style="opacity:0;"><?php echo $lan_arr_eosa["open_event"]; ?></span>
                </div>
                <div class="eo_card_title_boxContainer" onclick="<?php echo $openLink ?>">
                    <div class="eo_card_title_box" infobox="<?php echo $id . '_' . $eo_index ?>">
                        <div class="eo_card_main_date"  style="border-left-color: #<?php echo $item_array[10]; ?>">
                            <div id="<?php echo $id; ?>_mebox_day"><?php echo $item_array[0]; ?></div>
                            <span id="<?php echo $id; ?>_mebox_month" class="only_date"><?php echo mb_substr($item_array[1],0,3); ?></span>
                        </div>
                        <div class="eo_card_title">
                            <div id="<?php echo $id; ?>_mebox_title"><span><?php echo $item_array[2]; ?></span></div>
                            <p class="eo_card_p"  id="<?php echo $id; ?>_mebox_subtitle"><?php echo $item_array[3]; ?></p>
                        </div>
                        <div class="event_clear"></div>
                    </div>
                </div>
                <div class="eo_card_details_box">
                    <p id="<?php echo $id; ?>_mebox_desc"><?php echo $item_array[7]; ?></p>
                </div>
                <div class="eo_card_downbox">
                    <div class="eo_card_row eo_over" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxlocation",$eo_index,getEventAddress($item_array[5],'address')); echo getEventAddress($item_array[5],'coords'); ?>  onclick="showMapEOSA('<?php echo $item_array[5] . "','" . $id; ?>')">
                        <i class="fa fa-map-marker" style="color: #<?php echo $item_array[10] ?>"></i>
                        <span class="eo_card_sotitle overflow_box" id="<?php echo $id . '_' . $eo_index. "_eo_location" ?>"><?php echo getEventAddress($item_array[5],'address'); ?></span>
                    </div>
                    <div class="eo_card_row" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxtime",$eo_index,$item_array[8]) ?>>
                        <i class="fa fa-clock-o" style="color: #<?php echo $item_array[10] ?>"></i>
                        <span class="eo_card_sotitle overflow_box" id="<?php echo $id . '_' . $eo_index. "_eo_time_long" ?>"><?php echo $item_array[8]; ?></span>
                    </div>
                    <?php if (($ef=="all")||($ef=="out")) echo getExtraFieldsHTML($slider_type, $style, $item_array[10],$item_array[14]); ?>
                </div>
                <div class="event_clear"></div>
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
                controlsContainer: "#<?php echo $id; ?> #nav-box-s1c",  
                animation: "slide",  
                itemWidth: <?php echo $car_itemwidth; ?>,
                itemMargin: <?php echo $car_itemmargin; ?>, 
                minItems: <?php if($car_minitems == 'auto') { echo "getGridSize('" . $id . "','" . $car_itemwidth . "')"; } else echo $car_minitems; ?>,
                maxItems: <?php if($car_maxitems == 'auto') { echo "getGridSize('" . $id . "','" . $car_itemwidth . "')"; } else echo $car_maxitems; ?>,               
                move: <?php echo $car_move; ?>,
                start: function (slider) {
                    hideNavigationArrow('#<?php echo $id; ?>');
                    showSlider('<?php echo $id; ?>');
                }
            });
        });
    </script>
</div>
<div class="clear"></div>
<div class="c1b_box_nav <?php echo $skin ?>">
    <?php
    if($link == "events_list") {
        echo '<div onclick="showEventList(\'' . $id . '\',' . $id . '_eo_js_array)" class="button_showall button_generic_eo brad3">' .$lan_arr['evcal_lang_sme']. '</div>';
    } elseif(($link != "none") && (strlen($link) > 0)) {
        echo '<a href="'.$link.'" class="button_showall button_generic_eo brad3">' .$lan_arr['evcal_lang_sme']. '</a>';
    };
    ?>
    <div class="c1b_box_arr">
        <span id="<?php echo $id; ?>_arrowLeft" class="s1b_arrow" onclick="jQuery('#<?php echo $id; ?> .flexslider_event').flexslider('prev');"><i class="fa fa-angle-left"></i></span>
        <span id="<?php echo $id; ?>_arrowRight" class="s1b_arrow" onclick="jQuery('#<?php echo $id; ?> .flexslider_event').flexslider('next');"><i class="fa fa-angle-right"></i></span>
    </div>
    <div id="nav-box-s1c" class="c1b_box_dots"></div>
</div>
<div class="clear"></div>
<div id="<?php echo $id; ?>_box_dropdown" class="eosa_box_dropdown"></div>
<div class="<?php echo $skin ?>">
    <div class="eosa_fulllist_box <?php echo $skin ?>" id="<?php echo $id; ?>_eosa_fulllist_box"></div>
</div>