<?php

    /* Admin bar */
    function userpro_get_fields_list($template,$val,$k,$opt)
    {

        $output= "";
        $output .= "<select name='$k-$opt' id='$k-$opt'>";

        $field_names = get_option("userpro_fields_groups");
        foreach($field_names[$template]['default'] as $k=>$v) {
            if (array_key_exists('label', $v)) {

                $output.="<option value=$k ".selected($k, $val, 0).">$v[label]</option>";
            }
        }
        $output.="</select>";

        return $output;
    }


    function userpro_admin_bar(){
        ?>
<div class="userpro-admin-head">
<div class="userpro-admin-left">
<a href="<?php echo admin_url('admin.php'); ?>?page=userpro">User<span>Pro</span> <span class="userpro-admin-left--version"><?php echo UserPro::$version ?></span></a>
    <span class="userpro-admin-left--desc"><?php _e('administration backend', 'userpro') ?></span>

    <?php if (get_option('userpro_activated')) {
        $message_class = 'up-approve';
       $message =  __('Thank you for activating UserPro!', 'userpro');
    } else {
        $message_class = 'up-warning';
        $message = __('This copy is unlicensed. Please activate your copy.', 'userpro');
    } ?>
    <span class="<?= $message_class ?>"><?= $message ?></span>
</div>
<div class="userpro-admin-right">
<a href="http://codecanyon.net/user/DeluxeThemes#contact" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Email Support','userpro'); ?></a>
<a href="http://userproplugin.com/userpro/docs/" class="up-admin-btn up-admin-btn--dark-blue  small"><?php _e('User Manual','userpro'); ?></a>
<a href="http://codecanyon.net/downloads" class="up-admin-btn up-admin-btn--dark-blue small"><?php _e('Rate Us On CodeCanyon','userpro'); ?></a>
<a href="http://codecanyon.net/item/userpro-user-profiles-with-social-login/5958681" class="up-admin-btn big approve"><?php _e('Download Latest','userpro'); ?></a>
<?php if(!get_transient('userpro_disable_review')){?>
<div class="userpro-rate-me-bubble">
<div class="up-rating-close"><i class="userpro-icon-close"></i></div>
<div class="up-rating-text"><?php _e('Love the plugin?<br> Rate it on Codecanyon','userpro');?></div>
</div>
<?php }?>
</div>
<div class="clear"></div>
</div>
<?php
    }

    /* Get post value */
    function userpro_admin_post_value($key, $value, $post){
        if (isset($_POST[$key])){
            if ($_POST[$key] == $value)
                echo 'selected="selected"';
        }
    }

    /* Get skin list */
    function userpro_admin_skins(){
        $skins = scandir( userpro_path . 'skins/' );
        if (file_exists( get_stylesheet_directory() . '/userpro/skins/' ) ) {
            $custom_skins = scandir( get_stylesheet_directory() . '/userpro/skins/');
            $arr = array_merge($skins, $custom_skins);
        } else {
            $arr = $skins;
        }

        if (class_exists('userpro_sk_api')) {
            $arr = array_merge($arr, scandir( userpro_sk_path . '/skins/'));
        }

        $arr = array_unique($arr);
        return $arr;
    }

    /* Show builtin mail vars */
    function userpro_admin_list_builtin_vars($var1=null){
        $res = null;

        $array = array(
                       '{USERPRO_ADMIN_EMAIL}' => __('Displays the admin email that users can contact you at. You can configure it under Mail settings.','userpro'),
                       '{USERPRO_BLOGNAME}' => __('Displays blog name','userpro'),
                       '{USERPRO_BLOG_URL}' => __('Displays blog URL','userpro'),
                       '{USERPRO_BLOG_ADMIN}' => __('Displays blog WP-admin URL','userpro'),
                       '{USERPRO_LOGIN_URL}' => __('Displays the UserPro login page','userpro'),
                       '{USERPRO_USERNAME}' => __('Displays the Username of user','userpro'),
                       '{USERPRO_FIRST_NAME}' => __('Displays the user first name','userpro'),
                       '{USERPRO_LAST_NAME}' => __('Displays the user last name','userpro'),
                       '{USERPRO_NAME}' => __('Displays the user display name or public name','userpro'),
                       '{USERPRO_EMAIL}' => __('Displays the E-mail address of user','userpro'),
                       '{USERPRO_PROFILE_LINK}' => __('Displays the User Profile address','userpro'),
                       '{USERPRO_PROFILE_FIELDS}' => __('Outputs all profile fields in the e-mail','userpro'),
                       '{USERPRO_VALIDATE_URL}' => __('The account validation URL that user receives after signing up (If you enable e-mail validation feature)','userpro'),
                       '{USERPRO_PENDING_REQUESTS_URL}' => __('Gives a link to the admin to manage his pending user requests and registrations.','userpro'),
                       '{USERPRO_ACCEPT_VERIFY_INVITE}' => __('This is an automatic generated URL that user will click to become verified after an invitation to get verified is sent to him.','userpro'),
                       '{USERPRO_custom_field}' => __('Displays value of custom field','userpro')
                       );

        if ($var1){
            $array[$var1] = __('Custom Variable 1','userpro');
        }

        foreach($array as $key => $val) {
            $res .= '<br /><code>'.$key.'</code> '. $val;
        }

        echo $res;
    }


    /* Show builtin restricted content vars */
    function userpro_admin_list_builtin_vars_restricted_content(){
        $res = null;

        $array = array(
                       '{LOGIN_POPUP}' => __('Displays Login Popup','userpro'),
                       '{REGISTER_POPUP}' => __('Displays Register Popup','userpro')
                       );

        foreach($array as $key => $val) {
            $res .= '<br /><code>'.$key.'</code> '. $val;
        }

        echo $res;
    }

    /* Count fields */
    function userpro_admin_count_fields(){
        $array= get_option('userpro_fields');
        return sprintf(__('%s fields available','userpro'), count($array));
    }

    /* Return field actions */
    function userpro_admin_field_actions($key=null,$arr=null){
        $output = null;
        $output .= '<div class="upadmin-field-actions">';


        if ($arr){

            if (isset($arr['type'])) {
                if (!isset($arr['help'])) $arr['help'] = '';
                if (!isset($arr['placeholder'])) $arr['placeholder'] = '';
                if (!isset($arr['html'])) $arr['html'] = 0;
                if (!isset($arr['hideable'])) $arr['hideable'] = 0;
                if (!isset($arr['hidden'])) $arr['hidden'] = 0;
                if (!isset($arr['required'])) $arr['required'] = 0;
                if (!isset($arr['locked'])) $arr['locked'] = 0;
                if (!isset($arr['private'])) $arr['private'] = 0;
            }

            ksort($arr);

            foreach($arr as $k=>$v){
                if ($k && in_array($k, array('html','hideable','hidden','required','locked','private') ) ) {
                    if ($v == 0) { $class = 'off'; } else { $class = 'on'; }
                    $output .= '<a href="#" class="upadmin-field-action upadmin-field-action-'.$k.' '.$class.'" data-key="'.$key.'" data-role="'.$k.'" data-value="'.$v.'"></a>';
                }
            }

        }

        $output .= '<a href="#" title="'.__('Edit Field','userpro').'" class="upadmin-field-action-edit"></a>';

        $output .= '<a href="#" title="'.__('Delete Field','userpro').'" class="upadmin-field-action-remove"></a>';

        $output .= '</div>';
        return $output;
    }

    /**
     List all fields
     **/
    function userpro_admin_list_fields($specific_field=null){
        $output = null;
        $unsorted = get_option('userpro_fields');
        if ($specific_field){
            $arr = $unsorted[$specific_field];
            if (isset($arr['type']) && $arr['type'] != '') {
                $label = (isset($arr['label'])) ? $arr['label'] : '';
                if (!isset($arr['help'])) $arr['help'] = '';
                if (!isset($arr['placeholder'])) $arr['placeholder'] = '';
                if (!isset($arr['html'])) $arr['html'] = 0;
                if (!isset($arr['hideable'])) $arr['hideable'] = 0;
                if (!isset($arr['hidden'])) $arr['hidden'] = 0;
                if (!isset($arr['required'])) $arr['required'] = 0;
                if (!isset($arr['locked'])) $arr['locked'] = 0;
                if (!isset($arr['private'])) $arr['private'] = 0;
                if (!isset($arr['ajaxcheck'])) $arr['ajaxcheck'] = '';
                if (!isset($arr['woo'])) $arr['woo'] = 0;
                if (userpro_get_field_icon($specific_field)) { $arr['icon'] = userpro_get_field_icon($specific_field); } else { $arr['icon'] = ''; }
                if (!isset($arr['button_text']) && isset($arr['type']) && ( $arr['type'] == 'file' || $arr['type'] == 'picture') ) $arr['button_text'] = '';
                if (!isset($arr['allowed_extensions']) && isset($arr['type']) && ( $arr['type'] == 'file' ) ) $arr['allowed_extensions'] = '';
                if (!isset($arr['list_id']) && isset($arr['type']) && ( $arr['type'] == 'mailchimp' ) ) $arr['list_id'] = '';
                if (!isset($arr['sitekey']) && isset($arr['type']) && ( $arr['type'] == 'recaptcha' ) ) $arr['sitekey'] = '';
                if (!isset($arr['list_text']) && isset($arr['type']) && ( $arr['type'] == 'mailchimp' ) ) $arr['list_text'] = '';
                if (!isset($arr['security_qa']) && isset($arr['type']) && ( $arr['type'] == 'securityqa')) $arr['security_qa'] = 'Sample Question 1:Answer
                    Sample Question 2:Answer'; // Security Question new Field
                    }
            $output .= "<li class='field woo-".$arr['woo']."' id='upadmin-$specific_field'>$label <span class='ufieldkey'>$specific_field</span>";

            if (isset($arr['type']) && in_array($arr['type'], array('select','multiselect','checkbox','checkbox-full','radio','radio-full','recatpcha','security_qa'))){
                if (!isset($arr['options'])) $arr['options'] = '';
            }

            $output .= '<div class="upadmin-field-zone"><a href="#" class="upadmin-field-zone-cancel"></a>';

            if (is_array($arr)){
                ksort($arr);
                foreach($arr as $opt=>$val){
                    if (in_array($opt, array('label','help','placeholder')) ) {
                        $output .= userpro_admin_field_desc($opt) . '<input type="text" name="'.$specific_field.'-'.$opt.'" id="'.$specific_field.'-'.$opt.'" value="'.stripslashes($val).'" />';
                    }
                    if (in_array($opt, array('options' , 'security_qa')) ) {
                        if ($val != '' && is_array($val) ) $val = implode("\n", $val);
                        $output .= '<textarea name="'.$specific_field.'-'.$opt.'" id="'.$specific_field.'-'.$opt.'" cols="40" rows="10">'.stripslashes($val).'</textarea>';
                    }
                }
            }

            $output .= '</div>';

            ksort($arr);
            foreach($arr as $opt=>$val){
                if (!in_array($opt, array('label','help','placeholder','options', 'security_qa')) ) {
                    $output .= "<input type='hidden' name='$specific_field-$opt' id='$specific_field-$opt' value='$val' />";
                }
            }

            $output .= userpro_admin_field_actions($specific_field, $arr);
            $output .= "</li>";
        } else {
            foreach($unsorted as $k=>$arr){
                $label = '';
                if (is_array($arr)) {
                    if (isset($arr['type']) && $arr['type'] != '') {
                        $label = (isset($arr['label'])) ? $arr['label'] : '';
                        if (!isset($arr['help'])) $arr['help'] = '';
                        if (!isset($arr['placeholder'])) $arr['placeholder'] = '';
                        if (!isset($arr['html'])) $arr['html'] = 0;
                        if (!isset($arr['hideable'])) $arr['hideable'] = 0;
                        if (!isset($arr['hidden'])) $arr['hidden'] = 0;
                        if (!isset($arr['required'])) $arr['required'] = 0;
                        if (!isset($arr['locked'])) $arr['locked'] = 0;
                        if (!isset($arr['private'])) $arr['private'] = 0;
                        if (!isset($arr['ajaxcheck'])) $arr['ajaxcheck'] = '';
                        if (!isset($arr['woo'])) $arr['woo'] = 0;
                        if (userpro_get_field_icon($k)) { $arr['icon'] = userpro_get_field_icon($k); } else { $arr['icon'] = ''; }
                        if (!isset($arr['button_text']) && isset($arr['type']) && ( $arr['type'] == 'file' || $arr['type'] == 'picture') ) $arr['button_text'] = '';
                        if (!isset($arr['list_id']) && isset($arr['type']) && ( $arr['type'] == 'mailchimp' ) ) $arr['list_id'] = '';
                        if (!isset($arr['sitekey']) && isset($arr['type']) && ( $arr['type'] == 'recaptcha' ) ) $arr['sitekey'] = '';
                        if (!isset($arr['list_text']) && isset($arr['type']) && ( $arr['type'] == 'mailchimp' )) $arr['list_text'] = '';
                        if (!isset($arr['follower_text']) && isset($arr['type']) && ( $arr['type'] == 'followers' )) $arr['follower_text'] = '';
                        if (!isset($arr['security_qa']) && isset($arr['type']) && ( $arr['type'] == 'securityqa')) $arr['security_qa'] = 'Sample Question 1:Answer
                            Sample Question 2:Answer'; // Security Question new Field
                            }
                    $woo = isset($arr['woo'])?$arr['woo']:'';
                    $output .= "<li class='field woo-".$woo."' id='upadmin-$k'>$label <span class='ufieldkey'>$k</span>";

                    if (isset($arr['type']) && in_array($arr['type'], array('select','multiselect','checkbox','checkbox-full','radio','radio-full'))){
                        if (!isset($arr['options'])) $arr['options'] = '';
                    }

                    $output .= '<div class="upadmin-field-zone"><a href="#" class="upadmin-field-zone-cancel"></a>';

                    if (is_array($arr)){
                        ksort($arr);
                        foreach($arr as $opt=>$val){
                            if (in_array($opt, array('label','help','placeholder','ajaxcheck','icon','button_text','list_id','list_text','sitekey','follower_text', 'allowed_extensions')) ) {
                                $output .= userpro_admin_field_desc($opt) . '<input type="text" name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" value="'.stripslashes($val).'" />';
                            }
                            if (in_array($opt, array('options' , 'security_qa')) ) {
                                if ($val != '' && is_array($val) ) $val = implode("\n", $val);
                                $output .= '<textarea name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" cols="40" rows="10">'.stripslashes($val).'</textarea>';
                            }
                        }
                    }

                    $output .= '</div>';

                    ksort($arr);
                    foreach($arr as $opt=>$val){
                        if (!in_array($opt, array('label','help','placeholder','options','ajaxcheck','icon','button_text','list_id','list_text','follower_text' , 'security_qa', 'allowed_extensions')) ) {
                            if (!is_array($val)){
                                $output .= "<input type='hidden' name='$k-$opt' id='$k-$opt' value='$val' />";
                            } else {
                                $output .= "<input type='hidden' name='$k-$opt' id='$k-$opt' value='options' />";
                            }
                        }
                    }

                    $output .= userpro_admin_field_actions($k, $arr);
                    $output .= "</li>";
                }
            }
        }
        return $output;
    }

    /** List all groups **/
    function userpro_admin_list_groups(){
        global $userpro;
        $output = null;
        $groups = $userpro->groups;
        $groups = apply_filters( 'userpro_groups', $groups );
        unset($groups['register']);
        unset($groups['edit']);
        unset($groups['view']);
        unset($groups['login']);
        unset($groups['social']);

        $array = array(
                       'register' => __('Registration Fields','userpro'),
                       'edit' => __('Edit Profile Fields','userpro'),
                       'login' => __('Login Fields','userpro'),
                       'social' => __('Social Fields','userpro')
                       );

        $array = apply_filters('userpro_groups_array', $array);

        if (is_array($groups) ){
            foreach($groups as $k=>$v){
                $add[$k] = strtoupper($k);
            }
            if (isset($add) && is_array($add)){
                $array = array_merge($array, $add );
            }
        }

        foreach($array as $template => $name) {
            $output .= '<div class="upadmin-tpl upadmin-tpl-'.$template.'">
            <form action="" method="post" data-role="'.$template.'" data-group="default" data-loading="'.userpro_url.'admin/images/loading.gif">
            <div class="upadmin-tpl-head max">
            '.$name.'
            <div class="upadmin-icon-abs">';
            if ($template != 'social' && !isset($add[$template]) ) {
                $output .= '<a href="'.userpro_admin_link($template).'" class="button upadmin-noajax">'.__('View Page','userpro').'</a>';
            }
            $output .= '<a href="#" class="button resetgroup">'.__('Reset','userpro').'</a>
            <a href="#" class="button button-primary saveform">'.__('Save','userpro').'</a>
            <a href="#" class="max"></a>
            </div>
            </div>
            <div class="upadmin-tpl-body max">
            <ul>'.userpro_admin_list_fields_by_group($template,'default').'</ul>
            </div>
            </form>
            </div>';

            if($template=='register')
            {


                $output.='<div class="up-description up-description--full">';
                $output.=__('Fields which user will see at time of registration.','userpro');
                $output.='</div>';
            }
            if($template=='edit')
            {


                $output.='<div class="up-description up-description--full">';
                $output.=__('Fields which user will see at time of edit profile. ','userpro');
                $output.='</div>';
            }
            if($template=='login')
            {

                $output.='<div class="up-description up-description--full">';
                $output.=__('Fields which user will see at time of login. ','userpro');
                $output.='</div>';
            }

            if($template=='social')
            {

                $output.='<div class="up-description up-description--full">';
                $output.= __('Social Fields.','userpro');
                $output.='</div>';
            }
        }
        return $output;
    }

    /** List one group **/
    function userpro_admin_list_group($role, $group){
        $output = null;
        $array = array(
                       'register' => __('Registration Fields','userpro'),
                       'edit' => __('Edit Profile Fields','userpro'),
                       'login' => __('Login Fields','userpro'),
                       'social' => __('Social Fields','userpro'),
                       );
        $array = apply_filters('userpro_group_array', $array);
        foreach($array as $template => $name) {
            if ($template == $role) {
                $output .= '<div class="upadmin-tpl">
                <form action="" method="post" data-role="'.$template.'" data-group="default" data-loading="'.userpro_url.'admin/images/loading.gif">
                <div class="upadmin-tpl-head max">
                '.$name.'
                <div class="upadmin-icon-abs">';

                if ($template != 'social') {
                    $output .= '<a href="'.userpro_admin_link($template).'" class="button upadmin-noajax">'.__('View Page','userpro').'</a>';
                }

                $output .= '<a href="#" class="button resetgroup">'.__('Reset','userpro').'</a>
                <a href="#" class="button button-primary saveform">'.__('Save','userpro').'</a>
                <a href="#" class="max"></a>
                </div>
                </div>
                <div class="upadmin-tpl-body max">
                <ul>'.userpro_admin_list_fields_by_group($template, $group).'</ul>
                </div>
                </form>
                </div>';
            }
        }
        return $output;
    }

    /* Field description */
    function userpro_admin_field_desc($opt) {
        switch ($opt){
            case 'label': $text = __('Label','userpro'); break;
            case 'help': $text = __('Help Text','userpro'); break;
            case 'placeholder' : $text = __('Placeholder','userpro'); break;
            case 'ajaxcheck' : $text = __('Ajax Check Callback (advanced)','userpro'); break;
            case 'heading' : $text = __('Heading Text','userpro'); break;
            case 'collapsible' : $text = __('Collapsible Section','userpro'); break;
            case 'collapsed' : $text = __('Collapsed','userpro'); break;
            case 'follower_text' : $text = __('followers email alert text','userpro'); break;
            case 'button_text' : $text = __('Upload Button Text','userpro'); break;
            case 'allowed_extensions' : $text = __('Allowed extensions', 'userpro'); break;
            case 'list_id' : $text = __('MailChimp List ID','userpro'); break;
            case 'sitekey' : $text = __('Recaptcha SiteKey','userpro'); break;
            case 'list_text' : $text = __('MailChimp Subscribe Text','userpro'); break;
            case 'icon' : $text = __('Font Icon Code','userpro'); break;
            case 'security_qa': $text = __('Security Question' , 'userpro'); break;
        }
        return '<span class="upadmin-field-zone-desc">'.$text.'</span>';
    }

    /** List fields based on group **/
    function userpro_admin_list_fields_by_group($template, $group) {
        $array = get_option('userpro_fields_groups');
        $output = '<li>'.__('Drag fields / sections into this area','userpro').'</li>';
        $group = $array[$template][$group];
        if (isset($group) && !empty($group)){
            foreach($group as $k=> $arr){
                if (isset($arr['heading']) && $arr['heading'] != '' || isset($arr['label']) && $arr['label'] != '') {
                    if (isset($arr['heading'])) { // seperator
                        $output .= '<li class="heading" data-special="'.$k.'"><span>'.$arr['heading'].'</span> <span class="ufieldkey">'.$k.'</span>';

                        $output .= '<div class="upadmin-field-zone"><a href="#" class="upadmin-field-zone-cancel"></a>';

                        if (!isset($arr['collapsible'])) $arr['collapsible'] = 0;
                        if (!isset($arr['collapsed'])) $arr['collapsed'] = 0;

                        ksort($arr);
                        foreach($arr as $opt=>$val){
                            if (in_array($opt, array('heading')) ) {
                                $output .= userpro_admin_field_desc($opt) . '<input type="text" name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" value="'.stripslashes($val).'" />';
                            }
                            if (in_array($opt, array('collapsible','collapsed')) ) {
                                $output .= userpro_admin_field_desc($opt);
                                $output .= "<select name='$k-$opt' id='$k-$opt'>
                                <option value='1' ".selected(1, $val, 0).">".__('Yes','userpro')."</option>
                                <option value='0' ".selected(0, $val, 0).">".__('No','userpro')."</option>
                                </select>";
                            }

                        }
                        $output .= '</div>';

                        ksort($arr);
                        foreach($arr as $opt=>$val){
                            if (!in_array($opt, array('heading','collapsible','collapsed') ) ) {
                                $output .= "<input type='hidden' name='$k-$opt' id='$k-$opt' value='$val' />";
                            }
                        }

                        $output .= userpro_admin_field_actions($k, $arr);
                        $output .= '</li>';
                    } else {

                        if (!isset($arr['help'])) $arr['help'] = '';
                        if (!isset($arr['placeholder'])) $arr['placeholder'] = '';
                        if (!isset($arr['html'])) $arr['html'] = 0;
                        if (!isset($arr['hideable'])) $arr['hideable'] = 0;
                        if (!isset($arr['add_condition'])) $arr['add_condition'] = '';
                        if (!isset($arr['condition_fields'])) $arr['condition_fields'] = '';
                        if (!isset($arr['condition_rule'])) $arr['condition_rule'] = '';
                        if (!isset($arr['condition_value'])) $arr['condition_value'] = '';

                        if (!isset($arr['hidden'])) $arr['hidden'] = 0;
                        if (!isset($arr['required'])) $arr['required'] = 0;
                        if (!isset($arr['locked'])) $arr['locked'] = 0;
                        if (!isset($arr['private'])) $arr['private'] = 0;
                        if (!isset($arr['ajaxcheck'])) $arr['ajaxcheck'] = '';
                        if (!isset($arr['woo'])) $arr['woo'] = 0;
                        if (userpro_get_field_icon($k)) { $arr['icon'] = userpro_get_field_icon($k); } else { $arr['icon'] = ''; }
                        if (!isset($arr['button_text']) && isset($arr['type']) && ( $arr['type'] == 'file' || $arr['type'] == 'picture') ) $arr['button_text'] = '';
                        if (!isset($arr['list_id']) && isset($arr['type']) && ( $arr['type'] == 'mailchimp' )) $arr['list_id'] = '';
                        if (!isset($arr['sitekey']) && isset($arr['type']) && ( $arr['type'] == 'recaptcha' )) $arr['sitekey'] = '';
                        if (!isset($arr['list_text']) && isset($arr['type']) && ( $arr['type'] == 'mailchimp' )) $arr['list_text'] = '';
                        if (!isset($arr['follower_text']) && isset($arr['type']) && ( $arr['type'] == 'followers' )) $arr['follower_text'] = '';
                        if (!isset($arr['security_qa']) && isset($arr['type']) && ( $arr['type'] == 'securityqa')) $arr['security_qa'] = 'Sample Question 1:Answer
                            Sample Question 2:Answer'; // Security Question new Field
                            $output .= '<li class="field woo-'.$arr['woo'].'">'.$arr['label'] . '<span class="ufieldkey">'.$k.'</span>';

                        $output .= '<div class="upadmin-field-zone"><a href="#" class="upadmin-field-zone-cancel"></a>';

                        if (isset($arr['type']) && in_array($arr['type'], array('select','multiselect','checkbox','checkbox-full','radio','radio-full' , 'security_qa','add_condition'))){
                            if (!isset($arr['options'])) $arr['options'] = '';
                        }

                        if (is_array($arr)){
                            ksort($arr);
                            foreach($arr as $opt=>$val){
                                if (in_array($opt, array('label','help','placeholder','ajaxcheck','icon','button_text','list_id','sitekey','list_text','follower_text')) ) {
                                    $output .= userpro_admin_field_desc($opt) . '<input type="text" name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" value="'.stripslashes($val).'" />';


                                }
                                if (in_array($opt, array('options' , 'security_qa'))){
                                    if ($val != '' && is_array($val) ) $val = implode("\n", $val);
                                    $output .= '<textarea name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" cols="40" rows="10">'.stripslashes($val).'</textarea>';
                                }


                            }
                        }

                        if (is_array($arr) && $arr['type']!='picture'){
                            ksort($arr);
                            $output .="<br><br>";
                            $output .="<b>Manage Condition</b><br>";
                            foreach($arr as $opt=>$val){

                                if (in_array($opt, array('add_condition'))){
                                    $output .= "<select name='$k-$opt' id='$k-$opt'>

                                    <option value='show' ".selected('show', $val, 0).">".__('Show','userpro')."</option>
                                    <option value='hide' ".selected('hide', $val, 0).">".__('hide','userpro')."</option>
                                    </select>";
                                }


                                if (in_array($opt, array('condition_fields'))){
                                    $output .="<b> If  </b>";
                                    $output .= userpro_get_fields_list($template,$val,$k,$opt);
                                }

                                if (in_array($opt, array('condition_rule'))){
                                    $output .= "<select name='$k-$opt' id='$k-$opt'>
                                    <option>".__('select','userpro')."</option>
                                    <option value='empty' ".selected('empty', $val, 0).">".__('Empty','userpro')."</option>
                                    <option value='Not_empty' ".selected('Not_empty', $val, 0).">".__('Not empty','userpro')."</option>
                                    <option value='equal_to' ".selected('equal_to', $val, 0).">".__('Equal to','userpro')."</option>
                                    <option value='not_equal' ".selected('not_equal', $val, 0).">".__('Not Equal to','userpro')."</option>

                                    </select>";
                                }

                                if (in_array($opt, array('condition_value'))){            
                                    $output .= '<input type="text" name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" value="'.stripslashes($val).'" />';

                                }
                            }}
                        $output .= '</div>';
                        ksort($arr);
                        foreach($arr as $opt=>$val){

                            if (!in_array($opt, array('label','help','placeholder','options','ajaxcheck','icon','button_text','list_id','list_text','follower_text','security_qa','condition_rule','add_condition','condition_value','condition_fields')) ) {
                                if (!is_array($val)){
                                    $output .= "<input type='hidden' name='$k-$opt' id='$k-$opt' value='$val' />";
                                } else {
                                    $output .= "<input type='hidden' name='$k-$opt' id='$k-$opt' value='options' />";
                                }
                            }
                        }

                        $output .= userpro_admin_field_actions($k, $arr);

                        $output .= '</li>';

                    }
                }
            }
        }
        return $output;
    }

    /* new section empty */
    function userpro_admin_new_section(){
        $output = null;
        $output .= '<li class="heading" data-special="newsection"><span>'.__('Add Seperator / Section','userpro').'</span>';

        $k = 'newsection';
        $arr['heading'] = __('My Custom Heading','userpro');
        $arr['collapsible'] = 0;
        $arr['collapsed'] = 0;

        $output .= '<div class="upadmin-field-zone"><a href="#" class="upadmin-field-zone-cancel"></a>';

        ksort($arr);
        foreach($arr as $opt=>$val){
            if (in_array($opt, array('heading')) ) {
                $output .= userpro_admin_field_desc($opt) . '<input data-special="newsection" type="text"  name="'.$k.'-'.$opt.'" id="'.$k.'-'.$opt.'" value="'.stripslashes($val).'" />';


            }
            if (in_array($opt, array('collapsible','collapsed')) ) {
                $output .= userpro_admin_field_desc($opt);
                $output .= "<select data-special=newsection' name='$k-$opt' id='$k-$opt'>
                <option value='1' ".selected(1, $val, 0).">".__('Yes','userpro')."</option>
                <option value='0' ".selected(0, $val, 0).">".__('No','userpro')."</option>
                </select>";
            }
        }
        $output .= '</div>';

        $output .= userpro_admin_field_actions();
        $output .= '</li>';

        return $output;
    }

    /** Get link of specific page */
    function userpro_admin_link($template){
        $pages = get_option('userpro_pages');
        if ($template=='view') $template = 'profile';
        if (isset($pages[$template])){
            return get_page_link( $pages[$template] );
        }
    }

    /* Check page exists */
    function userpro_admin_page_exists($template) {
        $pages = get_option('userpro_pages');
        if ($template=='view') $template = 'profile';
        if (isset($pages[$template]))
            $page_id = $pages[$template];
        if(isset($page_id)){
            $page_data = get_page($page_id);
        }
        if(isset($page_data) && $page_data->post_status == 'publish'){
            return true;
        }
        return false;
    }

    /* Broken page notification */
    function userpro_admin_broken_page() {
        return '<div class="upadmin-broken">'.__('Broken page. Please rebuild plugin pages.','userpro').'</div>';
    }

    /** Display field types **/
    function userpro_admin_field_types(){
        $array = array(
                       'text' => __('Text Input','userpro'),
                       'picture' => __('Photo Upload','userpro'),
                       'file' => __('File Upload','userpro'),
                       'textarea' => __('Textarea','userpro'),
                       'select' => __('Select Dropdown','userpro'),
                       'multiselect' => __('Multiselect Box','userpro'),
                       'checkbox' => __('Checkbox (floating)','userpro'),
                       'checkbox-full' => __('Checkbox (full width)','userpro'),
                       'radio' => __('Radio Choice (floating)','userpro'),
                       'radio-full' => __('Radio Choice (full width)','userpro'),
                       'recaptcha' => __('ReCaptcha','userpro'),
                       'datepicker' => __('Date Picker','userpro'),
                       'mailchimp' => __('MailChimp Newsletter Subscription','userpro'),
                       'password' => __('Password Field','userpro'),
                       'passwordstrength' => __('Password Strength Meter','userpro'),
                       'securityqa'	=> __('Security Question Answer' , 'userpro') // Security Question new Filled
                       );
        foreach($array as $k=>$v){
            echo '<option value="'.$k.'">'.$v.'</option>';
        }
    }

    /**Sync usermeta **/
    function userpro_admin_usermeta(){
        $array = get_user_meta( get_current_user_id() );
        echo '<option value="">&mdash; Choose an existing usermeta &mdash;</option>';
        ksort($array);
        foreach($array as $k=>$v){
            if (!strstr($k, 'hide_')){
                echo '<option value="'.$k.'">'.$k.'</option>';
            }
        }
    }

    add_action('wp_ajax_nopriv_userpro_default_background_img_remove', 'userpro_default_background_img_remove');
    add_action('wp_ajax_userpro_default_background_img_remove', 'userpro_default_background_img_remove');
    function userpro_default_background_img_remove(){
    	if (!current_user_can('manage_options'))
    		die(); // admin priv

    	userpro_set_option('default_background_img','');

    }

    /**
     Resort fields
     **/
    add_action('wp_ajax_nopriv_userpro_field_sort', 'userpro_field_sort');
    add_action('wp_ajax_userpro_field_sort', 'userpro_field_sort');
    function userpro_field_sort(){
        if (!current_user_can('manage_options'))
            die(); // admin priv
        $output='';

        $order = explode(',', $_POST['order']);
        foreach($order as $item) {
            $item = str_replace('upadmin-','',$item);
            $clean[$item] = $item;
        }

        $unsorted = get_option('userpro_fields');
        $sorted = array_merge(array_flip( $clean ), $unsorted);
        update_option('userpro_fields', $sorted);

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Create a new field in backend
     **/
    add_action('wp_ajax_nopriv_userpro_create_field', 'userpro_create_field');
    add_action('wp_ajax_userpro_create_field', 'userpro_create_field');
    function userpro_create_field(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        $newfield= array();
        $upadmin_n_sync = isset($_POST['upadmin_n_sync'])?$_POST['upadmin_n_sync']:'';
        $upadmin_n_key = isset($_POST['upadmin_n_key'])?$_POST['upadmin_n_key']:'';
        if ( empty($_POST['upadmin_n_title']) ){
            $output['error']['upadmin_n_title'] = __('Each field must have a title.','userpro');
        } elseif ( empty($_POST['upadmin_n_key']) && empty($_POST['upadmin_n_sync']) ){
            $output['error']['upadmin_n_key'] = __('Please enter a unique key or choose an existing usermeta.','userpro');
        } else {

            if (isset($upadmin_n_sync) && !empty($upadmin_n_sync) ){
                $key=$upadmin_n_sync;
            }else{
                $key=$upadmin_n_key;
            }

            // check that field key is unique
            $fields = get_option('userpro_fields');
            if (isset($fields[$upadmin_n_sync]) && !empty($upadmin_n_sync) ){
                $output['error']['upadmin_n_sync'] = __('This existing usermeta already exists in your fields list below.','userpro');
            } elseif (isset($fields[$upadmin_n_key]) && !empty($upadmin_n_key) ){
                $output['error']['upadmin_n_key'] = __('This unique key already exists in your fields list below.','userpro');

            } else {

                // create the field

                $newfield[$key] = array(
                                        '_builtin' => 0,
                                        'type' => $_POST['upadmin_n_type']
                                        );

                if (isset($_POST['upadmin_n_title']) && !empty($_POST['upadmin_n_title'])) {
                    $newfield[$key]['label'] = $_POST['upadmin_n_title'];
                }

                if (isset($_POST['upadmin_n_help']) && !empty($_POST['upadmin_n_help'])) {
                    $newfield[$key]['help'] = $_POST['upadmin_n_help'];
                }

                if (isset($_POST['upadmin_n_ph']) && !empty($_POST['upadmin_n_ph'])) {
                    $newfield[$key]['placeholder'] = $_POST['upadmin_n_ph'];
                }

                if (isset($_POST['upadmin_n_filetypes']) && !empty($_POST['upadmin_n_filetypes'])){
                    $newfield[$key]['allowed_extensions'] = str_replace(' ','', $_POST['upadmin_n_filetypes']);
                } elseif ($_POST['upadmin_n_type'] == 'file'){
                    $newfield[$key]['allowed_extensions'] = 'zip,pdf,txt';
                }

                if ( isset($_POST['upadmin_n_choices_builtin']) && !empty($_POST['upadmin_n_choices_builtin']) ){
                    $newfield[$key]['options'] = userpro_filter_to_array( $_POST['upadmin_n_choices_builtin'] );
                } elseif ( isset($_POST['upadmin_n_choices']) && !empty($_POST['upadmin_n_choices']) ){
                    $n_choices = preg_split('/[\r\n]+/', $_POST['upadmin_n_choices'], -1, PREG_SPLIT_NO_EMPTY);

                    $newfield[$key]['options'] = $n_choices;
                }

                /* finished creating new field */

                $allfields = $newfield+$fields;
                update_option('userpro_fields',$allfields);

                $output['html'] = userpro_admin_list_fields($key);
                $output['count'] = userpro_admin_count_fields();

            }

        }

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Restore default fields in backend
     **/
    add_action('wp_ajax_nopriv_userpro_restore_builtin_fields', 'userpro_restore_builtin_fields');
    add_action('wp_ajax_userpro_restore_builtin_fields', 'userpro_restore_builtin_fields');
    function userpro_restore_builtin_fields(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        $_builtin = get_option('userpro_fields_builtin');
        update_option('userpro_fields',$_builtin);
        delete_option('userpro_pre_icons_setup');
        delete_option('userpro_update_1036');     // Added by Yogesh to solve profile background custom field issue
        $output['html'] = userpro_admin_list_fields();
        $output['count'] = userpro_admin_count_fields();

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Restore default groups in backend
     **/
    add_action('wp_ajax_nopriv_userpro_restore_builtin_groups', 'userpro_restore_builtin_groups');
    add_action('wp_ajax_userpro_restore_builtin_groups', 'userpro_restore_builtin_groups');
    function userpro_restore_builtin_groups(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        $_builtin = get_option('userpro_fields_groups_default');
        update_option('userpro_fields_groups',$_builtin);

        $output['html'] = userpro_admin_list_groups();

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Restore default group in backend
     **/
    add_action('wp_ajax_nopriv_userpro_reset_group', 'userpro_reset_group');
    add_action('wp_ajax_userpro_reset_group', 'userpro_reset_group');
    function userpro_reset_group(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        $_builtin_group = get_option('userpro_fields_groups_default_'.$_POST['role']);
        $all = get_option('userpro_fields_groups');
        $all[$_POST['role']]['default'] = $_builtin_group;
        update_option('userpro_fields_groups',$all);

        $output['html'] = userpro_admin_list_group($_POST['role'], 'default');

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /* Get choices of field */
    function userpro_admin_field_choices($key){
        $fields = get_option('userpro_fields');
        return $fields[$key]['options'];
    }

    /**
     Save/update field groups
     **/
    add_action('wp_ajax_nopriv_userpro_save_group', 'userpro_save_group');
    add_action('wp_ajax_userpro_save_group', 'userpro_save_group');
    function userpro_save_group(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro;
        $fields = array();
        $fields = $userpro->fields;
        $output = array();

        // Save field group
        $groups = array();
        $groups = get_option('userpro_fields_groups');
        $groups[$_POST['role']][$_POST['group']] = array();
        foreach($_POST as $k => $v){
            $encoding=mb_detect_encoding($v,'auto');
            if($encoding!='ASCII' && $encoding!='UTF-8')
            {
                $v=mb_convert_encoding($v,'UTF-8','auto');
            }
            $v = stripslashes($v);
            if ($k != 'role' && $k != 'group' && $k != 'action'){
                $key = array();
                $key = explode('-',$k,2);
                if ($key[1] != 'options' && $key[1] != 'icon'){
                    $groups[$_POST['role']][$_POST['group']][$key[0]][$key[1]] = $v;
                } elseif ($key[1] == 'options') {
                    $groups[$_POST['role']][$_POST['group']][$key[0]][$key[1]] = preg_split('/[\r\n]+/', $v, -1, PREG_SPLIT_NO_EMPTY);
                } elseif ($key[1] == 'icon') {
                    $fields[$key[0]]['icon'] = $v;
                }
            }
        }

        //Save view group
        unset($groups['view']);
        $groups['view'] = $groups['edit'];

        update_option('userpro_fields_groups', $groups);
        update_option('userpro_fields',$fields);

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     User signup deny
     **/
    add_action('wp_ajax_nopriv_userpro_admin_user_deny', 'userpro_admin_user_deny');
    add_action('wp_ajax_userpro_admin_user_deny', 'userpro_admin_user_deny');
    function userpro_admin_user_deny(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin;
        $userpro_request_admin = new userpro_request_admin();
        $output = array();
        $user_id = $_POST['user_id'];
        $reject_email_subject = userpro_get_option('mail_rejectuser_s');
        $reject_email_message = userpro_get_option('mail_rejectuser');
        $ud = get_userdata($user_id);
        $user_email = $ud->user_email;
        wp_mail($user_email, $reject_email_subject, $reject_email_message);
        $userpro->delete_user($user_id);
        $output['count'] = $userpro_request_admin->get_pending_verify_requests_count_only();

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     User signup approve
     **/
    add_action('wp_ajax_nopriv_userpro_admin_user_approve', 'userpro_admin_user_approve');
    add_action('wp_ajax_userpro_admin_user_approve', 'userpro_admin_user_approve');
    function userpro_admin_user_approve(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin,$userpro_request_admin;
        $output = array();
        $user_id = $_POST['user_id'];
        $role=userpro_get_option('update_role');

        if($role!='no_role')
        {
            $user_obj = new WP_User( $user_id );
            $user_obj->set_role($role);
        }

        $userpro->activate($user_id);
        $output['count'] = $userpro_request_admin->get_pending_verify_requests_count_only();

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Verify a user instantly
     **/
    add_action('wp_ajax_nopriv_userpro_verify_user', 'userpro_verify_user');
    add_action('wp_ajax_userpro_verify_user', 'userpro_verify_user');
    function userpro_verify_user(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin,$userpro_request_admin;
        $output = array();
        $user_id = $_POST['user_id'];
        $userpro->verify($user_id);

        $arr = get_option('userpro_verify_requests');
        if (isset($arr) && is_array($arr)){
            $arr = array_diff($arr, array( $user_id ));
            update_option('userpro_verify_requests', $arr);
        }

        $output['count'] = $userpro_request_admin->get_pending_verify_requests_count_only();

        $output['admin_tpl'] = '<a href="#" class="button button-primary upadmin-unverify-u" data-user="'.$user_id.'">'.userpro_get_badge('verified').'</a>';
        if ($userpro->get_verified_status($user_id) == 0){
            $output['admin_tpl'] .= '<a href="#" class="button upadmin-invite-u" data-user="'.$user_id.'">'.__('Verified Invite','userpro').'</a>';
        }

        do_action('userpro_after_account_verified');

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Unverify a user instantly
     **/
    add_action('wp_ajax_nopriv_userpro_unverify_user', 'userpro_unverify_user');
    add_action('wp_ajax_userpro_unverify_user', 'userpro_unverify_user');
    function userpro_unverify_user(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin,$userpro_request_admin;
        $output = array();
        $user_id = $_POST['user_id'];
        $userpro->unverify($user_id);

        $output['count'] = $userpro_request_admin->get_pending_verify_requests_count_only();

        $output['admin_tpl'] = '<a href="#" class="button upadmin-verify-u" data-user="'.$user_id.'">'.userpro_get_badge('unverified').'</a>';
        if ($userpro->get_verified_status($user_id) == 0){
            $output['admin_tpl'] .= '<a href="#" class="button upadmin-invite-u" data-user="'.$user_id.'">'.__('Verified Invite','userpro').'</a>';
        }

        do_action('userpro_after_account_unverified'); // cache clear

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Block an user instantly
     **/
    add_action('wp_ajax_nopriv_userpro_block_account', 'userpro_block_account');
    add_action('wp_ajax_userpro_block_account', 'userpro_block_account');

    function userpro_block_account(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin;
        $output = array();
        $user_id = $_POST['user_id'];
        $sessions = WP_Session_Tokens::get_instance($user_id);

        // we have got the sessions, destroy them all!
        $sessions->destroy_all();
        $userpro->block_account($user_id);
        $output['admin_tpl'] = '<a href="#" class="button upadmin-unblock-u" data-user="'.$user_id.'">'.userpro_get_badge('blocked').'</a><span class="button" data-user="'.$user_id.'">'.__('Account Blocked','userpro').'</span>';
        do_action('userpro_after_account_blocked');
        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Unblock an user instantly
     **/
    add_action('wp_ajax_nopriv_userpro_unblock_account', 'userpro_unblock_account');
    add_action('wp_ajax_userpro_unblock_account', 'userpro_unblock_account');
    function userpro_unblock_account(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin;
        $output = array();
        $user_id = $_POST['user_id'];
        $userpro->unblock_account($user_id);


        $output['admin_tpl'] = '<a href="#" class="button upadmin-block-u" data-user="'.$user_id.'">'.userpro_get_badge('unblocked').'</a>';
        do_action('userpro_after_account_unblocked');

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }


    add_action('wp_ajax_nopriv_up_delete_invitation', 'deleteUserInvitation');
    add_action('wp_ajax_up_delete_invitation', 'deleteUserInvitation');

    function deleteUserInvitation()
    {
        $userInvitation = new UP_UserInvitation();
        $userInvitation->deleteInvitation($_POST['email']);

        $response = [
          'success' => true,
          'counter' => count($userInvitation->getAll()),
          'message' => __($_POST['email'].' email deleted successfully', 'userpro'),
        ];
        wp_send_json_success($response);
    }

add_action('wp_ajax_nopriv_up_resend_invitation', 'reSendUserInvitation');
add_action('wp_ajax_up_resend_invitation', 'reSendUserInvitation');

function reSendUserInvitation()
{
    $userInvitation = new UP_UserInvitation();
    $userInvitation->resend($_POST['email']);

    $response = [
        'success' => true,
        'message' => __($_POST['email'].' resend email invitation was successfully sent', 'userpro'),
    ];
    wp_send_json_success($response);
}


    /**
     Send a verification invitation
     **/
    add_action('wp_ajax_nopriv_userpro_verify_invite', 'userpro_verify_invite');
    add_action('wp_ajax_userpro_verify_invite', 'userpro_verify_invite');
    function userpro_verify_invite(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro, $userpro_admin;
        $output = array();
        $user_id = $_POST['user_id'];
        $userpro->new_invitation_verify($user_id);

        $output['admin_tpl'] = '<a href="#" class="button upadmin-verify-u" data-user="'.$user_id.'">'.userpro_get_badge('unverified').'</a>';
        if ($userpro->get_verified_status($user_id) == 0){
            $output['admin_tpl'] .= '&nbsp;&nbsp;' . __('Invitation sent!','userpro');
        }

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }

    /**
     Delete a default field
     **/
    add_action('wp_ajax_nopriv_userpro_delete_field', 'userpro_delete_field');
    add_action('wp_ajax_userpro_delete_field', 'userpro_delete_field');
    function userpro_delete_field(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro;
        $field = $_POST['field'];
        $output = array();

        $fields = $userpro->fields;
        unset($fields[$field]);
        update_option('userpro_fields', $fields);

        $output['count'] = userpro_admin_count_fields();

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }


    /**
     Update a default field
     **/
    add_action('wp_ajax_nopriv_userpro_update_field', 'userpro_update_field');
    add_action('wp_ajax_userpro_update_field', 'userpro_update_field');
    function userpro_update_field(){
        if (!current_user_can('manage_options'))
            die(); // admin priv

        global $userpro;
        $output = '';
        $field = $_POST['field'];
        unset($_POST['action']);
        unset($_POST['field']);

        $userpro->update_field($field, $_POST);

        $output=json_encode($output);
        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
    }


    add_action('wp_ajax_nopriv_userpro_disable_rate_me', 'userpro_disable_rate_me');
    add_action('wp_ajax_userpro_disable_rate_me', 'userpro_disable_rate_me');

    function userpro_disable_rate_me(){
    	set_transient('userpro_disable_review',1,360 * HOUR_IN_SECONDS);
    }

    function userpro_check_addon_status( $active_plugins, $slug ){

    	if( isset( $active_plugins[$slug] ) ){
    		return true;
    	}
    	return false;
    }

    add_action('wp_ajax_nopriv_userpro_save_email_template', 'userpro_save_email_template');
    add_action('wp_ajax_userpro_save_email_template', 'userpro_save_email_template');

    function userpro_save_email_template(){

    	$template = $_POST['template'];
    	$template_file = userpro_path.'email-templates/'.$template.'.html';
		$theme_name = get_template();
    	$output = '';
    	if ( ! empty( $template_file ) ) {

    		$theme_file = get_stylesheet_directory() . '/userpro/email-templates/'.$template.'.html';

    		if ( wp_mkdir_p( dirname( $theme_file ) ) && ! file_exists( $theme_file ) ) {

    			copy( $template_file, $theme_file );
    		}
    	}
    	ob_start();
    	?>
    	<div class="up-template-overridden">
			<?php echo sprintf(esc_attr__('This template has been overridden by your theme and can be found in: %s ',"userpro"),"$theme_name/userpro/email-templates/$template.html"); ?>
			<input type="button" value="Delete Email Template" class="up-delete-email-template button" data-template="<?php echo $template;?>" />
			<input type="button" value="Preview Template" class="up-preview-template button" data-template="<?php echo $template;?>" />
		</div>
    	<?php
    	$output = ob_get_contents();
    	ob_end_clean();
    	echo json_encode(array('output'=>$output));
    	die();
    }

    add_action('wp_ajax_nopriv_userpro_delete_email_template', 'userpro_delete_email_template');
    add_action('wp_ajax_userpro_delete_email_template', 'userpro_delete_email_template');

    function userpro_delete_email_template(){
    	$template = $_POST['template'];
    	$theme_name = get_template();
    	$output = '';
    	if( !empty( $template ) ){
    		$theme_file = get_stylesheet_directory() . '/userpro/email-templates/'.$template.'.html';
    		if ( file_exists( $theme_file ) ) {
				unlink( $theme_file );
    		}
    	}
    	ob_start();
    	?>
    	<div class="up-template-override">
			<?php echo sprintf(esc_attr__('To override and edit this email template copy %s to your theme folder: %s ',"userpro"),"userpro/email-templates/$template.html","$theme_name/userpro/email-templates/$template.html"); ?>
			<input type="button" value="Copy template to theme" class="up-copy-mail-template button" data-template="<?php echo $template;?>" />
    		<input type="button" value="Preview Template" class="up-preview-template button" data-template="<?php echo $template;?>" />
    	</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		echo json_encode(array('output'=>$output));
    	die();
    }

    add_action('wp_ajax_nopriv_userpro_preview_email', 'userpro_preview_email');
    add_action('wp_ajax_userpro_preview_email', 'userpro_preview_email');

    function userpro_preview_email(){
    	$template = $_POST['template'];
    	$html = '';
		if (locate_template('userpro/email-templates/' . $template . '.html') != '') {
			$template_url =  get_stylesheet_directory_uri() . '/userpro/email-templates/' . $template . '.html';
		} else {
			$template_url = userpro_url . 'email-templates/' . $template . '.html';
		}
		$html = "<div class='userpro-preview-container'><iframe src='".$template_url."' width='715px'></iframe></div>";
		echo json_encode(array('output'=>$html));
		die();
    }

    add_action( 'wp_ajax_userpro_service_request', 'userpro_service_request');
    add_action( 'wp_ajax_nopriv_userpro_service_request', 'userpro_service_request');

    function userpro_service_request(){

        $to = 'userpro.services@gmail.com';
        $f_email = $_POST['email'];
        $type = isset( $_POST['type'] )?$_POST['type']:'';
        $module = isset( $_POST['module'] )?$_POST['module']:'';
        $description = isset( $_POST['detailed-description'] )?$_POST['detailed-description']:'';

        $headers = 'From: ' . userpro_get_option( 'mail_from_name' ) . ' <' . $f_email . '>' . "\r\n";
        $subject = "Service Request : {$module} {$type}";
        $body = 'FROM: '.$f_email."\r\n";
        $body .= "Subject: {$module} {$type}" . "\r\n\r\n";
        $body .= "Message Body:\r\n";
        $body .= $description;
        wp_mail( $to, $subject, $body, $headers );

        $output = 'Thank you for contacting us, our Services Team will get back to you at earliest.';


      echo $output;
      die();
}
