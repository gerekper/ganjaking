<?php

/**
 *  Get IP
 */
function seedprod_pro_get_ip()
{
    $ip = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) and strlen($_SERVER['HTTP_X_FORWARDED_FOR'])>6) {
        $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) and strlen($_SERVER['HTTP_CLIENT_IP'])>6) {
        $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
    } elseif (!empty($_SERVER['REMOTE_ADDR']) and strlen($_SERVER['REMOTE_ADDR'])>6) {
        $ip = strip_tags($_SERVER['REMOTE_ADDR']);
    }//endif
    if (!$ip) {
        $ip="127.0.0.1";
    }
    return strip_tags($ip);
}

/**
 * Update cookie length for bypass url
 */
function seedprod_pro_change_wp_cookie_logout( $expirein ) {
    global $seed_cspv5_bypass_expires;
    if(!empty($seed_cspv5_bypass_expires)){
        return $seed_cspv5_bypass_expires; // Modify the exire cookie
    }else{
        return $expirein;
    }
}


/**
 * Get roles
 */
function seedprod_pro_get_roles() {
    global $wp_roles;
   
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    if ( is_multisite() ){
        $roles['superadmin'] = __('SuperAdmin','seedprod-pro');
    }
    $roles['anyoneloggedin'] = __('Anyone Logged In','seedprod-pro');

    return $roles;
}


/**
* Get Enviroment
*/
function seedprod_pro_is_localhost()
{
    // $localhost = array('127.0.0.1','::1');

    // $is_localhost = false;
    // if (in_array($_SERVER['REMOTE_ADDR'], $localhost) || !empty($_GET['debug'])) {
    //     $is_localhost = true;
    // }
    $is_localhost = false;
    if (defined('SEEDPROD_LOCAL_JS')) {
        $is_localhost = true;
    }

    return $is_localhost;
}

// YouTube video ID
function seedprod_pro_youtube_id_from_url($url) {
    $pattern =
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
    	if(isset($matches[1]))
        	return $matches[1];
    }
    return false;
}

/**
* Entry Options
*/
function seedprod_pro_block_options()
{
    $block_options = array(
         array('name'=>__('Column','seedprod-pro'),'is_pro'=> false,'cat'=>'layout','type'=>'column', 'id'=>1, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6  sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M4 5v13h17V5H4zm10 2v9h-3V7h3zM6 7h3v9H6V7zm13 9h-3V7h3v9z"/></svg>'),
        // array('name'=>__('2 Column','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'2-col', 'id'=>2),
        // array('name'=>__('3 Column','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'3-col', 'id'=>3),
        // array('name'=>__('4 Column','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'4-col', 'id'=>4),
        // array('name'=>__('5 Column','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'5-col', 'id'=>5),
        // array('name'=>__('6 Column','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'6-col', 'id'=>6),
        // array('name'=>__('Left Sidebar','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'left-sidebar', 'id'=>7),
        // array('name'=>__('Right Sidebar','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'right-sidebar', 'id'=>8),

        // array('name'=>__('6 Column','seedprod-pro'),'is_pro'=> false ,'cat'=>'layout','type'=>'6-col', 'id'=>6),

        array('name'=>__('Headline','seedprod-pro'),'is_pro'=> false,'cat'=>'common','type'=>'header', 'id'=>9, 'icon' => '<svg viewBox="0 0 12 17" class="sp-w-14px  sp-fill-current sp-pb-2" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 0.800049V7.04005H3V0.800049H0V16.4H3V10.16H9V16.4H12V0.800049H9Z"/>
        </svg>
        '),
        //array('name'=>__('Sub Headline','seedprod-pro'),'is_pro'=> false,'cat'=>'common','type'=>'sub-header', 'id'=>10, 'icon' => 'fas fa-heading'),
        array('name'=>__('Text','seedprod-pro'),'is_pro'=> false,'cat'=>'common','type'=>'text', 'id'=>11, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="sp-w-6 sp-fill-current "><rect x="0" fill="none"/><g><path d="M15 2H7.54c-.83 0-1.59.2-2.28.6-.7.41-1.25.96-1.65 1.65C3.2 4.94 3 5.7 3 6.52s.2 1.58.61 2.27c.4.69.95 1.24 1.65 1.64.69.41 1.45.61 2.28.61h.43V17c0 .27.1.51.29.71.2.19.44.29.71.29.28 0 .51-.1.71-.29.2-.2.3-.44.3-.71V5c0-.27.09-.51.29-.71.2-.19.44-.29.71-.29s.51.1.71.29c.19.2.29.44.29.71v12c0 .27.1.51.3.71.2.19.43.29.71.29.27 0 .51-.1.71-.29.19-.2.29-.44.29-.71V4H15c.27 0 .5-.1.7-.3.2-.19.3-.43.3-.7s-.1-.51-.3-.71C15.5 2.1 15.27 2 15 2z"/></g></svg>'),
        array('name'=>__('List','seedprod-pro'),'is_pro'=> false  ,'cat'=>'common','type'=>'bullet-list', 'id'=>12, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current"><g fill="none"><path d="M0 0h24v24H0V0z"/><path d="M0 0h24v24H0V0z" opacity=".87"/></g><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7zm-4 6h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>'),
        array('name'=>__('Button','seedprod-pro'),'is_pro'=> false  ,'cat'=>'common','type'=>'button', 'id'=>13, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" class="sp-w-6 sp-fill-current "  viewBox="0 0 24 24" ><g><rect fill="none" height="24" width="24"/></g><g><g><path d="M18.19,12.44l-3.24-1.62c1.29-1,2.12-2.56,2.12-4.32c0-3.03-2.47-5.5-5.5-5.5s-5.5,2.47-5.5,5.5c0,2.13,1.22,3.98,3,4.89 v3.26c-2.15-0.46-2.02-0.44-2.26-0.44c-0.53,0-1.03,0.21-1.41,0.59L4,16.22l5.09,5.09C9.52,21.75,10.12,22,10.74,22h6.3 c0.98,0,1.81-0.7,1.97-1.67l0.8-4.71C20.03,14.32,19.38,13.04,18.19,12.44z M17.84,15.29L17.04,20h-6.3 c-0.09,0-0.17-0.04-0.24-0.1l-3.68-3.68l4.25,0.89V6.5c0-0.28,0.22-0.5,0.5-0.5c0.28,0,0.5,0.22,0.5,0.5v6h1.76l3.46,1.73 C17.69,14.43,17.91,14.86,17.84,15.29z M8.07,6.5c0-1.93,1.57-3.5,3.5-3.5s3.5,1.57,3.5,3.5c0,0.95-0.38,1.81-1,2.44V6.5 c0-1.38-1.12-2.5-2.5-2.5c-1.38,0-2.5,1.12-2.5,2.5v2.44C8.45,8.31,8.07,7.45,8.07,6.5z"/></g></g></svg>'),
        array('name'=>__('Image','seedprod-pro'),'is_pro'=> false  ,'cat'=>'common','type'=>'image', 'id'=>14, 'icon' => '<svg  class="sp-w-6 sp-fill-current " xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4.86 8.86l-3 3.87L9 13.14 6 17h12l-3.86-5.14z"/></svg>'),
        array('name'=>__('Video','seedprod-pro'),'is_pro'=> false  ,'cat'=>'common','type'=>'video', 'id'=>15, 'icon' => '<svg  class="sp-w-6 sp-fill-current " xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15 8v8H5V8h10m1-2H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4V7c0-.55-.45-1-1-1z"/></svg>'),
        array('name'=>__('Divider','seedprod-pro'),'is_pro'=> false  ,'cat'=>'common','type'=>'divider', 'id'=>17, 'icon' => '
        <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><g><rect fill="none" height="24" width="24"/></g><g><g><g><path d="M20,9H4v2h16V9z M4,15h16v-2H4V15z"/></g></g></g></svg>'),
        array('name'=>__('Spacer','seedprod-pro'),'is_pro'=> false  ,'cat'=>'common','type'=>'spacer', 'id'=>24, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current sp-transform sp-rotate-90" ><path d="M0 0h24v24H0z" fill="none"/><path d="M7.77 6.76L6.23 5.48.82 12l5.41 6.52 1.54-1.28L3.42 12l4.35-5.24zM7 13h2v-2H7v2zm10-2h-2v2h2v-2zm-6 2h2v-2h-2v2zm6.77-7.52l-1.54 1.28L20.58 12l-4.35 5.24 1.54 1.28L23.18 12l-5.41-6.52z"/></svg>'),


        array('name'=>__('Giveaway','seedprod-pro'),'is_pro'=> false  ,'cat'=>'adv','type'=>'giveaway', 'id'=>32, 'icon' => '<svg class="sp-w-5 sp-fill-current " viewBox="0 0 394 416" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path d="M161.294,281.219 C151.445,281.219 143.462,289.202 143.462,299.049 C143.462,308.896 151.445,316.878 161.294,316.878 C171.139,316.878 179.122,308.896 179.122,299.049 C179.122,289.202 171.139,281.219 161.294,281.219 Z M232.979,281.219 C223.132,281.219 215.149,289.202 215.149,299.049 C215.149,308.896 223.132,316.878 232.979,316.878 C242.826,316.878 250.806,308.896 250.806,299.049 C250.806,289.202 242.826,281.219 232.979,281.219 Z M32.608,123.757 C30.714,158.655 31.726,255.445 32.608,292.617 C32.68,295.618 34.565,297.889 37.042,299.527 C58.017,313.458 79.698,326.395 101.835,338.541 C98.77,308.445 98.261,273.714 107.731,252.542 C111.467,244.191 119.577,237.434 130.383,232.272 C111.019,204.919 98.751,172.762 95.699,143.461 C91.243,100.685 159.191,80.829 161.091,113.506 C163.202,149.839 167.026,185.74 173.214,221.056 C180.966,220.166 188.963,219.72 196.962,219.708 C205.077,219.704 213.195,220.154 221.06,221.056 C227.245,185.74 231.071,149.839 233.18,113.506 C235.079,80.829 303.03,100.685 298.574,143.461 C295.523,172.762 283.254,204.919 263.891,232.272 C274.694,237.434 282.806,244.191 286.542,252.542 C295.99,273.665 295.504,308.286 292.458,338.332 C314.469,326.252 336.023,313.381 356.885,299.527 C359.356,297.889 361.245,295.618 361.316,292.617 C362.199,255.445 363.21,158.655 361.316,123.757 C361.008,120.766 359.356,118.487 356.885,116.846 C307.739,84.205 254.723,57.023 201.025,32.736 C199.667,32.123 198.314,31.818 196.962,31.818 C195.61,31.818 194.257,32.123 192.902,32.736 C139.201,57.023 86.185,84.205 37.042,116.846 C34.565,118.487 32.913,120.766 32.608,123.757 Z M1.328,120.554 C2.595,108.178 9.333,97.499 19.644,90.651 C70.294,57.012 124.602,29.116 179.943,4.087 C190.893,-0.864 203.032,-0.864 213.981,4.087 C269.323,29.116 323.628,57.012 374.28,90.651 C384.913,97.713 392.019,109.24 392.712,122.052 C394.273,150.787 393.913,180.541 393.792,209.337 C393.674,237.33 393.416,265.374 392.75,293.359 C392.432,306.785 385.326,318.385 374.28,325.719 C323.628,359.361 269.323,387.262 213.981,412.29 C203.032,417.237 190.893,417.237 179.943,412.29 C124.602,387.262 70.294,359.361 19.644,325.719 C8.596,318.385 1.493,306.785 1.174,293.359 C0.509,265.374 0.248,237.33 0.132,209.337 C0.047,189.407 -0.464,137.991 1.328,120.554 L1.328,120.554 Z" id="Fill-5"></path>
        </svg>'),

        array('name'=>__('Contact Form','seedprod-pro'),'is_pro'=> false  ,'cat'=>'adv','type'=>'contact-form', 'id'=>23, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="sp-w-5 sp-fill-current "><path fill="currentColor" d="M448 75.2v361.7c0 24.3-19 43.2-43.2 43.2H43.2C19.3 480 0 461.4 0 436.8V75.2C0 51.1 18.8 32 43.2 32h361.7c24 0 43.1 18.8 43.1 43.2zm-37.3 361.6V75.2c0-3-2.6-5.8-5.8-5.8h-9.3L285.3 144 224 94.1 162.8 144 52.5 69.3h-9.3c-3.2 0-5.8 2.8-5.8 5.8v361.7c0 3 2.6 5.8 5.8 5.8h361.7c3.2.1 5.8-2.7 5.8-5.8zM150.2 186v37H76.7v-37h73.5zm0 74.4v37.3H76.7v-37.3h73.5zm11.1-147.3l54-43.7H96.8l64.5 43.7zm210 72.9v37h-196v-37h196zm0 74.4v37.3h-196v-37.3h196zm-84.6-147.3l64.5-43.7H232.8l53.9 43.7zM371.3 335v37.3h-99.4V335h99.4z"></path></svg>'),


        array('name'=>__('Optin Form','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'optin-form', 'id'=>22, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 4.99L4 6h16zm0 12H4V8l8 5 8-5v10z"/></svg>'),
        
        array('name'=>__('Countdown','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'countdown', 'id'=>16, 'icon' => '
        
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.07 1.01h-6v2h6v-2zm-4 13h2v-6h-2v6zm8.03-6.62l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.14 4.74 14.19 4 12.07 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.11-.74-4.06-1.97-5.61zm-7.03 12.62c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>'),
        array('name'=>__('Social Profiles','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'social-profiles', 'id'=>18, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M9 13.75c-2.34 0-7 1.17-7 3.5V19h14v-1.75c0-2.33-4.66-3.5-7-3.5zM4.34 17c.84-.58 2.87-1.25 4.66-1.25s3.82.67 4.66 1.25H4.34zM9 12c1.93 0 3.5-1.57 3.5-3.5S10.93 5 9 5 5.5 6.57 5.5 8.5 7.07 12 9 12zm0-5c.83 0 1.5.67 1.5 1.5S9.83 10 9 10s-1.5-.67-1.5-1.5S8.17 7 9 7zm7.04 6.81c1.16.84 1.96 1.96 1.96 3.44V19h4v-1.75c0-2.02-3.5-3.17-5.96-3.44zM15 12c1.93 0 3.5-1.57 3.5-3.5S16.93 5 15 5c-.54 0-1.04.13-1.5.35.63.89 1 1.98 1 3.15s-.37 2.26-1 3.15c.46.22.96.35 1.5.35z"/></svg>'),
        array('name'=>__('Social Sharing','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'social-sharing', 'id'=>19, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>'),
        //array('name'=>__('Form','seedprod-pro'),'is_pro'=> false  ,'cat'=>'adv','type'=>'form', 'id'=>25, 'icon' => 'far fa-envelope'),

        array('name'=>__('Progress Bar','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'progress-bar', 'id'=>24, 'icon' => '
        <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"  class="sp-w-6 sp-fill-current sp-transform sp-rotate-90"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 9.2h3V19H5V9.2zM10.6 5h2.8v14h-2.8V5zm5.6 8H19v6h-2.8v-6z"/></svg>'),
        array('name'=>__('Icon','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'icon', 'id'=>24, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg>'),
        array('name'=>__('Image Box','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'feature', 'id'=>24, 'icon' => '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM4 6h9v7H4z"/></svg>'),

        array('name'=>__('Icon Box','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'iconfeature', 'id'=>26, 'icon' => '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM5 10h9v2H5zm0-3h9v2H5z"/></svg>'),

        array('name'=>__('Nav Menu','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'nav', 'id'=>25, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0z" fill="none"/><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>'),

        array('name'=>__('Anchor','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'anchor', 'id'=>30, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current" ><path d="M17,15l1.55,1.55c-0.96,1.69-3.33,3.04-5.55,3.37V11h3V9h-3V7.82C14.16,7.4,15,6.3,15,5c0-1.65-1.35-3-3-3S9,3.35,9,5 c0,1.3,0.84,2.4,2,2.82V9H8v2h3v8.92c-2.22-0.33-4.59-1.68-5.55-3.37L7,15l-4-3v3c0,3.88,4.92,7,9,7s9-3.12,9-7v-3L17,15z M12,4 c0.55,0,1,0.45,1,1s-0.45,1-1,1s-1-0.45-1-1S11.45,4,12,4z"/></g></svg>'),

        array('name'=>__('Star Rating','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'starrating', 'id'=>31, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" class="sp-w-6 sp-fill-current "><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 7.13l.97 2.29.47 1.11 1.2.1 2.47.21-1.88 1.63-.91.79.27 1.18.56 2.41-2.12-1.28-1.03-.64-1.03.62-2.12 1.28.56-2.41.27-1.18-.91-.79-1.88-1.63 2.47-.21 1.2-.1.47-1.11.97-2.27M12 2L9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2z"/></svg>'),



        array('name'=>__('Shortcode','seedprod-pro'),'is_pro'=> true  ,'cat'=>'adv','type'=>'shortcode', 'id'=>21, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="sp-w-6 sp-fill-current"><path d="M256 8C119.3 8 8 119.2 8 256c0 136.7 111.3 248 248 248s248-111.3 248-248C504 119.2 392.7 8 256 8zM33 256c0-32.3 6.9-63 19.3-90.7l106.4 291.4C84.3 420.5 33 344.2 33 256zm223 223c-21.9 0-43-3.2-63-9.1l66.9-194.4 68.5 187.8c.5 1.1 1 2.1 1.6 3.1-23.1 8.1-48 12.6-74 12.6zm30.7-327.5c13.4-.7 25.5-2.1 25.5-2.1 12-1.4 10.6-19.1-1.4-18.4 0 0-36.1 2.8-59.4 2.8-21.9 0-58.7-2.8-58.7-2.8-12-.7-13.4 17.7-1.4 18.4 0 0 11.4 1.4 23.4 2.1l34.7 95.2L200.6 393l-81.2-241.5c13.4-.7 25.5-2.1 25.5-2.1 12-1.4 10.6-19.1-1.4-18.4 0 0-36.1 2.8-59.4 2.8-4.2 0-9.1-.1-14.4-.3C109.6 73 178.1 33 256 33c58 0 110.9 22.2 150.6 58.5-1-.1-1.9-.2-2.9-.2-21.9 0-37.4 19.1-37.4 39.6 0 18.4 10.6 33.9 21.9 52.3 8.5 14.8 18.4 33.9 18.4 61.5 0 19.1-7.3 41.2-17 72.1l-22.2 74.3-80.7-239.6zm81.4 297.2l68.1-196.9c12.7-31.8 17-57.2 17-79.9 0-8.2-.5-15.8-1.5-22.9 17.4 31.8 27.3 68.2 27.3 107 0 82.3-44.6 154.1-110.9 192.7z"/></svg>'),
       
        array('name'=>__('Custom HTML','seedprod-pro'),'is_pro'=> false  ,'cat'=>'adv','type'=>'custom-html', 'id'=>20, 'icon' => '
        <svg xmlns="http://www.w3.org/2000/svg"class="sp-w-6 sp-fill-current " viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>'),


       
    );

    

    return $block_options ;
}


/**
* Get times
*/
function seedprod_pro_get_default_timezone()
{
    $general_settings = get_option('seedprod_settings');
    $timezone = 'UTC';
    
    if (!empty($general_settings)) {
        $general_settings = json_decode($general_settings);
        if (!empty($general_settings->default_timezone)) {
            $timezone = $general_settings->default_timezone;
        }
    }

    return $timezone;
}



/**
* Get times
*/
function seedprod_pro_get_times()
{
    $times = array();
    $times[] = array('v'=> '', 'l'=> __('Select Time', 'seedprod-pro'));
    $times[] = array('v'=> '00:00', 'l'=>'12:00 A.M.');
    $times[] = array('v'=> '00:30', 'l'=>'12:30 A.M.');
    $times[] = array('v'=> '01:00', 'l'=>'1:00 A.M.');
    $times[] = array('v'=> '01:30', 'l'=>'1:30 A.M.');
    $times[] = array('v'=> '02:00', 'l'=>'2:00 A.M.');
    $times[] = array('v'=> '02:30', 'l'=>'2:30 A.M.');
    $times[] = array('v'=> '03:00', 'l'=>'3:00 A.M.');
    $times[] = array('v'=> '03:30', 'l'=>'3:30 A.M.');
    $times[] = array('v'=> '04:00', 'l'=>'4:00 A.M.');
    $times[] = array('v'=> '04:30', 'l'=>'4:30 A.M.');
    $times[] = array('v'=> '05:00', 'l'=>'5:00 A.M.');
    $times[] = array('v'=> '05:30', 'l'=>'5:30 A.M.');
    $times[] = array('v'=> '06:00', 'l'=>'6:00 A.M.');
    $times[] = array('v'=> '06:30', 'l'=>'6:30 A.M.');
    $times[] = array('v'=> '07:00', 'l'=>'7:00 A.M.');
    $times[] = array('v'=> '07:30', 'l'=>'7:30 A.M.');
    $times[] = array('v'=> '08:00', 'l'=>'8:00 A.M.');
    $times[] = array('v'=> '08:30', 'l'=>'8:30 A.M.');
    $times[] = array('v'=> '09:00', 'l'=>'9:00 A.M.');
    $times[] = array('v'=> '09:30', 'l'=>'9:30 A.M.');
    $times[] = array('v'=> '10:00', 'l'=>'10:00 A.M.');
    $times[] = array('v'=> '10:30', 'l'=>'10:30 A.M.');
    $times[] = array('v'=> '11:00', 'l'=>'11:00 A.M.');
    $times[] = array('v'=> '11:30', 'l'=>'11:30 A.M.');
    $times[] = array('v'=> '12:00', 'l'=>'12:00 P.M.');
    $times[] = array('v'=> '12:30', 'l'=>'12:30 P.M.');
    $times[] = array('v'=> '13:00', 'l'=>'1:00 P.M.');
    $times[] = array('v'=> '13:30', 'l'=>'1:30 P.M.');
    $times[] = array('v'=> '14:00', 'l'=>'2:00 P.M.');
    $times[] = array('v'=> '14:30', 'l'=>'2:30 P.M.');
    $times[] = array('v'=> '15:00', 'l'=>'3:00 P.M.');
    $times[] = array('v'=> '15:30', 'l'=>'3:30 P.M.');
    $times[] = array('v'=> '16:00', 'l'=>'4:00 P.M.');
    $times[] = array('v'=> '16:30', 'l'=>'4:30 P.M.');
    $times[] = array('v'=> '17:00', 'l'=>'5:00 P.M.');
    $times[] = array('v'=> '17:30', 'l'=>'5:30 P.M.');
    $times[] = array('v'=> '18:00', 'l'=>'6:00 P.M.');
    $times[] = array('v'=> '18:30', 'l'=>'6:30 P.M.');
    $times[] = array('v'=> '19:00', 'l'=>'7:00 P.M.');
    $times[] = array('v'=> '19:30', 'l'=>'7:30 P.M.');
    $times[] = array('v'=> '20:00', 'l'=>'8:00 P.M.');
    $times[] = array('v'=> '20:30', 'l'=>'8:30 P.M.');
    $times[] = array('v'=> '21:00', 'l'=>'9:00 P.M.');
    $times[] = array('v'=> '21:30', 'l'=>'9:30 P.M.');
    $times[] = array('v'=> '22:00', 'l'=>'10:00 P.M.');
    $times[] = array('v'=> '22:30', 'l'=>'10:30 P.M.');
    $times[] = array('v'=> '23:00', 'l'=>'11:00 P.M.');
    $times[] = array('v'=> '23:30', 'l'=>'11:30 P.M.');
    
    return $times;
}

/**
* Check per
*/
function seedprod_pro_get_api_key()
{
    $seedprod_api_key = '';

    if (defined('SEEDPROD_API_KEY')) {
        $seedprod_api_key = SEEDPROD_API_KEY;
    }

    if (empty($seedprod_api_key)) {
        $seedprod_api_key = get_option('seedprod_api_key ');
    }

    return $seedprod_api_key;
}

/**
* Get timezones
*/
function seedprod_pro_get_timezones()
{
    // timezones
    $zonen = array();
    $continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');

    foreach (timezone_identifiers_list() as $zone) {
        $zone = explode('/', $zone);
        if (!in_array($zone[0], $continents)) {
            continue;
        }

        // This determines what gets set and translated - we don't translate Etc/* strings here, they are done later
        $exists = array(
            0 => (isset($zone[0]) && $zone[0]),
            1 => (isset($zone[1]) && $zone[1]),
            2 => (isset($zone[2]) && $zone[2]),
        );
        $exists[3] = ($exists[0] && 'Etc' !== $zone[0]);
        $exists[4] = ($exists[1] && $exists[3]);
        $exists[5] = ($exists[2] && $exists[3]);

        $zonen[] = array(
            'continent'   => ($exists[0] ? $zone[0] : ''),
            'city'        => ($exists[1] ? $zone[1] : ''),
            'subcity'     => ($exists[2] ? $zone[2] : ''),
            't_continent' => ($exists[3] ? translate(str_replace('_', ' ', $zone[0]), 'continents-cities') : ''),
            't_city'      => ($exists[4] ? translate(str_replace('_', ' ', $zone[1]), 'continents-cities') : ''),
            't_subcity'   => ($exists[5] ? translate(str_replace('_', ' ', $zone[2]), 'continents-cities') : '')
        );
    }
    usort($zonen, '_wp_timezone_choice_usort_callback');

    $structure = array();

    foreach ($zonen as $key => $zone) {
        // Build value in an array to join later
        $value = array( $zone['continent'] );

        if (empty($zone['city'])) {
            // It's at the continent level (generally won't happen)
            $display = $zone['t_continent'];
        } else {
            // It's inside a continent group

            // Continent optgroup
            if (!isset($zonen[$key - 1]) || $zonen[$key - 1]['continent'] !== $zone['continent']) {
                $label = $zone['t_continent'];
                //$structure[] = $label ;
            }

        
            // Add the city to the value
            $value[] = $zone['city'];

            // get offset
            // $timezone = $label.'/'.str_replace(' ', '_', $zone['t_city']);
            // $time = new \DateTime('now', new DateTimeZone($timezone));
            // $timezoneOffset = $time->format('P');

      


            $display = $zone['t_city'];
            ;
            if (!empty($zone['subcity'])) {
                // Add the subcity to the value
                $value[] = $zone['subcity'];
                $display .= ' - ' . $zone['t_subcity'];
            }
        }

          

        // Build the value
        $value = join('/', $value);


        // get offset
        $time = new \DateTime('now', new DateTimeZone($value));
        $timezoneOffset = $time->format('P');
        $structure[$label][] = array('v'=> $value, 'l'=>$display.' ('.$timezoneOffset.' GMT)');
    }

    $structure['UTC'][] = array('v'=> "UTC", 'l'=>"UTC");

    return $structure;
}


/**
* Add to array if value does not exist
*/
function seedprod_pro_array_add($arr, $key, $value)
{
    if (!array_key_exists($key, $arr)) {
        $arr[$key] = $value;
    }
    return $arr;
}




/**
* Check per
*/
function seedprod_pro_cu($rper = null)
{
    if (!empty($rper)) {
        $uper = explode(",", get_option('seedprod_per'));
        if (in_array($rper, $uper)) {
            return true;
        } else {
            return false;
        }
    } else {
        $a = get_option('seedprod_a');
        if ($a) {
            return true;
        } else {
            return false;
        }
    }
}


function seedprod_pro_upgrade_link($medium = 'link')
{
    return apply_filters('seedprod_pro_upgrade_link', 'https://seedprod.com/lite-upgrade/?utm_source=WordPress&utm_campaign=liteplugin&utm_medium=' . sanitize_key(apply_filters('seedprod_pro_upgrade_link_medium', $medium)));
}


function seedprod_pro_disable_admin_notices()
{
    global $wp_filter;
    if (is_user_admin()) {
        if (isset($wp_filter['user_admin_notices'])) {
            unset($wp_filter['user_admin_notices']);
        }
    } elseif (isset($wp_filter['admin_notices'])) {
        unset($wp_filter['admin_notices']);
    }
    if (isset($wp_filter['all_admin_notices'])) {
        unset($wp_filter['all_admin_notices']);
    }
}
if (!empty($_GET['page']) && strpos($_GET['page'], 'seedprod') !==  false) {
    add_action('admin_print_scripts', 'seedprod_pro_disable_admin_notices');
}


function seedprod_pro_plugin_nonce()
{
    check_ajax_referer('seedprod_pro_plugin_nonce', 'nonce');

    if (! current_user_can('install_plugins')) {
        wp_send_json_error();
    }

    $install_plugin_nonce = wp_create_nonce('install-plugin_'.sanitize_text_field($_POST['plugin']));
    
    wp_send_json($install_plugin_nonce);
}

function seedprod_pro_is_dev_url($url = '')
{
    $is_local_url = false;
    // Trim it up
    $url = strtolower(trim($url));
    // Need to get the host...so let's add the scheme so we can use parse_url
    if (false === strpos($url, 'http://') && false === strpos($url, 'https://')) {
        $url = 'http://' . $url;
    }
    $url_parts = parse_url($url);
    $host      = ! empty($url_parts['host']) ? $url_parts['host'] : false;
    if (! empty($url) && ! empty($host)) {
        if (false !== ip2long($host)) {
            if (! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $is_local_url = true;
            }
        } elseif ('localhost' === $host) {
            $is_local_url = true;
        }

        $tlds_to_check = array( '.local', ':8888', ':8080', ':8081', '.invalid', '.example', '.test' );
        foreach ($tlds_to_check as $tld) {
            if (false !== strpos($host, $tld)) {
                $is_local_url = true;
                break;
            }
        }
        if (substr_count($host, '.') > 1) {
            $subdomains_to_check =  array( 'dev.', '*.staging.', 'beta.', 'test.' );
            foreach ($subdomains_to_check as $subdomain) {
                $subdomain = str_replace('.', '(.)', $subdomain);
                $subdomain = str_replace(array( '*', '(.)' ), '(.*)', $subdomain);
                if (preg_match('/^(' . $subdomain . ')/', $host)) {
                    $is_local_url = true;
                    break;
                }
            }
        }
    }
    return $is_local_url;
}


function seedprod_pro_find_fonts_in_doc($someArray) {
    if(empty($someArray)){
        return false;
    }
    $load_fonts = array();
    $load_variants = array();
    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($someArray), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $k => $v) {
        $indent = str_repeat('&nbsp;', 10 * $iterator->getDepth());
        // Not at end: show key only
        if ($iterator->hasChildren()) {
            //echo "$indent$k :<br>";
        // At end: show key, value and path
        } else {
            for ($p = array(), $i = 0, $z = $iterator->getDepth(); $i <= $z; $i++) {
                $p[] = $iterator->getSubIterator($i)->key();
            }
            $path = implode(',', $p);
            //echo "$indent$k : $v : path -> $path<br>";
            // get font
            if(stripos($k, 'font') !== false && stripos($k, 'variant') === false && !empty($v) && strpos($v, ',') === false && stripos($k, 'fontSize') === false){
                $load_fonts[] = array("k"=>$k,"v"=>$v,"p"=>$path);
            }
            // get variant
            if(stripos($k, 'font') !== false && stripos($k, 'variant') !== false && !empty($v) && strpos($v, ',') === false){
                $load_variants[] = array("k"=>$k,"v"=>$v,"p"=>$path);
            }
            

        }
    }
    return array_merge($load_fonts,$load_variants);
}

function seedprod_pro_construct_font_str($doc_settings) {
    $fonts = seedprod_pro_find_fonts_in_doc($doc_settings);
    $myfonts = array();
    $myvariants = array();
    if(!empty($fonts)){
        foreach($fonts as $k => $v){
            if(stripos($v['k'], 'font') !== false && stripos($v['k'], 'variant') === false){
                if(empty($myfonts[$v['v']])){
                    $myfonts[$v['v']] = array();
                }
                
                foreach($fonts as $k2 => $v2){
                    if($v['p'].'Variant' === $v2['p']){
                        $myfonts[$v['v']][] = $v2['v'];
                    }
                }
            }
        }
        
        foreach($myfonts as $k3 => $v3){
            $myfonts[$k3] = array_unique($v3);
        }
    }
    $google_fonts_str = '';
    if(!empty($myfonts)){
        $google_fonts_str = 'https://fonts.googleapis.com/css?family=';
        $c = 1;
        foreach($myfonts as $k4 => $v4){
            $end = '|';
            if(count($myfonts) == $c){
                $end = '';
            }
            $google_fonts_str .= urlencode($k4);
            if(!empty($v4)){
                $google_fonts_str .= ':' . implode(',',$v4);
            }
            
            $google_fonts_str .= $end;
            $c++;
        }
        $google_fonts_str .= '&display=swap';
    }
    return  $google_fonts_str;
}


add_filter('_wp_post_revision_fields','seedprod_pro_wp_post_revision_fields',11,2);
function seedprod_pro_wp_post_revision_fields($fields, $post){
    if(!empty($post['post_content_filtered']) && strpos($post['post_content'], 'sp-page') !== false){
         $fields['post_content_filtered'] = 'Content Filtered';
         return $fields;
    }else{
    return $fields;
    }
}


add_filter( 'page_row_actions', 'seedprod_pro_filter_page_row_actions', 11, 2 );
function seedprod_pro_filter_page_row_actions( $actions, $post ) {
    $has_settings = get_post_meta( $post->ID, '_seedprod_page', true );
    if ( !empty($has_settings)) {
        $id = $post->ID;
        $actions['edit_seedprod'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            admin_url().'admin.php?page=seedprod_pro_builder&id='.$id.'#/setup/'.$id,
            __( 'Edit with SeedProd', 'seedprod' )
        );
       // unset($actions['inline hide-if-no-js']);
    }

    return $actions;
}

//add_filter( 'get_edit_post_link', 'seedprod_pro_filter_get_edit_post_link', 11, 3 );
function seedprod_pro_filter_get_edit_post_link( $link, $id, $context ) {
    $has_settings = get_post_meta( $id, '_seedprod_page', true );
    if ( !empty($has_settings)) {
        $link = admin_url(). 'admin.php?page=seedprod_pro_builder&id='.$id.'#/setup/'.$id;
    }
    return $link;
}

/**
 * Dismiss Settings Lite CTA
 */
function seedprod_pro_dismiss_settings_lite_cta()
{
    if (check_ajax_referer('seedprod_pro_dismiss_settings_lite_cta')) {
        $_POST = stripslashes_deep($_POST);
        
        if (!empty($_POST['dismiss'])) {
            update_option('seedprod_dismiss_settings_lite_cta', true);

            $response = array(
            'status'=> 'true',

        );
        }

        // Send Response
        wp_send_json($response);
        exit;
    }
}

/**
 * Dismiss Lite Banners
 */
function seedprod_pro_dismiss_upsell()
{
    if (check_ajax_referer('seedprod_pro_dismiss_upsell')) {
        $_POST = stripslashes_deep($_POST);
        
        if (!empty($_POST['id'])) {
            $ts = time();
            update_option('seedprod_dismiss_upsell_'.$_POST['id'], $ts );
            $response = array(
            'status'=> 'true',

        );
        }

        // Send Response
        wp_send_json($response);
        exit;
    }
}

function seedprod_pro_get_expire_times(){
    return array(
        '1' => "1 Hour",
        '2' => "2 Hours",
        '3' => "3 Hours",
        '4' => "4 Hours",
        '5' => "5 Hours",
        '6' => "6 Hours",
        '7' => "7 Hours",
        '8' => "8 Hours",
        '9' => "9 Hours",
        '10' => "10 Hours",
        '11' => "11 Hours",
        '12' => "12 Hours",
        '13' => "13 Hours",
        '14' => "14 Hours",
        '15' => "15 Hours",
        '16' => "16 Hours",
        '17' => "17 Hours",
        '18' => "18 Hours",
        '19' => "19 Hours",
        '20' => "20 Hours",
        '21' => "21 Hours",
        '21' => "22 Hours",
        '23' => "23 Hours",
        '24' => "1 Day",
        '48' => "2 Days",
        '72' => "3 Days",
        '96' => "4 Days",
        '120' => "5 Days",
        '144' => "6 Days",
        '168' => "7 Days",
        '192' => "8 Days",
        '216' => "9 Days",
        '240' => "10 Days",
        '264' => "11 Days",
        '288' => "12 Days",
        '312' => "13 Days",
        '336' => "14 Days",
        '360' => "15 Days",
        '384' => "16 Days",
        '408' => "17 Days",
        '432' => "18 Days",
        '456' => "19 Days",
        '480' => "20 Days",
        '504' => "21 Days",
        '528' => "22 Days",
        '552' => "23 Days",
        '576' => "24 Days",
        '600' => "25 Days",
        '624' => "26 Days",
        '648' => "27 Days",
        '672' => "28 Days",
        '696' => "29 Days",
        '720' => "30 Days",
        '8760' => "1 Year",
    );
}

 
function seedprod_pro_bypass_form_func( $atts ){
    $a = shortcode_atts( array(
        'msg' => 'Password',
        'button-txt' => 'Enter',
        'return' => '',
    ), $atts );
    ob_start();
    ?>
    <div class="row">
    <div class="col-md-12 seperate">
    <div class="input-group">
    <input type="password" id="cspio-bypass" class="form-control input-lg form-el sp-form-input" placeholder="<?php echo $a['msg'] ?>"></input>
    <span class="input-group-btn">
    <button id="cspio-bypass-btn" class="btn btn-lg btn-primary form-el noglow"><?php echo $a['button-txt'] ?></button>
    </span>
    </div>
    </div>
    </div>
    <script>
    jQuery( document ).ready(function($) {
        $( "#cspio-bypass-btn" ).click(function(e) {
          e.preventDefault();
          window.location = "?bypass="+$("#cspio-bypass").val()+'&return=<?php echo urlencode($a['return']) ?>';
        });
    });
    </script>
    
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
add_shortcode( 'seed_bypass_form', 'seedprod_pro_bypass_form_func' );
 

 function seedprod_pro_get_system_info() {

    global $wpdb;

    // Get theme info.
    $theme_data = wp_get_theme();
    $theme      = $theme_data->Name . ' ' . $theme_data->Version;

    $return = '### Begin System Info ###' . "\n\n";

    // WPForms info.
    $return   .= '-- SeedProd Info' . "\n\n";

    // Now the basics...
    $return .= "\n" . '-- Site Info' . "\n\n";
    $return .= 'Site URL:                 ' . site_url() . "\n";
    $return .= 'Home URL:                 ' . home_url() . "\n";
    $return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

    // WordPress configuration.
    $return .= "\n" . '-- WordPress Configuration' . "\n\n";
    $return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
    $return .= 'Language:                 ' . ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ) . "\n";
    $return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
    $return .= 'Active Theme:             ' . $theme . "\n";
    $return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";
    // Only show page specs if front page is set to 'page'.
    if ( get_option( 'show_on_front' ) === 'page' ) {
        $front_page_id = get_option( 'page_on_front' );
        $blog_page_id  = get_option( 'page_for_posts' );

        $return .= 'Page On Front:            ' . ( 0 != $front_page_id ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
        $return .= 'Page For Posts:           ' . ( 0 != $blog_page_id ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
    }
    $return .= 'ABSPATH:                  ' . ABSPATH . "\n";
    $return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n";
    $return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
    $return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
    $return .= 'Registered Post Stati:    ' . implode( ', ', get_post_stati() ) . "\n";

    // @todo WPForms configuration/specific details.
    $return .= "\n" . '-- WordPress Uploads/Constants' . "\n\n";
    $return .= 'WP_CONTENT_DIR:           ' . ( defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR ? WP_CONTENT_DIR : 'Disabled' : 'Not set' ) . "\n";
    $return .= 'WP_CONTENT_URL:           ' . ( defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL ? WP_CONTENT_URL : 'Disabled' : 'Not set' ) . "\n";
    $return .= 'UPLOADS:                  ' . ( defined( 'UPLOADS' ) ? UPLOADS ? UPLOADS : 'Disabled' : 'Not set' ) . "\n";

    $uploads_dir = wp_upload_dir();

    $return .= 'wp_uploads_dir() path:    ' . $uploads_dir['path'] . "\n";
    $return .= 'wp_uploads_dir() url:     ' . $uploads_dir['url'] . "\n";
    $return .= 'wp_uploads_dir() basedir: ' . $uploads_dir['basedir'] . "\n";
    $return .= 'wp_uploads_dir() baseurl: ' . $uploads_dir['baseurl'] . "\n";

    // Get plugins that have an update.
    $updates = get_plugin_updates();

    // Must-use plugins.
    // NOTE: MU plugins can't show updates!
    $muplugins = get_mu_plugins();
    if ( count( $muplugins ) > 0 && ! empty( $muplugins ) ) {
        $return .= "\n" . '-- Must-Use Plugins' . "\n\n";

        foreach ( $muplugins as $plugin => $plugin_data ) {
            $return .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
        }
    }

    // WordPress active plugins.
    $return .= "\n" . '-- WordPress Active Plugins' . "\n\n";

    $plugins        = get_plugins();
    $active_plugins = get_option( 'active_plugins', array() );

    foreach ( $plugins as $plugin_path => $plugin ) {
        if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
            continue;
        }
        $update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
        $return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
    }

    // WordPress inactive plugins.
    $return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

    foreach ( $plugins as $plugin_path => $plugin ) {
        if ( in_array( $plugin_path, $active_plugins, true ) ) {
            continue;
        }
        $update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
        $return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
    }

    if ( is_multisite() ) {
        // WordPress Multisite active plugins.
        $return .= "\n" . '-- Network Active Plugins' . "\n\n";

        $plugins        = wp_get_active_network_plugins();
        $active_plugins = get_site_option( 'active_sitewide_plugins', array() );

        foreach ( $plugins as $plugin_path ) {
            $plugin_base = plugin_basename( $plugin_path );
            if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
                continue;
            }
            $update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
            $plugin  = get_plugin_data( $plugin_path );
            $return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
        }
    }

    // Server configuration (really just versions).
    $return .= "\n" . '-- Webserver Configuration' . "\n\n";
    $return .= 'PHP Version:              ' . PHP_VERSION . "\n";
    $return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
    $return .= 'Webserver Info:           ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

    // PHP configs... now we're getting to the important stuff.
    $return .= "\n" . '-- PHP Configuration' . "\n\n";
    $return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
    $return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
    $return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
    $return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
    $return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
    $return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
    $return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

    // PHP extensions and such.
    $return .= "\n" . '-- PHP Extensions' . "\n\n";
    $return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . "\n";
    $return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
    $return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient', false ) ? 'Installed' : 'Not Installed' ) . "\n";
    $return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

    // Session stuff.
    $return .= "\n" . '-- Session Configuration' . "\n\n";
    $return .= 'Session:                  ' . ( isset( $_SESSION ) ? 'Enabled' : 'Disabled' ) . "\n";

    // The rest of this is only relevant if session is enabled.
    if ( isset( $_SESSION ) ) {
        $return .= 'Session Name:             ' . esc_html( ini_get( 'session.name' ) ) . "\n";
        $return .= 'Cookie Path:              ' . esc_html( ini_get( 'session.cookie_path' ) ) . "\n";
        $return .= 'Save Path:                ' . esc_html( ini_get( 'session.save_path' ) ) . "\n";
        $return .= 'Use Cookies:              ' . ( ini_get( 'session.use_cookies' ) ? 'On' : 'Off' ) . "\n";
        $return .= 'Use Only Cookies:         ' . ( ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off' ) . "\n";
    }

    $return .= "\n" . '### End System Info ###';

    return $return;
}


