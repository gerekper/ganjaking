<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;


class WP_Customize_Submit_Button_Control extends \WP_Customize_Control
{
    public $type = 'submit_button';

    /**
     * Render the control's content.
     */
    public function render_content()
    { ?>
        <label>
            <input id="mailoptin-send-test-email-nonce" type="hidden" value="<?php echo wp_create_nonce('mailoptin-send-test-email-nonce');?>">
            <button class="button button-primary" id="mailoptin-send-mail" tabindex="0">
                <?php _e('Send', 'mailoptin'); ?>
            </button>
            <img id="mailoptin-spinner" src="<?php echo admin_url('images/spinner.gif'); ?>" style="display:none;"/>
            <span id="mailoptin-success" style="display:none;"><?php _e('Email sent. Go check your message.', 'mailoptin'); ?></span>
            <?php if ( ! empty( $this->description )) : ?>
                <p><span class="description customize-control-description"><?php echo $this->description; ?></span></p>
            <?php endif; ?>
        </label>
        <?php
    }
}