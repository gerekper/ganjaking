<?php

/**
 * Mega Menu Modal template
 */

defined('ABSPATH') || exit;

?>

<div id="bdt-ep-megamenu-modal" bdt-modal="stack: true">
    <div class="bdt-modal-dialog bdt-ep-megamenu-modal-wrapper">
        <button class="bdt-modal-close-default" type="button" bdt-close></button>
        <div class="bdt-modal-header">
            <h2 class="bdt-modal-title"><?php esc_html_e('Mega Menu', 'bdthemes-element-pack'); ?></h2>
        </div>
        <div class="bdt-modal-body">
            <!-- sm ENABLE/DISABLE SWITCHER -->
            <div id="ep-megamenu-toggler">
                <div class="ep-dashboard-widgets">
                    <label for="bdt-item-enable"><?php esc_html_e('Enable Mega Menu', 'bdthemes-element-pack'); ?></label>
                    <input id="bdt-item-enable" type="checkbox" class="ep-item-toggle" value="1">
                </div>
                <a id="ep-content-trigger" class="elementor bdt-button bdt-button-default bdt-margin-small-right" href="javascript:void(0) "><?php echo esc_html__('Edit Mega Menu Content'); ?></a>
            </div>

            <!-- sm BUILDER CONTAINER -->
            <div class="ep-content-width">
                <label for="ep-content-width-type"><?php esc_html_e('Content Width Type', 'bdthemes-element-pack'); ?></label>
                <select id="ep-content-width-type">
                    <option value="default_width">
                        <?php esc_html_e('Default', 'bdthemes-element-pack'); ?>
                    </option>
                    <option value="full_width">
                        <?php esc_html_e('Full', 'bdthemes-element-pack'); ?>
                    </option>
                    <option value="custom_width">
                        <?php esc_html_e('Custom', 'bdthemes-element-pack'); ?>
                    </option>
                </select>
            </div>

            <div id="ep-megamenu-custom-content">
                <div id="ep-custom-width-valueb">
                    <div class="ep-custom-width">
                        <label for="ep-custom-width-value"><?php esc_html_e('Custom Width', 'bdthemes-element-pack'); ?></label>
                        <div class="form-control">
                            <input type="text" placeholder="750px;" id="ep-custom-width-value" />
                        </div>
                    </div>
                </div>
                <div id="ep-megamenu-custom-position">
                    <div class="ep-megamenu-custom-position">
                        <label for="ep-megamenu-custom-position"><?php esc_html_e('Menu Position', 'bdthemes-element-pack'); ?></label>
                        <select class="form-control" id="ep-megamenu-custom-position-value">
                            <option value="bottom-left">
                                <?php esc_html_e('Bottom Left', 'bdthemes-element-pack'); ?>
                            </option>
                            <option value="bottom-center">
                                <?php esc_html_e('Bottom Center', 'bdthemes-element-pack'); ?>
                            </option>
                            <option value="bottom-right">
                                <?php esc_html_e('Bottom Right', 'bdthemes-element-pack'); ?>
                            </option>
                        </select>
                    </div>
                </div>
            </div>


            <!-- sm BADGE  -->
            <div class="ep-modal-text-separator"><?php esc_html_e('Badge', 'bdthemes-element-pack'); ?></div>
            <div class="ep-badge">
                <div class="ep-badge-item">
                    <label for="ep-badge-text-field">
                        <?php esc_html_e('Badge Text', 'bdthemes-element-pack'); ?>
                    </label>
                    <input type="text" placeholder="<?php esc_html_e('Badge Text', 'bdthemes-element-pack'); ?>" id="ep-badge-text-field" />
                </div>

                <div class="ep-badge-item">
                    <label for="ep-badge-text-color">
                        <?php esc_html_e('Color', 'bdthemes-element-pack'); ?>
                    </label>
                    <div class="form-control">
                        <input type="text" value="#115cfa" class="ep-menu-colorpicker" id="ep-badge-text-color" />
                    </div>
                </div>
                <div class="ep-badge-item">
                    <label for="ep-badge-text-bgcolor">
                        <?php esc_html_e('Background Color', 'bdthemes-element-pack'); ?>
                    </label>
                    <div class="form-control">
                        <input type="text" value="#222" class="ep-menu-colorpicker" id="ep-badge-text-bgcolor" />
                    </div>
                </div>

            </div>

            <!-- sm ICON picker  -->
            <div class="ep-modal-text-separator"><?php esc_html_e('Icon', 'bdthemes-element-pack'); ?></div>
            <div class="icon-picker-wrap" id="icon-picker-wrap">
                <div class="ep-icon-picker-inner">
                    <label for="select-icon"><?php esc_html_e('Choose Icon', 'bdthemes-element-pack'); ?></label>
                    <ul class="icon-picker">
                        <li class="icon-none" title="None">
                            <span><?php esc_html_e('Reset Icon', 'bdthemes-element-pack'); ?></span>
                            <!-- <i class="fa fa-ban"></i> -->
                        </li>
                        <li id='select-icon' class="select-icon" title="Icon Library">
                            <i class="far fa-heart"></i>
                            <span><?php esc_html_e('Select Icon', 'bdthemes-element-pack'); ?></span>
                        </li>

                        <input type="hidden" name="icon_value" id="icon_value" value="">
                        <input type="hidden" name="icon_library" id="icon_library" value="">
                    </ul>
                </div>

                <div class="ep-icon-color">
                    <label for="ep-icon-color">
                        <?php esc_html_e('Choose Icon Color'); ?>
                    </label>
                    <div class="form-control">
                        <input type="text" value="#f0506e" class="ep-menu-colorpicker" id="ep-icon-color" />
                    </div>
                </div>

            </div>

        </div>
        <div class="bdt-modal-footer bdt-text-right">
            <input type="hidden" id="ep-modal-menu-id">
            <input type="hidden" id="ep-has-child">
            <span class='spinner'></span>
            <span class='ep-save-notice'></span>
            <?php echo get_submit_button(esc_html__('Save Mega Menu', 'bdthemes-element-pack'), 'ep-item-save', '', false); ?>
        </div>
    </div>
</div>

<div class="bdt-modal-full" id="ep-megamenu-content" bdt-modal="stack: true">
    <div class="bdt-modal-dialog">
        <button class="ep-modal-close bdt-close-large close-mega-menu-modal" type="button">
            <i class="eicon-close"></i>
        </button>
        <iframe id="ep-megamenu-iframe" src=""></iframe>
    </div>
</div>


<div id="ep-megamenu-editor-confirmation" bdt-modal="stack: true">
    <div class="bdt-modal-dialog">
        <button class="bdt-modal-close-default" type="button" bdt-close title="Back to editor"></button>
        <form>
            <div class="bdt-modal-body"><?php esc_html_e('There have made some changes. Do you want to save changes?', 'bdthemes-element-pack'); ?></div>
            <div class="bdt-modal-footer bdt-text-right">
                <button class="bdt-button bdt-button-danger bdt-modal-close confirmation-cancel" type="button"><?php esc_html_e('Don\'t Save', 'bdthemes-element-pack'); ?></button>
                <button class="bdt-button bdt-button-primary confirmation-ok"><?php esc_html_e('Save', 'bdthemes-element-pack'); ?></button>
            </div>
        </form>
    </div>
</div>