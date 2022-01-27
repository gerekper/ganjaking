<?php

if (is_admin()) {
    return false;
}


/*************************************************
## HERO FUNCTION
*************************************************/


if (! function_exists('agro_hero_section')) {
    function agro_hero_section()
    {
        if (is_404()) { // error page
            $name 	= 'error';
            $h_t	= esc_html__('404 - Not Found', 'agro');
        } elseif (is_archive()) { // blog and cpt archive page
            $name 	= 'archive';
            $h_t	= get_the_archive_title();
        } elseif (is_search()) { // search page
            $name 	= 'search';
            $h_t	= esc_html__('Search results for :', 'agro') . " " . get_search_query() ;
        } elseif (is_home() or is_front_page()) { // blog post loop page index.php or your choise on settings
            $name 	= 'blog';
            $h_t	= get_bloginfo('name');
        } elseif (is_single() and ! is_singular('portfolio')) { // blog post single/singular page
            $name 	= 'single';
            $h_t	= get_the_title();
        } elseif (is_singular('portfolio')) { // it is cpt and if you want use another clone this condition and add your cpt name as portfolio
            $name 	= 'single_portfolio';
            $h_t	= get_the_title();
        } elseif (is_page()) {	// default or custom page
            $name 	= 'page';
            $h_t	= get_the_title();
        }

        global $agro;

        // page hero options from metabox
        if (is_page()) {

            // hero display
            $h_v = (rwmb_meta('agro_page_hero_display') != '') ? rwmb_meta('agro_page_hero_display') : '1';
            // page title
            $h_t = (rwmb_meta('agro_page_title') != '') ? rwmb_meta('agro_page_title') : $h_t;
            // page slogan
            $h_s = (rwmb_meta('agro_page_slogan') != '') ? rwmb_meta('agro_page_slogan') : '';
            // page description
            $h_d = (rwmb_meta('agro_page_desc') != '') ? rwmb_meta('agro_page_desc') : '';
            // page breadcrumbs
            $h_b = isset($agro['breadcrumbs_onoff']) ? $agro['breadcrumbs_onoff']  : '0';
            // page hero alignment
            $h_a = (rwmb_meta('agro_page_hero_align') != '') ? rwmb_meta('agro_page_hero_align') : 'text-left';
            $h_o = (rwmb_meta('agro_page_hero_overlay') != '') ? ' hero-overlay' : '';
        } else {

            // page hero options from theme-options panel for blog pages
            // hero display
            $h_v = isset($agro[$name.'_hero_onoff']) ? $agro[$name.'_hero_onoff'] : '1';
            // page title
            $h_t = isset($agro[$name.'_title']) && $agro[$name.'_title'] != '' ? $agro[$name.'_title'] : $h_t;
            // page slogan
            $h_s = isset($agro[$name.'_slogan']) ? $agro[$name.'_slogan'] : '';
            // page description
            $h_d = isset($agro[$name.'_desc']) ? $agro[$name.'_desc'] : '';
            // page breadcrumbs
            $h_b = isset($agro['breadcrumbs_onoff']) ? $agro['breadcrumbs_onoff']  : '0';
            // page hero alignment
            $h_a = isset($agro[$name.'_hero_alignment']) ? $agro[$name.'_hero_alignment'] : 'text-left';
            $h_o = isset($agro[$name.'_hero_overlay']) ? ' hero-overlay' : '';
        }

        if ($h_v != '0' and $h_t != '') {
            echo '<div id="hero" class="jarallax page-id-'. get_the_ID() .' hero-container'.$h_o.'" data-speed="0.7" data-img-position="50% 80%">
					<div class="container ">
						<div class="row">
							<div class="col-lg-12">
								<div class="hero-content '. esc_attr($h_a) .'">
									<div class="hero-innner-last-child">';

                                        // page hero subtitle
                                        $page_subtitle = $h_s != '' ? '<span class="nt-hero-subtitle">'. wp_kses( $h_s, agro_allowed_html() ) .'</span> ' : '';

                                        // page hero title
                                        if ($h_t != '') {
                                            echo ' <h1 class="nt-hero-title __title">'.$page_subtitle. wp_kses( $h_t, agro_allowed_html() ) .'</h1>';
                                        }

                                        // page hero description
                                        if ($h_d != '') {
                                            echo '<p class="nt-hero-description">'. wp_kses( $h_d, agro_allowed_html() ) .'</p>';
                                        }

                                        // page breadcrumbs
                                        if( $h_b != '0' ) {
                                            if ( function_exists( 'bcn_display') ) {
                                                bcn_display();
                                            } else {
                                                agro_breadcrumbs();
                                            }
                                        }

                                        echo '</div><!-- End hero-innner-last-child -->

								</div><!-- End hero-content -->
							</div><!-- End column -->
						</div><!-- End row -->
					</div><!-- End container -->
				</div>	<!-- End hero-container -->';
        } // hide hero area
    }
}
