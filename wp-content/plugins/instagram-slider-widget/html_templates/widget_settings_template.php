<?php
/* @var array $args */

$instance = $args['instance'];
$accounts = $args['accounts'];
$accounts_business = $args['accounts_business'];
$sliders = $args['sliders'];
$options_linkto = $args['options_linkto'];

$w_id  = explode('-', $this->id)[1];
?>

<div class="jr-container">
    <div class="isw-tabs" id="widget_tabs_<?=$w_id?>" data-widget-id="<?=$w_id?>">
        <div class="isw-common-settings">
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('Title:', 'instagram-slider-widget'); ?></strong></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>"
                       value="<?php echo $instance['title']; ?>"/>
            </p>
            <p>
                <strong><?php _e('Search Instagram for:', 'instagram-slider-widget'); ?></strong>
                <span class="jr-search-for-container">
                    <label class="jr-seach-for">
                        <input type="radio" id="<?php echo $this->get_field_id('search_for'); ?>"
                               name="<?php echo $this->get_field_name('search_for'); ?>"
                               value="account" <?php checked('account', $instance['search_for']); ?> />
                        <?php _e('Account:', 'instagram-slider-widget'); ?>
                    </label>
                    <?php
                    if (count($accounts)) {
                        ?>

                        <select id="<?php echo $this->get_field_id('account'); ?>" class="isw-float-right "
                                name="<?php echo $this->get_field_name('account'); ?>"><?php
                        foreach ($accounts as $acc) {
                            $selected = $instance['account'] == $acc['username'] ? "selected='selected'" : "";
                            echo "<option value='{$acc['username']}' {$selected}>{$acc['username']}</option>";
                        }
                        ?>
                        </select><?php
                    } else {
                        echo "<a href='" . admin_url('admin.php?page=settings-wisw') . "'>" . __('Add account in settings', 'instagram-slider-widget') . "</a>";
                    }
                    ?>
                </span>
                <span class="jr-search-for-container" style="margin-top: 11px !important;">
                    <label class="jr-seach-for">
                        <input type="radio" id="<?php echo $this->get_field_id('search_for'); ?>"
                               name="<?php echo $this->get_field_name('search_for'); ?>"
                               value="account_business" <?php checked('account_business', $instance['search_for']); ?> />
                        <?php _e('Business account:', 'instagram-slider-widget'); ?>
                    </label>
                    <?php
                    if (count($accounts_business)) {
                        ?>

                        <select id="<?php echo $this->get_field_id('account_business'); ?>" class="isw-float-right "
                                name="<?php echo $this->get_field_name('account_business'); ?>"><?php
                        foreach ($accounts_business as $acc) {
                            $selected = $instance['account_business'] == $acc['username'] ? "selected='selected'" : "";
                            echo "<option value='{$acc['username']}' {$selected}>{$acc['username']}</option>";
                        }
                        ?>
                        </select><?php
                    } else {
                        echo "<a href='" . admin_url('admin.php?page=settings-wisw') . "'>" . __('Add account in settings', 'instagram-slider-widget') . "</a>";
                    }
                    ?>
                </span>
                <span class="jr-search-for-container"><label class="jr-seach-for"><input type="radio" class=""
                                                                                         id="<?php echo $this->get_field_id('search_for'); ?>"
                                                                                         name="<?php echo $this->get_field_name('search_for'); ?>"
                                                                                         value="username" <?php checked('username', $instance['search_for']); ?> /> <?php _e('Username:', 'instagram-slider-widget'); ?></label> <input
                            id="<?php echo $this->get_field_id('username'); ?>" class="isw-float-right inline-field-text"
                            name="<?php echo $this->get_field_name('username'); ?>"
                            value="<?php echo $instance['username']; ?>"/></span>
                <span class="jr-search-for-container"><label class="jr-seach-for"><input type="radio" class=""
                                                                                         id="<?php echo $this->get_field_id('search_for'); ?>"
                                                                                         name="<?php echo $this->get_field_name('search_for'); ?>"
                                                                                         value="hashtag" <?php checked('hashtag', $instance['search_for']); ?> /> <?php _e('Hashtag:', 'instagram-slider-widget'); ?></label> <input
                            id="<?php echo $this->get_field_id('hashtag'); ?>" class="isw-float-right inline-field-text"
                            name="<?php echo $this->get_field_name('hashtag'); ?>"
                            value="<?php echo $instance['hashtag']; ?>"
                            placeholder="<?php _e('without # sign', 'instagram-slider-widget') ?>"/></span>
            </p>
            <p class="<?php if ('hashtag' != $instance['search_for']) {
                echo 'hidden';
            } ?>">
                <label for="<?php echo $this->get_field_id('blocked_users'); ?>"><?php _e('Block Users', 'instagram-slider-widget'); ?>
                    :</label>
                <input class="widefat" id="<?php echo $this->get_field_id('blocked_users'); ?>"
                       name="<?php echo $this->get_field_name('blocked_users'); ?>"
                       value="<?php echo $instance['blocked_users']; ?>"/>
                <span class="jr-description"><?php _e('Enter words separated by commas whose images you don\'t want to show', 'instagram-slider-widget'); ?></span>
            </p>
            <p class="<?php if ('username' != $instance['search_for']) {
                echo 'hidden';
            } ?>"><strong><?php _e('Save in Media Library: ', 'instagram-slider-widget'); ?></strong>

                <label class="switch" for="<?php echo $this->get_field_id('attachment'); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id('attachment'); ?>"
                           name="<?php echo $this->get_field_name('attachment'); ?>" type="checkbox"
                           value="1" <?php checked('1', $instance['attachment']); ?> /><span
                            class="slider round"></span></label>
                <br><span
                        class="jr-description"><?php _e(' Turn on to save Instagram Images into WordPress media library.', 'instagram-slider-widget') ?></span>
                <?php
                if (isset ($instance['username']) && !empty($instance['username'])) {
                    echo '<br><button class="button action jr-delete-instagram-dupes" type="button" data-username="' . $instance['username'] . '"><strong>Remove</strong> duplicate images for <strong>' . $instance['username'] . '</strong></button><span class="jr-spinner"></span>';
                    echo '<br><br><strong><span class="deleted-dupes-info"></span></strong>';
                    wp_nonce_field('jr_delete_instagram_dupes', 'delete_insta_dupes_nonce');
                }
                ?>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('refresh_hour'); ?>"><strong><?php _e('Check for new images every:', 'instagram-slider-widget'); ?></strong>
                    <input class="small-text isw-float-right" type="number" min="1" max="200"
                           id="<?php echo $this->get_field_id('refresh_hour'); ?>"
                           name="<?php echo $this->get_field_name('refresh_hour'); ?>"
                           value="<?php echo $instance['refresh_hour']; ?>"/>
                    <span><?php _e('hours', 'instagram-slider-widget'); ?></span>
                </label>
            </p>
        </div>
        <ul>
            <li class="desk_tab active" id="desk_tab_<?=$w_id?>" data-tab-id="<?=$w_id?>">Desktop</li>
            <li class="mob_tab" id="mob_tab_<?=$w_id?>" data-tab-id="<?=$w_id?>">Mobile</li>
        </ul>
        <div>
            <div id="desk_tab_content_<?=$w_id?>" class="desk_settings">
                <h3 style="width: 100%; text-align: center">DESKTOP SETTINGS</h3>
                <p class="<?php if ('hashtag' == $instance['search_for']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('blocked_words'); ?>"><?php _e('Block words', 'instagram-slider-widget'); ?>
                        :</label>
                    <input class="widefat" id="<?php echo $this->get_field_id('blocked_words'); ?>"
                           name="<?php echo $this->get_field_name('blocked_words'); ?>"
                           value="<?php echo $instance['blocked_words']; ?>"/>
                    <span class="jr-description"><?php _e('Enter comma-separated words. If one of them occurs in the image description, the image will not be displayed', 'instagram-slider-widget'); ?></span>
                </p>
                <p class="<?php if ('hashtag' == $instance['search_for']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('allowed_words'); ?>"><?php _e('Allow words', 'instagram-slider-widget'); ?>
                        :</label>
                    <input class="widefat" id="<?php echo $this->get_field_id('allowed_words'); ?>"
                           name="<?php echo $this->get_field_name('allowed_words'); ?>"
                           value="<?php echo $instance['allowed_words']; ?>"/>
                    <span class="jr-description"><?php _e('Enter comma-separated words. If one of them occurs in the image description, the image will be displayed', 'instagram-slider-widget'); ?></span>
                </p>
                <p id="img_to_show">
                    <label for="<?php echo $this->get_field_id('images_number'); ?>"><strong><?php _e('Count of images to show:', 'instagram-slider-widget'); ?></strong>
                        <input class="small-text isw-float-right" type="number" min="1" max=""
                               id="<?php echo $this->get_field_id('images_number'); ?>"
                               name="<?php echo $this->get_field_name('images_number'); ?>"
                               value="<?php echo $instance['images_number']; ?>"/>
                        <span class="jr-description">
                        <?php if (!$this->WIS->is_premium()) {
                            _e('Maximum 20 images in free version.', 'instagram-slider-widget');
                            echo " " . sprintf(__("More in <a href='%s'>PRO version</a>", 'instagram-slider-widget'), $this->WIS->get_support()->get_pricing_url(true, "wis_widget_settings"));
                        }
                        ?>
                    </span>
                    </label>
                </p>

                <p class="show_feed_header <?php if ('account_business' != $instance['search_for']) {
                    echo 'hidden';
                } ?>">
                    <strong><?php _e('Show feed header:', 'instagram-slider-widget'); ?></strong>
                    <label class="switch" for="<?php echo $this->get_field_id('show_feed_header'); ?>">
                        <input class="widefat" id="<?php echo $this->get_field_id('show_feed_header'); ?>"
                               name="<?php echo $this->get_field_name('show_feed_header'); ?>" type="checkbox"
                               value="1" <?php checked('1', $instance['show_feed_header']); ?> />
                        <span class="slider round"></span>
                    </label>
                </p>
                <p>

                    <label for="<?php echo $this->get_field_id('template'); ?>"><strong><?php _e('Template', 'instagram-slider-widget'); ?></strong>
                        <select class="widefat" name="<?php echo $this->get_field_name('template'); ?>"
                                id="<?php echo $this->get_field_id('template'); ?>">
                            <?php
                            if (count($sliders)) {
                                foreach ($sliders as $key => $slider) {
                                    $selected = ($instance['template'] == $key) ? "selected='selected'" : '';
                                    echo "<option value='{$key}' {$selected}>{$slider}</option>\n";
                                }
                            }
                            if (!$this->WIS->is_premium()) {
                                ?>
                                <optgroup label="Available in PRO">
                                    <option value='slick_slider' disabled="disabled">Slick</option>
                                    <option value='masonry' disabled="disabled">Masonry</option>
                                    <option value='highlight' disabled="disabled">Highlight</option>
                                    <option value='showcase' disabled="disabled">Shopifeed - Thumbnails</option>
                                </optgroup>
                                <?php
                            }
                            ?>
                        </select>
                    </label>
                </p>
                <span id="masonry_notice"
                      class="masonry_notice jr-description <?php if ('masonry' != $instance['template']) {
                          echo 'hidden';
                      } ?>"><?php _e("Not recommended for <strong>sidebar</strong>") ?></span>
                <p class="<?php if ('thumbs' != $instance['template'] && 'thumbs-no-border' != $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('columns'); ?>"><strong><?php _e('Number of Columns:', 'instagram-slider-widget'); ?></strong>
                        <input class="small-text isw-float-right" id="<?php echo $this->get_field_id('columns'); ?>"
                               name="<?php echo $this->get_field_name('columns'); ?>"
                               value="<?php echo $instance['columns']; ?>"/>
                        <span class='jr-description'><?php _e('max is 10 ( only for thumbnails template )', 'instagram-slider-widget'); ?></span>
                    </label>
                </p>
                <p class="masonry_settings <?php if ('masonry' != $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('gutter'); ?>"><strong><?php _e('Vertical space between item elements:', 'instagram-slider-widget'); ?></strong>
                        <input class="small-text isw-float-right" id="<?php echo $this->get_field_id('gutter'); ?>"
                               name="<?php echo $this->get_field_name('gutter'); ?>"
                               value="<?php echo $instance['gutter']; ?>"/>
                        <span><?php _e('px', 'instagram-slider-widget'); ?></span>
                    </label>
                    <br>
                    <label for="<?php echo $this->get_field_id('masonry_image_width'); ?>"><strong><?php _e('Image width:', 'instagram-slider-widget'); ?></strong>
                        <input class="small-text isw-float-right" id="<?php echo $this->get_field_id('masonry_image_width'); ?>"
                               name="<?php echo $this->get_field_name('masonry_image_width'); ?>"
                               value="<?php echo $instance['masonry_image_width']; ?>"/>
                        <span><?php _e('px', 'instagram-slider-widget'); ?></span>
                    </label>
                </p>
                <p class="slick_settings <?php if ('slick_slider' != $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <strong><?php _e('Enable control buttons:', 'instagram-slider-widget'); ?></strong>
                    <label class="switch" for="<?php echo $this->get_field_id('enable_control_buttons'); ?>">
                        <input class="widefat" id="<?php echo $this->get_field_id('enable_control_buttons'); ?>"
                               name="<?php echo $this->get_field_name('enable_control_buttons'); ?>" type="checkbox"
                               value="1" <?php checked('1', $instance['enable_control_buttons']); ?> />
                        <span class="slider round"></span>
                    </label>
                    <br>
                    <strong><?php _e('Keep 1x1 Instagram ratio:', 'instagram-slider-widget'); ?></strong>
                    <label class="switch" for="<?php echo $this->get_field_id('keep_ratio'); ?>">
                        <input class="widefat" id="<?php echo $this->get_field_id('keep_ratio'); ?>"
                               name="<?php echo $this->get_field_name('keep_ratio'); ?>" type="checkbox"
                               value="1" <?php checked('1', $instance['keep_ratio']); ?> />
                        <span class="slider round"></span>
                    </label>
                    <br>
                    <label class="slick_img_size"
                           for="<?php echo $this->get_field_id('slick_img_size'); ?>"><strong><?php _e('Images size: ', 'instagram-slider-widget'); ?></strong>
                        <input class="small-text" type="number" min="1" max="500" step="1"
                               id="<?php echo $this->get_field_id('slick_img_size'); ?>"
                               name="<?php echo $this->get_field_name('slick_img_size'); ?>"
                               value="<?php echo $instance['slick_img_size']; ?>"/>
                        <span><?php _e('px', 'instagram-slider-widget'); ?></span>
                    </label>
                    <br>
                    <label for="<?php echo $this->get_field_id('slick_slides_to_show'); ?>"><strong><?php _e('Pictures per slide:', 'instagram-slider-widget'); ?></strong>
                        <input class="small-text" id="<?php echo $this->get_field_id('slick_slides_to_show'); ?>"
                               name="<?php echo $this->get_field_name('slick_slides_to_show'); ?>"
                               value="<?php echo $instance['slick_slides_to_show']; ?>"/>
                        <span><?php _e('pictures', 'instagram-slider-widget'); ?></span>
                    </label>
                    <br>
                    <strong><?php _e('Space between pictures:', 'instagram-slider-widget'); ?></strong>
                    <label class="switch" for="<?php echo $this->get_field_id('slick_slides_padding'); ?>">
                        <input class="widefat" id="<?php echo $this->get_field_id('slick_slides_padding'); ?>"
                               name="<?php echo $this->get_field_name('slick_slides_padding'); ?>" type="checkbox"
                               value="1" <?php checked('1', $instance['slick_slides_padding']); ?> />
                        <span class="slider round"></span>
                    </label>
                    <br>
                </p>
                <p class="highlight_settings <?php if ('highlight' != $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('highlight_offset'); ?>"><strong><?php _e('Offset', 'instagram-slider-widget'); ?></strong>
                        <input type="number" min="1" class="small-text"
                               id="<?php echo $this->get_field_id('highlight_offset'); ?>"
                               name="<?php echo $this->get_field_name('highlight_offset'); ?>"
                               value="<?php echo $instance['highlight_offset']; ?>"/>
                    </label>
                    <br>
                    <label for="<?php echo $this->get_field_id('highlight_pattern'); ?>"><strong><?php _e('Pattern', 'instagram-slider-widget'); ?></strong>
                        <input type="number" min="0" class="small-text"
                               id="<?php echo $this->get_field_id('highlight_pattern'); ?>"
                               name="<?php echo $this->get_field_name('highlight_pattern'); ?>"
                               value="<?php echo $instance['highlight_pattern']; ?>"/>
                    </label>
                </p>
                <p class="shopifeed_settings <?php if ('showcase' != $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('shopifeed_phone'); ?>"><strong><?php _e('Phone', 'instagram-slider-widget'); ?></strong>
                        <input type="text" class="shopifeed_phone isw-float-right"
                               id="<?php echo $this->get_field_id('shopifeed_phone'); ?>"
                               name="<?php echo $this->get_field_name('shopifeed_phone'); ?>"
                               value="<?php echo $instance['shopifeed_phone']; ?>"/>
                        <span id="" class="jr-description"><?php _e("Use for whatsapp messages") ?></span>
                    </label>
                    <label for="<?php echo $this->get_field_id('shopifeed_color'); ?>"><strong><?php _e('Buttons Color', 'instagram-slider-widget'); ?></strong>
                        <input type="color" class="shopifeed_color isw-float-right"
                               id="<?php echo $this->get_field_id('shopifeed_color'); ?>"
                               name="<?php echo $this->get_field_name('shopifeed_color'); ?>"
                               value="<?php echo $instance['shopifeed_color']; ?>"
                               style="border: none !important;"/>
                    </label>

                    <br>
                    <label for="<?php echo $this->get_field_id('shopifeed_columns'); ?>"><strong><?php _e('Columns count', 'instagram-slider-widget'); ?></strong>
                        <input type="number" class="isw-float-right shopifeed_columns" min="1" max="6" step="1"
                               id="<?php echo $this->get_field_id('shopifeed_columns'); ?>"
                               name="<?php echo $this->get_field_name('shopifeed_columns'); ?>"
                               value="<?php echo $instance['shopifeed_columns']; ?>"/>
                    </label>
                </p>
                <p class="slider_normal_settings jr-slider-options <?php if ('slider' != $instance['template'] && 'slider-overlay' != $instance['template']) {
                    echo 'hidden';
                } ?>">

                    <?php _e('Slider Navigation Controls:', 'instagram-slider-widget'); ?><br>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id('controls'); ?>"
                                                   name="<?php echo $this->get_field_name('controls'); ?>"
                                                   value="prev_next" <?php checked('prev_next', $instance['controls']); ?> /> <?php _e('Prev & Next', 'instagram-slider-widget'); ?>
                    </label>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id('controls'); ?>"
                                                   name="<?php echo $this->get_field_name('controls'); ?>"
                                                   value="numberless" <?php checked('numberless', $instance['controls']); ?> /> <?php _e('Dotted', 'instagram-slider-widget'); ?>
                    </label>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id('controls'); ?>"
                                                   name="<?php echo $this->get_field_name('controls'); ?>"
                                                   value="none" <?php checked('none', $instance['controls']); ?> /> <?php _e('No Navigation', 'instagram-slider-widget'); ?>
                    </label>
                    <br>
                    <?php _e('Slider Animation:', 'instagram-slider-widget'); ?><br>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id('animation'); ?>"
                                                   name="<?php echo $this->get_field_name('animation'); ?>"
                                                   value="slide" <?php checked('slide', $instance['animation']); ?> /> <?php _e('Slide', 'instagram-slider-widget'); ?>
                    </label>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id('animation'); ?>"
                                                   name="<?php echo $this->get_field_name('animation'); ?>"
                                                   value="fade" <?php checked('fade', $instance['animation']); ?> /> <?php _e('Fade', 'instagram-slider-widget'); ?>
                    </label>
                    <br>
                    <label for="<?php echo $this->get_field_id('slidespeed'); ?>"><?php _e('Slide Speed:', 'instagram-slider-widget'); ?>
                        <input type="number" min="1000" step="100" class="small-text"
                               id="<?php echo $this->get_field_id('slidespeed'); ?>"
                               name="<?php echo $this->get_field_name('slidespeed'); ?>"
                               value="<?php echo $instance['slidespeed']; ?>"/>
                        <span><?php _e('milliseconds', 'instagram-slider-widget'); ?></span>
                        <span class='jr-description'><?php _e('1000 milliseconds = 1 second', 'instagram-slider-widget'); ?></span>
                    </label>
                    <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Slider Text Description:', 'instagram-slider-widget'); ?></label>
                    <select size=3 class='widefat' id="<?php echo $this->get_field_id('description'); ?>"
                            name="<?php echo $this->get_field_name('description'); ?>[]" multiple="multiple">
                        <option class="<?php if ('hashtag' == $instance['search_for']) {
                            echo 'hidden';
                        } ?>"
                                value='username' <?php $this->selected($instance['description'], 'username'); ?>><?php _e('Username', 'instagram-slider-widget'); ?></option>
                        <option value='time'<?php $this->selected($instance['description'], 'time'); ?>><?php _e('Time', 'instagram-slider-widget'); ?></option>
                        <option value='caption'<?php $this->selected($instance['description'], 'caption'); ?>><?php _e('Caption', 'instagram-slider-widget'); ?></option>
                    </select>
                    <span class="jr-description"><?php _e('Hold ctrl and click the fields you want to show/hide on your slider. Leave all unselected to hide them all. Default all selected.', 'instagram-slider-widget') ?></span>
                </p>
                <p class="words_in_caption <?php if ('thumbs' == $instance['template'] || 'thumbs-no-border' == $instance['template'] || 'highlight' == $instance['template'] || 'slick_slider' == $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('caption_words'); ?>"><strong><?php _e('Number of words in caption:', 'instagram-slider-widget'); ?></strong>
                        <input class="isw-float-right small-text" type="number" min="0" max="200"
                               id="<?php echo $this->get_field_id('caption_words'); ?>"
                               name="<?php echo $this->get_field_name('caption_words'); ?>"
                               value="<?php echo $instance['caption_words']; ?>"/>
                    </label>
                </p>
                <br>
                <strong><?php _e('Enable author\'s ad:', 'instagram-slider-widget'); ?></strong>
                <label class="switch" for="<?php echo $this->get_field_id('enable_ad'); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id('enable_ad'); ?>"
                           name="<?php echo $this->get_field_name('enable_ad'); ?>" type="checkbox"
                           value="1" <?php checked('1', $instance['enable_ad']); ?> />
                    <span class="slider round"></span>
                </label>
                <p>
                    <label for="<?php echo $this->get_field_id('orderby'); ?>"><strong><?php _e('Order by', 'instagram-slider-widget'); ?></strong>
                        <select class="widefat" name="<?php echo $this->get_field_name('orderby'); ?>"
                                id="<?php echo $this->get_field_id('orderby'); ?>">
                            <option value="date-ASC" <?php selected($instance['orderby'], 'date-ASC', true); ?>><?php _e('Date - Ascending', 'instagram-slider-widget'); ?></option>
                            <option value="date-DESC" <?php selected($instance['orderby'], 'date-DESC', true); ?>><?php _e('Date - Descending', 'instagram-slider-widget'); ?></option>
                            <option value="popular-ASC" <?php selected($instance['orderby'], 'popular-ASC', true); ?>><?php _e('Popularity - Ascending', 'instagram-slider-widget'); ?></option>
                            <option value="popular-DESC" <?php selected($instance['orderby'], 'popular-DESC', true); ?>><?php _e('Popularity - Descending', 'instagram-slider-widget'); ?></option>
                            <option value="rand" <?php selected($instance['orderby'], 'rand', true); ?>><?php _e('Random', 'instagram-slider-widget'); ?></option>
                        </select>
                    </label>
                </p>
                <p class="isw-linkto <?php if ('showcase' == $instance['template']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('images_link'); ?>"><strong><?php _e('Link to', 'instagram-slider-widget'); ?></strong>
                        <select class="widefat" name="<?php echo $this->get_field_name('images_link'); ?>"
                                id="<?php echo $this->get_field_id('images_link'); ?>">
                            <?php
                            if (count($options_linkto)) {
                                foreach ($options_linkto as $key => $option) {
                                    $selected = selected($instance['images_link'], $key, false);
                                    echo "<option value='{$key}' {$selected}>{$option}</option>\n";
                                }
                            }
                            if (!$this->WIS->is_premium()) {
                                ?>
                                <optgroup label="Available in PRO">
                                    <option value='1' disabled="disabled">Pop Up</option>
                                </optgroup>
                                <?php
                            }
                            ?>
                        </select>
                    </label>
                </p>
                <p class="<?php if ('custom_url' != $instance['images_link']) {
                    echo 'hidden';
                } ?>">
                    <label for="<?php echo $this->get_field_id('custom_url'); ?>"><?php _e('Custom link:', 'instagram-slider-widget'); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id('custom_url'); ?>"
                           name="<?php echo $this->get_field_name('custom_url'); ?>"
                           value="<?php echo $instance['custom_url']; ?>"/>
                    <span><?php _e('* use this field only if the above option is set to <strong>Custom Link</strong>', 'instagram-slider-widget'); ?></span>
                </p>
            </div>
            <?php
                if(defined('WISP_PLUGIN_ACTIVE') && $this->WIS->is_premium()) :
                    echo apply_filters('wis/mob_settings', $this, $instance, $sliders, $options_linkto, $w_id);
                else: ?>
                    <div id="mob_tab_content_<?=$w_id?>" class="mob_settings" style="display: none;">
                        <h3 style="width: 100%; text-align: center">MOBILE SETTINGS AVAILABLE ONLY IN PREMIUM VERSION</h3>
                    </div>
            <?php endif; ?>
        </div>
        <?php $widget_id = preg_replace('/[^0-9]/', '', $this->id);
        if ($widget_id != '') : ?>
            <p>
                <label for="jr_insta_shortcode"><?php _e('Shortcode of this Widget:', 'instagram-slider-widget'); ?></label>
                <input id="jr_insta_shortcode" onclick="this.setSelectionRange(0, this.value.length)" type="text"
                       class="widefat" value="[jr_instagram id=&quot;<?php echo $widget_id ?>&quot;]"
                       readonly="readonly" style="border:none; color:black; font-family:monospace;">
                <span class="jr-description"><?php _e('Use this shortcode in any page or post to display images with this widget configuration!', 'instagram-slider-widget') ?></span>
            </p>
        <?php endif; ?>
    </div>
</div>
