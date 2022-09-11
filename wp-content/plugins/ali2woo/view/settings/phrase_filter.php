
<div class="panel panel-primary mt20" id="phrase_list">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo esc_html_x('Phrase Filtering (case sensitive)', 'Setting title', 'ali2woo'); ?></h3>
        <span class="pull-right">
            <a class="disabled" style="display: none;"><?php esc_html_e('You have unsaved changes', 'ali2woo');?></a>
            <a href="#" class="apply-phrase-rules btn"><?php esc_html_e('Apply Filter to your Shop', 'ali2woo');?></a></span>
    </div>


    <div class="panel-body">
        <div class="panel panel-default" id="a2w-panel-info" style="display: none;">
            <div class="panel-heading"><?php esc_html_e('Applying filter progress', 'ali2woo');?>  <button type="button" class="close" data-target="#a2w-panel-info" data-dismiss="alert"> <span aria-hidden="true">&times;</span><span class="sr-only"><?php esc_html_e('Close', 'ali2woo');?></span>

            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                    <?php esc_html_e('Reviews', 'ali2woo')?>
                        <div class="progress reviews-progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 4em;">
                            <?php esc_html_e('wait', 'ali2woo')?>...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid_default grid_3">
            <div class="grid__col pb20">
                <strong><?php esc_html_e('Phrase', 'ali2woo');?></strong>
            </div>
            <div class="grid__col pb20">
                <strong><?php esc_html_e('Replacement', 'ali2woo');?></strong>
            </div>
        </div>
        <?php foreach ($phrases as $ind => $phrase): ?>
            <div class="grid grid_default grid_3 row">
                <div class="grid__col">
                    <div class="form-group input-block no-margin">
                        <input type="text" value="<?php echo $phrase->phrase; ?>" class="form-control small-input a2w_phrase" placeholder="<?php esc_html_e('some phrase or word', 'ali2woo');?>" />
                    </div>
                </div>
                <div class="grid__col">
                    <div class="form-group input-block no-margin">
                        <input type="text" value="<?php echo $phrase->phrase_replace; ?>" class="form-control small-input a2w_phrase_replace" placeholder="<?php esc_html_e('sreplacement or empty', 'ali2woo');?>" />
                    </div>
                </div>
                <div class="grid__col">
                    <button class="btn btn--transparent delete">
                        <svg class="icon-cross">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach;?>
        <div class="grid grid_default grid_3 row">
            <div class="grid__col">
                <div class="form-group input-block no-margin">
                    <input type="text" class="form-control small-input a2w_phrase" placeholder="<?php esc_html_e('some phrase or word', 'ali2woo');?>" />
                </div>
            </div>
            <div class="grid__col">
                <div class="form-group input-block no-margin">
                    <input type="text" class="form-control small-input a2w_phrase_replace" placeholder="<?php esc_html_e('sreplacement or empty', 'ali2woo');?>" />
                </div>
            </div>
            <div class="grid__col">
                <button class="btn btn--transparent delete" style="display:none;">
                    <svg class="icon-cross">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                    </svg>
                </button>
            </div>
        </div>

    </div>

</div>
<div class="panel small-padding margin-small-top panel-danger" style="display: none;">
    <div class="panel-body">
        <div class="container-flex flex-between">
            <div class="container-flex">
                <div class="svg-container no-shrink">
                    <svg class="icon-danger-circle margin-small-right">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-danger-circle"></use>
                    </svg>
                </div>
                <div class="ml5 mr10">
                    <div class="content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row pt20 border-top">
        <div class="col-sm-12">
            <input class="btn btn-success" id="save-phrases"  type="submit" value="<?php esc_html_e('Save settings', 'ali2woo');?>"/>
        </div>
    </div>
</div>

<div class="modal-overlay modal-apply-phrases">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php esc_html_e('Apply Filter to your Shop', 'ali2woo');?></h3>
            <a class="modal-btn-close" href="#"></a>
        </div>
        <div class="modal-body">
            <label><?php esc_html_e('Select the update type', 'ali2woo');?></label>
            <div style="padding-bottom: 20px;">
                <div class="type btn-group" role="group">
                    <button type="button" class="btn btn-default" value="products"><?php echo esc_html_x('Products', 'Apply Phrases', 'ali2woo'); ?></button>
                    <button type="button" class="btn btn-default" value="reviews"><?php echo esc_html_x('Reviews', 'Aplly Phrases', 'ali2woo'); ?></button>
                    <?php /*
<button type="button" class="btn btn-default" value="shippings"><?php echo esc_html_x('Shipping methods', 'Apply Phrases', 'ali2woo'); ?></button>
 */?>
                    <button type="button" class="btn btn-default" value="all_types"><?php echo esc_html_x('All', 'Apply Phrases', 'ali2woo'); ?></button>
                </div>
            </div>
            <div class="scope">
                <label><?php esc_html_e('Select the update scope', 'ali2woo');?></label>
                <div>
                    <div class="scope btn-group" role="group">
                        <button type="button" class="btn btn-default" value="shop"><?php echo esc_html_x('Shop', 'Apply Phrases', 'ali2woo'); ?></button>
                        <button type="button" class="btn btn-default" value="import"><?php echo esc_html_x('Import List', 'Apply Phrases', 'ali2woo'); ?></button>
                        <button type="button" class="btn btn-default" value="all"><?php echo esc_html_x('Shop and Import List', 'Apply Phrases', 'ali2woo'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default close-btn" type="button"><?php esc_html_e('Close', 'ali2woo');?></button>
            <button class="btn btn-success apply-btn" type="button"><?php esc_html_e('Apply', 'ali2woo');?></button>
        </div>
    </div>
</div>



<script>
    (function ($) {

        var set_progress_bar_value = function (c, v) {
            jQuery('#a2w-panel-info ' + c + ' .progress-bar')
                    .css('width', v + '%')
                    .attr('aria-valuenow', v)
                    .html(v + '%');
        }

        var get_status_filter = function (show) {

            var data = {action: 'a2w_get_status_apply_phrase_rules'};
            jQuery.post(ajaxurl, data).done(function (response) {
                var json = $.parseJSON(response);
                if (json.state === 'error') {
                    console.log(json);
                } else {


                    if (typeof json.review_valuenow !== 'undefined') {

                        jQuery('#a2w-panel-info').fadeIn(400);
                        set_progress_bar_value('.reviews-progress', json.review_valuenow);

                        setTimeout(get_status_filter, 1000);
                    }
                    else if (typeof show !== 'undefined') {
                        jQuery('#a2w-panel-info').fadeIn(400);
                        setTimeout(function () {
                            get_status_filter(true)
                        }, 1000);
                    }
                    else {
                        //jQuery('.panel-info').fadeOut(400);
                    }

                    if (typeof json.review_valuenow == 'undefined') {
                        set_progress_bar_value('.reviews-progress', 100);
                    }
                }

            }).fail(function (xhr, status, error) {
                show_notification('Get status of filters failed.', true);
            });

        }

        //  get_status_filter();

        $(".apply-phrase-rules").on("click", function () {
            $(".modal-apply-phrases .btn-group").each(function () {
                $(this).find('.btn').removeClass('btn-info').removeClass('active').addClass('btn-default');
                $(this).find('.btn:first').removeClass('btn-default').addClass('btn-info').addClass('active');
                $(this).data({value: $(this).find('.btn:first').val()});
            });

            $(".modal-apply-phrases .scope").show();
            $(".modal-apply-phrases").addClass('opened');
            return false;
        });

        $(".modal-apply-phrases .btn-group .btn").on("click", function () {

            if ($(this).val() == 'reviews' || $(this).val() == 'shippings')
                $(".modal-apply-phrases .scope").hide();

            else if ($(this).val() == 'products' || $(this).val() == 'all_types')
                $(".modal-apply-phrases .scope").show();

            $(this).parents('.btn-group').find('.btn').removeClass('btn-info').removeClass('active').addClass('btn-default');
            $(this).removeClass('btn-default').addClass('btn-info').addClass('active');
            $(this).parents('.btn-group').data({value: $(this).val()});
        });

        $(".modal-apply-phrases .close-btn").on("click", function () {
            $(".modal-apply-phrases").removeClass('opened');
            return false;
        });

        $(".modal-apply-phrases .apply-btn").on("click", function () {
            $(".modal-apply-phrases").removeClass('opened');

            //  get_status_filter(true);

            var data = {action: 'a2w_apply_phrase_rules', type: $(".modal-apply-phrases .btn-group.type").data().value, scope: $(".modal-apply-phrases .btn-group.scope").data().value};
            jQuery.post(ajaxurl, data).done(function (response) {
                show_notification('Applying filter to your Shop');
            }).fail(function (xhr, status, error) {
                show_notification('Applying filter failed.', true);
            });

            return false;
        });

        function check_phrases() {
            var empty_check = true;
            $('#phrase_list > .panel-body .has-error').removeClass('has-error');

            $('#phrase_list > .panel-body .row:gt(0)').each(function () {
                if (!$(this).is(":last-child") && $(this).find(".a2w_phrase").length > 0 && $.trim($(this).find(".a2w_phrase").val()) == '') {
                    $(this).find(".a2w_phrase").addClass('has-error');
                    empty_check = false;
                }
            });



            $('.panel-danger').hide();
            if (!empty_check) {
                $('.panel-danger .content').html("Please fill out Phrase fields");
                $('.panel-danger').show();
            }

            return empty_check;

        }

        function set_last_phrase_row_enability(show) {
            var row = $('#phrase_list > .panel-body .row:last-child');

            if (show) {
                row.find('.a2w_phrase_replace').removeClass('opacity50');
                row.find('.a2w_phrase_replace').prop('disabled', false);

            } else {
                row.find('.a2w_phrase_replace').removeClass('opacity50').addClass('opacity50');

                row.find('.a2w_phrase_replace').prop('disabled', true);

            }


        }

        function add_phrase_row(this_row) {
            var row = $(this_row).parents('.panel-body').children('.row:last-child'),
                    new_row = row.clone();

            new_row.find('.a2w_phrase').val('');
            new_row.find('.a2w_phrase_replace').val('');
            new_row.find('.delete').hide();

            set_last_phrase_row_enability(true);
            $(this_row).parents('.panel-body').append(new_row);
            set_last_phrase_row_enability(false);

            row.find('.delete').show();
        }


        var settings_changed = false;

        $("#phrase_list > .panel-body").change(function () {
            if (!settings_changed) {
                settings_changed = true;

                $('a.apply-phrase-rules').hide();
                $('a.apply-phrase-rules').prev().show();

            }
        });


        set_last_phrase_row_enability(false);

        var keyup_timer = false;

        $('#phrase_list > .panel-body').on('keyup', 'input[type="text"]', function () {
            var this_row = $(this).parents('.row');
            if (keyup_timer) {
                clearTimeout(keyup_timer);
            }
            keyup_timer = setTimeout(function () {

                if (check_phrases() && $.trim($(this_row).parents('.panel-body').find(".row:last-child .a2w_phrase").val()) != '') {

                    add_phrase_row(this_row);


                }
            }, 1000);

            //$(this).removeClass('error_input');
        });

        $('#phrase_list > .panel-body').on('click', '.delete', function () {
            if ($(this).parents('.row').is(":eq(1)") && $(this).parents('.panel-body').find('.row').length < 3) {
                //first action: empty first phrase row
                var row = $(this).parents('.row:eq(1)');
                row.find('input[type="text"]').val('');
            } else if ($(this).parents('.row').is(":last-child")) {
                //last action must be empty
            } else {
                $(this).trigger('change');
                $(this).parents('.row').remove();
            }

            check_phrases();

            return false;
        });

        if (jQuery.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip({"placement": "top"});
        }

        $('#save-phrases').on('click', function () {
            if ($(this).find('.has-error').length > 0)
                return false;

            var data = {'action': 'a2w_update_phrase_rules', 'phrases': []};

            $('#phrase_list > .panel-body .row').each(function () {
                if (!$(this).is(":last-child") && !$(this).is(":first-child")) {
                    var rule = {'phrase': $(this).find('.a2w_phrase').val(),
                        'phrase_replace': $(this).find('.a2w_phrase_replace').val()
                    };
                    data.phrases.push(rule);
                }
            });

            jQuery.post(ajaxurl, data).done(function (response) {
                show_notification('Saved successfully.');
                var json = jQuery.parseJSON(response);

                settings_changed = false;
                $('a.apply-phrase-rules').show();
                $('a.apply-phrase-rules').prev().hide();

            }).fail(function (xhr, status, error) {
                show_notification('Save failed.', true);
            });

            return false;

        });

    })(jQuery);




</script>
