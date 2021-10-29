<?php

if ( Jetpack::is_active() ){
//devicepx-jetpack.js
$this->partial_replace_old[]=  set_url_scheme("http://s0.wp.com/wp-content/js/devicepx-jetpack.js");
$this->partial_replace_new[]=  network_home_url('/_get/ws0js/devicepx.js');

$this->top_replace_old[] = "<link rel='dns-prefetch' href='//0.gravatar.com'>\r
<link rel='dns-prefetch' href='//1.gravatar.com'>\r
<link rel='dns-prefetch' href='//2.gravatar.com'>\r";
$this->top_replace_new[] = "";

$this->top_replace_old[] = "<link rel='dns-prefetch' href='//s0.wp.com' />";
$this->top_replace_new[] = "";
}

if ( Jetpack::is_active() && Jetpack::is_module_active('photon')) {
$this->top_replace_old[] = "<link rel='dns-prefetch' href='//i0.wp.com'>\r
<link rel='dns-prefetch' href='//i1.wp.com'>\r
<link rel='dns-prefetch' href='//i2.wp.com'>\r
";
$this->top_replace_new[] = "";
}


//order is important for common tags like s0
if ( Jetpack::is_active() && Jetpack::is_module_active('comments')) {
$this->top_replace_old[] = "<link rel='dns-prefetch' href='//jetpack.wordpress.com'>\r
<link rel='dns-prefetch' href='//s0.wp.com'>\r
<link rel='dns-prefetch' href='//s1.wp.com'>\r
<link rel='dns-prefetch' href='//s2.wp.com'>\r
<link rel='dns-prefetch' href='//public-api.wordpress.com'>\r";
$this->top_replace_new[] = "";
}


if ( Jetpack::is_active() && Jetpack::is_module_active('likes')) {
$this->top_replace_old[] = "<link rel='dns-prefetch' href='//s0.wp.com'>";
$this->top_replace_new[] = "";

$this->top_replace_old[] = "<link rel='dns-prefetch' href='//widgets.wp.com'>";
$this->top_replace_new[] = "";
}

if ( Jetpack::is_active() && Jetpack::is_module_active('videopress')) {
$this->top_replace_old[] = "<link rel='dns-prefetch' href='//v0.wordpress.com'>";
$this->top_replace_new[] = "";
}

if ( Jetpack::is_active()){
$jet_folder = trim(str_replace(WP_PLUGIN_DIR, '', JETPACK__PLUGIN_DIR), '/\\');
if ($this->opt('rename_plugins')) {
$path = trim($this->opt('new_plugin_path'), '/ ') . '/' . $this->hash($jet_folder . '/jetpack.php');
} else {
$path = trim($this->opt('new_plugin_path'), ' /') . '/' . $jet_folder;
}

$this->top_replace_old[] = " id='jetpack_css-css'";
$this->top_replace_new[] = "";

$this->auto_replace_urls[] = $prefix.'wp-content/plugins/jetpack/css/jetpack.css==' . $path . '/css/jp.css';


if (Jetpack::is_module_active('carousel')){
$this->top_replace_old[] = "id='jetpack-carousel";
$this->top_replace_new[] = "id='jc";

$this->auto_replace_urls[] = $prefix.'wp-content/plugins/jetpack/modules/carousel/jetpack-carousel-ie8fix.css==' . $path . '/modules/carousel/jc-1e8fix.css';

}
}

if (Jetpack::is_module_active('custom-css')){
$this->top_replace_old[]=' id="wp-custom-css"';
$this->top_replace_new[]="";
}

if ( Jetpack::is_active() && Jetpack::is_module_active('stats')){
$this->auto_config_internal_css='img#wpstats{display:none}';
$this->top_replace_old[]="<style type='text/css'>img#wpstats{display:none}</style>";
$this->top_replace_new[]="";

$this->top_replace_old[]= set_url_scheme("http://stats.wp.com/");
$this->top_replace_new[]= network_home_url('/_get/stats/');
}

if (Jetpack::is_module_active('publicize') || Jetpack::is_module_active('sharedaddy')){
add_filter('jetpack_open_graph_image_default', function() { return network_home_url("/_get/ws0i/blank.jpg"); }, 2);


$this->top_replace_old[]="<!-- Jetpack Open Graph Tags -->";
$this->top_replace_new[]='';
}

?>