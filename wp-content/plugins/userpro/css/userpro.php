<?php global $userpro; ?>

<?php
	if ( userpro_get_option('customfont') ) {
		$font = userpro_get_option('customfont');
	} elseif ( userpro_get_option('googlefont') ) {
		$font = userpro_get_option('googlefont');
	} else {
		$font = 'inherit'; // fallback
	}

	if (isset( $userpro->temp_id ) && $userpro->temp_id != '' ) {
		$user_id = $userpro->temp_id;

	}
?>

<style type="text/css">

div.userpro-awsm-pic {
	margin-left: -<?php echo ($memberlist_v2_pic_size/ 2) +5; ?>px;
	top: -<?php echo ($memberlist_v2_pic_size/ 2) +5; ?>px;
}

div.userpro-awsm-pic img {
	width: <?php echo $memberlist_v2_pic_size; ?>px;
	height: <?php echo $memberlist_v2_pic_size; ?>px;
}

div.userpro,
div.emd-main,
div.emd-filters,
div.userpro-search-results,
div.userpro-label label,
div.userpro input,
div.userpro textarea,
div.userpro select,
div.userpro-field textarea.userpro_editor,
div.userpro-msg-overlay-content,
div.userpro-msg-overlay-content input,
div.userpro-msg-overlay-content textarea,
div.userpro-notifier
{
	font-family: <?php echo $font; ?>;
}

<?php //Show custom background
$default_background_img = userpro_get_option('default_background_img');
if ( (isset($user_id) && $userpro->has('custom_profile_bg', $user_id)) || !empty($default_background_img) ) { ?>

div.userpro-<?php echo $i; ?>.userpro-id-<?php if(!empty($user_id)) echo $user_id; ?> div.userpro-centered,
div.userpro-<?php echo $i; ?>.userpro-id-<?php if(!empty($user_id)) echo $user_id; ?> div.userpro-centered-header-only
{
	background-image: url(<?php echo $userpro->correct_space_in_url(  userpro_profile_data('custom_profile_bg', $user_id) ); ?>) !important;
	background-size: cover;
	-webkit-background-origin:border;
	background-repeat: no-repeat;
}

<?php if (!empty($user_id) && userpro_profile_data('custom_profile_color', $user_id) == userpro_get_option('heading_light') ) { ?>
div.userpro-<?php echo $i; ?>.userpro-id-<?php if(!empty($user_id)) echo $user_id; ?> div.userpro-profile-name,
div.userpro-<?php echo $i; ?>.userpro-id-<?php if(!empty($user_id)) echo $user_id; ?> div.userpro-profile-name a {
	color: #fff !important;
}
<?php } ?>

<?php } // End custom background ?>

div.userpro-<?php echo $i; ?> {
	max-width: <?php echo $max_width; ?>;
	<?php if ($align == 'left') { ?>
	float: left;
	width: <?php echo $max_width; ?>;
	<?php } ?>
	<?php if ($align == 'right') { ?>
	float: right;
	width: <?php echo $max_width; ?>;
	<?php } ?>
	<?php if ($align == 'center') { ?>
	margin-left: auto;margin-right: auto;
	<?php } ?>
	<?php if ($margin_top) { ?>
	margin-top: <?php echo $margin_top; ?>;
	<?php } ?>
	<?php if ($margin_bottom) { ?>
	margin-bottom: <?php echo $margin_bottom; ?>;
	<?php } ?>
}

<?php if (isset($args['no_header'])) { ?>

div.userpro-<?php echo $i; ?> div.userpro-head,
div.userpro-<?php echo $i; ?> div.userpro-centered {
	display: none !important;
}
div.userpro-<?php echo $i; ?> div.userpro-centered-header-only {
	display: block !important;
}

<?php } ?>

<?php if (isset($args['no_style'])) { ?>

div.userpro-<?php echo $i; ?> div.userpro-head,
div.userpro-<?php echo $i; ?> div.userpro-centered {
	display: none !important;
}
div.userpro-<?php echo $i; ?> div.userpro-centered-header-only {
	display: block !important;
}

div.userpro-<?php echo $i; ?> {
	border: none !important;
	padding: 0 !important;
	background: transparent !important;
}
div.userpro-<?php echo $i; ?> div.userpro-body {
	padding: 0 !important;
}

div.userpro-<?php echo $i; ?> div.userpro-body div.userpro-online-count {
	padding: 0 !important;
}
<?php } ?>

div.userpro-<?php echo $i; ?>.userpro-nostyle {
	max-width: <?php echo isset($card_width)?$card_width:'250px'; ?>;
}

div.userpro-<?php echo $i; ?>.userpro-users {
	max-width: <?php echo $memberlist_width; ?> !important;
}

div.userpro-<?php echo $i; ?> div.userpro-user {
	margin-top: <?php echo $memberlist_pic_topspace; ?>px;
	margin-left: <?php echo $memberlist_pic_sidespace; ?>px;
	margin-right: <?php echo $memberlist_pic_sidespace; ?>px;
}

div.userpro-<?php echo $i; ?> div.userpro-user a.userpro-user-img {
	width: <?php echo $memberlist_pic_size; ?>px;
	height: <?php echo $memberlist_pic_size; ?>px;
}
div.userpro-<?php echo $i; ?> div.userpro-user a.userpro-user-img span {
	top: -<?php echo $memberlist_pic_size; ?>px;
	line-height: <?php echo $memberlist_pic_size; ?>px;
}

div.userpro-<?php echo $i; ?> div.userpro-user div.userpro-user-link {
	width: <?php echo $memberlist_pic_size; ?>px;
}

<?php if ($memberlist_pic_rounded) { ?>
div.userpro-<?php echo $i; ?> div.userpro-user a.userpro-user-img,
div.userpro-<?php echo $i; ?> div.userpro-user a.userpro-user-img span {
	border-radius: 999px !important;
}
<?php } ?>

div.userpro-<?php echo $i; ?> div.userpro-list-item-i {
	width: <?php echo $list_thumb; ?>px;
	height: <?php echo $list_thumb; ?>px;
}

<?php if (isset($list_mini)){ ?>
div.userpro-<?php echo $i; ?> div.userpro-list-item {
	border-bottom: 0px !important;
	padding: 10px 0 0 0;
}

div.userpro-<?php echo $i; ?> div.userpro-list-item img.userpro-profile-badge,
div.userpro-<?php echo $i; ?> div.userpro-list-item img.userpro-profile-badge-right {
	max-width: 14px !important;
	max-height: 14px !important;
}
<?php } ?>

div.userpro-<?php echo $i; ?> div.userpro-online-item-i {
	width: <?php echo $online_thumb; ?>px;
	height: <?php echo $online_thumb; ?>px;
}

<?php if (isset($online_mini)){ ?>
div.userpro-<?php echo $i; ?> div.userpro-online-item {
	border-bottom: 0px !important;
	padding: 10px 0 0 0;
}

div.userpro-<?php echo $i; ?> div.userpro-online-item img.userpro-profile-badge,
div.userpro-<?php echo $i; ?> div.userpro-online-item img.userpro-profile-badge-right {
	max-width: 14px !important;
	max-height: 14px !important;
}
<?php } ?>

div.userpro-<?php echo $i; ?> div.userpro-profile-img {
	width: <?php echo $profile_thumb_size; ?>px;
}

div.emd-user {
    width: <?php if(isset($args['emd_col_width'])) echo $args['emd_col_width']; ?>;
    margin-left: <?php if(isset($args['emd_col_margin'])) echo $args['emd_col_margin']; ?> !important;
}
div.emd-user-img img{
    width: <?php if(isset($args['emd_thumb'])) echo $args['emd_thumb'] ?>px!important;
}

<?php if (userpro_get_option('thumb_style') == 'abit_rounded') { ?>
div.userpro-profile-img img,
div.userpro-pic-profilepicture img,
div.userpro-list-item-i,
div.userpro-list-item-i img,
div.userpro-online-item-i,
div.userpro-online-item-i img,
div.userpro-post.userpro-post-compact div.userpro-post-img img,
a.userpro-online-i-thumb img,
div.userpro-awsm-pic img,
div.userpro-awsm-pic,
div.userpro-sc-img img
{
	border-radius: 3px !important;
}
<?php } ?>

<?php if (userpro_get_option('thumb_style') == 'rounded') { ?>
div.userpro-profile-img img,
div.userpro-pic-profilepicture img,
div.userpro-list-item-i,
div.userpro-list-item-i img,
div.userpro-online-item-i,
div.userpro-online-item-i img,
div.userpro-post.userpro-post-compact div.userpro-post-img img,
a.userpro-online-i-thumb img,
div.userpro-awsm-pic img,
div.userpro-awsm-pic,
div.userpro-sc-img img
{
	border-radius: 999px !important;
}
<?php } ?>

<?php if (userpro_get_option('thumb_style') == 'square') { ?>
div.userpro-profile-img img,
div.userpro-pic-profilepicture img,
div.userpro-list-item-i,
div.userpro-list-item-i img,
div.userpro-online-item-i,
div.userpro-online-item-i img,
div.userpro-post.userpro-post-compact div.userpro-post-img img,
a.userpro-online-i-thumb img,
div.userpro-awsm-pic img,
div.userpro-awsm-pic,
div.userpro-sc-img img
{
	border-radius: 0px !important;
}
<?php } ?>

</style>

