<?php
// get settings
if (empty($settings)) {
    global $wpdb, $post;
    $settings = json_decode($post->post_content_filtered);
    $google_fonts_str = seedprod_pro_construct_font_str(json_decode($post->post_content_filtered, true));
    $content = $post->post_content;
    $lpage_uuid = get_post_meta($post->ID, '_seedprod_page_uuid', true);
} else {
    $google_fonts_str = seedprod_pro_construct_font_str($settings);
    $content = $page->post_content;
    $lpage_uuid = get_post_meta($page->ID, '_seedprod_page_uuid', true);
}

// mapped domain settings
$plugin_url = SEEDPROD_PRO_PLUGIN_URL;
if(!empty($is_mapped)){
    global $seedprod_page_mapped_url;
    $url_parsed = parse_url($seedprod_page_mapped_url);
    $new_domain = $url_parsed['scheme'].'://'.$url_parsed['host'];
    $domain = explode('/wp-content/',$plugin_url);
    $plugin_url = str_replace($domain[0],$new_domain,$plugin_url);
}

//check to see if we have a shortcode, form or giveaway
$settings_str = serialize($settings);
if(strpos($settings_str, 'contact-form') !== false){
    $settings->no_conflict_mode = false;
}
if(strpos($settings_str, 'giveaway') !== false){
    $settings->no_conflict_mode = false;
}

// get url
$scheme = 'http';
if($_SERVER['SERVER_PORT'] == '443'){
	$scheme = 'https';
}
if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
	$scheme = 'https';
}
$ogurl = "$scheme://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// subscriber callback
$seedprod_subscribe_callback_ajax_url = html_entity_decode(wp_nonce_url(admin_url().'admin-ajax.php?action=seedprod_pro_subscribe_callback','seedprod_pro_subscribe_callback'));

// $email_integration_id = '';
// if (!empty($settings->email_integration_id)) {
//     $email_integration_id = $settings->email_integration_id;
// }

if (!empty($settings)) {
    ?>
<!DOCTYPE html>
<html class="sp-html <?php if (wp_is_mobile()) {
        echo 'sp-is-mobile';
    } ?> <?php if (is_user_logged_in()) {
        echo 'sp-is-logged-in';
    } ?> sp-seedprod sp-h-full">
<head>
<?php
if (!empty($settings->no_conflict_mode)) {
?>
<?php if(!empty($settings->seo_title)): ?>
<title><?php echo esc_html($settings->seo_title); ?></title>
<?php endif; ?>
<?php if(!empty($settings->seo_description)): ?>
<meta name="description" content="<?php echo esc_attr($settings->seo_description); ?>">
<?php endif; ?>
<?php if(!empty($settings->favicon)): ?>
<link href="<?php echo esc_attr($settings->favicon); ?>" rel="shortcut icon" type="image/x-icon" />
<?php endif; ?>


<?php if(!empty($settings->no_index)): ?>
<meta name="robots" content="noindex">
<?php endif; ?>



<!-- Open Graph -->
<meta property="og:url" content="<?php echo $ogurl; ?>" />
<meta property="og:type" content="website" />
<?php if(!empty($settings->seo_title)): ?>
<meta property="og:title" content="<?php echo esc_attr($settings->seo_title); ?>" />
<?php endif; ?>
<?php if(!empty($settings->seo_description)): ?>
<meta property="og:description" content="<?php echo esc_attr($settings->seo_description); ?>" />
<?php endif; ?>
<?php if(!empty($settings->social_thumbnail)): ?>
<meta property="og:image" content="<?php echo $settings->social_thumbnail; ?>" />
<?php elseif(!empty($settings->logo)): ?>
<meta property="og:image" content="<?php echo $settings->logo; ?>" />
<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="summary" />
<?php if(!empty($settings->seo_title)): ?>
<meta name="twitter:title" content="<?php echo esc_attr($settings->seo_title); ?>" />
<?php endif; ?>
<?php if(!empty($settings->seo_description)): ?>
<meta name="twitter:description" content="<?php echo esc_attr($settings->seo_description); ?>" />
<?php endif; ?>
<?php if(!empty($settings->social_thumbnail)): ?>
<meta property="twitter:image" content="<?php echo $settings->social_thumbnail; ?>" />
<?php endif; ?>

<?php
} 
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Default CSS -->
<link rel='stylesheet' id='seedprod-css-css'  href='<?php echo $plugin_url; ?>public/css/tailwind.min.css?ver=1.2.7.1' type='text/css' media='all' />
<link rel='stylesheet' id='seedprod-fontawesome-css'  href='<?php echo $plugin_url; ?>public/fontawesome/css/all.min.css?ver=1.2.7.1' type='text/css' media='all' />
<?php if (!empty($google_fonts_str)): ?>
<!-- Google Font -->
<link rel="stylesheet" href="<?php echo $google_fonts_str ?>">
<?php endif; ?>


<?php if (!empty($settings->enable_recaptcha)) { ?>
<!-- Recaptcha -->
<script src="https://www.google.com/recaptcha/api.js?onload=sp_CaptchaCallback&render=explicit" async defer></script>
<?php } ?>

<!-- Global Styles -->
<style>
<?php echo $settings->document->settings->headCss; ?>

<?php if (!empty($settings->document->settings->placeholderCss)) { ?>
    <?php echo $settings->document->settings->placeholderCss; ?>
<?php } ?>

<?php if (!empty($settings->document->settings->mobileCss)) { ?>
@media only screen and (max-width: 480px) {
    <?php echo str_replace(".sp-mobile-view", "", $settings->document->settings->mobileCss); ?>
}
<?php } ?>


<?php if (!empty($settings->document->settings->customCss)) { ?>
/* Custom CSS */
<?php
echo $settings->document->settings->customCss;
?>
<?php } ?>
</style>

<!-- JS -->
<script>
var seedprod_api_url = "<?php echo SEEDPROD_PRO_API_URL ?>";
<?php if (!empty($settings->enable_recaptcha)) { ?>
var seeprod_enable_recaptcha = <?php echo $settings->enable_recaptcha; ?>;
<?php } else { ?>
    var seeprod_enable_recaptcha = 0;
<?php } ?>
</script>
<script src="<?php echo $plugin_url; ?>public/js/sp-scripts.min.js" defer></script>
<?php if ( isset($settings->document->settings->useSlideshowBg) &&
           $settings->document->settings->useSlideshowBg ) { ?>
  <script>
    // Need to defer until after sp-scripts.min.js & defer attribute only works when using src
    window.addEventListener('DOMContentLoaded', (event) => {
        var setDelay = 5000;
        var sliderArgs = {};
        sliderArgs.slide = <?php echo json_encode($settings->document->settings->useSlideshowImgs); ?>;
        sliderArgs.delay = sliderArgs.slide.map( x => setDelay );
        easy_background("body", sliderArgs);
    });
  </script>
<?php } ?>

<?php if (!empty($settings->document->settings->useVideoBg)) { ?>
<script src="<?php echo $plugin_url; ?>public/js/tubular.js" defer></script>
<?php } ?>
<?php if (1 == 0) { ?>
<script src="<?php echo $plugin_url; ?>public/js/dynamic-text.js" defer></script>
<?php } ?>

<?php 
if(empty($settings->no_conflict_mode)){
    wp_enqueue_script('jquery');
    wp_head();
}else{
	$include_url = trailingslashit(includes_url());
	if(empty($settings->enable_wp_head_footer)){
		echo '<script src="'.$include_url.'js/jquery/jquery.js"></script>'."\n";
	}
}
?>
<?php echo $settings->header_scripts; ?>
</head>
<body class="spBg<?php echo $settings->document->settings->bgPosition; ?> sp-h-full sp-antialiased sp-bg-slideshow">
<?php 
if (!empty($settings->body_scripts)) {
    echo $settings->body_scripts;
}
?>

<?php
$actual_link = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    $content = str_replace("the_link", $actual_link, $content);
    echo do_shortcode($content); ?>



<div class="tv">
    <div class="screen mute" id="tv"></div>
</div>

<?php if(!empty($settings->show_powered_by_link)){
$aff_link = 'https://www.seedprod.com/?utm_source=seedprod-plugin&utm_medium=seedprod-frontend&utm_campaign=powered-by-link';
if(!empty($settings->affiliate_url)){
    $aff_link = $settings->affiliate_url;
}

?>
<div class="sp-credit" >
	<a target="_blank" href="<?php echo $aff_link ?>" rel="nofollow"><span>made with</span><img src="<?php echo $plugin_url; ?>public/svg/powered-by-logo.svg"></a>
</div>
<?php
} ?>

<script>
    var sp_subscriber_callback_url = '<?php echo $seedprod_subscribe_callback_ajax_url ; ?>';
    var sp_is_mobile = <?php if (wp_is_mobile()) {
        echo 'true';
    } else {
        echo 'false';
    } ?>;
    <?php if (!empty($settings->document->settings->useVideoBg)) { ?>
    jQuery( document ).ready(function($) {
    if(!sp_is_mobile){
	$('body').tubular({
						videoId: '<?php echo seedprod_pro_youtube_id_from_url($settings->document->settings->useVideoBgUrl); ?>',
						mute: true,
						repeat: true,
                        });
                    }
    });
    <?php } ?>

</script>

<?php 
if (empty($settings->no_conflict_mode)) {
    wp_footer() ;
} 
?>
<?php echo $settings->footer_scripts; ?>
</body>

</html>

<?php
} ?>
