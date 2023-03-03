<?php
$rootPath = ABSPATH;
require($rootPath . 'wp-load.php');


/**
 * Simulate not-logged user
 */
/*
$cookie_key2_start_with = 'wordpress_logged_in';
$cookie_key2_name = '';

foreach ($_COOKIE as $name => $content) {
    if (strpos($name, $cookie_key2_start_with) !== false) {
        $cookie_key2_name = $name;
    }
}
ct_reset_cookie($cookie_key2_name);
*/
get_header(); ?>
    <style>
        #wpadminbar {
            display: none !important;
        }
        .ct-preview-wrapper {
            max-width:95%;
            margin:20px auto;
        }
        .ct-preview-wrapper__title {
            text-align:center;
        }
    </style>
<?php

$number = isset($_GET['shortcodepreview']) ? intval($_GET['shortcodepreview']) : 0;

$shortcodes_list = ct_ultimate_gdpr_wizard_shortcodes_list();

$shortcode_to_render = $shortcodes_list[$number];

?>
<div class="ct-preview-wrapper">
    <?php
    if ($shortcode_to_render) {
        echo '<p class="ct-preview-wrapper__title"><code>'. $shortcode_to_render . '</code></p><br>';
        echo do_shortcode($shortcode_to_render);
    }
    ?>
</div>

<?php
get_footer();