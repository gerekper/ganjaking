<?php
/**
 * Nav menu item: MegaMenu settings popup.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ha__modal modal" id="ha__menu_settings_modal">

    <div class="ha__modal-header">
        <div class="ha__modal-title">
            <span class="branding"></span>
            <span class="title">Happy Menu</span>
        </div>
        <div class="ha__modal-close">
            <a href="#" rel="modal:close"><i class="eicon-close" aria-hidden="true" title="Close"></i></a>
        </div>
    </div>
    <div class="ha__modal-body ha-wid-con">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="blogname">Enable Mega Menu</label></th>
                    <td>
                        <div class="ha-dashboard-widgets__item-toggle ha-toggle">
                            <input id="ha-menu-item-enable" type="checkbox" class="ha-toggle__check ha-widget" value="1">
                            <b class="ha-toggle__switch"></b>
                            <b class="ha-toggle__track"></b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogname">Mega Menu Width</label></th>
                    <td id="xs_megamenu_width_type">
                        <input type="radio" name="width_type" id="width_type_default" value="default_width" checked>
                        <label for="width_type_default"><?php esc_html_e('Default Width', 'happy-addons-pro'); ?></label>
                        <input type="radio" id="width_type_full" name="width_type" value="full_width">
                        <label for="width_type_full"><?php esc_html_e('Full Width', 'happy-addons-pro'); ?></label>
                        <input type="radio" id="width_type_custom" name="width_type" value="custom_width">
                        <label for="width_type_custom"><?php esc_html_e('Custom Width', 'happy-addons-pro'); ?></label>
                    </td>
                </tr>
                <tr class="menu-width-container">
                    <th scope="row"><label for="ha-menu-vertical-menu-width-field"><?php esc_html_e('Menu Width', 'happy-addons-pro'); ?></label></th>
                    <td>
                        <input type="text" placeholder="<?php esc_html_e('750px', 'happy-addons-pro'); ?>" id="ha-menu-vertical-menu-width-field" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogname">Mega Menu Position</label></th>
                    <td id="vertical_megamenu_position_type">
                        <input type="radio" id="position_type_top" name="position_type" value="top_position">
                        <label for="position_type_top"><?php esc_html_e('Default', 'happy-addons-pro'); ?></label>
                        <input type="radio" name="position_type" id="position_type_relative" checked value="relative_position">
                        <label for="position_type_relative"><?php esc_html_e('Relative', 'happy-addons-pro'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogname">Mobile Submenu Content</label></th>
                    <td id="mobile_submenu_content_type">
                        <input type="radio" id="content_type_builder_content" name="content_type" checked value="builder_content">
                        <label for="content_type_builder_content"><?php esc_html_e('Menu Builder Content'); ?></label>
                        <input type="radio" id="content_type_submenu_list" name="content_type" value="submenu_list">
                        <label for="content_type_submenu_list"><?php esc_html_e('WordPress Submenu items'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td>
                        <a id="ha-menu-builder-trigger"
                            class="ha-menu-elementor-button ha-btn elementor"
                            href="#ha-menu-builder-modal"><?php esc_html_e('Edit Mega Menu Content'); ?></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="ha__section_heading">
            <span>Icon</span>
            <span class="sep"></span>
        </div>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="ha-menu-icon-color-field"><?php esc_html_e('Select Icon', 'happy-addons-pro'); ?></label></th>
                    <td>
                        <div class="aim-icon-picker-wrap" id="icon-picker-wrap">
                            <ul class="icon-picker">
                                <li id='select-icon' class="select-icon" title="Icon Library"><i class="fas fa-circle"></i></li>
                                <li class="icon-none" title="None"><i class="fas fa-ban"></i></li>
                                <input type="hidden" name="icon_value" id="ha-menu-icon-field" value="">
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ha-menu-vertical-menu-width-field"><?php esc_html_e('Choose Icon Color', 'happy-addons-pro'); ?></label></th>
                    <td>
                        <input type="text" value="#bada55" class="ha-menu-wpcolor-picker" id="ha-menu-icon-color-field" />
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="ha__section_heading">
            <span>Badge</span>
            <span class="sep"></span>
        </div>

        <div class="ha__flex">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="ha-menu-badge-text-field"><?php esc_html_e('Badge Text', 'happy-addons-pro'); ?></label></th>
                        <td>
                            <input type="text" class="badge-text" placeholder="<?php esc_html_e('Badge Text', 'happy-addons-pro'); ?>" id="ha-menu-badge-text-field" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="ha-menu-badge-color-field"><?php esc_html_e('Text Color', 'happy-addons-pro'); ?></label></th>
                        <td>
                            <input type="text" class="ha-menu-wpcolor-picker" value="#ffffff" id="ha-menu-badge-color-field" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="ha-menu-badge-background-field"><?php esc_html_e('Badge Background', 'happy-addons-pro'); ?></label></th>
                        <td>
                            <input type="text" class="ha-menu-wpcolor-picker" value="#bada55" id="ha-menu-badge-background-field" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row" class="w-170"><label for=""><?php esc_html_e('Badge Radius', 'happy-addons-pro'); ?></label></th>
                        <td>
                            <ul class="ha__control-dimensions">
                                <li class="elementor-control-dimension">
                                    <input id="ha-menu-badge-radius-topLeft" type="number" data-setting="topLeft" min="0">
                                    <label for="ha-menu-badge-radius-topLeft" class="elementor-control-dimension-label">T Left</label>
                                </li>
                                    <li class="elementor-control-dimension">
                                    <input id="ha-menu-badge-radius-topRight" type="number" data-setting="topRight" min="0">
                                    <label for="ha-menu-badge-radius-topRight" class="elementor-control-dimension-label">T Right</label>
                                </li>
                                <li class="elementor-control-dimension">
                                    <input id="ha-menu-badge-radius-bottomLeft" type="number" data-setting="bottomLeft" min="0">
                                    <label for="ha-menu-badge-radius-bottomLeft" class="elementor-control-dimension-label">B Left</label>
                                </li>
                                <li class="elementor-control-dimension">
                                    <input id="ha-menu-badge-radius-bottomRight" type="number" data-setting="bottomRight" min="0">
                                    <label for="ha-menu-badge-radius-bottomRight" class="elementor-control-dimension-label">B Right</label>
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="w-170"><label for=""><?php esc_html_e('Badge Preview', 'happy-addons-pro'); ?></label></th>
                        <td>
                            <div id="badge-preview-backdrop">
                                <div id="badge-preview"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="ha__tab-content">
            <div role="tabpanel" class="ha__tab-pane ha__active" id="attr_content_tab">
                <?php if(defined( 'ELEMENTOR_VERSION' )): ?>

                <div id="ha-menu-builder-warper">
                </div>
                <?php else: ?>
                <p class="no-elementor-notice">
                    <?php esc_html_e( 'This plugin requires Elementor page builder to edt megamenu items content', 'happy-addons-pro' ); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="ha__modal-footer">
        <input type="hidden" id="ha-menu-modal-menu-id">
        <input type="hidden" id="ha-menu-modal-menu-has-child">
        <span class='spinner'></span>
        <?php echo get_submit_button(esc_html__('Save', 'happy-addons-pro'), 'ha-menu-item-save aligncenter','', false); ?>
    </div>

</div>

<div class="modal" id="ha-menu-builder-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="ha__modal-dialog ha__modal-dialog-centered" role="document">
        <div class="ha__modal-content">
            <div class="ha__modal-body">
                <iframe id="ha-menu-builder-iframe" src="" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
