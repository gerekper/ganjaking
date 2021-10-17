<?php global $post;
      global $plugin_url;
?>
<div class="eosa_masonry <?php echo $skin ?>">
    <?php if ($filters != "no") { ?>
    <div class="filters-box">
        <div class="eventon_sorting_section">
            <?php if (($filters == "all")||($filters == "order")) {  ?>
            <div class="eventon_sort_line evo_sortOpt">
                <div class="evo_sortby">
                    <p>
                        <?php echo $lan_arr['evcal_lang_sort'] ?>
                    </p>
                </div>
                <div class="evo_srt_sel" eid="<?php echo $id; ?>" sort-value="number">
                    <p class="eo_label">
                        <?php echo $lan_arr['evcal_lang_sdate'] ?>
                        <i class="fa fa-caret-down"></i>
                    </p>
                    <div class="evo_srt_options">
                        <p sort-value="number" eid="<?php echo $id; ?>" class="evs_btn evs_hide">
                            <?php echo $lan_arr['evcal_lang_sdate'] ?>
                        </p>
                        <p sort-value="title" eid="<?php echo $id; ?>" class="evs_btn ">
                            <?php echo $lan_arr['evcal_lang_stitle'] ?>
                        </p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <?php }
                  if (($filters == "all")||($filters == "cat")) {
            ?>
            <div class="eventon_filter_line">
                <div class="eventon_filter_selection">
                    <p class="eo_label cat_all_button" eid="<?php echo $id; ?>">
                        <?php echo $lan_arr['evcal_lang_all'] ?>
                    </p>
                </div>
                <?php
                      $eo_cat = getCateogries($lan);
                      for ($i=0; $i < count($eo_cat); $i++) {
                          $et_ = $eo_cat[$i][0][1];   ?>
                <div class="eventon_filter_selection" id="box_event_type<?php echo $et_; ?>">
                    <p class="eo_label" la="<?php echo $et_; ?>" eid="<?php echo $id; ?>" id="event_type<?php echo $et_; ?>">
                        <?php echo $eo_cat[$i][0][0]; ?>
                    </p>
                    <div class="eventon_filter_dropdown" id="eo_dropdown<?php echo $et_; ?>" style="display: none">
                        <div class="catn">
                            <?php echo $eo_cat[$i][0][0]; ?>
                        </div>
                        <p cat-slug="" la="<?php echo $et_; ?>" eid="<?php echo $id; ?>">
                            <?php echo $lan_arr['evcal_lang_all'] ?>
                        </p>
                        <?php  for ($j=0; $j < count($eo_cat[$i][1]); $j++) {
                                   $tmp = $eo_cat[$i][1][$j];
                        ?>
                        <p cat-slug="<?php echo $tmp["slug"]; ?>" la="<?php echo $et_; ?>" eid="<?php echo $id; ?>">
                            <?php echo $tmp["name"]; ?>
                        </p>
                        <?php  }  ?>
                    </div>
                </div>
                <?php  }
                      if ($calendar == "yes") {
                ?>
                <div class="eventon_filter_selection eosa-cal fa">
                    <input name="start_date" id="start_date" eid="<?php echo $id; ?>" type="text" data-toggle="datepicker" placeholder="From" />
                </div>
                <div class="eventon_filter_selection eosa-cal fa">
                    <input name="end_date" id="end_date" eid="<?php echo $id; ?>" type="text" data-toggle="datepicker" placeholder="To" />
                </div>
                <?php } ?>
            </div>
            <?php }
                  if (($filters == "all")||($filters == "order")) {
            ?>
            <div class="eosa_sortorder" eid="<?php echo $id; ?>">
                <i class="fa <?php if ($orderby == "asc") echo "fa-arrow-down"; else echo "fa-arrow-up"; ?> "></i>
            </div>
            <?php }  ?>
            <img class="filter_loader" src="<?php echo $plugin_url . "/eventon-slider-addon/assets/images/loader.gif"; ?>" />
            <div class="clear"></div>
        </div>
    </div>
    <?php } ?>
    <div class="masonrybox"></div>
    <?php
    if ($maso_paged == "yes") echo '<div class="eo_cont"><div class="eo_loadmore" eid="' .$id. '">' . $lan_arr['evcal_lang_sme'] . '</div></div>';
    ?>
    <div class="clear"></div>
    <script type='text/javascript'>
        <?php

        echo "var " .$id. "_eo_js_array = ". json_encode_arr($global_array) . ";\n";
        ?>
        var masonry_<?php echo $id; ?>;
        var paged = 1;
        jQuery(document).ready(function () {
            masonry_<?php echo $id; ?> = jQuery('#<?php echo $id; ?> .masonrybox').isotope({
                itemSelector: '.maso_<?php echo $id; ?>',
                percentPosition: true,
                getSortData: {
                    number: function (e) {
                        return parseInt(jQuery(e).attr('date-code'));
                    },
                    title: function (e) {
                         <?php
                         if ($style == 'a') echo "return jQuery(e).find('.eo_s2_event_title .eo_title').html()";
                         if ($style == 'b') echo "return jQuery(e).find('.eo_card_title span').html()";
                         ?>
                    }
                },
                sortBy: 'number',
                <?php if ($orderby == "asc") echo "sortAscending: true"; else echo "sortAscending: false"; ?>
            });
            setShortcodeCat('<?php echo $id; ?>');
            ajax_getEvents("masonry", '<?php echo $id; ?>', eo_lan_arr, eo_lan_arr_eosa, 1, '<?php echo $id; ?>');
        });
    </script>
</div>
<div class="clear"></div>
<div id="<?php echo $id; ?>_box_dropdown" class="eosa_box_dropdown"></div>
<div class="<?php echo $skin ?>">
    <div class="eosa_fulllist_box <?php echo $skin ?>" id="<?php echo $id; ?>_eosa_fulllist_box"></div>
</div>
