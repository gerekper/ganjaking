<?php

use MailOptin\Connections\Init;

$connections                = $this->email_service_providers();
$mofm_settings              = $this->get_field_name('mofm_settings');
$integration_field_name     = $this->get_field_name('mofm_integration');
$list_field_name            = $this->get_field_name('mofm_list');
$tag_field_name             = $this->get_field_name('mofm_tags');
$mofm_is_double_optin       = $this->get_field_name('mofm_is_double_optin');
$mofm_custom_fields         = $this->get_field_name('mofm_custom_fields');
?>
<div class="mailoptin_list frm_grid_container">
    <?php if (isset($connections) && $connections) { ?>
    <p class="frm6">
        <label for="mofmSelectIntegration"><?php esc_html_e('Select Integration', 'mailoptin') ?>
            <span class="frm_required">*</span></label>
        <select name="<?php echo $integration_field_name ?>" id="mofmSelectIntegration">
            <?php foreach ($connections as $key => $value) { ?>
                <option value="<?php echo $key ?>" <?= selected($key, $saved_integration, false); ?>>
                    <?php echo FrmAppHelper::truncate($value, 40) ?>
                </option>
            <?php } ?>
        </select>
        <?php
        } else {
            esc_html_e('No MailOptin integration found', 'mailoptin');
        }
        ?>
    </p>
    <?php if ( ! empty($saved_integration)) { ?>
        <p class="frm6">
        <?php if ($saved_integration != 'leadbank') { ?>
            <label for="mofmSelectList"><?php esc_html_e('Select List', 'mailoptin') ?>
                <span class="frm_required">*</span></label>
            <select name="<?php echo $list_field_name; ?>" id="mofmSelectList">
                <option value=""><?= esc_html__('Select...', 'mailoptin'); ?></option>

                <?php if (is_array($lists) && ! empty($lists)) : ?>
                    <?php foreach ($lists as $key => $value) : ?>
                        <option value="<?= $key ?>" <?= selected($saved_list, $key, false); ?>><?= FrmAppHelper::truncate($value, 40) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            </p>

            <?php if(defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($saved_integration, Init::double_optin_support_connections(true))) { ?>
                <p class="frm6">
                    <label for="mofmDoubleOptin"><?php $default_double_optin === false ? esc_html_e('Enable Double Optin', 'mailoptin') : esc_html_e('Disable Double Optin', 'mailoptin') ?></label>
                    <input type="checkbox" id="mofmDoubleOptin" value="true" name="<?= $mofm_is_double_optin ?>" <?= checked($is_double_optin, "true", false); ?> /> <br />
                    <span class="description"><?= esc_html__('Double optin requires users to confirm their email address before they are added or subscribed.', 'mailoptin') ?></span>
                </p>
            <?php } ?>

            <?php if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($saved_integration, Init::select2_tag_connections())) { ?>
                <p class="frm6">
                    <label for="mofmSelectTags"><?php esc_html_e('Tags', 'mailoptin') ?>
                        <span class="frm_required">*</span></label>
                    <select class="moFmSelect2 frm_multiselect" name="<?php echo $tag_field_name; ?>[]" id="mofmSelectTags" multiple>
                        <?php if (is_array($tags) && ! empty($tags)) : ?>
                            <?php foreach ($tags as $key => $value) : ?>
                                <option value="<?= $key ?>" <?= @in_array($key, $saved_tags) ? 'selected="selected"' : ''; ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="description"><?= esc_html__('Select tags to assign to subscribers.', 'mailoptin') ?></span>
                </p>
                <?php
            }

            if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($saved_integration, Init::text_tag_connections())) { ?>
                <p class="frm6">
                    <label for="mofmSelectTags"><?php esc_html_e('Tags', 'mailoptin') ?>
                        <span class="frm_required">*</span></label>
                    <input id="mofmSelectTags" type="text" name="<?php echo $tag_field_name; ?> ?>" class="regular-text" size="70" value="<?= is_string($saved_tags) ? $saved_tags : ''; ?>">
                    <span class="description"><?= esc_html__('Enter a comma-separated list of tags to assign to subscribers.', 'mailoptin') ?></span>
                </p>
                <?php
            }
        }

        if (is_array($custom_fields) && ! empty($custom_fields)) :

            foreach ($custom_fields as $key => $value) :
                ?>
                <p class="frm6">

                    <label for="mofm_<?= $key ?>"><?php echo esc_html($value); ?>
                        <?php if ($key == 'moEmail') : ?>
                        <span class="frm_required">*</span></label>
                    <?php endif; ?>

                    <select name="<?php echo $mofm_custom_fields; ?>[<?php echo $key ?>]" id="mofm_<?= $key ?>">
                        <option value=""><?= esc_html__('Select...', 'mailoptin'); ?></option>
                        <?php
                        foreach ($form_fields as $form_field):
                            if ($key == 'moEmail' && ! in_array($form_field->type, array('email', 'hidden', 'user_id', 'text'))) continue;
                            $selected = (isset($post_content['mofm_custom_fields'][$key]) && $post_content['mofm_custom_fields'][$key] == $form_field->id) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo esc_attr($form_field->id) ?>" <?= $selected ?>><?= FrmAppHelper::truncate($form_field->name, 40) ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            <?php
            endforeach;
        endif;
    }

    if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) :
        $upgrade_url   = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=formidable_forms_builder_settings';
        $learnmore_url = 'https://mailoptin.io/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=formidable_forms_builder_settings';
        $output        = '<p>' . sprintf(esc_html__('Upgrade to %s to remove the 500 subscribers monthly limit, add support for custom field mapping and assign tags to subscribers.', 'mailoptin'), '<strong>MailOptin premium</strong>') . '</p>';
        $output        .= '<p><a href="' . $upgrade_url . '" style="margin-right: 10px;" class="button-primary" target="_blank">' . esc_html__('Upgrade to MailOptin Premium', 'mailoptin') . '</a>';
        $output        .= sprintf(esc_html__('%sLearn more about us%s', 'mailoptin'), '<a href="' . $learnmore_url . '" target="_blank">', '</a>') . '</p>';

        echo "<style>.mo-fm-upsell-block {background-color: #d9edf7;border: 1px solid #bce8f1;box-sizing: border-box;color: #31708f;outline: 0;padding: 5px 10px;}</style><div class='mo-fm-upsell-block'>$output</div>";
    endif;
    ?>
</div>