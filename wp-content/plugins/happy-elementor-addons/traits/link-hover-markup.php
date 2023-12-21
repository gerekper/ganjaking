<?php
/**
 * Link Hover Markup trait
 */
namespace Happy_Addons\Elementor\Traits;

defined('ABSPATH') || exit;

/**
 * Trait to load markup for link hover
 */

trait Link_Hover_Markup
{
    public static function render_metis_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--metis" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_io_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--io" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_thebe_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--thebe" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_leda_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--leda" data-text="$link_text" $target $nofollow>
                <span>$link_text</span>
            </a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_ersa_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--ersa" $target $nofollow>
                <span>$link_text</span>
            </a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_elara_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--elara" $target $nofollow>
                <span>$link_text</span>
            </a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_dia_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--dia" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_kale_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--kale" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_carpo_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--carpo" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_helike_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--helike" $target $nofollow><span>$link_text</span></a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_mneme_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--mneme" $target $nofollow>$link_text</a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_iocaste_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--iocaste" $target $nofollow>
                <span>$link_text</span>
                <svg class="ha_link__graphic ha_link__graphic--slide" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
                    <path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path>
                </svg>
            </a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_herse_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--herse" $target $nofollow>
                <span>$link_text</span>
                <svg class="ha_link__graphic ha_link__graphic--stroke ha_link__graphic--arc" width="100%" height="18" viewBox="0 0 59 18"><path d="M.945.149C12.3 16.142 43.573 22.572 58.785 10.842" pathLength="1"/></svg>
            </a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_carme_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--carme" $target $nofollow>
                <span>$link_text</span>
                <svg class="ha_link__graphic ha_link__graphic--stroke ha_link__graphic--scribble" width="100%" height="9" viewBox="0 0 101 9"><path d="M.426 1.973C4.144 1.567 17.77-.514 21.443 1.48 24.296 3.026 24.844 4.627 27.5 7c3.075 2.748 6.642-4.141 10.066-4.688 7.517-1.2 13.237 5.425 17.59 2.745C58.5 3 60.464-1.786 66 2c1.996 1.365 3.174 3.737 5.286 4.41 5.423 1.727 25.34-7.981 29.14-1.294" pathLength="1"/></svg>
            </a>
        </link-hover>
EOF;
        echo $markup;
    }

    public static function render_eirene_markup( $settings ){
        $link_text = $settings['link_text'];

        $link_url = $settings['link_url']['url'];
        $target = $settings['link_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['link_url']['nofollow'] ? ' rel="nofollow"' : '';

        $markup = <<<EOF
        <link-hover class="ha_content__item">
            <a href="$link_url" class="ha-link ha-link--eirene" $target $nofollow><span>$link_text</span></a>
        </link-hover>
EOF;
        echo $markup;
    }
}