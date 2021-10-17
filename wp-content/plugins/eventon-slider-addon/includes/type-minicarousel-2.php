<?php 
echo "<style> #" . $id . " .flexslider_event .slides > li { margin-right: " . $car_itemmargin . "px; } </style>";
global $post; 
    if (!have_posts()) return;
?>
<div class="flexslider_event eosa_minicarousel micro_padd <?php echo $skin ?>">
    <div id="<?php echo $id; ?>_box_card" class="eosa_box_card"></div>
    <div id="box_arrow_s4" class="box_arrow">
        <span id="eo_s1_arrowLeft" class="eo_s4_arrow eo_sx"><i class="fa fa-angle-left"></i></span>
        <span id="eo_s1_arrowRight" class="eo_s4_arrow eo_dx"><i class="fa fa-angle-right"></i></span>
    </div>
    <ul class="slides">
        <?php 
          
          while(have_posts()):
              the_post();  
              $optionArr = array("slider_type" => $slider_type,"c_open_type" => $c_open_type,"open_type" => $open_type, "cover" => $cover);
              $item_array = readEventData($date_out, $date_in, $lan, $optionArr);

              // MIKE
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
            <?php $fullid = $id . '_' . $eo_index. "_eo_s3_item" ?>
            <div id="<?php echo $fullid ?>" style="border-right: 2px solid #<?php echo $item_array[10]; ?>" class="eo_s4_main <?php if ($item_array[18] == "yes") echo "cancelled_event"; ?>">
                <div class="eo_s4_box" onclick="showEventOESAinit('<?php echo $eo_index ?>','<?php echo $id; ?>',<?php echo $id. "_eo_js_array"; ?>,'<?php echo $open_type ?>','<?php echo $c_dir ?>','<?php echo $fullid ?>')">
                    <div class="eo_s4_month"><?php echo mb_substr($item_array[1],0,3) ?></div>
                    <div class="eo_s4_day"><?php echo $item_array[0]; ?></div>
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
                controlNav: false,            
                directionNav: false,
                animation: "slide",
                itemWidth: <?php echo $car_itemwidth; ?>, 
                itemMargin: <?php echo $car_itemmargin; ?>, 
                move: <?php echo $car_move; ?>,
                minItems: getGridSize(<?php echo "'" . $id . "','" . $car_itemwidth . "'"; ?>),
                maxItems: getGridSize(<?php echo "'" . $id . "','" . $car_itemwidth . "'"; ?>), 
                start: function (slider) {
                    showSlider('<?php echo $id; ?>');
                    hideNavigationArrow('#<?php echo $id; ?>');
                },
                before: function (slider) {
                    jQuery('.box_overlaybox_s3').css('display', 'none');
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
<div id="<?php echo $id; ?>_box_dropdown" class="eosa_box_dropdown"></div>