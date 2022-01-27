<?php

if (is_admin()) {
    return false;
}

/**
 * Custom paginations for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package agro
*/


/*************************************************
##  next post css class
*************************************************/


function agro_posts_next_pag_attrs()
{
    return 'class="nt-pagination-link -next"';
}
add_filter('next_posts_link_attributes', 'agro_posts_next_pag_attrs');


/*************************************************
##  prev post css class
*************************************************/


function agro_posts_prev_pag_attrs()
{
    return 'class="nt-pagination-link -previous"';
}
add_filter('previous_posts_link_attributes', 'agro_posts_prev_pag_attrs');


/*************************************************
##  SINLGE POST/CPT NAVIGATION - Display navigation to next/previous post when applicable.
*************************************************/


if (! function_exists('agro_single_navigation')) {
    function agro_single_navigation()
    {
        if ('0' != agro_settings('single_navigation_onoff')) {

            // Don't print empty markup if there's nowhere to navigate.
            $previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
            $next  = get_adjacent_post(false, '', false);

            if (! $next && ! $previous) {
                return;
            } ?>

        	<div class="single-post-navigation mt-30">

            	<!-- Project Pager -->
            	<nav class="nt-single-navigation -style-centered">
                	<ul class="nt-single-navigation-inner">

                    	<li class="nt-single-navigation-item -prev">
            	           <?php previous_post_link('%link', _x('PREVIOUS POST', 'Previous post link', 'agro')); ?>
                    	</li>

                    	<li class="nt-single-navigation-item -next">
                	       <?php next_post_link('%link', _x('NEXT POST', 'Next post link', 'agro')); ?>
                    	</li>

                	</ul>
            	</nav>
            	<!-- Project Pager End -->

        	</div>

    	<?php

        }
    }
}


/*************************************************
## POST PAGINATION - Display post navigation to next/previous post when applicable.
*************************************************/

function agro_index_loop_pagination()
{
    $opt = agro_settings('pag_onoff');

    if ('0' != agro_settings('pag_onoff', '1')) {

        $groupo	= (agro_settings('pag_group') == 'yes') ? ' -group' : '';
        $typeo = agro_settings('pag_type', 'outline');
        $sizeo = agro_settings('pag_size', 'large');
        $aligno	= agro_settings('pag_align', 'left');
        $cornero = agro_settings('pag_corner', 'circle');

        $prev = get_previous_posts_link('<i class="nt-pagination-icon fa fa-angle-left" aria-hidden="true"></i>');
        $next = get_next_posts_link('<i class="nt-pagination-icon fa fa-angle-right" aria-hidden="true"></i>');

        if (is_singular()) {
            return;
        }

        global $wp_query;

        /** Stop execution if there's only 1 page */
        if ($wp_query->max_num_pages <= 1) {
            return;
        }

        $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
        $max   = intval($wp_query->max_num_pages);

        /** Add current page to the array */
        if ($paged >= 1) {
            $links[] = $paged;
        }

        /** Add the pages around the current page to the array */
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if (($paged + 2) <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }

        echo "<div class='nt-pagination -style-".esc_attr($typeo)." -size-".esc_attr($sizeo)." -align-".esc_attr($aligno)." -corner-".esc_attr($cornero)." ".esc_attr($groupo)." '>
    <ul class='nt-pagination-inner'>" . "\n";

        /** Previous Post Link */
        if (get_previous_posts_link()) {
            echo '<li class="nt-pagination-item">' . wp_kses($prev, agro_allowed_html()) . '</li>';
        }

        /** Link to first page, plus ellipses if necessary */
        if (! in_array(1, $links)) {
            $class = 1 == $paged ? ' active' : '';

            printf('<li class="nt-pagination-item%s" ><a class="nt-pagination-link" href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

            if (! in_array(2, $links)) {
                echo '<li class="nt-pagination-item">…</li>';
            }
        }

        /** Link to current page, plus 2 pages in either direction if necessary */
        sort($links);
        foreach ((array) $links as $link) {
            $class = $paged == $link ? ' active' : '';
            printf('<li class="nt-pagination-item%s" ><a class="nt-pagination-link" href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
        }

        /** Link to last page, plus ellipses if necessary */
        if (! in_array($max, $links)) {
            if (! in_array($max - 1, $links)) {
                echo '<li class="nt-pagination-item">…</li>' . "\n";
            }

            $class = $paged == $max ? ' active' : '';
            printf('<li class="nt-pagination-item%s" ><a class="nt-pagination-link" href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
        }

        /** Next Post Link */
        if (get_next_posts_link()) {
            echo '<li class="nt-pagination-item">' . wp_kses($next, agro_allowed_html()) . '</li>';
        }

        echo '</ul></div>' . "\n";
    }
}


/*************************************************
##  LINK PAGES CURRENT CLASS
*************************************************/


function agro_current_link_pages($link)
{
    if (ctype_digit($link)) {
        return '<span class="current">' . $link . '</span>';
    }

    return $link;
}
add_filter('wp_link_pages_link', 'agro_current_link_pages');


/*************************************************
##  LINK PAGES
*************************************************/


if (! function_exists('agro_wp_link_pages')) {
    function agro_wp_link_pages()
    {

        // pagination for page links
        wp_link_pages(array(

            'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages', 'agro') . '</span>',
            'after'       => '</div>',
            'link_before'      => '',
            'link_after'       => '',
            'next_or_number'   => 'number',
            'separator'        => ' ',
            'nextpagelink'     => esc_html__('Next page', 'agro'),
            'previouspagelink' => esc_html__('Previous page', 'agro'),
            'pagelink'         => '%',
            'echo'             => 1

        ));
    }
}
