<?php
    if ( ! ( $popular_search == true && $popular_search !== 'false' && ! empty( betterdocs_pro()->query->popular_search_keyword() ) ) ) {
        return;
    }

    if ( empty( $popular_search_title ) ) {
        $popular_search_title = betterdocs()->customizer->defaults->get( 'betterdocs_popular_search_text' );
    }
?>

<div class="betterdocs-popular-search-keyword">
    <span class="popular-search-title"><?php echo esc_html( $popular_search_title ); ?></span>
    <?php
        foreach ( betterdocs_pro()->query->popular_search_keyword() as $keyword ) {
            echo '<span class="popular-keyword">' . $keyword . '</span>';
        }
    ?>
</div>
