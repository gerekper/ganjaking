<?php
/**
 * This template is used for pagination.
 *
 * This template can be overridden by copying it to yourtheme/rewardsystem/pagination.php
 *
 * To maintain compatibility, Reward System will update the template files and you have to copy the updated files to your theme
 */
if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
?>
<nav class="pagination pagination-centered woocommerce-pagination">
    <ul>
        <li>
            <span class="rs-pagination rs-first-pagination">
                <a href="<?php echo esc_url( rs_get_endpoint_url( $query_args , '1' , $permalink ) ) ; ?>"> << </a>
            </span>
        <li>
            <span class="rs-pagination rs-prev-pagination">
                <a href="<?php echo esc_url( rs_get_endpoint_url( $query_args , $prev_page_count , $permalink ) ) ; ?>"> < </a>
            </span>
        </li>
        <?php
        for( $i = 1 ; $i <= $page_count ; $i ++ ) {
            $display = false ;
            $classes = array( 'rs-pagination' ) ;

            if( $current_page <= $page_count && $i <= $page_count ) {
                $page_no = $i ;
                $display = true ;
            } else if( $current_page > $page_count ) {

                $overall_count = $current_page - $page_count + $i ;

                if( $overall_count <= $current_page ) {
                    $page_no = $overall_count ;
                    $display = true ;
                }
            }

            if( $current_page == $i ) {
                $classes[] = 'current' ;
            }

            if( $display ) {
                ?>
                <li>
                    <span class="<?php echo esc_attr( implode( ' ' , $classes ) ) ; ?>">
                        <a href="<?php echo esc_url( rs_get_endpoint_url( $query_args , $page_no , $permalink ) ) ; ?>"> <?php echo esc_html( $page_no ) ; ?> </a>
                    </span>
                </li>
                <?php
            }
        }
        ?>
        <li>
            <span class="rs-pagination rs-next-pagination">
                <a href="<?php echo esc_url( rs_get_endpoint_url( $query_args , $next_page_count , $permalink ) ) ; ?>"> > </a>
            </span>
        </li>
        <li>
            <span class="rs-pagination rs-last-pagination">
                <a href="<?php echo esc_url( rs_get_endpoint_url( $query_args , $page_count , $permalink ) ) ; ?>"> >> </a>
            </span>
        </li>
    </ul>
</nav>
<?php
