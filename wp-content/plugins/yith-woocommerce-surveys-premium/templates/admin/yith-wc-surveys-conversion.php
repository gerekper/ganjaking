<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
    .ui-progressbar {
        position: relative;
    }
    .progress-label {
        position: absolute;
        left: 50%;
        top: 4px;
        font-weight: bold;
        text-shadow: 1px 1px 0 #fff;
    }
</style>
<div id="yith_wc_surveys_panel_conversion" class="yith-plugin-fw  yit-admin-panel-container">
    <div class="yit-admin-panel-content-wrap">
        <form id="plugin-fw-wc" method="post">
            <table class="form-table">
                <h2><?php _e( 'Conversion Tool', 'yith-woocommerce-surveys' ); ?></h2>
                <tbody>
                <tr valign="top" class="yith-plugin-fw-panel-wc-row buttons">
                    <th scope="row" class="titledesc">
                        <label for="yith_survey_convert"><?php _e('Convert', 'yith-woocommerce-surveys' );?></label>
                    </th>
                    <td class="forminp frominp-buttons">
                        <input id="yith_survey_convert" type="button" class="button button-primary"
                               value="<?php _e( 'Convert!', 'yith-woocommerce-surveys' ); ?>">
                        <span class="description"><?php _e( 'From version 1.1.0 the method to save answers of the surveys showed on the checkout page has changed. Please complete the conversion procedure', 'yith-woocommerce-surveys' ); ?></span>
                        <input type="hidden" id="yith_survey_conversion_nonce" value="<?php echo wp_create_nonce( 'yith-survey-conversion-nonce');?>">
                    </td>
                </tr>

                <tr valign="top" style="display: none;">
                    <td class="forminp forminp-progressbar" colspan="2">
                        <div id="yith_survey_convert_progressbar">
                            <div class="yith_survey_progessbar_label progress-label"></div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
