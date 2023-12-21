<?php

use Happy_Addons\Elementor\Theme_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$types    = Theme_Builder::TEMPLATE_TYPE;
$selected = get_query_var( 'ha_library_type' );

?>

<script type="text/template" id="tmpl-modal-template-condition">
    <div class="modal micromodal-slide modal-template-condition ha-template-element-modal" id="modal-new-template-condition" aria-hidden="false">
        <div class="modal__overlay" tabindex="-1">
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-login-title">
                <header class="modal__header">
                    <h3 class="modal__title" id="modal-2-title">
                        Template Elements Condition
                    </h3>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close=""></button>
                </header>
                <div class="modal__content" id="modal-2-content">
                    <div class="form-data">
                        <div class="modal__information">
                            <div class="info-title">Where do you want to display your template?</div>
                            <div class="info-message">
                                Set the conditions that determine where your Template is used throughout your site.
                                <br>
                                For example, choose 'Entire Site' to display the template across your site.
                            </div>
                        </div>
                        <p class="ha-template-notice"></p>
                        <form id="ha-template-edit-form">
                            <div class="ha-template-condition-wrap"></div>
                            <button class="ha-cond-repeater-add" type="button">+ Add Condition</button>
                        </form>
                    </div>
                </div>
                <footer class="modal__footer">
                    <button class="modal__close modal__btn" aria-label="Close modal" data-micromodal-close="">Cancel</button>
                    <button id="ha-template-save-data" class="modal__btn modal__btn-primary">Save & Close</button>
                </footer>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-elementor-new-template">
    <div id="ha-template-condition-item-{{uniqeID}}" class="ha-template-condition-item">
        <div class="ha-template-condition-item-row">
            <div class="ha-tce-type">
                <select data-id="type-{{uniqeID}}" data-parent="{{uniqeID}}" data-setting="type">
                    <option value="include">Include</option>
                    <option value="exclude">Exclude</option>
                </select>
            </div>
            <div class="ha-tce-name">
                <select data-id="name-{{uniqeID}}" data-parent="{{uniqeID}}" data-setting="name">
                    <optgroup label="General">
                        <option value="general">Entire Site</option>
                        <option value="archive">Archives</option>
                        <option value="singular">Singular</option>
                    </optgroup>
                </select>
            </div>
            <div class="ha-tce-sub_name" style="display:none">
                <select data-id="sub_name-{{uniqeID}}" data-parent="{{uniqeID}}" data-setting="sub_name">
                </select>
            </div>
            <div class="ha-tce-sub_id" style="display:none">
                <select data-id="sub_id-{{uniqeID}}" data-parent="{{uniqeID}}" data-setting="sub_id">
                </select>
            </div>
        </div>
        <div class="ha-template-condition-remove">
            <i class="eicon-trash" aria-hidden="true"></i>
            <span class="elementor-screen-only">Remove this item</span>
        </div>
    </div>
</script>