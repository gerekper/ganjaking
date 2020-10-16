<?php
/**
 * This template is used for Display Points Earned in a Specific Duration.
 *
 * This template can be overridden by copying it to yourtheme/rewardsystem/points-earned-in-specific-duration.php
 *
 * To maintain compatibility, Reward System will update the template files and you have to copy the updated files to your theme.
 */
if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
?>
<h3><?php esc_html_e( 'Earned Points' , SRP_LOCALE ) ?></h3>
<table class = "rs_points_earned_in_specific_duration table-bordered" data-page-size="<?php echo esc_attr( $per_page ) ; ?>" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">

    <thead>
        <tr>
            <th><?php esc_html_e( 'S.No' , SRP_LOCALE ) ; ?></th>
            <th><?php esc_html_e( 'Username' , SRP_LOCALE ) ; ?></th>
            <th><?php esc_html_e( 'Earned Points' , SRP_LOCALE ) ; ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
        if( srp_check_is_array( $earned_points_data ) ) :

            $i = 1 ;
            foreach( $earned_points_data as $value ) :

                $user = get_user_by( 'id' , $value[ 'userid' ] ) ;
                if( ! is_object( $user ) ):
                    continue ;
                endif ;

                $points = round_off_type( $value[ 'total_points' ] ) ;
                ?>
                <tr>
                    <td><?php echo esc_html( $i ) ; ?></td>                                     
                    <td><?php echo esc_html( $user->user_login ) ; ?></td>                                     
                    <td><?php echo esc_html( $points ) ; ?></td>
                </tr>
                <?php
                $i ++ ;
            endforeach ;
        endif ;

        if( empty( $earned_points_data ) ) :
            ?>
            <tr>
                <td colspan="2" style="text-align:center"><?php esc_html_e( "No data found." , SRP_LOCALE ) ; ?></td>
            </tr>
            <?php
        endif ;
        ?>   
    </tbody>  

    <?php if( ! empty( $earned_points_data ) ) : ?>
        <tfoot>
            <tr style="clear:both;">
                <td colspan="3">
                    <div class="pagination pagination-centered"></div>
                </td>
            </tr>
        </tfoot>
    <?php endif ; ?>
</table>
<?php
