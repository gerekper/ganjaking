<?php

if ( is_admin() )
return false;



/*************************************************
##  BREADCRUMB

* WordPress Breadcrumbs
* author: Dimox
* version: 2018.10.05
* license: MIT

*************************************************/



function agro_breadcrumbs() {

	/* === OPTIONS === */
	$bred_title = '' != agro_settings( 'bred_title' ) ? esc_html(agro_settings( 'bred_title' )) : esc_html__( 'Home', 'agro');
	$text['home']     = $bred_title; // text for the 'Home' link
	$text['category'] = esc_html__( 'Archive by Category "%s"', 'agro'); // text for a category page
	$text['search']   = esc_html__( 'Search Results for "%s" Query', 'agro'); // text for a search results page
	$text['tag']      = esc_html__( 'Posts Tagged "%s"', 'agro'); // text for a tag page
	$text['author']   = esc_html__( 'Articles Posted by %s', 'agro'); // text for an author page
	$text['404']      = esc_html__( 'Error 404', 'agro'); // text for the 404 page
	$text['page']     = esc_html__( 'Page %s', 'agro'); // text 'Page N'
	$text['cpage']    = esc_html__( 'Comment Page %s', 'agro'); // text 'Comment Page N'

	$wrap_before    = '<div class="nt-breadcrumbs"><ul class="nt-breadcrumbs-list">'; // the opening wrapper tag
	$wrap_after     = '</ul></div>'; // the closing wrapper tag
	$sep            = '<li class="bitem"><i class="fa fa-angle-right"></i></li>'; // separator between crumbs
	$before         = '<li class="active">'; // tag before the current crumb
	$after          = wp_kses( '</li>', agro_allowed_html() ); // tag after the current crumb

	$show_on_home   = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
	$show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
	$show_current   = 1; // 1 - show current page title, 0 - don't show
	$show_last_sep  = 1; // 1 - show last separator, when current page title is not displayed, 0 - don't show
	/* === END OF OPTIONS === */

	global $post;
	$home_url       = esc_url( home_url('/') );
	$link           = '<li class="bitem"><span>';
	$link          .= '<a class="breadcrumbs__link" href="%1$s"><span >%2$s</span></a>';
	$link          .= '</span></li>';
	$parent_id      = ( $post ) ? $post->post_parent : '';
	$home_link      = sprintf( $link, $home_url, $text['home'], 1 );

	if ( is_home() || is_front_page() ) {

		if ( $show_on_home ) echo wp_kses( $wrap_before, agro_allowed_html() ) . wp_kses( $home_link, agro_allowed_html() ) . $wrap_after;

	} else {

		$position = 0;

		echo wp_kses( $wrap_before, agro_allowed_html() );

		if ( $show_home_link ) {
			$position += 1;
			echo wp_kses( $home_link, agro_allowed_html() );
		}

		if ( is_category() ) {
			$parents = get_ancestors( get_query_var('cat'), 'category' );
			foreach ( array_reverse( $parents ) as $cat ) {
				$position += 1;
				if ( $position > 1 ) echo wp_kses( $sep, agro_allowed_html() );
				echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
			}
			if ( get_query_var( 'paged' ) ) {
				$position += 1;
				$cat = get_query_var('cat');
				echo wp_kses( $sep, agro_allowed_html() ) . sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
				echo wp_kses( $sep, agro_allowed_html() ) . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
			} else {
				if ( $show_current ) {
					if ( $position >= 1 ) echo wp_kses( $sep, agro_allowed_html() );
					echo wp_kses( $before, agro_allowed_html() ) . sprintf( $text['category'], single_cat_title( '', false ) ) . $after;
				} elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
			}

		} elseif ( is_search() ) {
			if ( $show_home_link && $show_current || ! $show_current && $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
			if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . sprintf( $text['search'], get_search_query() ) . $after;

		} elseif ( is_year() ) {
			if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
			if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . get_the_time('Y') . $after;
			elseif ( $show_home_link && $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( is_month() ) {
			if ( $show_home_link ) echo wp_kses( $sep, agro_allowed_html() );
			$position += 1;
			echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position );
			if ( $show_current ) echo wp_kses( $sep, agro_allowed_html() ) . $before . get_the_time('F') . $after;
			elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( is_day() ) {
			if ( $show_home_link ) echo wp_kses( $sep, agro_allowed_html() );
			$position += 1;
			echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position ) . $sep;
			$position += 1;
			echo sprintf( $link, get_month_link( get_the_time('Y'), get_the_time('m') ), get_the_time('F'), $position );
			if ( $show_current ) echo wp_kses( $sep, agro_allowed_html() ) . $before . get_the_time('d') . $after;
			elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( is_single() && ! is_attachment() ) {
			if ( get_post_type() != 'post' ) {

				$position += 1;
				$post_type = get_post_type_object( get_post_type() );
				$label = apply_filters( 'agro_shop_bread_title', $post_type->labels->name );
				if ( $position > 1 ) echo wp_kses( $sep, agro_allowed_html() );
				echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $label, $position );
				if ( $show_current ) echo wp_kses( $sep, agro_allowed_html() ) . $before . get_the_title() . $after;
				elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
			} else {
				$cat = get_the_category(); $catID = $cat[0]->cat_ID;
				$parents = get_ancestors( $catID, 'category' );
				$parents = array_reverse( $parents );
				$parents[] = $catID;
				foreach ( $parents as $cat ) {
					$position += 1;
					if ( $position > 1 ) echo wp_kses( $sep, agro_allowed_html() );
					echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
				}
				if ( get_query_var( 'cpage' ) ) {
					$position += 1;
					echo wp_kses( $sep, agro_allowed_html() ) . sprintf( $link, get_permalink(), get_the_title(), $position );
					echo wp_kses( $sep, agro_allowed_html() ) . $before . sprintf( $text['cpage'], get_query_var( 'cpage' ) ) . $after;
				} else {
					if ( $show_current ) echo wp_kses( $sep, agro_allowed_html() ) . $before . get_the_title() . $after;
					elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
				}
			}

		} elseif ( is_post_type_archive() ) {
			$post_type = get_post_type_object( get_post_type() );
			$label = apply_filters( 'agro_shop_bread_title', $post_type->label );
			if ( get_query_var( 'paged' ) ) {
				$position += 1;
				if ( $position > 1 ) echo wp_kses( $sep, agro_allowed_html() );
				echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $label, $position );
				echo wp_kses( $sep, agro_allowed_html() ) . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
			} else {
				if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
				if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . $label . $after;
				elseif ( $show_home_link && $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
			}

		} elseif ( is_attachment() ) {
			$parent = get_post( $parent_id );
			$cat = get_the_category( $parent->ID ); $catID = $cat[0]->cat_ID;
			$parents = get_ancestors( $catID, 'category' );
			$parents = array_reverse( $parents );
			$parents[] = $catID;
			foreach ( $parents as $cat ) {
				$position += 1;
				if ( $position > 1 ) echo wp_kses( $sep, agro_allowed_html() );
				echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
			}
			$position += 1;
			echo wp_kses( $sep, agro_allowed_html() ) . sprintf( $link, get_permalink( $parent ), $parent->post_title, $position );
			if ( $show_current ) echo wp_kses( $sep, agro_allowed_html() ) . $before . get_the_title() . $after;
			elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( is_page() && ! $parent_id ) {
			if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
			if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . get_the_title() . $after;
			elseif ( $show_home_link && $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( is_page() && $parent_id ) {
			$parents = get_post_ancestors( get_the_ID() );
			foreach ( array_reverse( $parents ) as $pageID ) {
				$position += 1;
				if ( $position > 1 ) echo wp_kses( $sep, agro_allowed_html() );
				echo sprintf( $link, get_page_link( $pageID ), get_the_title( $pageID ), $position );
			}
			if ( $show_current ) echo wp_kses( $sep, agro_allowed_html() ) . $before . get_the_title() . $after;
			elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( is_tag() ) {
			if ( get_query_var( 'paged' ) ) {
				$position += 1;
				$tagID = get_query_var( 'tag_id' );
				echo wp_kses( $sep, agro_allowed_html() ) . sprintf( $link, get_tag_link( $tagID ), single_tag_title( '', false ), $position );
				echo wp_kses( $sep, agro_allowed_html() ) . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
			} else {
				if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
				if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after;
				elseif ( $show_home_link && $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
			}

		} elseif ( is_author() ) {
			$author = get_userdata( get_query_var( 'author' ) );
			if ( get_query_var( 'paged' ) ) {
				$position += 1;
				echo wp_kses( $sep, agro_allowed_html() ) . sprintf( $link, get_author_posts_url( $author->ID ), sprintf( $text['author'], $author->display_name ), $position );
				echo wp_kses( $sep, agro_allowed_html() ) . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after;
			} else {
				if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
				if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . sprintf( $text['author'], $author->display_name ) . $after;
				elseif ( $show_home_link && $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );
			}

		} elseif ( is_404() ) {
			if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
			if ( $show_current ) echo wp_kses( $before, agro_allowed_html() ) . $text['404'] . $after;
			elseif ( $show_last_sep ) echo wp_kses( $sep, agro_allowed_html() );

		} elseif ( has_post_format() && ! is_singular() ) {
			if ( $show_home_link && $show_current ) echo wp_kses( $sep, agro_allowed_html() );
			echo get_post_format_string( get_post_format() );
		}

		echo wp_kses( $wrap_after, agro_allowed_html() );

	}
}
