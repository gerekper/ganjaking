<?php
/**
 * Functions used for the showing help/links to eventon resources in admin
 *
 * @author 		EventON
 * @category 	Admin
 * @package 	Eventon/Admin
 * @version     4.0.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Help Tab Content*/
function eventon_admin_help_tab_content() {
	$screen = get_current_screen();

	ob_start();
?>
<p><b><?php _e('EventON WP Event Calendar General Information.','eventon');?></b></p>
<p><a class='evo_admin_btn btn_prime' href='<?php echo get_admin_url();?>index.php?page=evo-getting-started'><?php _e('Getting started guide to eventON','eventon');?></a></p>
<p><?php _e('All the updated documentation for eventON can be found from our','eventon');?> <a class='evo_admin_btn btn_triad'href='http://www.myeventon.com/documentation/'><?php _e('online documentation library.','eventon');?></a></p>
<?php
	$content = ob_get_clean();

	ob_start();
?>
<p><b><?php _e('Support for EventON Calendar','eventon');?></b></p>
<p><?php _e('We provide support for issues directly related to eventON calendar via our','eventon');?> <a href='http://helpdesk.ashanjay.com' target='_blank' class='evo_admin_btn btn_triad'><?php _e('HelpDesk','eventon');?></a> <?php _e('EventON valid purchase code is required to access helpdesk if eventON came bundled with your theme.','eventon');?></p>
<p><?php _e('Before creating a ticket, please check the troubleshooter guide that can very well help you solve your issue by trying our common solutions.','eventon');?></p>
<p><?php _e('Please check out our','eventon');?> <a href='http://www.myeventon.com/documentation/check-eventon-working/' class='evo_admin_btn btn_triad' target='_blank' ><?php _e('troubleshooter guide to eventon','eventon');?></a></p>
<?php
	$support = ob_get_clean();

	$screen->add_help_tab( 
		array(
		    'id'	=> 'eventon_overview_tab',
		    'title'	=> __( 'General', 'eventon' ),
		    'content'	=>$content
		));
	$screen->add_help_tab( array(
		    'id'	=> 'eventon_overview_tab_s',
		    'title'	=> __( 'Support', 'eventon' ),
		    'content'	=>$support
		) 
	);

	

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'eventon' ) . '</strong></p>' .
		'<p><a href="http://www.myeventon.com/" target="_blank">' . __( 'EventON', 'eventon' ) . '</a></p>' .		
		'<p><a href="http://www.myeventon.com/changelog/" target="_blank">' . __( 'Changelog', 'eventon' ) . '</a></p>'.
		'<p><a href="http://www.myeventon.com/documentation/" target="_blank">' . __( 'Documentation', 'eventon' ) . '</a></p>'.
		'<p><a href="http://www.myeventon.com/addons/" target="_blank">' . __( 'Addons', 'eventon' ) . '</a></p>'.
		'<p><a href="http://helpdesk.ashanjay.com" target="_blank">' . __( 'Helpdesk', 'eventon' ) . '</a></p>'
	);
}
?>