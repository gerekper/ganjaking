<?php

/**
 * @author Hassan Jahangiri
 * @copyright 2013
 */

(array) $counter = get_option('hmwp_spam_counter');
(array) $blocked_req = get_option('trust_network_rules');
if (!$blocked_req || ($blocked_req && !isset($blocked_req['counter'])))
    $blocked_req['counter'] = 0;

$blocked_counter = $blocked_req['counter'];
$blocked_counter = ($blocked_counter > 5) ? '<br><strong> Blocked Requests: '.$blocked_counter.'</strong>' : '';

function is_mu_subdir(){

    if (is_multisite() && ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes')))
        return false;
    if (!is_multisite())
        return false;
    return true;
}

if (!isset($counter['1']) || !$counter['1'])
    $counter['1']=0;

if (!isset($counter['2']) || !$counter['2'])
    $counter['2']=0;


$spam_counter=  $counter['1'] + $counter['2'];
$spam_counter = ($spam_counter>1) ? ' [Spam Counter: <strong>'.$spam_counter.'</strong>]' : '';

$sections = array(
    array(
        'id' => 'start',
        'title' => __( 'Start', self::slug )
    ),
    array(
        'id' => 'general',
        'title' => __( 'Hiding', self::slug )
    ),
    // array(
    //     'id' => 'advanced-general',
    //     'title' => __( 'General (A)', self::slug )
    // ),
    array(
        'id' => 'permalink',
        'title' => __( 'Permalinks', self::slug )
    ),
    
    array(
        'id' => 'ids',
        'title' => __( 'Protection', self::slug )
    ),
    array(
        'id' => 'source',
        'title' => __( 'Cleanup', self::slug )
    ),


    /* array(
         'id' => 'trustip',
         'title' => __( 'Trust IP', self::slug )
     ),*/

    array(
        'id' => 'replaces',
        'title' => __( 'Replace', self::slug )
    )
    // ,
    // array(
    //     'id' => 'others',
    //     'title' => __( 'Others', self::slug )
    // )
);

$admin_email = get_option('admin_email');

$pre_made_settings = $this->pre_made_settings();

$fields['start'] =
    array(
        array(
            'name' => 'li',
            'label' => __( 'Purchase Code', self::slug ),
            'desc' => __( 'Enter your Purchase Code to make sure everything work as expected. <a target="_blank" href="http://wpwave.com/envato/purchase_code_1200.png">Get it.</a>', self::slug ),
            'type' => 'text',
            'default' => $this->opt('li'),
            'class' =>''

        ),
        array(
            'name' => 'import_options',
            'label' => __( 'Import Options', self::slug ),
            'desc' => __( 'Paste your settings code below or choose a pre-made settings scheme.', self::slug ),
            'type' => 'import',
            'default' => '',
            'class' =>'',
            'options' => array(
                'Light Privacy - Most Compatibilty' => $pre_made_settings['low_privacy'],
                'Medium Privacy - More Compatibilty (Recommended)' => $pre_made_settings['medium_privacy'],
                'High Privacy - Less Compatibility *' => $pre_made_settings['high_privacy'],
            )
        )
    ,
        array(
            'name' => 'export_options',
            'label' => __( 'Export Options', self::slug ),
            'desc' => __( 'Copy your export code and save it somewhere for later use.', self::slug ),
            'type' => 'export',
            'default' => '',
            'class' =>''

        )


    );



if (stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') || stristr($_SERVER['SERVER_SOFTWARE'], 'wpengine') || isset($_GET['nginx_config']) ){
    $fields['start'][]  = array(
        'name' => 'nginx_config',
        'label' => __( 'Nginx Configuration', self::slug ),
        'desc' =>  '<a target="_blank" href="'.add_query_arg(array('die_message'=>'nginx')).'" class="button">'.__('Nginx Configuration', self::slug).'</a><br><span class="description"> It\'s require to config Nginx to get all features of the plugin</span>',
        'type' => 'custom',
        'default' => '',
        'class' =>''

    );
}

if (stristr($_SERVER['SERVER_SOFTWARE'], 'iis') || stristr($_SERVER['SERVER_SOFTWARE'], 'Windows') || isset($_GET['iis_config']) ){
    $fields['start'][]  = array(
        'name' => 'iis_config',
        'label' => __( 'Windows Configuration (IIS)', self::slug ),
        'desc' =>  '<a target="_blank" href="'.add_query_arg(array('die_message'=>'iis')).'" class="button">'.__('IIS Configuration', self::slug).'</a><br><span class="description"> It\'s require to config web.config file to get all features of the plugin</span>',
        'type' => 'custom',
        'default' => '',
        'class' =>''

    );
}

if (function_exists('bulletproof_security_load_plugin_textdomain') || isset($_GET['single_config']) ){
    $fields['start'][]  = array(
        'name' => 'single_config',
        'label' => __( 'Manual Configuration', self::slug ),
        'desc' =>  '<a target="_blank" href="'.add_query_arg(array('die_message'=>'single')).'" class="button">'.__('Manual Configuration', self::slug).'</a><br><span class="description"> In rare cases you need to configure .htaccess file manually</span>',
        'type' => 'custom',
        'default' => '',
        'class' =>''

    );
}

if (is_multisite() || (isset($_GET['multisite_config']) && $_GET['multisite_config'])){
    $fields['start'][]  = array(
        'name' => 'multisite_config',
        'label' => __( 'Multisite Configuration', self::slug ),
        'desc' =>  '<a target="_blank" href="'.add_query_arg(array('die_message'=>'multisite')).'" class="button">'.__('Multisite Configuration', self::slug).'</a><br><span class="description"> It\'s require to config .htaccess file to get all features of the plugin</span>',
        'type' => 'custom',
        'default' => '',
        'class' =>''

    );
}

$fields['start'][]=
    array(
        'name' => 'debug_report',
        'label' => __( 'Debug Report', self::slug ),
        'desc' => __( 'Provide above report to support team to get better and faster service.', self::slug ),
        'type' => 'debug_report',
        'default' => '',
        'class' =>''

    );

/* $fields['start'][]  = array(
     'name' => 'vote_us',
     'label' => __( 'Spread it (please!)', self::slug ),
     'desc' =>  '<a target="_blank" href="http://codecanyon.net/item/hide-my-wp-no-one-can-know-you-use-wordpress/4177158" class="button">'.__(' ★ Vote it', self::slug).'</a> ' . '  <a target="_blank" href="http://twitter.com/home?status=Hide+My+WP+::+No+one+can+know+you+use+WordPress!+http://codecanyon.net/item/hide-my-wp-no-one-can-know-you-use-wordpress/4177158?rate_it=true" class="button">'.__(' ♥  Tweet it', self::slug).'</a>',
     'type' => 'custom',
     'default' => '',
     'class' =>''

 );*/
/*
$fields['start'][]  = array(
    'name' => 'video',
    'label' => __( ' Video Tutorials ', self::slug ),
    'desc' =>  '<a target="_blank" href="http://support.wpwave.com/videos" class="button">'.__(' ★ Video Tutorials', self::slug).'</a> | <a target="_blank" href="http://support.wpwave.com/videos" class="button">'.__(' ★ Video Tutorials', self::slug).'</a><br/> <span class="description"> Coming Soon...</span>',
    'type' => 'custom',
    'default' => '',
    'class' =>''

);*/

$fields['start'][]  = array(
    'name' => 'help',
    'label' => __( 'Quick Fix Guide', self::slug ),
    'desc' =>  '<a target="_blank" href="http://support.wpwave.com/videos" class="button">'.__(' ▸ Video Tutorials', self::slug).'</a><ol><li>Make sure you have a <strong>writable htaccess</strong> file (if you use Apache) or configured your web server manually (if you use Nginx, IIS or enabled multi-site). Follow installation guide for details.</li><li>Disable features which have an <strong>asterisk(*)</strong> in their names or use a more compatible settings scheme.</li><li>See <a target="_blank" href="http://codecanyon.net/item/hide-my-wp-no-one-can-know-you-use-wordpress/4177158/support">  <strong>Frequently Asked Questions</strong></a> for common issues.</li><li>To remove remained WP footprints use <strong>Replace Tools</strong></li><li>Use our dedicted <strong><a href="http://support.wpwave.com/forums/forum/hide-my-wp?ref=start_tab" target="_blank">Support Forum</a></strong> for other issues</li><!--<li>For better support provide debug report or login details (if possible) via a message (and not comment) using author\'s profile page in Codecanyon.</li>--></ol>',
    'type' => 'custom',
    'default' => '',
    'class' =>''

);

if (get_option(self::slug.'_undo') ){
    $fields['start'][]  = array(
        'name' => 'undo',
        'label' => __( 'Undo Settings', self::slug ),
        'desc' => $this->undo_config(),
        'type' => 'custom',
        'default' => '',
        'class' =>''
    );
}
$fields['start'][] = array(
    'name' => 'uninstall_hmwp_data',
    'label' => __( 'Clean Uninstall', self::slug ),
    'desc' => __( 'Remove all saved settings when uninstalling the plugin', self::slug ),
    'type' => 'checkbox',
    'default' => '',
    'class' =>''
);

$fields['general'] =
    array(                
        array(
            'name' => 'hide_wp_login',
            'label' => __( 'Hide Login Page', self::slug ),
            'desc' => __( 'Hide wp-login.php. [<b>Important:</b> You need to remember new address to login!]', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'opener'
        )
    ,

        array(
            'name' => 'login_query',
            'label' => __( 'Login Query', self::slug ),
            'desc' => __( 'Login parameter for protected login address (default: hide_my_wp) e.g. wp-login.php?hide_my_wp=ADMIN_KEY', self::slug ),
            'type' => 'text',
            'default' => self::slug,
            'class' =>'open_by_hide_wp_login'
        )
    ,

        array(
            'name' => 'admin_key',
            'label' => __( 'Admin Login Key', self::slug ),
            'desc' => sprintf(__( '<br>Current Login url: %s <a title="New WP Login" href="%s">[Link]</a> (Save changes to update)<br>Want something like "/login"? Change New Login Path (Permalinks tab)</a>', self::slug ), '<b>/wp-login.php?'.$this->opt('login_query').'='.$this->opt('admin_key').'</b>', site_url('wp-login.php?'.$this->opt('login_query').'='.$this->opt('admin_key')) ),
            'type' => 'text',
            'default' => '1234',
            'class' =>'open_by_hide_wp_login'
        )
    ,
        array(
            'name' => 'hide_wp_admin',
            'label' => __( 'Hide Admin', self::slug ),
            'desc' => __( 'Hide wp-admin folder and its files for untrusted users.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,

        array(
            'name' => 'spy_notifier',
            'label' => __( 'Spy Notify', self::slug ),
            'desc' => __( 'Send an email to site admin whenever someone visits 404 page!', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,        

        array(
            'name' => 'antispam',
            'label' => __( 'Anti-Spam', self::slug ),
            'desc' => __( 'Enable HMWP anti-spam system.', self::slug ) . $spam_counter,
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,
        array(
            'name' => 'full_hide',
            'label' => __( 'Full Hide', self::slug ),
            'desc' => __( 'Enables to hide assets paths (disable if you use more than one domain)', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'permalink_req'
        )
    ,
        array(
            'name' => 'hide_online_detectors',
            'label' => __( 'Hide Online Detectors', self::slug ),
            'desc' => __( 'Enables hiding from online detectors like whatcms.org, isitwp.com and <a href="https://detectmywp.com/" target="_blank">detectmywp.com</a>', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'permalink_req'
        )
    ,

        array(
            'name' => 'hide_other_wp_files',
            'label' => __( 'Hide Other Files', self::slug ),
            'desc' => __( 'Hide license.txt, wp-includes, wp-content/debug.log, etc.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'permalink_req'
        )
    ,

        array(
            'name' => 'disable_directory_listing',
            'label' => __( 'Directory List', self::slug ),
            'desc' => __( 'Disable directory listing and hide other .txt files.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'permalink_req'
        )
    ,
        array(
            'name' => 'disable_canonical_redirect',
            'label' => __( 'Canonical Redirect', self::slug ),
            'desc' => __( 'Disable canonical redirect. This is require when you want to use URL queries.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''

        ),

        array(
            'name' => 'hide_admin_bar',
            'label' => __( 'Hide Admin Bar', self::slug ),
            'desc' => __( 'Hide admin bar for untrusted users.', self::slug ),
            'type' => 'checkbox',
            'default' => 'on',
            'class' =>''
        )

    ,


        
        /*   array(
              'name' => 'replace_in_html',
              'label' => __( 'Replace in HTML', self::slug ),
              'desc' => __( 'Replace words in HTML output. Case-sensitive. Order is important. One per line. e.g. old=new <br/> Use [equal] and [bslash] when \'=\' and "\" is in keywords. Do not use this to change URLs. <br/>', self::slug ),
              'type' => 'textarea',
              'default' => '',
              'class' =>''
              )
           ,
           array(
              'name' => 'replace_urls',
              'label' => __( 'Replace URLs', self::slug ),
              'desc' => __( 'Replace or rename URLs in HTML output. Case-sensitive. Order is important. One per line.<br> Use \'orginal\' path and \'==\' e.g wp-content/plugins/woocommerce/assets/css/woocommerce.css==ec.css<br>Use \'nothing_404_404\' as second part to make it unavailable.<br>Add \'/\' to the end of first part to change all files in that folder<br>Use \'Full Page\' replace mode if you experience conflict with other replaces', self::slug ),
              'type' => 'textarea',
              'default' => '',
              'class' =>''
              )*/

 array(
            'name'=>'separator',
            'label' => '',
            'desc' => '<div style="border-top: 1px solid #ccc;"></div><br/>',
            'type' => 'html',
            'class' =>''
        )
    ,

    array(
            'name' => 'custom_404',
            'label' => __( '404 Page Template', self::slug ),
            'desc' => __( '', self::slug ),
            'type' => 'radio',
            'default' => '0',
            'class' =>'opener',
            'options' => array(
                '0' => 'Use default 404 page from theme',
                '1' => 'Choose a custom page'
            )
        )
    ,
        array(
            'name' => 'custom_404_page',
            'label' => __( 'Custom 404 Page', self::slug ),
            'desc' => __( 'We use this as 404 page.', self::slug ),
            'type' => 'pagelist',
            'default' => '',
            'class' =>'open_by_custom_404_1'
        )
    ,
    array(
            'name' => 'trusted_user_roles',
            'label' => __( 'Trusted User Roles', self::slug ),
            'desc' => __( 'Choose trusted user roles. (Administrator are trusted by default)', self::slug ),
            'type' => 'rolelist',
            'options' => array(),
            'class' =>''

        )
    ,
        array(
            'name' => 'replace_mode',
            'label' => __( 'Replace Mode', self::slug ),
            'desc' => __( 'How should we replace old URLs? (Use Full mode with cache)', self::slug ),
            'type' => 'select',
            'default' => 'quick',
            'class' =>'',
            'options' => array(
                'quick' => __('Partial (Quick) *', self::slug),
                'safe' =>  __('Full Page', self::slug)
            )
        )
    ,
    array(
            'name' => 'customized_htaccess',
            'label' => __( 'Customized htaccess', self::slug ),
            'desc' => __( 'Choose this option only if you have a customized htaccess and don\'t want to allow HMWP update it frequently. You need to configure HMWP manually. <a target="_blank" href="'.add_query_arg(array('die_message'=>'single')).'" class="button">'.__('Manual Configuration', self::slug).'</a>', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,
        array(
            'name'=>'separator',
            'label' => '',
            'desc' => '<div style="border-top: 1px solid #ccc;"></div><br/>',
            'type' => 'html',
            'class' =>''
        )
    ,
        array(
            'name' => 'cdn_path',
            'label' => __( 'CDN Path', self::slug ),
            'desc' => __( 'Enter your main CDN address', self::slug ),
            'type' => 'text',
            'default' => '',
            'class' =>''
        ),        
        array(
            'name' => 'email_from_name',
            'label' => sprintf( __( 'Email sender name <br/><span style="color: red;font-size:12px;">%s</span>.', self::slug ), 'Will be deprecated in next release' ),
            'desc' => __( 'e.g. John Smith', self::slug ),
            'type' => 'text',
            'default' => '',
            'class' =>''
        ),
        array(
            'name' => 'email_from_address',
            'label' => sprintf( __( 'Email sender address <br/><span style="color: red;font-size:12px;">%s</span>.', self::slug ), 'Will be deprecated in next release' ),
            'desc' => __( 'e.g. info@domain.com', self::slug ),
            'type' => 'text',
            'default' => '',
            'class' =>''
        ),
    array(
            'name' => 'avoid_direct_access',
            'label' => __( 'Hide PHP Files', self::slug ),
            'desc' => __( 'Avoid direct access to php files (except wp-admin) *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'opener'
        ),
        array(
            'name' => 'direct_access_except',
            'label' => __( 'Exclude Files', self::slug ),
            'desc' => __( 'Except these PHP files (or folders). Separate with ,', self::slug ),
            'type' => 'textarea',
            'default' => 'index.php, wp-comments-post.php, wp-includes/js/tinymce/wp-tinymce.php, xmlrpc.php, wp-cron.php, wp-admin/upgrade.php',
            'class' =>'open_by_avoid_direct_access'
        )
    ,
        array(
            'name' => 'exclude_theme_access',
            'label' => __( 'Exclude Theme', self::slug ),
            'desc' => __( 'Add theme files to above list. Use this if you experience incompatibility with your theme.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'open_by_avoid_direct_access'
        )
    ,
        array(
            'name' => 'exclude_plugins_access',
            'label' => __( 'Exclude Plugins', self::slug ),
            'desc' => __( 'Add plugins files to above list. Use this if you experience incompatibility with plugins.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'open_by_avoid_direct_access'
        )
    ,
        /*array(
            'name' => 'hide_whatcms_detection',
            'label' => __( 'Hide whatcms Detection', self::slug ),
            'desc' => __( 'Avoid detection in whatcms site', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'opener'
        ), */
);




$fields['permalink'] = array(
    array(
        'name' => 'new_theme_path',
        'label' => __( 'New theme path', self::slug ),
        'desc' => __( 'e.g. "/template"', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    ),

    array(
        'name' => 'new_style_name',
        'label' => __( 'New style name', self::slug ),
        'desc' => __( 'e.g. "main.css" (Require New theme name)', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    ),
    
    array(
        'name' => 'style_expiry_days',
        'label' => __( 'Style Expiry Header', self::slug ),
        'desc' => __( '(in days) for browser caching', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    ),


    

    array(
            'name' => 'auto_config_plugins',
            'label' => __( 'Auto Configuration', self::slug ),
            'desc' => __( 'Automatically hide popular plugins (woocommerce, elementor, gravity forms, jetpack, wprocket)', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>' permalink_req'
        ),

    

    array(
        'name' => 'new_include_path',
        'label' => __( 'New wp-includes path', self::slug ),
        'desc' => __( 'e.g. "/lib"', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    )
,
    array(
        'name' => 'new_plugin_path',
        'label' => __( 'New plugin path', self::slug ),
        'desc' => __( 'e.g. "/modules"', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    ),


    array(
        'name' => 'rename_plugins',
        'label' => __( 'Rename Plugins', self::slug ),
        'desc' => (is_multisite()) ? __( 'Change plugins folder both in sitewide and main blog) with a codename (Require new plugin path).', self::slug ) : __( 'Change each plugin folder name with a codename (Require new plugin path).', self::slug ),
        'type' => 'select',
        'default' => '',
        'class' =>'permalink_req',
        'options' => array(
            '' => __( 'Disable Plugin Rename', self::slug ),
            'on' => __( 'Only Active Plugins (Quick) *', self::slug ),
            'all' => __( 'All Plugins', self::slug )
        )


    )
,
    array(
        'name' => 'new_upload_path',
        'label' => __( 'New upload path', self::slug ),
        'desc' => __( 'e.g. "/file". <br>If your theme or your image plugins uses <strong>TimThumb</strong> (check source code) <a href="http://codecanyon.net/item/hide-my-wp-no-one-can-know-you-use-wordpress/4177158/faqs/16224">read here</a>.', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    )
,
    array(
        'name' => 'replace_comments_post',
        'label' => __( 'Post Comment', self::slug ),
        'desc' => __( 'Change "wp_comments_post.php" URL (e.g. "/user_submit" or "/folder/user_submit.php").', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    )
,

    array(
        'name' => 'replace_admin_ajax',
        'label' => __( 'AJAX URL', self::slug ),
        'desc' => __( 'Change wp-admin/admin_ajax.php URL (e.g. "/ajax" or "ajax.php").', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    )
,
    array(
        'name' => 'new_content_path',
        'label' => __( 'New wp-content path', self::slug ),
        'desc' => __( 'e.g. "/inc" You usually do not need to use it. Only useful for some kinds of plugins (cache, gallery)', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    ),


    array(
        'name' =>'separator2',
        'label' =>'',
        'desc' => '<div style="border-top:1px solid #ccc;"></div><br/>',
        'type' => 'html',
        'class'=>'permalink_req'
    )
,
    array(
        'name' => 'new_login_path',
        'label' => __( 'Change Login URL', self::slug ),
        'desc' => __( 'Change wp-login.php e.g. login', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>'permalink_req'
    )
,
    array(
        'name' => 'new_admin_path',
        'label' => __( 'New wp-admin Path*', self::slug ),
        'desc' => (is_mu_subdir()) ? 'Not available for sub-directory multisite' : __( '<br> Change "/wp-admin" (e.g. panel, cp). <br> <strong>REQUIRE</strong> to update wp-config.php manually <br><strong><span style="color:red">WARNING:</span></strong> You <strong>MUST</strong> follow messages instantly otherwise you might unable to login' , self::slug) ,
        'type' => (is_mu_subdir()) ? 'custom' : 'text' ,
        'default' => '',
        'class' =>'permalink_req'

    ),



    array(
        'name' =>'separator3',
        'label' =>'',
        'desc' => '<div style="border-top:1px solid #ccc;"></div><br/>',
        'type' => 'html',
        'class'=>'permalink_req'
    )
,
    array(
        'name' => 'api_disable',
        'label' => __( 'API', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '0',
        'class' =>'opener',
        'options' => array(
            '0' => __( 'Enable REST API', self::slug ),
            '1' => __( 'Disable REST API', self::slug )
        )
    ),
    array(
        'name' => 'api_base',
        'label' => __( 'API Base', self::slug ),
        'desc' => __( 'Change "/wp-json/" (e.g. rest, api). NOTE: Please flush the rewrite rule.', self::slug ),
        'type' => 'text',
        'default' => '',
        'class' =>' open_by_api_disable_0 permalink_req'
    )
,
    array(
        'name' => 'api_query',
        'label' => __( 'API Query', self::slug ),
        'desc' => __( 'Change /?rest_route=1 (e.g. rest_api).', self::slug ),
        'type' => 'text',
        'default' => 'rest_route',
        'class' =>' open_by_api_disable_0'
    )
,
    array(
        'name' => 'author_enable',
        'label' => __( 'Author', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __( 'Enable Authors URL', self::slug ),
            '0' => __( 'Disable Authors URL', self::slug )
        )
    ),
    array(
        'name' => 'author_base',
        'label' => __( 'Author Base', self::slug ),
        'desc' => __( 'Change "/author/username" (e.g. user, profile, members/editrs/).', self::slug ),
        'type' => 'text',
        'default' => '/author',
        'class' =>' open_by_author_enable_1 permalink_req'
    )
,
    array(
        'name' => 'author_query',
        'label' => __( 'Author Query', self::slug ),
        'desc' => __( 'Change /?author=1 and /?author_name=username (e.g. u, user, member).', self::slug ),
        'type' => 'text',
        'default' => 'author',
        'class' =>' open_by_author_enable_1'
    )
,
    array(
        'name' => 'author_without_base',
        'label' => __( 'Author without base', self::slug ),
        'desc' => __( 'Use username directly and without base (e.g. domain.com/admin) *', self::slug ),
        'type' => 'checkbox',
        'default' => '',
        'class' =>'open_by_author_enable_1 permalink_req'
    )
,
    array(
        'name' => 'feed_enable',
        'label' => __( 'Feeds', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __( 'Enable Feeds URL', self::slug ),
            '0' => __( 'Disable All Feeds URL' , self::slug )
        )
    ),
    array(
        'name' => 'feed_base',
        'label' => __( 'Feed Base', self::slug ),
        'desc' => __( 'Change /feed (e.g. xml, rss, index.xml).', self::slug ),
        'type' => 'text',
        'default' => '/feed',
        'class' =>' open_by_feed_enable_1 permalink_req'
    )
,

    array(
        'name' => 'feed_query',
        'label' => __( 'Feed Query', self::slug ),
        'desc' => __( 'Change /?feed=rss2 (e.g. xml, rss, sitefeed).', self::slug ),
        'type' => 'text',
        'default' => 'feed',
        'class' =>' open_by_feed_enable_1'
    )
,

    array(
        'name' => 'post_enable',
        'label' => __( 'Post', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __('Enable Posts URL', self::slug ),
            '0' => __('Disable Posts URL', self::slug )
        )
    ),
    array(
        'name' => 'post_base',
        'label' => __( 'Post Permalink', self::slug ),
        'desc' => (is_multisite()) ? __( 'Use default WP permalink page to change post permalink.', self::slug) : __( 'Change default WP post permalink. <a href="http://codex.wordpress.org/Using_Permalinks">[Get Tags]</a>.', self::slug ),
        'type' => (is_multisite()) ? 'custom' : 'text',
        'default' => get_option('permalink_structure'),
        'class' =>' open_by_post_enable_1 permalink_req'
    )
,
    array(
        'name' => 'post_query',
        'label' => __( 'Post Query', self::slug ),
        'desc' => __( 'Change /?p=1 (e.g. article_id, news_id or pid).', self::slug ),
        'type' => 'text',
        'default' => 'p',
        'class' =>' open_by_post_enable_1'
    )
,
    array(
        'name' => 'page_enable',
        'label' => __( 'Page', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __('Enable Pages URL', self::slug ),
            '0' => __('Disable Pages URL', self::slug )
        )
    ),
    array(
        'name' => 'page_base',
        'label' => __( 'Page Base', self::slug ),
        'desc' => __( 'Change /sample-page to /X/sample-page (e.g. pages, static).', self::slug ),
        'type' => 'text',
        'default' => '/',
        'class' =>' open_by_page_enable_1 permalink_req'
    )
,
    array(
        'name' => 'page_query',
        'label' => __( 'Page Query', self::slug ),
        'desc' => __( 'Change /?page_id=1 or /?page_name=about (e.g. pages).', self::slug ),
        'type' => 'text',
        'default' => 'page_id',
        'class' =>' open_by_page_enable_1'
    )
,

    array(
        'name' => 'paginate_enable',
        'label' => __( 'Paginate', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __( 'Enable Paginates URL', self::slug ),
            '0' => __( 'Disable Paginates URL', self::slug )
        )
    ),
    array(
        'name' => 'paginate_base',
        'label' => __( 'Paginate Base', self::slug ),
        'desc' => __( 'Change /page/2 (e.g. pages, go).', self::slug ),
        'type' => 'text',
        'default' => '/page',
        'class' =>' open_by_paginate_enable_1 permalink_req'
    )
,
    array(
        'name' => 'paginate_query',
        'label' => __( 'Paginate Query', self::slug ),
        'desc' => __( 'Change /?paged=2.', self::slug ),
        'type' => 'text',
        'default' => 'paged',
        'class' =>' open_by_paginate_enable_1'
    )
,
    array(
        'name' => 'category_enable',
        'label' => __( 'Category', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __('Enable Categories URL', self::slug ),
            '0' => __('Disable Categories URL', self::slug )
        )
    ),
    array(
        'name' => 'category_base',
        'label' => __( 'Category Base', self::slug ),
        'desc' => (is_multisite()) ? __( 'Use default WP permalink page to change category base.', self::slug) : __( 'Change /category/uncategorized. (e.g. topic, all).', self::slug ),
        'type' => (is_multisite()) ? 'custom' : 'text',
        'default' => get_option('category_base'),
        'class' =>' open_by_category_enable_1 permalink_req'
    )
,
    array(
        'name' => 'category_query',
        'label' => __( 'Category Query', self::slug ),
        'desc' => __( 'Change /?cat=1 or /?category_name=uncategorized (e.g. topic).', self::slug ),
        'type' => 'text',
        'default' => 'cat',
        'class' =>' open_by_category_enable_1'
    )
,

    array(
        'name' => 'tag_enable',
        'label' => __( 'Tag', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __('Enable Tags URL', self::slug ),
            '0' => __('Disable Tags URL', self::slug )
        )
    ),
    array(
        'name' => 'tag_base',
        'label' => __( 'Tag Base', self::slug ),
        'desc' => (is_multisite()) ? __( 'Use default WP permalink page to change tag base.', self::slug) : __( 'Change /tag/tag1 (e.g. keyword, find).', self::slug ),
        'type' => (is_multisite()) ? 'custom' :'text',
        'default' => get_option('tag_base'),
        'class' =>' open_by_tag_enable_1 permalink_req'
    )
,
    array(
        'name' => 'tag_query',
        'label' => __( 'Tag Query', self::slug ),
        'desc' => __( 'Change /?tag=tag1 (e.g. keyword, find).', self::slug ),
        'type' => 'text',
        'default' => 'tag',
        'class' =>' open_by_tag_enable_1'
    )
,

    array(
        'name' => 'search_enable',
        'label' => __( 'Search', self::slug ),
        'desc' => '',
        'type' => 'select',
        'default' => '1',
        'class' =>'opener',
        'options' => array(
            '1' => __('Enable Search', self::slug ),
            '0' => __('Disable Search' , self::slug )
        )
    ),
    array(
        'name' => 'search_base',
        'label' => __( 'Search Base', self::slug ),
        'desc' => __( 'Change /search/keyword (e.g. find, s, dl).', self::slug ),
        'type' => 'text',
        'default' => '/search',
        'class' =>' open_by_search_enable_1 permalink_req'
    )
,
    array(
        'name' => 'search_query',
        'label' => __( 'Search Query', self::slug ),
        'desc' => __( 'Change /?s=keyword (e.g. find, s, dl).', self::slug ),
        'type' => 'text',
        'default' => 's',
        'class' =>' open_by_search_enable_1'
    )
,
    array(
        'name' => 'nice_search_redirect',
        'label' => __( 'Search base redirect', self::slug ),
        'desc' => __( 'Redirect all search queries to permalink (e.g. /search/test instead /?s=test).', self::slug ),
        'type' => 'checkbox',
        'default' => '',
        'class' =>'open_by_search_enable_1 permalink_req'
    )
,

    array(
        'name' => 'disable_archive',
        'label' => __( 'Disable Archive', self::slug ),
        'desc' => __( 'Disable archive queries (yearly, monthly or daily archives).', self::slug ),
        'type' => 'checkbox',
        'default' => '',
        'class' =>''
    )
,

    array(
        'name' => 'disable_other_wp',
        'label' => __( 'Disable Other WP', self::slug ),
        'desc' => __( 'Disable other WordPress queries like post type, taxonamy, attachments, comment page etc. Post types may be used by themes or plugins. *', self::slug ),
        'type' => 'checkbox',
        'default' => '',
        'class' =>''
    )

);



//others tab
$fields['source'] =
    array(

        array(
            'name' => 'remove_html_comments',
            'label' => __( 'Minify HTML', self::slug ),
            'desc' => __( 'Remove comments and whitespaces from HTML (Use with cache).', self::slug ),
            'type' => 'select',
            'default' => '',
            'class' =>'',
            'options' => array(
                '' => 'Disable Minify',
                'simple' => __('Simple Minify', self::slug),
                'quick' => __('Quick Minify *', self::slug),
                'safe' =>  __('Safe Minify *', self::slug)
            )
        ),

        array(
        'name' => 'minify_new_style',
        'label' => __( 'Minify style', self::slug ),
        'desc' => __( 'Remove theme info and other comments from stylesheet. (Require new style name)(Use with cache!).', self::slug ),
        'type' => 'select',
        'default' => '',
        'class' =>'permalink_req' ,
        'options' => array(
            '' => 'Disable Minify',
            'quick' => __('Quick Minify', self::slug),
            'safe' =>  __('Safe Minify', self::slug)
        )
    ),

        array(
            'name' => 'replace_javascript_path',
            'label' => __( 'Replace \/ URLs', self::slug ),
            'desc' => 'Choose if you see Javascript URLs (e.g. \/wp-content\/themes)',
            'type' => 'select',
            'default' => '1',
            'class' =>'opener',
            'options' => array(
                '0' => __( 'Disable JS URLs', self::slug ),
                '1' => __( 'Only for theme', self::slug ),
                '2' => __( 'For theme and plugins', self::slug ),
                '3' => __( 'For theme, plugins and uploads', self::slug ),
            )
        ),


        /* array( //prints styles will be removed but not included in the external file / IE style can be removed but will be added to the file, too now way to know it's ie style or not and src scripts require new theme path
             'name' => 'auto_internal',
             'label' => __( 'Internal JS/CSS', self::slug ),
             'desc' => 'Move internal css/js codes to a separate file automatically (Require New Theme Path)*',
             'type' => 'select',
             'default' => '1',
             'class' =>'',
             'options' => array(
                 '0' => __( 'Disable', self::slug ),
                 '1' => __( 'CSS Only', self::slug ),
                 '2' => __( 'JS Only *', self::slug ),
                 '3' => __( 'Both CSS and JS *', self::slug ),
             )
         ),
 */

        array(
            'name' => 'remove_feed_meta',
            'label' => __( 'Feed Meta', self::slug ),
            'desc' => __( 'Remove auto-generated feeds from header.', self::slug ),
            'type' => 'checkbox',
            'default' => 'on',
            'class' =>'open_by_feed_enable_1'
        )
    ,
        array(
            'name' => 'remove_other_meta',
            'label' => __( 'Other Meta', self::slug ),
            'desc' => __( 'Remove other header metas like short link, previous/next links, emojis, etc. (emjis still work in modern browsers)', self::slug ),
            'type' => 'checkbox',
            'default' => 'on',
            'class' =>''
        )
    ,
        array(
            'name' => 'remove_default_description',
            'label' => __( 'Default Tagline', self::slug ),
            'desc' => __( 'Remove \'Just another WordPress blog\' from your feed.', self::slug ),
            'type' => 'checkbox',
            'default' => 'on',
            'class' =>''
        )
    ,



        array(
            'name' => 'remove_ver_scripts',
            'label' => __( 'Remove Version', self::slug ),
            'desc' => __( 'Remove version number (?ver=) from styles and scripts URLs.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,

        array(
            'name' => 'remove_body_class',
            'label' => __( 'Body Classes', self::slug ),
            'desc' => __( 'Clean up body classes *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,

        array(
            'name' => 'remove_menu_class',
            'label' => __( 'Menu Classes', self::slug ),
            'desc' => __( 'Clean up menu classes *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        ),

        array(
            'name' => 'clean_post_class',
            'label' => __( 'Post Classes', self::slug ),
            'desc' => __( 'Clean up post classes *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        ) ,
        array(
            'name' => 'clean_new_style',
            'label' => __( 'Clean up other', self::slug ),
            'desc' => __( 'Replacing other WP classes (wp-caption, etc.) with their "x-" version e.g x-caption (Require new style path).', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'permalink_req'
        ),
        array(
            'name' => 'replace_in_ajax',
            'label' => __( 'Replace in AJAX', self::slug ),
            'desc' => __( 'Replace content of AJAX responses *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        )
    ,
        array(
            'name' => 'replace_wpnonce',
            'label' => __( 'Change Nonce', self::slug ),
            'desc' => __( 'Replace _wpnonce in URLs with _nonce. <br/><b>Note:</b> its not compatible with all themes / plugins.<br/><em>We have deprecated this feature and may not be compatible with softwares like Buddypress. This feature will be removed in upcoming versions.</em>', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        ),
        array(
            'name' => 'disable_xml_rpc',
            'label' => __( 'Disable XML RPC', self::slug ),
            'desc' => __( 'Disables the XML-RPC API on a your site', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        ),
        array(
            'name' => 'internal_css',
            'label' => __( 'Internal CSS', self::slug ),
            'desc' => 'Useful to paste internal CSS code (Require New Theme Path)',
            'type' => 'textarea',
            'default' => '',
            'class' =>' permalink_req'
        ),

        array(
            'name' => 'internal_js',
            'label' => __( 'Internal JS', self::slug ),
            'desc' => 'Useful to paste internal JS code (Require New Theme Path)',
            'type' => 'textarea',
            'default' => '',
            'class' =>' permalink_req'
        ),


    );
$fields['ids'] =
    array(
        array(
            'name' => 'enable_ids',
            'label' => __( 'Enable IDS', self::slug ),
            'desc' => __( 'Monitor potential dangerous requests.', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'opener'

        ),

        array(
            'name' => 'ids_mode',
            'label' => __( 'IDS Mode', self::slug ),
            'desc' => '',
            'type' => 'radio',
            'default' => '0',
            'class' =>'open_by_enable_ids',
            'options' => array(
                '0' => __( 'Block All Attacks', self::slug ),
                '1' => __( 'Alert-Only', self::slug )
            )
        ),
		
        array(
            'name' => 'ids_email',
            'label' => __( 'Alert Email', self::slug ),
            'desc' => __( 'Send IDS email alerts to this email', self::slug ),
            'type' => 'text',
            'default' => get_option('admin_email'),
            'class' =>'open_by_enable_ids'

        ),

        array(
            'name' => 'ids_level',
            'label' => __( 'Enable IDS For', self::slug ),
            'desc' => __( 'Monitor potential dangerous requests.', self::slug ),
            'type' => 'radio',
            'default' => '0',
            'class' =>'open_by_enable_ids',
            'options' => array(
                '0' => 'Frontend',
                '1' => 'Frontend + Backend *'
            )
        ),
        array(
            'name' => 'ids_admin_include',
            'label' => __( 'Track Admin', self::slug ),
            'desc' => __( 'Include monitoring administrator\'s activities (you!) *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'open_by_enable_ids'

        ),

        array(
            'name' => 'ids_cookie',
            'label' => __( 'Monitor Cookies', self::slug ),
            'desc' => __( 'Monitor cookies values. Enable if there is no other site on this domain *', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'open_by_enable_ids'

        ),

        array(
            'name' => 'logger_link',
            'label' => __( 'Logs', self::slug ),
            'desc' =>  '<a target="_blank" href="admin.php?page=hmwp_ms_intrusions" class="button">'.__(' View Current Logs', self::slug).'</a> <a target="_blank" href="admin.php?page=hmwp_ms_intrusions&action=delete_all" onclick="return confirm(\'Are you sure to delete all logs?\')" class="button">'.__(' Delete Current Logs', self::slug).'</a>',
            'type' => 'custom',
            'default' => '',
            'class' =>'open_by_enable_ids'

        ),

        array(
            'name' => 'log_ids_min',
            'label' => __( 'Log Threshold', self::slug ),
            'desc' => __( 'Minimium total impact to log the request. 0 to disable logging.', self::slug ),
            'type' => 'number',
            'default' => '5',
            'class' =>'open_by_enable_ids'

        ),

        array(
            'name' => 'block_ids_min',
            'label' => __( 'Block Threshold', self::slug ),
            'desc' => __( 'Minimium total impact to block request with a 404 page. 0 to disable blocking *', self::slug ),
            'type' => 'number',
            'default' => '30',
            'class' =>'open_by_enable_ids'

        ),
        array(
            'name' => 'email_ids_min',
            'label' => __( 'Notify Threshold', self::slug ),
            'desc' => __( 'Minimium total impact to notify you about the request by an email. 0 to disable notifing.', self::slug ),
            'type' => 'number',
            'default' => '30',
            'class' =>'open_by_enable_ids'

        ),

        array(
            'name' => 'exception_fields',
            'label' => __( 'Exception fields', self::slug ),
            'desc' => __( 'Define fields that will be excluded from PHPIDS. One field per line.<br/> e.g. <code>POST.my_field</code>
wildcard example: <code>%.hide_my_wp.%</code> <br/>You may need to add each field several times (GET.my_field and REQUEST.my_field)', self::slug ),
            'type' => 'textarea',
            'default' => $this->exception_fields(),
            'class' =>'open_by_enable_ids'
        )
    ,
        array(
            'name' => 'ids_html_fields',
            'label' => __( 'HTML fields', self::slug ),
            'desc' => __( 'Define fields that contain HTML and need preparation before hitting the PHPIDS rules. One field per line. Note: Fields must contain valid HTML  <br/>', self::slug ),
            'type' => 'textarea',
            'default' => '',
            'class' =>'open_by_enable_ids'
        ),

        array(
            'name' =>'separator4',
            'label' =>'',
            'desc' => '<div style="border-top:1px solid #ccc;"></div><br/>',
            'type' => 'html',
            'class'=>'permalink_req'
        ),
		
		array(
            'name' => 'avoid_wp_admin_access',
            'label' => __( "Show wp-admin only allowed IP's", self::slug ),
            'desc' => __( "Allow to access wp-admin for allowed IP's", self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>'opener'
        ),
        array(
            'name' => 'allowed_ips_wp_admin_access',
            'label' => __( "Allowed IP's for wp-admin", self::slug ),
            'desc' => __( 'Separate with , e.g. 128.1.2.3, 128.11.122.*, 123.45.12.1/24', self::slug ),
            'type' => 'textarea',
            'default' => HideMyWP::get_client_ip(),
            'class' =>'open_by_avoid_wp_admin_access'
        ),	
	

        /* Trust Network Here */
        array(
            'name' => 'trust_network',
            'label' => __( 'Enable trust network', self::slug ),
            'desc' => __( 'Fetch known dangerous IPs, patterns and ban them' . $blocked_counter .'', self::slug ),
            'type' => 'checkbox',
            'default' => 'on',
            'class' =>''
        ),


        array(
            'name' => 'help_trust_network',
            'label' => __( 'Participate in Trust Network', self::slug ),
            'desc' => __( 'Send potential dangerous IPs (blocked by IDS) to strengthen the trust network <br/><span style="color: #b55050; margin-top:2px">Privacy notice - We will upload malicious IP addresses blocked by the Firewall to our trust network</span>', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        ),

        array(
            'name' => 'enable_smwp_server',
            'label' => __( 'Scan My WP Whitelist', self::slug ),
            'desc' => __( 'Whitelist <a href="https://wordpress.org/plugins/scan-my-wp/">Scan My WP</a> API server to scan your website (does not apply if you don\' use the plugin)', self::slug ),
            'type' => 'checkbox',
            'default' => '',
            'class' =>''
        ),


        array(
            'name' => 'blocked_ips',
            'label' => __( 'Blocked IPs', self::slug ),
            'desc' => __( 'Separate with , e.g. 128.1.2.3, 128.11.122.*, 123.45.12.1/24 <br>Do not enter your own IP for testing purposes!', self::slug ),
            'type' => 'textarea',
            'default' => '',
            'class' =>''
        ),

        array(
            'name' => 'blocked_ip_message',
            'label' => __( 'Blocked Message', self::slug ),
            'desc' => __( 'This message will be shown to blocked IPs. HTML is allowed.', self::slug ),
            'type' => 'textarea',
            'default' => 'You are blocked. Please contact site administrator if you think there is a problem. ',
            'class' =>''
        ),

        array(
            'name' => 'blocked_countries',
            'label' => __( 'Blocked Countries Code', self::slug ),
            'desc' => __( 'Visitors from these countries will be blocked. Use <a target="_blank" href="http://www.nationsonline.org/oneworld/country_code_list.htm">ISO-2 code list</a>. Separate with ,<strong> e.g. US, UK, CA</strong>. Best result for less than ~100K visits/day', self::slug ),
            'type' => 'text',
            'default' => '',
            'class' =>''
        ),
		
        array(
            'name' => 'allowed_countries',
            'label' => __( 'Allowed Countries Code', self::slug ),
            'desc' => __( 'Only visitors from these countries will be allowed. Use <a target="_blank" href="http://www.nationsonline.org/oneworld/country_code_list.htm">ISO-2 code list</a>. Separate with ,<strong> e.g. US, UK, CA</strong><br/>This option will override <em>`Blocked Countries Code`</em> Leave blank for allow all countries.', self::slug ),
            'type' => 'text',
            'default' => '',
            'class' =>''
        )
    );

//replace_in_html
$fields['replaces'] =
    array(


        array(
            'name' => 'replace1',
            'label' => __( 'Replace in HTML', self::slug ),
            'desc' => $this->replace_field('replace_in_html'),
            'type' => 'custom',
            'default' => '',
            'class' =>''
        ),
        
        array(
            'name' => 'replace2',
            'label' => __( 'Replace URLs', self::slug ),
            'desc' => $this->replace_field('replace_urls'),
            'type' => 'custom',
            'default' => '',
            'class' =>''
        )
    );

$menu=array(
    'name' => self::slug,
    'title' => self::title,
    'version' => self::ver,
    'icon_path' => '',
    'role' => '',
    'template_file' =>'',
    'display_metabox' => '1',
    'plugin_file' => self::main_file ,
    'action_link' => (is_super_admin()) ? '<b>Settings</b>' : '',
    'multisite_only' => (is_multisite()) ? true : false
);



foreach ($fields as $tab=>$field){
    $i=0;
    foreach ($field as $option) {
        if ($this->h->str_contains($option['class'], 'permalink_req') && !get_option('permalink_structure'))
            unset($fields[$tab][$i]) ;
        $i++;
    }
}

/*
$fields['trustip'] =
    array(

);*/

$this->s = new PP_Settings_API($fields, $sections, $menu);

?>