<?php

//Added for enabling conditional menu
if(userpro_get_option('up_conditional_menu') == 1){
	add_filter( 'wp_nav_menu_objects', 'userpro_filter_wp_nav_menu_args', 10, 2 );
}
function userpro_filter_wp_nav_menu_args( $menu , $args) {

	$pages = get_option('userpro_pages') + get_option('userpro_sc_pages') + get_option('userpro_connections') ;

	foreach($menu as $k => $v){
		if(is_user_logged_in()){
			if( isset($pages['login']) && $v->object_id == $pages['login'] || isset($pages['register']) && $v->object_id == $pages['register'] ){
				unset($menu[$k]);
			}
		}else{
			if( isset($pages['connections']) && $v->object_id == $pages['connections'] || isset($pages['following']) && $v->object_id == $pages['following'] || isset($pages['followers']) && $v->object_id == $pages['followers'] || isset($pages['dashboard']) && $v->object_id == $pages['dashboard'] || isset($pages['edit']) && $v->object_id == $pages['edit'] || isset($pages['logout_page']) && $v->object_id == $pages['logout_page'] ){
				unset($menu[$k]);
			}
		}
	}
	return $menu;
}


/* Overrides default avatars */
function userpro_get_avatar( $avatar, $id_or_email, $size, $default, $alt='' ) {
	global $userpro;
	require_once(userpro_path.'lib/BFI_Thumb.php');
	if (isset($id_or_email->user_id)){
		$id_or_email = $id_or_email->user_id;
	} elseif (is_email($id_or_email)){
		$user = get_user_by('email', $id_or_email);
		$id_or_email = $user->ID;
	}

	if ($id_or_email && userpro_profile_data( 'profilepicture', $id_or_email ) ) {
			
		$url = $userpro->file_uri(  userpro_profile_data( 'profilepicture', $id_or_email ), $id_or_email );
		$params = array('width'=>$size);
		if(!userpro_get_option('aspect_ratio')){
			$params['height'] = $size;
		}
		if(userpro_get_option('pimg')==1)
		{
			$crop=bfi_thumb($url,$params);
		}
		else
		{
			$crop = bfi_thumb(get_site_url().(strpos($url,"http") !== false ? urlencode($url) : $url),$params);
		}
		$return = '<img src="'.$crop.'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" class="modified avatar" />';

	} else {

		if ($id_or_email && userpro_profile_data( 'gender', $id_or_email ) ) {
			$gender = strtolower( userpro_profile_data( 'gender', $id_or_email ) );
		} else {
			$gender = 'male'; // default gender
		}

		$userpro_default = userpro_url . 'img/default_avatar_'.$gender.'.jpg';
		$return = '<img src="'.$userpro_default.'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" class="default avatar" />';

	}

	if ( userpro_profile_data( 'profilepicture', $id_or_email ) != '') {
		return $return;
	} else {
		if ( userpro_get_option('use_default_avatars') == 1 ) {
			return $avatar;
		} else {
			return $return;
		}
	}
}
add_filter('get_avatar', 'userpro_get_avatar', 99, 5);

/* shortcode allowed in sidebar */
add_filter('widget_text', 'do_shortcode');

/************ Added by Ranjith for changing bbpress author url ***************/

if( userpro_get_option('bbpress_userpro_link_sync') ){
	add_filter('bbp_get_reply_author_url', 'userpro_bbp_get_reply_author_url', 10, 2 );

	function userpro_bbp_get_reply_author_url( $author_url, $reply_id ){
		global $userpro;
		//return $userpro->permalink( bbp_get_reply_author_id( $reply_id ) , 'profile' );
		$reply_id = bbp_get_reply_id( $reply_id );

		// Check for anonymous user or non-existant user
		if ( !bbp_is_reply_anonymous( $reply_id ) && bbp_user_has_profile( bbp_get_reply_author_id( $reply_id ) ) ) {
			$author_url = $userpro->permalink( bbp_get_reply_author_id( $reply_id ) );
		} else {
			$author_url = get_post_meta( $reply_id, '_bbp_anonymous_website', true );
			if ( empty( $author_url ) ) {
				$author_url = '';
			}
		}
		
		return $author_url;
	}

	add_filter('bbp_get_topic_author_url','userpro_bbp_get_topic_author_url',10,2 );

	function userpro_bbp_get_topic_author_url( $author_url, $topic_id ){
		$topic_id = bbp_get_topic_id( $topic_id );
		global $userpro;
		// Check for anonymous user or non-existant user
		if ( !bbp_is_topic_anonymous( $topic_id ) && bbp_user_has_profile( bbp_get_topic_author_id( $topic_id ) ) ) {
			$author_url = $userpro->permalink( bbp_get_topic_author_id( $topic_id ), 'profile' );
		} else {
			$author_url = get_post_meta( $topic_id, '_bbp_anonymous_website', true );

			// Set empty author_url as empty string
			if ( empty( $author_url ) ) {
				$author_url = '';
			}
		}

		return $author_url;
	}
}
// add badges to bbpress 
add_filter('bbp_has_replies','userpro_get_reply_author_link',9999,2 );

function userpro_get_reply_author_link( $author_link, $r ){
global $userpro;
$reply_id = bbp_get_reply_id( $r->post_id );
$user_id = bbp_get_reply_author_id( $reply_id );
ob_start();
if ( userpro_get_option('lightbox') && userpro_get_option('profile_lightbox') ) { ?>
<div class="bbpress-usepro-div">
<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->profile_photo_url($user_id); ?>" class="userpro-tip-fade lightview" data-lightview-caption="<?php echo $userpro->profile_photo_title( $user_id ); ?>" title="<?php _e('View member photo','userpro'); ?>"><?php echo bbp_get_reply_author_avatar( $reply_id, $r->size ); ?></a></div>
<?php } else { ?>
<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->permalink($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo bbp_get_reply_author_avatar( $reply_id, $r->size );?></a></div>
<?php } ?>
<div class="bbpress-usepro-div-inner">
<a href="<?php echo $userpro->permalink($user_id); ?>" class=""><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php
echo userpro_show_badges( $user_id ); ?>
</div>
</div>
<?php
$output = ob_get_contents();
ob_end_clean();
$author_role = bbp_get_reply_author_role( array( 'reply_id' => $reply_id ) );
$author_link = $output.$r->sep.$author_role;
return $author_link;
}
add_filter('bbp_get_reply_author_link', 'userpro_get_topic_author_link',9999,2 );



function userpro_get_topic_author_link( $author_link, $r ){

global $userpro;

    $reply_id = bbp_get_reply_id( $r['post_id'] );

$user_id = bbp_get_reply_author_id( $reply_id );

ob_start();

if ( userpro_get_option('lightbox') && userpro_get_option('profile_lightbox') ) { ?>

<div class="bbpress-usepro-div">

<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->profile_photo_url($user_id); ?>" class="userpro-tip-fade lightview" data-lightview-caption="<?php echo $userpro->profile_photo_title( $user_id ); ?>" title="<?php _e('View member photo','userpro'); ?>"><?php echo bbp_get_reply_author_avatar( $reply_id, $r['size'] ); ?></a></div>

<?php } else { ?>

<div class="bbpress-usepro-div">

<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->permalink($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo bbp_get_reply_author_avatar( $reply_id, $r['size'] );?></a></div>

<?php } ?>

<div class="bbpress-usepro-div-inner">

<a href="<?php echo $userpro->permalink($user_id); ?>" class=""><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php

echo userpro_show_badges( $user_id ); ?>

</div>

</div>

<?php

$output = ob_get_contents();

ob_end_clean();

$author_role = bbp_get_reply_author_role( array( 'reply_id' => $reply_id ) );

$author_link = $output.$r['sep'].$author_role;

return $author_link;

}
