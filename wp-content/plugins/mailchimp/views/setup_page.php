<div class="wrap">

    <div class="mailchimp-header">
        <h2><?php esc_html_e('MailChimp List Setup', 'mailchimp_i18n');?> </h2>
    </div>
<?php

$user = get_option('mc_user');
/* TODO MC SOPRESTO USER INFO */

// If we have an API Key, see if we need to change the lists and its options
mailchimpSF_change_list_if_necessary();

// Display our success/error message(s) if have them
if (mailchimpSF_global_msg() != ''){
    // Message has already been html escaped, so we don't want to 2x escape it here
    ?>
    <div id="mc_message" class=""><?php echo mailchimpSF_global_msg(); ?></div>
    <?php
}

// If we don't have an API Key, do a login form
if (!$user || !get_option('mc_api_key')) {
?>
    <div>
        <h3 class="mc-h2"><?php esc_html_e('Log In', 'mailchimp_i18n');?></h3>
        <p class="mc-p" style="width: 40%;line-height: 21px;"><?php echo __('To get started, we’ll need to access your MailChimp account with an <a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys">API Key</a>. Paste your MailChimp API key, and click <strong>Connect</strong> to continue.
', 'mailchimp_i18n'); ?></p>
        <p class="mc-a">
            <?php
            echo sprintf(
                '%1$s <a href="http://www.mailchimp.com/signup/" target="_blank">%2$s</a>',
                esc_html(__("Don't have a MailChimp account?", 'mailchimp_i18n')),
                esc_html(__('Try one for Free!', 'mailchimp_i18n'))
            );
            ?>
        </p>
        
        <div style="width: 900px;">
            <table class="widefat mc-widefat mc-api">
            <form method="POST" action="">
                <tr valign="top">
                    <th scope="row" class="mailchimp-connect"><?php esc_html_e('Connect to MailChimp', 'mailchimp_i18n'); ?></th>
                    <td>
                        <input type="hidden" name="mcsf_action" value="login"/>
                        <input type="password" name="mailchimpSF_api_key" placeholder="API Key">
                    </td>
                    <td>
                        <input type="submit" value="Connnect">
                    </td>
                </tr>
            </form>
            </table>
        </div>
    </div>

    <br/>
    <?php
    if ($user && $user['username'] != '') {
        ?>
<!--<div class="notes_msg">
        <strong><?php esc_html_e('Notes', 'mailchimp_i18n'); ?>:</strong>
        <ul>
            <li><?php esc_html_e('Changing your settings at MailChimp.com may cause this to stop working.', 'mailchimp_i18n'); ?></li>
            <li><?php esc_html_e('If you change your login to a different account, the info you have setup below will be erased.', 'mailchimp_i18n'); ?></li>
            <li><?php esc_html_e('If any of that happens, no biggie - just reconfigure your login and the items below...', 'mailchimp_i18n'); ?></li>
        </ul>
</div>-->
        <?php
    }
} // End of login form

// Start logout form
else {
?>
<table style="min-width:400px;" class="mc-user" cellspacing="0">
    <tr>
        <td><h3><?php esc_html_e('Logged in as', 'mailchimp_i18n');?>: <?php echo esc_html($user['username']); 
        ?></h3>
        </td>
        <td>
            <form method="post" action="">
                <input type="hidden" name="mcsf_action" value="logout"/>
                <input type="submit" name="Submit" value="<?php esc_attr_e('Logout', 'mailchimp_i18n');?>" class="button" />
                <?php wp_nonce_field('mc_logout', '_mcsf_nonce_action'); ?>
            </form>
        </td>
    </tr>
</table>
<?php
} // End Logout form

//Just get out if nothing else matters...
$api = mailchimpSF_get_api();
if (!$api) { return; }

if ($api){
    ?>
    <h3 class="mc-h2"><?php esc_html_e('Your Lists', 'mailchimp_i18n'); ?></h3>

<div>

    <p class="mc-p"><?php esc_html_e('Please select the MailChimp list you’d like to connect to your form.', 'mailchimp_i18n'); ?></p>
    <p class="mc-list-note"><strong><?php esc_html_e('Note:', 'mailchimp_i18n'); ?></strong> <?php esc_html_e('Updating your list will not remove list settings in this plugin, but changing lists will.', 'mailchimp_i18n'); ?></p>

    <form method="post" action="options-general.php?page=mailchimpSF_options">
        <?php
        //we *could* support paging, but few users have that many lists (and shouldn't)
        $lists = $api->get('lists', 100, array('fields' => 'lists.id,lists.name,lists.email_type_option'));
        $lists = $lists['lists'];

        if (count($lists) == 0) {
            ?>
            <span class='error_msg'>
                <?php
                echo sprintf(
                    esc_html(__("Uh-oh, you don't have any lists defined! Please visit %s, login, and setup a list before using this tool!", 'mailchimp_i18n')),
                    "<a href='http://www.mailchimp.com/'>MailChimp</a>"
                );
                ?>
            </span>
            <?php
        }
        else {
            ?>
        <table style="min-width:400px" class="mc-list-select" cellspacing="0">
            <tr class="mc-list-row">
                <td>
                    <select name="mc_list_id" style="min-width:200px;">
                        <option value=""> &mdash; <?php esc_html_e('Select A List','mailchimp_i18n'); ?> &mdash; </option>
                        <?php
                        foreach ($lists as $list) {
                            $option = get_option('mc_list_id');
                            ?>
                            <option value="<?php echo esc_attr($list['id']); ?>"<?php selected($list['id'], $option); ?>><?php echo esc_html($list['name']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="mcsf_action" value="update_mc_list_id" />
                    <input type="submit" name="Submit" value="<?php esc_attr_e('Update List', 'mailchimp_i18n'); ?>" class="button" />
                </td>
            </tr>
        </table>
            <?php
        } //end select list
        ?>
    </form>
</div>

<br/>

<?php
}
else {
//display the selected list...
?>

<p class="submit">
    <form method="post" action="options-general.php?page=mailchimpSF_options">
        <input type="hidden" name="mcsf_action" value="reset_list" />
        <input type="submit" name="reset_list" value="<?php esc_attr_e('Reset List Options and Select again', 'mailchimp_i18n'); ?>" class="button" />
        <?php wp_nonce_field('reset_mailchimp_list', '_mcsf_nonce_action'); ?>
    </form>
</p>
<h3><?php esc_html_e('Subscribe Form Widget Settings for this List', 'mailchimp_i18n'); ?>:</h3>
<h4><?php esc_html_e('Selected MailChimp List', 'mailchimp_i18n'); ?>: <?php echo esc_html(get_option('mc_list_name')); ?></h4>
<?php
}

//Just get out if nothing else matters...
if (get_option('mc_list_id') == '') return;


// The main Settings form
?>

<div>
<form method="post" action="options-general.php?page=mailchimpSF_options">
<div style="width:900px;">
<input type="hidden" name="mcsf_action" value="change_form_settings">
<?php wp_nonce_field('update_general_form_settings', '_mcsf_nonce_action'); ?>

<table class="widefat mc-widefat mc-label-options">
    <tr><th colspan="2">Content Options</th></tr>
    <tr valign="top">
        <th scope="row"><?php esc_html_e('Header', 'mailchimp_i18n'); ?></th>
        <td>
            <textarea name="mc_header_content" rows="2" cols="70"><?php echo esc_html(get_option('mc_header_content')); ?></textarea><br/>
            <?php esc_html_e('Add your own text, HTML markup (including image links), or keep it blank.', 'mailchimp_i18n'); ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php esc_html_e('Sub-header', 'mailchimp_i18n'); ?></th>
        <td>
            <textarea name="mc_subheader_content" rows="2" cols="70"><?php echo esc_html(get_option('mc_subheader_content')); ?></textarea><br/>
            <?php esc_html_e('Add your own text, HTML markup (including image links), or keep it blank.', 'mailchimp_i18n'); ?>.<br/>
            <?php esc_html_e('This will be displayed under the heading and above the form.', 'mailchimp_i18n'); ?>
        </td>
    </tr>

    <tr valign="top" class="last-row">
    <th scope="row"><?php esc_html_e('Submit Button', 'mailchimp_i18n'); ?></th>
    <td>
    <input type="text" name="mc_submit_text" size="70" value="<?php echo esc_attr(get_option('mc_submit_text')); ?>"/>
    </td>
    </tr>
</table>

<input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button mc-submit" /><br/>

<table class="widefat mc-widefat mc-nuke-styling">
<tr><th colspan="2">Remove MailChimp CSS</th></tr>
<tr class="mc-internal-heading"><th><label for="mc_nuke_all_styles"><?php esc_html_e('Remove CSS');?></label></th><td><span class="mc-pre-input"></span><input type="checkbox" name="mc_nuke_all_styles" id="mc_nuke_all_styles" <?php checked(get_option('mc_nuke_all_styles'), true);?> onclick="showMe('mc-custom-styling')"/><?php esc_html_e('This will disable all MailChimp CSS, so it’s recommended for WordPress experts only.');?></td></tr>
</table>
<?php if(get_option('mc_nuke_all_styles') == true) {
    ?>
    <table class="widefat mc-widefat mc-custom-styling" id="mc-custom-styling" style="display:none">
    <?php } else {?>
        <table class="widefat mc-widefat mc-custom-styling" id="mc-custom-styling">
    <?php } ?>
    <tr><th colspan="2">Custom Styling</th></tr>
    <tr class="mc-turned-on"><th><label for="mc_custom_style"><?php esc_html_e('Enabled?', 'mailchimp_i18n'); ?></label></th><td><span class="mc-pre-input"></span><input type="checkbox" name="mc_custom_style" id="mc_custom_style"<?php checked(get_option('mc_custom_style'), 'on'); ?> /><?php esc_html_e('Edit the default MailChimp CSS style.');?></td></tr>

    <tr><th><?php esc_html_e('Border Width (px)', 'mailchimp_i18n'); ?></th><td><span class="mc-pre-input"></span><input type="text" name="mc_form_border_width" size="3" maxlength="3" value="<?php echo esc_attr(get_option('mc_form_border_width')); ?>"/>
        <em><?php esc_html_e('Set to 0 for no border, do not enter', 'mailchimp_i18n'); ?> px</em>
    </td></tr>
    <tr><th><?php esc_html_e('Border Color', 'mailchimp_i18n'); ?></th><td><span class="mc-pre-input">#</span><input type="text" name="mc_form_border_color" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_form_border_color')); ?>"/>
        <em><?php esc_html_e('Do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
    </td></tr>
    <tr><th><?php esc_html_e('Text Color', 'mailchimp_i18n'); ?></th><td><span class="mc-pre-input">#</span><input type="text" name="mc_form_text_color" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_form_text_color')); ?>"/>
        <em><?php esc_html_e('Do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
    </td></tr>
    <tr class="last-row"><th><?php esc_html_e('Background Color', 'mailchimp_i18n'); ?></th><td><span class="mc-pre-input">#</span><input type="text" name="mc_form_background" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_form_background')); ?>"/>
        <em><?php esc_html_e('Do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
    </td></tr>
</table>

<input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button mc-submit" /><br/>


<table class="widefat mc-widefat">
    <tr><th colspan="2">List Options</th></tr>
    <tr valign="top">
        <th scope="row"><?php esc_html_e('MonkeyRewards', 'mailchimp_i18n'); ?>?</th>
        <td><input name="mc_rewards" type="checkbox"<?php if (get_option('mc_rewards')=='on' || get_option('mc_rewards')=='' ) { echo ' checked="checked"'; } ?> id="mc_rewards" class="code" />
            <em><label for="mc_rewards"><?php echo __('We’ll add a "powered by MailChimp" link to your form that will help you earn <a href="http://kb.mailchimp.com/accounts/account-setup/monkeyrewards-credits" target="_blank">MonkeyRewards</a>. You can turn it off at any time.', 'mailchimp_i18n'); ?></label></em>
        </td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><?php esc_html_e('Use JavaScript Support?', 'mailchimp_i18n'); ?></th>
        <td><input name="mc_use_javascript" type="checkbox" <?php checked(get_option('mc_use_javascript'), 'on'); ?> id="mc_use_javascript" class="code" />
            <em><label for="mc_use_javascript"><?php esc_html_e('This plugin uses JavaScript submission, and it should degrade gracefully for users not using JavaScript. It is optional, and you can turn it off at any time.', 'mailchimp_i18n'); ?></label></em>
        </td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><?php esc_html_e('Use JavaScript Datepicker?', 'mailchimp_i18n'); ?></th>
        <td><input name="mc_use_datepicker" type="checkbox" <?php checked(get_option('mc_use_datepicker'), 'on'); ?> id="mc_use_datepicker" class="code" />
            <em><label for="mc_use_datepicker"><?php esc_html_e('We’ll use the jQuery UI Datepicker for dates.', 'mailchimp_i18n'); ?></label></em>
        </td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><?php esc_html_e('Use Double Opt-In (Recommended)?', 'mailchimp_i18n'); ?></th>
        <td><input name="mc_double_optin" type="checkbox" <?php checked(get_option('mc_double_optin'), true); ?> id="mc_double_optin" class="code" />
            <em><label for="mc_double_optin"><?php esc_html_e('Before new your subscribers are added via the plugin, they\'ll need to confirm their email address.', 'mailchimp_i18n'); ?></label></em>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php esc_html_e('Update existing subscribers?', 'mailchimp_i18n'); ?></th>
        <td><input name="mc_update_existing" type="checkbox" <?php checked(get_option('mc_update_existing'), true); ?> id="mc_update_existing" class="code" />
            <em><label for="mc_double_optin"><?php esc_html_e('If an existing subscriber fills out this form, we will update their information with what\'s provided.', 'mailchimp_i18n'); ?></label></em>
        </td>
    </tr>
    
    <tr valign="top" class="last-row">
        <th scope="row"><?php esc_html_e('Include Unsubscribe link?', 'mailchimp_i18n'); ?></th>
        <td><input name="mc_use_unsub_link" type="checkbox"<?php checked(get_option('mc_use_unsub_link'), 'on'); ?> id="mc_use_unsub_link" class="code" />
            <em><label for="mc_use_unsub_link"><?php esc_html_e('We’ll automatically  add a link to your list’s unsubscribe form.', 'mailchimp_i18n'); ?></label></em>
        </td>
    </tr>
</table>

</div>

<div style="width:900px;">

    <input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button mc-submit" /><br/>

    <table class='widefat mc-widefat'>
        <tr>
            <th colspan="4">
                <?php esc_html_e('Merge Fields Included', 'mailchimp_i18n'); ?>

                <?php
                $mv = get_option('mc_merge_vars');
                
                if (count($mv) == 0 || !is_array($mv)){
                    ?>
                    <em><?php esc_html_e('No Merge Fields found.', 'mailchimp_i18n'); ?></em>
                    <?php
                } else {
                    ?>
            </th>
        </tr>
        <tr valign="top">
            <th><?php esc_html_e('Name', 'mailchimp_i18n');?></th>
            <th><?php esc_html_e('Tag', 'mailchimp_i18n');?></th>
            <th><?php esc_html_e('Required?', 'mailchimp_i18n');?></th>
            <th><?php esc_html_e('Include?', 'mailchimp_i18n');?></th>
        </tr>
    <?php
    foreach($mv as $var){
        ?>
        <tr valign="top">
            <td><?php echo esc_html($var['name']); ?></td>
            <td><?php echo esc_html($var['tag']); ?></td>
            <td><?php echo esc_html(($var['required'] == 1) ? 'Y' : 'N'); ?></td>
            <td>
                <?php
                if (!$var['required']){
                    $opt = 'mc_mv_'.$var['tag'];
                    ?>
                    <input name="<?php echo esc_attr($opt); ?>" type="checkbox" id="<?php echo esc_attr($opt); ?>" class="code"<?php checked(get_option($opt), 'on'); ?> />
                    <?php
                } else {
                    ?>
                    &nbsp;&mdash;&nbsp;
                    <?php
                }
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
    </table>
    <input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button mc-submit" /><br/>
</div>

<?php
    // Interest Groups Table
    $igs = get_option('mc_interest_groups');
    if (is_array($igs) && !isset($igs['id'])) { ?>
        <h3 class="mc-h3"><?php esc_html_e('Group Settings', 'mailchimp_i18n'); ?></h3> <?php
        // Determines whether or not to continue processing. Only false if there was an error.
        $continue = true;
        foreach ($igs as $ig) {
            if ($continue) {
                if (!is_array($ig) || empty($ig) || $ig == 'N' ) {
                ?>
            <em><?php esc_html_e('No Interest Groups Setup for this List', 'mailchimp_i18n'); ?></em>
                <?php
                    $continue = false;
                }
                else {
                ?>
            <table class='mc-widefat mc-blue' width="450px" cellspacing="0">
                <tr valign="top">
                    <th colspan="2"><?php echo esc_html($ig['title']); ?></th>
                </tr>
                <tr valign="top">
                    <th>
                        <label for="<?php echo esc_attr('mc_show_interest_groups_'.$ig['id']); ?>"><?php esc_html_e('Show?', 'mailchimp_i18n'); ?></label>
                    </th>
                    <th>
                        <input name="<?php echo esc_attr('mc_show_interest_groups_'.$ig['id']); ?>" id="<?php echo esc_attr('mc_show_interest_groups_'.$ig['id']); ?>" type="checkbox" class="code"<?php checked('on', get_option('mc_show_interest_groups_'.$ig['id'])); ?> />
                    </th>
                </tr>
                <tr valign="top">
                    <th><?php esc_html_e('Input Type', 'mailchimp_i18n'); ?></th>
                    <td><?php echo esc_html($ig['type']); ?></td>
                </tr>
                <tr valign="top" class="last-row">
                    <th><?php esc_html_e('Options', 'mailchimp_i18n'); ?></th>
                    <td>
                        <ul>
                        <?php
                        foreach($ig['groups'] as $interest){
                            ?>
                            <li><?php echo esc_html($interest['name']); ?></li>
                            <?php
                        }
                        ?>
                        </ul>
                    </td>
                </tr>
            </table>
            <?php
                }
            }
        }
    }
}
?>
    <div style="width: 900px; margin-top: 35px;">
        <table class="widefat mc-widefat mc-yellow">
            <tr><th colspan="2">CSS Cheat Sheet</th></tr>
            <tr valign="top">
                <th scope="row">.widget_mailchimpsf_widget </th>
                <td>This targets the entire widget container.</td>
            </tr>
            <tr valign="top">
                <th scope="row">.widget-title</th>
                <td>This styles the title of your MailChimp widget. <i>Modifying this class will affect your other widget titles.</i></td>
            </tr>
            <tr valign="top">
                <th scope="row">#mc_signup</th>
                <td>This targets the entirity of the widget beneath the widget title.</td>
            </tr>
            <tr valign="top">
                <th scope="row">#mc_subheader</th>
                <td>This styles the subheader text.</td>
            </tr>
            <tr valign="top">
                <th scope="row">.mc_form_inside</th>
                <td>The guts and main container for the all of the form elements (the entirety of the widget minus the header and the sub header).</td>
            </tr>
            <tr valign="top">
                <th scope="row">.mc_header</th>
                <td>This targets the label above the input fields.</td>
            </tr>
            <tr valign="top">
                <th scope="row">.mc_input</th>
                <td>This attaches to the input fields.</td>
            </tr>
            <tr valign="top">
                <th scope="row">.mc_header_address</th>
                <td>This is the label above an address group.</td>
            </tr>
            <tr valign="top">
                <th scope="row">.mc_radio_label</th>
                <td>These are the labels associated with radio buttons.</td>
            </tr>
            <tr valign="top">
                <th scope="row">#mc-indicates-required</th>
                <td>This targets the “Indicates Required Field” text.</td>
            </tr>
            <tr valign="top">
                <th scope="row">#mc_signup_submit</th>
                <td>Use this to style the submit button.</td>
            </tr>
        </table>
    </div>

</form>