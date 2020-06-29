<?php
/**
 * The main template file.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

$translate['search-placeholder'] = mfn_opts_get('translate') ? mfn_opts_get('translate-search-placeholder','Enter your search') : __('Enter your search','betheme');
?>

<form method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">

	<?php if( mfn_opts_get('header-search') == 'shop' ): ?>
		<input type="hidden" name="post_type" value="product" />
	<?php endif;?>

	<i class="icon_search icon-search-fine"></i>
	<a href="#" class="icon_close"><i class="icon-cancel-fine"></i></a>

	<input type="text" class="field" name="s" placeholder="<?php echo esc_html($translate['search-placeholder']); ?>" />
	<input type="submit" class="display-none" value="" />

</form>
