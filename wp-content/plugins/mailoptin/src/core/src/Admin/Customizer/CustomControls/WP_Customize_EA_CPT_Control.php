<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use MailOptin\Core\Repositories\EmailCampaignRepository;
use WP_Customize_Control;

class WP_Customize_EA_CPT_Control extends WP_Customize_Control
{
    use WP_Customize_EA_CPT_Control_Trait;

    public $type = 'mailoptin_ea_cpt';

    public function enqueue()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_style('mailoptin-customizer-chosen', MAILOPTIN_ASSETS_URL . 'chosen/chosen.min.css');
        wp_enqueue_script('mailoptin-customizer-chosen', MAILOPTIN_ASSETS_URL . 'chosen/chosen.jquery.min.js', array('jquery'), MAILOPTIN_VERSION_NUMBER);
        wp_enqueue_script('mailoptin-customizer-chosen-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/chosen.js', array('jquery', 'mailoptin-customizer-chosen'), MAILOPTIN_VERSION_NUMBER);
        wp_enqueue_script('mailoptin-ea-cpt-settings-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/ea-cpt-settings-control.js', array('jquery'), MAILOPTIN_VERSION_NUMBER);
    }

    public function render_content()
    {
        $email_campaign_id = absint($_GET['mailoptin_email_campaign_id']);
        $custom_post_type  = EmailCampaignRepository::get_merged_customizer_value($email_campaign_id, 'custom_post_type');

        $saved_value = json_decode($this->value(), true);
        echo '<div class="mo-ea-cpt-setting-container" style="margin-bottom: 10px">';

        $this->render_fields($custom_post_type, $saved_value);
        echo '</div>';
        ?>
        <input class="mo-ea-cpt-control" id="<?= '_customize-input-' . $this->id; ?>" type="hidden" <?php $this->link(); ?>/>

        <?php if ( ! empty($this->description)) : ?>
        <span class="description customize-control-description"><?php echo $this->description; ?></span>
    <?php endif;
    }
}