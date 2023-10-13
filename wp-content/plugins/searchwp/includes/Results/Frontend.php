<?php

namespace SearchWP\Results;

use SearchWP\Forms\Frontend as SearchFormsFrontend;
use SearchWP\Forms\Storage;
use SearchWP\Support\Arr;

/**
 * Display search results page on the frontend.
 *
 * @since 4.3.6
 */
class Frontend {

    /**
     * Init.
     *
     * @since 4.3.6
     */
    public function init() {

        $this->hooks();
    }

    /**
     * Hooks.
     *
     * @since 4.3.6
     */
    public function hooks() {

        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'assets' ] );
        add_filter( 'template_include', [ __CLASS__, 'render' ], PHP_INT_MAX );
    }

    /**
     * Load frontend assets.
     *
     * @since 4.3.6
     */
    public static function assets() {

        wp_enqueue_style(
            'searchwp-results-page',
            SEARCHWP_PLUGIN_URL . 'assets/css/frontend/results-page.css',
            [],
            SEARCHWP_VERSION
        );

        wp_add_inline_style( 'searchwp-results-page', self::get_inline_styles() );
    }

    /**
     * Get dynamic inline styles based on current settings.
     *
     * @since 4.3.6
     */
    private static function get_inline_styles() {

        $settings = Settings::get();

        $css_array = [];

        $title_selector = '.swp-result-item .entry-title';
        if ( ! empty( $settings['swp-title-color'] ) ) {
            Arr::set( $css_array, "{$title_selector}/color", esc_html( $settings['swp-title-color'] ), '/' );
        }
        if ( ! empty( $settings['swp-title-font-size'] ) ) {
            Arr::set( $css_array, "{$title_selector}/font-size", absint( $settings['swp-title-font-size'] ) . 'px', '/' );
        }

        $price_selector = '.swp-result-item .swp-result-item--price, .swp-result-item .swp-result-item--price *';
        if ( ! empty( $settings['swp-price-color'] ) ) {
            Arr::set( $css_array, "{$price_selector}/color", esc_html( $settings['swp-price-color'] ), '/' );
        }
        if ( ! empty( $settings['swp-price-font-size'] ) ) {
            Arr::set( $css_array, "{$price_selector}/font-size", absint( $settings['swp-price-font-size'] ) . 'px', '/' );
        }

        $button_selector = '.swp-result-item a.swp-result-item--button';
        if ( ! empty( $settings['swp-button-font-color'] ) ) {
            Arr::set( $css_array, "{$button_selector}/color", esc_html( $settings['swp-button-font-color'] ), '/' );
        }
        if ( ! empty( $settings['swp-button-bg-color'] ) ) {
            Arr::set( $css_array, "{$button_selector}/background-color", esc_html( $settings['swp-button-bg-color'] ), '/' );
        }
        if ( ! empty( $settings['swp-button-font-size'] ) ) {
            Arr::set( $css_array, "{$button_selector}/font-size", absint( $settings['swp-button-font-size'] ) . 'px', '/' );
        }

        return self::css_array_to_css( $css_array );
    }

    /**
     * Render page.
     *
     * @since 4.3.6
     *
     * @param string $template The path of the template to include.
     *
     * @return string
     */
    public static function render( $template ) {

        if ( ! isset( $_GET['swps'] ) ) {
            return $template;
        }

        $form_id = isset( $_GET['swp_form']['form_id'] ) ? absint( $_GET['swp_form']['form_id'] ) : 0;
        $engine = 'default';

        if ( ! empty( $form_id ) ) {
            $form   = Storage::get( $form_id );
            $engine = ! empty( $form['engine'] ) ? $form['engine'] : 'default';
        }

        // TODO: Include template instead of echo output.

        $settings = Settings::get();

        // Retrieve applicable query parameters.
        $search_query = isset( $_GET['swps'] ) ? sanitize_text_field( $_GET['swps'] ) : null;
        $search_page  = isset( $_GET['swppg'] ) ? absint( $_GET['swppg'] ) : 1;

        // Perform the search.
        $search_results    = [];
        $search_pagination = '';
        $per_page = absint( $settings['swp-results-per-page'] );
        if ( class_exists( '\\SearchWP\\Query' ) ) {
            $search_args = [
                'engine' => $engine, // The Engine name.
                'fields' => 'all',          // Load proper native objects of each result.
                'page'   => $search_page,
            ];

            if ( ! empty( $per_page ) ) {
                $search_args['per_page'] = $per_page;
            }
            $searchwp_query = new \SearchWP\Query( $search_query, $search_args );

            $search_results = $searchwp_query->get_results();

            $search_pagination = paginate_links( array(
                'format'  => '?swppg=%#%',
                'current' => $search_page,
                'total'   => $searchwp_query->max_num_pages,
                'prev_text' => "&larr;",
                'next_text' => "&rarr;",
            ) );
        }

        if ( wp_is_block_theme() ) {
            ?><!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <head>
                <meta charset="<?php bloginfo( 'charset' ); ?>" />
                <?php wp_head(); ?>
            </head>
            <body <?php body_class(); ?>>
                <?php wp_body_open(); ?>
                <div class="wp-site-blocks">
                <?php block_template_part( 'header' ); ?>
                <main class="wp-block-group swp-rp-main">
                    <header class="swp-rp-page-header">
                        <?php echo ! empty( $form['id'] ) ? SearchFormsFrontend::render( [ 'id' => $form['id'] ] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <h1 class="page-title">
                        <?php
                            echo esc_html(
                                sprintf(
                                    /* translators: %s is the search term. */
                                    __( 'Search results for: "%s"', 'searchwp' ),
                                    $search_query
                                )
                            );
                        ?>
                    </h1>
                    </header>
            <?php

        } else {
            get_header('searchwp');
            ?>
            <div id="content" class="site-content">
                <div id="primary" class="content-area">
                    <main id="main" class="site-main swp-rp-main">
                        <header class="swp-rp-page-header">
                            <?php echo ! empty( $form['id'] ) ? SearchFormsFrontend::render( [ 'id' => $form['id'] ] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                            <h1 class="page-title">
                                <?php
                                    echo esc_html(
                                        sprintf(
                                            /* translators: %s is the search term. */
                                            __( 'Search results for: "%s"', 'searchwp' ),
                                            $search_query
                                        )
                                    );
                                ?>
                            </h1>
                        </header>
                <?php
        }
        ?>
        <div class="<?php echo esc_attr( self::get_container_classes( $settings ) ); ?>">

        <?php if ( ! empty( $search_results ) ) : ?>
            <?php foreach ( $search_results as $search_result ) : ?>
                <?php $display_data = self::get_display_data( $search_result ); ?>
                <article id="post-0" class="swp-result-item post-0 post type-post status-publish format-standard hentry category-uncategorized entry">
                    <?php if ( ! empty( $display_data['image_html'] ) && ! empty( $settings['swp-image-size'] ) && $settings['swp-image-size'] !== 'none' ) : ?>
                        <div class="swp-result-item--img-container">
                            <div class="swp-result-item--img">
                                <?php echo wp_kses_post( $display_data['image_html'] ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="swp-result-item--info-container">
                        <h2 class="entry-title">
                            <a href="<?php echo esc_url( $display_data['permalink'] ); ?>">
                                <?php echo wp_kses_post( $display_data['title'] ); ?>
                            </a>
                        </h2>
                        <?php if ( ! empty( $settings['swp-description-enabled'] ) ) : ?>
                            <p class="swp-result-item--desc">
                                <?php echo wp_kses_post( $display_data['content'] ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( in_array( $display_data['type'], [ 'product', 'download' ], true ) ) : ?>
                            <p class="swp-result-item--price">
                                <?php echo wp_kses_post( $display_data['type'] === 'product' ? self::get_product_price_html( $display_data['id'] ) : self::get_download_price_html( $display_data['id'] ) ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['swp-button-enabled'] ) ) : ?>
                            <a href="<?php echo esc_url( $display_data['permalink'] ); ?>" class="swp-result-item--button">
                                <?php echo ! empty( $settings['swp-button-label'] ) ? esc_html( $settings['swp-button-label'] ) : esc_html__( 'Read More', 'searchwp' ); ?>
                            </a>
                                <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            </div><!-- End of .swp-search-results -->

            <?php if ( $searchwp_query->max_num_pages > 1 ) : ?>
                <div class="navigation pagination" role="navigation">
                    <h2 class="screen-reader-text"><?php esc_html_e( 'Results navigation', 'searchwp' ); ?></h2>
                    <div class="<?php echo esc_attr( self::get_pagination_classes( $settings ) ); ?>"><?php echo wp_kses_post( $search_pagination ); ?></div>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No results found, please search again.', 'searchwp' ); ?></p>
        <?php endif; ?>
        <?php

        if ( wp_is_block_theme() ) {
                    ?>
                        </main>
                        <footer class="wp-block-template-part">
                            <?php block_template_part( 'footer' ); ?>
                        </footer>
                    <div><!-- End of .wp-site-blocks -->

                    <?php wp_footer(); ?>
                </body>
            </html>
            <?php
        } else {
            ?>
                    </main>
                </div><!-- End of #primary -->
            </div><!-- End of #content -->
            <?php
            get_footer( 'searchwp' );
        }

        return '';
    }

    /**
     * Get the result data to display in the template.
     *
     * @since 4.3.6
     *
     * @param \WP_Post|\WP_User|\WP_Term|mixed $result Result object.
     *
     * @return array
     */
    private static function get_display_data( $result ) {

        if ( $result instanceof \WP_Post ) {
            $data = [
                'id'         => absint( $result->ID ),
                'type'       => get_post_type( $result ),
                'title'      => get_the_title( $result ),
                'permalink'  => get_the_permalink( $result ),
                'image_html' => get_the_post_thumbnail( $result ),
                'content'    => get_the_excerpt( $result ),
            ];
        }

        if ( $result instanceof \WP_User ) {
            $data = [
                'id'         => absint( $result->ID ),
                'type'       => 'user',
                'title'      => $result->data->display_name,
                'permalink'  => get_author_posts_url( $result->data->ID ),
                'image_html' => get_avatar( $result->data->ID ),
                'content'    => get_the_author_meta( 'description', $result->data->ID ),
            ];
        }

        if ( $result instanceof \WP_Term ) {
            $data = [
                'id'         => absint( $result->term_id ),
                'type'       => 'taxonomy-term',
                'title'      => $result->name,
                'permalink'  => get_term_link( $result->term_id, $result->taxonomy ),
                'image_html' => '',
                'content'    => $result->description,
            ];
        }

        $defaults = [
            'id'         => 0,
            'type'       => 'unknown',
            'title'      => '',
            'permalink'  => '',
            'image_html' => '',
            'content'    => '',
        ];

        $data = apply_filters( 'searchwp\results\entry\data', $data, $result );

        // Make sure that default array structure is preserved.
        return is_array( $data ) ? array_merge( $defaults, $data ) : $defaults;
    }

    /**
     * Get search results page container classes.
     *
     * @since 4.3.6
     *
     * @param array $settings Search Results Page settings.
     *
     * @return string
     */
    private static function get_container_classes( $settings ) {

        $classes = [
            'swp-search-results',
        ];

        if ( $settings['swp-layout-style'] === 'grid' ) {
            $classes[] = 'swp-grid';
            $per_row   = absint( $settings['swp-results-per-row'] );
            if ( ! empty( $per_row ) ) {
                $classes[] = 'swp-grid--cols-' . $per_row;
            }
        }

        if ( $settings['swp-layout-style'] === 'list' ) {
            $classes[] = 'swp-flex';
        }

        $image_size = $settings['swp-image-size'];
        if ( empty( $image_size ) || $image_size === 'none' ) {
            $classes[] = 'swp-rp--img-none';
        }
        if ( $image_size === 'small' ) {
            $classes[] = 'swp-rp--img-sm';
        }
        if ( $image_size === 'medium' ) {
            $classes[] = 'swp-rp--img-m';
        }
        if ( $image_size === 'large' ) {
            $classes[] = 'swp-rp--img-l';
        }

        return implode( ' ', $classes );
    }

    /**
     * Get search results page pagination classes.
     *
     * @since 4.3.6
     *
     * @param array $settings Search Results Page settings.
     *
     * @return string
     */
    private static function get_pagination_classes( $settings ) {
        $classes = [
            'nav-links',
        ];

        if ( $settings['swp-pagination-style'] === 'circular' ) {
            $classes[] = 'swp-results-pagination';
            $classes[] = 'swp-results-pagination--circular';
        }

        if ( $settings['swp-pagination-style'] === 'boxed' ) {
            $classes[] = 'swp-results-pagination';
            $classes[] = 'swp-results-pagination--boxed';
        }

        return implode( ' ', $classes );
    }

    /**
     * Get WooCommerce product price HTML.
     *
     * @since 4.3.6
     *
     * @param int $product_id WooCommerce product id.
     *
     * @return string
     */
    private static function get_product_price_html( $product_id ) {

        if ( ! function_exists( 'wc_get_product' ) ) {
            return '';
        }

        $product = wc_get_product( $product_id );

        if ( empty( $product ) ) {
            return '';
        }

        return $product->get_price_html();
    }

    /**
     * Get EDD product price HTML.
     *
     * @since 4.3.6
     *
     * @param int $download_id EDD download id.
     *
     * @return string
     */
    private static function get_download_price_html( $download_id ) {

        if ( ! function_exists( 'edd_price' ) ) {
            return '';
        }

        return edd_price( $download_id, false );
    }

    /**
     * Recursive function that generates from a multidimensional array of CSS rules, a valid CSS string.
     *
     * @since 4.3.6
     *
     * @param array $rules  CSS rules array.
     *   An array of CSS rules in the form of:
     *   array('selector'=>array('property' => 'value')). Also supports selector
     *   nesting, e.g.,
     *   array('selector' => array('selector'=>array('property' => 'value'))).
     * @param int   $indent Indentation level.
     *
     * @return string A CSS string of rules. This is not wrapped in <style> tags.
     * @source http://matthewgrasmick.com/article/convert-nested-php-array-css-string
     */
    private static function css_array_to_css( $rules, $indent = 0 ) {
        $css    = '';
        $prefix = str_repeat( '  ', $indent );

        foreach ( $rules as $key => $value ) {
            if ( is_array( $value ) ) {
                $selector   = $key;
                $properties = $value;

                $css .= $prefix . "$selector {\n";
                $css .= $prefix . self::css_array_to_css( $properties, $indent + 1 );
                $css .= $prefix . "}\n";
            } else {
                $property = $key;
                $css     .= $prefix . "$property: $value;\n";
            }
        }

        return $css;
    }
}
