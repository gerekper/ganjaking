<?php

class CT_Ultimate_GDPR_Shortcode_Block_Cookies {

    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this,'assets'));
    }

    public function render(){ 

        $options = array_merge(
			CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )->get_default_options(),
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_options( CT_Ultimate_GDPR_Controller_Cookie::ID )
		);
        
        $title = $options['cookie_withdrawal_cookies_agreement'];
        $content = $options['cookie_withdrawal_cookies_agreement_description'];
        $bg_color = $options['cookie_withdrawal_cookies_agreement_button_bg_color'];
        $text_color = $options['cookie_withdrawal_cookies_agreement_button_text_color'];
        $border_color = $options['cookie_withdrawal_cookies_agreement_button_border_color'];


    ?>
<div id="ct-ultimate-gdpr-withdrawal-cookie-agreement">
    <h4><?php echo esc_html__( $title, 'ct-ultimate-gdpr' ); ?></h4>
    <form id="ct-ultimate-gdpr-block-cookies" action="#">
        <div class="ct-ultimate-gdpr-cookie checkbox">
            <label for="blockCookies" class="ct-ultimate-gdpr-block-cookies">
                <input type="checkbox" name="ct-ultimate-gdpr-block-cookies" id="blockCookies"
                    class="ct-ultimate-gdpr-block-cookies-checkbox"
                    <?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? 'checked' : ''; ?>>
                <p class="description"><?php echo esc_html__( $content, 'ct-ultimate-gdpr' ); ?></p>
            </label>
        </div>

        <input type="hidden" name="level" class="level"
            value="<?php echo (CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL == apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0)) ? CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL : CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING; ?>" />
        <input type="submit" name="ct-ultimate-gdpr-block-cookies-submit"
            value="<?php echo esc_html__( "Save", 'ct-ultimate-gdpr' ); ?>"
            style="background-color:<?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?> ;border:1px solid <?php echo esc_attr($border_color); ?>;"
            class="ct-ultimate-gdpr-cookie-block-btn button btn ct-btn" disabled>

    </form>
    <div class="notification"></div>

</div>
<?php }

    public function assets()
    {
        wp_enqueue_script( 'ct-ultimate-gdpr-shortcode-block-cookie', ct_ultimate_gdpr_url() . '/assets/js/shortcode-block-cookie.js', array( 'jquery' ), ct_ultimate_gdpr_get_plugin_version() );
    }

}

function block_cookies($atts){
    $obj = new CT_Ultimate_GDPR_Shortcode_Block_Cookies();
    ob_start();
    $obj->render();
    return ob_get_clean();
}

add_shortcode( 'block_cookies', 'block_cookies' );