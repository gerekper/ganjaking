<?php
$load_review = a2w_get_setting('load_review');
?>
<form method="post" enctype='multipart/form-data'>
    <input type="hidden" name="setting_form" value="1"/>
    <div class="panel panel-primary mt20">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Reviews settings', 'Setting title', 'ali2woo'); ?></h3>
        </div>

        
        <div class="panel-body _a2wfv">
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Aliexpress Review Load', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Enable Review Load feature', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_load_review" name="a2w_load_review" value="yes" <?php if ($load_review): ?>checked<?php endif; ?>/>
                    </div>
                </div>

            </div>

            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Aliexpress Review Sync', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Enable Review Auto-Update feature', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_review_status" name="a2w_review_status" value="yes" <?php if (a2w_get_setting('review_status')): ?>checked<?php endif; ?>/>
                    </div>
                </div>

            </div>

            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Translated Reviews', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Try to import translated reviews`s text from Aliexpress', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_review_translated" name="a2w_review_translated" value="yes" <?php if (a2w_get_setting('review_translated')): ?>checked<?php endif; ?>/>
                    </div>
                </div>

            </div>
            
            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Import Avatars', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Try to import review`s avatar from Aliexpress', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="a2w_review_avatar_import" name="a2w_review_avatar_import" value="yes" <?php if (a2w_get_setting('review_avatar_import')): ?>checked<?php endif; ?>/>
                    </div>
                </div>

            </div>


            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Reviews per product', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('The max. number of reviews (per product) that can be imported during Aliexpress Review Sync', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-8">
                    <div class="form-group input-block no-margin">
                        <input type="text" class="form-control small-input" id="a2w_review_max_per_product" name="a2w_review_max_per_product" value="<?php echo a2w_get_setting('review_max_per_product'); ?>"/>
                    </div>
                </div>
            </div>

            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Reviews Raiting', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Filter imported reviews by the rating', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-3">
                    <div class="form-group input-block no-margin">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1"><?php _e('From', 'ali2woo'); ?></span>
                            <input type="text" class="form-control small-input" aria-describedby="basic-addon1" id="a2w_review_raiting_from" name="a2w_review_raiting_from" value="<?php echo a2w_get_setting('review_raiting_from'); ?>">
                        </div>

                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group input-block no-margin">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2"><?php _e('To', 'ali2woo'); ?></span>
                            <input type="text" class="form-control small-input" aria-describedby="basic-addon2" id="a2w_review_raiting_to" name="a2w_review_raiting_to" value="<?php echo a2w_get_setting('review_raiting_to'); ?>" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Default Avatar', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Defalut review`s Avatar photo used for displaying near review`s text', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-3">
                    <?php
                    $cur_a2w_review_noavatar_photo = a2w_get_setting('review_noavatar_photo', A2W()->plugin_url() . '/assets/img/noavatar.png');
                    ?>
                    <?php /* <div href="#" class="thumbnail"> */ ?>
                    <img style="height: 80px; width: 80px; display: block;" src="<?php echo $cur_a2w_review_noavatar_photo ?>"/>
                    <?php /* </div>  */ ?>
                </div>
                <div class="col-md-5">
                    <label class="btn btn-default btn-file">
                        Browse <input class="form-control" type="file" hidden id="a2w_review_noavatar_photo" name="a2w_review_noavatar_photo">
                    </label>
                </div>
            </div>

            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Load Review Attributes', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Import Review Attributes from Aliexpress', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control small-input" id="a2w_review_load_attributes" name="a2w_review_load_attributes" <?php if (a2w_get_setting('review_load_attributes')): ?>value="yes" checked<?php endif; ?> />
                    </div>
                </div>

            </div>

            <div class="row review_option" <?php if (!$load_review): ?>style="display: none;"<?php endif; ?>>
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Load Review photos', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Load Review Photo list', 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control small-input" id="a2w_review_show_image_list" name="a2w_review_show_image_list" <?php if (a2w_get_setting('review_show_image_list')): ?>value="yes" checked<?php endif; ?>  />
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="a2w_moderation_reviews">
                        <strong><?php _ex('Moderation of Reviews', 'Setting title', 'ali2woo'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Allow manually approve imported reviews", 'setting description', 'ali2woo'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control small-input" id="a2w_moderation_reviews" name="a2w_moderation_reviews" <?php if (a2w_get_setting('moderation_reviews')): ?>value="1" checked<?php endif; ?>  />
                    </div>
                </div>
            </div>

        </div> 
    </div>

    <div class="container-fluid _a2wfv">
        <div class="row pt20 border-top">
            <div class="col-sm-12">
                <input id="a2w_remove_all_reviews" class="btn btn-default" type="button" value="<?php _e('Remove all reviews', 'ali2woo'); ?>"/>
                <input class="btn btn-success" type="submit" value="<?php _e('Save settings', 'ali2woo'); ?>"/>
            </div>
        </div>
    </div>

</form>

<script>
    function a2w_isInt(value) {
        return !isNaN(value) &&
                parseInt(Number(value)) == value &&
                !isNaN(parseInt(value, 10));
    }

    (function ($) {

        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }
        
        jQuery("#a2w_load_review").change(function () {
            if(jQuery(this).is(':checked')){
                $('.review_option').show();
            }else{
                $('.review_option').hide();
            }
            return true;
        });
        
        var a2w_review_max_per_product_keyup_timer = false;
        $('#a2w_review_max_per_product').on('keyup', function () {
            if (a2w_review_max_per_product_keyup_timer) {
                clearTimeout(a2w_review_max_per_product_keyup_timer);
            }

            var this_el = $(this);

            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            a2w_review_max_per_product_keyup_timer = setTimeout(function () {
                if (this_el.val() !== "" && (!a2w_isInt(this_el.val()) || this_el.val() < 1)) {
                    this_el.after("<span class='help-block'>The value should be an integer greater than 0</span>");
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);
        });

        var a2w_review_raiting_from_keyup_timer = false;

        $('#a2w_review_raiting_from').on('keyup', function () {
            if (a2w_review_raiting_from_keyup_timer) {
                clearTimeout(a2w_review_raiting_from_keyup_timer);
            }

            $('#a2w_review_raiting_to').trigger('keyup');

            var this_el = $(this);

            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            a2w_review_raiting_from_keyup_timer = setTimeout(function () {
                if (!a2w_isInt(this_el.val()) || this_el.val() < 1 || this_el.val() > 5) {
                    this_el.parents('.input-group').after("<span class='help-block'>The value should be an integer between 1 and 5</span>");
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);
        });

        var a2w_review_raiting_to_keyup_timer = false;

        $('#a2w_review_raiting_to').on('keyup', function () {
            if (a2w_review_raiting_to_keyup_timer) {
                clearTimeout(a2w_review_raiting_to_keyup_timer);
            }

            var this_el = $(this);

            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            a2w_review_raiting_to_keyup_timer = setTimeout(function () {
                if (!a2w_isInt(this_el.val()) || this_el.val() < 1 || this_el.val() > 5 || !a2w_isInt($('#a2w_review_raiting_from').val()) || this_el.val() < $('#a2w_review_raiting_from').val()) {
                    this_el.parents('.input-group').after("<span class='help-block'>The value should be an integer between 1 and 5. Also it can`t be less than 'from' value.</span>");
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);
        });

        //form buttons  
        $('#a2w_remove_all_reviews').click(function () {
            if(confirm('Are you sure you want to delete all reviews?')){
                var e = $(this);
                e.val('Processing...');
                var data = {'action': 'a2w_arvi_remove_reviews'};
                $.post(ajaxurl, data, function (response) {
                    var json = $.parseJSON(response);

                    if (json.state === 'error') {
                        console.log(json);
                        e.val('Error');
                    } else {
                        e.val('Done!');
                    }
                });
            }
        });


        $('.a2w-content form').on('submit', function () {
            if ($(this).find('.has-error').length > 0)
                return false;
        });

    })(jQuery);




</script>
