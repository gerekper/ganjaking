<?php global $post; 
    if (!have_posts()) return;
?>
<div class="<?php echo $skin ." ". $class_ ?>">
    <div class="flexslider_event eosa_slider">
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
                <div class="eo_s1b_box <?php if ($item_array[18] == "yes") echo "cancelled_event"; ?>">
                    <div class="s1b_img <?php if ($details == "no") { echo "s1300"; } else { if (strlen($item_array[3]) == 0) echo "s1345"; else echo "s1300"; } ?>" style="background-image:url(<?php echo $item_array[4]; ?>);">
                        <div class="s1b_header">
                            <div class="s1b_col">
                                <p class="s1b_d"><?php echo $item_array[0] . " " . $item_array[1]; ?></p>
                                <p class="s1b_d2"><?php echo $item_array[12]; ?></p>
                            </div>
                            <div class="s1b_col2">
                                <div><?php echo $item_array[2]; ?></div>
                            </div>
                        </div>
                        <?php if ($item_array[18] == "yes") echo '<div class="cancelled_label">' . $lan_arr['evcal_evcard_evcancel'] . '</div>'; ?>
                        <?php if(strlen($item_array[6]) > 0) { ?><div class="s1b_organizer oflow"><i class="fa fa-headphones" style="color: #<?php echo $item_array[10]; ?>"></i><?php echo $lan_arr['evcal_evcard_org'] . " " . $item_array[6]; ?></div><?php } ?>
                    </div>
                    <?php if (($details == "yes") && (strlen($item_array[3]) > 0)) { ?><div class="s1b_row oflow"><?php echo $item_array[3]; ?></div><?php } ?>
                    <div class="s1b_box_a">
                        <div class="s1b_datetime_box <?php if ($details == "no") echo "nocontent" ?>">
                            <div class="s1b_colb oflow" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxlocation",$eo_index,getEventAddress($item_array[5],'address')) ?>>
                                <div class="overflow_box" style="padding-right: 5px;"><i class="fa fa-map-marker" style="color: #<?php echo $item_array[10]; ?>"></i><?php echo getEventAddress($item_array[5],'address'); ?></div>
                            </div>
                            <div class="s1b_colb oflow" <?php echo printPopCode($id, $id . '_' . $eo_index. "_eo_boxtime",$eo_index,$item_array[8]) ?>>
                                <div class="overflow_box"><i class="fa fa-clock-o" style="color: #<?php echo $item_array[10]; ?>"></i><?php echo $item_array[8]; ?></div>
                            </div>
                            <?php if ($ef=="yes") echo getExtraFieldsHTML($slider_type, $style, $item_array[10],$item_array[14]); ?>
                        </div>
                        <?php if (($ef=="all")||($ef=="out")) echo getExtraFieldsHTML($slider_type, $style, $item_array[10],$item_array[14]); ?>
                        <?php if($details == "yes") { ?>
                        <p class="s1b_desc"><?php echo $item_array[7]; ?></p><?php } ?>
                        <div class="s1b_button_box">
                            <?php if($map == "yes") { ?><div class="s1b_button" onclick="showMapEOSA('<?php echo $item_array[5]; ?>','<?php echo $id; ?>')" <?php echo getEventAddress($item_array[5],'coords'); ?>><?php echo $lan_arr_eosa["show_map"]; ?></div><?php } ?>
                            <div class="s1b_button" onclick="showEventOESAinit('<?php echo $eo_index ?>','<?php echo $id; ?>',<?php echo $id. "_eo_js_array"; ?>,'<?php echo $open_type ?>','style-b')"><?php echo $lan_arr_eosa["open_event"]; ?></div>
                        </div>
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
                    controlNav: true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
                    directionNav: false,
                    controlsContainer: "#<?php echo $id; ?> #nav-box-s1b",  //Boolean: Create navigation for previous/next navigation? (true/false)
                    animation: "<?php echo $animation; ?>",
                    start: function (slider) {
                        showSlider('<?php echo $id; ?>');
                    }
                });
            });
        </script>
    </div>
    <div class="clear"></div>

    <div class="s1b_box_nav <?php echo $class_ ?>">
        <?php
    if($link == "events_list") {
        echo '<div onclick="showEventList(\'' . $id . '\',' . $id . '_eo_js_array)" class="button_showall button_generic_eo brad3 pdao' . $class_ .'">' .$lan_arr['evcal_lang_sme']. '</div>';
    } elseif(($link != "none") && (strlen($link) > 0)) {
        echo '<a href="'.$link.'" class="button_showall button_generic_eo brad3 ' . $class_ . '">' .$lan_arr['evcal_lang_sme']. '</a>';
    };
    ?>
        <div class="s1b_box_arr">
            <span id="<?php echo $id; ?>_arrowLeft" class="s1b_arrow" onclick="jQuery('#<?php echo $id; ?> .flexslider_event').flexslider('prev');"><i class="fa fa-angle-left"></i></span>
            <span id="<?php echo $id; ?>_arrowRight" class="s1b_arrow" onclick="jQuery('#<?php echo $id; ?> .flexslider_event').flexslider('next');"><i class="fa fa-angle-right"></i></span>
        </div>
        <div id="nav-box-s1b" class="s1b_box_dots <?php echo $class_ ?>"></div>
    </div>
    <div class="eosa_fulllist_box <?php echo $skin ?>" id="<?php echo $id; ?>_eosa_fulllist_box"></div>
</div>
<div id="<?php echo $id; ?>_box_dropdown" class="eosa_box_dropdown"></div>