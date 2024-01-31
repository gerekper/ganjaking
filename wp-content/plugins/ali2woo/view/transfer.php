<div class="a2w-content">
    <h1><?php _e('Transfer settings', 'ali2woo'); ?></h1>
    <form class="a2w-transfer" method="post">
        <input type="hidden" name="transfer_form" value="1">
        <div class="a2w-transfer__row">
            <div class="field field_default field_inline">
                <div class="field__label"><?php echo _e('Paste a hash from another site to update settings', 'ali2woo'); ?></div>
                <div class="field__input-wrap">
                    <textarea class="field__input" name="hash" placeholder="<?php echo _e('Paste your hash', 'ali2woo'); ?>" rows="10"><?php
                        echo esc_html($hash);
                        ?></textarea>
                </div>
            </div>
        </div>
        <div class="a2w-transfer__buttons">
            <button class="btn btn-success"><?php echo __('Update Settings', 'ali2woo'); ?></button>
        </div>
    </form>
</div>