<?php
if( !defined( 'ABSPATH' ) )
    exit;
?>
<div class="wrap">
    <h2><?php _e( 'Export Data', 'yith-woocommerce-surveys' ); ?></h2>

    <div class="exportation-survey-settings">
        <form id="survey-export" method="post">
            <table class="form-table">
                <input type="hidden" name="survey_export" value="1">

                <tbody>

                <tr valign="top" class="">
                    <th scope="row" class="titledesc"><?php _e( 'Choose data to export', 'yith-woocommerce-surveys' ); ?></th>
                    <td class="forminp forminp-checkbox">
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e( 'Choose data to export', 'yith-woocommerce-surveys' ); ?></span>
                            </legend>
                            <label for="survey_checkout_type">
                                <input name="survey_checkout_type" id="survey_checkout_type" type="checkbox" value="1"
                                       checked="checked" class="export-items"><?php _e( 'Surveys in Checkout', 'yith-woocommerce-surveys' ); ?></label>
                        </fieldset>
                        <fieldset class="">
                            <legend class="screen-reader-text">
                                <span><?php _e( 'Choose data to export', 'yith-woocommerce-surveys' ); ?></span>
                            </legend>
                            <label for="survey_product_type">
                                <input name="survey_product_type" id="survey_product_type" type="checkbox" value="1"
                                       checked="checked" class="export-items"><?php _e( 'Surveys in Product', 'yith-woocommerce-surveys' ); ?>
                            </label></fieldset>
                        <fieldset class="">
                            <legend class="screen-reader-text">
                                <span><?php _e( 'Choose data to export', 'yith-woocommerce-surveys' ); ?></span>
                            </legend>
                            <label for="survey_other_type">
                                <input name="survey_other_type" id="survey_other_type" type="checkbox" value="1"
                                       checked="checked" class="export-items"><?php _e( 'Surveys in other pages', 'yith-woocommerce-surveys' );
                                ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                    </th>
                    <td class="forminp forminp-color plugin-option">
                        <input type="submit" value="<?php _e( 'Confirm', 'yith-woocommerce-surveys' ); ?>" id="start-now"
                               class="button-primary" name="submit">
                    </td>
                </tr>

                </tbody>
            </table>
        </form>
    </div>