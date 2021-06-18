<?php
add_filter('updb_default_options_array','userpro_personalwall_in_dashboard','11','1');
function userpro_personalwall_in_dashboard($array)
{
	
	$template_path= UPS_PLUGIN_DIR.'templates/';
	$olddata=$array['updb_available_widgets'];
	$newdata= array ('personal'=>array('title'=>'Personal Wall', 'template_path'=>$template_path ));	
    $array['updb_available_widgets']=   array_merge($olddata,$newdata);

	$oldunsetwidgets=$array['updb_unused_widgets'];
	$newunsetwidgets= array('personal');
	$array['updb_unused_widgets']= array_merge($oldunsetwidgets,$newunsetwidgets);

	return $array;
}

add_action('wp_head','socialwall_add_custom_styles', 99999);
function socialwall_add_custom_styles() {?>
<style type="text/css">
.content-text
{
    color:<?php echo userpro_userwall_get_option('postcontent_color');?>
}
</style>
<?php
}
add_action('userpro_after_fields', 'personaldisplay', 99);

 function personaldisplay($args){
 if(is_user_logged_in() && userpro_userwall_get_option('enablepersonalwall')==1)
 {
	if($args['template']=="view")
	 	include_once(UPS_PLUGIN_DIR.'templates/personalwall.php');
  }
}

add_action('wp_head', 'userpro_sw_fbog');
function userpro_sw_fbog() {
	
	if( isset($_GET["postid"]) ){
		$og_check_postid = $_GET["postid"];
		global $post;
		
		$content_post = get_post($og_check_postid);
		$content = $content_post->post_content;
		?>    
	    <meta property="og:image" content="<?php echo $content; ?>"/>
	<?php 
	}
}
