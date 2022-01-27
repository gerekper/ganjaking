<?php

if (is_admin()) {
    return false;
}

/**
 * Custom template parts for this theme.
 *
 * preloader, backtotop, conten-none
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package agro
*/



/*************************************************
## START PRELOADER
*************************************************/

if (! function_exists('agro_preloader')) {
    function agro_preloader()
    {
        if ('0' != agro_settings('pre_onoff')) {
            if ('default' == agro_settings('pre_type')) {
                echo'<div class="se-pre-con"></div>';
            } else {
                echo'<div id="nt-preloader" class="preloader">
					<div class="loader'.agro_settings('pre_type').'"></div>
				</div>';
            }
        }
    }
}

/*************************************************
##  BACKTOP
*************************************************/

if (! function_exists('agro_backtop')) {
    function agro_backtop()
    {
        if ('1' == agro_settings('backtotop_onoff')) {
            echo '<div id="btn-to-top-wrap">
				<a id="btn-to-top" class="circled" href="javascript:void(0);" data-visible-offset="800"></a>
			</div>';
        }
    }
}


/*************************************************
##  CONTENT NONE
*************************************************/


if (! function_exists('agro_content_none')) {
    function agro_content_none()
    {
        ?>

	<div class="content-none-container">

		<h3 class="page-title"><?php esc_html_e('Nothing Found', 'agro'); ?></h3>

		<?php if (is_home() && current_user_can('publish_posts')) : ?>

			<p><?php printf(esc_html__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'agro'), esc_url(admin_url('post-new.php'))); ?></p>

			<?php elseif (is_search()) : ?>
			<p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'agro'); ?></p>
			<?php get_search_form(); ?>

			<?php else : ?>
			<p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'agro'); ?></p>

			<?php get_search_form(); ?>

		<?php endif; ?>

	</div> <!-- End article -->

<?php
    }
}
