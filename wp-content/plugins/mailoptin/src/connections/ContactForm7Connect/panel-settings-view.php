<?php

use MailOptin\Connections\Init;
use function MailOptin\Core\moVar;

?>
<style>
    #contact-form-editor #mailoptin .form-table th {
        width: 200px;
    }
    #contact-form-editor #mailoptin .mo-cf7-upsell-block {
        background-color: #d9edf7;
        border: 1px solid #bce8f1;
        box-sizing: border-box;
        color: #31708f;
        outline: 0;
        padding: 15px 10px;
    }

    #contact-form-editor #mailoptin p {
        margin: 0 0 10px 0;
    }
</style>
<table class="form-table">
    <tbody>
    <tr>
        <th scope="row">
            <label for="mocf7SelectIntegration"><?= esc_html__('Select Integration', 'mailoptin') ?></label>
        </th>
        <td>
            <select id="mocf7SelectIntegration" name="mocf7_settings[integration]" style="width: 25em;">
                <?php foreach ($connections as $key => $value) : ?>
                    <option value="<?= $key ?>" <?= selected($key, $saved_integration, false); ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php if ( ! empty($saved_integration)) : ?>

        <?php if ($saved_integration != 'leadbank') : ?>
            <tr>
                <th scope="row">
                    <label for="mocf7SelectList"><?= esc_html__('Select List', 'mailoptin') ?></label>
                </th>
                <td>
                    <select id="mocf7SelectList" name="mocf7_settings[list]" style="width: 25em;">
                        <option value=""><?= esc_html__('Select...', 'mailoptin'); ?></option>
                        <?php foreach ($lists as $key => $value) : ?>
                            <option value="<?= $key ?>" <?= selected($saved_list, $key, false); ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <?php if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($saved_integration, Init::double_optin_support_connections(true))) : ?>
                <tr>
                    <th scope="row">
                        <label for="mocf7DoubleOptin"><?= $default_double_optin === false ? esc_html__('Enable Double Optin', 'mailoptin') : esc_html__('Disable Double Optin', 'mailoptin') ?></label>
                    </th>
                    <td>
                        <input id="mocf7DoubleOptin" type="checkbox" value="true" name="mocf7_settings[is_double_optin]" <?= checked($saved_double_optin, "true", false); ?> />
                        <span class="description"><?= esc_html__('Double optin requires users to confirm their email address before they are added or subscribed.', 'mailoptin') ?></span>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($saved_integration, Init::select2_tag_connections())) : ?>
                <tr>
                    <th scope="row">
                        <label for="mocf7Tags"><?= esc_html__('Tags', 'mailoptin') ?></label>
                    </th>
                    <td>
                        <select class="mocf7Tags" id="mocf7Tags" name="mocf7_settings[tags][]" multiple style="width: 25em;">
                            <?php foreach ($tags as $key => $value) : ?>
                                <option value="<?= $key ?>" <?= @in_array($key, $saved_tags) ? 'selected="selected"' : ''; ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?= esc_html__('Select tags to assign to subscribers.', 'mailoptin') ?></p>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($saved_integration, Init::text_tag_connections())) : ?>
                <tr>
                    <th scope="row">
                        <label for="mocf7Tags"><?= esc_html__('Tags', 'mailoptin') ?></label>
                    </th>
                    <td>
                        <input id="mocf7Tags" type="text" name="mocf7_settings[tags]" class="regular-text" size="70" value="<?= is_string($saved_tags) ? $saved_tags : ''; ?>">
                        <p class="description"><?= esc_html__('Enter a comma-separated list of tags to assign to subscribers.', 'mailoptin') ?></p>

                    </td>
                </tr>
            <?php endif; ?>

        <?php endif; ?>
        <tr>
            <th scope="row">
                <label><?= esc_html__('Field Mapping', 'mailoptin') ?></label>
            </th>
            <td style="padding:0 5px;">
                <table class="form-table" cellspacing="0" style="margin-top:0;">
                    <tbody>
                    <?php foreach ($custom_fields as $key => $value): ?>
                        <?php $cf7_form_tags = $this->form_tags($contact_form); ?>
                        <tr>
                            <td scope="row" style="width: 200px;">
                                <label for="mocf_<?= $key ?>"><?= $value ?></label></td>
                            <td>
                                <?php if ($key == 'moEmail') $cf7_form_tags = $this->form_tags($contact_form, 'email'); ?>
                                <select name="mocf7_settings[custom_fields][<?= $key ?>]" id="mocf_<?= $key ?>" style="width: 25em;">
                                    <?php foreach ($cf7_form_tags as $cf7_form_tag_key => $cf7_form_tag_value): ?>
                                        <option value="<?= $cf7_form_tag_key ?>" <?= selected($cf7_form_tag_key, moVar($mapped_custom_fields, $key), false) ?>><?= $cf7_form_tag_value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="mocf7SelectList"><?= esc_html__('Require Acceptance', 'mailoptin') ?></label>
            </th>
            <td>
                <select name="mocf7_settings[require_acceptance]" id="mocf_<?= $key ?>" style="width: 25em;">
                    <?php foreach ($this->form_tags($contact_form, 'acceptance') as $key => $value): ?>
                        <option value="<?= $key ?>" <?= selected($key, $saved_require_acceptance, false) ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?= esc_html__('Select acceptance field that must be checked before this integration is processed. This is optional.', 'mailoptin') ?></p>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) :
        $upgrade_url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cf7_builder_settings';
        $learnmore_url = 'https://mailoptin.io/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cf7_builder_settings';
        $output = '<p>' . sprintf(esc_html__('Upgrade to %s to remove the 500 subscribers monthly, add support for custom field mapping and assign tags to subscribers.', 'mailoptin'), '<strong>MailOptin premium</strong>') . '</p>';
        $output .= '<p><a href="' . $upgrade_url . '" style="margin-right: 10px;" class="button-primary" target="_blank">' . esc_html__('Upgrade to MailOptin Premium', 'mailoptin') . '</a>';
        $output .= sprintf(esc_html__('%sLearn more about us%s', 'mailoptin'), '<a href="' . $learnmore_url . '" target="_blank">', '</a>') . '</p>';
        ?>
        <tr>
            <th scope="row"></th>
            <td>
                <div class="mo-cf7-upsell-block"><?= $output ?></div>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>