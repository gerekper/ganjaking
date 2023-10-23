<?php global $userpro; ?>

<p class="upadmin-highlight"><?php _e('You can find the Multiple Forms that you have created and edit it from this page.','userpro'); ?></p>
<?php if(isset($_GET["action"]) && $_GET["action"] == "edit"){

	require_once 'template.php';
}else {?>

<table class="wp-list-table widefat fixed">

	<thead>
		<tr>
			<th scope='col' class='manage-column'><?php _e('Form Title','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Form Shortcode','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Action','userpro'); ?></th>
			
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope='col' class='manage-column'><?php _e('Form Title','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Form Shortcode','userpro'); ?></th>
			 <th scope='col' class='manage-column'><?php _e('Actions','userpro'); ?></th>			
		</tr>
	</tfoot>

				
			<?php 
                        $up_multiforms = array();
			$up_multiforms = userpro_mu_get_option('multi_forms');
			if( isset($up_multiforms) && !empty($up_multiforms) ){
				foreach( $up_multiforms as $key => $arr ) {  ?>
				<tr>
					<td valign="top"><strong><?php echo $key; ?></strong></td>
					<td valign="top"><strong>[userpro template=register type="<?php echo $key;?>"]</strong></td>
					<td valign="top"><a href="<?php echo admin_url(); ?>admin.php?page=userpro-multi&tab=edit_multifrm&action=edit&title=<?php echo $key; ?>"><?php _e('Edit','userpro'); ?></a> | <a href="#" onclick="up_delete_form('<?php echo $key; ?>',event, this)"><?php _e('Remove','userpro'); ?></a></td>
				</tr>
				<?php 
				} 
			}
			?>
			
		</table>
<?php }?>
