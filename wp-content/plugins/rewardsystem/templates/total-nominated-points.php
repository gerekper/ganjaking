<?php
/**
 * This template is used for Display Buyer total points in nominee.
 *
 * This template can be overridden by copying it to yourtheme/rewardsystem/total-nominated-points.php
 *
 * To maintain compatibility, Reward System will update the template files and you have to copy the updated files to your theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<h3><?php esc_html_e( 'Nominee Table' , 'rewardsystem' ); ?></h3>
<table class="rs-buyer-total-points-for-nominee">
	<thead>
		<tr>
			<th><?php esc_html_e( 'S.No' , 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Nominee' , 'rewardsystem' ) ; ?></th>
			<th><?php esc_html_e( 'Total Nominated Points' , 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1 ;
		foreach ( $nominee_points_data as $data ) :

			$total_points = isset( $data[ 'total_points' ] ) ? $data[ 'total_points' ] : '' ;
			if ( ! $total_points ) :
				continue ;
			endif ;

			$user_id = isset( $data[ 'userid' ] ) ? $data[ 'userid' ] : '' ;
			$user    = get_user_by( 'ID' , $user_id ) ;
			if ( ! is_object( $user ) ) :
				continue ;
			endif ;

			$user_name = $user->user_login ;
			?>
			<tr>
				<td><?php echo esc_html( $i ++  ) ; ?></td>
				<td><?php echo esc_html( $user_name ) ; ?></td>
				<td><?php echo esc_html( $total_points ) ; ?></td>
			</tr>
		<?php endforeach ; ?>
	</tbody>
</table>
<?php
