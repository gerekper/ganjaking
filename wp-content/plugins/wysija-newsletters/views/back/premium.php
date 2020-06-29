<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_premium extends WYSIJA_view_back
{
    function __construct()
    {
        $this->skip_header = true;
    }

    function defaultDisplay($data)
    {
        $helper_licence = WYSIJA::get('licence', 'helper');
        $url_checkout = $helper_licence->get_url_checkout('a_buy_now');

        ?>
        <div class="wrap about-wrap">
            <div class="feature-section one-col" style="text-align:center;">
                <h2><?php _e('Looking for Premium?', WYSIJA) ?></h2>
                <p class="lead-description"><?php _e('So you know, MailPoet has a completely new version...', WYSIJA) ?></p>
                <p style="text-align: justify;"><?php _e('The MailPoet team will continue to support the Premium users of version 2 (the one you are using now) until the spring of 2019. A purchase of the Premium for version 2 will be valid for Premium version 3.', WYSIJA) ?></p>
                <div style="margin:0 auto;">
                    <ol style="display:inline-block; text-align: left;">
                        <li><?php echo str_replace(array('[link]', '[/link]'), array('<a href="http://www.mailpoet.com/faq-mailpoet-version-2/" target="_blank">', '</a>'), __('[link]Read the FAQ on MailPoet version 2[/link] (your version)', WYSIJA)); ?></li>
                        <li><?php echo str_replace(array('[link]', '[/link]'), array('<a href="http://www.mailpoet.com/support/sales-pre-sales-questions/" target="_blank">', '</a>'), __('Got a sales question? [link]Get in touch[/link]', WYSIJA)); ?></li>
                        <li><?php echo str_replace(array('[link]', '[/link]'), array('<a href="http://www.mailpoet.com/features/features-list/" target="_blank">', '</a>'), __('See Premium version 2 [link]features list[/link]', WYSIJA)); ?></li>
                    </ol>
                </div>
                <a class="buy-button" target="_blank" href="<?php echo $url_checkout; ?>"><span><?php _e('Yes, I want to purchase Premium version 2', WYSIJA) ?></span></a>

            </div>
            <div class="wysija-premium-actions-kim">
                <?php echo $this->messages(); ?>
                <input type="hidden" value="faed9c414f" id="wysijax">
                <p><?php _e('Already paid?', WYSIJA) ?> <a class="button-primary wysija-premium-activate" href="javascript:;"><?php _e('Activate now', WYSIJA) ?></a></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
    }
}
